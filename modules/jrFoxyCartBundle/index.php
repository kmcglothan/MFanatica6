<?php
/**
 * Jamroom FoxyCart Bundles module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
 *
 * This Jamroom file is LICENSED SOFTWARE, and cannot be redistributed.
 *
 * This Source Code is subject to the terms of the Jamroom Network
 * Commercial License -  please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
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
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//--------------------------------
// display
//--------------------------------
function view_jrFoxyCartBundle_display($_post, $_user, $_conf)
{
    // We must get a unique ID - i.e. jrAudio-audio_file-31
    if (!isset($_post['id']) || strlen($_post['id']) === 0) {
        jrCore_json_response(array('error' => 'invalid id'));
    }
    list($module, $field, $item_id) = explode('-', $_post['id']);
    if (!jrCore_module_is_active($module)) {
        jrCore_json_response(array('error' => 'module is not active'));
    }
    if (!jrCore_checktype($item_id, 'number_nz')) {
        jrCore_json_response(array('error' => 'invalid item_id'));
    }
    $_rt = jrCore_db_get_item($module, $item_id);
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_json_response(array('error' => 'invalid item_id'));
    }
    if (!isset($_rt["{$field}_item_bundle"]) || strlen($_rt["{$field}_item_bundle"]) === 0) {
        jrCore_json_response(array('error' => 'no bundles'));
    }
    $_sp = array(
        'search'              => array(
            "_item_id in " . $_rt["{$field}_item_bundle"]
        ),
        'order_by'            => array(
            'bundle_title' => 'ASC'
        ),
        'exclude_jrUser_keys' => true,
        'ignore_pending'      => true
    );
    $_bd = jrCore_db_search_items('jrFoxyCartBundle', $_sp);
    if (isset($_bd) && is_array($_bd['_items'])) {
        foreach ($_bd['_items'] as $k => $_item) {
            $_pm                                  = array(
                'module'       => 'jrFoxyCartBundle',
                'field'        => 'bundle',
                'item'         => $_item,
                'quantity_max' => 1
            );
            $_bd['_items'][$k]['add_to_cart_url'] = smarty_function_jrFoxyCart_add_url($_pm, new stdClass());
        }
    }
    // Add in some info about this item
    $prefix           = jrCore_db_get_prefix($module);
    $_pm              = array(
        'module'       => $module,
        'field'        => $field,
        'item'         => $_rt,
        'quantity_max' => 1
    );
    $_bd['item_info'] = array(
        'item_title' => $_rt["{$prefix}_title"],
        'item_price' => $_rt["{$field}_item_price"]
    );
    if (isset($_rt["{$field}_item_price"]) && $_rt["{$field}_item_price"] > 0) {
        $_bd['item_info']['add_to_cart_url'] = smarty_function_jrFoxyCart_add_url($_pm, new stdClass());
    }
    $_bd['module']   = $module;
    $_bd['field']    = $field;
    $_bd['_item_id'] = $item_id;
    $_bd['class']    = "{$module}-{$field}-{$item_id}";
    $_bd['item']     = $_rt;
    unset($_rt);
    return jrCore_parse_template('bundle_display.tpl', $_bd, 'jrFoxyCartBundle');
}

//--------------------------------
// update
//--------------------------------
function view_jrFoxyCartBundle_update($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFoxyCart');

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid bundle_id - please try again');
        jrCore_location('referrer');
    }
    $_rt = jrCore_db_get_item('jrFoxyCartBundle', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid bundle_id - please try again');
        jrCore_location('referrer');
    }
    // Make sure the calling user has permissions to remove this action
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    jrCore_page_banner(46);

    // Form init
    $_tmp = array(
        'submit_value'     => 17,
        'cancel'           => 'referrer',
        'form_ajax_submit' => false,
        'values'           => $_rt
    );
    jrCore_form_create($_tmp);

    // Bundle ID
    $_tmp = array(
        'name'  => 'id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    // Bundle Name
    $_tmp = array(
        'name'     => 'bundle_title',
        'label'    => 2,
        'help'     => 8,
        'type'     => 'text',
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    // Bundle Price
    $_tmp = array(
        'name'     => 'bundle_item_price',
        'label'    => 3,
        'help'     => 9,
        'type'     => 'text',
        'validate' => 'price'
    );
    jrCore_form_field_create($_tmp);

    // Bundle Description
    $_tmp = array(
        'name'     => 'bundle_description',
        'label'    => 19,
        'help'     => 20,
        'type'     => 'editor',
        'validate' => 'allowed_html',
        'required' => false
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrFoxyCartBundle_update_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();

    // Validate all incoming posted data
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrFoxyCart');

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid bundle_id - please try again');
        jrCore_form_result('referrer');
    }

    // Get data
    $_rt = jrCore_db_get_item('jrFoxyCartBundle', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 'invalid bundle_id - please try again');
        jrCore_form_result('referrer');
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrFoxyCartBundle', 'update', $_post);

    // Add in our SEO URL names
    $_sv['bundle_title_url'] = jrCore_url_string($_sv['bundle_title']);

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'update', 'jrFoxyCartBundle', $_post['id']);

    jrCore_db_update_item('jrFoxyCartBundle', $_post['id'], $_sv);
    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$_post['id']}/{$_sv['bundle_title_url']}");
}

//----------------------------------------------------
// remove_save
//----------------------------------------------------
function view_jrFoxyCartBundle_remove_save($_post, &$_user, &$_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();

    jrUser_check_quota_access('jrFoxyCartBundle');

    if (!isset($_post['bundle_module']) || !jrCore_module_is_active($_post['bundle_module'])) {
        $out = array(
            'type' => 'error',
            'note' => 'invalid bundle module'
        );
        jrCore_json_response($out);
    }
    $mod = $_post['bundle_module'];

    if (!isset($_post['bundle_id']) || !jrCore_checktype($_post['bundle_id'], 'number_nz')) {
        $out = array(
            'type' => 'error',
            'note' => 'invalid bundle_id'
        );
        jrCore_json_response($out);
    }
    $bid = (int) $_post['bundle_id'];

    // Make sure this is a good bundle
    $_rt = jrCore_db_get_item('jrFoxyCartBundle', $bid);
    if (!isset($_rt) || !is_array($_rt)) {
        $out = array(
            'type' => 'error',
            'note' => 'invalid bundle_id'
        );
        jrCore_json_response($out);
    }
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    if (!isset($_post['item_id']) || !jrCore_checktype($_post['item_id'], 'number_nz')) {
        $out = array(
            'type' => 'error',
            'note' => 'invalid item_id'
        );
        jrCore_json_response($out);
    }
    $iid = (int) $_post['item_id'];

    // Get item info
    $_it = jrCore_db_get_item($mod, $iid);

    $_rt['bundle_list'] = json_decode($_rt['bundle_list'], true);
    if (isset($_rt['bundle_list'][$mod][$iid])) {
        unset($_rt['bundle_list'][$mod][$iid]);
        $_rt['bundle_count'] = (intval($_rt['bundle_count']) - 1);
    }
    $_dt = array(
        'bundle_list'  => json_encode($_rt['bundle_list']),
        'bundle_count' => $_rt['bundle_count']
    );
    jrCore_db_update_item('jrFoxyCartBundle', $_post['bundle_id'], $_dt);

    // Next, we need to update the item and remove this bundle from it's list
    if (isset($_it) && is_array($_it)) {
        foreach ($_it as $k => $v) {
            if (strpos($k, '_item_bundle')) {
                $_tmp = explode(',', $v);
                if (isset($_tmp) && is_array($_tmp)) {
                    $_new = array();
                    foreach ($_tmp as $bundle_id) {
                        if ($bundle_id != $bid) {
                            $_new[] = $bundle_id;
                        }
                    }
                    if (count($_new) > 0) {
                        $_dt = array(
                            $k => implode(',', $_new)
                        );
                        jrCore_db_update_item($mod, $iid, $_dt);
                        $_it[$k] = $_dt[$k];
                    }
                    else {
                        jrCore_db_delete_item_key($mod, $iid, $k);
                        unset($_it[$k]);
                    }
                }
                break;
            }
        }
    }

    // Send out delete_bundle_item trigger
    jrCore_trigger_event('jrFoxyCartBundle', 'delete_bundle_item', $_it, $_post);

    jrProfile_reset_cache();

    $out = array(
        'type' => 'success',
        'note' => 'bundle item successfully deleted'
    );
    jrCore_json_response($out);
}

//--------------------------------
// delete
//--------------------------------
function view_jrFoxyCartBundle_delete($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrFoxyCart');

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid bundle_id - please try again');
        jrCore_location('referrer');
    }
    $_rt = jrCore_db_get_item('jrFoxyCartBundle', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid bundle_id - please try again');
        jrCore_location('referrer');
    }
    // Make sure the calling user has permissions to remove this action
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Delete the bundle and remove it from any items that were part of it
    if (jrCore_db_delete_item('jrFoxyCartBundle', $_post['id'])) {

        // We need to go get the items associated with this bundle
        $_xtra = array('module' => 'jrFoxyCart');
        $_form = jrCore_trigger_event('jrFoxyCart', 'add_price_field', array(), $_xtra);
        if ($_form && is_array($_form)) {
            $_temp = array();
            $bid   = (int) $_post['id'];
            foreach ($_form as $view => $field) {
                list($module,) = explode('/', $view);
                if (!isset($_temp[$module])) {
                    $_rt = array(
                        'search'              => array(
                            "{$field}_item_bundle = {$bid} || {$field}_item_bundle like {$bid},% || {$field}_item_bundle like %,{$bid} || {$field}_item_bundle like %,{$bid},%"
                        ),
                        'return_item_id_only' => true,
                        'skip_triggers'       => true,
                        'limit'               => 10000
                    );
                    $_rt = jrCore_db_search_items($module, $_rt);
                    if ($_rt && is_array($_rt)) {
                        $_it = jrCore_db_get_multiple_items($module, $_rt);
                        if ($_it && is_array($_it)) {
                            foreach ($_it as $_item) {
                                foreach ($_item as $k => $v) {
                                    if (strpos($k, '_item_bundle')) {
                                        // explode and save
                                        $_cur = explode(',', $v);
                                        if (isset($_cur) && is_array($_cur)) {
                                            foreach ($_cur as $ck => $cv) {
                                                if (intval($cv) === $bid) {
                                                    unset($_cur[$ck]);
                                                    $_dat = array(
                                                        $k => implode(',', $_cur)
                                                    );
                                                    jrCore_db_update_item($module, $_item['_item_id'], $_dat);
                                                }
                                            }
                                        }
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    $_temp[$module] = 1;
                }
            }
        }
        jrProfile_reset_cache();
    }
    jrCore_location('referrer');
}

//------------------------------
// add to bundle via javascript
//------------------------------
function view_jrFoxyCartBundle_add($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrFoxyCartBundle');

    // Make sure we get a good id
    if (!isset($_post['_2']) || !jrCore_checktype($_post['_2'], 'number_nz')) {
        jrCore_notice_page('error', "invalid item_id - please try again", false, false, true, false);
    }

    // Check for pagebreak
    $p = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $p = (int) $_post['p'];
    }
    // show all the bundles belonging to this profile
    $_sp                   = array(
        'search'    => array(
            "_profile_id = {$_user['user_active_profile_id']}",
            "bundle_count > 0"
        ),
        'order_by'  => array(
            '_created' => 'desc'
        ),
        'pagebreak' => 6,
        'page'      => $p
    );
    $_rep                  = jrCore_db_search_items('jrFoxyCartBundle', $_sp);
    $_rep['item_id']       = $_post['_2'];
    $_rep['bundle_module'] = $_post['_1'];
    $_rep['field']         = $_post['field'];

    // Mark the bundles that we are already part of
    if (isset($_rep['_items']) && is_array($_rep['_items'])) {
        foreach ($_rep['_items'] as $k => $_v) {
            if (!empty($_v['bundle_list'])) {
                $_v['bundle_list'] = json_decode($_v['bundle_list'], true);
                if (isset($_v['bundle_list']["{$_post['_1']}"]["{$_post['_2']}"])) {
                    $_rep['_items'][$k]['bundle_includes_item'] = 1;
                }
                else {
                    $_rep['_items'][$k]['bundle_includes_item'] = 0;
                }
            }
        }
    }
    return jrCore_parse_template('bundle_add.tpl', $_rep, 'jrFoxyCartBundle');
}

//------------------------------
// add to bundle via javascript
//------------------------------
function view_jrFoxyCartBundle_add_save($_post, &$_user, &$_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrFoxyCartBundle');

    if (!isset($_post['bundle_module']) || !jrCore_module_is_active($_post['bundle_module'])) {
        $out = array(
            'type' => 'error',
            'note' => 'invalid bundle module'
        );
        jrCore_json_response($out);
    }
    $mod = $_post['bundle_module'];

    if (!isset($_post['item_id']) || !jrCore_checktype($_post['item_id'], 'number_nz')) {
        $out = array(
            'type' => 'error',
            'note' => 'invalid item_id'
        );
        jrCore_json_response($out);
    }
    $iid = (int) $_post['item_id'];

    $pfx = jrCore_db_get_prefix($mod);
    if (!isset($_post['field']) || strlen($_post['field']) === 0 || strpos($_post['field'], $pfx) !== 0) {
        $out = array(
            'type' => 'error',
            'note' => 'Invalid item field'
        );
        jrCore_json_response($out);
    }

    if (!isset($_post['bundle_price']) || !jrCore_checktype($_post['bundle_price'], 'price') || $_post['bundle_price'] < 0) {
        $out = array(
            'type' => 'error',
            'note' => 'You must enter a valid price for this bundle'
        );
        jrCore_json_response($out);
    }
    $_bi   = array(
        $mod => array(
            $iid => 1
        )
    );
    $title = strip_tags(html_entity_decode($_post['title']));
    if (jrCore_run_module_function('jrBanned_is_banned', 'word', $title)) {
        $out = array(
            'type' => 'error',
            'note' => 'Invalid bundle title'
        );
        jrCore_json_response($out);
    }

    $_rt = array(
        'bundle_title'      => $title,
        'bundle_title_url'  => jrCore_url_string($title),
        'bundle_item_price' => $_post['bundle_price'],
        'bundle_list'       => $_bi,
        'bundle_count'      => 1
    );

    // Send out add_bundle_item trigger
    $_rt = jrCore_trigger_event('jrFoxyCartBundle', 'add_bundle_item', $_rt, $_post);
    if (isset($_rt['bundle_list'])) {
        $_rt['bundle_list'] = json_encode($_rt['bundle_list']);
    }

    $bid = jrCore_db_create_item('jrFoxyCartBundle', $_rt);
    if (!$bid) {
        $out = array(
            'type' => 'error',
            'note' => 'Could not save to the database'
        );
        jrCore_json_response($out);
    }

    // Update item with bundle info
    $_up = array("{$_post['field']}_item_bundle" => $bid);
    jrCore_db_update_item($mod, $iid, $_up);

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'create', 'jrFoxyCartBundle', $bid);

    jrProfile_reset_cache();
    $out = array(
        'type' => 'success',
        'note' => 'New bundle successfully created'
    );
    jrCore_json_response($out);
}

//----------------------------------------------------
// update the contents of a bundle via javascript
//----------------------------------------------------
function view_jrFoxyCartBundle_inject_save($_post, &$_user, &$_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrFoxyCartBundle');

    // Make sure module is active
    if (!isset($_post['bundle_module']) || !jrCore_module_is_active($_post['bundle_module'])) {
        $out = array(
            'type' => 'error',
            'note' => 'Invalid bundle module'
        );
        jrCore_json_response($out);
    }
    $mod = $_post['bundle_module'];

    if (!isset($_post['bundle_id']) || !jrCore_checktype($_post['bundle_id'], 'number_nz')) {
        $out = array(
            'type' => 'error',
            'note' => 'Invalid bundle_id'
        );
        jrCore_json_response($out);
    }
    $bid = (int) $_post['bundle_id'];

    $pfx = jrCore_db_get_prefix($mod);
    if (!isset($_post['field']) || strlen($_post['field']) === 0 || strpos($_post['field'], $pfx) !== 0) {
        $out = array(
            'type' => 'error',
            'note' => 'Invalid item field'
        );
        jrCore_json_response($out);
    }

    if (!isset($_post['item_id']) || !jrCore_checktype($_post['item_id'], 'number_nz')) {
        $out = array(
            'type' => 'error',
            'note' => 'Invalid item_id'
        );
        jrCore_json_response($out);
    }
    $iid = (int) $_post['item_id'];

    // Make sure this is a good bundle
    $_rt = jrCore_db_get_item('jrFoxyCartBundle', $bid);
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    $cnt                = 0;
    $_rt['bundle_list'] = json_decode($_rt['bundle_list'], true);
    if (!isset($_rt['bundle_list'][$mod][$iid])) {
        $sum = 0;
        foreach ($_rt['bundle_list'] as $entries) {
            $sum += count($entries);
        }
        if (!isset($_rt['bundle_list'][$mod])) {
            $_rt['bundle_list'][$mod] = array();
        }
        $_rt['bundle_list'][$mod][$iid] = $sum;
        $cnt                            = 1;
    }

    // Send out add_bundle_item trigger
    $_rt = jrCore_trigger_event('jrFoxyCartBundle', 'add_bundle_item', $_rt, $_post);

    // Add item to bundle
    $_sv = array(
        'bundle_title'     => $_rt['bundle_title'],
        'bundle_title_url' => jrCore_url_string($_rt['bundle_title']),
        'bundle_list'      => json_encode($_rt['bundle_list']),
        'bundle_count'     => intval($_rt['bundle_count'] + $cnt)
    );
    jrCore_db_update_item('jrFoxyCartBundle', $bid, $_sv);

    // Update item with bundle info
    $val = jrCore_db_get_item_key($mod, $iid, "{$_post['field']}_item_bundle");
    if ($val) {
        $val .= ",{$bid}";
    }
    else {
        $val = $bid;
    }
    $_up = array("{$_post['field']}_item_bundle" => $val);
    jrCore_db_update_item($mod, $iid, $_up);

    jrProfile_reset_cache();
    $out = array(
        'type' => 'success',
        'note' => 'Item added to bundle'
    );
    jrCore_json_response($out);
}

//----------------------------------
// update the order of a bundle
//----------------------------------
function view_jrFoxyCartBundle_order_update($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_session_require_login();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        return jrCore_json_response(array('error', 'invalid bundle id received'));
    }
    if (!isset($_post['bundle_order']) || !is_array($_post['bundle_order'])) {
        return jrCore_json_response(array('error', 'invalid bundle_order array received'));
    }
    $_pl = jrCore_db_get_item('jrFoxyCartBundle', $_post['id']);
    if (!isset($_pl) || !is_array($_pl)) {
        return jrCore_json_response(array('error', 'invalid bundle - unable to load data'));
    }
    if (!jrUser_can_edit_item($_pl)) {
        return jrCore_json_response(array('error', 'permission denied'));
    }
    // Update bundle order
    // [bundle_list] => {"jrAudio":{"8":0,"10":1,"11":2,"12":3,"152":4,"385":8,"383":9,"382":10,"393":11},"jrVideo":{"41":5,"40":6},"jrSoundCloud":{"1":7}}
    $_list = array();
    foreach ($_post['bundle_order'] as $num => $mod_id) {
        list($mod, $id) = explode('-', $mod_id, 2);
        if (!isset($_mods[$mod])) {
            continue;
        }
        $id               = intval($id);
        $_list[$mod][$id] = $num;
    }
    $_data = array(
        'bundle_list' => json_encode($_list)
    );
    jrCore_db_update_item('jrFoxyCartBundle', $_post['id'], $_data);
    jrProfile_reset_cache();
    return jrCore_json_response(array('success', 'video_file_track successfully updated'));
}
