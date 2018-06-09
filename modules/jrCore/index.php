<?php
/**
 * Jamroom System Core module
 *
 * copyright 2018 The Jamroom Network
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0.  Please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
 *
 * Jamroom may use modules and skins that are licensed by third party
 * developers, and licensed under a different license  - please
 * reference the individual module or skin license that is included
 * with your installation.
 *
 * This software is provided "as is" and any express or implied
 * warranties, including, but not limited to, the implied warranties
 * of merchantability and fitness for a particular purpose are
 * disclaimed.  In no event shall the Jamroom Network be liable for
 * any direct, indirect, incidental, special, exemplary or
 * consequential damages (including but not limited to, procurement
 * of substitute goods or services; loss of use, data or profits;
 * or business interruption) however caused and on any theory of
 * liability, whether in contract, strict liability, or tort
 * (including negligence or otherwise) arising from the use of this
 * software, even if advised of the possibility of such damage.
 * Some jurisdictions may not allow disclaimers of implied warranties
 * and certain statements in the above disclaimer may not apply to
 * you as regards implied warranties; the other terms and conditions
 * remain enforceable notwithstanding. In some jurisdictions it is
 * not permitted to limit liability and therefore such limitations
 * may not apply to you.
 *
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit;

// Bring in functions
require_once APP_DIR . '/modules/jrCore/lib/view.php';

//------------------------------
// queue_view
//------------------------------
function view_jrCore_queue_view($_post, $_user, $_conf)
{
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard/queue_viewer");
}

//------------------------------
// queue_entry_delete
//------------------------------
function view_jrCore_queue_entry_delete($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_validate_location_url();
    if (!isset($_post['id'])) {
        jrCore_json_response(array('msg' => 'queue id required'));
    }
    $_id = explode(',', $_post['id']);
    if ($_id && is_array($_id)) {
        foreach ($_id as $v) {
            // First get the queue info
            $qid = (int) $v;
            $tbl = jrCore_db_table_name('jrCore', 'queue');
            $req = "SELECT queue_id, queue_worker, CONCAT_WS('_', queue_module, queue_name) AS qname FROM {$tbl} WHERE queue_id = {$qid}";
            $_rt = jrCore_db_query($req, 'SINGLE');
            if ($_rt && is_array($_rt)) {

                // Delete the queue entry
                jrCore_queue_delete($qid);

                // Decrement in queue_info
                $tbl = jrCore_db_table_name('jrCore', 'queue_info');
                // Was it being worked?
                if (strlen($_rt['queue_worker']) > 0) {
                    $req = "UPDATE {$tbl} SET queue_depth = (queue_depth - 1), queue_workers = (queue_workers - 1) WHERE queue_name = '{$_rt['qname']}' AND queue_depth > 0";
                }
                else {
                    $req = "UPDATE {$tbl} SET queue_depth = (queue_depth - 1) WHERE queue_name = '{$_rt['qname']}' AND queue_depth > 0";
                }
                jrCore_db_query($req);

            }
        }
    }
    jrCore_json_response(array('msg' => 'ok'));
}

//------------------------------
// queue_empty_save
//------------------------------
function view_jrCore_queue_empty_save($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_validate_location_url();
    if (!isset($_post['queue_module']) || strlen($_post['queue_module']) === 0) {
        jrCore_set_form_notice('error', 'invalid queue module - please try again');
    }
    if (!isset($_post['queue_name']) || strlen($_post['queue_name']) === 0) {
        jrCore_set_form_notice('error', 'invalid queue name - please try again');
    }
    $mod = jrCore_db_escape($_post['queue_module']);
    $nam = jrCore_db_escape($_post['queue_name']);

    $tb1 = jrCore_db_table_name('jrCore', 'queue');
    $tb2 = jrCore_db_table_name('jrCore', 'queue_data');
    $req = "DELETE {$tb1}, {$tb2} FROM {$tb1} LEFT JOIN {$tb2} ON ({$tb2}.queue_id = {$tb1}.queue_id) WHERE {$tb1}.queue_module = '{$mod}' AND {$tb1}.queue_name = '{$nam}'";
    jrCore_db_query($req);
    jrCore_validate_queue_info();
    jrCore_location('referrer');
}

//------------------------------
// queue_entry_reset
//------------------------------
function view_jrCore_queue_entry_reset($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_validate_location_url();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_json_response(array('msg' => 'invalid queue id'));
    }

    // Is this queue entry in PROGRESS or SLEEPING?
    $pid = (int) $_post['id'];
    $tbl = jrCore_db_table_name('jrCore', 'queue');
    $req = "SELECT CONCAT_WS('_', queue_module, queue_name) AS qname, queue_worker FROM {$tbl} WHERE queue_id = {$pid}";
    $_qw = jrCore_db_query($req, 'SINGLE');
    if ($_qw && is_array($_qw)) {
        if (strlen($_qw['queue_worker']) > 0) {

            // This was being worked - increment queue counter
            $req = "UPDATE {$tbl} SET queue_worker = '', queue_started = 0, queue_count = (queue_count + 1), queue_sleep = UNIX_TIMESTAMP() WHERE queue_id = {$pid}";
            jrCore_db_query($req);

            // Decrement in queue_info
            $tbl = jrCore_db_table_name('jrCore', 'queue_info');
            $req = "UPDATE {$tbl} SET queue_workers = (queue_workers - 1) WHERE queue_name = '{$_qw['qname']}' AND queue_workers > 0";
            jrCore_db_query($req);

        }
        else {

            // This was just sleeping
            $req = "UPDATE {$tbl} SET queue_worker = '', queue_started = 0, queue_sleep = (UNIX_TIMESTAMP() - 1) WHERE queue_id = {$pid}";
            jrCore_db_query($req);

        }
    }
    jrCore_location('referrer');
}

//------------------------------
// empty_recycle_bin
//------------------------------
function view_jrCore_empty_recycle_bin($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_validate_location_url();

    // Get all RB modules => item_ids for trigger
    $tbl = jrCore_db_table_name('jrCore', 'recycle');
    $req = "SELECT r_module AS module, r_item_id AS iid, r_profile_id AS pid, r_data FROM {$tbl}";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        $_tm = array();
        foreach ($_rt as $_rb) {
            $mod = $_rb['module'];
            if (!isset($_tm[$mod])) {
                $_tm[$mod] = array();
            }
            $_tm[$mod][] = (int) $_rb['iid'];
        }
    }
    else {
        $_tm = array();
    }

    // Trigger
    jrCore_trigger_event('jrCore', 'empty_recycle_bin', $_tm);

    // Empty everything
    $tbl = jrCore_db_table_name('jrCore', 'recycle');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");

    // Cleanup attached media - split into chunks for our worker
    if ($_rt && is_array($_rt)) {
        $_rt = array_chunk($_rt, 100);
        foreach ($_rt as $_ch) {
            jrCore_queue_create('jrCore', 'empty_recycle_bin_files', array('_items' => $_ch));
        }
    }

    jrCore_logger('INF', 'the recycle bin has been emptied');
    jrCore_set_form_notice('success', 'The Recycle Bin has been emptied');
    jrCore_location('referrer');
}

//------------------------------
// recycle_bin_delete
//------------------------------
function view_jrCore_recycle_bin_delete($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_validate_location_url();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_json_response(array('msg' => 'invalid recycle bin id'));
    }
    if (jrCore_delete_recycle_bin_item($_post['id'])) {
        jrCore_json_response(array('msg' => 'ok'));
    }
    jrCore_json_response(array('msg' => 'error encountered deleting recycle bin entry - please try again'));
}

//------------------------------
// recycle_bin_item_content
//------------------------------
function view_jrCore_recycle_bin_item_content($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_validate_location_url();

    // Must get a good recycle bin id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid recycle bin id - please try again');
        jrCore_location('referrer');
    }

    // Get item
    $tbl = jrCore_db_table_name('jrCore', 'recycle');
    $req = "SELECT * FROM {$tbl} WHERE r_id = '{$_post['id']}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid recycle bin id - please try again (2)');
        jrCore_location('referrer');
    }

    jrCore_view_recycle_bin_content($_rt);

    jrCore_page_title("Recycle Bin: {$_rt['r_title']}");
    jrCore_page_display();
}

//------------------------------
// restore_recycle_bin_item
//------------------------------
function view_jrCore_restore_recycle_bin_item($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_validate_location_url();
    @ini_set('max_execution_time', 3600); // 1 hour max

    // Must get a good recycle bin id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid recycle bin id - please try again');
        jrCore_location('referrer');
    }

    // Make sure it exists
    $tbl = jrCore_db_table_name('jrCore', 'recycle');
    $req = "SELECT * FROM {$tbl} WHERE r_id = '{$_post['id']}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid recycle bin id - please try again (2)');
        jrCore_location('referrer');
    }

    $cnt = jrCore_restore_recycle_bin_item($_post['id']);
    if ($cnt > 0) {
        jrProfile_reset_cache($_rt['r_profile_id'], $_rt['r_module']);
        jrCore_set_form_notice('success', 'The item was successfully restored');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered restoring the item - please try again');
    }
    $url = jrCore_get_local_referrer();
    if (strpos($url, 'item_content')) {
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard/recycle_bin");
    }
    else {
        jrCore_location('referrer');
    }
}

//------------------------------
// live_search_options
//------------------------------
function view_jrCore_live_search_options($_post, $_user, $_conf)
{
    $_rp = array();
    if (!isset($_post['m']) || !jrCore_module_is_active($_post['m'])) {
        jrCore_json_response($_rp);
    }
    if (!isset($_post['o']) || strlen($_post['o']) === 0) {
        jrCore_json_response($_rp);
    }
    if (!isset($_post['n']) || strlen($_post['n']) === 0) {
        jrCore_json_response($_rp);
    }
    $_fl = jrCore_get_designer_form_fields($_post['m'], $_post['o']);
    if ($_fl && isset($_fl["{$_post['n']}"]) && isset($_fl["{$_post['n']}"]['options']) && strlen($_fl["{$_post['n']}"]['options']) > 0) {
        $opt = $_fl["{$_post['n']}"]['options'];
        if (function_exists($opt)) {
            $_rp = $opt($_post['q']);
        }
        else {
            $opt = explode("\n", $opt);
            if ($opt && is_array($opt)) {
                foreach ($opt as $k => $v) {
                    if (stripos(' ' . $v, $_post['q'])) {
                        if (strpos($v, '|')) {
                            list($kk, $kv) = explode('|', $v);
                            $kk       = trim($kk);
                            $_rp[$kk] = trim($kv);
                        }
                        else {
                            $v       = trim($v);
                            $_rp[$v] = $v;
                        }
                    }
                }
            }
        }
    }
    return jrCore_live_search_results($_post['n'], $_rp);
}

//------------------------------
// widget_list_get_module_info
//------------------------------
function view_jrCore_widget_list_get_module_info($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['m']) || !jrCore_module_is_active($_post['m'])) {
        jrCore_json_response(array('error' => 'invalid module'));
    }
    $key = json_encode($_post);
    if (!$_mi = jrCore_is_cached('jrCore', $key, false)) {

        $_mi = array();

        // Get unique DataStore keys
        $_mi[0] = jrCore_db_get_unique_keys($_post['m']);
        if (!$_mi[0] || !is_array($_mi[0])) {
            $_mi[0] = array('_created', '_updated');
        }
        foreach ($_mi[0] as $k => $v) {
            if (strpos($v, '_file_')) {
                unset($_mi[0][$k]);
            }
        }
        $_mi[0]['_'] = ' ';
        switch ($_post['m']) {
            case 'jrUser':
            case 'jrProfile':
                break;
            default:
                $_mi[0]['_item_id'] = '_item_id';
                break;
        }
        sort($_mi[0]);

        // Get module widget templates
        $_mi[1]           = jrCore_widget_list_get_module_templates($_post['m']);
        $_mi[1]['custom'] = 'custom';

        jrCore_add_to_cache('jrCore', $key, $_mi, 0, 0, false);
    }
    jrCore_json_response($_mi);
}

//------------------------------
// delete
//------------------------------
function view_jrCore_delete($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    // [module_url] => image
    // [module] => jrImage
    // [option] => delete
    // [_1] => jrProfile
    // [_2] => profile_bg_image
    // [_3] => 1
    if (!isset($_post['_1']) || !jrCore_db_get_prefix($_post['_1'])) {
        jrCore_set_form_notice('error', 'Invalid module');
        jrCore_location('referrer');
    }
    if (!isset($_post['_3']) || !jrCore_checktype($_post['_3'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid item_id');
        jrCore_location('referrer');
    }
    // Get info about this item to be sure the requesting user is allowed
    $_rt = jrCore_db_get_item($_post['_1'], $_post['_3'], SKIP_TRIGGERS);
    if (!$_rt || !is_array($_rt) || !isset($_rt['_profile_id'])) {
        jrCore_set_form_notice('error', 'Invalid item_id (2)');
        jrCore_location('referrer');
    }
    if (!jrUser_is_admin() && !jrProfile_is_profile_owner($_rt['_profile_id'])) {
        jrUser_not_authorized();
    }

    // Remove file
    jrCore_delete_item_media_file($_post['_1'], $_post['_2'], $_rt['_profile_id'], $_post['_3']);

    // If this was a user or profile image, reload session
    switch ($_post['_1']) {

        case 'jrUser':
            jrUser_reset_cache($_user['_user_id'], $_post['_1']);
            jrProfile_reset_cache($_rt['_profile_id']);
            break;

        case 'jrProfile':
            jrProfile_reset_cache($_rt['_profile_id'], $_post['_1']);
            break;
    }
    jrCore_set_form_notice('success', 'The file was successfully deleted');
    jrCore_location('referrer');
}

//------------------------------
// url_stream_error
//------------------------------
function view_jrCore_stream_url_error($_post, $_user, $_conf)
{
    global $_urls;
    if (!isset($_post['_1']) || !isset($_urls["{$_post['_1']}"])) {
        $_er = array('error' => 'Invalid module received in stream_url_error');
        jrCore_json_response($_er);
    }
    $_er = array('error' => 'An error was encountered loading the media URL');
    $_xx = array(
        'module' => $_urls["{$_post['_1']}"]
    );
    $_er = jrCore_trigger_event('jrCore', 'stream_url_error', $_er, $_xx);
    jrCore_json_response($_er);
}

//------------------------------
// queue_pause
//------------------------------
function view_jrCore_queue_pause($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'onoff')) {
        jrCore_set_form_notice('error', 'invalid queue state received - please try again');
        jrCore_location('referrer');
    }
    jrCore_set_system_queue_state($_post['_1']);
    jrCore_check_for_dead_queue_workers();
    jrCore_location('referrer', false);
}

//------------------------------
// css
//------------------------------
function view_jrCore_css($_post, $_user, $_conf)
{
    global $_urls;
    // http://site.com/core/css/audio/jrAudio_jplayer_dark.css
    if (!isset($_post['_1']) || ($_post['_1'] != 'skin' && !isset($_urls["{$_post['_1']}"]))) {
        jrCore_notice('Error', "invalid module or skin");
    }
    if (!isset($_post['_2']) || strlen($_post['_2']) === 0) {
        jrCore_notice('Error', "invalid css files");
    }
    $key = md5(json_encode($_post));
    if (!$css = jrCore_is_cached('jrCore', $key, false)) {
        $crl = jrCore_get_module_url('jrImage');
        if ($_post['_1'] == 'skin') {
            $css = APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/css/{$_post['_2']}";
            $_rp = array(
                '{$' . $_conf['jrCore_active_skin'] . '_img_url}' => "{$_conf['jrCore_base_url']}/{$crl}/img/skin/{$_conf['jrCore_active_skin']}"
            );
        }
        else {
            $mod = $_urls["{$_post['_1']}"];
            $css = APP_DIR . "/modules/{$mod}/css/{$_post['_2']}";
            $_rp = array(
                '{$' . $mod . '_img_url}' => "{$_conf['jrCore_base_url']}/{$crl}/img/module/{$mod}"
            );
        }
        if (!$size = filesize($css)) {
            jrCore_notice('Error', 'CSS file not found');
            exit;
        }
        $css = str_replace(array_keys($_rp), $_rp, file_get_contents($css));

        // Next, get our customized style from the database
        $tbl = jrCore_db_table_name('jrCore', 'skin');
        $req = "SELECT skin_custom_css FROM {$tbl} WHERE skin_directory = '" . jrCore_db_escape($_conf['jrCore_active_skin']) . "'";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if ($_rt && is_array($_rt) && isset($_rt['skin_custom_css']{1})) {
            $css .= "\n";
            $_custom = json_decode($_rt['skin_custom_css'], true);
            if ($_custom && is_array($_custom)) {
                $css .= jrCore_format_custom_css($_custom);
            }
        }
        jrCore_add_to_cache('jrCore', $key, $css, 0, 0, false);
    }
    jrCore_set_custom_header('Content-Disposition: inline; filename="' . $_post['_2'] . '"');
    jrCore_set_custom_header('Content-Type: text/css');
    jrCore_set_custom_header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400000));
    jrCore_send_response_and_detach($css, true);
    exit;
}

//------------------------------
// icon_css
//------------------------------
function view_jrCore_icon_css($_post, $_user, $_conf)
{
    jrUser_ignore_action();
    $width = 64;
    if (isset($_post['_1']) && jrCore_checktype($_post['_1'], 'number_nz') && $_post['_1'] < 64) {
        $width = intval($_post['_1']);
    }
    // Get the color our skin is requesting
    $color = 'white';
    if (isset($_post['_2']) && ($_post['_2'] == 'white' || $_post['_2'] == 'black' || strlen($_post['_2']) === 6)) {
        $color = $_post['_2'];
    }
    else {
        $_tmp = jrCore_get_registered_module_features('jrCore', 'icon_color');
        if (isset($_tmp["{$_conf['jrCore_active_skin']}"])) {
            $color = array_keys($_tmp["{$_conf['jrCore_active_skin']}"]);
            $color = reset($color);
        }
    }
    $exp = (time() + 86400000);
    $dir = jrCore_get_module_cache_dir($_conf['jrCore_active_skin']);
    if (!is_file("{$dir}/sprite_{$color}_{$width}.css") || !is_file("{$dir}/sprite_{$color}_{$width}.png")) {
        jrCore_create_css_sprite($_conf['jrCore_active_skin'], $color, $width);
    }
    else {
        $tim = filectime("{$dir}/sprite_{$color}_{$width}.css");
        $ifs = false;
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']{1})) {
            $ifs = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
        }
        elseif (function_exists('getenv')) {
            $ifs = getenv('HTTP_IF_MODIFIED_SINCE');
        }
        if ($ifs && strtotime($ifs) == $tim) {
            jrCore_set_custom_header('Content-Disposition: inline; filename="sprite_' . $color . '_' . $width . '.css"');
            jrCore_set_custom_header('Content-Type: text/css');
            jrCore_set_custom_header("Last-Modified: " . gmdate('D, d M Y H:i:s \G\M\T', $tim));
            jrCore_set_custom_header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', $exp));
            jrCore_set_custom_header('HTTP/1.1 304 Not Modified');
            jrCore_send_response_and_detach(null, true);
            exit;
        }
    }
    $tmp = file_get_contents("{$dir}/sprite_{$color}_{$width}.css");
    jrCore_set_custom_header('Content-Disposition: inline; filename="sprite_' . $color . '_' . $width . '.css"');
    jrCore_set_custom_header('Content-Type: text/css');
    jrCore_set_custom_header("Last-Modified: " . gmdate('D, d M Y H:i:s \G\M\T', $exp));
    jrCore_set_custom_header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', $exp));
    jrCore_send_response_and_detach($tmp, true);
    exit;
}

//------------------------------
// icon_sprite
//------------------------------
function view_jrCore_icon_sprite($_post, $_user, $_conf)
{
    jrUser_ignore_action();
    $width = 64;
    if (isset($_post['_1']) && jrCore_checktype($_post['_1'], 'number_nz') && $_post['_1'] < 64) {
        $width = intval($_post['_1']);
    }

    // Get the color our skin is requesting
    $color = 'white';
    if (isset($_post['_2']) && ($_post['_2'] == 'white' || $_post['_2'] == 'black' || strlen($_post['_2']) === 6)) {
        $color = $_post['_2'];
    }
    else {
        $_tmp = jrCore_get_registered_module_features('jrCore', 'icon_color');
        if (isset($_tmp["{$_conf['jrCore_active_skin']}"])) {
            $color = array_keys($_tmp["{$_conf['jrCore_active_skin']}"]);
            $color = reset($color);
        }
    }
    $dir = jrCore_get_module_cache_dir($_conf['jrCore_active_skin']);
    if (!is_file("{$dir}/sprite_{$color}_{$width}.png") || !is_file("{$dir}/sprite_{$color}_{$width}.css")) {
        jrCore_create_css_sprite($_conf['jrCore_active_skin'], $color, $width);
    }
    $dat = gmdate('D, d M Y H:i:s \G\M\T', time() + 8640000);
    $tmp = file_get_contents("{$dir}/{$_conf['jrCore_active_skin']}_sprite_{$color}_{$width}.png");
    jrCore_set_custom_header('Content-Disposition: inline; filename="sprite_' . $color . '_' . $width . '.png"');
    jrCore_set_custom_header('Content-Type: image/png');
    jrCore_set_custom_header("Expires: {$dat}");
    jrCore_send_response_and_detach($tmp, true);
    exit;
}

//------------------------------
// form_validate
//------------------------------
function view_jrCore_form_validate($_post, $_user, $_conf)
{
    jrUser_ignore_action();
    return jrCore_form_validate($_post);
}

//------------------------------
// form_modal_status
//------------------------------
function view_jrCore_form_modal_status($_post, $_user, $_conf)
{
    if (!isset($_post['k'])) {
        $_tmp = array('t' => 'error', 'm' => 'invalid key');
        jrCore_json_response($_tmp);
    }
    // Get the results from the DB of our status
    $tbl = jrCore_db_table_name('jrCore', 'modal');
    $req = "SELECT modal_id AS i, modal_value AS m FROM {$tbl} WHERE modal_key = '" . jrCore_db_escape($_post['k']) . "' ORDER BY modal_id ASC";
    $_rt = jrCore_db_query($req, 'i', false, 'm');
    if ($_rt && is_array($_rt)) {
        $req = "DELETE FROM {$tbl} WHERE modal_id IN(" . implode(',', array_keys($_rt)) . ")";
        jrCore_db_query($req);
        foreach ($_rt as $k => $v) {
            $_rt[$k] = json_decode($v, true);
        }
        jrCore_json_response($_rt, true, false, false);
    }
    $_tmp = array(array('t' => 'empty', 'm' => 'no results found for key'));
    jrCore_json_response($_tmp);
}

//------------------------------
// form_modal_cleanup
//------------------------------
function view_jrCore_form_modal_cleanup($_post, $_user, $_conf)
{
    if (!isset($_post['k'])) {
        $_tmp = array(array('t' => 'error', 'm' => 'invalid key'));
        jrCore_json_response($_tmp, true, false, false);
    }
    jrCore_form_modal_cleanup($_post['k']);
    jrCore_db_close();
    exit;
}

//------------------------------
// module_detail_features
//------------------------------
function view_jrCore_module_detail_features($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore', 'tools');
    jrCore_page_banner('item detail features', 'features provided by modules for item detail pages');
    jrCore_get_form_notice();

    // Get all registered features
    $_tmp = jrCore_get_registered_module_features('jrCore', 'item_detail_feature');
    if (!$_tmp || !is_array($_tmp)) {
        jrCore_notice_page('notice', 'There are no modules in the system that provide Item Detail Features');
    }

    $_ord = array();
    if (isset($_conf['jrCore_detail_feature_order']) && strlen($_conf['jrCore_detail_feature_order']) > 0) {
        $_ord = array_flip(explode(',', $_conf['jrCore_detail_feature_order']));
    }
    else {
        foreach ($_tmp as $mod => $_ft) {
            foreach ($_ft as $name => $_ftr) {
                $_ord[] = "{$mod}~{$name}";
            }
        }
        jrCore_set_setting_value('jrCore', 'detail_feature_order', implode(',', $_ord));
        $_ord = array_flip($_ord);
    }

    // First get things in the right order
    $_res = array();
    foreach ($_tmp as $mod => $_ft) {
        if (jrCore_module_is_active($mod)) {
            foreach ($_ft as $nam => $_ftr) {
                $name           = "{$mod}~{$nam}";
                $_ftr['module'] = $mod;
                $_res[$name]    = $_ftr;
            }
        }
    }

    $dat             = array();
    $dat[1]['title'] = '';
    $dat[1]['width'] = '2%';
    $dat[2]['title'] = 'order';
    $dat[2]['width'] = '2%';
    $dat[3]['title'] = 'feature';
    $dat[3]['width'] = '25%';
    $dat[4]['title'] = 'description';
    $dat[4]['width'] = '61%';
    $dat[5]['title'] = 'template name';
    $dat[5]['width'] = '10%';
    jrCore_page_table_header($dat);

    $cnt = 0;
    // First do our items that have been ordered
    if (count($_ord) > 0) {
        foreach ($_ord as $name => $order) {
            if (!isset($_res[$name])) {
                continue;
            }
            $_ftr            = $_res[$name];
            $dat             = array();
            $dat[1]['title'] = jrCore_get_module_icon_html($_ftr['module'], 32);
            if ($cnt === 0) {
                $dat[2]['title'] = '';
            }
            else {
                $dat[2]['title'] = jrCore_page_button("idf-{$cnt}", '&#8679;', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/module_detail_feature_order/{$name}/{$cnt}')");
            }
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $_ftr['label'];
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = $_ftr['help'];
            list(, $tpl_name) = explode('~', $name);
            $dat[5]['title'] = $tpl_name;
            $dat[5]['class'] = 'center';
            jrCore_page_table_row($dat);
            $cnt++;
            unset($_res[$name]);
        }
    }
    // Any left overs
    if (count($_res) > 0) {
        foreach ($_res as $name => $_ftr) {
            $dat             = array();
            $dat[1]['title'] = jrCore_get_module_icon_html($_ftr['module'], 32);
            if ($cnt === 0) {
                $dat[2]['title'] = '';
            }
            else {
                $dat[2]['title'] = jrCore_page_button("idf-{$cnt}", '&#8679;', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/module_detail_feature_order/{$name}/{$cnt}')");
            }
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $_ftr['label'];
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = $_ftr['help'];
            list(, $tpl_name) = explode('~', $name);
            $dat[5]['title'] = $tpl_name;
            $dat[5]['class'] = 'center';
            jrCore_page_table_row($dat);
            $cnt++;
        }
    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button('referrer');
    jrCore_page_display();
}

//------------------------------
// module_detail_feature_order
//------------------------------
function view_jrCore_module_detail_feature_order($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    // [_1] => jrTags~item_tags
    // [_2] => 1 (current order)
    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        jrCore_set_form_notice('error', 'Invalid item detail feature');
        jrCore_location('referrer');
    }
    $nam = trim($_post['_1']);
    list($mod, $feat) = explode('~', $nam);
    $_tmp = jrCore_get_registered_module_features('jrCore', 'item_detail_feature');
    if (!$_tmp || !is_array($_tmp) || !isset($_tmp[$mod]) || !isset($_tmp[$mod][$feat])) {
        jrCore_set_form_notice('error', 'Invalid item feature - feature is not registered');
        jrCore_location('referrer');
    }
    $idx  = (int) $_post['_2'];
    $_cfg = array();
    $_don = array();
    if (isset($_conf['jrCore_detail_feature_order']) && strlen($_conf['jrCore_detail_feature_order']) > 0) {
        $_cfg = explode(',', $_conf['jrCore_detail_feature_order']);
        if (isset($_cfg) && is_array($_cfg)) {
            foreach ($_cfg as $k => $v) {
                $_don[$v] = 1;
                $tmp      = ($idx - 1);
                if ($k == $tmp) {
                    // We have found our swap
                    $_cfg[$idx] = $v;
                    $_cfg[$tmp] = $nam;
                }
            }
        }
    }
    // Add in reset of detail features
    foreach ($_tmp as $mod => $_ft) {
        foreach ($_ft as $name => $_ftr) {
            $nam = "{$mod}~{$name}";
            if (!isset($_don[$nam])) {
                $_cfg[] = $nam;
            }
        }
    }
    jrCore_set_setting_value('jrCore', 'detail_feature_order', implode(',', $_cfg));
    jrCore_delete_config_cache();
    if (jrCore_checktype($_conf['jrCore_default_cache_seconds'], 'number_nz')) {
        jrCore_set_form_notice('success', 'The item feature order has been updated.<br>Make sure and <a href="' . $_conf['jrCore_base_url'] . '/' . $_post['module_url'] . '/cache_reset"><u>Reset Caches</u></a> for your changes to take effect', false);
    }
    else {
        jrCore_set_form_notice('success', 'The item feature order has been updated');
    }
    jrCore_location('referrer');
}

//------------------------------
// performance_history
//------------------------------
function view_jrCore_performance_history($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore', 'tools');
    jrCore_page_banner('performance history');

    // See if we have an existing value
    $page = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $page = (int) $_post['p'];
    }
    $tbl = jrCore_db_table_name('jrCore', 'performance');
    $req = "SELECT * FROM {$tbl} ORDER BY p_id DESC";
    $_tm = jrCore_db_paged_query($req, $page, 12);
    if ($_tm && is_array($_tm) && isset($_tm['_items'])) {

        $dat             = array();
        $dat[1]['title'] = 'date';
        $dat[1]['width'] = '20%';
        $dat[2]['title'] = 'processor';
        $dat[2]['width'] = '20%';
        $dat[3]['title'] = 'database';
        $dat[3]['width'] = '20%';
        $dat[4]['title'] = 'filesystem';
        $dat[4]['width'] = '20%';
        $dat[5]['title'] = 'total score';
        $dat[5]['width'] = '20%';
        jrCore_page_table_header($dat);

        $_inf = jrCore_get_proc_info();
        $pnum = count($_inf);

        foreach ($_tm['_items'] as $k => $_v) {
            $_pt = json_decode($_v['p_val'], true);
            $tot = round((10 / $_pt['total']) * 800);
            if ($pnum > 1) {
                $tot += round(($tot / (8 + $pnum)) * ($pnum / 2));
            }
            $cls = 'success';
            if ($tot < 200) {
                $cls = 'error';
            }
            elseif ($tot < 400) {
                $cls = 'error';
            }
            elseif ($tot < 600) {
                $cls = 'notice';
            }
            elseif ($tot < 800) {
                $cls = 'notice';
            }
            $dat             = array();
            $dat[1]['title'] = jrCore_format_time($_v['p_time']);
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $_pt['cpu'] . 's';
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $_pt['db'] . 's';
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = $_pt['fs'] . 's';
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = '<strong>' . jrCore_number_format($tot) . '</strong>';
            $dat[5]['class'] = "{$cls} center";
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_tm);
        jrCore_page_table_footer();
    }
    jrCore_page_cancel_button('referrer');
    jrCore_page_display();
}

//------------------------------
// performance_check
//------------------------------
function view_jrCore_performance_check($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore', 'tools');

    // See if we have an existing value
    $btn = jrCore_page_button('help', 'performance check details', '$(\'#performance_notice\').slideToggle(300)');

    $tbl = jrCore_db_table_name('jrCore', 'performance');
    $req = "SELECT * FROM {$tbl} ORDER BY p_id DESC LIMIT 1";
    $_tm = jrCore_db_query($req, 'SINGLE');
    if ($_tm && is_array($_tm)) {
        $btn .= jrCore_page_button('performance', 'history', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/performance_history');");
    }

    $share = false;
    // If we are properly configured for the Marketplace, let the user submit a result
    if (jrCore_module_is_active('jrMarket')) {
        if ($_mkt = jrMarket_get_active_release_system()) {
            if (isset($_mkt['system_url']) && strpos($_mkt['system_url'], 'jamroom.net')) {
                $share = true;
            }
        }
    }

    jrCore_page_banner('performance check', $btn);
    jrCore_set_form_notice('notice', "The Performance Check will run a series of performance tests to assess how well Jamroom is likely to<br>perform on your server - it is recommended to run this test at a low traffic time on your server.", false);
    jrCore_get_form_notice();

    $note = '<div id="performance_notice" class="notice" style="padding: 10px 20px;display:none"><div class="item rounded p20">The Performance Check tests 3 separate components of your server to try and give you an idea of how well Jamroom will run on your server:<br><br><strong>Processor Test</strong> &nbsp;&bull;&nbsp; The Processor Test runs a series of calculations on your server processor (CPU) to see how quickly it can finish.  Processor speed is important as it determines how quickly the Jamroom PHP code can be executed.<br><br><strong>Database Test</strong> &nbsp;&bull;&nbsp; The Database Test executes 10,000 separate queries on the database server using the DataStore format.  This test is an important indicator of how fast Jamroom will run on your server.<br><br><strong>Filesystem Test</strong> &nbsp;&bull;&nbsp; The Filesystem Test tests how quickly files can be created and deleted on the Filesystem (the server hard drive).  Having a high speed SSD disk for your server filesystem can really boost Jamroom performance.</div></div>';
    jrCore_page_custom($note);

    if ($_tm && is_array($_tm)) {

        $_tm = json_decode($_tm['p_val'], true);

        $_inf = jrCore_get_proc_info();
        $pnum = count($_inf);

        $type = 'MySQL';
        $_db = jrCore_db_query("SHOW VARIABLES WHERE Variable_name = 'version'", 'SINGLE');
        if ($_db && is_array($_db) && isset($_db) && isset($_db['Value'])) {
            $ver = $_db['Value'];
            if (stripos($ver, 'maria')) {
                $type = 'MariaDB';
            }
        }
        else {
            $msi = jrCore_db_connect();
            $ver = mysqli_get_server_info($msi);
        }
        if (strpos($ver, '-')) {
            list($ver,) = explode('-', $ver);
        }
        $_dsk = jrCore_get_disk_usage();

        // Our "baseline" for a high performance systems is 6.00
        // $_tm = array( cpu, db, fs, total)
        $bonus = '';
        $total = round((10 / $_tm['total']) * 800);
        if ($pnum > 1) {
            $bonus = round(($total / (8 + $pnum)) * ($pnum / 2));
            $total += $bonus;
            $bonus = '<br>includes ' . jrCore_number_format($bonus) . ' point multiple processor bonus<br>';
        }

        // Baselines...
        $_bs = array(
            'cpu' => '0.5',
            'db'  => '6.5',
            'fs'  => '1.0'
        );

        // baselines:
        // cpu = 2
        // db = 6
        // fs = 4

        $cpu = round($_tm['cpu'], 2);
        $cpu_class = '';
        if ($cpu > ($_bs['cpu'] * 4)) {
            $cpu_class = ' bigsystem-maj';
        }
        $db_class = '';
        $db  = round($_tm['db'], 2);
        if ($db > ($_bs['db'] * 4)) {
            $db_class = ' bigsystem-maj';
        }
        $fs_class = '';
        $fs  = round($_tm['fs'], 2);
        if ($fs > ($_bs['fs'] * 4)) {
            $fs_class = ' bigsystem-maj';
        }

        $msg = 'success';
        $txt = 'Jamroom should run <strong>excellent</strong> on your server!';
        if ($total < 200) {
            $msg = 'error';
            $txt = 'Jamroom will run <strong>very slowly</strong> on your server - check out hosting alternatives';
        }
        elseif ($total < 300) {
            $msg = 'error';
            $txt = 'Jamroom is <strong>likely to run slowly</strong> on your server - check out hosting alternatives';
        }
        elseif ($total < 500) {
            $msg = 'notice';
            $txt = 'Jamroom <strong>may run slowly</strong> on your server - check out tips on improving performance';
        }
        elseif ($total < 750) {
            $msg = 'notice';
            $txt = 'Jamroom <strong>may run a bit slower than optimal</strong> on your server';
        }
        $btn = '';
        if ($share) {
            $btn = jrCore_page_button('share', 'share your results', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/performance_share');");
        }
        $htm = '<div style="padding:12px;">
        <table class="page_table bigtable" style="width:100%">
        <tr><th class="page_table_header" style="width:20%">Test</th>
            <th class="page_table_header" style="width:35%">Test Time in Seconds<br><small style="font-weight:normal">Lower is Better</small></th>
            <th class="page_table_header" style="width:45%">Overall Score<br><small style="font-weight:normal">Higher is Better</small></th></tr>
        <tr><td class="page_table_header">Processor<br><span style="color:#888;font-weight:normal">' . $pnum . ' @' . $_inf[1]['mhz'] . '</span></td><td class="page_table_cell bignum bignum2' . $cpu_class . '">' . $cpu . '<br><span>Baseline: ' . $_bs['cpu'] . ' seconds</td>
        <td class="page_table_cell bignum bignum1" rowspan="3"><big>' . jrCore_number_format($total) . '</big><span>' . $bonus . '<br>' . $btn . '</span></td></tr>
        <tr><td class="page_table_header">Database<br><span style="color:#888;font-weight:normal">' . $type . ' ' . $ver . '</span></td><td class="page_table_cell bignum bignum3' . $db_class . '">' . $db . '<br><span>Baseline: ' . $_bs['db'] . ' seconds</span></td></tr>
        <tr><td class="page_table_header">Filesystem<br><span style="color:#888;font-weight:normal">In Use: ' . $_dsk['percent_used'] . '%</span></td><td class="page_table_cell bignum bignum4' . $fs_class . '">' . $fs . '<br><span>Baseline: ' . $_bs['fs'] . ' seconds</span></td></tr>
        </table>' . $txt . '<br><small>Baseline is a <a href="http://www.jamroom.net/hosting"><u>Jamroom Hosted</u></a> Server with 1 XEON CPU @ 2.5GHz, 1 GB RAM and Fast SSD Disk</small></div>';
        jrCore_set_form_notice($msg, $htm, false);
        jrCore_get_form_notice();
    }

    $_tmp = array(
        'submit_value'  => 'run performance check',
        'cancel'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'submit_prompt' => 'Please be patient - depending on the speed of your servers this could take a few minutes to run'
    );
    jrCore_form_create($_tmp);

    // New Menu Entry
    $_tmp = array(
        'name'  => 'hidden',
        'type'  => 'hidden',
        'value' => 1
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// performance_share
//------------------------------
function view_jrCore_performance_share($_post, $_user, $_conf)
{
    jrUser_master_only();
    $tbl = jrCore_db_table_name('jrCore', 'performance');
    $req = "SELECT * FROM {$tbl} ORDER BY p_id DESC LIMIT 1";
    $_tm = jrCore_db_query($req, 'SINGLE');
    if (!$_tm || !is_array($_tm)) {
        jrCore_notice_page('error', 'There are no results to share - please run the Performance Check tool at least once');
    }
    if (isset($_tm['p_provider']) && strlen($_tm['p_provider']) > 0) {
        // See if they JUST shared this
        if (!$tmp = jrCore_get_temp_value('jrCore', 'performance_shared')) {
            jrCore_set_form_notice('warning', "This performance result has already been shared - resubmitting will overwrite your previous entry");
        }
        else {
            jrCore_delete_temp_value('jrCore', 'performance_shared');
        }
    }

    // If we are EMPTY on provider, price and rating get the LAST entry so we can pre-fill
    if (!isset($_tm['p_provider']) || strlen($_tm['p_provider']) === 0) {
        $req = "SELECT * FROM {$tbl} WHERE LENGTH(p_provider) > 0 ORDER BY p_id DESC LIMIT 1";
        $_ol = jrCore_db_query($req, 'SINGLE');
        if ($_ol && is_array($_ol)) {
            $_tm['p_provider'] = $_ol['p_provider'];
            $_tm['p_price']    = $_ol['p_price'];
            $_tm['p_rating']   = $_ol['p_rating'];
            $_tm['p_type']     = (strlen($_ol['p_type']) > 0) ? $_ol['p_type'] : 'none';
        }
        unset($_ol);
    }

    $pid  = (int) $_tm['p_id'];
    $_pr  = json_decode($_tm['p_val'], true);
    $_inf = jrCore_get_proc_info();
    $pnum = count($_inf);
    $tot  = round((10 / $_pr['total']) * 800);
    if ($pnum > 1) {
        $tot += round(($tot / (8 + $pnum)) * ($pnum / 2));
    }
    $tot = jrCore_number_format($tot);

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore', 'tools');
    jrCore_page_banner('share your performance results');

    $_tmp = array(
        'submit_value'     => 'share these results',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/performance_check",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    $titl = '';
    $line = '';
    $cadd = '';
    $_cpu = jrCore_get_proc_info();
    if ($_cpu && is_array($_cpu) && isset($_cpu[1]['mhz'])) {
        $cadd = "&nbsp;<b>Server CPU:</b> {$_cpu[1]['mhz']}<br>";
    }
    $madd = '';
    $_mem = jrCore_get_system_memory();
    if ($_mem && is_array($_mem) && isset($_mem['memory_total'])) {
        $madd = "&nbsp;<b>Server RAM:</b> " . jrCore_format_size($_mem['memory_total']) . '<br>';
    }
    if (strlen("{$cadd}{$madd}") > 0) {
        $titl = 'Server Info:<br><br>';
        $line = '<br>Performance Results:<br><br>';
    }

    // Results
    $_tmp = array(
        'name'  => 'result',
        'type'  => 'hidden',
        'value' => jrCore_url_encode_string($_tm['p_val'])
    );
    jrCore_form_field_create($_tmp);

    // Performance ID
    $_tmp = array(
        'name'  => 'p_id',
        'type'  => 'hidden',
        'value' => $pid
    );
    jrCore_form_field_create($_tmp);

    // Hardware
    if (strlen($cadd) > 0) {
        $_tmp = array(
            'name'  => 'p_cpu',
            'type'  => 'hidden',
            'value' => $_cpu[1]['mhz']
        );
        jrCore_form_field_create($_tmp);
    }
    if (strlen($madd) > 0) {
        $_tmp = array(
            'name'  => 'p_mem',
            'type'  => 'hidden',
            'value' => jrCore_format_size($_mem['memory_total'])
        );
        jrCore_form_field_create($_tmp);
    }

    // Provider
    $_tmp = array(
        'name'     => 'provider',
        'label'    => 'Provider Name',
        'help'     => 'Enter the name of your Hosting Provider',
        'type'     => 'text',
        'validate' => 'not_empty',
        'section'  => 'required information',
        'required' => true,
        'value'    => (isset($_tm['p_provider']) && strlen($_tm['p_provider']) > 0) ? jrCore_entity_string($_tm['p_provider']) : ''
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_custom("<div class=\"success p10 fixed-width rounded\" style=\"margin-left:4px\">{$titl}{$cadd}{$madd}{$line}&nbsp;&nbsp;<b>Processor:</b> {$_pr['cpu']}<br>&nbsp;&nbsp;&nbsp;<b>Database:</b> {$_pr['db']}<br>&nbsp;<b>Filesystem:</b> {$_pr['fs']}<br><b>Total Score:</b> {$tot}</div>", 'Results To Share');

    // Comment
    $_tmp = array(
        'name'     => 'comment',
        'label'    => 'Result Comments',
        'sublabel' => '(max 512 characters)',
        'help'     => 'If you would like to provide a short Comment or Review of your Provider or Test Results, enter it here and it will be shared.',
        'type'     => 'textarea',
        'validate' => 'not_empty',
        'section'  => 'optional',
        'required' => false,
        'value'    => (isset($_tm['p_comment']) && strlen($_tm['p_comment']) > 0) ? jrCore_entity_string($_tm['p_comment']) : ''
    );
    jrCore_form_field_create($_tmp);

    // Price
    $_val = array(
        0 => '-',
        1 => 'Under &#36;10',
        2 => '&#36;10 to &#36;25',
        3 => '&#36;25 to &#36;50',
        4 => '&#36;50 to &#36;100',
        5 => 'Over &#36;100'
    );
    $_tmp = array(
        'name'     => 'price',
        'label'    => 'Monthly Hosting Cost',
        'help'     => 'How much do you pay <strong>monthly</strong> for your Jamroom hosting account?',
        'type'     => 'select',
        'options'  => $_val,
        'default'  => 0,
        'validate' => 'number_nz',
        'required' => false,
        'value'    => (isset($_tm['p_price']) && is_numeric($_tm['p_price'])) ? intval($_tm['p_price']) : 0
    );
    jrCore_form_field_create($_tmp);

    // Type
    $_val = array(
        'none'      => '-',
        'jamroom'   => 'Jamroom Hosted',
        'shared'    => 'Shared Hosting',
        'reseller'  => 'Reseller Hosting',
        'vps'       => 'VPS (Virtual Private Server)',
        'dedicated' => 'Dedicated Server',
        'cloud'     => 'Cloud Provider (Amazon Web Services, Rackspace Cloud, Heroku, etc.)'
    );
    $_tmp = array(
        'name'     => 'type',
        'label'    => 'Hosting Type',
        'help'     => 'What type of Hosting Account do you run Jamroom on?  Select the account type that fits your hosting account the closest.',
        'type'     => 'select',
        'options'  => $_val,
        'default'  => 'none',
        'validate' => 'core_string',
        'required' => false,
        'value'    => (isset($_tm['p_type']) && strlen($_tm['p_type']) > 0) ? $_tm['p_type'] : 'none'
    );
    jrCore_form_field_create($_tmp);

    // Rating
    $_val = array(
        0 => '-',
        5 => 'Excellent',
        4 => 'Above Average',
        3 => 'Average',
        2 => 'Below Average',
        1 => 'Poor'
    );
    $_tmp = array(
        'name'     => 'rating',
        'label'    => 'Provider Rating',
        'help'     => 'How would you rate the value of the service you receive from your provider?',
        'type'     => 'select',
        'options'  => $_val,
        'default'  => 0,
        'validate' => 'number_nz',
        'required' => false,
        'value'    => (isset($_tm['p_rating']) && is_numeric($_tm['p_rating'])) ? intval($_tm['p_rating']) : 0
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_display();
}

//------------------------------
// performance_share_save
//------------------------------
function view_jrCore_performance_share_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Make sure performance results exist
    $pid = intval($_post['p_id']);
    $tbl = jrCore_db_table_name('jrCore', 'performance');
    $req = "SELECT * FROM {$tbl} WHERE p_id = '{$pid}' LIMIT 1";
    $_tm = jrCore_db_query($req, 'SINGLE');
    if (!$_tm || !is_array($_tm)) {
        jrCore_set_form_notice('error', 'Unable to find results to share - please try again');
        jrCore_location('referrer');
    }

    // Save our updated results
    $prv = jrCore_db_escape($_post['provider']);
    $cmt = jrCore_db_escape($_post['comment']);
    $typ = jrCore_db_escape($_post['type']);
    $prc = (int) $_post['price'];
    $rtg = (int) $_post['rating'];
    $req = "UPDATE {$tbl} SET p_provider = '{$prv}', p_comment = '{$cmt}', p_price = '{$prc}', p_rating = '{$rtg}', p_type = '{$typ}' WHERE p_id = '{$pid}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (!$cnt || $cnt !== 1) {
        jrCore_set_form_notice('error', 'Unable to save share results - please try again');
        jrCore_location('referrer');
    }

    // Active Marketplace info
    $_mk = jrMarket_get_active_release_system();

    // Our payload
    $_up            = array(
        'payload' => array(
            'system_id' => $_mk['system_code'],
            'result'    => $_post['result'],
            'provider'  => jrCore_strip_html($_post['provider']),
            'price'     => intval($_post['price']),
            'comment'   => jrCore_strip_html($_post['comment']),
            'rating'    => intval($_post['rating']),
            'type'      => $_post['type'],
            'cpu'       => floatval($_post['p_cpu']),
            'mem'       => $_post['p_mem'],
        )
    );
    $_up['payload'] = jrCore_url_encode_string(json_encode($_up['payload']));
    $_rs            = jrCore_load_url("http://www.jamroom.net/networkperformance/submit", $_up, 'POST');
    if (!$_rs || strlen($_rs) < 3) {
        jrCore_set_form_notice('error', 'Unable to share results to server - please try again');
        jrCore_location('referrer');
    }
    $_rs = json_decode($_rs, true);
    if (!$_rs || !is_array($_rs)) {
        jrCore_set_form_notice('error', 'Invalid results received from share server - please try again');
        jrCore_location('referrer');
    }
    if (isset($_rs['error'])) {
        jrCore_set_form_notice('error', $_rs['error']);
    }
    else {
        jrCore_form_delete_session();
        jrCore_set_form_notice('success', $_rs['success']);
    }
    jrCore_set_temp_value('jrCore', 'performance_shared', 1);
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/performance_share");
}

//------------------------------
// performance_check_save
//------------------------------
function view_jrCore_performance_check_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_run_performance_check();
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/performance_check");
}

//------------------------------
// system_check_update
//------------------------------
function view_jrCore_system_check_update($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_delete_config_cache();
    jrCore_location('referrer');
}

//------------------------------
// system_check
//------------------------------
function view_jrCore_system_check($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore', 'tools');
    $tmp = jrCore_page_button('pcheck', 'refresh', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/system_check_update');");
    jrCore_page_banner('system check', $tmp);
    jrCore_get_form_notice();

    $pass = jrCore_get_option_image('pass');
    $fail = jrCore_get_option_image('fail');

    $dat             = array();
    $dat[1]['title'] = 'checked';
    $dat[1]['width'] = '25%';
    $dat[2]['title'] = 'value';
    $dat[2]['width'] = '25%';
    $dat[3]['title'] = 'result';
    $dat[3]['width'] = '5%';
    $dat[4]['title'] = 'note';
    $dat[4]['width'] = '45%';
    jrCore_page_table_header($dat);

    // Get our core version from file and compare to what's in the DB
    $_mta            = jrCore_module_meta_data('jrCore');
    $dat             = array();
    $dat[1]['title'] = "<a href=\"{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/info\">{$_mods['jrCore']['module_name']}</a>";
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = $_mta['version'];
    $dat[2]['class'] = 'center';
    $dat[3]['title'] = $pass;
    $dat[3]['class'] = 'center';
    $dat[4]['title'] = $_mods['jrCore']['module_version'];
    if ($_mta['version'] != $_mods['jrCore']['module_version']) {
        $dat[3]['title'] = $fail;
        $dat[4]['title'] .= '&nbsp;&nbsp;&nbsp;<a href="' . $_conf['jrCore_base_url'] . '/' . jrCore_get_module_url('jrCore') . '/integrity_check"><strong><u>Integrity Check Required!</u></strong>';
    }
    jrCore_page_table_row($dat);

    // Server
    $dat             = array();
    $dat[1]['title'] = 'Server OS';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = jrCore_get_server_os();
    $dat[2]['class'] = 'center';
    $dat[3]['title'] = $pass;
    $dat[3]['class'] = 'center';
    $dat[4]['title'] = 'Linux or Mac OS X based server';
    jrCore_page_table_row($dat);

    $web_server = php_sapi_name();
    if (strpos(' ' . $web_server, 'apache2handler')) {
        $web_server = 'Apache';
        // Get version
        if (isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], '/')) {
            list(,$apache_version) = explode('/', $_SERVER['SERVER_SOFTWARE']);
            $apache_version = jrCore_string_field($apache_version, 1);
            if (strpos($apache_version, '.')) {
                $web_server .= ' ' . $apache_version;
            }
        }
    }

    // Web Server
    $dat             = array();
    $dat[1]['title'] = 'Web Server';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = $web_server;
    $dat[2]['class'] = 'center word-break';
    $dat[3]['title'] = $pass;
    $dat[3]['class'] = 'center';
    $dat[4]['title'] = 'Apache or Nginx Web Server required';
    jrCore_page_table_row($dat);

    // PHP Version
    list($php_version,) = explode('+', phpversion());
    $php_version = preg_replace('/[^0-9\.-]/', '', $php_version);
    $result = $fail;
    if (version_compare($php_version, '5.3.0') != -1) {
        $result = $pass;
    }

    $dat             = array();
    $dat[1]['title'] = 'PHP Version';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = '<input type="button" value="' . $php_version . '" onclick="window.open(\'' . $_conf['jrCore_base_url'] . '/' . $_post['module_url'] . '/phpinfo\')" style="vertical-align:middle;font-size:13px">';
    $dat[2]['class'] = 'center';
    $dat[3]['title'] = $result;
    $dat[3]['class'] = 'center';
    $dat[4]['title'] = 'PHP 5.3 minimum, PHP 7.0+ recommended';
    jrCore_page_table_row($dat);

    // DB Version
    $_db = jrCore_db_query("SHOW VARIABLES WHERE Variable_name = 'version'", 'SINGLE');
    if ($_db && is_array($_db) && isset($_db['Value'])) {
        $ver = $_db['Value'];
    }
    else {
        $msi = jrCore_db_connect();
        $ver = mysqli_get_server_info($msi);
    }
    if (strpos($ver, '-')) {
        list($ver,) = explode('-', $ver);
    }
    $result = $pass;
    if (strpos($ver, '3.') === 0 || strpos($ver, '4.') === 0 || strpos($ver, '5.0') === 0 || strpos($ver, '5.1') === 0) {
        $result = $fail;
    }

    $dat             = array();
    $dat[1]['title'] = 'Database';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = $ver;
    $dat[2]['class'] = 'center';
    $dat[3]['title'] = $result;
    $dat[3]['class'] = 'center';
    $dat[4]['title'] = 'MySQL 5.5.0 minimum, MariaDB 10.1+ recommended';
    jrCore_page_table_row($dat);

    // Disabled Functions
    $dis_funcs = ini_get('disable_functions');
    if ($dis_funcs && $dis_funcs != '') {
        $dis_funcs = explode(',', $dis_funcs);
        if (isset($dis_funcs) && is_array($dis_funcs)) {
            foreach ($dis_funcs as $k => $fnc) {
                // We don't care about disabled process control functions
                $fnc = trim($fnc);
                if (strlen($fnc) === 0 || strpos($fnc, 'pcntl') === 0) {
                    unset($dis_funcs[$k]);
                }
                // Other functions we do not care about as Jamroom does not use them
                switch ($fnc) {
                    case 'dl':
                        unset($dis_funcs[$k]);
                        break;
                }
            }
        }
        if (isset($dis_funcs) && count($dis_funcs) > 0) {
            $dis_funcs = implode('<br>', $dis_funcs);
            $result    = $fail;

            $dat             = array();
            $dat[1]['title'] = 'Disabled Functions';
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $dis_funcs;
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $result;
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = 'Disabled PHP Functions can impact system functionality.';
            jrCore_page_table_row($dat);
        }
    }

    // Max Allowed Packet
    $req = "SHOW VARIABLES LIKE 'max_allowed_packet'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && isset($_rt['Value'])) {
        $map = round(($_rt['Value'] / 1048576), 1);
        if ($map < 32) {
            $dat             = array();
            $dat[1]['title'] = 'Max Query Size';
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $map . 'mb';
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $fail;
            $dat[4]['title'] = 'Increase max_allowed_packet setting to 32mb:<br><a href="http://dev.mysql.com/doc/refman/5.1/en/packet-too-large.html" target="_blank"><u>Increasing Max Allowed Packet</u></a>';
            $dat[3]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
    }

    // Local Cache
    if (!jrCore_local_cache_is_enabled(false)) {
        $dat             = array();
        $dat[1]['title'] = 'Memory Cache';
        $dat[1]['class'] = 'center';
        $dat[2]['title'] = 'functions are missing';
        $dat[2]['class'] = 'center';
        $dat[3]['title'] = $fail;
        $dat[4]['title'] = 'Ensure <a href="http://us2.php.net/apcu" target="_blank"><u>APCu functions</u></a> have been enabled in your PHP install';
        $dat[3]['class'] = 'center';
        jrCore_page_table_row($dat);
    }

    // Directories
    $_to_check = array('cache', 'logs', 'media');
    $_bad      = array();
    foreach ($_to_check as $dir) {
        if (!is_dir(APP_DIR . "/data/{$dir}")) {
            // See if we can create it
            if (!jrCore_create_directory(APP_DIR . "/data/{$dir}")) {
                $_bad[] = "data/{$dir} does not exist";
            }
        }
        elseif (!is_writable(APP_DIR . "/data/{$dir}")) {
            chmod(APP_DIR . "/data/{$dir}", $_conf['jrCore_dir_perms']);
            if (!is_writable(APP_DIR . "/data/{$dir}")) {
                $_bad[] = "data/{$dir} is not writable";
            }
        }
    }
    if (isset($_bad) && is_array($_bad) && count($_bad) > 0) {
        $note   = 'All directories <strong>must be writable</strong> by web user!';
        $dirs   = implode('<br>', $_bad);
        $result = $fail;
    }
    else {
        $note   = 'All directories are writable';
        $dirs   = 'all writable';
        $result = $pass;
    }
    $dat             = array();
    $dat[1]['title'] = 'Data Directories';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = $dirs;
    $dat[2]['class'] = 'center';
    $dat[3]['title'] = $result;
    $dat[3]['class'] = 'center';
    $dat[4]['title'] = $note;
    jrCore_page_table_row($dat);

    $upl             = jrCore_get_max_allowed_upload();
    $dat             = array();
    $dat[1]['title'] = 'Max Upload';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = jrCore_format_size($upl);
    $dat[2]['class'] = 'center';
    $dat[3]['title'] = ($upl <= 2097152) ? $fail : $pass;
    $dat[3]['class'] = 'center';
    $dat[4]['title'] = ($upl <= 2097152) ? 'increase post_max_size and upload_max_filesize in your php.ini to allow larger uploads<br>' : '';
    $dat[4]['title'] .= 'View the <a href="https://www.jamroom.net/the-jamroom-network/documentation/problems/748/how-do-i-increase-phps-upload-limit" target="_blank"><u>FAQ on increasing the allowed upload size</u></a>';
    jrCore_page_table_row($dat);

    // Check installed locale's
    if (isset($_conf['jrUser_default_language']) && $_conf['jrUser_default_language'] != 'en-US') {
        if ($_lc = jrCore_get_installed_locales()) {
            $dat             = array();
            $dat[1]['title'] = 'Supported Languages';
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = count($_lc);
            $dat[2]['class'] = 'center';

            // Is the default language supported?
            if (!isset($_lc["{$_conf['jrUser_default_language']}"])) {
                $dat[3]['title'] = $fail;
                $dat[4]['title'] = "Language support (locale) for {$_conf['jrUser_default_language']} is not installed on this server";
            }
            else {
                $dat[3]['title'] = $pass;
                $dat[4]['title'] = "Language support (locale) for {$_conf['jrUser_default_language']} is installed and active";
            }
            $dat[3]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
    }

    // Apache rlimits
    if (function_exists('posix_getrlimit')) {
        $_rl = posix_getrlimit();

        // Apache RlimitMEM
        if ((jrCore_checktype($_rl['soft totalmem'], 'number_nz') && $_rl['soft totalmem'] < 67108864) || (jrCore_checktype($_rl['hard totalmem'], 'number_nz') && $_rl['hard totalmem'] < 67108864)) {
            $apmem = $_rl['soft totalmem'];
            if (jrCore_checktype($_rl['hard totalmem'], 'number_nz') && $_rl['hard totalmem'] < $_rl['soft totalmem']) {
                $apmem = $_rl['hard totalmem'];
            }
            $show            = (($apmem / 1024) / 1024);
            $dat             = array();
            $dat[1]['title'] = 'Apache Memory Limit';
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $show . 'MB';
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $fail;
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = "Apache is limiting the amount of memory you can use - this could cause issues, especially when doing Media Conversions. Apache Memory Limits are put in place by your hosting provider, and cannot be modified - contact your hosting provider and have them increase the limit, or set it to &quot;unlimited&quot;.";
            jrCore_page_table_row($dat);
        }
        // Apache RlimitCPU
        if (jrCore_checktype($_rl['soft cpu'], 'number_nz') && $_rl['soft cpu'] < 20) {
            $dat             = array();
            $dat[1]['title'] = 'Apache Soft CPU Limit';
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $_rl['soft cpu'];
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $fail;
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = "Apache is limiting the amount of CPU you can use - this could cause issues, especially when doing Media Conversions. Apache CPU Limits are put in place by your hosting provider, and cannot be modified - you will want to contact your hosting provider and have them set the soft cpu limit to &quot;unlimited&quot;.";
            jrCore_page_table_row($dat);
        }
        elseif (jrCore_checktype($_rl['hard cpu'], 'number_nz') && $_rl['hard cpu'] < 40) {
            $dat             = array();
            $dat[1]['title'] = 'Apache Hard CPU Limit';
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $_rl['hard cpu'];
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $fail;
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = "Apache is limiting the amount of CPU you can use - this could cause issues, especially when doing Media Conversions. Apache CPU Limits are put in place by your hosting provider, and cannot be modified - you will want to contact your hosting provider and have them set the soft cpu limit to &quot;unlimited&quot;.";
            jrCore_page_table_row($dat);
        }

        // Apache RlimitNPROC
        if ((jrCore_checktype($_rl['soft maxproc'], 'number_nz') && $_rl['soft maxproc'] < 200) || (jrCore_checktype($_rl['hard maxproc'], 'number_nz') && $_rl['hard maxproc'] < 200)) {
            $approc = $_rl['soft maxproc'];
            if (jrCore_checktype($_rl['hard maxproc'], 'number_nz') && $_rl['hard maxproc'] < $_rl['soft maxproc']) {
                $approc = $_rl['hard maxproc'];
            }
            $dat             = array();
            $dat[1]['title'] = 'Apache Process Limit';
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $approc;
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $fail;
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = "Apache is limiting the amount of Processes you can use - this could cause issues, especially when doing Media Conversions. Apache PROC Limits are put in place by your hosting provider, and cannot be modified - you will want to contact your hosting provider and have them set the soft and hard maxproc limits to &quot;unlimited&quot;.";
            jrCore_page_table_row($dat);
        }
    }
    $dat             = array();
    $dat[1]['title'] = 'checked';
    $dat[2]['title'] = 'value';
    $dat[3]['title'] = 'result';
    $dat[4]['title'] = 'note';
    jrCore_page_table_header($dat, null, true);

    // Go through installed modules
    foreach ($_mods as $mod => $_inf) {
        if ($mod == 'jrCore' || $mod == 'jrSystemTools' || !jrCore_module_is_active($mod)) {
            continue;
        }
        // Check if this module requires other modules to function - make sure they exist and are activated
        if (isset($_inf['module_requires']{1})) {
            $_req = explode(',', $_inf['module_requires']);
            if (is_array($_req)) {
                foreach ($_req as $rmod) {
                    // See if we have been given an explicit version - i.e. jrImage:1.1.5
                    if (strpos($rmod, ':')) {
                        list($rmod, $vers) = explode(':', $rmod);
                        $rmod = trim($rmod);
                        $vers = trim($vers);
                    }
                    else {
                        $rmod = trim($rmod);
                        $vers = '0.0.0';
                    }
                    if (!is_dir(APP_DIR . "/modules/{$rmod}")) {
                        $murl = jrCore_get_module_url('jrMarket');
                        $dat             = array();
                        $dat[1]['title'] = $_mods[$mod]['module_name'];
                        $dat[1]['class'] = 'center';
                        $dat[2]['title'] = 'required module: ' . $rmod;
                        $dat[2]['class'] = 'center';
                        $dat[3]['title'] = $fail;
                        $dat[3]['class'] = 'center';
                        $dat[4]['title'] = "<strong>{$rmod}</strong> module is missing. <a href='{$_conf['jrCore_base_url']}/{$murl}/browse/module?search_string={$rmod}' style='text-decoration: underline' target='_blank'>Search in Marketplace</a>";
                        jrCore_page_table_row($dat);
                    }
                    elseif (!jrCore_module_is_active($rmod)) {
                        $murl = jrCore_get_module_url($rmod);
                        $dat             = array();
                        $dat[1]['title'] = $_mods[$mod]['module_name'];
                        $dat[1]['class'] = 'center';
                        $dat[2]['title'] = 'required module: ' . $rmod;
                        $dat[2]['class'] = 'center';
                        $dat[3]['title'] = $fail;
                        $dat[3]['class'] = 'center';
                        $dat[4]['title'] = "<strong>{$rmod}</strong> module is not active, <a href='{$_conf['jrCore_base_url']}/{$murl}/admin/info/hl=module_active' style='text-decoration: underline' target='_blank'>click here</a>";
                        jrCore_page_table_row($dat);
                    }
                    elseif (version_compare($_mods[$rmod]['module_version'], $vers, '<')) {
                        $dat             = array();
                        $dat[1]['title'] = $_inf['module_name'];
                        $dat[1]['class'] = 'center';
                        if ($vers != '0.0.0') {
                            $dat[2]['title'] = 'required module: ' . $rmod . ' ' . $vers;
                        }
                        else {
                            $dat[2]['title'] = 'required module: ' . $rmod;
                        }
                        $dat[2]['class'] = 'center';
                        $dat[3]['title'] = $fail;
                        $dat[3]['class'] = 'center';
                        $dat[4]['title'] = "<strong>{$rmod}</strong> version {$vers} required (current: {$_mods["{$rmod}"]['module_version']})";
                        jrCore_page_table_row($dat);
                    }
                }
            }
        }
        // See if this module has any additional checks to add
        $_inf['pass']    = $pass;
        $_inf['fail']    = $fail;
        $_inf['warning'] = jrCore_get_option_image('warning');
        jrCore_trigger_event('jrCore', 'system_check', array(), $_inf, $mod);
    }

    // System Tools
    if (jrCore_module_is_active('jrSystemTools')) {
        $dat             = array();
        $dat[1]['title'] = 'system tools';
        $dat[2]['title'] = 'value';
        $dat[3]['title'] = 'result';
        $dat[4]['title'] = 'note';
        jrCore_page_table_header($dat, null, true);
        $_inf['pass']    = $pass;
        $_inf['fail']    = $fail;
        $_inf['warning'] = jrCore_get_option_image('warning');
        jrCore_trigger_event('jrCore', 'system_check', array(), $_inf, 'jrSystemTools');
    }

    jrCore_page_table_footer();
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    jrCore_page_display();
}

//------------------------------
// phpinfo
//------------------------------
function view_jrCore_phpinfo($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (function_exists('phpinfo')) {
        phpinfo();
        exit;
    }
    jrCore_notice_page('error', 'The phpinfo() function has been disabled in your install');
}

//------------------------------
// skin_menu
//------------------------------
function view_jrCore_skin_menu($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore', 'tools');

    $tbl = jrCore_db_table_name('jrCore', 'menu');
    $req = "SELECT * FROM {$tbl} ORDER BY menu_order ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');

    jrCore_page_banner('user menu entries');
    jrCore_set_form_notice('notice', 'Some entries may be controlled by Quota access and may (or may not) show depending on the User');
    jrCore_get_form_notice();

    $_lang = jrUser_load_lang_strings();

    $dat             = array();
    $dat[1]['title'] = '';
    $dat[1]['width'] = '2%;';
    $dat[2]['title'] = 'order';
    $dat[2]['width'] = '2%;';
    $dat[3]['title'] = 'label';
    $dat[3]['width'] = '30%;';
    $dat[4]['title'] = 'URL';
    $dat[4]['width'] = '25%;';
    $dat[5]['title'] = 'active';
    $dat[5]['width'] = '5%;';
    $dat[6]['title'] = 'groups';
    $dat[6]['width'] = '8%;';
    $dat[7]['title'] = 'modify';
    $dat[7]['width'] = '5%;';
    $dat[8]['title'] = 'action';
    $dat[8]['width'] = '5%;';
    jrCore_page_table_header($dat);

    if (isset($_rt) && is_array($_rt)) {

        // let's make sure these are sanely ordered
        $_od = array();
        $_sn = array();
        $ord = false;
        foreach ($_rt as $_v) {
            $_od["{$_v['menu_id']}"] = $_v['menu_order'];
            if (isset($_sn["{$_v['menu_order']}"])) {
                $ord = true;
            }
            $_sn["{$_v['menu_order']}"] = 1;
        }
        if ($ord) {
            asort($_od, SORT_NUMERIC);
            $req = "UPDATE {$tbl} SET menu_order = CASE menu_id\n";
            $num = 100;
            foreach ($_od as $mid => $mord) {
                $req .= "WHEN {$mid} THEN {$num}\n";
                $num++;
            }
            $req .= "ELSE menu_id END";
            jrCore_db_query($req);

            // Refresh
            $req = "SELECT * FROM {$tbl} ORDER BY menu_order ASC";
            $_rt = jrCore_db_query($req, 'NUMERIC');
        }
        unset($_od, $_sn, $ord);

        $pass = jrCore_get_option_image('pass');
        $fail = jrCore_get_option_image('fail');
        $top = 0;
        $_qt = jrProfile_get_quotas();
        foreach ($_rt as $k => $_v) {

            if ($_v['menu_module'] != 'CustomEntry' && !jrCore_module_is_active($_v['menu_module'])) {
                continue;
            }

            $dat = array();
            if (isset($_v['menu_module'])) {
                $dat[1]['title'] = jrCore_get_module_icon_html($_v['menu_module'], 32);
            }
            else {
                $dat[1]['title'] = jrCore_get_module_icon_html('default', 32);
            }
            if (isset($k) && $k > 0) {
                $dat[2]['title'] = jrCore_page_button("smu-{$k}", '&#8679;', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_menu_move_save/id={$_v['menu_id']}/top={$top}')");
            }
            else {
                $dat[2]['title'] = '&nbsp;';
            }
            $top = $_v['menu_id'];
            $dat[2]['class'] = 'center';
            if (isset($_lang["{$_v['menu_module']}"]["{$_v['menu_label']}"])) {
                $url = jrCore_get_module_url($_v['menu_module']);
                $dat[3]['title'] = jrCore_page_button("sml-{$k}", $_lang["{$_v['menu_module']}"]["{$_v['menu_label']}"], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/admin/language?id={$_v['menu_label']}')");
            }
            else {
                $dat[3]['title'] = $_v['menu_label'];
            }
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = $_v['menu_action'];
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = (isset($_v['menu_active']) && $_v['menu_active'] === 'on') ? $pass : $fail;
            $dat[5]['class'] = 'center';
            if (strpos($_v['menu_groups'], ',')) {
                $_ot = array();
                foreach (explode(',', $_v['menu_groups']) as $grp) {
                    if (isset($grp) && is_numeric($grp) && isset($_qt[$grp])) {
                        $_ot[] = $_qt[$grp];
                    }
                    else {
                        $_ot[] = $grp;
                    }
                }
                $dat[6]['title'] = implode('<br>', $_ot);
            }
            else {
                $dat[6]['title'] = $_v['menu_groups'];
            }
            $dat[6]['class'] = 'center';
            $dat[7]['title'] = jrCore_page_button("smm-{$k}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_menu_modify/id={$_v['menu_id']}')");

            // We can only delete entries that we have created
            if (isset($_v['menu_module']) && isset($_mods["{$_v['menu_module']}"])) {
                $dat[8]['title'] = jrCore_page_button("smd-{$k}", 'delete', 'disabled');
            }
            else {
                $dat[8]['title'] = jrCore_page_button("smd-{$k}", 'delete', "jrCore_confirm('Delete this menu entry?', '', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_menu_delete_save/id={$_v['menu_id']}')} )");
            }
            $dat[8]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>There are no custom skin menu entries</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    $_tmp = array(
        'submit_value' => 'create new entry',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools"
    );
    jrCore_form_create($_tmp);

    // New Menu Entry
    $_tmp = array(
        'name'       => 'new_menu_label',
        'label'      => 'new menu label',
        'help'       => 'Enter the label you would like to appear on this new Menu Entry.',
        'type'       => 'text',
        'validate'   => 'printable',
        'required'   => true,
        'onkeypress' => "if (event && event.keyCode == 13) return false;"
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// skin_menu_save
//------------------------------
function view_jrCore_skin_menu_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    $tbl = jrCore_db_table_name('jrCore', 'menu');
    $_post['new_menu_label'] = substr($_post['new_menu_label'], 0, 127);
    $req = "INSERT INTO {$tbl} (menu_module,menu_active,menu_label,menu_order) VALUES ('CustomEntry','0','" . jrCore_db_escape($_post['new_menu_label']) . "',100)";
    $mid = jrCore_db_query($req, 'INSERT_ID');
    if ($mid && jrCore_checktype($mid, 'number_nz')) {
        jrCore_delete_all_cache_entries();
        jrCore_set_form_notice('success', 'The new menu item was successfully created');
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_menu_modify/id={$mid}");
    }
    jrCore_set_form_notice('error', 'Unable to create new menu entry in database - please try again');
    jrCore_form_result();
}

//------------------------------
// skin_menu_move_save
//------------------------------
function view_jrCore_skin_menu_move_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid menu_id - please try again');
        jrCore_location('referrer');
    }
    if (!isset($_post['top']) || !jrCore_checktype($_post['top'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid top id - please try again');
        jrCore_location('referrer');
    }
    $pid = (int) $_post['id'];
    $tid = (int) $_post['top'];
    $tbl = jrCore_db_table_name('jrCore', 'menu');
    $req = "SELECT * FROM {$tbl} WHERE menu_id IN('{$pid}','{$tid}')";
    $_rt = jrCore_db_query($req, 'menu_id');
    if (isset($_rt) && is_array($_rt)) {
        if (!isset($_rt[$pid])) {
            jrCore_set_form_notice('error', 'invalid menu_id - please try again');
            jrCore_location('referrer');
        }
        if (!isset($_rt[$tid])) {
            jrCore_set_form_notice('error', 'invalid top id - please try again');
            jrCore_location('referrer');
        }
        // Move Up
        if ($_rt[$pid]['menu_order'] == $_rt[$tid]['menu_order']) {
            $ord = $_rt[$tid]['menu_order'] - 1;
        }
        else {
            $ord = $_rt[$tid]['menu_order'];
        }
        $req = "UPDATE {$tbl} SET menu_order = '{$ord}' WHERE menu_id = '{$pid}' LIMIT 1";
        jrCore_db_query($req);

        $ord = $_rt[$pid]['menu_order'];
        $req = "UPDATE {$tbl} SET menu_order = '{$ord}' WHERE menu_id = '{$tid}' LIMIT 1";
        jrCore_db_query($req);
    }
    jrCore_delete_all_cache_entries();
    jrCore_location('referrer');
    return true;
}

//------------------------------
// skin_menu_disable_save
//------------------------------
function view_jrCore_skin_menu_disable_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid menu_id - please try again');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrCore', 'menu');
    $req = "UPDATE {$tbl} SET menu_active = 'off' WHERE menu_id = '{$_post['id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        jrCore_set_form_notice('success', 'The menu item was successfully disabled');
    }
    else {
        jrCore_set_form_notice('error', 'Unable to disable menu entry in database - please try again');
    }
    jrCore_location('referrer');
}

//------------------------------
// skin_menu_delete_save
//------------------------------
function view_jrCore_skin_menu_delete_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid menu_id - please try again');
        jrCore_location('referrer');
    }
    $mid = (int) $_post['id'];
    $tbl = jrCore_db_table_name('jrCore', 'menu');
    $req = "DELETE FROM {$tbl} WHERE menu_id = {$mid}";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        jrCore_set_form_notice('success', 'The menu item was successfully deleted');
    }
    else {
        jrCore_set_form_notice('error', 'Unable to delete menu entry from database - please try again');
    }
    jrCore_location('referrer');
}

//------------------------------
// skin_menu_modify
//------------------------------
function view_jrCore_skin_menu_modify($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid menu_id - please try again');
        jrCore_location('referrer');
    }
    // Get info
    $tbl = jrCore_db_table_name('jrCore', 'menu');
    $req = "SELECT * FROM {$tbl}";
    $_me = jrCore_db_query($req, 'NUMERIC');

    $_rt = array();
    $_ct = array();
    foreach ($_me as $_v) {
        if (isset($_v['menu_id']) && $_v['menu_id'] == $_post['id']) {
            $_rt = $_v;
        }
        if (isset($_v['menu_category']) && strlen($_v['menu_category']) > 0) {
            $_ct["{$_v['menu_category']}"] = $_v['menu_category'];
        }
    }
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid menu_id - please try again');
        jrCore_location('referrer');
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore', 'tools');
    jrCore_page_banner('modify menu entry');

    $_tmp = array(
        'submit_value'     => 'save changes',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_menu",
        'values'           => $_rt,
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // ID
    $_tmp = array(
        'name'  => 'id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    // Label
    if (isset($_rt['menu_label']) && jrCore_checktype($_rt['menu_label'], 'number_nz')) {
        $murl = jrCore_get_module_url($_rt['menu_module']);
        $html = jrCore_page_button('lang-id-link', "{$_rt['menu_label']} - Change in Language Strings", "jrCore_window_location('{$_conf['jrCore_base_url']}/{$murl}/admin/language?id={$_rt['menu_label']}')");
        jrCore_page_custom($html, 'label');
    }
    else {
        $_tmp = array(
            'name'     => 'menu_label',
            'label'    => 'label',
            'help'     => 'This is the text that will appear as the label for the menu entry.<br><br><strong>Note:</strong> You can enter a language index ID here to use a language entry in place of a text label.',
            'type'     => 'text',
            'validate' => 'printable',
            'required' => true
        );
        jrCore_form_field_create($_tmp);
    }

    // Category
    $_tmp = array(
        'name'     => 'menu_category',
        'label'    => 'category',
        'help'     => 'If your skin menu supports grouping menu entries into categories, you can enter the category for this link here.',
        'type'     => 'select_and_text',
        'options'  => $_ct,
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    // URL
    if (isset($_rt['menu_module']) && $_rt['menu_module'] == 'CustomEntry') {
        $_pt = array();
        if (jrCore_module_is_active('jrPage')) {
            $_sp = array(
                'search'        => array(
                    'page_location = 0'
                ),
                'return_keys'   => array('_item_id', 'page_title', 'page_title_url'),
                'order_by'      => array(
                    'page_title' => 'asc'
                ),
                'skip_triggers' => true,
                'limit'         => 250
            );
            $_pg = jrCore_db_search_items('jrPage', $_sp);
            if ($_pg && is_array($_pg) && isset($_pg['_items'])) {
                $purl = jrCore_get_module_url('jrPage');
                foreach ($_pg['_items'] as $_page) {
                    $_pt["{$purl}/{$_page['_item_id']}/{$_page['page_title_url']}"] = $_page['page_title'];
                }
            }
            // If we have a custom URL, insert int
            if (isset($_rt['menu_action']) && strlen($_rt['menu_action']) > 0 && !isset($_pt["{$_rt['menu_action']}"])) {
                $_pt["{$_rt['menu_action']}"] = $_rt['menu_action'];
            }
        }
        if (count($_pt) > 0) {
            $_tmp = array(
                'name'     => 'menu_action',
                'label'    => 'linked URL',
                'help'     => 'This is the page or module/view that will be loaded when the menu item is clicked on',
                'type'     => 'select_and_text',
                'options'  => $_pt,
                'validate' => 'printable',
                'required' => true
            );
            jrCore_form_field_create($_tmp);
        }
        else {
            $_tmp = array(
                'name'     => 'menu_action',
                'label'    => 'linked URL',
                'help'     => 'This is the module/view that will be loaded when the menu item is clicked on',
                'type'     => 'text',
                'validate' => 'printable',
                'required' => true
            );
            jrCore_form_field_create($_tmp);
        }
    }

    // Group
    $_grp = array(
        'all'     => 'Everyone',
        'master'  => 'Master Admins',
        'admin'   => 'Admin Users',
        'power'   => 'Power Users',
        'multi'   => 'Multi Profile Users',
        'user'    => 'Users Only (logged in)',
        'visitor' => 'Visitors Only (not logged in)'
    );
    $_qt  = jrProfile_get_quotas();
    if (isset($_qt) && is_array($_qt)) {
        foreach ($_qt as $qid => $qname) {
            $_grp[$qid] = "Quota: {$qname}";
        }
    }
    $size = 12;
    if (count($_grp) < 12) {
        $size = count($_grp);
    }
    $_tmp = array(
        'name'     => 'menu_groups',
        'label'    => 'visible to',
        'sublabel' => 'select multiple',
        'help'     => 'Select the group(s) of users that will be able to see this menu entry.',
        'type'     => 'select_multiple',
        'options'  => $_grp,
        'required' => true,
        'size'     => $size
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'menu_active',
        'label'    => 'active',
        'help'     => 'Is this menu entry active?',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'default'  => 'on',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// skin_menu_modify_save
//------------------------------
function view_jrCore_skin_menu_modify_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid menu_id - please try again');
        jrCore_form_result('referrer');
    }
    $tbl = jrCore_db_table_name('jrCore', 'menu');
    $req = "SELECT * FROM {$tbl} WHERE menu_id = '{$_post['id']}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid menu_id - please try again');
        jrCore_form_result('referrer');
    }

    // Update...
    $cat = jrCore_db_escape($_post['menu_category']);
    $act = '';
    if (isset($_rt['menu_module']) && $_rt['menu_module'] == 'CustomEntry') {
        $sav = jrCore_db_escape($_post['menu_action']);
        $act = "menu_unique = '{$sav}',menu_action = '{$sav}',";

        // Make sure we are unique...
        $req = "SELECT * FROM {$tbl} WHERE menu_module = 'CustomEntry' AND menu_category = '{$cat}' AND menu_action = '{$sav}' AND menu_id != '{$_post['id']}' LIMIT 1";
        $_ex = jrCore_db_query($req, 'SINGLE');
        if ($_ex && is_array($_ex)) {
            jrCore_set_form_notice('error', 'There is already a menu entry using the Category and Linked URL - please enter something different');
            jrCore_form_result('referrer');
        }
    }
    $req = "UPDATE {$tbl} SET
              menu_label    = '" . jrCore_db_escape($_post['menu_label']) . "',{$act}
              menu_category = '{$cat}',
              menu_groups   = '" . jrCore_db_escape(implode(',', $_post['menu_groups'])) . "',
              menu_active   = '" . jrCore_db_escape($_post['menu_active']) . "'
             WHERE menu_id = '{$_post['id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (!$cnt || $cnt !== 1) {
        jrCore_set_form_notice('error', 'Error updating menu entry in the database - please try again');
    }
    else {
        jrCore_set_form_notice('success', 'The menu entry was successfully updated');
        jrCore_form_delete_session();
    }
    jrCore_form_result('referrer');
}

//------------------------------
// search
//------------------------------
function view_jrCore_search($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_include_admin_menu();

    $_tabs['results'] = array(
        'label'  => 'search results',
        'url'    => jrCore_get_current_url(),
        'active' => 1
    );
    jrCore_page_tab_bar($_tabs);

    if (!isset($_post['ss']) || strlen($_post['ss']) === 0) {
        jrCore_page_banner('search results');
        jrCore_set_form_notice('error', 'You forgot to enter a search string');
        jrCore_get_form_notice();
    }
    else {
        $fnd = false;
        $src = jrCore_db_escape($_post['ss']);

        // Check if we are searching modules or skins
        $_mi = array();
        $tbl = jrCore_db_table_name('jrCore', 'setting');
        if (isset($_post['sa']) && $_post['sa'] == 'skin') {
            $_sk = jrCore_get_skins();
            $req = "SELECT * FROM {$tbl} WHERE (`module` LIKE '%{$src}%' OR `name` LIKE '%{$src}%' OR `label` LIKE '%{$src}%' OR `help` LIKE '%{$src}%') AND `type` != 'hidden' AND module IN('" . implode("','", $_sk) . "') ORDER BY `module` ASC, `label` ASC";
            // See if we have matching skins
            $_ms = jrCore_get_skins();
            if ($_ms && is_array($_ms)) {
                foreach ($_ms as $sd => $sn) {
                    $_mt = jrCore_skin_meta_data($sd);
                    if (stripos(' ' . $sd, $_post['ss']) || (isset($_mt['title']) && stripos(' ' . $_mt['title'], $_post['ss']))) {
                        $_mi[$sd] = (isset($_mt['title'])) ? $_mt['title'] : $sd;
                    }
                }
            }
        }
        else {
            $req = "SELECT * FROM {$tbl} WHERE (`module` LIKE '%{$src}%' OR `name` LIKE '%{$src}%' OR `label` LIKE '%{$src}%' OR `help` LIKE '%{$src}%') AND `type` != 'hidden' AND module IN('" . implode("','", array_keys($_mods)) . "') ORDER BY `module` ASC, `label` ASC";
            foreach ($_mods as $sd => $sn) {
                if (stripos(' ' . $sd, $_post['ss']) || stripos(' ' . $sn['module_name'], $_post['ss'])) {
                    $_mi[$sd] = $sn['module_name'];
                }
            }
        }
        $_cf = jrCore_db_query($req, 'NUMERIC');

        jrCore_page_banner("search results for &quot;" . jrCore_entity_string($_post['ss']) . '&quot;');
        jrCore_page_title('search results');
        if (is_array($_cf) || count($_mi) > 0) {
            $fnd = true;
        }

        // Show DASHBOARD tools
        $prurl = jrCore_get_module_url('jrProfile');

        if (!isset($_post['sa']) || $_post['sa'] != 'skin') {
            $_dmss = array();
            $_dash = array(
                'Dashboard'     => array("{$_post['module_url']}/dashboard", 'The System Dashboard &quot;BigView&quot;'),
                'Users Online'  => array("{$_post['module_url']}/dashboard/online", 'View Users that are currently active on your system'),
                'Pending Users' => array("{$_post['module_url']}/dashboard/pending_users", 'Viewing Pending User Accounts'),
                'Pending Items' => array("{$_post['module_url']}/dashboard/pending", 'View Pending Items created by Users'),
                'Activity Log'  => array("{$_post['module_url']}/dashboard/activity", "View the system Activity Logs, including Debug and Error logs"),
                'Data Browser'  => array("{$prurl}/dashboard/browser", 'View the DataStore browser for active modules on the system'),
                'Recycle Bin'   => array("{$_post['module_url']}/dashboard/recycle_bin", 'View, delete and restore deleted items in the system Recycle Bin'),
                'Queue Viewer'  => array("{$_post['module_url']}/dashboard/queue_viewer", 'View the system Queue and any active Queue Workers')
            );
            foreach ($_dash as $db => $_view) {
                if (stripos(' ' . $db, $_post['ss']) || stripos(' ' . $_view[1], $_post['ss'])) {
                    $_dmss[$db] = $_view;
                }
            }
            if (count($_dmss) > 0) {

                $dat             = array();
                $dat[1]['title'] = 'module';
                $dat[1]['width'] = '5%';
                $dat[2]['title'] = 'dashboard';
                $dat[2]['width'] = '35%;';
                $dat[3]['title'] = 'help';
                $dat[3]['width'] = '50%';
                $dat[4]['title'] = 'view';
                $dat[4]['width'] = '10%';
                jrCore_page_table_header($dat);

                foreach ($_dmss as $db => $_view) {
                    $dat             = array();
                    $dat[1]['title'] = jrCore_get_module_icon_html('jrCore', 48);
                    $dat[2]['title'] = "<h3>{$db}</h3>";
                    $dat[3]['title'] = $_view[1];
                    $dat[4]['title'] = jrCore_page_button("s{$db}", 'view', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_view[0]}')");
                    $dat[4]['class'] = 'center';
                    jrCore_page_table_row($dat);
                }
                jrCore_page_table_footer();
            }
        }

        // Show matching modules or skins
        if (count($_mi) > 0) {
            $dat = array();
            if (isset($_post['sa']) && $_post['sa'] == 'skin') {
                $dat[1]['title'] = 'skin';
                $tag = 'skin name';
            }
            else {
                $dat[1]['title'] = 'module';
                $tag = 'module name';
            }
            $dat[1]['width'] = '5%';
            $dat[2]['title'] = $tag;
            $dat[2]['width'] = '35%';
            $dat[3]['title'] = 'description';
            $dat[3]['width'] = '50%';
            $dat[4]['title'] = 'info';
            $dat[4]['width'] = '10%';
            jrCore_page_table_header($dat);

            foreach ($_mi as $dir => $name) {
                $dat             = array();
                $dat[2]['title'] = "<h3>{$name}</h3>";
                $dat[3]['title'] = $_mods[$dir]['module_description'];
                if (isset($_post['sa']) && $_post['sa'] == 'skin') {
                    $dat[1]['title'] = jrCore_get_skin_icon_html($dir, 48);
                    $dat[4]['title'] = jrCore_page_button("v{$dir}", 'info', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_mods['jrCore']['module_url']}/skin_admin/info/skin={$dir}')");
                }
                else {
                    $dat[1]['title'] = jrCore_get_module_icon_html($dir, 48);
                    $dat[4]['title'] = jrCore_page_button("v{$dir}", 'info', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_mods[$dir]['module_url']}/admin/info')");
                }
                $dat[4]['class'] = 'center';
                jrCore_page_table_row($dat);
            }
            jrCore_page_table_footer();
        }

        if ($_cf && is_array($_cf)) {

            // Prune out ones we are not going to show
            foreach ($_cf as $k => $_fld) {
                if (isset($_post['sa']) && $_post['sa'] == 'skin') {
                    if (!is_dir(APP_DIR . "/skins/{$_fld['module']}")) {
                        unset($_cf[$k]);
                        continue;
                    }
                }
                else {
                    if (!is_dir(APP_DIR . "/modules/{$_fld['module']}") || !jrCore_module_is_active($_fld['module'])) {
                        unset($_cf[$k]);
                        continue;
                    }
                }
            }
            if (count($_cf) > 0) {

                $dat = array();
                if (isset($_post['sa']) && $_post['sa'] == 'skin') {
                    $dat[1]['title'] = 'skin';
                }
                else {
                    $dat[1]['title'] = 'module';
                }
                $dat[1]['width'] = '5%;';
                $dat[2]['title'] = 'config option';
                $dat[2]['width'] = '35%;';
                $dat[3]['title'] = 'help';
                $dat[3]['width'] = '50%;';
                $dat[4]['title'] = 'modify';
                $dat[4]['width'] = '10%;';
                jrCore_page_table_header($dat);

                foreach ($_cf as $_fld) {

                    if (!isset($_fld['section']) || strlen($_fld['section']) === 0) {
                        $_fld['section'] = 'general settings';
                    }
                    $dat = array();
                    if (isset($_post['sa']) && $_post['sa'] == 'skin') {
                        if (!is_dir(APP_DIR . "/skins/{$_fld['module']}")) {
                            continue;
                        }
                        $dat[1]['title'] = '<img src="' . $_conf['jrCore_base_url'] . '/skins/' . $_fld['module'] . '/icon.png" alt="' . $_fld['module'] . '" title="' . $_fld['module'] . '" width="48" height="48">';
                        $dat[4]['title'] = jrCore_page_button("m{$_fld['name']}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/global/skin={$_fld['module']}/section=" . urlencode($_fld['section']) . "/hl={$_fld['name']}')");
                    }
                    else {
                        if (!is_dir(APP_DIR . "/modules/{$_fld['module']}") || !jrCore_module_is_active($_fld['module'])) {
                            continue;
                        }
                        $murl            = jrCore_get_module_url($_fld['module']);
                        $dat[1]['title'] = jrCore_get_module_icon_html($_fld['module'], 48);
                        $dat[4]['title'] = jrCore_page_button("m{$_fld['name']}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$murl}/admin/global/section=" . urlencode($_fld['section']) . "/hl={$_fld['name']}')");
                    }
                    $dat[1]['class'] = 'center';
                    if (isset($_fld['section'])) {
                        $dat[2]['title'] = '<h3>' . ucwords($_fld['section']) . ' &raquo; ' . ucwords($_fld['label']) . '</h3>';
                    }
                    else {
                        $dat[2]['title'] = '<h3>' . ucwords($_fld['label']) . '</h3>';
                    }
                    $dat[3]['title'] = $_fld['help'];
                    $dat[4]['class'] = 'center';
                    jrCore_page_table_row($dat);
                }
                jrCore_page_table_footer();
            }
        }

        if (!isset($_post['sa']) || $_post['sa'] != 'skin') {
            $tbl = jrCore_db_table_name('jrProfile', 'quota_setting');
            $req = "SELECT * FROM {$tbl} WHERE (`module` LIKE '%{$src}%' OR `name` LIKE '%{$src}%' OR `label` LIKE '%{$src}%' OR `help` LIKE '%{$src}%') AND `type` != 'hidden' AND module IN('" . implode("','", array_keys($_mods)) . "') ORDER BY `module` ASC, `label` ASC";
            $_cf = jrCore_db_query($req, 'NUMERIC');

            if ($_cf && is_array($_cf) && count($_cf) > 0) {

                // Prune out ones we are not going to show
                foreach ($_cf as $k => $_fld) {
                    if (isset($_post['sa']) && $_post['sa'] == 'skin') {
                        if (!is_dir(APP_DIR . "/skins/{$_fld['module']}")) {
                            unset($_cf[$k]);
                            continue;
                        }
                    }
                    else {
                        if (!is_dir(APP_DIR . "/modules/{$_fld['module']}") || !jrCore_module_is_active($_fld['module'])) {
                            unset($_cf[$k]);
                            continue;
                        }
                    }
                }
                if (count($_cf) > 0) {
                    $dat             = array();
                    $dat[1]['title'] = 'module';
                    $dat[1]['width'] = '5%;';
                    $dat[2]['title'] = 'quota option';
                    $dat[2]['width'] = '35%;';
                    $dat[3]['title'] = 'help';
                    $dat[3]['width'] = '50%;';
                    $dat[4]['title'] = 'modify';
                    $dat[4]['width'] = '10%;';
                    jrCore_page_table_header($dat);

                    foreach ($_cf as $_fld) {
                        if (!is_dir(APP_DIR . "/modules/{$_fld['module']}") || !jrCore_module_is_active($_fld['module'])) {
                            continue;
                        }
                        $fnd             = true;
                        $dat             = array();
                        $dat[1]['title'] = jrCore_get_module_icon_html($_fld['module'], 48);
                        $dat[1]['class'] = 'center';
                        $dat[2]['title'] = '<h3>' . ucwords($_fld['label']) . '</h3>';
                        $dat[3]['title'] = $_fld['help'];
                        $murl            = jrCore_get_module_url($_fld['module']);
                        $dat[4]['title'] = jrCore_page_button("m{$_fld['name']}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$murl}/admin/quota/hl={$_fld['name']}#ff-{$_fld['name']}')");
                        $dat[4]['class'] = 'center';
                        jrCore_page_table_row($dat);
                    }
                    jrCore_page_table_footer();
                }
            }

            // Tools
            if (!isset($_post['sa']) || $_post['sa'] != 'skin') {
                $_tool = jrCore_get_registered_module_features('jrCore', 'tool_view');
                $_show = array();
                if ($_tool && is_array($_tool)) {
                    foreach ($_tool as $tool_mod => $_tools) {
                        foreach ($_tools as $view => $_inf) {
                            if (stristr($_inf[0], $_post['ss']) || stristr($_inf[1], $_post['ss'])) {
                                $fnd     = true;
                                $_show[] = array(
                                    'module' => $tool_mod,
                                    'view'   => $view,
                                    'label'  => $_inf[0],
                                    'help'   => $_inf[1]
                                );
                            }
                        }
                    }
                    if (count($_show) > 0) {

                        $dat             = array();
                        $dat[1]['title'] = 'module';
                        $dat[1]['width'] = '5%;';
                        $dat[2]['title'] = 'tool name';
                        $dat[2]['width'] = '35%;';
                        $dat[3]['title'] = 'help';
                        $dat[3]['width'] = '50%;';
                        $dat[4]['title'] = 'view';
                        $dat[4]['width'] = '10%;';
                        jrCore_page_table_header($dat);

                        foreach ($_show as $k => $_fld) {
                            if (!is_dir(APP_DIR . "/modules/{$_fld['module']}") || !jrCore_module_is_active($_fld['module'])) {
                                continue;
                            }
                            $dat             = array();
                            $dat[1]['title'] = jrCore_get_module_icon_html($_fld['module'], 48);
                            $dat[1]['class'] = 'center';
                            $dat[2]['title'] = '<h3>' . ucwords($_fld['label']) . '</h3>';
                            $dat[3]['title'] = $_fld['help'];
                            $murl            = jrCore_get_module_url($_fld['module']);
                            if (strpos($_fld['view'], 'http') !== 0) {
                                $dat[4]['title'] = jrCore_page_button("m{$k}", 'view', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$murl}/{$_fld['view']}')");
                            }
                            else {
                                $dat[4]['title'] = jrCore_page_button("m{$k}", 'view', "jrCore_window_location('{$_fld['view']}')");
                            }
                            $dat[4]['class'] = 'center';
                            jrCore_page_table_row($dat);
                        }
                        jrCore_page_table_footer();
                    }
                }
            }
        }

        if (!$fnd) {
            $dat             = array();
            $dat[1]['title'] = '<p>No results found to match your search</p>';
            $dat[1]['class'] = 'center';
            jrCore_page_table_row($dat);
            jrCore_page_table_footer();
        }
    }
    jrCore_page_display();
}

//------------------------------
// license (magic)
//------------------------------
function view_jrCore_license($_post, $_user, $_conf)
{
    jrUser_master_only();
    // Check for license file
    if (!isset($_post['skin'])) {
        $_mta = jrCore_module_meta_data($_post['module']);
        jrCore_page_banner("{$_mta['name']}: license");
        $lic_file = APP_DIR . "/modules/{$_post['module']}/license.html";
    }
    else {
        $_mta = jrCore_skin_meta_data($_post['skin']);
        jrCore_page_banner("{$_mta['name']}: license");
        $lic_file = APP_DIR . "/skins/{$_post['skin']}/license.html";
    }
    if (is_file($lic_file)) {
        $temp = file_get_contents($lic_file);
        jrCore_page_custom($temp);
    }
    else {
        jrCore_set_form_notice('error', 'NO LICENSE FILE FOUND - contact developer');
        jrCore_get_form_notice();
    }
    jrCore_page_close_button();
    jrCore_page_set_meta_header_only();
    jrCore_page_display();
}

//------------------------------
// dashboard_panels
//------------------------------
function view_jrCore_dashboard_panels($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();

    list(, $row, $col) = explode('-', $_post['_1']);
    $one = (int) $row + 1;
    $two = (int) $col + 1;

    // See if we have an existing function
    $_cfg = array();
    if (isset($_conf['jrCore_dashboard_config']{1})) {
        $_cfg = json_decode($_conf['jrCore_dashboard_config'], true);
    }
    $func = false;
    if (isset($_cfg['_panels'][$row][$col]['f'])) {
        $func = "{$_cfg['_panels'][$row][$col]['f']}|{$_cfg['_panels'][$row][$col]['t']}";
    }

    jrCore_page_banner('Dashboard Panels', "row {$one}, column {$two}");
    $_fnc = array();
    $_tmp = jrCore_get_registered_module_features('jrCore', 'dashboard_panel');
    if ($_tmp) {
        foreach ($_tmp as $mod => $_opts) {
            $nam = $_mods[$mod]['module_name'];
            if (!isset($_fnc[$nam])) {
                $_fnc[$nam] = array();
            }
            foreach ($_opts as $title => $fnc) {
                $key              = "{$fnc}|{$title}";
                $_fnc[$nam][$key] = $title;
            }
        }
    }
    $_tmp = array(
        'name'     => 'panel',
        'label'    => 'available panels',
        'help'     => 'Select the panel you would like to appear in this dashboard location',
        'type'     => 'select',
        'options'  => $_fnc,
        'value'    => $func,
        'onchange' => "jrCore_set_dashboard_panel({$row}, {$col}, $(this).val());",
        'required' => true,
        'size'     => 8
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_close_button('$.modal.close();');
    jrCore_page_set_no_header_or_footer();
    jrCore_page_display();
}

//------------------------------
// set_dashboard_panel
//------------------------------
function view_jrCore_set_dashboard_panel($_post, $_user, $_conf)
{
    jrUser_master_only();
    $_cfg = array();
    if (isset($_conf['jrCore_dashboard_config']{1})) {
        $_cfg = json_decode($_conf['jrCore_dashboard_config'], true);
    }
    if (!isset($_cfg['_panels'])) {
        $_cfg['_panels'] = array();
    }
    $row  = (int) $_post['row'];
    $col  = (int) $_post['col'];
    $_tmp = jrCore_get_registered_module_features('jrCore', 'dashboard_panel');
    $name = false;
    $func = false;
    if ($_tmp) {
        foreach ($_tmp as $mod => $_opts) {
            foreach ($_opts as $title => $fnc) {
                $key = "{$fnc}|{$title}";
                if ($key == $_post['opt']) {
                    $name = $title;
                    $func = $fnc;
                    break;
                }
            }
        }
    }
    if (!$func) {
        // Check for generic DS function
        if (strpos($_post['opt'], 'item count')) {
            list($func, $name) = explode('|', $_post['opt']);
        }
    }
    $_cfg['_panels'][$row][$col] = array('t' => $name, 'f' => $func);
    ksort($_cfg['_panels'], SORT_NUMERIC);
    jrCore_set_setting_value('jrCore', 'dashboard_config', json_encode($_cfg));
    jrCore_delete_config_cache();
    $_rp = array('success' => 'OK');
    jrCore_json_response($_rp);
}

//------------------------------
// dashboard_config
//------------------------------
function view_jrCore_dashboard_config($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_banner("Dashboard Config");

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard"
    );
    jrCore_form_create($_tmp);

    // See if we have our cols and rows
    $rows = 2;
    $cols = 4;
    if (isset($_conf['jrCore_dashboard_config']{1})) {
        $_tmp = json_decode($_conf['jrCore_dashboard_config'], true);
        if ($_tmp && jrCore_checktype($_tmp['rows'], 'number_nz')) {
            $rows = (int) $_tmp['rows'];
        }
        if ($_tmp && jrCore_checktype($_tmp['cols'], 'number_nz')) {
            $cols = (int) $_tmp['cols'];
        }
    }

    // Rows
    $_opt = array(
        1 => '1 Row',
        2 => '2 Rows',
        3 => '3 Rows',
        4 => '4 Rows',
        5 => '5 Rows'
    );
    $_tmp = array(
        'name'     => 'dashboard_rows',
        'label'    => 'number of rows',
        'help'     => 'Select the number of rows you would like to appear in the dashboard',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 2,
        'value'    => $rows,
        'required' => true,
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    // Columns
    $_opt = array(
        2 => '2 Columns',
        3 => '3 Columns',
        4 => '4 Columns',
        5 => '5 Columns'
    );
    $_tmp = array(
        'name'     => 'dashboard_cols',
        'label'    => 'number of columns',
        'help'     => 'Select the number of columns you would like to appear in the dashboard',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => 4,
        'value'    => $cols,
        'required' => true,
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// dashboard_config_save
//------------------------------
function view_jrCore_dashboard_config_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    $_cfg = array();
    if (isset($_conf['jrCore_dashboard_config']{1})) {
        $_cfg = json_decode($_conf['jrCore_dashboard_config'], true);
    }
    $_cfg['rows'] = (int) $_post['dashboard_rows'];
    $_cfg['cols'] = (int) $_post['dashboard_cols'];

    if (jrCore_set_setting_value('jrCore', 'dashboard_config', json_encode($_cfg))) {
        jrCore_delete_config_cache();
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard");
    }
    jrCore_set_form_notice('error', 'Unable to save dashboard config - please try again');
    jrCore_form_result();
}

//------------------------------
// dashboard
//------------------------------
function view_jrCore_dashboard($_post, $_user, $_conf)
{
    global $_mods, $_urls;
    jrUser_admin_only();
    // http://www.site.com/core/dashboard/online
    // http://www.site.com/core/dashboard/pending
    // http://www.site.com/core/dashboard/browser
    $title = '';
    if (!isset($_post['_1'])) {
        $_post['_1'] = 'bigview';
    }

    jrCore_page_dashboard_tabs($_post['_1']);
    switch ($_post['_1']) {

        //------------------------------
        // BIGVIEW
        //------------------------------
        case 'bigview':
            $title = 'Dashboard';
            // Setup timer
            $refresh = '';
            if (!jrCore_is_mobile_device()) {
                $refresh = jrCore_page_button('reload', '60', "jrCore_dashboard_reload_page(60,0);");
            }
            $refresh .= jrCore_page_button('refresh', 'refresh', "location.reload();");
            if (jrUser_is_master()) {
                $refresh .= jrCore_page_button('custom', 'customize', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard_config')");
            }

            // for reload timer
            if (isset($_COOKIE['dash_reload']) && $_COOKIE['dash_reload'] == 'on') {
                $_js = array('jrCore_dashboard_reload_page(60,1);');
            }
            else {
                $_js = array('$(\'#reload\').addClass(\'form_button_disabled\'); jrCore_dashboard_reload_page(60,1);');
            }
            jrCore_create_page_element('javascript_ready_function', $_js);

            jrCore_page_banner('dashboard', $refresh);
            jrCore_get_form_notice();
            jrCore_dashboard_bigview($_post, $_user, $_conf);

            break;

        //------------------------------
        // USERS ONLINE
        //------------------------------
        case 'online':
            $btn = null;
            if (!isset($_conf['jrUser_bot_sessions']) || $_conf['jrUser_bot_sessions'] == 'on') {
                $url = jrCore_strip_url_params(jrCore_get_current_url(), array('show_bots'));
                if (isset($_post['show_bots'])) {
                    $btn = jrCore_page_button('sb', 'Hide Bots', "jrCore_window_location('{$url}')");
                }
                else {
                    $btn = jrCore_page_button('sb', 'Show Bots', "jrCore_window_location('{$url}/show_bots=1')");
                }
            }
            $url  = jrCore_get_module_url('jrUser');
            $btn .= jrCore_page_button('newuser', 'new user account', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/create')");

            jrCore_page_banner('users online', $btn);
            jrCore_get_form_notice();
            jrUser_online_users($_post, $_user, $_conf);
            $title = 'Users Online';
            break;

        //------------------------------
        // PENDING USERS
        //------------------------------
        case 'pending_users':
            $title = 'Pending Users';
            jrCore_page_banner('pending users');
            jrCore_get_form_notice();
            jrUser_dashboard_pending_users($_post, $_user, $_conf);
            break;

        //------------------------------
        // PENDING
        //------------------------------
        case 'pending':

            if (!isset($_post['m']) || !jrCore_module_is_active($_post['m'])) {
                $_post['m'] = jrCore_get_dashboard_default_pending_tab();
            }
            jrCore_dashboard_pending_tabs($_post['m']);
            $title = 'Pending: ' . $_mods["{$_post['m']}"]['module_name'];
            jrCore_page_banner($title);
            jrCore_get_form_notice();
            switch ($_post['m']) {
                case 'jrUser':
                    jrUser_dashboard_pending_users($_post, $_user, $_conf);
                    break;
                default:
                    $func = "{$_post['m']}_dashboard_pending";
                    if (function_exists($func)) {
                        $func($_post, $_user, $_conf);
                    }
                    else {
                        jrCore_dashboard_pending($_post, $_user, $_conf);
                    }
                    break;
            }
            break;

        //------------------------------
        // ACTIVITY LOG
        //------------------------------
        case 'activity':
            $title = 'Activity Log';
            jrCore_show_activity_log($_post, $_user, $_conf, 'dashboard');
            break;

        //------------------------------
        // DATA BROWSER
        //------------------------------
        case 'browser':
            $title = 'Data Browser';
            jrCore_dashboard_browser('dashboard', $_post, $_user, $_conf);
            break;

        //------------------------------
        // RECYCLE BIN
        //------------------------------
        case 'recycle_bin':
            $title = 'Recycle Bin';
            jrCore_dashboard_recycle_bin($_post, $_user, $_conf);
            break;

        //------------------------------
        // QUEUE VIEWER
        //------------------------------
        case 'queue_viewer':
            $title = 'Queue Viewer';
            jrCore_dashboard_queue_viewer($_post, $_user, $_conf);
            break;

        //------------------------------
        // MODULE FUNCTION
        //------------------------------
        default:
            // Do we have a module function that lines up?
            if (isset($_urls["{$_post['_1']}"]) && isset($_post['_2'])) {
                $mod = $_urls["{$_post['_1']}"];
                $fnc = "{$mod}_dashboard_{$_post['_2']}";
                if (function_exists($fnc)) {
                    $title = $fnc($_post, $_user, $_conf);
                }
            }
            break;
    }
    jrCore_page_title($title);

    jrCore_page_display();
}

//------------------------------
// form_designer_reset
//------------------------------
function view_jrCore_form_designer_reset($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['m']) || !isset($_mods["{$_post['m']}"])) {
        jrCore_set_form_notice('error', 'invalid module');
        jrCore_location('referrer');
    }
    if (!isset($_post['v']) || strlen($_post['v']) === 0) {
        jrCore_set_form_notice('error', 'invalid view');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrCore', 'form');
    $req = "DELETE FROM {$tbl} WHERE `module` = '" . jrCore_db_escape($_post['m']) . "' AND `view` = '" . jrCore_db_escape($_post['v']) . "'";
    jrCore_db_query($req);
    jrCore_set_form_notice('success', "The Form Designer fields for the &quot;{$_post['v']}&quot; view were reset");
    if (isset($_SESSION['form_designer_cancel'])) {
        jrCore_location($_SESSION['form_designer_cancel']);
    }
    $url = jrCore_get_module_url($_post['m']);
    jrCore_location("{$_conf['jrCore_base_url']}/{$url}/{$_post['v']}");
}

//------------------------------
// form_designer (magic)
//------------------------------
function view_jrCore_form_designer($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    if (!isset($_post['m']) || !isset($_mods["{$_post['m']}"])) {
        jrCore_notice_page('error', 'invalid module');
    }
    if (!isset($_post['v']) || strlen($_post['v']) === 0) {
        jrCore_notice_page('error', 'invalid view');
    }
    $_fields = jrCore_get_designer_form_fields($_post['m'], $_post['v']);
    if (!$_fields || !is_array($_fields)) {
        $_tmp = jrCore_get_registered_module_features('jrCore', 'designer_form');
        if (!isset($_tmp["{$_post['m']}"]) || !isset($_tmp["{$_post['m']}"]["{$_post['v']}"])) {
            jrCore_notice_page('error', 'This form has not been setup properly to work with the custom form designer');
        }
    }

    // We need to record where we come in from
    $rurl = jrCore_get_local_referrer();
    if (!strpos($rurl, '/form_field_update/') && !strpos($rurl, '/form_designer/')) {
        $_SESSION['form_designer_cancel'] = $rurl;
    }

    $mod = $_post['m'];
    $opt = $_post['v'];
    $url = jrCore_get_module_url('jrCore');

    $_lang = jrUser_load_lang_strings();

    // Show our table of options
    $btn = '';
    $tbl = jrCore_db_table_name('jrCore', 'form');
    $req = "SELECT `view` FROM {$tbl} WHERE `module` = '" . jrCore_db_escape($mod) . "' GROUP BY `view` ORDER by `view` ASC";
    $_rt = jrCore_db_query($req, 'view', false, 'view');
    if ($_rt && is_array($_rt)) {
        $btn = jrCore_page_button('reset', 'Reset This Form', "jrCore_confirm('Reset this form?', 'After resetting you will be redirected to the form so it is re-initialized.', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/form_designer_reset/m={$_post['m']}/v={$_post['v']}') })");
        if (count($_rt) > 1) {
            $jump_url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/form_designer/m={$_post['module']}/v=";
            // Create a Quick Jump list for custom forms for this module
            $btn .= '<select name="designer_form" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $jump_url . "'+ $(this).val())\">\n";
            foreach ($_rt as $option) {
                if ($option == $_post['v']) {
                    $btn .= '<option value="' . $option . '" selected="selected"> ' . $_post['module_url'] . '/' . $option . "</option>\n";
                }
                else {
                    $btn .= '<option value="' . $option . '"> ' . $_post['module_url'] . '/' . $option . "</option>\n";
                }
            }
            $btn .= '</select>';
        }
        else {
            $btn = "{$_post['module_url']}/{$_post['v']}";
        }
    }

    // Check for additional views that have been registered by this module, but have
    // not been setup for customization yet...
    $_tmp = jrCore_get_registered_module_features('jrCore', 'designer_form');
    foreach ($_rt as $option) {
        unset($_tmp[$mod][$option]);
    }
    if (isset($_tmp[$mod]) && count($_tmp[$mod]) > 0) {
        $text = "The following designer forms have not been setup yet for this module:<br><br>";
        foreach ($_tmp[$mod] as $view => $prefix) {
            $text .= "{$_post['module_url']}/{$view}<br>";
        }
        $text .= "<br>These forms will be initialized the first time they are viewed.  It is recommended that you view all forms for this module before using the Form Designer.";
        jrCore_set_form_notice('notice', $text, false);
    }

    // See if our module has a DS prefix, or has registered a designer prefix
    $pfx = jrCore_db_get_prefix($mod);
    if (!$pfx) {
        // Check for registered prefix
        $_tmp = jrCore_get_registered_module_features('jrCore', 'designer_form_prefix');
        if (isset($_tmp[$mod]) && is_array($_tmp[$mod])) {
            $pfx = array_keys($_tmp[$mod]);
            $pfx = reset($pfx);
        }
        else {
            jrCore_notice_page('error', 'This module is not setup with a DataStore prefix - unable to use form designer', 'referrer');
        }
    }

    jrCore_page_banner('form designer', $btn);
    jrCore_get_form_notice();

    $dat             = array();
    $dat[1]['title'] = 'order';
    $dat[1]['width'] = '2%;';
    $dat[2]['title'] = 'label';
    $dat[2]['width'] = '38%;';
    $dat[3]['title'] = 'name';
    $dat[3]['width'] = '15%;';
    $dat[4]['title'] = 'type';
    $dat[4]['width'] = '15%;';
    $dat[5]['title'] = 'active';
    $dat[5]['width'] = '10%;';
    $dat[6]['title'] = 'required';
    $dat[6]['width'] = '10%;';
    $dat[7]['title'] = 'modify';
    $dat[7]['width'] = '5%;';
    $dat[8]['title'] = 'delete';
    $dat[8]['width'] = '5%;';
    jrCore_page_table_header($dat);

    if (is_array($_fields) && count($_fields) > 0) {
        foreach ($_fields as $_fld) {

            $dat = array();
            if ($_fld['order'] > 1) {
                $dat[1]['title'] = jrCore_page_button("o{$_fld['name']}", '&#8679;', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/form_field_order/m={$mod}/v={$opt}/n={$_fld['name']}/o={$_fld['order']}')");
            }
            else {
                $dat[1]['title'] = '';
            }
            $dat[2]['title'] = (is_numeric($_fld['label']) && isset($_lang[$mod]["{$_fld['label']}"])) ? '&nbsp;' . $_lang[$mod]["{$_fld['label']}"] : '&nbsp;' . $_fld['label'];
            $dat[3]['title'] = $_fld['name'];
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = $_fld['type'];
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = (isset($_fld['active']) && $_fld['active'] == '1') ? 'yes' : '<strong>no</strong>';
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = (isset($_fld['required']) && $_fld['required'] == '1') ? 'yes' : 'no';
            $dat[6]['class'] = 'center';
            $dat[7]['title'] = jrCore_page_button("m{$_fld['name']}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/form_field_update/m={$mod}/v={$opt}/n={$_fld['name']}')");
            if ((isset($_fld['locked']) && $_fld['locked'] == '1') || count($_fields) === 1) {
                $dat[8]['title'] = jrCore_page_button("d{$_fld['name']}", 'delete', 'disabled');
            }
            else {
                $dat[8]['title'] = jrCore_page_button("d{$_fld['name']}", 'delete', "jrCore_confirm('Delete this form field?', '', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/form_field_delete/m={$mod}/v={$opt}/n={$_fld['name']}')} )");
            }
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat = array();
        $dat[1]['title'] = 'no custom fields have been created for this form yet';
        $dat[1]['class'] = 'p10 center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    $_tmp = array(
        'submit_value'  => 'create new field',
        'cancel'        => (isset($_SESSION['form_designer_cancel'])) ? $_SESSION['form_designer_cancel'] : $rurl,
        'cancel_detect' => false
    );
    jrCore_form_create($_tmp);

    // Module
    $_tmp = array(
        'name'     => 'field_module',
        'type'     => 'hidden',
        'value'    => $mod,
        'validate' => 'core_string'
    );
    jrCore_form_field_create($_tmp);

    // View
    $_tmp = array(
        'name'     => 'field_view',
        'type'     => 'hidden',
        'value'    => $opt,
        'validate' => 'core_string'
    );
    jrCore_form_field_create($_tmp);

    // New Form Field
    $_tmp = array(
        'name'       => 'new_name',
        'label'      => 'new field name',
        'help'       => "If you would like to create a new field in this form, enter the field name here.<br><br>Note that the new field name must begin with <strong>{$pfx}_</strong> and be all lowercase",
        'type'       => 'text',
        'value'      => "{$pfx}_",
        'validate'   => 'core_string',
        'onkeypress' => "if (event && event.keyCode == 13) return false;"
    );
    jrCore_form_field_create($_tmp);

    if (isset($_post['v']) && ($_post['v'] == 'create' || $_post['v'] == 'update')) {
        $opp = ($_post['v'] == 'create') ? 'update' : 'create';
        // See if this module defines the opposite view
        require_once APP_DIR . "/modules/{$mod}/index.php";
        if (function_exists("view_{$mod}_{$opp}")) {
            if (isset($_rt[$opp])) {
                // Link to Update/Create
                $_tmp = array(
                    'name'     => "linked_form_field",
                    'label'    => "add to {$opp} form",
                    'help'     => "If you would like the same field name created for the &quot;{$opp}&quot; form view, check this option",
                    'type'     => 'checkbox',
                    'value'    => 'on',
                    'validate' => 'onoff'
                );
                jrCore_form_field_create($_tmp);
            }
        }
    }
    jrCore_page_display();
}

//------------------------------
// form_designer_save
//------------------------------
function view_jrCore_form_designer_save($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_form_validate($_post);
    if (!isset($_post['field_module']) || !isset($_mods["{$_post['field_module']}"])) {
        jrCore_set_form_notice('error', 'Invalid module');
        jrCore_form_result();
    }
    if (!isset($_post['field_view']) || strlen($_post['field_view']) === 0) {
        jrCore_set_form_notice('error', 'Invalid view');
        jrCore_form_result();
    }
    if (isset($_post['new_name']) && $_post['new_name'] !== strtolower($_post['new_name'])) {
        jrCore_set_form_notice('error', 'New Field Name must be all lowercase');
        jrCore_form_result();
    }
    $mod     = $_post['field_module'];
    $opt     = $_post['field_view'];
    $_fields = jrCore_get_designer_form_fields($mod, $opt);
    if (!$_fields || !is_array($_fields)) {
        $_tmp = jrCore_get_registered_module_features('jrCore', 'designer_form');
        if (!isset($_tmp[$mod]) || !isset($_tmp[$mod][$opt])) {
            jrCore_set_form_notice('error', 'This form has not been setup properly to work with the custom form designer');
            jrCore_form_result();
        }
    }
    $nam = trim(strtolower($_post['new_name']));
    // Make sure we don't already exist
    if (isset($_fields[$nam]) && is_array($_fields[$nam])) {
        jrCore_set_form_notice('error', 'The name you entered is already being used in this form - please enter a different name');
        jrCore_form_field_hilight('new_name');
        jrCore_form_result();
    }
    // See if our module has a DS prefix, or has registered a designer prefix
    $pfx = jrCore_db_get_prefix($mod);
    if (!$pfx) {
        // Check for registered prefix
        $_tmp = jrCore_get_registered_module_features('jrCore', 'designer_form_prefix');
        if (!isset($_tmp[$mod]) || !is_array($_tmp[$mod])) {
            jrCore_set_form_notice('error', 'This module is not setup with a DataStore prefix - unable to use form designer');
            jrCore_form_result();
        }
    }
    $prfx = jrCore_db_get_prefix($mod);
    if (strpos($_post['new_name'], "{$prfx}_") !== 0) {
        jrCore_set_form_notice('error', "The new field name must begin with &quot;{$prfx}_&quot;");
        jrCore_form_field_hilight('new_name');
        jrCore_form_result();
    }
    // We can't just use the prefix
    if ($_post['new_name'] == $prfx || $_post['new_name'] == "{$prfx}_") {
        jrCore_set_form_notice('error', "Please enter a valid field name beyond just the prefix");
        jrCore_form_field_hilight('new_name');
        jrCore_form_result();
    }

    // Validate the field does not already exist in our DS
    if (jrCore_db_item_key_exists($mod, $nam)) {

        // See if this is already being used in another form for this module
        $tbl = jrCore_db_table_name('jrCore', 'form');
        $req = "SELECT `view` FROM {$tbl} WHERE `module` = '" . jrCore_db_escape($mod) . "' AND `view` = '" . jrCore_db_escape($opt) . "' AND `name` = '" . jrCore_db_escape($nam) . "' LIMIT 1";
        $_ex = jrCore_db_query($req, 'SINGLE');
        if ($_ex && is_array($_ex)) {
            // This is a field we cannot create
            jrCore_set_form_notice('error', 'The name you entered is already being used in this form - please enter a different name (2)');
            jrCore_form_field_hilight('new_name');
            jrCore_form_result();
        }
    }

    // Looks good - create new form field
    $_field = array(
        'name'   => $_post['new_name'],
        'type'   => 'text',
        'label'  => $_post['new_name'],
        'locked' => '0'
    );
    jrCore_set_flag('jrcore_designer_create_custom_field', 1);
    $tmp = jrCore_verify_designer_form_field($mod, $opt, $_field);
    if ($tmp) {
        // See if we are also adding it to the create/update view
        if (isset($_post['linked_form_field']) && $_post['linked_form_field'] == 'on') {
            $opp = ($opt == 'create') ? 'update' : 'create';
            $tmp = jrCore_verify_designer_form_field($mod, $opp, $_field);
            if (!$tmp) {
                jrCore_set_form_notice('error', "An error was encountered inserting the new field into the {$opp} form - please try again");
                jrCore_form_result();
            }
        }
        $url = jrCore_get_module_url($mod);
        jrCore_form_delete_session();

        // Insert defaults into each existing record
        // This is required otherwise these records may not be searchable
        jrCore_db_create_default_key($_post['field_module'], $_post['new_name'], '');

        jrCore_form_result("{$_conf['jrCore_base_url']}/{$url}/form_field_update/m={$mod}/v={$opt}/n={$_post['new_name']}");
        return true;
    }
    jrCore_set_form_notice('error', 'An error was encountered saving the new for field to the database - please try again');
    jrCore_form_result();
    return true;
}

//------------------------------
// form_field_delete
//------------------------------
function view_jrCore_form_field_delete($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['m']) || !isset($_mods["{$_post['m']}"])) {
        jrCore_set_form_notice('error', 'Invalid module');
        jrCore_form_result('referrer');
    }
    if (!isset($_post['v']) || strlen($_post['v']) === 0) {
        jrCore_set_form_notice('error', 'Invalid view');
        jrCore_form_result('referrer');
    }
    if (!isset($_post['n']) || strlen($_post['n']) === 0) {
        jrCore_set_form_notice('error', 'Invalid name');
        jrCore_form_result('referrer');
    }
    $mod = jrCore_db_escape($_post['m']);
    $opt = jrCore_db_escape($_post['v']);
    $nam = jrCore_db_escape($_post['n']);
    $tbl = jrCore_db_table_name('jrCore', 'form');
    $req = "SELECT * FROM {$tbl} WHERE `module` = '{$mod}' AND `view` = '{$opt}' and `name` = '{$nam}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'Invalid Field - not found in custom forms table');
        jrCore_form_result('referrer');
    }
    // Delete field
    $req = "DELETE FROM {$tbl} WHERE `module` = '{$mod}' AND `view` = '{$opt}' and `name` = '{$nam}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {

        // We need to remove any language strings for this custom field
        $_fnd = array();
        $_rem = array('label', 'sublabel', 'help');
        foreach ($_rem as $k) {
            if (isset($_rt[$k]) && jrCore_checktype($_rt[$k], 'number_nz')) {
                $_fnd[] = (int) $_rt[$k];
            }
        }
        if (count($_fnd) > 0) {
            $tbl = jrCore_db_table_name('jrUser', 'language');
            $req = "DELETE FROM {$tbl} WHERE lang_module = '{$mod}' AND lang_key IN(" . implode(',', $_fnd) . ")";
            jrCore_db_query($req);
        }

        // We need to reset any existing Form Sessions for this view
        jrCore_form_delete_session_view($_post['m'], $_post['v']);
        jrCore_set_form_notice('success', 'The form field was successfully deleted');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered trying to delete the form field - please try again');
    }
    jrCore_form_result('referrer');
}

//------------------------------
// form_field_order
//------------------------------
function view_jrCore_form_field_order($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['m']) || !isset($_mods["{$_post['m']}"])) {
        jrCore_set_form_notice('error', 'Invalid module');
        jrCore_form_result('referrer');
    }
    if (!isset($_post['v']) || strlen($_post['v']) === 0) {
        jrCore_set_form_notice('error', 'Invalid view');
        jrCore_form_result('referrer');
    }
    if (!isset($_post['n']) || strlen($_post['n']) === 0) {
        jrCore_set_form_notice('error', 'Invalid name');
        jrCore_form_result('referrer');
    }
    if (!isset($_post['o']) || !jrCore_checktype($_post['o'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid order');
        jrCore_form_result('referrer');
    }
    $ord = intval($_post['o'] - 1);
    // Okay - we need to MOVE UP the name we got, and MOVE DOWN the one above it
    jrCore_set_form_designer_field_order($_post['m'], $_post['v'], $_post['n'], $ord);
    jrCore_form_delete_session_view($_post['m'], $_post['v']);
    jrCore_form_result('referrer');
}

//------------------------------
// form_field_update (magic)
//------------------------------
function view_jrCore_form_field_update($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    if (!isset($_post['m']) || !isset($_mods["{$_post['m']}"])) {
        jrCore_notice_page('error', 'invalid module');
    }
    if (!isset($_post['v']) || strlen($_post['v']) === 0) {
        jrCore_notice_page('error', 'invalid view');
    }
    if (!isset($_post['n']) || strlen($_post['n']) === 0) {
        jrCore_notice_page('error', 'invalid name');
    }
    $mod     = $_post['m'];
    $opt     = $_post['v'];
    $_fields = jrCore_get_designer_form_fields($mod, $opt);
    if (!$_fields || !is_array($_fields)) {
        jrCore_notice_page('error', 'This form has not been setup properly to work with the custom form designer');
    }
    $nam = $_post['n'];
    if (!isset($_fields[$nam]) || !is_array($_fields[$nam])) {
        jrCore_notice_page('error', 'This form field has not been setup properly to work with the custom form designer');
    }
    $_fld = $_fields[$nam];

    $_lang = jrUser_load_lang_strings(null, false);

    jrCore_page_banner("field: <span style=\"text-transform:lowercase;\">{$_fld['name']}</span>", "{$_post['module_url']}/{$_post['v']}");

    // Some fields will BREAK if they are changed - warn about this
    switch ($nam) {
        case 'user_passwd1':
        case 'user_passwd2':
            jrCore_set_form_notice('warning', 'This field is required for proper functionality - do not <strong>make inactive</strong> or change the field <strong>type</strong>, <strong>validation</strong> or <strong>group</strong> fields!', false);
            break;
    }
    jrCore_get_form_notice();

    // Show our table of options
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => 'referrer'
    );
    jrCore_form_create($_tmp);

    // Module
    $_tmp = array(
        'name'     => 'field_module',
        'type'     => 'hidden',
        'value'    => $mod,
        'validate' => 'core_string'
    );
    jrCore_form_field_create($_tmp);

    // View
    $_tmp = array(
        'name'     => 'field_view',
        'type'     => 'hidden',
        'value'    => $opt,
        'validate' => 'core_string'
    );
    jrCore_form_field_create($_tmp);

    // Name
    $_tmp = array(
        'name'     => 'name',
        'type'     => 'hidden',
        'value'    => $nam,
        'validate' => 'core_string'
    );
    jrCore_form_field_create($_tmp);

    // Fields can have the following attributes:
    // label
    // sublabel
    // help
    // name
    // type
    // validate
    // options
    // min
    // max
    // required

    // Field Label
    $_tmp = array(
        'name'     => 'label',
        'label'    => 'label',
        'help'     => 'This is the Label name that will appear to the left of the field.<br><br><strong>NOTE:</strong> If you see *change* in the field it means this text label has not been created yet - enter a label and save your changes.<br><br>Language ID: ' . $_fld['label'],
        'type'     => 'text',
        'value'    => (isset($_lang[$mod]["{$_fld['label']}"])) ? $_lang[$mod]["{$_fld['label']}"] : $_fld['label'],
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    // Field Sub Label
    $_tmp = array(
        'name'     => 'sublabel',
        'label'    => 'sub label',
        'help'     => 'This is the text that will be appear UNDER the Label in smaller type. Use this to let the user know about any restrictions in the field. This is an optional field - if left empty it will not show.<br><br>Language ID: ' . $_fld['sublabel'],
        'type'     => 'text',
        'value'    => (isset($_lang[$mod]["{$_fld['sublabel']}"])) ? $_lang[$mod]["{$_fld['sublabel']}"] : $_fld['sublabel'],
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    // Field Help
    $_tmp = array(
        'name'     => 'help',
        'label'    => 'help',
        'help'     => 'The Help text will appear in the small drop down area when the user clicks on the Question button (like you are viewing right now). Leave this empty to not show a help drop down.<br><br>Language ID: ' . $_fld['help'],
        'type'     => 'text',
        'value'    => (isset($_lang[$mod]["{$_fld['help']}"])) ? $_lang[$mod]["{$_fld['help']}"] : $_fld['help'],
        'validate' => false
    );
    jrCore_form_field_create($_tmp);

    // Bring in any custom form fields
    $_opt = array();
    $_fdo = array();
    $_tmp = jrCore_get_registered_module_features('jrCore', 'form_field');
    if ($_tmp && is_array($_tmp)) {
        foreach ($_tmp as $mod => $_v) {
            foreach ($_v as $k => $v) {
                $_opt[$k] = $k;
                $fnc      = "{$mod}_form_field_{$k}_form_designer_options";
                if (function_exists($fnc)) {
                    $_fdo[$k] = $fnc();
                }
            }
        }
        unset($_opt['custom']);
    }

    // Some field types have their own internal validation, so we "disable"
    // this field if those types are the selected one
    $_dis = array();
    $_dop = array();
    $_def = array();
    $_dmx = array();
    $_drq = array();
    foreach ($_fdo as $ft => $_fo) {
        if (isset($_fo['disable_validation']) && $_fo['disable_validation'] === true) {
            $_dis[] = $ft;
        }
        if (isset($_fo['disable_options']) && $_fo['disable_options'] === true) {
            $_dop[] = $ft;
        }
        if (isset($_fo['disable_default']) && $_fo['disable_default'] === true) {
            $_def[] = $ft;
        }
        if (isset($_fo['disable_min_and_max']) && $_fo['disable_min_and_max'] === true) {
            $_dmx[] = $ft;
        }
        if (isset($_fo['disable_required']) && $_fo['disable_required'] === true) {
            $_drq[] = $ft;
        }
    }

    // Field Type
    natcasesort($_opt);
    $_tmp = array(
        'name'     => 'type',
        'label'    => 'field type',
        'help'     => 'The Field Type defines the type of form element that will be displayed for this field.',
        'type'     => 'select',
        'options'  => $_opt,
        'value'    => $_fld['type'],
        'validate' => 'core_string',
        'onchange' => "var a=this.options[this.selectedIndex].value;var b={'" . implode("':1,'", $_dis) . "':1};if(typeof b[a] !== 'undefined' && b[a] == 1){\$('.validate_element_right select').fadeTo(250,0.3).attr('disabled','disabled').addClass('form_element_disabled')} else {\$('.validate_element_right select').fadeTo(100,1).removeAttr('disabled').removeClass('form_element_disabled')};var c={'" . implode("':1,'", $_dop) . "':1};if(typeof c[a] !== 'undefined' && c[a] == 1){\$('.options_element_right textarea').fadeTo(250,0.3).attr('disabled','disabled').addClass('form_element_disabled')} else {\$('.options_element_right textarea').fadeTo(100,1).removeAttr('disabled').removeClass('form_element_disabled')};var d={'" . implode("':1,'", $_def) . "':1};if(typeof d[a] !== 'undefined' && d[a] == 1){\$('.default_element_right #default').fadeTo(250,0.3).attr('disabled','disabled').addClass('form_element_disabled')} else {\$('.default_element_right #default').fadeTo(100,1).removeAttr('disabled').removeClass('form_element_disabled')};var e={'" . implode("':1,'", $_dmx) . "':1};if(typeof e[a] !== 'undefined' && e[a] == 1){\$('.min_element_right #min').fadeTo(250,0.3).attr('disabled','disabled').addClass('form_element_disabled');\$('.max_element_right #max').fadeTo(250,0.3).attr('disabled','disabled').addClass('form_element_disabled')} else {\$('.min_element_right #min').fadeTo(100,1).removeAttr('disabled').removeClass('form_element_disabled');\$('.max_element_right #max').fadeTo(100,1).removeAttr('disabled').removeClass('form_element_disabled')};var f={'" . implode("':1,'", $_drq) . "':1};if(typeof f[a] !== 'undefined' && f[a] == 1){\$('.required_element_right #required').fadeTo(250,0.3).attr('disabled','disabled').addClass('form_element_disabled')} else {\$('.required_element_right #required').fadeTo(100,1).removeAttr('disabled').removeClass('form_element_disabled')}"

    );
    foreach ($_fdo as $ft => $_fo) {
        if (isset($_fo['type_help']{1})) {
            $_tmp['help'] .= "<br><br><strong>{$ft}</strong> - {$_fo['type_help']}";
        }
    }
    jrCore_form_field_create($_tmp);

    // Options
    $_opt = array();
    if (isset($_fld['options']) && strpos($_fld['options'], '{') === 0) {
        $_tmp = json_decode($_fld['options'], true);
        if (isset($_tmp) && is_array($_tmp)) {
            foreach ($_tmp as $k => $v) {
                $_opt[] = "{$k}|{$v}";
            }
            $_fld['options'] = implode("\n", $_opt);
        }
    }
    $_tmp = array(
        'name'     => 'options',
        'label'    => 'field options',
        'sublabel' => 'see <strong>help</strong> for what is allowed here',
        'help'     => 'The Options value will vary depending on the selected field type:',
        'type'     => 'textarea',
        'value'    => $_fld['options'],
        'validate' => 'allowed_html'
    );
    foreach ($_fdo as $ft => $_fo) {
        if (isset($_fo['options_help']{1})) {
            $_tmp['help'] .= "<br><br><strong>{$ft}</strong> - {$_fo['options_help']}";
        }
    }
    jrCore_form_field_create($_tmp);

    // Field Default
    $_tmp = array(
        'name'     => 'default',
        'label'    => 'default',
        'help'     => 'If you would like a default value to be used for this field, enter the default value here.',
        'type'     => 'text',
        'value'    => $_fld['default'],
        'validate' => 'printable'
    );
    if (isset($_fdo["{$_fld['type']}"]) && isset($_fdo["{$_fld['type']}"]['disable_default'])) {
        $_js = array("$('.default_element_right input').fadeTo(250,0.3).attr('disabled','disabled')");
        jrCore_create_page_element('javascript_ready_function', $_js);
    }
    jrCore_form_field_create($_tmp);

    // Validate
    $_opt = array();
    $_tmp = jrCore_get_registered_module_features('jrCore', 'checktype');
    if ($_tmp && is_array($_tmp)) {
        $_eng = jrUser_load_lang_strings('en-US');
        foreach ($_tmp as $mod => $_entries) {
            foreach ($_entries as $type => $ignore) {
                if ($type == 'array') {
                    continue;
                }
                $func = $mod . '_checktype_' . $type;
                if (function_exists($func)) {
                    $check_type = jrCore_checktype('', $type, false, true);
                    $check_desc = jrCore_checktype('', $type, true);
                    if (jrCore_checktype($check_desc, 'number_nz')) {
                        $check_desc = $_eng['jrCore'][$check_desc];
                    }
                    $_opt[$type] = '(' . $check_type . ') ' . $check_desc;
                }
            }
        }
    }
    natcasesort($_opt);
    $_tmp = array(
        'name'     => 'validate',
        'label'    => 'validation',
        'help'     => 'Select the type of field validation you would like to have for this field. The following field types:<br><br>optionlist<br>select<br>select_multiple<br>radio<br>image<br>file<br>audio<br>checkbox<br><br>are automatically validated internally, so the validation option will be grayed out if these field types are selected.',
        'type'     => 'select',
        'options'  => $_opt,
        'value'    => (isset($_fld['validate']) && strlen($_fld['validate']) > 0) ? $_fld['validate'] : 'not_empty',
        'validate' => 'core_string'
    );
    // See if we have selected a disabled type
    if (in_array($_fld['type'], $_dis)) {
        $_js = array("$('.validate_element_right select').fadeTo(250,0.3).attr('disabled','disabled')");
        jrCore_create_page_element('javascript_ready_function', $_js);
    }
    jrCore_form_field_create($_tmp);

    // Field Min
    $_tmp = array(
        'name'     => 'min',
        'label'    => 'minimum',
        'help'     => 'The Field Minimum Value will validate that any entered value is greater than or equal to the minimum value.<br><br><strong>For (number) Fields:</strong> This is the minimum value accepted.<br><strong>For (string) Fields:</strong> This is the minimum <strong>character length</strong> for the string.<br><strong>For (date) Fields:</strong> This is the minimum accepted date (in YYYYMMDD[HHMMSS] format).',
        'type'     => 'text',
        'value'    => (isset($_fld['min']) && $_fld['min'] == '0') ? '' : (int) $_fld['min'],
        'validate' => 'number_nn'
    );
    if (isset($_fdo["{$_fld['type']}"]) && isset($_fdo["{$_fld['type']}"]['disable_min_and_max'])) {
        $_js = array("$('.min_element_right input').fadeTo(250,0.3).attr('disabled','disabled')");
        jrCore_create_page_element('javascript_ready_function', $_js);
    }
    jrCore_form_field_create($_tmp);

    // Field Max
    $_tmp = array(
        'name'     => 'max',
        'label'    => 'maximum',
        'help'     => 'The Field Maximum Value will validate that any entered value is less than or equal to the maximum value.<br><br><strong>For (number) Fields:</strong> This is the maximum value accepted.<br><strong>For (string) Fields:</strong> This is the maximum <strong>character length</strong> for the string.<br><strong>For (date) Fields:</strong> This is the maximum accepted date (in YYYYMMDD[HHMMSS] format).',
        'type'     => 'text',
        'value'    => (isset($_fld['max']) && $_fld['max'] == '0') ? '' : (int) $_fld['max'],
        'validate' => 'number_nz'
    );
    if (isset($_fdo["{$_fld['type']}"]) && isset($_fdo["{$_fld['type']}"]['disable_min_and_max'])) {
        $_js = array("$('.max_element_right input').fadeTo(250,0.3).attr('disabled','disabled')");
        jrCore_create_page_element('javascript_ready_function', $_js);
    }
    jrCore_form_field_create($_tmp);

    // Field Group
    $_opt = array(
        'all'     => '(group) All Users (including logged out)',
        'master'  => '(group) Master Admins',
        'admin'   => '(group) Profile Admins',
        'power'   => '(group) Power Users',
        'user'    => '(group) Normal Users',
        'visitor' => '(group) Logged Out Users'
    );
    $_qta = jrProfile_get_quotas();
    if (isset($_qta) && is_array($_qta)) {
        foreach ($_qta as $qid => $qname) {
            $_opt[$qid] = "(quota) {$qname}";
        }
    }
    $_tmp = array(
        'name'     => 'group',
        'label'    => 'display groups',
        'sublabel' => 'more than 1 group allowed',
        'help'     => 'If you would like this field to only be visible to Users in specific Profile Quotas, Profile Admins or Master Admins, select the group(s) here.',
        'type'     => 'select_multiple',
        'options'  => $_opt,
        'value'    => $_fld['group'],
        'default'  => 'user',
        'validate' => 'core_string'
    );
    jrCore_form_field_create($_tmp);

    // Field Required
    $_tmp = array(
        'name'     => 'required',
        'label'    => 'required',
        'help'     => 'If you would like to ensure a valid value is always received for this field, check the Field Required option.',
        'type'     => 'checkbox',
        'value'    => (isset($_fld['required']) && $_fld['required'] == '1') ? 'on' : 'off',
        'validate' => 'onoff'
    );
    if (isset($_fdo["{$_fld['type']}"]) && isset($_fdo["{$_fld['type']}"]['disable_required'])) {
        $_js = array("$('.required_element_right #required').fadeTo(250,0.3).attr('disabled','disabled')");
        jrCore_create_page_element('javascript_ready_function', $_js);
    }
    jrCore_form_field_create($_tmp);

    // Field Active
    $_tmp = array(
        'name'     => 'active',
        'label'    => 'active',
        'help'     => 'If Field Active is not checked, this field will not appear in the form.',
        'type'     => 'checkbox',
        'value'    => (isset($_fld['active']) && $_fld['active'] == '1') ? 'on' : 'off',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    if (isset($_post['v']) && ($_post['v'] == 'create' || $_post['v'] == 'update')) {

        // Make sure this module supplies the create/update view
        $opp = ($_post['v'] == 'create') ? 'update' : 'create';
        require_once APP_DIR . "/modules/{$mod}/index.php";
        if (function_exists("view_{$mod}_{$opp}")) {
            // Make sure it exists in the DB
            $_fields = jrCore_get_designer_form_fields($_post['m'], $opp);
            if (isset($_fields["{$_post['n']}"])) {
                // Link to Update/Create
                $_tmp = array(
                    'name'     => "linked_form_field",
                    'label'    => "change {$opp} field",
                    'help'     => "If you would like your changes to be saved to the same field in the &quot;{$opp}&quot; form, check here.",
                    'type'     => 'checkbox',
                    'value'    => 'on',
                    'validate' => 'onoff'
                );
                jrCore_form_field_create($_tmp);
            }
        }
    }

    jrCore_page_display();
}

//------------------------------
// form_field_update_save (magic)
//------------------------------
function view_jrCore_form_field_update_save($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_set_flag('master_html_trusted', 1);
    jrCore_form_validate($_post);
    if (!isset($_post['field_module']) || !isset($_mods["{$_post['field_module']}"])) {
        jrCore_set_form_notice('error', 'Invalid module');
        jrCore_form_result();
    }
    if (!isset($_post['field_view']) || strlen($_post['field_view']) === 0) {
        jrCore_set_form_notice('error', 'Invalid view');
        jrCore_form_result();
    }
    if (isset($_post['required']) && $_post['required'] == 'on') {
        $_post['required'] = 1;
    }
    else {
        $_post['required'] = 0;
    }
    if (isset($_post['active']) && $_post['active'] == 'on') {
        $_post['active'] = 1;
    }
    else {
        $_post['active'] = 0;
    }
    $mod = $_post['field_module'];
    $opt = $_post['field_view'];
    $nam = $_post['name'];

    $_lang = jrUser_load_lang_strings();
    $_save = array();

    // Update Lang Strings
    $_tm = jrCore_get_designer_form_fields($mod, $opt);
    $tbl = jrCore_db_table_name('jrUser', 'language');
    $lcd = jrCore_db_escape($_user['user_language']);
    if (isset($_tm[$nam]) && is_array($_tm[$nam])) {
        $_todo = array('label', 'sublabel', 'help');
        foreach ($_todo as $do) {
            $num = (isset($_tm[$nam][$do]) && jrCore_checktype($_tm[$nam][$do], 'number_nz')) ? (int) $_tm[$nam][$do] : 0;
            if (isset($num) && jrCore_checktype($num, 'number_nz')) {
                if (isset($_lang[$mod][$num])) {
                    if ($do === 'label') {
                        $_post[$do] = strtolower($_post[$do]);
                    }
                    $req = "UPDATE {$tbl} SET lang_text = '" . jrCore_db_escape($_post[$do]) . "' WHERE lang_module = '" . jrCore_db_escape($mod) . "' AND lang_key = '{$num}' AND (lang_code = '{$lcd}' OR lang_text LIKE '%change this%')";
                    jrCore_db_query($req);
                    $_save[$do] = $_post[$do];
                    $_post[$do] = $num;
                }
            }
        }
        jrCore_delete_all_cache_entries('jrUser');
    }

    // See if we are Create/Update Linked
    if (isset($_post['linked_form_field']) && $_post['linked_form_field'] == 'on') {
        $opp = ($_post['field_view'] == 'create') ? 'update' : 'create';
        $_tm = jrCore_get_designer_form_fields($mod, $opp);
        if (isset($_tm[$nam]) && is_array($_tm[$nam])) {
            $_todo = array('label', 'sublabel', 'help');
            foreach ($_todo as $do) {
                $num = (isset($_tm[$nam][$do]) && jrCore_checktype($_tm[$nam][$do], 'number_nz')) ? (int) $_tm[$nam][$do] : 0;
                if (isset($num) && jrCore_checktype($num, 'number_nz')) {
                    if (isset($_lang[$mod][$num])) {
                        $req = "UPDATE {$tbl} SET lang_text = '" . jrCore_db_escape($_save[$do]) . "' WHERE lang_module = '" . jrCore_db_escape($mod) . "' AND lang_key = '{$num}' AND (lang_code = '{$lcd}' OR lang_text LIKE '%change this%')";
                        jrCore_db_query($req);
                    }
                }
            }
        }
        jrCore_delete_all_cache_entries('jrUser');
    }

    // Check validation.  Some fields (such as checkbox) have specific validation
    // requirements - set this here so they cannot be set wrong.
    switch ($_post['type']) {
        case 'date':
        case 'datetime':
            $_post['validate'] = 'date';
            break;
        case 'select_date':
            $_post['validate'] = 'number_nz';
            break;
        case 'checkbox':
            $_post['validate'] = 'onoff';
            break;
        case 'select':
        case 'select_multiple':
        case 'radio':
        case 'optionlist':
            // For a select field, our OPTIONS will come in either as a FUNCTION or as individual options on each line
            if (isset($_post['options']) && strlen($_post['options']) > 0) {
                $cfunc = $_post['options'];
                if (!function_exists($cfunc)) {
                    // okay - we're not a function
                    $_tmp = explode("\n", $_post['options']);
                    if (!isset($_tmp) || !is_array($_tmp)) {
                        jrCore_set_form_notice('error', 'You have entered an invalid value for Options - must be a valid function or a set of options, one per line.');
                        jrCore_form_result();
                    }
                    $_post['options'] = array();
                    foreach ($_tmp as $v) {
                        $v = trim($v);
                        if (strpos($v, '|')) {
                            list($k, $v) = explode('|', $v, 2);
                        }
                        else {
                            $k = $v;
                        }
                        $_post['options'][$k] = $v;
                    }
                    // Make sure the DEFAULT we get is a valid option
                    if (isset($_post['default']) && strlen($_post['default']) > 0 && !isset($_post['options']["{$_post['default']}"])) {
                        jrCore_set_form_notice('error', 'The value entered as the Default must be a valid value from the Options');
                        jrCore_form_field_hilight('default');
                        jrCore_form_result();
                    }
                }
            }
            else {
                jrCore_set_form_notice('error', 'You must enter valid Options for a Select form field');
                jrCore_form_result();
            }
            break;
    }

    // First - get existing default value for use below
    $def = '';
    $tbl = jrCore_db_table_name('jrCore', 'form');
    $req = "SELECT `default` FROM {$tbl} WHERE `module` = '" . jrCore_db_escape($_post['field_module']) . "' AND `name` = '" . jrCore_db_escape($_post['name']) . "' LIMIT 1";
    $_ev = jrCore_db_query($req, 'SINGLE');
    if ($_ev && is_array($_ev) && isset($_ev['default']) && strlen($_ev['default']) > 0) {
        $def = jrCore_db_escape($_ev['default']);
    }

    $cnt = jrCore_verify_designer_form_field($_post['field_module'], $_post['field_view'], $_post, true);
    if ($cnt && $cnt == '1') {
        if (isset($_post['linked_form_field']) && $_post['linked_form_field'] == 'on') {
            // The linked lang strings are handled above - don't change them here
            unset($_post['label'], $_post['sublabel'], $_post['help']);
            $opp = ($_post['field_view'] == 'create') ? 'update' : 'create';
            $cnt = jrCore_verify_designer_form_field($_post['field_module'], $opp, $_post, true);
            if (!$cnt || $cnt != '1') {
                jrCore_set_form_notice('error', "An error was encountered updating the linked form field in the {$opp} form view - please try again");
                jrCore_form_result();
            }
        }
        jrCore_form_delete_session();
        jrCore_form_delete_session_view($_post['field_module'], $_post['field_view']);
        jrCore_set_form_notice('success', 'The field settings were successfully updated');

        // Next, we need to update any existing values in the DB
        // with the new default value, but only for those that have not
        // been set, or are still set to the previous default value (if set)
        $val = (isset($_post['default'])) ? jrCore_db_escape($_post['default']) : '';
        jrCore_db_update_default_key($_post['field_module'], $_post['name'], $val, $def);
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered saving the form field - please try again');
    }
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/form_designer/m={$mod}/v={$opt}");
}

//------------------------------
// skin_reset
//------------------------------
function view_jrCore_skin_reset($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    $_skn = jrCore_get_skins();
    if (!isset($_post['skin']) || !isset($_skn["{$_post['skin']}"])) {
        jrCore_set_form_notice('error', 'invalid skin');
        jrCore_location('referrer');
    }

    $tbl = jrCore_db_table_name('jrCore', 'skin');
    $req = "UPDATE {$tbl} SET skin_custom_css = '' WHERE skin_directory = '" . jrCore_db_escape($_post['skin']) . "'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (!$cnt || $cnt !== 1) {
        jrCore_set_form_notice('error', 'error updating the skin database to reset the style - please try again');
        jrCore_location('referrer');
    }
    jrCore_set_form_notice('success', 'The custom skin style was successfully removed');
    jrCore_location('referrer');
}

//------------------------------
// skin_admin (magic)
//------------------------------
function view_jrCore_skin_admin($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_create_media_directory(0);
    jrUser_load_lang_strings();

    if (!isset($_post['_1'])) {
        $_post['_1'] = 'info';
    }
    if (!isset($_post['skin']{0})) {
        $_post['skin'] = $_conf['jrCore_active_skin'];
    }
    else {
        $_mta = jrCore_skin_meta_data($_post['skin']);
        if (!$_mta || !is_array($_mta)) {
            $_post['skin'] = $_conf['jrCore_active_skin'];
        }
    }

    $admin = '';
    $title = '';
    // See if we are getting an INDEX page for this module.  The Index
    // Page will tell us what "view" for the module config they are showing.
    // This can be either a config page for the module (i.e. global settings,
    // quota settings, language, etc.) OR it can be a tool.
    // Our URL will be like:
    // http://www.site.com/core/config/global
    // http://www.site.com/core/config/quota
    // http://www.site.com/core/config/language
    // http://www.site.com/core/config/tools
    switch ($_post['_1']) {

        //------------------------------
        // GLOBAL SETTINGS
        //------------------------------
        case 'global':
            $title = 'Global Config';
            $admin = jrCore_show_global_settings('skin', $_post['skin'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // STYLE
        //------------------------------
        case 'style':
            $title = 'Style';
            $admin = jrCore_show_skin_style($_post['skin'], $_post, $_user, $_conf);

            // Bring in our Color Picker if needed
            $_tmp = jrCore_get_flag('style_color_picker');
            if ($_tmp) {
                $_inc = array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/js/jquery.colorpicker.js");
                jrCore_create_page_element('javascript_href', $_inc);
                foreach ($_tmp as $v) {
                    jrCore_create_page_element('javascript_ready_function', $v);
                }
            }
            break;

        //------------------------------
        // IMAGES
        //------------------------------
        case 'images':
            $title = 'Images';
            $admin = jrCore_show_skin_images('skin', $_post['skin'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // LANGUAGE STRINGS
        //------------------------------
        case 'language':
            $title = 'Language Strings';
            $admin = jrUser_show_module_lang_strings('skin', $_post['skin'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // TEMPLATES
        //------------------------------
        case 'templates':
            $title = 'Templates';
            $admin = jrCore_show_skin_templates($_post['skin'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // INFO
        //------------------------------
        case 'info':
            $title = 'Info';
            $admin = jrCore_show_skin_info($_post['skin'], $_post, $_user, $_conf);
            break;
    }

    // Process view
    $_rep = array(
        'active_tab'         => 'skins',
        'admin_page_content' => $admin,
        '_skins'             => jrCore_get_acp_skins()
    );

    $_mta = jrCore_skin_meta_data($_post['skin']);
    $_rep['default_category'] = 'general';
    if (isset($_mta['category']) && strlen($_mta['category']) > 0) {
        $_tmp = explode(',', $_mta['category']);
        if ($_tmp[0] != $_mta['category']) {
            $_rep['default_category'] = trim(strtolower($_tmp[0]));
        }
        else {
            $_rep['default_category'] = trim(strtolower($_mta['category']));
        }
    }

    jrCore_install_new_modules();
    $html = jrCore_parse_template('admin.tpl', $_rep, 'jrCore');

    // Output
    jrCore_page_title("{$title} - {$_mta['title']}");
    jrCore_admin_menu_accordion_js($_rep['default_category']);
    jrCore_page_custom($html);
    jrCore_page_display();
}

//------------------------------
// skin_admin_save (magic)
//------------------------------
function view_jrCore_skin_admin_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (isset($_post['skin_delete']) && $_post['skin_delete'] === 'on') {
        jrCore_validate_location_url();
    }
    else {
        jrCore_form_validate($_post);

        // Make sure we get a good skin
        if (!isset($_post['skin'])) {
            $_post['skin'] = $_conf['jrCore_active_skin'];
        }

        // Make sure our skin config is properly loaded
        $_conf = jrCore_load_skin_config($_post['skin'], $_conf);
    }

    // See what we are saving...
    switch ($_post['_1']) {

        case 'global':

            // See if this module is presenting us with a validate function
            if (is_file(APP_DIR . "/skins/{$_post['skin']}/config.php")) {
                $vfunc = "{$_post['skin']}_config_validate";
                if (!function_exists($vfunc)) {
                    require_once APP_DIR . "/skins/{$_post['skin']}/config.php";
                }
                if (function_exists($vfunc)) {
                    $_post = $vfunc($_post);
                }
            }
            // Update
            $show = false;
            foreach ($_post as $k => $v) {
                if (isset($_conf["{$_post['skin']}_{$k}"]) && $v != $_conf["{$_post['skin']}_{$k}"]) {
                    jrCore_set_setting_value($_post['skin'], $k, $v);
                    $show = true;
                }
            }
            jrCore_delete_all_cache_entries('jrCore', 0);
            $text = 'The settings have been successfully saved';
            if ($show) {
                $text .= "<br>Make sure you <a href=\"{$_conf['jrCore_base_url']}/{$_post['module_url']}/cache_reset\"><u>Reset Caches</u></a> to activate your changes";
            }
            jrCore_set_form_notice('success', $text, false);
            break;

        case 'language':

            // Get all the lang strings for this module
            $tbl = jrCore_db_table_name('jrUser', 'language');
            $mod = jrCore_db_escape($_post['skin']);
            $req = "SELECT * FROM {$tbl} WHERE lang_module = '{$mod}' AND lang_code = '" . jrCore_db_escape($_post['lang_code']) . "'";
            $_rt = jrCore_db_query($req, 'lang_id');
            if (!isset($_rt) || !is_array($_rt)) {
                jrCore_set_form_notice('error', "Unable to retrieve skin language settings from language table - check debug_log errors");
                jrCore_form_result();
            }
            $req = "UPDATE {$tbl} SET lang_text = CASE lang_id\n";
            foreach ($_rt as $key => $_lng) {
                if (isset($_post["lang_{$key}"])) {
                    $req .= "WHEN {$key} THEN '" . jrCore_db_escape($_post["lang_{$key}"]) . "'\n";
                }
            }
            if (isset($req) && strpos($req, 'THEN')) {
                $req .= "ELSE lang_text END";
                jrCore_db_query($req, 'COUNT');
            }
            jrCore_delete_all_cache_entries('jrUser');
            jrCore_set_form_notice('success', 'The language strings have been successfully saved');
            jrCore_form_delete_session();
            jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/{$_post['_1']}/skin={$_post['skin']}/lang_code={$_post['lang_code']}/p={$_post['p']}");
            break;

        case 'images':

            jrCore_create_media_directory(0);
            // Get existing skin info to see what images we have customized
            $_im = array();
            if (isset($_conf["jrCore_{$_post['skin']}_custom_images"]{2})) {
                $_im = json_decode($_conf["jrCore_{$_post['skin']}_custom_images"], true);
            }
            // Check for new custom files being uploaded
            $_up = jrCore_get_uploaded_meter_files($_post['upload_token']);
            if ($_up && is_array($_up)) {
                foreach ($_up as $_info) {
                    jrCore_write_media_file(0, "{$_post['skin']}_{$_info['name']}", $_info['tmp_name'], 'public-read');
                    unlink($_info['tmp_name']);  // OK
                    $_im["{$_info['name']}"] = array($_info['size'], 'on');
                }
            }
            // Go through and save our uploaded images (if any)
            if (isset($_FILES) && is_array($_FILES)) {
                foreach ($_FILES as $k => $_info) {
                    if (isset($_info['size']) && jrCore_checktype($_info['size'], 'number_nz')) {
                        $num = (int) str_replace('file_', '', $k);
                        // Image extensions must match
                        $ext = jrCore_file_extension($_info['name']);
                        switch ($ext) {
                            case 'jpg':
                            case 'png':
                            case 'gif':
                                break;
                            default:
                                jrCore_set_form_notice('error', 'Invalid image type for ' . $_post["name_{$num}"] . ' - only JPG, PNG and GIF images are allowed');
                                jrCore_form_result();
                                break;
                        }
                        if (isset($_post["name_{$num}"]{0})) {
                            $nam = $_post["name_{$num}"];
                            jrCore_write_media_file(0, "{$_post['skin']}_{$nam}", $_info['tmp_name'], 'public-read');
                            unlink($_info['tmp_name']);  // OK
                            $_im[$nam] = array($_info['size']);
                            $_post["name_{$num}_active"] = 'on';
                        }
                    }
                }
            }
            // Update setting with new values
            // [name_0_active] => on
            // [name_0] => bckgrd.png
            foreach ($_post as $k => $v) {
                if (strpos($k, 'name_') === 0 && strpos($k, '_active')) {
                    $num = (int) substr($k, 5, strrpos($k, '_'));
                    $nam = $_post["name_{$num}"];
                    if (isset($_im[$nam][0])) {
                        $_im[$nam][1] = $v;
                    }
                    else {
                        unset($_im[$nam]);
                    }
                }
            }
            jrCore_set_setting_value('jrCore', "{$_post['skin']}_custom_images", json_encode($_im));
            jrCore_delete_all_cache_entries();
            break;

        case 'style':

            // We need to save our updates to the database so they "override" the defaults...
            $_pcc = false;
            $_out = array();
            $_com = array();

            // Get what we are overriding
            if (isset($_post['section']) && $_post['section'] == 'search') {
                $_pcc = jrCore_get_temp_value('jrCore', $_post['search_key']);
                if (!$_pcc || !is_array($_pcc)) {
                    jrCore_set_form_notice('error', 'invalid search_key - please try again');
                    jrCore_location('referrer');
                }
                $sstr = $_pcc['search_string'];
                $_pcc = $_pcc['found'];
                jrCore_delete_temp_value('jrCore', $_post['search_key']);
            }
            elseif (isset($_post['file']) && strlen($_post['file']) > 0) {
                if (is_file(APP_DIR . "/skins/{$_post['skin']}/css/{$_post['file']}")) {
                    $_pcc = jrCore_parse_css_file(APP_DIR . "/skins/{$_post['skin']}/css/{$_post['file']}", $_post['section']);
                }
                else {
                    // Is this a module file?
                    if ($_tm = jrCore_get_registered_module_features('jrCore', 'css')) {
                        foreach ($_tm as $mod => $_css_files) {
                            if (in_array($_post['file'], $_css_files)) {
                                if (is_file(APP_DIR . "/modules/{$mod}/css/{$_post['file']}")) {
                                    $_pcc = jrCore_parse_css_file(APP_DIR . "/modules/{$mod}/css/{$_post['file']}", $_post['section']);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            if (!$_pcc || count($_pcc) === 0) {
                jrCore_set_form_notice('error', 'unable to load existing CSS rules - please try again');
                jrCore_location('referrer');
            }

            // Is this a mobile/tablet override?
            $medq = jrCore_get_flag('jrcore_css_media_enabled');
            foreach ($_post as $k => $v) {
                // all of our custom style entries will start with "jrse"....
                if (strpos($k, 'jrse') === 0) {
                    // We have a style entry.  the key for this entry will in position 4
                    $key = $k;
                    if (strpos($key, '_')) {
                        list($key,) = explode('_', $k);
                    }
                    $key = (int) substr($key, 4);
                    if (!isset($_com[$key])) {
                        // Now we can get our Name, Selector and New Value - i.e.:
                        // [jrse3_s] => body~font-family
                        // [jrse3] => Open Sans,Tahoma,sans-serif
                        list($selector, $rule) = @explode('~', $_post["jrse{$key}_s"], 2);
                        // See if we have a color...
                        if (isset($_post["jrse{$key}_hex"])) {
                            $val                    = trim($_post["jrse{$key}_hex"]);
                            $_out[$selector][$rule] = $val;
                            $tst                    = str_replace('#', '', jrCore_str_to_lower($val));
                        }
                        else {
                            $val = trim($_post["jrse{$key}"]);
                            switch ($rule) {
                                case 'font-family':
                                    if (strpos($val, ',')) {
                                        $_vl = array();
                                        foreach (explode(',', $val) as $vl) {
                                            if (strpos($vl, ' ')) {
                                                $vl = '"' . $vl . '"';
                                            }
                                            $_vl[] = $vl;
                                        }
                                        $val = implode(',', $_vl);
                                        unset($_vl);
                                    }
                                    break;
                            }
                            $_out[$selector][$rule] = $val;
                            $tst                    = str_replace('"', '', jrCore_str_to_lower($val));
                        }

                        // See if we have a match
                        if (isset($_pcc[$selector]['rules'][$rule])) {
                            $compare = str_replace('"', '', jrCore_str_to_lower($_pcc[$selector]['rules'][$rule]));
                            if ($compare == $tst) {
                                unset($_out[$selector][$rule]);
                                continue;
                            }
                        }

                        // See if we are !important
                        if (isset($_post["jrse{$key}_add_important"]) && $_post["jrse{$key}_add_important"] == 'on') {
                            $_out[$selector][$rule] .= ' !important';
                        }
                        if (isset($_post["jrse{$key}_add_auto"]) && $_post["jrse{$key}_add_auto"] == 'on') {
                            $_out[$selector][$rule] .= ' auto';
                        }
                        $_com[$key] = 1;
                    }
                }
            }
            if ($_out && is_array($_out) && count($_out) > 0) {
                foreach ($_out as $k => $v) {
                    if (count($v) === 0) {
                        unset($_out[$k]);
                    }
                }
            }
            $tbl = jrCore_db_table_name('jrCore', 'skin');
            if (count($_out) > 0) {
                if ($medq) {
                    $_out = array($medq => $_out);
                }
                // Save out to database
                $req = "SELECT skin_custom_css, skin_custom_image FROM {$tbl} WHERE skin_directory = '" . jrCore_db_escape($_post['skin']) . "'";
                $_rt = jrCore_db_query($req, 'SINGLE');
                if ($_rt && is_array($_rt) && isset($_rt['skin_custom_css']{2})) {
                    $_css = json_decode($_rt['skin_custom_css'], true);
                    $_css = array_merge($_css, $_out);
                    $cimg = $_rt['skin_custom_image'];
                }
                else {
                    $_css = $_out;
                    $cimg = '';
                }
                // Cleanup any empty selectors
                foreach ($_css as $k => $v) {
                    if (!is_array($v) || count($v) === 0) {
                        unset($_css[$k]);
                    }
                }
                $_css = json_encode($_css);
                $skn  = jrCore_db_escape($_post['skin']);
                $req  = "INSERT INTO {$tbl} (skin_directory, skin_updated, skin_custom_css, skin_custom_image) VALUES ('{$skn}',UNIX_TIMESTAMP(),'" . jrCore_db_escape($_css) . "', '" . jrCore_db_escape($cimg) . "')
                         ON DUPLICATE KEY UPDATE skin_updated = UNIX_TIMESTAMP(), skin_custom_css = '" . jrCore_db_escape($_css) . "'";
                $cnt  = jrCore_db_query($req, 'COUNT');
                if (!$cnt || $cnt === 0) {
                    jrCore_set_form_notice('error', 'An error was enountered saving the custom style to the database - please try again');
                    jrCore_form_result();
                }
            }
            else {
                $req = "UPDATE {$tbl} SET skin_custom_css = '' WHERE skin_directory = '" . jrCore_db_escape($_post['skin']) . "'";
                jrCore_db_query($req);
            }

            // Recreate our site CSS
            jrCore_create_master_css($_post['skin']);

            jrCore_form_delete_session();
            switch ($_post['section']) {
                case 'simple':
                case 'padding':
                case 'advanced':
                case 'extra':
                    $section = $_post['section'];
                    break;
                default:
                    $section = 'simple';
                    break;
            }
            if (isset($sstr) && strlen($sstr) > 0) {
                jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/{$_post['_1']}/skin={$_post['skin']}?search_string={$sstr}");
            }
            jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/{$_post['_1']}/skin={$_post['skin']}/file={$_post['file']}/section={$section}");
            break;

        case 'templates':

            //  [form_begin_template_active] => on
            $_act = array();
            $_off = array();
            $_all = array();
            foreach ($_post as $k => $v) {
                if (strpos($k, '_template_active')) {
                    $tpl = str_replace('_template_active', '.tpl', $k);
                    // See if we are turning this template on or off
                    if ($v == 'on') {
                        $_act[] = $tpl;
                        $_all[] = $tpl;
                    }
                    else {
                        $_off[] = $tpl;
                        $_all[] = $tpl;
                    }
                }
            }

            // Set active/inactive
            if (isset($_all) && is_array($_all) && count($_all) > 0) {
                $tbl = jrCore_db_table_name('jrCore', 'template');
                $mod = jrCore_db_escape($_post['skin']);
                if (isset($_act) && is_array($_act) && count($_act) > 0) {
                    $req = "UPDATE {$tbl} SET template_active = '1' WHERE template_module = '{$mod}' AND template_name IN('" . implode("','", $_act) . "')";
                    jrCore_db_query($req);
                }
                if (isset($_off) && is_array($_off) && count($_off) > 0) {
                    $req = "UPDATE {$tbl} SET template_active = '0' WHERE template_module = '{$mod}' AND template_name IN('" . implode("','", $_off) . "')";
                    jrCore_db_query($req);
                }
                // Reset cache for any that changed
                foreach ($_all as $tpl) {
                    jrCore_get_template_file($tpl, $_post['skin'], 'reset');
                }
            }
            jrCore_set_form_notice('success', 'The template settings have been successfully saved');
            break;

        case 'info':

            // Update
            if (isset($_post['skin_active']) && $_post['skin_active'] == 'on') {

                jrCore_verify_skin($_post['skin']);

                // Build skin CSS and JS
                jrCore_create_master_css($_post['skin']);
                jrCore_create_master_javascript($_post['skin']);

                // Activate it
                jrCore_set_setting_value('jrCore', 'active_skin', $_post['skin']);

                // Reset Template caches
                $dir = jrCore_get_module_cache_dir('jrCore');
                jrCore_delete_dir_contents($dir);
                $dir = jrCore_get_module_cache_dir($_post['skin']);
                jrCore_delete_dir_contents($dir);
                jrCore_delete_all_cache_entries();

                // redirect so we reload
                jrCore_form_delete_session();
                jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/info/skin={$_post['skin']}");
            }
            elseif (isset($_post['skin_delete']) && $_post['skin_delete'] === 'on') {

                $res = jrCore_delete_skin($_post['skin']);
                if ($res && strpos($res, 'error:') === 0) {
                    jrCore_set_form_notice('error', substr($res, 7));
                    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/info/skin={$_conf['jrCore_active_skin']}");
                }

                jrCore_trigger_event('jrCore', 'skin_deleted', $_post);
                jrCore_logger('INF', "the {$_post['skin']} skin was successfully deleted");
                jrCore_set_form_notice('success', 'The skin was successfully deleted');
                jrCore_form_delete_session();
                jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/info/skin={$_conf['jrCore_active_skin']}");

            }
            jrCore_set_form_notice('success', 'The settings have been successfully saved');
            break;

    }
    jrCore_form_delete_session();
    jrCore_form_result('referrer');
}

//------------------------------
// admin (magic)
//------------------------------
function view_jrCore_admin($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();

    // Reset any saved location
    if (function_exists('jrUser_delete_saved_url_location')) {
        jrUser_delete_saved_url_location();
    }

    $admin = '';
    $title = '';
    // See if we are getting an INDEX page for this module.  The Index
    // Page will tell us what "view" for the module config they are showing.
    // This can be either a config page for the module (i.e. global settings,
    // quota settings, language, etc.) OR it can be a tool.
    // Our URL will be like:
    // http://www.site.com/core/config/global
    // http://www.site.com/core/config/quota
    // http://www.site.com/core/config/language
    // http://www.site.com/core/config/tools
    if (!isset($_post['_1'])) {
        $_post['_1'] = 'global';
    }
    switch ($_post['_1']) {

        //------------------------------
        // GLOBAL SETTINGS
        //------------------------------
        case 'global':
            if (is_file(APP_DIR . "/repair.php")) {
                jrCore_set_form_notice('error', "Delete the <strong>repair.php</strong> script from your root directory or rename it to <strong>repair.php.html</strong>!", false);
            }
            $title = 'Global Config';
            $admin = jrCore_show_global_settings('module', $_post['module'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // QUOTA SETTINGS
        //------------------------------
        case 'quota':
            $title = 'Quota Config';
            $admin = jrProfile_show_module_quota_settings($_post['module'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // TOOLS
        //------------------------------
        case 'tools':
            $title = 'Tools';
            $admin = jrCore_show_module_tools($_post['module'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // LANGUAGE STRINGS
        //------------------------------
        case 'language':
            $title = 'Language Strings';
            $admin = jrUser_show_module_lang_strings('module', $_post['module'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // TEMPLATES
        //------------------------------
        case 'templates':
            $title = 'Templates';
            $admin = jrCore_show_module_templates($_post['module'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // IMAGES
        //------------------------------
        case 'images':
            $title = 'Images';
            $admin = jrCore_show_skin_images('module', $_post['module'], $_post, $_user, $_conf);
            break;

        //------------------------------
        // INFO
        //------------------------------
        case 'info':
            $title = 'Info';
            $admin = jrCore_show_module_info($_post['module'], $_post, $_user, $_conf);
            break;

    }

    // Process view
    $_rep = array(
        'modules'            => array(),
        'active_tab'         => 'modules',
        'admin_page_content' => $admin
    );

    $_tmp = array();
    $_ina = array();
    foreach ($_mods as $mod_dir => $_inf) {
        if (file_exists(APP_DIR . "/modules/{$mod_dir}/include.php")) {
            if ($_inf['module_active'] == '1') {
                $_tmp[$mod_dir] = $_inf['module_name'];
            }
            else {
                $_ina[$mod_dir] = $_inf['module_name'];
            }
        }
    }
    asort($_tmp, SORT_NATURAL);
    asort($_ina, SORT_NATURAL);
    $_tmp = array_merge($_tmp, $_ina);

    $_out = array();
    foreach ($_tmp as $mod_dir => $ignored) {
        if (!isset($_mods[$mod_dir]['module_category'])) {
            $_mods[$mod_dir]['module_category'] = 'tools';
        }
        $cat = $_mods[$mod_dir]['module_category'];
        if (!isset($_out[$cat])) {
            $_out[$cat] = array();
        }
        $_out[$cat][$mod_dir] = $_mods[$mod_dir];
    }
    $_rep['_modules']['core'] = $_out['core'];
    unset($_out['core']);
    $_rep['_modules'] = $_rep['_modules'] + $_out;
    ksort($_rep['_modules']);
    unset($_out);

    $_rep['default_category'] = 'core';
    if (isset($_post['module']) && isset($_mods["{$_post['module']}"]) && isset($_mods["{$_post['module']}"]['module_category'])) {
        $_rep['default_category'] = $_mods["{$_post['module']}"]['module_category'];
    }

    // See if our skin is overriding our core admin template
    jrCore_install_new_modules();
    $html = jrCore_parse_template('admin.tpl', $_rep, 'jrCore');

    // Output
    $_mta = jrCore_module_meta_data($_post['module']);
    jrCore_page_title("{$title} - {$_mta['name']}");
    jrCore_admin_menu_accordion_js($_rep['default_category']);
    jrCore_page_custom($html);
    jrCore_page_display();
}

//------------------------------
// admin_save (magic)
//------------------------------
function view_jrCore_admin_save($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    if (isset($_post['module_delete']) && $_post['module_delete'] === 'on') {
        jrCore_validate_location_url();
    }
    else {
        jrCore_form_validate($_post);
    }

    // See what we are saving...
    switch ($_post['_1']) {

        case 'global':

            // See if this module is presenting us with a validate function
            if (is_file(APP_DIR . "/modules/{$_post['module']}/config.php")) {
                $vfunc = "{$_post['module']}_config_validate";
                if (!function_exists($vfunc)) {
                    require_once APP_DIR . "/modules/{$_post['module']}/config.php";
                }
                if (function_exists($vfunc)) {
                    $_temp = $vfunc($_post);
                    if (!$_temp) {
                        // Error in validation
                        jrCore_form_result('referrer');
                        return true;
                    }
                    $_post = $_temp;
                    unset($_temp);
                }
            }
            // Update
            foreach ($_post as $k => $v) {
                if (isset($_conf["{$_post['module']}_{$k}"])) {
                    jrCore_set_setting_value($_post['module'], $k, $v);
                }
            }

            // Are we running the core MySQL Cache system?
            if (isset($_post['active_cache_system']) && $_post['active_cache_system'] != 'jrCore_mysql') {
                // We are not using MySQL caching - make sure core cache table is empty
                _jrCore_mysql_delete_all_cache_entries();
            }
            jrCore_delete_config_cache();
            jrCore_trigger_event('jrCore', 'global_config_updated', $_post);
            jrCore_set_form_notice('success', 'The settings have been successfully saved');
            $ref = jrCore_get_local_referrer();
            if (strpos($ref, '/section=')) {
                $sec = '';
                $_tm = explode('/', $ref);
                if ($_tm && is_array($_tm)) {
                    foreach ($_tm as $part) {
                        if (strpos($part, 'section=') === 0) {
                            $sec = "/{$part}";
                        }
                    }
                }
                jrCore_form_delete_session();
                jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/{$_post['_1']}{$sec}");
            }
            break;

        case 'quota':

            // See if this module is presenting us with a validate function
            if (is_file(APP_DIR . "/modules/{$_post['module']}/quota.php")) {
                $vfunc = "{$_post['module']}_quota_config_validate";
                if (!function_exists($vfunc)) {
                    require_once APP_DIR . "/modules/{$_post['module']}/quota.php";
                }
                if (function_exists($vfunc)) {
                    $_temp = $vfunc($_post);
                    if (!$_temp) {
                        // Error in validation
                        jrCore_form_result('referrer');
                        return true;
                    }
                    elseif (is_array($_temp)) {
                        $_post = $_temp;
                    }
                    unset($_temp);
                }
            }

            // See if we are doing a single quota or ALL quotas
            if (isset($_post['apply_to_all_quotas']) && $_post['apply_to_all_quotas'] == 'on') {
                $_aq = jrProfile_get_quotas();
                foreach ($_aq as $qid => $qname) {
                    $_qt = jrProfile_get_quota($_post['id'], false);
                    foreach ($_post as $k => $v) {
                        if (isset($_qt["quota_{$_post['module']}_{$k}"])) {
                            jrProfile_set_quota_value($_post['module'], $qid, $k, $v);
                        }
                    }
                }
            }
            else {
                if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
                    jrCore_set_form_notice('error', 'Invalid quota_id');
                    jrCore_form_result();
                }
                // Get current settings for this Quota
                $_qt = jrProfile_get_quota($_post['id'], false);
                if (!isset($_qt) || !is_array($_qt)) {
                    jrCore_set_form_notice('error', 'Invalid quota_id - unable to retrieve settings');
                    jrCore_form_result();
                }
                // Update
                foreach ($_post as $k => $v) {
                    if (isset($_qt["quota_{$_post['module']}_{$k}"])) {
                        jrProfile_set_quota_value($_post['module'], $_post['id'], $k, $v);
                    }
                }
            }

            // Empty caches
            jrCore_delete_all_cache_entries();

            jrCore_form_delete_session();
            jrCore_set_form_notice('success', 'The settings have been successfully saved');
            jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/{$_post['_1']}/id={$_post['id']}");
            break;

        case 'info':

            // Are we deleting this module?
            if (isset($_post['module_delete']) && $_post['module_delete'] === 'on') {

                $res = jrCore_delete_module($_post['module']);
                if ($res && strpos($res, 'error:') === 0) {
                    jrCore_set_form_notice('error', substr($res, 7));
                    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/info");
                }

                jrCore_logger('INF', "the " . $_mods["{$_post['module']}"]['module_name'] . " module was successfully deleted");
                jrCore_trigger_event('jrCore', 'module_deleted', $_post);
                jrCore_set_form_notice('success', 'The module was successfully deleted');
                jrCore_form_delete_session();

                jrCore_delete_all_cache_entries('jrCore');
                $_mods["{$_post['module']}"]['module_active'] = 0;

                // Rebuild JS and CSS
                jrCore_create_master_css($_conf['jrCore_active_skin']);
                jrCore_create_master_javascript($_conf['jrCore_active_skin']);

                jrCore_form_delete_session();
                jrCore_delete_config_cache();
                $url = jrCore_get_module_url('jrCore');
                jrCore_form_result("{$_conf['jrCore_base_url']}/{$url}/admin/global");

            }
            else {

                $tbl = jrCore_db_table_name('jrCore', 'module');
                $url = jrCore_db_escape($_post['module_url']);
                $mod = jrCore_db_escape($_post['module']);
                if (isset($_post['new_module_url']) && jrCore_checktype($_post['new_module_url'], 'url_name')) {

                    // Is this URL already being used by another module?
                    $nwu = jrCore_db_escape($_post['new_module_url']);
                    $req = "SELECT module_name FROM {$tbl} WHERE module_url = '{$nwu}' AND module_directory != '{$mod}' LIMIT 1";
                    $_ex = jrCore_db_query($req, 'SINGLE');
                    if ($_ex && isset($_ex)) {
                        jrCore_set_form_notice('error', "The URL you entered is already being used by the {$_ex['module_name']} module");
                        jrCore_form_field_hilight('new_module_url');
                        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/info");
                    }
                    $url = jrCore_db_escape($_post['new_module_url']);
                    $_post['module_url'] = $_post['new_module_url'];

                }
                $cat = jrCore_db_escape($_post['new_module_category']);
                $act = (isset($_post['module_active']) && $_post['module_active'] == 'off') ? 0 : 1;

                // If we are turning a module OFF let's give the module a chance to do any clean up if needed
                if ($act === 0) {
                    jrCore_trigger_event('jrCore', 'module_deactivated', array('module' => $mod));
                }

                $req = "UPDATE {$tbl} SET module_updated = UNIX_TIMESTAMP(), module_url = '{$url}', module_active = {$act}, module_category = '{$cat}' WHERE module_directory = '{$mod}' LIMIT 1";
                $cnt = jrCore_db_query($req, 'COUNT');
                if (!$cnt || $cnt !== 1) {
                    jrCore_set_form_notice('error', 'An error was encountered saving the module settings - please try again');
                    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/info");
                }

                // Verify the module if we are turning it on
                if ((!isset($_mods[$mod]['module_active']) || $_mods[$mod]['module_active'] != '1') && $act == 1) {
                    define('IN_JAMROOM_INSTALLER', 1);  // This ensures init() is run
                    jrCore_verify_module($mod);
                    $_mods[$mod]['module_active'] = 1;

                    // Rebuild JS and CSS
                    jrCore_create_master_css($_conf['jrCore_active_skin']);
                    jrCore_create_master_javascript($_conf['jrCore_active_skin']);

                    jrCore_load_config_file_and_defaults(true);
                    jrCore_trigger_event('jrCore', 'module_activated', array('module' => $mod));
                }

                jrCore_delete_config_cache();
                jrCore_delete_all_cache_entries();
                $_mods[$mod]['module_active'] = $act;

                jrCore_form_delete_session();
                if ($act == 1) {
                    jrCore_set_form_notice('success', 'The settings have been successfully saved'); // Module is currently disabled will show if its been deactivated.
                }

            }
            break;

        case 'language':

            // Get all the lang strings for this module
            $tbl = jrCore_db_table_name('jrUser', 'language');
            $mod = jrCore_db_escape($_post['module']);
            $req = "SELECT * FROM {$tbl} WHERE lang_module = '{$mod}' AND lang_code = '" . jrCore_db_escape($_post['lang_code']) . "'";
            $_rt = jrCore_db_query($req, 'lang_id');
            if (!isset($_rt) || !is_array($_rt)) {
                jrCore_set_form_notice('error', "Unable to retrieve language settings for module from language table - check debug_log errors");
                jrCore_form_result();
            }
            $req = "UPDATE {$tbl} SET lang_text = CASE lang_id\n";
            foreach ($_rt as $key => $_lng) {
                if (isset($_post["lang_{$key}"])) {
                    $req .= "WHEN {$key} THEN '" . jrCore_db_escape($_post["lang_{$key}"]) . "'\n";
                }
            }
            if (isset($req) && strpos($req, 'THEN')) {
                $req .= "ELSE lang_text END";
                jrCore_db_query($req);
            }
            jrCore_delete_all_cache_entries('jrUser');
            jrCore_set_form_notice('success', 'The language strings have been successfully saved');
            jrCore_form_delete_session();
            jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/{$_post['_1']}/lang_code={$_post['lang_code']}/p={$_post['p']}");
            break;

        case 'images':

            jrCore_create_media_directory(0, FORCE_LOCAL);
            // Get existing module info to see what images we have customized
            $_im = array();
            if (isset($_conf["jrCore_{$_post['module']}_custom_images"]{2})) {
                $_im = json_decode($_conf["jrCore_{$_post['module']}_custom_images"], true);
            }
            // Check for new custom files being uploaded
            $_up = jrCore_get_uploaded_meter_files($_post['upload_token']);
            if ($_up && is_array($_up)) {
                foreach ($_up as $_info) {
                    jrCore_write_media_file(0, "mod_{$_post['module']}_{$_info['name']}", $_info['tmp_name'], 'public-read');
                    $_im["{$_info['name']}"] = array($_info['size'], 'on');
                }
            }
            // Go through and save our uploaded images (if any)
            if (isset($_FILES) && is_array($_FILES)) {
                foreach ($_FILES as $k => $_info) {
                    $num = (int) str_replace('file_', '', $k);
                    if (isset($_info['size']) && jrCore_checktype($_info['size'], 'number_nz')) {
                        // Image extensions must match
                        $ext = jrCore_file_extension($_info['name']);
                        switch ($ext) {
                            case 'jpg':
                            case 'png':
                            case 'gif':
                                break;
                            default:
                                jrCore_set_form_notice('error', 'Invalid image type for ' . $_post["name_{$num}"] . ' - only JPG, PNG and GIF images are allowed');
                                jrCore_form_result();
                                break;
                        }
                        if (isset($_post["name_{$num}"]{0})) {
                            $nam = $_post["name_{$num}"];
                            jrCore_write_media_file(0, "mod_{$_post['module']}_{$nam}", $_info['tmp_name'], 'public-read');
                            unlink($_info['tmp_name']);  // OK
                            $_im[$nam] = array($_info['size']);
                            $_post["name_{$num}_active"] = 'on';
                        }
                    }
                }
            }
            // Update setting with new values
            // [name_0_active] => on
            // [name_0] => bckgrd.png
            foreach ($_post as $k => $v) {
                if (strpos($k, 'name_') === 0 && strpos($k, '_active')) {
                    $num = (int) substr($k, 5, strrpos($k, '_'));
                    $nam = $_post["name_{$num}"];
                    if (isset($_im[$nam][0])) {
                        $_im[$nam][1] = $v;
                    }
                    else {
                        unset($_im[$nam]);
                    }
                }
            }
            jrCore_set_setting_value('jrCore', "{$_post['module']}_custom_images", json_encode($_im));
            jrCore_delete_all_cache_entries();
            break;

        case 'templates':

            //  [form_begin_template_active] => on
            $_act = array();
            $_off = array();
            $_all = array();
            foreach ($_post as $k => $v) {
                if (strpos($k, '_template_active')) {
                    $tpl = str_replace('_template_active', '.tpl', $k);
                    // See if we are turning this template on or off
                    if ($v == 'on') {
                        $_act[] = $tpl;
                        $_all[] = $tpl;
                    }
                    else {
                        $_off[] = $tpl;
                        $_all[] = $tpl;
                    }
                }
            }

            // Set active/inactive
            if (isset($_all) && is_array($_all) && count($_all) > 0) {
                $mod = jrCore_db_escape($_post['module']);
                $tbl = jrCore_db_table_name('jrCore', 'template');
                if (isset($_act) && is_array($_act) && count($_act) > 0) {
                    $req = "UPDATE {$tbl} SET template_active = '1' WHERE template_module = '{$mod}' AND template_name IN('" . implode("','", $_act) . "')";
                    jrCore_db_query($req);
                }
                if (isset($_off) && is_array($_off) && count($_off) > 0) {
                    $req = "UPDATE {$tbl} SET template_active = '0' WHERE template_module = '{$mod}' AND template_name IN('" . implode("','", $_off) . "')";
                    jrCore_db_query($req);
                }

                // Reset cache for any that were changed
                foreach ($_all as $tpl) {
                    jrCore_get_template_file($tpl, $_post['module'], 'reset');
                }
            }
            jrCore_set_form_notice('success', 'The template settings have been successfully saved');
            break;
    }
    jrCore_form_delete_session();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/{$_post['_1']}");
    return true;
}

//------------------------------
// template_modify (magic)
//------------------------------
function view_jrCore_template_modify($_post, $_user, $_conf)
{
    jrUser_master_only();

    // Setup Code Mirror
    $_tmp = array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/lib/codemirror.css");
    jrCore_create_page_element('css_href', $_tmp);
    $_tmp = array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/lib/codemirror.js");
    jrCore_create_page_element('javascript_href', $_tmp);
    $_tmp = array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/mode/smarty/smarty.js");
    jrCore_create_page_element('javascript_href', $_tmp);
    $_tmp = array('var editor = CodeMirror.fromTextArea(document.getElementById("template_body"), { lineNumbers: true, matchBrackets: true, mode: \'smarty\' });');
    jrCore_create_page_element('javascript_ready_function', $_tmp);

    if (isset($_post['skin'])) {
        jrCore_page_skin_tabs($_post['skin'], 'templates');
        $cancel_url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/templates/skin={$_post['skin']}";
        $t_type     = 'skin';
    }
    else {
        jrCore_page_admin_tabs($_post['module'], 'templates');
        $cancel_url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/templates";
        $t_type     = 'module';
    }

    // our page banner
    $_tp = array();
    $tpl = '';
    $btn = null;
    if (isset($_post['template']{1}) && jrCore_checktype($_post['template'], 'printable')) {
        if (isset($_post['skin']{0})) {
            $tpl_file = APP_DIR . "/skins/{$_post['skin']}/{$_post['template']}";
        }
        else {
            $tpl_file = APP_DIR . "/modules/{$_post['module']}/templates/{$_post['template']}";
        }
        $tpl = str_replace(APP_DIR .'/', '', $tpl_file);
    }
    elseif (isset($_post['id']) && jrCore_checktype($_post['id'], 'number_nz')) {
        // Database template
        $tbl = jrCore_db_table_name('jrCore', 'template');
        $req = "SELECT * FROM {$tbl} WHERE template_id = '{$_post['id']}'";
        $_tp = jrCore_db_query($req, 'SINGLE');
        if (!$_tp || !is_array($_tp)) {
            jrCore_set_form_notice('error', 'Invalid template_id - please try again');
            jrCore_location($cancel_url);
        }
        $tpl = $_tp['template_name'];
        $url = jrCore_get_module_url('jrCore');
        $btn = jrCore_page_button("r{$_tp['template_name']}", 'reset', "jrCore_confirm('Reset Template?', 'Reset this template to the default provided by the {$t_type}?', function() { jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/template_reset_save/{$t_type}={$_tp['template_module']}/id=" . $_tp['template_id'] . "')})");
    }

    jrCore_page_banner('Template Editor: <span style="text-transform:none">'. $tpl . '</span>', $btn);

    $_tmp = array(
        'submit_value'     => 'save changes',
        'cancel'           => $cancel_url,
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // Template ID
    $_tmp = array(
        'name'  => 'template_type',
        'type'  => 'hidden',
        'value' => $t_type
    );
    jrCore_form_field_create($_tmp);

    if (isset($_post['skin']{0})) {
        $_tmp = array(
            'name'  => 'skin',
            'type'  => 'hidden',
            'value' => $_post['skin']
        );
        jrCore_form_field_create($_tmp);
    }

    // Get info about this template...
    $tpl_body = '';
    if (isset($_post['id']) && jrCore_checktype($_post['id'], 'number_nz')) {

        $tpl_body = $_tp['template_body'];

        // Template ID
        $_tmp = array(
            'name'  => 'template_id',
            'type'  => 'hidden',
            'value' => $_post['id']
        );
        jrCore_form_field_create($_tmp);
    }

    // From file
    elseif (isset($_post['template']{1}) && jrCore_checktype($_post['template'], 'printable')) {

        // Make sure this is a good file
        $_post['template'] = basename($_post['template']);
        if (isset($_post['skin']{0})) {
            $tpl_file = APP_DIR . "/skins/{$_post['skin']}/{$_post['template']}";
        }
        else {
            $tpl_file = APP_DIR . "/modules/{$_post['module']}/templates/{$_post['template']}";
        }
        if (!is_file($tpl_file)) {
            jrCore_set_form_notice('error', 'Template file not found - please try again');
            jrCore_location($cancel_url);
        }
        $tpl_body = file_get_contents($tpl_file);

        $_tmp = array(
            'name'  => 'template_name',
            'type'  => 'hidden',
            'value' => $_post['template']
        );
        jrCore_form_field_create($_tmp);
    }
    else {
        jrCore_set_form_notice('error', 'Invalid template - please try again');
        jrCore_location($cancel_url);
    }

    // Show template
    if (isset($_SESSION['template_body_save']) && strlen($_SESSION['template_body_save']) > 0) {
        $tpl_body = $_SESSION['template_body_save'];
        unset($_SESSION['template_body_save']);
    }
    $html = '<div class="form_template"><textarea id="template_body" name="template_body" class="form_template_editor">' . htmlspecialchars($tpl_body) . '</textarea></div>';
    jrCore_page_custom($html);
    jrCore_page_display();
}

//------------------------------
// test_template
//------------------------------
function view_jrCore_test_template($_post, $_user, $_conf)
{
    global $_mods;
    if (!isset($_post['_1']) || strlen($_post['_1']) !== 8) {
        echo "error: invalid template";
        jrCore_db_close();
        exit;
    }
    $key = $_post['_1'];
    $tpl = jrCore_get_temp_value('jrCore', "{$key}_template");
    if (!$tpl) {
        echo "error: invalid template";
        jrCore_db_close();
        exit;
    }

    $cdr = jrCore_get_module_cache_dir('jrCore');
    $nam = 'test_template_' . $_post['_1'] . '.tpl';
    jrCore_write_to_file("{$cdr}/{$nam}", $tpl);
    if (!is_file("{$cdr}/{$nam}")) {
        echo "error : unable to create template file for testing - check directory permissions";
        jrCore_db_close();
        exit;
    }

    ini_set('display_errors', 1);
    ini_set('html_errors', 0);
    ini_set('log_errors', 0);
    jrCore_set_flag('jrCore_suppress_activity_log', 1);

    if (!class_exists('Smarty')) {
        require_once APP_DIR . '/modules/jrCore/contrib/smarty/libs/Smarty.class.php';
    }

    // Set our compile dir
    $temp             = new Smarty;
    $temp->compile_id = md5(APP_DIR);
    $temp->setCompileDir(jrCore_get_module_cache_dir($_conf['jrCore_active_skin']));

    // Get plugin directories
    $_dir = array(APP_DIR . '/modules/jrCore/contrib/smarty/libs/plugins');
    $temp->setPluginsDir($_dir);
    $temp->force_compile = true;

    $_data['page_title']  = jrCore_get_flag('jrcore_html_page_title');
    $_data['jamroom_dir'] = APP_DIR;
    $_data['jamroom_url'] = $_conf['jrCore_base_url'];
    $_data['_conf']       = $_conf;
    $_data['_post']       = $_post;
    $_data['_mods']       = $_mods;
    $_data['_user']       = $_SESSION;
    $_data['_items']      = array();

    // Remove User and MySQL info - we don't want this to ever leak into a template
    unset($_data['_user']['user_password'], $_data['_user']['user_old_password'], $_data['_user']['user_forgot_key']);
    unset($_data['_conf']['jrCore_db_host'], $_data['_conf']['jrCore_db_user'], $_data['_conf']['jrCore_db_pass'], $_data['_conf']['jrCore_db_name'], $_data['_conf']['jrCore_db_port']);

    // We also need to load the include.php for the skin this template file belongs to
    if (isset($_post['_2']) && is_file(APP_DIR . "/skins/{$_post['_2']}/include.php")) {
        require_once APP_DIR . "/skins/{$_post['_2']}/include.php";
    }

    $temp->assign($_data);
    ob_start();
    $temp->display("{$cdr}/{$nam}");
    $html = ob_get_contents();
    ob_end_clean();
    jrCore_db_close();
    echo $html;
    unlink("{$cdr}/{$nam}");  // OK
    exit;
}

//------------------------------
// template_modify_save (magic)
//------------------------------
function view_jrCore_template_modify_save($_post, $_user, $_conf)
{
    jrUser_master_only();

    // See if we are doing a skin or module
    $tid = false;
    $crt = false;
    $mod = (isset($_post['skin'])) ? $_post['skin'] : $_post['module'];

    $err = jrCore_test_template_for_errors($mod, $_post['template_body']);
    if ($err && strpos($err, 'error') === 0) {
        $_SESSION['template_body_save'] = $_post['template_body'];
        jrCore_set_form_notice('error', substr($err, 7), false);
        jrCore_form_result();
    }

    $tbl = jrCore_db_table_name('jrCore', 'template');
    // See if we are updating a DB template or first time file
    if (isset($_post['template_id']) && jrCore_checktype($_post['template_id'], 'number_nz')) {
        // Make sure we have a valid template
        $req = "SELECT * FROM {$tbl} WHERE template_id = '{$_post['template_id']}'";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if (!$_rt || !is_array($_rt)) {
            $_SESSION['template_body_save'] = $_post['template_body'];
            jrCore_set_form_notice('error', 'Invalid template_id - please try again');
            jrCore_form_result();
        }
        $req = "UPDATE {$tbl} SET
                  template_updated = UNIX_TIMESTAMP(),
                  template_user    = '" . jrCore_db_escape($_user['user_name']) . "',
                  template_body    = '" . jrCore_db_escape($_post['template_body']) . "'
                 WHERE template_id = '{$_post['template_id']}'";
        $cnt = jrCore_db_query($req, 'COUNT');
        // Reset the template cache
        jrCore_get_template_file($_rt['template_name'], $mod, 'reset');
    }
    else {
        if (!isset($_post['template_name']{1})) {
            $_SESSION['template_body_save'] = $_post['template_body'];
            jrCore_set_form_notice('error', 'Invalid template_name - please try again');
            jrCore_form_result();
        }
        // See if we already exist - this can happen when the user FIRST modifies the template
        // and does not leave the screen, and modifies again
        $nam = jrCore_db_escape($_post['template_name']);
        $mod = jrCore_db_escape($mod);
        $req = "INSERT INTO {$tbl} (template_created,template_updated,template_user,template_active,template_name,template_module,template_body)
                VALUES(UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),'" . jrCore_db_escape($_user['user_name']) . "','0','{$nam}','{$mod}','" . jrCore_db_escape($_post['template_body']) . "')";
        $tid = jrCore_db_query($req, 'INSERT_ID');
        if (isset($tid) && jrCore_checktype($tid, 'number_nz')) {
            $cnt = 1;
            // Reset the template cache
            jrCore_get_template_file($_post['template_name'], $mod, 'reset');
        }
        $crt = true;
    }
    if (isset($cnt) && $cnt === 1) {
        jrCore_set_form_notice('success', 'The template has been successfully updated<br><b>NOTE:</b> Changes to template do not take effect until caches are reset', false);
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered saving the template update - please try again');
    }
    jrCore_form_delete_session();
    // If we have just CREATED a new template, we must refresh on the ID
    if ($tid && $crt) {
        if (isset($_post['skin'])) {
            jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/template_modify/skin={$_post['skin']}/id={$tid}");
        }
        else {
            jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/template_modify/id={$tid}");
        }
    }
    jrCore_form_result();
}

//------------------------------
// cache_reset
//------------------------------
function view_jrCore_cache_reset($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore');
    jrCore_page_banner('Reset Caches');

    // Form init
    $_tmp = array(
        'submit_value' => 'reset selected caches',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools"
    );
    jrCore_form_create($_tmp);

    // Reset Smarty cache
    $_tmp = array(
        'name'     => 'reset_template_cache',
        'label'    => 'Reset Template Cache',
        'help'     => 'Check this box to delete the compiled skin templates, CSS and Javascript - these items will be rebuilt as needed.',
        'type'     => 'checkbox',
        'value'    => 'on',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Reset Database Cache
    $_tmp = array(
        'name'     => 'reset_database_cache',
        'label'    => 'Reset Database Cache',
        'help'     => 'Check this box to delete cached skin and profile pages in the database.',
        'type'     => 'checkbox',
        'value'    => 'on',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Reset APCu cache
    $_tmp = array(
        'name'     => 'reset_local_cache',
        'label'    => 'Reset Memory Cache',
        'help'     => 'Check this box to reset the memory based local cache',
        'type'     => 'checkbox',
        'value'    => 'on',
        'validate' => 'onoff'
    );
    if (!jrCore_local_cache_is_enabled()) {
        $_tmp['disabled'] = 'disabled';
        $_tmp['help']    .= '<br><br><b>NOTE:</b> Memory Cache is disabled when running in Developer Mode';
    }
    jrCore_form_field_create($_tmp);

    // Reset Sprite Cache
    $_tmp = array(
        'name'     => 'reset_sprite_cache',
        'label'    => 'Reset Icon Cache',
        'help'     => 'Check this box to delete the cached sprite icon images so they are rebuilt.',
        'type'     => 'checkbox',
        'value'    => 'on',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Reset Sprite Cache
    $_tmp = array(
        'name'     => 'reset_form_cache',
        'label'    => 'Reset Form Session Cache',
        'help'     => 'Check this box to delete all active and cached form sessions - any in-progress form submissions will need to be redone.',
        'type'     => 'checkbox',
        'value'    => 'off',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_display();
}

//------------------------------
// cache_reset_save
//------------------------------
function view_jrCore_cache_reset_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Reset cache directories
    if (isset($_post['reset_template_cache']) && $_post['reset_template_cache'] == 'on') {

        // Reset other caches
        jrCore_reset_template_cache();

        // Rebuild master CSS and JS
        jrCore_create_master_css($_conf['jrCore_active_skin']);
        jrCore_create_master_javascript($_conf['jrCore_active_skin']);

    }

    // Reset database cache
    if (isset($_post['reset_database_cache']) && $_post['reset_database_cache'] == 'on') {
        jrCore_delete_all_cache_entries();
    }

    if (isset($_post['reset_local_cache']) && $_post['reset_local_cache'] == 'on') {
        jrCore_reset_local_cache();
    }

    // Remove any generated Sprite images and Spire CSS files
    if (isset($_post['reset_sprite_cache']) && $_post['reset_sprite_cache'] == 'on') {
        jrCore_reset_sprite_cache();
    }

    // Reset form sessions
    if (isset($_post['reset_form_cache']) && $_post['reset_form_cache'] == 'on') {
        jrCore_form_delete_all_form_sessions();
    }

    jrCore_set_form_notice('success', 'The selected caches were successfully reset');
    jrCore_location('referrer');
}

//------------------------------
// skin_image_delete_save
//------------------------------
function view_jrCore_skin_image_delete_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['skin']{0}) && !isset($_post['mod']{0})) {
        jrCore_set_form_notice('error', 'Invalid skin or module - please try again');
        jrCore_form_result('referrer');
    }
    if (!isset($_post['name']{0})) {
        jrCore_set_form_notice('error', 'Invalid image name - please try again');
        jrCore_form_result('referrer');
    }
    if (isset($_post['mod']{0})) {
        $nam = $_post['mod'];
        $tag = 'mod_';
    }
    else {
        $nam = $_post['skin'];
        $tag = '';
    }
    // Remove from custom image info
    if (isset($_conf["jrCore_{$nam}_custom_images"]{2})) {
        $_im = json_decode($_conf["jrCore_{$nam}_custom_images"], true);
        unset($_im["{$_post['name']}"]);
        // Update setting with new values
        jrCore_set_setting_value('jrCore', "{$nam}_custom_images", json_encode($_im));
        jrCore_delete_all_cache_entries('jrCore', 0);
        jrCore_delete_media_file(0, "{$tag}{$nam}_{$_post['name']}");
    }
    jrCore_set_form_notice('success', 'The custom image was successfully deleted');
    jrCore_form_result('referrer');
}

//------------------------------
// template_reset_save
//------------------------------
function view_jrCore_template_reset_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid template_id - please try again');
        jrCore_form_result('referrer');
    }

    // Get info about this template first so we can reset
    $tbl = jrCore_db_table_name('jrCore', 'template');
    $req = "SELECT template_name, template_module FROM {$tbl} WHERE template_id = '{$_post['id']}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'Invalid template_id - please try again');
        jrCore_form_result('referrer');
    }
    $req = "DELETE FROM {$tbl} WHERE template_id = '{$_post['id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        jrCore_get_template_file($_rt['template_name'], $_rt['template_module'], 'reset');
        jrCore_set_form_notice('success', 'The template has been reset to use the default version');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered deleting the modified template from the database - please try again');
    }
    $url = jrCore_get_local_referrer();
    if ($url && strpos($url, '/hl=')) {
        $url = jrCore_strip_url_params($url, array('hl'));
        jrCore_location($url);
    }
    if (strpos($url, '/id=')) {
        $url = jrCore_strip_url_params($url, array('id'));
        jrCore_location($url . "/template={$_rt['template_name']}");
    }
    jrCore_location($url);
}

//------------------------------
// template_import_save
//------------------------------
function view_jrCore_template_import_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['skin']) || strlen($_post['skin']) === 0) {
        jrCore_set_form_notice('error', 'invalid skin');
        jrCore_location('referrer');
    }
    $_skn = jrCore_get_skin_db_info($_post['skin']);
    if (!$_skn || !is_array($_skn)) {
        jrCore_set_form_notice('error', 'invalid skin - not data found');
        jrCore_location('referrer');
    }
    if (!isset($_skn['skin_cloned_from'])) {
        jrCore_set_form_notice('error', 'unable to determine the cloned from skin');
        jrCore_location('referrer');
    }
    if (!isset($_post['tpl']) || strlen($_post['tpl']) === 0) {
        jrCore_set_form_notice('error', 'invalid template');
        jrCore_location('referrer');
    }
    $tpl = APP_DIR . "/skins/{$_skn['skin_cloned_from']}/{$_post['tpl']}";
    if (!is_file($tpl)) {
        jrCore_set_form_notice('error', 'invalid template - file not found');
        jrCore_location('referrer');
    }
    if (!copy($tpl, APP_DIR . "/skins/{$_post['skin']}/{$_post['tpl']}")) {
        jrCore_set_form_notice('error', 'unable to copy template to skin directory - check permissions');
    }
    jrCore_location('referrer');
}

//------------------------------
// template_create_save
//------------------------------
function view_jrCore_template_create_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['skin']) || strlen($_post['skin']) === 0) {
        jrCore_set_form_notice('error', 'invalid skin');
        jrCore_location('referrer');
    }
    $_skn = jrCore_get_skin_db_info($_post['skin']);
    if (!$_skn || !is_array($_skn)) {
        jrCore_set_form_notice('error', 'invalid skin - not data found');
        jrCore_location('referrer');
    }
    if (!isset($_skn['skin_cloned_from'])) {
        jrCore_set_form_notice('error', 'unable to determine the cloned from skin');
        jrCore_location('referrer');
    }
    if (!isset($_post['tpl']) || strlen($_post['tpl']) === 0) {
        jrCore_set_form_notice('error', 'invalid template');
        jrCore_location('referrer');
    }
    $tpl = APP_DIR . "/skins/{$_post['skin']}/{$_post['tpl']}";
    if (is_file($tpl)) {
        jrCore_set_form_notice('error', 'The template name entered already exists - please enter a different name');
        jrCore_location('referrer');
    }
    if (!jrCore_write_to_file($tpl, "{* {$_post['tpl']} *}\n\n")) {
        jrCore_set_form_notice('error', 'unable to create template to skin directory - check permissions');
        jrCore_location('referrer');
    }
    jrCore_set_form_notice('success', "The new template was successfully created");
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/template_modify/skin={$_post['skin']}/template={$_post['tpl']}");
}

//------------------------------
// css_reset_save
//------------------------------
function view_jrCore_css_reset_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    // Reset CSS elements
    if (!isset($_post['skin']{0})) {
        jrCore_set_form_notice('error', 'Invalid skin - please try again');
        jrCore_form_result('referrer');
    }
    if (!isset($_post['tag']{0})) {
        jrCore_set_form_notice('error', 'Invalid element tag - please try again');
        jrCore_form_result('referrer');
    }
    $_post['tag'] = urldecode($_post['tag']);

    // Remove info about this element from the custom css
    $tbl = jrCore_db_table_name('jrCore', 'skin');
    $req = "SELECT skin_custom_css FROM {$tbl} WHERE skin_directory = '" . jrCore_db_escape($_post['skin']) . "'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt) && strlen($_rt['skin_custom_css']) > 3) {
        $_new = json_decode($_rt['skin_custom_css'], true);
        if ($_new && is_array($_new)) {
            if (isset($_new["{$_post['tag']}"])) {
                unset($_new["{$_post['tag']}"]);
                $_new = json_encode($_new);
                $req  = "UPDATE {$tbl} SET skin_updated = UNIX_TIMESTAMP(), skin_custom_css = '" . jrCore_db_escape($_new) . "' WHERE skin_directory = '" . jrCore_db_escape($_post['skin']) . "'";
                $cnt  = jrCore_db_query($req, 'COUNT');
                if (!$cnt || $cnt === 0) {
                    jrCore_set_form_notice('error', 'An error was enountered saving the custom style to the database - please try again');
                    jrCore_form_result('referrer');
                }
            }
        }
    }
    jrCore_form_delete_session();

    // Rebuild CSS
    if ($_post['skin'] == $_conf['jrCore_active_skin']) {
        jrCore_create_master_css($_post['skin']);
    }

    jrCore_form_result('referrer');
}

//------------------------------
// advanced_config
//------------------------------
function view_jrCore_advanced_config($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore');
    jrCore_page_banner('Advanced Config Keys');

    jrCore_set_form_notice('error', '<b>WARNING!</b> Advanced Config Keys should only be used if you are sure you need them!<br>You can render your site unusable by entering an invalid value here, so be careful!', false);
    jrCore_get_form_notice();

        // Form init
    $_tmp = array(
        'submit_value'     => 'update config',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    $_opt = array(
        'create' => 'Create a new Config Key',
        'delete' => 'Delete an existing Config Key'
    );
    $_tmp = array(
        'name'     => 'action',
        'label'    => 'action',
        'help'     => 'Select the action you would like to perform',
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'not_empty',
        'default'  => 'create',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Config key
    $_tmp = array(
        'name'     => 'config_key',
        'label'    => 'config key',
        'help'     => 'Enter the Config key that will be added or removed from the config.php file - i.e. &quot;jrCore_max_system_queue_workers&quot;',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Config value
    $_tmp = array(
        'name'     => 'config_value',
        'label'    => 'config value',
        'help'     => 'Enter the value that will be used for this config key (optional if removing an existing Config Key)',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => false
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// advanced_config_save
//------------------------------
function view_jrCore_advanced_config_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    if (strpos($_post['config_key'], 'jrCore_db_') === 0 && $_post['config_key'] != 'jrCore_db_persistent') {
        jrCore_set_form_notice('error', 'Invalid config key - cannot start with jrCore_db_');
        jrCore_form_result();
    }
    if ($_post['action'] == 'create') {
        if (jrCore_add_key_to_config($_post['config_key'], $_post['config_value'])) {
            $_conf["{$_post['config_key']}"] = $_post['config_value'];
            jrCore_delete_config_cache();
            jrCore_form_delete_session();
            jrCore_set_form_notice('success', 'The new config key was successfully added');
        }
        else {
            jrCore_set_form_notice('error', 'An error was encountered adding the config key');
        }
    }
    else {
        if (jrCore_delete_key_from_config($_post['config_key'])) {
            unset($_conf["{$_post['config_key']}"]);
            jrCore_delete_config_cache();
            jrCore_form_delete_session();
            jrCore_set_form_notice('success', 'The config key was successfully removed');
        }
        else {
            jrCore_set_form_notice('error', 'An error was encountered removing the config key');
        }
    }
    jrCore_form_result();
}

//------------------------------
// integrity_check
//------------------------------
function view_jrCore_integrity_check($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore');
    jrCore_page_banner("Integrity Check");

    // Form init
    $_tmp = array(
        'submit_value'  => 'run integrity check',
        'cancel'        => 'referrer',
        'submit_title'  => 'Run an Integrity Check?',
        'submit_prompt' => 'Please be patient - on a large system this could take a bit to complete depending on the options',
        'submit_modal'  => 'update',
        'modal_width'   => 600,
        'modal_height'  => 400,
        'modal_note'    => 'Please be patient while the Integrity Check runs'
    );
    jrCore_form_create($_tmp);

    // Validate Modules
    $_tmp = array(
        'name'     => 'validate_modules',
        'label'    => 'verify modules',
        'help'     => 'Check this box so the system will verify active modules and the structure of your database tables.',
        'type'     => 'checkbox',
        'value'    => 'on',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Validate Skins
    $_tmp = array(
        'name'     => 'validate_skins',
        'label'    => 'verify skins',
        'help'     => 'Check this box so the system will verify active skins and and skin config options.',
        'type'     => 'checkbox',
        'value'    => 'on',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_section_header('extra options');

    // Reset Caches
    $_tmp = array(
        'name'     => 'reset_caches',
        'label'    => 'reset caches',
        'help'     => 'Check this option to reset the Template and Database caches after running the Integrity Check',
        'type'     => 'checkbox',
        'value'    => 'off',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Repair Modules
    $_tmp = array(
        'name'     => 'repair_modules',
        'label'    => 'repair modules',
        'help'     => 'Some modules include additional checks to repair invalid database entries - check this option to run these additional options.<br><br><strong>WARNING:</strong> On large systems some of these checks may take a long time to run - please be patient.',
        'type'     => 'checkbox',
        'value'    => 'off',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Repair Tables
    $_tmp = array(
        'name'     => 'repair_tables',
        'label'    => 'repair tables',
        'help'     => 'If you suspect that some of your Database tables are corrupt, check this box and REPAIR TABLE will be run on each of your database tables.<br><br><strong>WARNING:</strong> While a repair is running on a table, access to that table will be locked. The repair operation could take several minutes for very large tables.',
        'type'     => 'checkbox',
        'value'    => 'off',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);

    // Optimize Tables
    $_tmp = array(
        'name'     => 'optimize_tables',
        'label'    => 'optimize tables',
        'help'     => 'Check this option to run OPTIMIZE TABLE on each database table.  This is helpful for sites that have been running a long time where the table data file can become &quot;fragmented&quot; and make data access a little bit slower.<br><br><strong>WARNING:</strong> While OPTIMIZE TABLE is running on a table, access to that table will be locked - the operation could take several minutes for very large tables.',
        'type'     => 'checkbox',
        'value'    => 'off',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// integrity_check_save
//------------------------------
function view_jrCore_integrity_check_save($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_form_validate($_post);
    @ini_set('max_execution_time', 28800); // 8 hours max
    @ini_set('memory_limit', '1024M');

    jrCore_logger('INF', 'integrity check started');

    // Check for Repair Tables first
    if (isset($_post['repair_tables']) && $_post['repair_tables'] == 'on') {
        $_rt = jrCore_db_query('SHOW TABLES', 'NUMERIC');
        if ($_rt && is_array($_rt)) {
            $_dn = array();
            foreach ($_rt as $tbl) {
                $tbl = reset($tbl);
                if (!isset($_dn[$tbl])) {
                    jrCore_form_modal_notice('update', "repairing table: {$tbl}");
                    jrCore_db_query("REPAIR TABLE {$tbl}");
                    $_dn[$tbl] = 1;
                }
            }
            unset($_dn);
        }
    }

    // Module install validation
    if (isset($_post['validate_modules']) && $_post['validate_modules'] == 'on') {

        // Make sure our Core schema is updated first
        jrCore_validate_module_schema('jrCore');
        jrCore_check_for_dead_queue_workers();
        jrCore_validate_queue_info();
        jrCore_validate_queue_data();

        //----------------------
        // MODULES
        //----------------------
        // First - validate schema for each module
        foreach ($_mods as $mod_dir => $_inf) {
            jrCore_form_modal_notice('update', "verifying schema: " . $_mods[$mod_dir]['module_name']);
            jrCore_validate_module_schema($mod_dir);
        }
        // Make sure module is setup
        foreach ($_mods as $mod_dir => $_inf) {
            if (!is_dir(APP_DIR . "/modules/{$mod_dir}") && !is_link(APP_DIR . "/modules/{$mod_dir}")) {

                // Looks like this module was removed from the filesystem - let's do a cleanup
                $tbl = jrCore_db_table_name('jrCore', 'module');
                $req = "DELETE FROM {$tbl} WHERE module_directory = '" . jrCore_db_escape($mod_dir) . "' LIMIT 1";
                $cnt = jrCore_db_query($req, 'COUNT');
                if (!$cnt || $cnt !== 1) {
                    jrCore_form_modal_notice('error', "unable to cleanup deleted module: {$mod_dir}");
                }

                // Remove any pending items
                $tbl = jrCore_db_table_name('jrCore', 'pending');
                $req = "DELETE FROM {$tbl} WHERE pending_module = '{$mod_dir}'";
                jrCore_db_query($req);

                // Cleanup any cache
                $cdr = jrCore_get_module_cache_dir($mod_dir);
                if (is_dir($cdr)) {
                    jrCore_delete_dir_contents($cdr);
                    rmdir($cdr);
                }

            }
            jrCore_form_modal_notice('update', "verifying module: " . $_mods[$mod_dir]['module_name']);
            jrCore_verify_module($mod_dir);
        }

    }

    // Skin install validation
    if (isset($_post['validate_skins']) && $_post['validate_skins'] == 'on') {

        //----------------------
        // SKINS
        //----------------------
        $_rt = jrCore_get_skins();
        if ($_rt && is_array($_rt)) {
            foreach ($_rt as $skin_dir) {

                $_mta = jrCore_skin_meta_data($skin_dir);
                $name = (isset($_mta['title'])) ? $_mta['title'] : $skin_dir;

                jrCore_form_modal_notice('update', "verifying skin: {$name}");
                jrCore_verify_skin($skin_dir);

            }
        }

    }

    // Module repair
    if (isset($_post['repair_modules']) && $_post['repair_modules'] == 'on') {
        foreach ($_mods as $mod_dir => $_inf) {
            jrCore_form_modal_notice('update', "repairing module: " . $_mods[$mod_dir]['module_name']);

            // Repair module DS
            if (jrCore_is_datastore_module($mod_dir)) {
                jrCore_db_repair_datastore($mod_dir);
            }
            jrCore_trigger_event('jrCore', 'repair_module', $_post, $_mods[$mod_dir], $mod_dir);
        }
    }

    // Optimize Tables
    if (isset($_post['optimize_tables']) && $_post['optimize_tables'] == 'on') {
        $_rt = jrCore_db_query('SHOW TABLES', 'NUMERIC');
        if ($_rt && is_array($_rt)) {
            $_dn = array();
            foreach ($_rt as $tbl) {
                $tbl = reset($tbl);
                if (!isset($_dn[$tbl])) {
                    jrCore_form_modal_notice('update', "optimizing table: {$tbl}");
                    jrCore_db_query("OPTIMIZE TABLE {$tbl}");
                    $_dn[$tbl] = 1;
                }
            }
            unset($_dn);
        }
    }

    // Reset Caches
    if (isset($_post['reset_caches']) && $_post['reset_caches'] == 'on') {

        // Rebuild master CSS and JS
        jrCore_create_master_css($_conf['jrCore_active_skin']);
        jrCore_create_master_javascript($_conf['jrCore_active_skin']);

        jrCore_delete_all_cache_entries();
        jrCore_reset_template_cache();

        jrCore_form_modal_notice('update', "cache reset: database and template caches reset");
    }

    jrCore_form_delete_session();
    jrCore_logger('INF', 'integrity check completed');
    jrCore_form_modal_notice('complete', 'The integrity check options were successfully completed');
    jrCore_db_close();
    exit;
}

//------------------------------
// activity_log
//------------------------------
function view_jrCore_activity_log($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore');
    jrCore_show_activity_log($_post, $_user, $_conf);
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    jrCore_page_display();
}

//------------------------------
// log_debug
//------------------------------
function view_jrCore_log_debug($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_page_set_meta_header_only();
    $button = jrCore_page_button('close', 'close', 'self.close();');
    jrCore_page_banner('debug entry', $button);
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice_page('error', 'invalid log_debug id');
    }
    $tbl = jrCore_db_table_name('jrCore', 'log');
    $tbd = jrCore_db_table_name('jrCore', 'log_debug');
    $req = "SELECT * FROM {$tbl} l LEFT JOIN {$tbd} d ON d.log_log_id = l.log_id WHERE log_id = '{$_post['_1']}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!is_array($_rt)) {
        jrCore_notice_page('error', 'invalid log_debug id - not found in db');
    }

    $dat             = array();
    $dat[1]['title'] = 'Key';
    $dat[1]['width'] = '10%';
    $dat[2]['title'] = 'Value';
    $dat[2]['width'] = '90%';
    jrCore_page_table_header($dat);

    $dat             = array();
    $dat[1]['title'] = 'Message';
    $dat[2]['title'] = jrCore_entity_string($_rt['log_text']);
    $dat[2]['class'] = "log-{$_rt['log_priority']}";
    jrCore_page_table_row($dat);

    $dat             = array();
    $dat[1]['title'] = 'Date';
    $dat[2]['title'] = jrCore_format_time($_rt['log_created']);
    jrCore_page_table_row($dat);

    $dat             = array();
    $dat[1]['title'] = 'IP&nbsp;Address';
    $dat[2]['title'] = $_rt['log_ip'];
    jrCore_page_table_row($dat);

    $dat             = array();
    $dat[1]['title'] = 'URL';
    $dat[2]['title'] = $_rt['log_url'];
    jrCore_page_table_row($dat);

    $dat             = array();
    $dat[1]['title'] = 'Memory';
    $dat[2]['title'] = jrCore_format_size($_rt['log_memory']);
    jrCore_page_table_row($dat);

    $dat             = array();
    $dat[1]['title'] = 'Data';
    $dat[2]['title'] = '<div class="fixed-width">' . str_replace(',', ', ', jrCore_entity_string($_rt['log_data'])) . '</div>';
    jrCore_page_table_row($dat);
    jrCore_page_table_footer();
    jrCore_page_close_button();
    jrCore_page_display();
}

//------------------------------
// activity_log_delete
//------------------------------
function view_jrCore_activity_log_delete($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_json_response(array('msg' => 'invalid log id'));
    }
    $tbl = jrCore_db_table_name('jrCore', 'log_debug');
    $req = "DELETE FROM {$tbl} WHERE log_log_id = '{$_post['id']}' LIMIT 1";
    jrCore_db_query($req);

    $tbl = jrCore_db_table_name('jrCore', 'log');
    $req = "DELETE FROM {$tbl} WHERE log_id = '{$_post['id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        jrCore_json_response(array('msg' => 'ok'));
    }
    jrCore_json_response(array('msg' => 'error encountered deleting log - please try again'));
}

//------------------------------
// activity_log_download
//------------------------------
function view_jrCore_activity_log_download($_post, $_user, $_conf)
{
    jrUser_master_only();
    $tbl = jrCore_db_table_name('jrCore', 'log');
    $req = "SELECT * FROM {$tbl} ORDER BY `log_id` ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (isset($_rt[0]) && is_array($_rt[0])) {
        $today = date("Ymd");
        $fn    = "Activity_Log_{$today}.csv";
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=\"{$fn}\"");
        $data = '"ID","Created","Priority","IP","Text"' . "\n";
        foreach ($_rt as $_x) {
            $_x['log_created'] = jrCore_format_time($_x['log_created']);
            $_x['log_text']    = str_replace('"', '', $_x['log_text']);
            $data .= '"' . $_x['log_id'] . '","' . $_x['log_created'] . '","' . $_x['log_priority'] . '","' . $_x['log_ip'] . '","' . $_x['log_text'] . '"' . "\n";
        }
        echo $data;
    }
    else {
        jrCore_notice_page('error', 'No activity logs to download');
    }
}

//------------------------------
// activity_log_delete_all
//------------------------------
function view_jrCore_activity_log_delete_all($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    $tbl = jrCore_db_table_name('jrCore', 'log');
    $req = "TRUNCATE {$tbl}";
    jrCore_db_query($req);

    $tbl = jrCore_db_table_name('jrCore', 'log_debug');
    $req = "TRUNCATE {$tbl}";
    jrCore_db_query($req);
    jrCore_form_result();
}

//------------------------------
// browser (datastore)
//------------------------------
function view_jrCore_browser($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs($_post['module']);

    // start our html output
    jrCore_dashboard_browser('master', $_post, $_user, $_conf);

    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    jrCore_page_display();
}

//------------------------------
// browser_item_update
//------------------------------
function view_jrCore_browser_item_update($_post, $_user, $_conf)
{
    jrUser_admin_only();

    // See if we are an admin or master user...
    $url = jrCore_get_local_referrer();
    if (jrUser_is_master() && !strpos($url, 'dashboard')) {
        jrCore_page_include_admin_menu();
        jrCore_page_admin_tabs($_post['module']);
    }
    else {
        jrCore_page_dashboard_tabs('browser');
    }
    jrCore_page_banner('modify datastore item', "item id: {$_post['id']}");
    jrCore_get_form_notice();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid item id');
        jrCore_form_result('referrer');
    }
    $_rt = jrCore_db_get_item($_post['module'], $_post['id'], SKIP_TRIGGERS);
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'Unable to retrieve item from DataStore - please try again');
    }
    // Go through each field and show it on a form
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => 'referrer'
    );
    jrCore_form_create($_tmp);

    // Item ID
    $_tmp = array(
        'name'     => 'id',
        'type'     => 'hidden',
        'value'    => $_rt['_item_id'],
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    $pfx = jrCore_db_get_prefix($_post['module']);
    ksort($_rt);
    foreach ($_rt as $k => $v) {
        if (strpos($k, $pfx) !== 0) {
            continue;
        }
        switch ($k) {
            case 'user_group':
            case 'user_password':
            case 'user_old_password':
                break;
            default:
                // New Form Field
                if (strlen($v) > 128 || strpos(' ' . $v, "\n")) {
                    $_tmp = array(
                        'name'  => "ds_key_{$k}",
                        'label' => '<span style="text-transform:none">' . $k . '</span>',
                        'type'  => 'textarea',
                        'value' => $v
                    );
                }
                else {
                    $_tmp = array(
                        'name'  => "ds_key_{$k}",
                        'label' => '<span style="text-transform:none">' . $k . '</span>',
                        'type'  => 'text',
                        'value' => $v
                    );
                }
                jrCore_form_field_create($_tmp);
                break;
        }
    }

    // New Field...
    $err = '';
    if (isset($_SESSION['jr_form_field_highlight']['ds_browser_new_key'])) {
        unset($_SESSION['jr_form_field_highlight']['ds_browser_new_key']);
        $err = ' field-hilight';
    }
    $text = '<input type="text" class="form_text' . $err . '" id="ds_browser_new_key" name="ds_browser_new_key" value="">';
    $html = '<input type="text" class="form_text" id="ds_browser_new_value" name="ds_browser_new_value" value="">';
    $_tmp = array(
        'type'     => 'page_link_cell',
        'label'    => $text,
        'url'      => $html,
        'module'   => 'jrCore',
        'template' => 'page_link_cell.tpl'
    );
    jrCore_create_page_element('page', $_tmp);
    jrCore_page_display();
}

//---------------------- -------
// browser_item_update_save
//---------------------- -------
function view_jrCore_browser_item_update_save($_post, $_user, $_conf)
{
    jrUser_admin_only();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid item id');
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item($_post['module'], $_post['id'], SKIP_TRIGGERS);
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'Unable to retrieve item from DataStore - please try again');
        jrCore_form_result();
    }
    $refresh = false;
    $_upd    = array();
    foreach ($_post as $k => $v) {
        if (strpos($k, 'ds_key_') === 0) {
            switch ($k) {
                // Only the Master Admin can change the user_group
                case 'ds_key_user_group':
                    if (!jrUser_is_master()) {
                        continue 2;
                    }
                    break;
                case 'ds_key_user_password':
                    continue 2;
                    break;
            }
            $k = substr($k, 7);
            if (isset($_rt[$k]) && ($_rt[$k] != $v || strlen($v) === 0)) {
                // See if we are removing fields....
                if (strlen($v) === 0) {
                    // Remove field
                    $refresh = true;
                    jrCore_db_delete_item_key($_post['module'], $_post['id'], $k);
                }
                else {
                    $_upd[$k] = $v;
                }
            }
        }
    }

    // Check for new Value..
    if (isset($_post['ds_browser_new_key']{0})) {
        // Make sure it begins with our DS prefix
        $pfx = jrCore_db_get_prefix($_post['module']);
        if (strpos($_post['ds_browser_new_key'], $pfx) !== 0) {
            jrCore_set_form_notice('error', "Invalid new key name - must begin with <strong>{$pfx}_</strong>", false);
            jrCore_form_field_hilight('ds_browser_new_key');
            jrCore_form_result();
        }
        elseif (!jrCore_checktype($_post['ds_browser_new_key'], 'core_string')) {
            $err = jrCore_checktype_core_string(null, true);
            jrCore_set_form_notice('error', "Invalid new key name - must contain {$err} only");
            jrCore_form_field_hilight('ds_browser_new_key');
            jrCore_form_result();
        }
        // Make sure it is NOT a restricted key
        switch ($_post['ds_browser_new_key']) {
            case 'user_group':
            case 'user_password':
                jrCore_set_form_notice('error', "Invalid new key name - {$_post['ds_browser_new_key']} cannot be set using the Data Browser");
                jrCore_form_field_hilight('ds_browser_new_key');
                jrCore_form_result();
                break;
        }
        $_upd["{$_post['ds_browser_new_key']}"] = $_post['ds_browser_new_value'];
        $refresh                                = true;
    }

    if (isset($_upd) && count($_upd) > 0) {
        if (!jrCore_db_update_item($_post['module'], $_post['id'], $_upd)) {
            jrCore_set_form_notice('error', 'An error was encountered saving the updates to the item - please try again');
            jrCore_form_result();
        }
    }
    jrCore_set_form_notice('success', 'The changes were successfully saved');
    if ($refresh) {
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browser_item_update/id={$_post['id']}");
    }
    else {
        jrCore_form_result();
    }
}

//------------------------------
// browser_item_delete
//------------------------------
function view_jrCore_browser_item_delete($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_validate_location_url();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid item id');
        jrCore_form_result('referrer');
    }
    if (!jrCore_db_delete_item($_post['module'], $_post['id'])) {
        jrCore_set_form_notice('error', 'Unable to delete item from DataStore - please try again');
    }
    jrCore_delete_all_cache_entries($_post['module']);
    jrCore_form_result('referrer');
}

//------------------------------
// stream_file
//------------------------------
function view_jrCore_stream_file($_post, $_user, $_conf)
{
    // When a stream request comes in, it will look like:
    // http://www.site.com/song/stream/audio_file/5
    // so we have URL / module / option / _1 / _2
    if (!isset($_post['_2']) || !is_numeric($_post['_2'])) {
        header('HTTP/1.0 404 Not Found');
        jrCore_notice('Error', 'invalid media id');
    }
    // Make sure this is a DataStore module
    if (!jrCore_db_get_prefix($_post['module'])) {
        header('HTTP/1.0 404 Not Found');
        jrCore_notice('Error', 'invalid module - no datastore');
    }
    $key_check = true;
    // ALLOW_ALL_DOMAINS - disables key checking
    if (isset($_conf['jrCore_allowed_domains']) && strpos(' ' . $_conf['jrCore_allowed_domains'], 'ALLOW_ALL_DOMAINS')) {
        $key_check = false;
    }
    elseif (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_conf['jrCore_base_url']) !== 0) {
        if (!jrCore_media_is_allowed_referrer_domain()) {
            header('HTTP/1.0 403 Forbidden');
            jrCore_notice('Error', 'media streams are blocked outside of onsite players');
        }
        $key_check = false;
    }
    // Make sure we have a valid play key
    if ($key_check && !jrUser_is_admin() && (!isset($_post['key']) || !jrCore_media_get_play_key($_post['key']))) {
        header('HTTP/1.0 403 Forbidden');
        jrCore_notice('Error', 'invalid play key');
    }

    // DO NOT CHANGE THIS to jrCore_db_get_item!  This needs to be
    // a db_search_item call so that Parameter Injection works!
    $_rt = array(
        'search'      => array(
            "_item_id = {$_post['_2']}"
        ),
        'quota_check' => false,
        'limit'       => 1
    );
    $_rt = jrCore_db_search_items($_post['module'], $_rt);
    if (!$_rt || !is_array($_rt) || !isset($_rt['_items'])) {
        header('HTTP/1.0 404 Not Found');
        jrCore_notice('Error', 'invalid media id - item not found');
    }
    $_rt = $_rt['_items'][0];
    $fld = $_post['_1'];
    if (strpos($fld, '_mobile')) {
        $fld = str_replace('_mobile', '', $fld);
    }

    // Privacy Checking for this profile
    if (!jrUser_is_admin() && isset($_rt['profile_private']) && $_rt['profile_private'] != '1') {
        // Privacy Check (Sub Select) - non admin users
        // 0 = Private
        // 1 = Global
        // 2 = Shared
        if (!jrProfile_is_profile_owner($_rt['_profile_id'])) {
            if ($_rt['profile_private'] == '0') {
                // We have a private profile and this is not the owner
                header('HTTP/1.0 403 Forbidden');
                header('Connection: close');
                jrCore_notice('Error', 'you do not have permission to stream this file');
                jrCore_db_close();
                exit;
            }
            // We're shared - viewer must be a follower of the profile
            elseif (jrCore_module_is_active('jrFollower')) {
                if (jrFollower_is_follower($_user['_user_id'], $_rt['_profile_id']) === false) {
                    // We are not a follower of this profile - not allowed
                    header('HTTP/1.0 403 Forbidden');
                    header('Connection: close');
                    jrCore_notice('Error', 'you do not have permission to stream this file');
                    jrCore_db_close();
                    exit;
                }
            }
            else {
                // Shared by followers not enabled
                header('HTTP/1.0 403 Forbidden');
                header('Connection: close');
                jrCore_notice('Error', 'you do not have permission to stream this file');
                jrCore_db_close();
                exit;
            }
        }
    }

    // Check that file exists
    $ext = $_rt["{$fld}_extension"];
    if (isset($_post['_3']) && strpos($_post['_3'], '.')) {
        $ext = jrCore_file_extension($_post['_3']);
    }
    $nam = "{$_post['module']}_{$_post['_2']}_{$fld}.{$ext}";
    // See if we have a SAMPLE for streaming - always overrides full stream
    if (isset($_rt["{$fld}_item_price"]) && $_rt["{$fld}_item_price"] > 0 && jrCore_media_file_exists($_rt['_profile_id'], "{$nam}.sample." . $_rt["{$fld}_extension"])) {
        $nam = "{$nam}.sample." . $_rt["{$fld}_extension"];
    }

    $fname = $nam;
    if (isset($_rt["{$fld}_original_name"]) && strlen($_rt["{$fld}_original_name"]) > 0) {
        $fname = $_rt["{$fld}_original_name"];
    }
    elseif (isset($_rt["{$fld}_name"]) && strlen($_rt["{$fld}_name"]) > 0) {
        $fname = $_rt["{$fld}_name"];
    }

    // "stream_file" event trigger
    $_args = array(
        'module'      => $_post['module'],
        'stream_file' => $nam,
        'file_name'   => $fld,
        'item_id'     => $_post['_2']
    );
    $_rt   = jrCore_trigger_event('jrCore', 'stream_file', $_rt, $_args);
    if (!empty($_rt['stream_file']) && $_rt['stream_file'] != $nam) {
        $nam = $_rt['stream_file'];
    }
    if (!empty($_rt['stream_file_name'])) {
        $fname = $_rt['stream_file_name'];
    }

    if (!jrCore_media_file_exists($_rt['_profile_id'], $nam)) {
        header('HTTP/1.0 404 Not Found');
        jrCore_notice('Error', 'invalid media id - no file found');
    }

    // Watch for browser scans
    if (!isset($_rt['skip_stream_count'])) {
        jrCore_counter($_post['module'], $_post['_2'], "{$fld}_stream");
    }

    // Stream the file to the client
    jrCore_media_file_stream($_rt['_profile_id'], $nam, $fname);
    if (isset($_SESSION)) {
        session_write_close();
    }
    jrCore_trigger_event('jrCore', 'process_done', $_post);
    exit;
}

//------------------------------
// download_file
//------------------------------
function view_jrCore_download_file($_post, $_user, $_conf)
{
    // When a download request comes in, it will look like:
    // http://www.site.com/song/download/audio_file/5
    // so we have URL / module / option / _1 / _2
    if (!isset($_post['_2']) || !is_numeric($_post['_2'])) {
        header('HTTP/1.0 404 Not Found');
        jrCore_notice('Error', 'invalid media id');
    }
    // Make sure this is a DataStore module
    if (!$pfx = jrCore_db_get_prefix($_post['module'])) {
        header('HTTP/1.0 404 Not Found');
        jrCore_notice('Error', 'invalid module - no datastore');
    }
    // Make sure referrer is allowed if we get one
    if (!jrCore_media_is_allowed_referrer_domain()) {
        header('HTTP/1.0 403 Forbidden');
        jrCore_notice('Error', 'offsite media downloads are blocked');
    }

    // DO NOT CHANGE THIS to jrCore_db_get_item!  This needs to be
    // a db_search_item call so that Parameter Injection works!
    $_rt = array(
        'search'      => array(
            "_item_id = {$_post['_2']}"
        ),
        'quota_check' => false,
        'limit'       => 1
    );
    $_rt = jrCore_db_search_items($_post['module'], $_rt);
    if (!$_rt || !is_array($_rt) || !isset($_rt['_items'])) {
        header('HTTP/1.0 404 Not Found');
        jrCore_notice('Error', 'invalid media id - no data found');
    }
    $_rt = $_rt['_items'][0];

    if (!isset($_rt["{$_post['_1']}_size"]) || $_rt["{$_post['_1']}_size"] < 1) {
        header('HTTP/1.0 404 Not Found');
        jrCore_notice('Error', 'invalid media id - no media item found');
    }

    // Non admin checks
    if (!jrUser_can_edit_item($_rt)) {

        // Make sure file is NOT for sale
        if (isset($_rt["{$_post['_1']}_item_price"]) && $_rt["{$_post['_1']}_item_price"] > 0) {
            header('HTTP/1.0 403 Forbidden');
            jrCore_notice('Error', 'invalid media item - item must be purchased to be downloaded');
        }

        // Privacy Checking for this profile
        if (isset($_rt['profile_private']) && $_rt['profile_private'] != '1') {
            // Privacy Check (Sub Select) - non admin users
            // 0 = Private
            // 1 = Global
            // 2 = Shared
            if ($_rt['profile_private'] == '0') {
                if (!jrProfile_is_profile_owner($_rt['_profile_id'])) {
                    // We have a private profile and this is not the owner
                    header('HTTP/1.0 403 Forbidden');
                    header('Connection: close');
                    jrCore_notice('Error', 'you do not have permission to download this file');
                    jrCore_db_close();
                    exit;
                }
            }

            // We're shared - viewer must be a follower of the profile
            elseif (jrCore_module_is_active('jrFollower')) {
                if (jrFollower_is_follower($_user['_user_id'], $_rt['_profile_id']) === false) {
                    // We are not a follower of this profile - not allowed
                    header('HTTP/1.0 403 Forbidden');
                    header('Connection: close');
                    jrCore_notice('Error', 'you do not have permission to download this file');
                    jrCore_db_close();
                    exit;
                }
            }
            else {
                // Shared by followers not enabled
                header('HTTP/1.0 403 Forbidden');
                header('Connection: close');
                jrCore_notice('Error', 'you do not have permission to download this file');
                jrCore_db_close();
                exit;
            }
        }
    }

    // Check that file exists
    $nam = "{$_post['module']}_{$_post['_2']}_{$_post['_1']}." . $_rt["{$_post['_1']}_extension"];
    if (!jrCore_media_file_exists($_rt['_profile_id'], $nam)) {
        header('HTTP/1.0 404 Not Found');
        jrCore_notice('Error', 'invalid media id - no file found');
    }

    $fname = $nam;
    if (isset($_rt["{$pfx}_title_url"]) && strlen($_rt["{$pfx}_title_url"]) > 0 && isset($_rt["{$_post['_1']}_extension"]{1})) {
        $fname = str_replace('.' . $_rt["{$_post['_1']}_extension"], '', $_rt["{$pfx}_title_url"]) . '.' . $_rt["{$_post['_1']}_extension"];
    }
    elseif (isset($_rt["{$_post['_1']}_name"]) && strlen($_rt["{$_post['_1']}_name"]) > 0) {
        $fname = $_rt["{$_post['_1']}_name"];
    }
    elseif (isset($_rt["{$_post['_1']}_original_name"]) && strlen($_rt["{$_post['_1']}_original_name"]) > 0) {
        $fname = $_rt["{$_post['_1']}_original_name"];
    }

    // "download_file" event trigger
    $_args = array(
        'module'    => $_post['module'],
        'file_name' => $_post['_1'],
        'item_id'   => $_post['_2']
    );
    $_rt   = jrCore_trigger_event('jrCore', 'download_file', $_rt, $_args);
    if (!empty($_rt['download_file_name'])) {
        // Our listener changed the download file name
        $fname = $_rt['download_file_name'];
    }

    // Increment our counter
    if (!isset($_rt['skip_download_count'])) {
        jrCore_counter($_post['module'], $_post['_2'], "{$_post['_1']}_download");
    }

    // Download the file to the client
    jrCore_media_file_download($_rt['_profile_id'], $nam, $fname);
    if (isset($_SESSION)) {
        session_write_close();
    }
    jrCore_trigger_event('jrCore', 'process_done', $_post);
    exit;
}

//------------------------------
// upload_file
//------------------------------
function view_jrCore_upload_file($_post, $_user, $_conf)
{
    // Upload progress
    jrUser_session_require_login();
    if (!isset($_post['upload_token']) || !jrCore_checktype($_post['upload_token'], 'md5')) {
        jrCore_db_close();
        exit;
    }

    // Bring in meter backend
    require_once APP_DIR . '/modules/jrCore/contrib/meter/server.php';

    // Determine max allowed upload size
    $max = (isset($_user['quota_jrCore_max_upload_size'])) ? intval($_user['quota_jrCore_max_upload_size']) : 2097152;
    if (!isset($max) || $max < 2097152) {
        $max = 2097152;
    }
    $ext = explode(',', $_post['extensions']);
    $mtr = new qqFileUploader($ext, jrCore_get_max_allowed_upload($max));

    $dir = jrCore_get_upload_temp_directory($_post['upload_token']);
    if (!is_dir($dir)) {
        jrCore_create_directory($dir);
    }
    $res = $mtr->handleUpload($dir . '/');
    jrCore_json_response($res, true, true, false);
}

//--------------------------------
// PHP Error Log
//--------------------------------
function view_jrCore_php_error_log($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_page_dashboard_tabs('activity');
    jrCore_master_log_tabs('error');

    $clear = null;
    $out   = "<div class=\"center\"><p>No PHP Errors at this time</p></div>";
    if (is_file(APP_DIR . "/data/logs/error_log")) {
        $_er = file(APP_DIR . "/data/logs/error_log");
        $_nm = array();
        $_ln = array();
        if (isset($_er) && is_array($_er)) {
            $cnt = count($_er);
            $idx = 0;
            while ($cnt > 0) {
                $index = md5(substr($_er[$idx], 27));
                if (!isset($_ln[$index])) {
                    $level       = str_replace(':', '', jrCore_string_field($_er[$idx], 5));
                    $_ln[$index] = "<span class=\"php_{$level}\">" . jrCore_entity_string($_er[$idx]);
                    $_nm[$index] = 1;
                }
                else {
                    $_nm[$index]++;
                }
                unset($_er[$idx]);
                $cnt--;
                $idx++;
            }
            $out = '<div id="error_log"><br>';
            foreach ($_ln as $k => $v) {
                $out .= trim($v) . ' [' . $_nm[$k] . ']</span><br>';
            }
            $out .= '</div>';
            if (jrUser_is_master()) {
                $clear = jrCore_page_button('clear', 'Delete Error Log', "jrCore_confirm('Delete the Error Log?', '', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/php_error_log_delete') } );");
            }
        }
    }
    jrCore_page_banner('Error Log', $clear);
    jrCore_page_custom($out);
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    jrCore_page_display();
}

//------------------------------
// php_error_log_delete
//------------------------------
function view_jrCore_php_error_log_delete($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (is_file(APP_DIR . "/data/logs/error_log")) {
        unlink(APP_DIR . "/data/logs/error_log");  // OK
    }
    jrCore_location('referrer');
}

//--------------------------------
// Debug Log
//--------------------------------
function view_jrCore_debug_log($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_page_dashboard_tabs('activity');
    jrCore_master_log_tabs('debug');

    $max_size = 1;  // in megabytes
    $clear    = null;
    $out      = "<div class=\"center\"><p>No Debug Log entries at this time</p></div>";
    if (is_file(APP_DIR . "/data/logs/debug_log")) {
        // How big is our debug log?
        $out = '<div id="debug_log">';
        if (filesize(APP_DIR . "/data/logs/debug_log") > ($max_size * 1048576)) {
            // we're only going to grab the first 2mb
            if ($f = fopen(APP_DIR . "/data/logs/debug_log", 'rb')) {
                $out .= jrCore_entity_string(fread($f, ($max_size * 1048576)));
                fclose($f);
                $out .= "\n\n(file has been truncated to fit here - to see full file download it via FTP)";
            }
        }
        else {
            $out .= jrCore_entity_string(file_get_contents(APP_DIR . "/data/logs/debug_log"));
        }
        $out .= '</div>';
        if (jrUser_is_master()) {
            $clear = jrCore_page_button('clear', 'Delete Debug Log', "jrCore_confirm('Delete the Debug Log?', '', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/debug_log_delete') } );");
        }
    }
    jrCore_page_banner('Debug Log', $clear);
    jrCore_page_custom($out);
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    jrCore_page_display();
}

//------------------------------
// debug_log_delete
//------------------------------
function view_jrCore_debug_log_delete($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (is_file(APP_DIR . "/data/logs/debug_log")) {
        unlink(APP_DIR . "/data/logs/debug_log");  // OK
    }
    jrCore_location('referrer');
}

//------------------------------
// pending_item_approve
//------------------------------
function view_jrCore_pending_item_approve($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_admin_only();
    jrCore_validate_location_url();
    if (!isset($_post['id']) || strlen($_post['id']) === 0) {
        jrCore_set_form_notice('error', 'Invalid item id');
        jrCore_location('referrer');
    }

    // See if we are doing ONE or multiple
    if (isset($_post['id']) && jrCore_checktype($_post['id'], 'number_nz')) {

        // We are only doing ONE item - this will be the _item_id for the ITEM
        $_todo  = array($_post['id']);
        $title  = 'item has';
        $single = true;
    }
    else {

        // We are doing MULTIPLE - this will be the pending_id from the pending table!
        $_todo  = explode(',', $_post['id']);
        $title  = 'items have';
        $single = false;
    }

    $_lg = array();
    $_nt = array();
    $tbl = jrCore_db_table_name('jrCore', 'pending');
    foreach ($_todo as $pid) {

        if (!jrCore_checktype($pid, 'number_nz')) {
            continue;
        }

        $pid = (int) $pid;
        if ($single) {
            $req = "SELECT * FROM {$tbl} WHERE pending_module = '" . jrCore_db_escape($_post['_1']) . "' AND pending_item_id = '{$pid}' LIMIT 1";
        }
        else {
            $req = "SELECT * FROM {$tbl} WHERE pending_id = '{$pid}'";
        }
        $_rt = jrCore_db_query($req, 'SINGLE');
        if (!$_rt || !is_array($_rt)) {
            jrCore_set_form_notice('error', 'Invalid pending id');
            jrCore_location('referrer');
        }

        // approve this item and remove the pending
        $pfx = jrCore_db_get_prefix($_rt['pending_module']);
        $_dt = array("{$pfx}_pending" => '0');
        jrCore_db_update_item($_rt['pending_module'], $_rt['pending_item_id'], $_dt);

        // Remove any rejected reasons
        jrCore_db_delete_item_key($_rt['pending_module'], $_rt['pending_item_id'], "{$pfx}_pending_reason");

        // Trigger approve pending event
        $_args = array(
            '_item_id' => $_rt['pending_item_id'],
            'module'   => $_rt['pending_module']
        );
        jrCore_trigger_event('jrCore', 'approve_pending_item', $_dt, $_args);

        // Notify user that pending item has been approved
        $_dt = json_decode($_rt['pending_data'], true);
        if ($_dt && isset($_dt['item']) && isset($_dt['item']['_user_id']) && $_dt['item']['_user_id'] != $_user['_user_id']) {
            $uid = (int) $_dt['item']['_user_id'];
            if (!isset($_nt[$uid])) {
                $_nt[$uid] = array(
                    '_items' => array()
                );
            }
            $_dt['item']['item_module_url']  = "{$_conf['jrCore_base_url']}/{$_dt['user']['profile_url']}/" . jrCore_get_module_url($_rt['pending_module']);
            $_dt['item']['item_module_name'] = $_mods["{$_rt['pending_module']}"]['module_name'];
            $_dt['item']['item_title']       = $_dt['item']["{$pfx}_title"];
            $_nt[$uid]['_items'][]           = $_dt['item'];
        }

        // Cleanup pending entry
        $req = "DELETE FROM {$tbl} WHERE pending_id = {$_rt['pending_id']}";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (!$cnt || $cnt !== 1) {
            jrCore_set_form_notice('error', "unable to delete pending entry for {$_rt['pending_module']}, item_id: {$_rt['pending_item_id']}");
            jrCore_location('referrer');
        }

        // Next, let's see if there is an associated ACTION that was created for this item - of so, we want to approve it as well.
        $req = "SELECT * FROM {$tbl} WHERE pending_linked_item_module = '" . jrCore_db_escape($_rt['pending_module']) . "' AND pending_linked_item_id = '" . intval($_rt['pending_item_id']) . "'";
        $_pa = jrCore_db_query($req, 'SINGLE');
        if ($_pa && is_array($_pa)) {

            // We've found a linked action - approve
            $pf2 = jrCore_db_get_prefix('jrAction');
            $_du = array("{$pf2}_pending" => '0');
            jrCore_db_update_item('jrAction', $_pa['pending_item_id'], $_du);

            // And remove the pending item
            $req = "DELETE FROM {$tbl} WHERE pending_id = {$_pa['pending_id']}";
            jrCore_db_query($req);
        }

        if ($single) {
            jrCore_logger('INF', "pending item " . $_mods["{$_rt['pending_module']}"]['module_name'] .": " . $_dt['item']["{$pfx}_title"] . " has been approved");
        }
        else {
            $_lg[] = $_mods["{$_rt['pending_module']}"]['module_name'] . ': ' . $_dt['item']["{$pfx}_title"];
        }
    }

    if (!$single) {
        jrCore_logger('INF', "The selected pending items have been approved", $_lg);
    }

    // Do we have users to notify?
    if (count($_nt) > 0) {
        foreach ($_nt as $uid => $_items) {
            list($sub, $msg) = jrCore_parse_email_templates('jrCore', 'pending_approve', $_items);
            jrUser_notify($uid, 0, 'jrCore', 'pending_approve', $sub, $msg);
        }
    }

    jrCore_set_form_notice('success', "The pending {$title} been approved");
    jrCore_location('referrer');
}

//------------------------------
// pending_item_reject
//------------------------------
function view_jrCore_pending_item_reject($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_admin_only();

    if (!isset($_post['_1']) || !isset($_mods["{$_post['_1']}"])) {
        jrCore_set_form_notice('error', 'Invalid module');
        jrCore_location('referrer');
    }
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid item id');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrCore', 'pending');
    $req = "SELECT * FROM {$tbl} WHERE pending_module = '" . jrCore_db_escape($_post['_1']) . "' AND pending_item_id = '" . intval($_post['id']) . "' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'Invalid pending id');
        jrCore_location('referrer');
    }
    // Get item
    $_it = jrCore_db_get_item($_rt['pending_module'], $_rt['pending_item_id']);
    if (!$_it || !is_array($_it)) {
        jrCore_set_form_notice('error', 'Invalid item - unable to retrieve data from DataStore');
        jrCore_form_result();
    }

    // Show our tabs if we are from the dashboard
    $url = jrCore_get_local_referrer();
    if (strpos($url, 'dashboard') || strpos($url, 'pending')) {
        jrCore_page_dashboard_tabs('pending');
    }

    // Show reject notice page
    jrCore_page_banner('reject item');
    $pfx = jrCore_db_get_prefix($_rt['pending_module']);
    $ttl = "{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$url}/{$_it['_item_id']}";
    $seo = '';
    if (isset($_it["{$pfx}_title_url"])) {
        $seo = '/' . $_it["{$pfx}_title_url"];
        $ttl = $_it["{$pfx}_title"];
    }
    $url = jrCore_get_module_url($_rt['pending_module']);

    // Form init
    $_tmp = array(
        'submit_value' => 'reject item and notify user',
        'cancel'       => 'referrer'
    );
    jrCore_form_create($_tmp);

    // Module
    $_tmp = array(
        'name'  => 'pending_id',
        'type'  => 'hidden',
        'value' => $_rt['pending_id']
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_custom("&nbsp;<a href=\"{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$url}/{$_it['_item_id']}{$seo}\"><u>{$ttl}</u></a>", 'rejected item URL');

    // Create an item list of our custom "quick reject" options
    $lbl = 'reject reason';
    $tbl = jrCore_db_table_name('jrCore', 'pending_reason');
    $req = "SELECT * FROM {$tbl} ORDER BY reason_text ASC";
    $_pr = jrCore_db_query($req, 'reason_key', false, 'reason_text');
    if ($_pr && is_array($_pr)) {
        // Add in our delete button
        $_att = array('class' => 'rejected_reason_delete');
        foreach ($_pr as $k => $v) {
            $_pr[$k] = jrCore_page_button("d{$k}", 'X', "jrCore_confirm('delete this reason?', '', function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/pending_reason_delete/key={$k}')} )", $_att) . '&nbsp' . $v;
        }
        $_tmp = array(
            'name'     => 'reject_reason',
            'label'    => 'reject reason(s)',
            'sublabel' => 'select all that apply',
            'help'     => 'Select predefined reasons for rejecting this item',
            'type'     => 'optionlist',
            'validate' => 'hex',
            'options'  => $_pr,
            'required' => false
        );
        jrCore_form_field_create($_tmp);
        $lbl = 'new reject reason';
    }

    $_tmp = array(
        'name'     => 'new_reject_reason',
        'label'    => $lbl,
        'help'     => 'Enter a NEW reject reason here and it will be saved for use on future rejection notices.',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'reject_message',
        'label'    => 'reject message',
        'sublabel' => '(optional)',
        'help'     => 'Enter a custom message to send to the profile owner(s) that explains why this item has been rejected.',
        'type'     => 'textarea',
        'validate' => 'printable',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'reject_delete',
        'label'    => 'delete item',
        'help'     => 'Check this option to delete this item after sending the rejection email',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => false
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// pending_item_reject_save
//------------------------------
function view_jrCore_pending_item_reject_save($_post, $_user, $_conf)
{
    jrUser_admin_only();
    if (!isset($_post['pending_id']) || !jrCore_checktype($_post['pending_id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid pending_id');
        jrCore_form_result();
    }
    $pid = (int) $_post['pending_id'];
    $tbl = jrCore_db_table_name('jrCore', 'pending');
    $req = "SELECT * FROM {$tbl} WHERE pending_id = '{$pid}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'Invalid pending_id');
        jrCore_form_result();
    }
    // Get item
    $_it = jrCore_db_get_item($_rt['pending_module'], $_rt['pending_item_id']);
    if (!$_it || !is_array($_it)) {
        jrCore_set_form_notice('error', 'Invalid item - unable to retrieve data from DataStore');
        jrCore_form_result();
    }

    // Save any new reject message
    $_rs = array();
    if (isset($_post['new_reject_reason']) && strlen($_post['new_reject_reason']) > 0) {
        $tb2 = jrCore_db_table_name('jrCore', 'pending_reason');
        $req = "INSERT INTO {$tb2} (reason_key,reason_text) VALUES ('" . md5($_post['new_reject_reason']) . "','" . jrCore_db_escape($_post['new_reject_reason']) . "')";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (!$cnt || $cnt !== 1) {
            jrCore_set_form_notice('error', 'Unable to store new pending reason - please try again');
            jrCore_form_result();
        }
        $_rs[] = strip_tags($_post['new_reject_reason']);
    }


    // See if we received any canned rejection notices
    $tb2 = jrCore_db_table_name('jrCore', 'pending_reason');
    $req = "SELECT * FROM {$tb2}";
    $_pr = jrCore_db_query($req, 'reason_key', false, 'reason_text');
    foreach ($_post as $k => $v) {
        if (strpos($k, 'reject_reason_') === 0 && $v == 'on') {
            $key = substr($k, 14);
            if (isset($_pr[$key])) {
                $_rs[] = $_pr[$key];
            }
        }
    }
    $reason = implode("\n", $_rs);

    // Trigger reject pending event
    $_args = array(
        '_item_id' => $_rt['pending_item_id'],
        'module'   => $_rt['pending_module']
    );
    jrCore_trigger_event('jrCore', 'reject_pending_item', $_it, $_args);

    // Update item with rejected info
    $pfx = jrCore_db_get_prefix($_rt['pending_module']);
    $_up = array(
        "{$pfx}_pending"        => 2,
        "{$pfx}_pending_reason" => $reason
    );
    if (isset($_post['reject_message']) && strlen($_post['reject_message']) > 1) {
        $_up["{$pfx}_pending_reason"] .= "\n{$_post['reject_message']}";
    }
    jrCore_db_update_item($_rt['pending_module'], $_rt['pending_item_id'], $_up);

    // [pending_id] => 17
    // [reject_reason_d86c579c827fec297d69e58e4c06cfa2] => on
    // [reject_reason_e37bbbb8065ecdc1d34cf3e98f37e8a3] => on
    // [new_reject_reason] => NEW REASON
    // [reject_message] => MESSAGE

    // Send Reject email
    if (isset($_it['_profile_id']) && jrCore_checktype($_it['_profile_id'], 'number_nz')) {

        switch ($_rt['pending_module']) {
            // this is an ACTION it is deleted (since they cannot be edited)
            case 'jrAction':
                $_post['reject_delete'] = 'on';
                $email_template         = 'pending_reject_deleted';
                break;
            default:
                if (isset($_post['reject_delete']) && $_post['reject_delete'] == 'on') {
                    $email_template = 'pending_reject_deleted';
                }
                else {
                    $email_template = 'pending_reject';
                }
                break;
        }
        $url = jrCore_get_module_url($_rt['pending_module']);
        $pfx = jrCore_db_get_prefix($_rt['pending_module']);
        $ttl = '';
        if (isset($_it["{$pfx}_title_url"])) {
            $ttl = "/" . $_it["{$pfx}_title_url"];
        }
        $_rp = array(
            'system_name'    => $_conf['jrCore_system_name'],
            'reject_reason'  => $reason,
            'reject_message' => strip_tags($_post['reject_message']),
            'reject_url'     => "{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$url}/{$_it['_item_id']}{$ttl}"
        );

        $_em = jrProfile_get_owner_email($_it['_profile_id']);
        if ($_em && is_array($_em)) {
            list($sub, $msg) = jrCore_parse_email_templates('jrCore', $email_template, $_rp);
            foreach ($_em as $uid => $email) {
                jrCore_send_email($email, $sub, $msg);
            }
        }
    }

    // Cleanup pending entry
    $req = "DELETE FROM {$tbl} WHERE pending_id = '{$_rt['pending_id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (!$cnt || $cnt !== 1) {
        jrCore_set_form_notice('error', "unable to delete pending entry for {$_rt['pending_module']} item_id {$_rt['pending_item_id']}");
        jrCore_form_result();
    }

    // Delete item if needed
    if (isset($_post['reject_delete']) && $_post['reject_delete'] == 'on') {
        jrCore_db_delete_item($_rt['pending_module'], $_rt['pending_item_id']);
    }
    jrCore_form_delete_session();

    // Refresh
    $url = jrCore_get_module_url($_rt['pending_module']);
    if (isset($reason) && isset($_rp)) {
        unset($_rp['system_name']);
        jrCore_logger('INF', "{$_it['profile_url']}/{$url}/{$_it['_item_id']} has been rejected: {$reason}", $_rp);
    }
    else {
        jrCore_logger('INF', "{$_it['profile_url']}/{$url}/{$_it['_item_id']} has been rejected");
    }
    jrCore_set_form_notice('success', 'The item was successfully rejected');
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard/pending/m={$_rt['pending_module']}");
}

//------------------------------
// pending_item_delete
//------------------------------
function view_jrCore_pending_item_delete($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_validate_location_url();

    if (!isset($_post['id']) || strlen($_post['id']) === 0) {
        jrCore_set_form_notice('error', 'Invalid item id');
        jrCore_location('referrer');
    }
    // See if we are doing ONE or multiple
    if (isset($_post['id']) && jrCore_checktype($_post['id'], 'number_nz')) {

        // We are only doing ONE item - this will be the _item_if for the ITEM
        $_todo  = array($_post['id']);
        $title  = 'item has';
        $single = true;
    }
    else {

        // We are doing MULTIPLE - this will be the pending_id from the pending table!
        $_todo  = explode(',', $_post['id']);
        $title  = 'items have';
        $single = false;
    }

    $tbl = jrCore_db_table_name('jrCore', 'pending');
    foreach ($_todo as $pid) {
        if (!jrCore_checktype($pid, 'number_nz')) {
            continue;
        }
        $pid = (int) $pid;
        if ($single) {
            $req = "SELECT * FROM {$tbl} WHERE pending_module = '" . jrCore_db_escape($_post['_1']) . "' AND pending_item_id = '{$pid}' LIMIT 1";
        }
        else {
            $req = "SELECT * FROM {$tbl} WHERE pending_id = '{$pid}'";
        }
        $_rt = jrCore_db_query($req, 'SINGLE');
        if (!$_rt || !is_array($_rt)) {
            jrCore_set_form_notice('error', 'Invalid pending id');
            jrCore_location('referrer');
        }

        // delete this item
        if (jrCore_db_delete_item($_rt['pending_module'], $_rt['pending_item_id'])) {

            // NOTE: No need to delete pending entry from the pending table
            // this is done automatically by the core in jrCore_db_delete_item()

            // Next, let's see if there is an associated ACTION that was created for
            // this item - of so, we want to remove it as well.
            $req = "SELECT * FROM {$tbl} WHERE pending_linked_item_module = '" . jrCore_db_escape($_rt['pending_module']) . "' AND pending_linked_item_id = '" . intval($_rt['pending_item_id']) . "'";
            $_pa = jrCore_db_query($req, 'SINGLE');
            if ($_pa && is_array($_pa)) {

                // We've found a linked action - delete
                if (!jrCore_db_delete_item('jrAction', $_pa['pending_item_id'], false, true)) {
                    jrCore_logger('CRI', "unable to delete pending entry for {$_rt['pending_module']} item_id {$_rt['pending_item_id']}");
                }

            }
            $pfx = jrCore_db_get_prefix($_rt['pending_module']);
            jrCore_logger('INF', "pending item id {$pfx}/{$_rt['pending_item_id']} has been deleted");

        }
    }
    // See if we are deleting from a media item's page or the dashboard
    $url = jrCore_get_local_referrer();
    if (strpos($url, 'dashboard')) {
        jrCore_set_form_notice('success', "The pending {$title} been deleted");
    }
    // We're coming in from an individual item's page.
    jrCore_location('referrer');
}

//------------------------------
// pending_reason_delete
//------------------------------
function view_jrCore_pending_reason_delete($_post, $_user, $_conf)
{
    jrUser_admin_only();
    jrCore_validate_location_url();

    if (!isset($_post['key']) || !jrCore_checktype($_post['key'], 'md5')) {
        jrCore_set_form_notice('error', 'Invalid pending reason key');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrCore', 'pending_reason');
    $req = "DELETE FROM {$tbl} WHERE reason_key = '" . jrCore_db_escape($_post['key']) . "' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (!$cnt || $cnt !== 1) {
        jrCore_set_form_notice('error', 'unable to delete pending reason from database - please try again');
    }
    jrCore_location('referrer');
}

/**
 * Set display order for items on a profile
 * @param $_post array Global $_post
 * @param $_user array Viewing user array
 * @param $_conf array Global config
 * @return bool
 */
