<?php
/**
 * Jamroom Item Bundles module
 *
 * copyright 2018 The Jamroom Network
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

//------------------------------
// import
//------------------------------
function view_jrBundle_import($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrBundle');

    $show = false;
    if (!isset($_mods['jrFoxyCartBundle'])) {
        jrCore_set_form_notice('error', 'The FoxyCart Bundle module is not installed');
    }
    else {
        if (jrCore_db_get_datastore_item_count('jrFoxyCartBundle') === 0) {
            jrCore_set_form_notice('error', "There are no FoxyCart Bundles to import");
        }
        else {
            $show = true;
            jrCore_set_form_notice('success', "This tool will import existing FoxyCart Bundle items in to the Item Bundles module");
            $_msg = array();
            if (jrCore_db_get_datastore_item_count('jrBundle') > 0) {
                $_msg[] = 'Existing bundles in the Bundle Module will be deleted!';
                $_msg[] = 'Existing bundles in the FoxyCart Bundle module will be unaffected.';
            }
            $_msg[] = 'After running this tool make sure and <b>disable</b> the FoxyCart Bundle module!';
            jrCore_set_form_notice('error', implode('<br>', $_msg), false);
        }
    }
    jrCore_page_banner('import FoxyCart bundles');
    jrCore_get_form_notice();

    if ($show) {
        $_tmp = array(
            'submit_value'  => 'import bundles',
            'cancel'        => 'referrer',
            'submit_prompt' => 'Import Bundles from FoxyCart Bundle?'
        );
        jrCore_form_create($_tmp);

        $_tmp = array(
            'name'     => 'create_bundles',
            'label'    => 'import bundles',
            'help'     => 'If this option is checked, bundle items will be copied from the FoxyCart Bundle module into the Item Bundles module',
            'type'     => 'checkbox',
            'validate' => 'onoff',
            'default'  => 'on',
            'required' => true
        );
        jrCore_form_field_create($_tmp);
    }
    jrCore_page_display();
}

//------------------------------
// import_save
//------------------------------
function view_jrBundle_import_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    $cnt = 0;
    // Import Bundles
    if (isset($_post['create_bundles']) && $_post['create_bundles'] == 'on') {

        jrCore_db_truncate_datastore('jrBundle');

        $tb1 = jrCore_db_table_name('jrBundle', 'item_key');
        $tb2 = jrCore_db_table_name('jrFoxyCartBundle', 'item_key');
        $req = "INSERT INTO {$tb1} (`_item_id`,`_profile_id`,`key`,`index`,`value`) SELECT `_item_id`,`_profile_id`,`key`,`index`,`value` FROM {$tb2}";
        jrCore_db_query($req);

        $tb1 = jrCore_db_table_name('jrBundle', 'item');
        $tb2 = jrCore_db_table_name('jrFoxyCartBundle', 'item');
        $req = "INSERT INTO {$tb1} SELECT * FROM {$tb2}";
        $cnt = jrCore_db_query($req, 'COUNT');

        // Update with new fields
        jrCore_db_create_default_key('jrBundle', 'bundle_extension', 'zip');

        // Next - we have to update existing keys with some new structure
        $iid = 0;
        while (true) {
            $_rt = array(
                'search'         => array("_item_id > {$iid}"),
                'return_keys'    => array('_item_id', 'bundle_list', 'bundle_title'),
                'order_by'       => array('_item_id' => 'asc'),
                'skip_triggers'  => true,
                'privacy_check'  => false,
                'ignore_pending' => true,
                'limit'          => 1000
            );
            $_rt = jrCore_db_search_items('jrBundle', $_rt);
            if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
                $_up = array();
                foreach ($_rt['_items'] as $i) {
                    $id = (int) $i['_item_id'];
                    // Fix up our bundle_list
                    $bl = array();
                    if (isset($i['bundle_list']) && strlen($i['bundle_list']) > 0) {
                        if ($_tmp = json_decode($i['bundle_list'], true)) {
                            foreach ($_tmp as $mod => $_ids) {
                                $_fl = jrCore_trigger_event('jrPayment', 'add_price_field', array(), array(), $mod);
                                if (count($_fl) === 0) {
                                    // Backwards compatible
                                    $_fl = jrCore_trigger_event('jrFoxyCart', 'add_price_field', array(), array(), $mod);
                                }
                                foreach ($_ids as $uid => $ord) {
                                    if (count($_fl) > 0) {
                                        $field = reset($_fl);
                                    }
                                    else {
                                        $field = jrCore_db_get_prefix($mod) . '_file';
                                    }
                                    $bl[$mod][$uid] = array($field, $ord);
                                }
                            }
                        }

                    }
                    $_up[$id] = array(
                        'bundle_name' => jrCore_url_string($i['bundle_title']) . '.zip',
                        'bundle_list' => json_encode($bl)
                    );
                    $iid      = $id;
                }
                if (count($_up) > 0) {
                    jrCore_db_update_multiple_items('jrBundle', $_up);
                }
            }
            else {
                // We're done
                break;
            }
        }

    }
    jrCore_set_form_notice('success', "successfully imported " . jrCore_number_format($cnt) . " bundles from the FoxyCart Bundle module");
    jrCore_form_result();
}

//--------------------------------
// display
//--------------------------------
function view_jrBundle_display($_post, $_user, $_conf)
{
    // We must get a unique ID - i.e. jrAudio-audio_file-31
    if (!isset($_post['id']) || strlen($_post['id']) === 0) {
        return 'ERROR: invalid id';
    }
    list($module, $field, $item_id) = explode('-', $_post['id']);
    if (!jrCore_module_is_active($module)) {
        return 'ERROR: module is not active';
    }
    if (!jrCore_checktype($item_id, 'number_nz')) {
        return 'ERROR: invalid item_id';
    }
    $_rt = jrCore_db_get_item($module, $item_id);
    if (!$_rt || !is_array($_rt)) {
        return 'ERROR: invalid item_id - data not found';
    }
    if (!isset($_rt["{$field}_item_bundle"]) || strlen($_rt["{$field}_item_bundle"]) === 0) {
        return 'ERROR: bundle not found';
    }
    $_bd = array(
        'search'              => array(
            "_item_id in " . $_rt["{$field}_item_bundle"],
            "bundle_item_price > 0"
        ),
        'order_by'            => array(
            'bundle_title' => 'ASC'
        ),
        'exclude_jrUser_keys' => true,
        'ignore_pending'      => true,
        'limit'               => 5
    );
    $_bd = jrCore_db_search_items('jrBundle', $_bd);
    if ($_bd && is_array($_bd) && isset($_bd['_items'])) {
        foreach ($_bd['_items'] as $k => $_item) {
            $_pm                                  = array(
                'module'       => 'jrBundle',
                'field'        => 'bundle',
                'item'         => $_item,
                'quantity_max' => 1
            );
            $_bd['_items'][$k]['add_to_cart_url'] = smarty_function_jrPayment_add_to_cart_button($_pm, new stdClass());
        }
    }
    else {
        // The bundles for this item no longer exists - cleanup
        jrCore_db_delete_item_key($module, $item_id, "{$field}_item_bundle");
        return 'ERROR: bundle not found';
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
        'item_price' => (isset($_rt["{$field}_item_price"])) ? $_rt["{$field}_item_price"] : 0
    );
    if (isset($_rt["{$field}_item_price"]) && $_rt["{$field}_item_price"] > 0) {
        $_bd['item_info']['add_to_cart_url'] = smarty_function_jrPayment_add_to_cart_button($_pm, new stdClass());
    }
    if (isset($_rt["{$prefix}_bundle_only"]) && $_rt["{$prefix}_bundle_only"] == 'on') {
        $_bd['item_info']['bundle_only'] = 'on';
    }
    else {
        $_bd['item_info']['bundle_only'] = 'off';
    }
    $_bd['module']   = $module;
    $_bd['field']    = $field;
    $_bd['_item_id'] = $item_id;
    $_bd['class']    = "{$module}-{$field}-{$item_id}";
    $_bd['item']     = $_rt;
    return jrCore_parse_template('bundle_display.tpl', $_bd, 'jrBundle');
}

//--------------------------------
// update
//--------------------------------
function view_jrBundle_update($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrPayment');

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid bundle_id - please try again');
        jrCore_location('referrer');
    }
    $_rt = jrCore_db_get_item('jrBundle', $_post['id']);
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

    // Bundle Image
    $_tmp = array(
        'name'     => 'bundle_image',
        'label'    => 48,
        'help'     => 49,
        'text'     => 50,
        'type'     => 'image',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrBundle_update_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();

    // Validate all incoming posted data
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrPayment');

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid bundle_id - please try again');
        jrCore_form_result('referrer');
    }

    // Get data
    $_rt = jrCore_db_get_item('jrBundle', $_post['id']);
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
    $_sv = jrCore_form_get_save_data('jrBundle', 'update', $_post);

    // Add in our SEO URL names
    $_sv['bundle_title_url'] = jrCore_url_string($_sv['bundle_title']);

    // Save any uploaded media file
    jrCore_save_all_media_files('jrBundle', 'update', $_user['user_active_profile_id'], $_post['id']);

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'update', 'jrBundle', $_post['id']);

    jrCore_db_update_item('jrBundle', $_post['id'], $_sv);
    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$_post['id']}/{$_sv['bundle_title_url']}");
}

//----------------------------------------------------
// remove_save
//----------------------------------------------------
function view_jrBundle_remove_save($_post, &$_user, &$_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();

    jrUser_check_quota_access('jrBundle');

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
    $_rt = jrCore_db_get_item('jrBundle', $bid);
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
    }
    $_dt = array(
        'bundle_list'  => json_encode($_rt['bundle_list']),
        'bundle_count' => (intval($_rt['bundle_count']) - 1)
    );
    jrCore_db_update_item('jrBundle', $_post['bundle_id'], $_dt);

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
    jrCore_trigger_event('jrBundle', 'delete_bundle_item', $_it, $_post);

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
function view_jrBundle_delete($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrPayment');

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid bundle_id - please try again');
        jrCore_location('referrer');
    }
    $_rt = jrCore_db_get_item('jrBundle', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid bundle_id - please try again');
        jrCore_location('referrer');
    }
    // Make sure the calling user has permissions to remove this action
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Delete the bundle and remove it from any items that were part of it
    if (jrCore_db_delete_item('jrBundle', $_post['id'])) {

        // We need to go get the items associated with this bundle
        $_xtra = array('module' => 'jrPayment');
        $_form = jrCore_trigger_event('jrPayment', 'add_price_field', array(), $_xtra);
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
// add to/create new bundle
//------------------------------
function view_jrBundle_select($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrBundle');

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
    $_rep                  = jrCore_db_search_items('jrBundle', $_sp);
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
    return jrCore_parse_template('bundle_select.tpl', $_rep, 'jrBundle');
}

//------------------------------
// add to bundle
//------------------------------
function view_jrBundle_add_save($_post, &$_user, &$_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrBundle');

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
            $iid => array($_post['field'], 0)
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

    $_it = jrCore_db_get_item($mod, $iid, true);
    if (!$_it || !is_array($_it)) {
        $out = array(
            'type' => 'error',
            'note' => 'invalid item - data not found'
        );
        jrCore_json_response($out);
    }

    $_rt = array(
        'bundle_title'      => $title,
        'bundle_title_url'  => jrCore_url_string($title),
        'bundle_item_price' => $_post['bundle_price'],
        'bundle_list'       => $_bi,
        'bundle_count'      => 1,
        'bundle_name'       => jrCore_url_string($title) . '.zip',
        'bundle_extension'  => 'zip'
    );

    // Send out add_bundle_item trigger
    $_rt = jrCore_trigger_event('jrBundle', 'add_bundle_item', $_rt, $_post);
    if (isset($_rt['bundle_list']) && is_array($_rt['bundle_list'])) {
        $_rt['bundle_list'] = json_encode($_rt['bundle_list']);
    }

    $bid = jrCore_db_create_item('jrBundle', $_rt);
    if (!$bid) {
        $out = array(
            'type' => 'error',
            'note' => 'Could not save to the database'
        );
        jrCore_json_response($out);
    }

    // Update item with bundle info
    $_id       = (isset($_it["{$_post['field']}_item_bundle"])) ? explode(',', $_it["{$_post['field']}_item_bundle"]) : array();
    $_id[$bid] = $bid;
    $_up       = array("{$_post['field']}_item_bundle" => implode(',', $_id));
    jrCore_db_update_item($mod, $iid, $_up);

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'create', 'jrBundle', $bid);

    jrProfile_reset_cache();
    $out = array(
        'type' => 'success',
        'note' => 'New bundle successfully created'
    );
    jrCore_json_response($out);
}

//----------------------------------------------------
// update the contents of a bundle
//----------------------------------------------------
function view_jrBundle_inject_save($_post, &$_user, &$_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    jrUser_check_quota_access('jrBundle');

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
    $_rt = jrCore_db_get_item('jrBundle', $bid);
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    $ord = 0;
    $_tm = array();
    if (isset($_rt['bundle_list']) && strlen($_rt['bundle_list']) > 1) {
        $_tm = json_decode($_rt['bundle_list'], true);
        // New items go at the bottom
        if (count($_tm) > 0) {
            foreach ($_tm as $m => $e) {
                $ord += count($e);
            }
        }
    }
    if (!isset($_tm[$mod])) {
        $_tm[$mod] = array();
    }
    $_tm[$mod][$iid]    = array($_post['field'], $ord);
    $_rt['bundle_list'] = $_tm;
    unset($_tm);

    // Send out add_bundle_item trigger
    $_rt = jrCore_trigger_event('jrBundle', 'add_bundle_item', $_rt, $_post);

    // Add item to bundle
    $_sv = array(
        'bundle_title'     => $_rt['bundle_title'],
        'bundle_title_url' => jrCore_url_string($_rt['bundle_title']),
        'bundle_list'      => json_encode($_rt['bundle_list']),
        'bundle_count'     => $ord
    );
    jrCore_db_update_item('jrBundle', $bid, $_sv);
    jrBundle_delete_bundle_zip_files($bid);

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

    jrProfile_reset_cache($_user['user_active_profile_id'], 'jrBundle');
    jrUser_reset_cache($_user['_user_id'], 'jrBundle');
    $out = array(
        'type' => 'success',
        'note' => 'Item added to bundle'
    );
    jrCore_json_response($out);
}

//----------------------------------
// update the order of a bundle
//----------------------------------
function view_jrBundle_order_update($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        return jrCore_json_response(array('error', 'invalid bundle id received'));
    }
    if (!isset($_post['bundle_order']) || !is_array($_post['bundle_order'])) {
        return jrCore_json_response(array('error', 'invalid bundle_order array received'));
    }
    jrCore_set_flag('jrbundle_skip_trigger', 1);
    $_pl = jrCore_db_get_item('jrBundle', $_post['id']);
    if (!$_pl || !is_array($_pl)) {
        return jrCore_json_response(array('error', 'invalid bundle - unable to load data'));
    }
    if (!jrUser_can_edit_item($_pl)) {
        return jrCore_json_response(array('error', 'permission denied'));
    }
    $_bi = array();
    if (isset($_pl['bundle_list']) && strlen($_pl['bundle_list']) > 0) {
        $_bi = json_decode($_pl['bundle_list'], true);
    }
    foreach ($_post['bundle_order'] as $k => $tmp) {
        list($mod, $iid) = explode('-', $tmp, 2);
        $_bi[$mod][$iid][1] = ($k + 1);
    }
    $_data = array(
        'bundle_list' => json_encode($_bi)
    );
    jrCore_db_update_item('jrBundle', $_post['id'], $_data);
    jrBundle_delete_bundle_zip_files($_post['id']);

    jrProfile_reset_cache($_user['user_active_profile_id'], 'jrBundle');
    jrUser_reset_cache($_user['_user_id'], 'jrBundle');

    return jrCore_json_response(array('success', 'bundle order successfully updated'));
}

//----------------------------------
// zip_in_progress
//----------------------------------
function view_jrBundle_zip_in_progress($_post, $_user, $_conf)
{
    $_ln = jrUser_load_lang_strings();
    jrCore_notice_page('success', '<p>' . $_ln['jrBundle'][47] . '</p>', 'referrer', $_ln['jrCore'][87], false);
}

//----------------------------------
// check_zip
//----------------------------------
function view_jrBundle_check_zip($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid register_id');
        jrCore_location('referrer');
    }
    $rid = (int) $_post['id'];
    $_rt = jrPayment_get_register_entry_by_id($rid);
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid register_id - not found');
        jrCore_location('referrer');
    }
    if (!jrUser_is_admin() && $_rt['r_purchase_user_id'] != $_user['_user_id']) {
        jrUser_not_authorized();
    }
    $bid = $_rt['r_item_id'];
    $_bi = jrCore_db_get_item('jrBundle', $bid);
    if (!$_bi || !is_array($_bi)) {
        jrCore_set_form_notice('error', 'invalid register_id - bundle not found');
        jrCore_location('referrer');
    }

    // First - we have to check if this user purchased a bundle that
    // contains items that were deleted
    $zip = false;
    if (isset($_bi['bundle_deleted_list']) && strlen($_bi['bundle_deleted_list']) > 0) {
        // This bundle contains DELETED items - get them and see if any of
        // the items were deleted AFTER this user bought this bundle
        if ($_di = json_decode($_bi['bundle_deleted_list'], true)) {
            $_de = array();
            foreach ($_di as $m => $_inf) {
                foreach ($_inf as $did => $_dat) {
                    $_de[] = "(del_module = '" . jrCore_db_escape($m) . "' AND del_item_id = " . intval($did) . ")";
                }
            }
            if (count($_de) > 0) {
                $tbl = jrCore_db_table_name('jrBundle', 'deleted');
                $req = "SELECT * FROM {$tbl} WHERE " . implode(' OR ', $_de) . " AND del_time > " . intval($_rt['r_created']);
                $_cn = jrCore_db_query($req, 'NUMERIC');
                if ($_cn && is_array($_cn)) {
                    // We have items that were deleted AFTER we purchased - must have a unique ZIP file
                    $zip = "jrBundle_{$bid}_{$_user['_user_id']}.zip";
                }
            }
        }
    }
    if (!$zip) {
        $zip = "jrBundle_{$bid}.zip";
    }
    if (!jrCore_media_file_exists('system', $zip)) {

        // We've not been created yet - create queue so it gets created
        // and redirect user to let them know it is being built for them
        jrBundle_rebuild_bundle($_bi['_item_id'], $_rt);

    }
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/content/id={$_post['id']}");

}

//-----------------------------------
// download
//-----------------------------------
function view_jrBundle_download($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid bundle id');
        jrCore_location('referrer');
    }

    // Make sure this user has purchased this vault file
    $_it = jrCore_db_get_item('jrBundle', $_post['_1']);
    if (!$_it || !is_array($_it)) {
        jrCore_notice('error', 'Invalid bundle - not found');
    }

    // Is this a paid bundle?
    if (isset($_it['bundle_item_price']) && $_it['bundle_item_price'] > 0) {
        // Only admins and profile owners can download a paid bundle
        if (!jrUser_can_edit_item($_it)) {
            jrCore_notice('error', 'It does not appear you have purchased this bundle - exiting');
        }
    }

    // "vault_download" event trigger
    $_it = jrCore_trigger_event('jrPayment', 'vault_download', $_it, array('module' => 'jrBundle'));
    $nam = $_it['vault_file'];
    $ttl = $_it['vault_name'];

    // Increment our counter
    jrCore_db_increment_key('jrBundle', $_it['_item_id'], "bundle_vault_download_count", 1);

    // Download the file to the client
    jrCore_media_file_download($_it['_profile_id'], $nam, $ttl);
    session_write_close();
    exit();
}

//-----------------------------------
// content (bundle contents)
//-----------------------------------
function view_jrBundle_content($_post, $_user, $_conf)
{
    jrUser_session_require_login();

    // We must get a register id of purchase
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid register id');
        jrCore_location('referrer');
    }

    $rid = (int) $_post['id'];
    $tbl = jrCore_db_table_name('jrPayment', 'register');
    $req = "SELECT * FROM {$tbl} WHERE r_id = {$rid} LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid register id - not found');
        jrCore_location('referrer');
    }
    if (!jrUser_is_admin() && $_rt['r_purchase_user_id'] != $_user['_user_id']) {
        jrUser_not_authorized();
    }
    // This has to be a bundle
    if ($_rt['r_module'] != 'jrBundle') {
        jrCore_set_form_notice('error', 'invalid register id - incorrect module');
        jrCore_location('referrer');
    }
    $_bi = jrCore_db_get_item('jrBundle', $_rt['r_item_id']);
    if (!$_bi || !is_array($_bi)) {
        jrCore_set_form_notice('error', 'invalid register id - bundle data not found');
        jrCore_location('referrer');
    }

    if (jrUser_is_admin() && isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz')) {
        $_us = jrCore_db_get_item('jrUser', $_post['user_id']);
        if (!isset($_us) || !is_array($_us)) {
            jrUser_account_tabs('purchases');
            jrCore_notice_page('error', 'invalid id - please pass in a valid user_id');
        }
        if ($_us['user_name'] != $_user['user_name']) {
            jrCore_set_form_notice('notice', "You are viewing the purchases for the user <strong>{$_us['user_name']}</strong>", false);
        }
    }
    else {
        $_us = $_user;
    }

    if (jrUser_is_admin()) {
        jrUser_account_tabs('purchases', $_us);
    }
    else {
        jrUser_account_tabs('purchases');
    }
    $_ln = jrUser_load_lang_strings();

    // List all items
    jrCore_page_banner($_bi['bundle_title']);
    jrCore_get_form_notice();

    $modurl = jrCore_get_module_url('jrPayment');
    $button = jrCore_page_button('dl-all', $_ln['jrBundle'][53], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$modurl}/download/{$_rt['r_id']}')");
    jrCore_page_notice('success', '<div class="p10">' . $button . '</div>', false);

    // Show Individual items
    $dat             = array();
    $dat[1]['title'] = '';
    $dat[1]['width'] = '2%';
    $dat[2]['title'] = $_ln['jrPayment'][7];
    $dat[2]['width'] = '78%';
    $dat[3]['title'] = $_ln['jrBundle'][52];
    $dat[3]['width'] = '15%';
    $dat[4]['title'] = $_ln['jrBundle'][51];
    $dat[4]['width'] = '5%';
    jrCore_page_table_header($dat);

    $_ip = array();
    $_pr = array();
    $_it = array();
    $_tm = json_decode($_bi['bundle_list'], true);
    foreach ($_tm as $mod => $_ids) {
        if ($_ti = jrCore_db_get_multiple_items($mod, array_keys($_ids))) {
            foreach ($_ti as $k => $v) {
                $pid = (int) $v['_profile_id'];
                $iid = (int) $v['_item_id'];
                if (isset($_tm[$mod][$iid][1])) {
                    $ord = $_tm[$mod][$iid][1];
                }
                else {
                    $ord = count($_it) + 1;
                }
                $_pr[$pid]           = $pid;
                $_it[$ord]           = $v;
                $_it[$ord]['module'] = $mod;
            }
        }
    }
    if (count($_pr) > 0) {
        if ($_pr = jrCore_db_get_multiple_items('jrProfile', $_pr)) {
            foreach ($_pr as $v) {
                $pid       = (int) $v['_item_id'];
                $_ip[$pid] = $v;
            }
        }
    }

    foreach ($_it as $k => $_item) {

        $mod = $_item['module'];
        $pid = (int) $_item['_profile_id'];
        $iid = (int) $_item['_item_id'];
        $fld = $_tm[$mod][$iid][0];
        $pfx = jrCore_db_get_prefix($mod);
        $_im = array(
            'crop'   => 'auto',
            'alt'    => $_item["{$pfx}_title"],
            'title'  => $_item["{$pfx}_title"],
            'width'  => 48,
            'height' => 48,
            '_v'     => (isset($_item["{$pfx}_image_time"]) && $_item["{$pfx}_image_time"] > 0) ? $_item["{$pfx}_image_time"] : time()
        );

        $dat             = array();
        $dat[1]['title'] = jrImage_get_image_src($_item['module'], "{$pfx}_image", $_item['_item_id'], 'small', $_im);

        $item_url        = "{$_conf['jrCore_base_url']}/" . $_ip[$pid]['profile_url'] . '/' . jrCore_get_module_url($mod) . "/{$_item['_item_id']}";
        $dat[2]['title'] = '<a href="' . $item_url . '">' . $_item["{$pfx}_title"] . '</a><br>@' . $_ip[$pid]['profile_url'] . ' - ' . $_ln[$mod]['menu'];
        $dat[3]['title'] = jrCore_format_size($_item["{$fld}_size"]);
        $dat[3]['class'] = 'center';
        $dat[4]['title'] = jrCore_page_button("download-{$k}", $_ln['jrPayment'][37], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/item_download/{$_bi['_item_id']}/{$_item['module']}/{$_item['_item_id']}')");
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button('referrer');
    jrCore_page_display();
}

//-----------------------------------
// bundle_item_download
//-----------------------------------
function view_jrBundle_item_download($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid bundle_id');
        jrCore_location('referrer');
    }
    $bid = (int) $_post['_1'];
    if (!isset($_post['_2'])) {
        jrCore_set_form_notice('error', 'invalid module');
        jrCore_location('referrer');
    }
    $mod = $_post['_2'];
    if (!isset($_post['_3']) || !jrCore_checktype($_post['_3'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid item_id');
        jrCore_location('referrer');
    }
    $iid = (int) $_post['_3'];
    $_bi = jrCore_db_get_item('jrBundle', $bid);
    if (!$_bi || !is_array($_bi)) {
        jrCore_set_form_notice('error', 'invalid bundle_id - no data');
        jrCore_location('referrer');
    }
    $_tm = json_decode($_bi['bundle_list'], true);
    if (!$_tm || !is_array($_tm)) {
        jrCore_set_form_notice('error', 'invalid bundle_id - no bundle items found');
        jrCore_location('referrer');
    }
    if (!isset($_tm[$mod][$iid])) {
        // Is it a deleted item?
        $error = true;
        if (isset($_bi['deleted_list']) && strlen($_bi['deleted_list']) > 0) {
            $_dl = json_decode($_bi['deleted_list']);
            if ($_dl && is_array($_dl) && isset($_dl[$mod][$iid])) {
                // This is a DELETED ITEM - check that this user has access
                $error           = false;
                $_tm[$mod][$iid] = $_dl[$mod][$iid];
            }
        }
        if ($error) {
            jrCore_set_form_notice('error', 'invalid item_id - not found in bundle');
            jrCore_location('referrer');
        }
    }

    if (!isset($_dl[$mod][$iid])) {
        $_it = jrCore_db_get_item($mod, $iid);
        if (!$_it || !is_array($_it)) {
            jrCore_set_form_notice('error', 'invalid item_id - data not found');
            jrCore_location('referrer');
        }
    }
    else {
        $tbl = jrCore_db_table_name('jrBundle', 'deleted');
        $req = "SELECT * FROM {$tbl} WHERE del_module = '" . jrCore_db_escape($mod) . "' AND del_item_id = {$iid} LIMIT 1";
        $_cn = jrCore_db_query($req, 'SINGLE');
        if ($_cn && is_array($_cn)) {
            $_it = json_decode($_cn['deL_data'], true);
        }
        else {
            jrCore_set_form_notice('error', 'invalid item_id - data not found (2)');
            jrCore_location('referrer');
        }
    }

    // When we get a VAULT download, we're going to be sending the
    // user the ORIGINAL file - not the down sampled copy.
    $fld = $_tm[$mod][$iid][0];
    if (isset($_it["{$fld}_original_extension"])) {
        // jrVideo_38_video_file.mov.original.mov
        $ext = $_it["{$fld}_original_extension"];
        $nam = "{$mod}_{$iid}_{$fld}.{$ext}.original.{$ext}";
    }
    else {
        // We don't have an "original" - i.e. no conversion was done
        $nam = "{$mod}_{$iid}_{$fld}." . $_it["{$fld}_extension"];
    }

    $pfx = jrCore_db_get_prefix($mod);
    if (isset($_it["{$pfx}_title_url"])) {
        $ttl = $_it["{$pfx}_title_url"];
    }
    elseif (isset($_it["{$pfx}_title"])) {
        $ttl = jrCore_url_string($_it["{$pfx}_title_url"]);
    }
    elseif (isset($_it["{$fld}_original_name"])) {
        $ttl = jrCore_url_string($_it["{$fld}_original_name"]);
    }
    elseif (isset($_it["{$fld}_name"])) {
        $ttl = jrCore_url_string($_it["{$fld}_name"]);
    }
    else {
        $ttl = $nam;
    }
    $ttl = pathinfo($ttl, PATHINFO_FILENAME) . '.' . $_it["{$fld}_extension"];

    // Increment our counter
    jrCore_db_increment_key($mod, $iid, "{$fld}_vault_download_count", 1);

    // Download the file to the client
    jrCore_media_file_download($_it['_profile_id'], $nam, $ttl);
    session_write_close();
    exit();
}