function view_jrCore_item_display_order($_post, $_user, $_conf)
{
    jrUser_session_require_login();

    // Make sure this module has registered for item_order
    $_md = jrCore_get_registered_module_features('jrCore', 'item_order_support');
    if (!isset($_md["{$_post['module']}"])) {
        jrCore_notice_page('error', 'Invalid module - module is not registered for item_order support');
        return false;
    }

    // See if this is a DS module
    $pfx = jrCore_db_get_prefix($_post['module']);
    if ($pfx) {
        // Get all items of this type
        $_sc = array(
            'search'         => array("_profile_id = {$_user['user_active_profile_id']}"),
            'return_keys'    => array('_item_id', "{$pfx}_title"),
            'order_by'       => array("{$pfx}_display_order" => 'numerical_asc'),
            'skip_triggers'  => true,
            'privacy_check'  => false,
            'ignore_pending' => true,
            'limit'          => 500
        );
        $_rt = jrCore_db_search_items($_post['module'], $_sc);
        if (!isset($_rt['_items']) || !is_array($_rt['_items'])) {
            jrCore_notice_page('notice', 'There are no items to set the order for!');
            return false;
        }
    }
    else {
        // See if we have been given a custom function to get items
        if (isset($_md["{$_post['module']}"]['on']) && function_exists($_md["{$_post['module']}"]['on'])) {
            $fnc = $_md["{$_post['module']}"]['on'];
            $_rt = $fnc($_post);
            if (!isset($_rt['_items']) || !is_array($_rt['_items'])) {
                jrCore_notice_page('notice', 'There are no items to set the order for!');
                return false;
            }
        }
        else {
            jrCore_notice_page('error', 'Invalid module - module does not use a DataStore');
            return false;
        }
    }
    $_ln = jrUser_load_lang_strings();
    $btn = jrCore_page_button('c', $_ln['jrCore'][87], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}')");
    jrCore_page_banner($_ln['jrCore'][83], $btn);

    // Let modules inspect our display order items
    $_rt = jrCore_trigger_event('jrCore', 'display_order', $_rt);

    $tmp = '<ul class="item_sortable list">';
    foreach ($_rt['_items'] as $_item) {
        if (isset($_item['data-id'])) {
            // This module is handling it's own id's and titles
            $tmp .= "<li data-id=\"{$_item['data-id']}\">" . $_item['title'] . "</li>\n";
        }
        else {
            $tmp .= "<li data-id=\"{$_item['_item_id']}\">" . $_item["{$pfx}_title"] . "</li>\n";
        }
    }
    $tmp .= '</ul>';
    jrCore_page_custom($tmp, $_ln['jrCore'][83], $_ln['jrCore'][85]);

    if ($pfx) {
        $url = "{$_conf['jrCore_base_url']}/" . jrCore_get_module_url('jrCore') . "/item_display_order_update/m={$_post['module']}/__ajax=1";
    }
    else {
        $url = "{$_conf['jrCore_base_url']}/" . jrCore_get_module_url($_post['module']) . "/item_display_order_update/__ajax=1";
    }
    $tmp = array('$(function() {
           $(\'.item_sortable\').sortable().bind(\'sortupdate\', function(event,ui) {
               var o = $(\'ul.item_sortable li\').map(function(){ return $(this).data("id"); }).get();
               $.post(\'' . $url . '\', { iid: o });
           });
       });');
    jrCore_create_page_element('javascript_footer_function', $tmp);
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}", $_ln['jrCore'][87]);
    return jrCore_page_display(true);
}

/**
 * Update item order in Datastore
 * @param $_post array Global $_post
 * @param $_user array Viewing user array
 * @param $_conf array Global config
 * @return bool
 */
function view_jrCore_item_display_order_update($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['m']) || !jrCore_module_is_active($_post['m'])) {
        return jrCore_json_response(array('error', 'Invalid module'));
    }
    // Make sure the requested module has a registered DS
    $pfx = jrCore_db_get_prefix($_post['m']);
    if (!$pfx) {
        return jrCore_json_response(array('error', 'Invalid module - module does not use a DataStore'));
    }
    // Make sure this module has registered for item_order
    $_md = jrCore_get_registered_module_features('jrCore', 'item_order_support');
    if (!isset($_md["{$_post['m']}"])) {
        return jrCore_json_response(array('error', 'Invalid module - module is not registered for item_order support'));
    }

    // Get our items that are being re-ordered and make sure
    // the calling user has access to them
    if (!jrUser_is_admin()) {
        $_rt = jrCore_db_get_multiple_items($_post['m'], $_post['iid']);
        if (!isset($_rt) || !is_array($_rt)) {
            return jrCore_json_response(array('error', 'unable to retrieve item entries from DataStore'));
        }
        foreach ($_rt as $_v) {
            if (!jrUser_can_edit_item($_v)) {
                return jrCore_json_response(array('error', 'permission denied'));
            }
        }
    }

    // Looks good - set item order
    $_ids = array();
    foreach ($_post['iid'] as $ord => $iid) {
        $_ids[$iid] = $ord;
    }
    jrCore_db_set_display_order($_post['m'], $_ids);

    $_ln = jrUser_load_lang_strings();
    jrProfile_reset_cache();
    return jrCore_json_response(array('success' => $_ln['jrCore'][86]));
}

/**
 * Update Item Action buttons for an index|list|detail
 * @param $_post array Global $_post
 * @param $_user array Viewing user array
 * @param $_conf array Global config
 */
function view_jrCore_item_action_buttons($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();

    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        jrCore_notice_page('error', 'Invalid button type');
    }
    if (!isset($_post['m']) || !jrCore_module_is_active($_post['m'])) {
        jrCore_notice_page('error', 'Invalid module');
    }
    $type = false;
    $key  = false;
    switch ($_post['_1']) {
        case 'index':
        case 'list':
        case 'detail':
        case 'bundle_index':
        case 'bundle_list':
        case 'bundle_detail':
            $type = $_post['_1'];
            $key  = "{$_post['m']}_item_{$type}_buttons";
            break;
        default:
            jrCore_notice_page('error', 'Invalid button type');
            break;
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore', 'tools');
    jrCore_page_banner($_mods["{$_post['m']}"]['module_name'] . " - " . str_replace('_', ' ', $type) . " buttons");
    jrCore_get_form_notice();

    // Get all registered features
    $_rf = jrCore_get_registered_module_features('jrCore', "item_{$type}_button");
    if (!$_rf || !is_array($_rf)) {
        jrCore_notice_page('notice', 'There are no modules in the system that provide Item Action Buttons');
    }
    $_rs = array();
    foreach ($_rf as $bmod => $_ft) {
        foreach ($_ft as $func => $_inf) {
            $_inf['module']   = $bmod;
            $_inf['function'] = $func;
            $_rs[]            = $_inf;
        }
    }

    // The admin can:
    // set a specific button to not show
    // set the ORDER the buttons appear in (left to right)
    // Our config holds the info, ordered and by function => on|off
    if (isset($_conf[$key]{1})) {
        // admin has configured
        $_ord = json_decode($_conf[$key], true);
        // "new" modules may not be present in the order until the admin actually
        // re-orders things, so let's add any extra in at the end.
        if ($_ord && is_array($_ord)) {
            foreach ($_rs as $_dat) {
                $found = false;
                foreach ($_ord as $_inf) {
                    if ($_inf['function'] == $_dat['function']) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $_ord[] = $_dat;
                }
            }
        }
        else {
            $_ord = $_rs;
        }
    }
    else {
        $_ord = $_rs;
    }

    // Let modules exclude buttons by trigger
    $_ex = array();
    $_ex = jrCore_trigger_event('jrCore', "exclude_item_{$type}_buttons", $_ex, $_ord, $_post['m']);

    // See if they are active for this view
    foreach ($_ord as $k => $_inf) {
        if (isset($_inf['function'])) {
            $func = $_inf['function'];
            if (function_exists($func)) {
                if (isset($_ex[$func]) || $func($_post['m'], false, false, false, true) === false) {
                    unset($_ord[$k]);
                }
            }
        }
    }
    $_ord = array_values($_ord);

    $dat             = array();
    $dat[1]['title'] = 'order';
    $dat[1]['width'] = '3%';
    $dat[2]['title'] = 'icon';
    $dat[2]['width'] = '3%';
    $dat[3]['title'] = 'module';
    $dat[3]['width'] = '27%';
    $dat[4]['title'] = 'button name';
    $dat[4]['width'] = '27%';
    $dat[5]['title'] = 'group';
    $dat[5]['width'] = '10%';
    $dat[6]['title'] = 'quota(s)';
    $dat[6]['width'] = '20%';
    $dat[7]['title'] = 'active';
    $dat[7]['width'] = '5%';
    $dat[8]['title'] = 'modify';
    $dat[8]['width'] = '5%';
    jrCore_page_table_header($dat);

    if (count($_ord) > 0) {
        $enabled  = jrCore_get_option_image('pass');
        $disabled = jrCore_get_option_image('fail');
        foreach ($_ord as $cnt => $_inf) {
            if (!jrCore_module_is_active($_inf['module'])) {
                continue;
            }
            $dat = array();
            if (!isset($first)) {
                $dat[1]['title'] = '';
                $first           = true;
            }
            else {
                $dat[1]['title'] = jrCore_page_button("f{$cnt}", '&#8679;', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/item_action_button_order/t={$type}/m={$_post['m']}/o={$cnt}')");
            }
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = (isset($_inf['icon'])) ? jrCore_get_icon_html($_inf['icon']) : '';
            $dat[3]['title'] = $_mods["{$_inf['module']}"]['module_name'];
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = ucwords($_inf['title']);
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = (isset($_inf['group'])) ? $_inf['group'] : '';
            $dat[5]['class'] = 'center';
            if (isset($_inf['quota']) && strlen(trim($_inf['quota'])) > 0) {
                $_q = array();
                if (!isset($_qt)) {
                    $_qt = jrProfile_get_quotas();
                }
                foreach (explode(',', $_inf['quota']) as $qid) {
                    if (jrCore_checktype($qid, 'number_nz')) {
                        $_q[] = $_qt[$qid];
                    }
                }
                $dat[6]['title'] = implode('<br>', $_q);
            }
            else {
                $dat[6]['title'] = '-';
            }
            $dat[6]['class'] = 'center';
            $dat[7]['title'] = (isset($_inf['active']) && $_inf['active'] == 'off') ? $disabled : $enabled;
            $dat[7]['class'] = 'center';
            $dat[8]['title'] = jrCore_page_button("m{$cnt}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/item_action_button_modify/t={$type}/m={$_post['m']}/o={$cnt}')");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_footer();
    }
    jrCore_page_cancel_button(jrCore_is_profile_referrer(), 'continue');
    jrCore_page_display();
}

//------------------------------
// item_action_button_modify
//------------------------------
function view_jrCore_item_action_button_modify($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['t']) || strlen($_post['t']) === 0) {
        jrCore_set_form_notice('error', 'Invalid button type');
        jrCore_location('referrer');
    }
    $type = '';
    switch ($_post['t']) {
        case 'index':
        case 'list':
        case 'detail':
            $type = $_post['t'];
            break;
        default:
            jrCore_set_form_notice('error', 'Invalid button type');
            jrCore_location('referrer');
            break;
    }
    if (!isset($_post['m']) || !jrCore_module_is_active($_post['m'])) {
        jrCore_set_form_notice('error', 'invalid module');
        jrCore_location('referrer');
    }
    $mod = $_post['m'];
    if (!isset($_post['o']) || !jrCore_checktype($_post['o'], 'number_nn')) {
        jrCore_set_form_notice('error', 'invalid button offset');
        jrCore_location('referrer');
    }
    $idx = (int) $_post['o'];

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCore', 'tools');
    jrCore_page_banner("modify item {$type} button");
    jrCore_get_form_notice();

    // See if we have customized this button
    $opt = "{$mod}_item_{$type}_buttons";
    if (isset($_conf[$opt])) {
        $_rs = json_decode($_conf[$opt], true);
        if (is_array($_rs)) {
            $_dn = array();
            foreach ($_rs as $_ab) {
                $_dn["{$_ab['function']}"] = 1;
            }
            // We need to go through and see if any new action buttons have been added
            $_rf = jrCore_get_registered_module_features('jrCore', "item_{$type}_button");
            if ($_rf && is_array($_rf)) {
                foreach ($_rf as $bmod => $_ft) {
                    foreach ($_ft as $func => $_inf) {
                        if (!isset($_dn[$func])) {
                            $_inf['module']   = $bmod;
                            $_inf['function'] = $func;
                            $_rs[]            = $_inf;
                        }
                    }
                }
            }
        }
    }
    else {
        // Get our existing (default) order
        $_rs = array();
        $_rf = jrCore_get_registered_module_features('jrCore', "item_{$type}_button");
        if ($_rf && is_array($_rf)) {
            foreach ($_rf as $bmod => $_ft) {
                foreach ($_ft as $func => $_inf) {
                    $_inf['module']   = $bmod;
                    $_inf['function'] = $func;
                    $_rs[]            = $_inf;
                }
            }
        }
        else {
            jrCore_set_form_notice('error', "no registered {$type} buttons found");
            jrCore_location('referrer');
        }
        if (!isset($_rs[$idx])) {
            jrCore_set_form_notice('error', "invalid button offset");
            jrCore_location('referrer');
        }
    }

    // See if they are active for this view
    foreach ($_rs as $k => $_inf) {
        if (isset($_inf['function'])) {
            $func = $_inf['function'];
            if (function_exists($func)) {
                if (!$func($mod, false, false, false, true)) {
                    unset($_rs[$k]);
                }
            }
        }
    }
    $_rs = array_values($_rs);
    $_rt = $_rs[$idx];

    $dat             = array();
    $dat[1]['title'] = 'icon';
    $dat[1]['width'] = '2%';
    $dat[2]['title'] = 'provider';
    $dat[2]['width'] = '49%';
    $dat[3]['title'] = 'button name';
    $dat[3]['width'] = '49%';
    jrCore_page_table_header($dat);

    $dat             = array();
    $dat[1]['title'] = (isset($_rt['icon'])) ? jrCore_get_icon_html($_rt['icon']) : '';
    $dat[2]['title'] = $_mods["{$_rt['module']}"]['module_name'];
    $dat[2]['class'] = 'center';
    $dat[3]['title'] = $_rt['title'];
    $dat[3]['class'] = 'center';
    jrCore_page_table_row($dat);
    jrCore_page_table_footer();

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => 'referrer',
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // module
    $_tmp = array(
        'name'  => 'm',
        'type'  => 'hidden',
        'value' => $mod
    );
    jrCore_form_field_create($_tmp);

    // type
    $_tmp = array(
        'name'  => 't',
        'type'  => 'hidden',
        'value' => $type
    );
    jrCore_form_field_create($_tmp);

    // offset
    $_tmp = array(
        'name'  => 'o',
        'type'  => 'hidden',
        'value' => $idx
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'active',
        'label'    => 'active',
        'help'     => 'Uncheck this option to disable this button from showing up in this location',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_opt = array(
        '0'       => '(no group restrictions)',
        'owner'   => 'profile owners',
        'master'  => 'master admins',
        'admin'   => 'profile admins',
        'power'   => 'power users',
        'multi'   => 'multi profile users',
        'user'    => 'logged in users',
        'visitor' => 'logged out users'
    );
    $_tmp = array(
        'name'     => 'group',
        'label'    => 'group',
        'sublabel' => '(required)',
        'help'     => 'Select the group you would like this button to visible to',
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'core_string',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'quota',
        'label'    => 'quotas',
        'sublabel' => '(optional)',
        'help'     => 'Select the group you would like this button to visible to',
        'type'     => 'optionlist',
        'options'  => 'jrProfile_get_quotas',
        'default'  => '',
        'validate' => 'number_nz',
        'required' => false
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// item_action_button_order
//------------------------------
function view_jrCore_item_action_button_modify_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    if (!isset($_post['t']) || strlen($_post['t']) === 0) {
        jrCore_set_form_notice('error', 'Error - missing button type');
        jrCore_location('referrer');
    }
    $type = '';
    switch ($_post['t']) {
        case 'index':
        case 'list':
        case 'detail':
        case 'bundle_index':
        case 'bundle_list':
        case 'bundle_detail':
            $type = $_post['t'];
            break;
        default:
            jrCore_set_form_notice('error', 'Invalid button type');
            jrCore_location('referrer');
            break;
    }
    if (!isset($_post['m']) || !jrCore_module_is_active($_post['m'])) {
        jrCore_set_form_notice('error', 'invalid module');
        jrCore_location('referrer');
    }
    $mod = $_post['m'];
    if (!isset($_post['o']) || !jrCore_checktype($_post['o'], 'number_nn')) {
        jrCore_set_form_notice('error', 'invalid button offset');
        jrCore_location('referrer');
    }
    $idx = (int) $_post['o'];

    // See if we have customized this button
    $_rs = false;
    $_rt = false;
    $opt = "{$mod}_item_{$type}_buttons";
    if (isset($_conf[$opt])) {
        $_rs = json_decode($_conf[$opt], true);
        if (isset($_rs[$idx])) {
            $_rt = $_rs[$idx];
        }
    }
    else {
        // We've never set order for this module - create conf entry
        $_tmp = array(
            'name'     => "item_{$type}_buttons",
            'default'  => '',
            'type'     => 'hidden',
            'required' => 'off',
            'validate' => 'not_empty',
            'label'    => "item {$type} buttons",
            'help'     => "this hidden field keeps track of the item {$type} button information for {$_post['m']}/{$_post['t']} - do not modify"
        );
        jrCore_register_setting($mod, $_tmp);
    }

    // See if we fall through
    if (!$_rt) {
        // Get our existing (default) order
        $_rs = array();
        $_rf = jrCore_get_registered_module_features('jrCore', "item_{$type}_button");
        if ($_rf && is_array($_rf)) {
            foreach ($_rf as $bmod => $_ft) {
                foreach ($_ft as $func => $_inf) {
                    $_inf['module']   = $bmod;
                    $_inf['function'] = $func;
                    $_rs[]            = $_inf;
                }
            }
        }
        else {
            jrCore_set_form_notice('error', "no registered {$type} buttons found");
            jrCore_location('referrer');
        }
        if (!isset($_rs[$idx])) {
            jrCore_set_form_notice('error', "invalid button offset");
            jrCore_location('referrer');
        }
    }

    // See if they are active for this view
    foreach ($_rs as $k => $_inf) {
        if (isset($_inf['function'])) {
            $func = $_inf['function'];
            if (function_exists($func)) {
                if (!$func($mod, false, false, false, true)) {
                    unset($_rs[$k]);
                }
            }
        }
    }
    $_rs = array_values($_rs);

    // Update with new settings
    $_rs[$idx]['active'] = $_post['active'];
    $_rs[$idx]['quota']  = trim($_post['quota']);
    if ($_post['group'] != '0') {
        $_rs[$idx]['group'] = trim($_post['group']);
    }
    else {
        unset($_rs[$idx]['group']);
    }

    jrCore_form_delete_session();
    jrCore_set_setting_value($mod, "item_{$type}_buttons", json_encode($_rs));

    // Reset caches
    jrCore_delete_config_cache();
    jrCore_delete_all_cache_entries($mod);

    if (jrCore_checktype($_conf['jrCore_default_cache_seconds'], 'number_nz')) {
        jrCore_set_form_notice('success', 'The button settings have been updated.<br>Make sure and <a href="' . $_conf['jrCore_base_url'] . '/' . $_post['module_url'] . '/cache_reset"><u>Reset Caches</u></a> when complete for your changes to take effect', false);
    }
    else {
        jrCore_set_form_notice('success', 'The button settings have been updated');
    }
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/item_action_buttons/{$type}/m={$mod}");
}

//------------------------------
// item_action_button_order
//------------------------------
function view_jrCore_item_action_button_order($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['t']) || strlen($_post['t']) === 0) {
        jrCore_notice_page('error', 'Error - missing button type');
    }
    $type = '';
    switch ($_post['t']) {
        case 'index':
        case 'list':
        case 'detail':
        case 'bundle_index':
        case 'bundle_list':
        case 'bundle_detail':
            $type = $_post['t'];
            break;
        default:
            jrCore_set_form_notice('error', 'invalid button type');
            jrCore_location('referrer');
            break;
    }
    if (!isset($_post['m']) || !jrCore_module_is_active($_post['m'])) {
        jrCore_set_form_notice('error', 'invalid module');
        jrCore_location('referrer');
    }
    if (!isset($_post['o']) || !jrCore_checktype($_post['o'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid order');
        jrCore_location('referrer');
    }

    // Get our existing (default) order
    $_rs = array();
    $_ex = array();
    $_rf = jrCore_get_registered_module_features('jrCore', "item_{$type}_button");
    if ($_rf && is_array($_rf)) {
        foreach ($_rf as $mod => $_ft) {
            foreach ($_ft as $func => $_inf) {
                $_inf['module']   = $mod;
                $_inf['function'] = $func;
                $_rs[]            = $_inf;
                $_ex[$func]       = $_inf;
            }
        }
    }

    // Every module has it's own custom setting to store the order for
    $opt = "{$_post['m']}_item_{$type}_buttons";
    if (!isset($_conf[$opt])) {

        // We've never set order for this module - create conf entry
        $_tmp = array(
            'name'     => "item_{$type}_buttons",
            'default'  => '',
            'type'     => 'hidden',
            'required' => 'off',
            'validate' => 'not_empty',
            'label'    => "item {$type} buttons",
            'help'     => "this hidden field keeps track of the item {$type} button information for {$_post['m']}/{$_post['t']} - do not modify"
        );
        jrCore_register_setting($_post['m'], $_tmp);

    }
    else {
        // Get our existing order - we need to swap the one we got with the one above it
        $_rs = json_decode($_conf[$opt], true);
        if (!isset($_rs)) {
            $_rs = array();
        }
        // See if we have any new modules that were not part of our save - they go at the bottom
        foreach ($_ex as $func => $_dat) {
            $found = false;
            foreach ($_rs as $_inf) {
                if ($_inf['function'] == $func) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $_rs[] = $_dat;
            }
        }
    }

    // See if they are active for this view
    foreach ($_rs as $k => $_inf) {
        if (isset($_inf['function'])) {
            $func = $_inf['function'];
            if (function_exists($func)) {
                if (!$func($_post['m'], false, false, false, true)) {
                    unset($_rs[$k]);
                }
            }
        }
    }
    $_rs = array_values($_rs);

    $idx = (int) $_post['o'];
    foreach ($_rs as $k => $v) {
        $pre = intval($idx - 1);
        if ($k === $pre) {
            $_tm       = $_rs[$idx];
            $_rs[$idx] = $v;
            $_rs[$pre] = $_tm;
            unset($_tm);
            break;
        }
    }
    jrCore_set_setting_value($_post['m'], "item_{$type}_buttons", json_encode($_rs));

    // Reset caches
    jrCore_delete_config_cache();
    jrCore_delete_all_cache_entries($_post['m']);

    if (jrCore_checktype($_conf['jrCore_default_cache_seconds'], 'number_nz')) {
        jrCore_set_form_notice('success', 'The button order has been updated.<br>Make sure and <a href="' . $_conf['jrCore_base_url'] . '/' . $_post['module_url'] . '/cache_reset"><u>Reset Caches</u></a> when complete for your changes to take effect', false);
    }
    else {
        jrCore_set_form_notice('success', 'The button order has been updated');
    }
    jrCore_location('referrer');
}

//------------------------------
// template_compare (magic)
//------------------------------
function view_jrCore_template_compare($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();

    if (isset($_post['skin'])) {
        $cancel_url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/templates/skin={$_post['skin']}";
        $t_type     = 'skin';
    }
    else {
        $cancel_url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/templates";
        $t_type     = 'module';
    }

    // DATABASE TEMPLATE
    if (isset($_post['id']) && jrCore_checktype($_post['id'], 'number_nz')) {
        $tbl = jrCore_db_table_name('jrCore', 'template');
        $req = "SELECT * FROM {$tbl} WHERE template_id = '{$_post['id']}'";
        $_tp = jrCore_db_query($req, 'SINGLE');
        if (!$_tp || !is_array($_tp)) {
            jrCore_set_form_notice('error', 'Invalid template_id - please try again');
            jrCore_location($cancel_url);
        }
        $tp1 = $_tp['template_body'];
        $mod = $_tp['template_module'];
        $nam = $_tp['template_name'];
        $tag = 'custom';
    }

    // SKIN TEMPLATE
    elseif ($t_type == 'skin') {
        if (!is_file(APP_DIR . "/skins/{$_post['skin']}/{$_post['id']}")) {
            jrCore_set_form_notice('error', 'skin template file not found - please try again');
            jrCore_location($cancel_url);
        }
        $tp1 = trim(file_get_contents(APP_DIR . "/skins/{$_post['skin']}/{$_post['id']}"));
        $mod = $_post['skin'];
        $nam = $_post['id'];
    }

    // MODULE TEMPLATE
    else {
        if (!is_file(APP_DIR . "/modules/{$_post['module']}/templates/{$_post['id']}")) {
            jrCore_set_form_notice('error', 'module template file not found - please try again');
            jrCore_location($cancel_url);
        }
        $tp1 = trim(file_get_contents(APP_DIR . "/modules/{$_post['module']}/templates/{$_post['id']}"));
        $mod = $_post['module'];
        $nam = $_post['id'];
    }
    $omod = $mod;

    // Handle our incoming version, which can be in the following format
    // ModDir:Version:Custom-[id]
    $cst = false;
    if (isset($_post['version']) && strlen($_post['version']) > 0) {
        @list($dir, $ver, $cst) = explode(':', $_post['version']);
        $_post['version'] = $ver;
        $mod              = $dir;
    }

    // Okay - we know this user has customized this template, so we compare to the
    // version located on the file system

    if ($t_type == 'skin') {
        if (isset($_post['version']) && strlen($_post['version']) > 0) {
            if (isset($_mods[$mod])) {
                // We are comparing a skin override template to it's module counterpart
                $tnam = str_replace("{$mod}_", '', $nam);
                $tpl_file = APP_DIR . "/modules/{$mod}-release-{$_post['version']}/templates/{$tnam}";
                if (!is_file($tpl_file)) {
                    $tpl_file = APP_DIR . "/modules/{$mod}/templates/{$tnam}";
                }
            }
            else {
                $tpl_file = APP_DIR . "/skins/{$mod}-release-{$_post['version']}/{$nam}";
                if (!is_file($tpl_file)) {
                    $tpl_file = APP_DIR . "/skins/{$mod}/{$nam}";
                }
            }
        }
        else {
            if (isset($_mods[$mod])) {
                $tnam = str_replace("{$mod}_", '', $nam);
                $tpl_file = APP_DIR . "/skins/{$mod}/templates/{$tnam}";
            }
            else {
                $tpl_file = APP_DIR . "/skins/{$mod}/{$nam}";
            }
        }
        $_v1 = glob(APP_DIR . "/skins/*-release-*/{$nam}");
        $_v2 = glob(APP_DIR . "/skins/*/{$nam}");
        // Is this a module override template?  If so, get version from module
        if (strpos($nam, '_')) {
            list($tmod, $tnam) = explode('_', $nam, 2);
            if (isset($_mods[$tmod]) && is_file(APP_DIR . "/modules/{$tmod}/templates/{$tnam}")) {
                $_v2[] = APP_DIR . "/modules/{$tmod}/templates/{$tnam}";
            }
        }
        $_vers  = array_merge($_v1, $_v2);
        $_vers  = array_unique($_vers);
        $_meta  = jrCore_skin_meta_data($mod);
        $active = $_meta['version'];
        $_met2  = jrCore_skin_meta_data($_post['skin']);
        if (!isset($tag)) {
            $tag = $_met2['version'];
        }
        $ctype = "/skin={$_post['skin']}";
    }
    else {
        if (isset($_post['version']) && strlen($_post['version']) > 0) {
            $tpl_file = APP_DIR . "/modules/{$mod}-release-{$_post['version']}/templates/{$nam}";
            if (!is_file($tpl_file)) {
                $tpl_file = APP_DIR . "/modules/{$mod}/templates/{$nam}";
            }
        }
        else {
            $tpl_file = APP_DIR . "/modules/{$mod}/templates/{$nam}";
        }
        $_v1    = glob(APP_DIR . "/modules/*-release-*/templates/{$nam}");
        $_v2    = glob(APP_DIR . "/modules/*/templates/{$nam}");
        $_vers  = array_merge($_v1, $_v2);
        $_vers  = array_unique($_vers);
        $active = $_mods[$mod]['module_version'];
        if (!isset($tag)) {
            $tag = $_mods["{$_post['module']}"]['module_version'];
        }
        $ctype = '';
    }

    $tp2 = false;
    if ($cst && isset($dir)) {
        list($ver, $tid) = explode('-', $cst, 2);
        $_post['version'] = $ver;
        // get the db version
        $tbl = jrCore_db_table_name('jrCore', 'template');
        $req = "SELECT * FROM {$tbl} WHERE template_id = '{$tid}'";
        $_tp = jrCore_db_query($req, 'SINGLE');
        if ($_tp && is_array($_tp)) {
            $_rp = array(
                $dir                        => $omod,
                substr($dir, 2)             => substr($omod, 2),
                strtolower($dir)            => strtolower($omod),
                strtoupper($dir)            => strtoupper($omod),
                strtolower(substr($dir, 2)) => strtolower(substr($omod, 2)),
                strtoupper(substr($dir, 2)) => strtoupper(substr($omod, 2)),
            );
            $_tp['template_body'] = trim($_tp['template_body']);
            $tp2 = strtr($_tp['template_body'], $_rp);
        }
    }
    elseif (is_file($tpl_file)) {

        if (isset($dir)) {
            $_rp = array(
                $dir                        => $_post['skin'],
                substr($dir, 2)             => substr($_post['skin'], 2),
                strtolower($dir)            => strtolower($_post['skin']),
                strtoupper($dir)            => strtoupper($_post['skin']),
                strtolower(substr($dir, 2)) => strtolower(substr($_post['skin'], 2)),
                strtoupper(substr($dir, 2)) => strtoupper(substr($_post['skin'], 2)),
            );
            $tp2 = strtr(file_get_contents($tpl_file), $_rp);
        }
        else {
            $tp2 = trim(file_get_contents($tpl_file));
        }
    }
    if (!$tp2) {
        jrCore_set_form_notice('error', "{$t_type} template file not found - please try again (2)");
        jrCore_location($cancel_url);
    }

    // Create jumper of previous versions if they exist
    if (is_array($_vers) && count($_vers) > 0) {

        if (isset($_post['version']) && strlen($_post['version']) > 0) {
            $sel = $_post['version'];
        }
        else {
            $sel = $active;
        }

        $selected = false;
        foreach ($_vers as $full_file) {

            // allow them to compare the file OTHER files of the same name
            if (strpos($full_file, '/skins/')) {
                $tmp = explode('-', basename(dirname($full_file)));
            }
            else {
                $tmp = explode('-', basename(dirname(dirname($full_file))));
            }
            $ver = end($tmp);
            $tmp = reset($tmp);
            $fnm = "{$tmp}/{$nam}";
            if ($t_type == 'skin' && isset($_mods[$tmp])) {
                $fnm = "{$tmp}/templates/" . str_replace("{$tmp}_", '', $nam);
            }

            if ($ver == $tmp) {
                // This is a NON release directory
                if (isset($_mods[$tmp])) {
                    $ver = $_mods[$tmp]['module_version'];
                }
                else {
                    $_sk = jrCore_skin_meta_data($tmp);
                    if ($_sk) {
                        $ver = $_sk['version'];
                    }
                    else {
                        continue;
                    }
                }
            }

            $v = "{$tmp}:{$ver}";

            if ($mod == $tmp && $ver == $sel) {
                $selected = $v;
            }
            $_option[$v] = "{$fnm} - {$ver}";
        }

        // add in any custom templates of the same name
        $skn = jrCore_get_skins();
        $skn = "'" . implode("','", array_keys($skn)) . "'";
        $tbl = jrCore_db_table_name('jrCore', 'template');
        $req = "SELECT * FROM {$tbl} WHERE template_name = '{$nam}' AND `template_module` IN ({$skn})";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if ($_rt && is_array($_rt)) {
            foreach ($_rt as $_row) {
                $v = "{$_row['template_module']}:{$_row['template_name']}:custom-{$_row['template_id']}";
                if ($cst && isset($tid) && $tid == $_row['template_id']) {
                    $selected = $v;
                }
                $_option[$v] = "{$_row['template_module']}/{$_row['template_name']} - custom";
            }
        }
        // ordering
        $sub = '<select name="skin_jumper" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $_conf['jrCore_base_url'] . "/{$_post['module_url']}/template_compare{$ctype}/id={$_post['id']}/version='+ $(this).val())\">\n";

        natcasesort($_option);
        if ($t_type == 'module') {
            foreach ($_option as $v => $t) {
                if ($selected == $v) {
                    $sub .= '<option value="' . $v . '" selected="selected"> ' . $t . "</option>\n";
                }
                else {
                    $sub .= '<option value="' . $v . '"> ' . $t . "</option>\n";
                }
            }
        }
        else {
            $_opt_s = array();
            $_opt_m = array();
            foreach ($_option as $v => $t) {
                list($sdir,) = explode(':', $v);
                if (isset($_mods[$sdir])) {
                    $_opt_m[$v] = $t;
                }
                else {
                    $_opt_s[$v] = $t;
                }
            }
            if (count($_opt_m) > 0) {
                $sub .= "<optgroup label=\"skins\">\n";
                foreach ($_opt_s as $v => $t) {
                    if ($selected == $v) {
                        $sub .= '<option value="' . $v . '" selected="selected"> ' . $t . "</option>\n";
                    }
                    else {
                        $sub .= '<option value="' . $v . '"> ' . $t . "</option>\n";
                    }
                }
                $sub .= "</optgroup>\n";
                $sub .= "<optgroup label=\"modules\">\n";
                foreach ($_opt_m as $v => $t) {
                    if ($selected == $v) {
                        $sub .= '<option value="' . $v . '" selected="selected"> ' . $t . "</option>\n";
                    }
                    else {
                        $sub .= '<option value="' . $v . '"> ' . $t . "</option>\n";
                    }
                }
                $sub .= "</optgroup>\n";
            }
            else {
                foreach ($_option as $v => $t) {
                    if ($selected == $v) {
                        $sub .= '<option value="' . $v . '" selected="selected"> ' . $t . "</option>\n";
                    }
                    else {
                        $sub .= '<option value="' . $v . '"> ' . $t . "</option>\n";
                    }
                }
            }
        }
        $sub .= '</select>';
    }
    else {
        $sub = $nam;
        $sel = $active;
    }

    jrCore_page_banner("template compare", $sub);

    if (isset($dir)) {
        if (isset($_mods[$dir])) {
            $ttl = "Compared To: {$dir}/templates/" . str_replace("{$dir}_", '', $nam);
        }
        else {
            $ttl = "Compared To: {$dir}/{$nam}";
        }
        jrCore_set_form_notice('success', "To help highlight changes all instances of <b>{$dir}</b> in the compare file have been changed to <b>{$_post['skin']}</b>", false);
    }
    elseif (!isset($_post['version'])) {
        $ttl = "{$omod}/{$nam}";
    }
    else {
        $ttl = $nam;
    }
    jrCore_get_form_notice();

    $ver = $_mods['jrCore']['module_version'];

    // Setup Code Mirror
    jrCore_create_page_element('css_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/lib/codemirror.css?_v={$ver}"));
    jrCore_create_page_element('css_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/addon/merge/merge.css?_v={$ver}"));
    jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/lib/codemirror.js?_v={$ver}"));
    jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/addon/diff_match_patch.js?_v={$ver}"));
    jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/addon/merge/merge.js?_v={$ver}"));
    jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/mode/htmlmixed/htmlmixed.js?_v={$ver}"));
    jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/mode/xml/xml.js?_v={$ver}"));
    jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/mode/smarty/smarty.js?_v={$ver}"));

    // Mergely
    jrCore_create_page_element('css_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/mergely/mergely.css?_v={$ver}"));
    jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/mergely/mergely.min.js?_v={$ver}"));

    $_tmp = array(
        'submit_value'     => 'save changes',
        'cancel'           => $cancel_url,
        'onclick'          => 'jrCore_compare_get_modified_template();',
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    $html = "<div><table><tr><th class=\"diff_head\">{$omod}/{$nam} ({$tag})</th><th class=\"diff_head\">{$ttl} ({$sel})</th></tr>";
    $html .= "<tr><td colspan=\"2\">";

    $_rep = array(
        'code_left'    => json_encode($tp1),
        'code_right'   => json_encode($tp2),
        'succcess_url' => $cancel_url
    );
    if (isset($_post['skin'])) {
        $_rep['skin'] = $_post['skin'];
    }
    else {
        $_rep['module_url'] = $_post['module_url'];
    }

    if (isset($_post['id']) && jrCore_checktype($_post['id'], 'number_nz')) {
        $_rep['template_id'] = $_post['id'];
    }
    else {
        $_rep['template_name'] = $_post['id'];
    }

    $html .= jrCore_parse_template('template_compare.tpl', $_rep, 'jrCore');
    $html .= "</td></tr></table></div>";
    jrCore_page_custom($html);

    jrCore_page_display();
}

//------------------------------
// template_compare_save (magic)
//------------------------------
function view_jrCore_template_compare_save($_post, $_user, $_conf)
{
    jrUser_master_only();

    if (!isset($_post['template_body']) || strlen(trim($_post['template_body'])) === 0) {
        jrCore_set_form_notice('error', 'The received template content was empty - please try again');
        jrCore_form_result();
    }

    // See if we are doing a skin or module
    $mod = (isset($_post['skin'])) ? $_post['skin'] : $_post['module'];

    // We need to test this template and make sure it does not cause any Smarty errors
    $err = jrCore_test_template_for_errors($mod, $_post['template_body']);
    if ($err && strpos($err, 'error') === 0) {
        $_SESSION['template_body_save'] = $_post['template_body'];
        jrCore_set_form_notice('error', substr($err, 7), false);
    }

    $tbl = jrCore_db_table_name('jrCore', 'template');
    // See if we are updating a DB template or first time file
    if (isset($_post['template_id']) && jrCore_checktype($_post['template_id'], 'number_nz')) {
        // Make sure we have a valid template
        $req = "SELECT * FROM {$tbl} WHERE template_id = '{$_post['template_id']}'";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if (!$_rt || !is_array($_rt)) {
            $_SESSION['template_body_save'] = $_post['template_body'];
            jrCore_set_form_notice('error', 'Invalid template_id - please try again');
            jrCore_form_result();
        }
        $req = "UPDATE {$tbl} SET
                  template_updated = UNIX_TIMESTAMP(),
                  template_user    = '" . jrCore_db_escape($_user['user_name']) . "',
                  template_body    = '" . jrCore_db_escape($_post['template_body']) . "'
                 WHERE template_id = '{$_post['template_id']}'";
        $cnt = jrCore_db_query($req, 'COUNT');
        // Reset the template cache
        jrCore_get_template_file($_rt['template_name'], $mod, 'reset');
        $hl = $_rt['template_name'];
    }
    else {
        if (!isset($_post['template_name']{1})) {
            $_SESSION['template_body_save'] = $_post['template_body'];
            jrCore_set_form_notice('error', 'Invalid template_name - please try again');
            jrCore_form_result();
        }
        $hl = $_post['template_name'];
        // See if we already exist - this can happen when the user FIRST modifies the template
        // and does not leave the screen, and modifies again
        $nam = jrCore_db_escape($_post['template_name']);
        $mod = jrCore_db_escape($mod);
        $req = "INSERT INTO {$tbl} (template_created,template_updated,template_user,template_active,template_name,template_module,template_body)
                VALUES(UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),'" . jrCore_db_escape($_user['user_name']) . "','0','{$nam}','{$mod}','" . jrCore_db_escape($_post['template_body']) . "')";
        $tid = jrCore_db_query($req, 'INSERT_ID');
        if ($tid && jrCore_checktype($tid, 'number_nz')) {
            $cnt = 1;
            // Reset the template cache
            jrCore_get_template_file($_post['template_name'], $mod, 'reset');
        }
    }
    if (isset($cnt) && $cnt === 1) {
        jrCore_set_form_notice('success', 'The template has been successfully updated');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered saving the template update - please try again');
    }
    jrCore_form_delete_session();
    if (isset($_post['skin'])) {
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/skin_admin/templates/skin={$_post['skin']}/hl={$hl}"); // skin
    }
    else {
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/templates/hl={$hl}"); // module
    }

}

//------------------------------
// export_datastore_csv
//------------------------------
function view_jrCore_export_datastore_csv($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    @ini_set('max_execution_time', 3600);
    @ini_set('memory_limit', '512M');
    if (!isset($_post['m']) || !jrCore_module_is_active($_post['m'])) {
        jrCore_notice_page('error', 'invalid module');
    }
    $_tm = jrCore_db_get_unique_keys($_post['m']);
    if ($_tm && is_array($_tm)) {
        $_fl = array();
        foreach ($_tm as $k) {
            $_fl[$k] = $k;
        }
        ksort($_fl);
        switch ($_post['m']) {
            case 'jrUser':
                $key = '_user_id';
                unset($_fl['user_password'], $_fl['user_old_password']);
                break;
            case 'jrProfile':
                $key = '_profile_id';
                break;
            default:
                $key = '_item_id';
                break;
        }
        $out = array($key => $key);
        unset($_fl[$key]);
        $_fl = array_merge($out, $_fl);
        $cdr = jrCore_get_module_cache_dir('jrCore');

        // Delete old CSV files
        jrCore_delete_old_datastore_csv_files();

        $fil = "{$cdr}/{$_post['m']}_datastore_" . strftime('%Y%m%d%H%M') . ".csv";
        jrCore_write_to_file($fil, implode(',', $_fl) . "\n");

        $uid = 0;
        while (true) {
            $_it = array(
                'search'         => array(
                    "_item_id > {$uid}"
                ),
                'order_by'       => array('_item_id' => 'asc'),
                'skip_triggers'  => true,
                'ignore_pending' => true,
                'privacy_check'  => false,
                'limit'          => 2000
            );
            $_it = jrCore_db_search_items($_post['m'], $_it);
            if ($_it && is_array($_it) && isset($_it['_items'])) {
                $out = array();
                foreach ($_it['_items'] as $i) {
                    $_tm = array();
                    foreach ($_fl as $f) {
                        if (isset($i[$f]) && strlen($i[$f]) > 0) {
                            if (is_numeric($i[$f])) {
                                if (strlen($i[$f]) === 10) {
                                    $_tm[$f] = jrCore_format_time($i[$f]);
                                }
                                else {
                                    $_tm[$f] = $i[$f];
                                }
                            }
                            else {
                                $_tm[$f] = '"' . str_replace('"', '""', $i[$f]) . '"';
                            }
                        }
                        else {
                            $_tm[$f] = '';
                        }
                    }
                    $out[] = implode(',', $_tm);
                    $uid   = $i[$key];
                }
                jrCore_write_to_file($fil, jrCore_replace_emoji(implode("\n", $out)) . "\n", 'append');
                if (count($_it['_items']) < 2000) {
                    // We are done
                    break;
                }
            }
            else {
                // We are done
                break;
            }
        }
        jrCore_send_download_file($fil);
        session_write_close();
        jrCore_trigger_event('jrCore', 'process_done', $_post);
        exit;
    }
    jrCore_page_notice('error', 'There are no DataStore entries for this module');
}

//------------------------------
// attachment_delete
//------------------------------
function view_jrCore_attachment_delete($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_json_response(array('error' => 'id was not a number or not set'));
    }

    if (!isset($_post['upload_module']) || !jrCore_module_is_active($_post['upload_module'])) {
        jrCore_json_response(array('error' => 'upload_module was not set or not active'));
    }

    $pfx = jrCore_db_get_prefix($_post['upload_module']);
    if (strpos($_post['upload_field'], $pfx) !== 0) {
        jrCore_json_response(array('error' => 'upload_field did not have the prefix of the upload_module'));
    }

    $_rt = jrCore_db_get_item($_post['upload_module'], $_post['id']);

    // Make sure the calling user has permission to delete this item
    if (!jrUser_is_admin() && !jrProfile_is_profile_owner($_rt[$pfx . '_profile_id']) && $_rt['_user_id'] != $_user['_user_id']) {
        jrCore_json_response(array('error' => 'not authorized'));
    }

    // Delete attached item
    jrCore_delete_item_media_file($_post['upload_module'], $_post['upload_field'], jrUser_get_profile_home_key('_profile_id'), $_post['id']);
    jrProfile_reset_cache($_rt['_profile_id'], $_post['upload_module']);
    jrCore_json_response(array('success' => 'item was deleted successfully'));
}
