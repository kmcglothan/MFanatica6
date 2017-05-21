<?php
/**
 * Jamroom 5 Batch Item Editor module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
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
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//---------------------------------------
// select datastore fields to edit
//---------------------------------------
function view_jrBatchEdit_batch($_post, $_user, $_conf)
{
    jrUser_master_only();
    $module = $_post['module'];

    $_meta = jrCore_module_meta_data($module);
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs($module);
    jrCore_page_banner($_meta['name'] . ' Batch Edit');

    // Form init
    $_tmp = array(
        'submit_value' => 'Edit Selected',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
    );
    jrCore_form_create($_tmp);

    // get all the keys in the datastore
    $_rt = jrCore_db_get_unique_keys($module);

    // checkboxes for columns to update
    if ($_rt && is_array($_rt)) {
        $keys            = false;
        $dat[1]['title'] = 'Select the field(s) you would like to edit';
        $dat[1]['width'] = '100%';
        jrCore_page_table_header($dat);
        foreach ($_rt as $key) {
            $keys[]  = $key;
            $exclude = array(
                '_item_id',
                '_user_id',
                '_profile_id',
                '_created',
                '_updated',
                '_image_extension',
                '_image_height',
                '_image_size',
                '_image_time',
                '_image_name',
                '_image_type',
                '_image_width',
                '_file_original_bitrate',
                '_file_original_extension',
                '_file_original_name',
                '_file_original_size',
                '_file_original_time',
                '_file_original_type',
                '_file_bitrate',
                '_file_extension',
                '_file_name',
                '_file_size',
                '_file_time',
                '_file_type',
                '_file_length',
                '_file_sample_length',
                '_file_smprate',
                '_file_track',
            );
            foreach ($exclude as $k) {
                if (strpos(' ' . $key, $k)) {
                    continue 2; // don't show the images as an option for editing, just show them if they exist in the results.
                }
            }
            if (substr($key, -4) == '_url') {
                if (in_array(substr($key, 0, -4), $_rt)) {
                    continue; // don't show auto-created _url fields. eg: gallery_title_url is created from gallery_title.  But profile_url is allowed, its not auto generated.
                }
            }
            $dat[1]['title'] = '<label><input type="checkbox" name="update_fields[' . $key . ']" value="' . $key . '"> ' . $key . '</label>';
            jrCore_page_table_row($dat);
        }
        jrCore_set_temp_value('jrBatchEdit', $module, $keys); // $_options
        jrCore_page_table_footer();

        // image size
        $_opt = array(
            'xsmall' => 'xsmall',
            'small'  => 'small',
            'medium' => 'medium',
            'large'  => 'large',
            'larger' => 'larger'
        );
        $_tmp = array(
            'name'     => 'image_size',
            'label'    => 'Image Size',
            'help'     => 'Select how large the item image should be displayed.',
            'type'     => 'select',
            'options'  => $_opt,
            'validate' => 'printable',
            'required' => true
        );
        jrCore_form_field_create($_tmp);

    }
    else {
        jrCore_page_notice('error', 'No DataStore fields found that can be batch edited');
    }
    jrCore_page_display();
}

//-----------------------------------------------------
// store the requested fields ( redirect to edit )
//-----------------------------------------------------
function view_jrBatchEdit_batch_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['update_fields']) || !is_array(($_post['update_fields']))) {
        jrCore_set_form_notice('error', 'Please select some fields to update');
        jrCore_form_result();
    }
    $key = false;
    // save the wanted fields to the db, then forward to display the results
    $pfx    = jrCore_db_get_prefix($_post['module']);
    $fields = array();
    foreach ($_post['update_fields'] as $v) {
        if (' ' . strpos($pfx, $v)) {
            $fields[] = $v;
        }
        $key = substr(md5(microtime()), 0, 16);
        if (empty($fields)) {
            jrCore_set_form_notice('error', 'Fields not found for this module.');
            jrCore_form_result();
        }
        $_data = array(
            'fields'     => $fields,
            'image_size' => jrCore_db_escape($_post['image_size'])
        );
        jrCore_set_temp_value('jrBatchEdit', $key, $_data);
    }

    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/batch_update/{$key}");

}

//---------------------------------
// batch edit selected fields
//---------------------------------
function view_jrBatchEdit_batch_update($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'string')) {
        jrCore_set_form_notice('error', 'key not found, please try again.');
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/batch");
    }
    $key     = $_post['_1'];
    $_data   = jrCore_get_temp_value('jrBatchEdit', $_post['_1']);
    $_fields = $_data['fields'];
    switch ($_data['image_size']) {
        case 'xsmall':
        case 'small':
        case 'medium':
        case 'large':
        case 'larger':
            $image_size = $_data['image_size'];
            break;
        default:
            $image_size = 'xsmall';
            break;

    }
    $col_width = round(52 / count($_fields));
    $_fields[] = '_item_id';
    $_fields[] = '_created';
    $module    = $_post['module'];
    $pfx       = jrCore_db_get_prefix($module);

    jrCore_page_banner('Batch Edit Items');
    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/batch_update/{$key}", '');

    $order_dir = 'desc';
    $order_opp = 'asc';
    if (isset($_post['order_dir']) && ($_post['order_dir'] == 'desc' || $_post['order_dir'] == 'numerical_desc')) {
        $order_dir = 'desc';
        $order_opp = 'asc';
    }
    elseif (isset($_post['order_dir']) && ($_post['order_dir'] == 'asc' || $_post['order_dir'] == 'numerical_asc')) {
        $order_dir = 'asc';
        $order_opp = 'desc';
    }

    $order_by = '_item_id';
    if (isset($_post['order_by'])) {
        switch ($_post['order_by']) {
            case '_item_id';
            case '_created';
                $order_dir = 'numerical_' . $order_dir;
                $order_opp = 'numerical_' . $order_opp;
            default:
                $order_by = $_post['order_by'];
                break;
        }
    }

    // get our items
    $_pr = array(
        'search'         => array(),
        'pagebreak'      => (isset($_COOKIE['jrcore_pager_rows']) && jrCore_checktype($_COOKIE['jrcore_pager_rows'], 'number_nz')) ? (int) $_COOKIE['jrcore_pager_rows'] : 12,
        'page'           => 1,
        'order_by'       => array(
            $order_by => $order_dir
        ),
        'skip_triggers'  => true,
        'ignore_pending' => true,
        'no_cache'       => true,
        'privacy_check'  => false
    );
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $_pr['page'] = (int) $_post['p'];
    }
    // See we have a search condition
    $_ex = false;
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $_ex = array('search_string' => $_post['search_string']);
        // Check for passing in a specific key name for search
        if (strpos($_post['search_string'], ':')) {
            list($sf, $ss) = explode(':', $_post['search_string'], 2);
            $_post['search_string'] = $ss;
            if (is_numeric($ss)) {
                $_pr['search'][] = "{$sf} = {$ss}";
            }
            else {
                $_pr['search'][] = "{$sf} like {$ss}%";
            }
        }
        else {
            $_tmp = array();
            foreach ($_fields as $field) {
                $_tmp[] = "{$field} like %{$_post['search_string']}%";
            }
            $_search         = implode(" || ", $_tmp);
            $_pr['search'][] = $_search;
        }
    }
    $_pr['return_keys'] = $_fields;
    $_rt                = jrCore_db_search_items($module, $_pr);

    // Start our output
    $url             = $_conf['jrCore_base_url'] . jrCore_strip_url_params($_post['_uri'], array('order_by', 'order_dir'));
    $dat             = array();
    $dat[1]['title'] = 'img';
    $dat[1]['width'] = '5%';
    $i               = 2;
    foreach ($_fields as $field) {
        switch ($field) {
            case '_created':
                $dat[$i]['title'] = '<a href="' . $url . '/order_by=' . $field . '/order_dir=' . $order_opp . '">created</a>';
                $dat[$i]['width'] = '16%';
                break;
            case '_item_id':
                $dat[$i]['title'] = '<a href="' . $url . '/order_by=' . $field . '/order_dir=' . $order_opp . '">Item ID</a>';
                $dat[$i]['width'] = '8%';
                break;
            default:
                $dat[$i]['title'] = '<a href="' . $url . '/order_by=' . $field . '/order_dir=' . $order_opp . '">' . $field . '</a>';
                $dat[$i]['width'] = "{$col_width}%";
                break;
        }
        $i++;
    }
    $i++;
    $dat[$i]['title'] = 'user account(s)';
    $dat[$i]['width'] = '14%';
    $i++;
    $dat[$i]['title'] = '<label>delete <input type="checkbox" id="delete_all" value="" onclick="jrBatchEdit_delete_checkbox_all()"></label>';
    $dat[$i]['width'] = '5%';

    jrCore_page_table_header($dat, 'batch-edit-table-header');

    if (isset($_rt['_items']) && is_array($_rt['_items'])) {

        // Get user info for these items
        $_pi = array();
        foreach ($_rt['_items'] as $_usr) {
            $_pi[] = (int) $_usr['_profile_id'];
        }

        $tbl = jrCore_db_table_name('jrProfile', 'profile_link');
        $req = "SELECT * FROM {$tbl} WHERE profile_id IN(" . implode(',', $_pi) . ")";
        $_ui = jrCore_db_query($req, 'NUMERIC');
        if ($_ui && is_array($_ui)) {

            $_id = array();
            foreach ($_ui as $v) {
                $_id["{$v['user_id']}"] = $v['user_id'];
            }

            // get users
            $_pr = array(
                'search'         => array(
                    '_user_id in ' . implode(',', $_id)
                ),
                'return_keys'    => array('_profile_id', '_user_id', 'user_name', 'user_email', 'user_group', 'user_image_time', 'user_active'),
                'skip_triggers'  => true,
                'ignore_pending' => true,
                'privacy_check'  => false,
                'no_cache'       => true,
                'limit'          => 100
            );
            $_pi = jrCore_db_search_items('jrUser', $_pr);
            if (isset($_pi['_items']) && is_array($_pi['_items'])) {
                $_ud = array();
                foreach ($_pi['_items'] as $_usr) {
                    $_ud["{$_usr['_user_id']}"] = $_usr;
                }
                unset($_pi);
                $_pr = array();
                $url = jrCore_get_module_url('jrUser');
                foreach ($_ui as $v) {
                    $uid = (int) $v['user_id'];
                    if (!isset($_pr["{$v['profile_id']}"])) {
                        $_pr["{$v['profile_id']}"] = array();
                    }
                    $_pr["{$v['profile_id']}"][] = "<a href=\"{$_conf['jrCore_base_url']}/{$url}/account/user_id={$uid}\">{$_ud[$uid]['user_name']}</a>";
                }
            }
            unset($_pi);
        }
        $tx = 900;
        foreach ($_rt['_items'] as $_itm) {

            // profile/user doesn't have an _item_id need to add it back.
            switch ($module) {
                case 'jrProfile':
                    if (isset($_itm['_profile_id']) && !isset($_itm['_item_id'])) {
                        $_itm['_item_id'] = $_itm['_profile_id'];
                    }
                    break;
                case 'jrUser':
                    if (isset($_itm['_user_id']) && !isset($_itm['_item_id'])) {
                        $_itm['_item_id'] = $_itm['_user_id'];
                    }
                    break;
            }

            $dat             = array();
            $_im             = array(
                'crop'  => 'auto',
                'alt'   => $_itm[$pfx . '_image_title'],
                'title' => $_itm[$pfx . '_image_title'],
                '_v'    => (isset($_itm[$pfx . '_image_time']) && $_itm[$pfx . '_image_time'] > 0) ? $_itm[$pfx . '_image_time'] : false
            );
            $dat[1]['title'] = jrImage_get_image_src($module, $pfx . '_image', $_itm['_item_id'], $image_size, $_im);
            $i               = 2;
            foreach ($_fields as $field) {
                if ($field == '_item_id') {
                    $dat[$i]['title'] = $_itm[$field];
                    $dat[$i]['class'] = 'center';
                    $i++;
                    continue;
                }
                elseif ($field == '_created') {
                    $dat[$i]['title'] = jrCore_format_time($_itm[$field]);
                    $dat[$i]['class'] = 'center';
                    $i++;
                    continue;
                }
                if (jrCore_checktype($_itm['_item_id'], 'number_nz') && strip_tags($_itm[$field]) == $_itm[$field] && strlen($_itm[$field]) <= 250) {
                    $dat[$i]['title'] = '<input type="text" name="' . $field . '[' . $_itm['_item_id'] . ']" value="' . $_itm[$field] . '" tabindex="' . $tx . '" class="form_text" style="width:auto">';
                }
                else {
                    $dat[$i]['title'] = '<i>Too long or contains HTML</i>';
                }
                $i++;
            }
            $i++;
            $dat[$i]['title'] = (isset($_pr["{$_itm['_profile_id']}"])) ? implode('<br>', $_pr["{$_itm['_profile_id']}"]) : '-';
            $dat[$i]['class'] = 'center';
            $i++;
            $dat[$i]['title'] = '<input type="checkbox" class="delete_id" name="delete_id[' . $_itm['_item_id'] . ']" value="' . $_itm['_item_id'] . '">';
            $dat[$i]['class'] = 'center';

            jrCore_page_table_row($dat, 'batch-edit-table-footer');
        }
        jrCore_page_table_pager($_rt, $_ex);
    }
    else {
        $dat = array();
        if (isset($_post['search_string'])) {
            $dat[1]['title'] = '<p>No Results found for your Search Criteria.</p>';
        }
        else {
            $dat[1]['title'] = '<p>No User Profiles found!</p>';
        }
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer(null, 'batch-edit-table-footer');

    // Form init
    $_tmp = array(
        'submit_value' => 'Save Changes to Datastore',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'onclick'      => "jrc=jrBatchEdit_confirm_delete();",

    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'  => "batch_key",
        'type'  => 'hidden',
        'value' => $key
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// batch edit update datastore
//------------------------------
function view_jrBatchEdit_batch_update_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!jrCore_module_is_active($_post['module'])) {
        jrCore_set_form_notice('error', 'That module is not active.');
        jrCore_form_result();
    }
    $module   = $_post['module'];
    $_meta    = jrCore_module_meta_data($module);
    $_data    = jrCore_get_temp_value('jrBatchEdit', $_post['batch_key']);
    $_fields  = $_data['fields'];
    $_options = jrCore_get_temp_value('jrBatchEdit', $module);
    if (!isset($_fields) || !is_array($_fields)) {
        jrCore_set_form_notice('error', 'Fields could not be found.');
        jrCore_form_result();
    }

    $_up = array();
    foreach ($_fields as $ds_field) {
        if (isset($_post[$ds_field]) && is_array($_post[$ds_field])) {
            foreach ($_post[$ds_field] as $item_id => $v) {
                if (strip_tags($v) !== $v || strlen($v) >= 250) {
                    jrCore_set_form_notice('error', 'Input contained HTML or sting too long ( 250 characters max ) for Item ID: ' . $item_id);
                    jrCore_form_result();
                }
                $_up[$item_id][$ds_field] = jrCore_db_escape($v);
                // see if the _url key exists
                foreach ($_options as $f => $o) {
                    if (strpos(' ' . $o, $ds_field . '_url')) {
                        $_up[$item_id][$ds_field . '_url'] = jrCore_url_string($v); // update _url too
                        break;
                    }
                }
            }
        }
    }

    if (count($_up) > 0) {
        jrCore_db_update_multiple_items($module, $_up);
        jrCore_set_form_notice('notice', $_meta['name'] . ' items have been updated.');
    }

    // check for any delete items
    if (isset($_post['delete_id']) && is_array($_post['delete_id'])) {
        $ct = count($_post['delete_id']);
        jrCore_set_form_notice('notice', $ct . ' items have been deleted.');
        jrCore_db_delete_multiple_items($module, array_keys($_post['delete_id']));
    }

    jrCore_delete_temp_value('jrBatchEdit', $_post['batch_key']);
    jrCore_delete_temp_value('jrBatchEdit', $module);
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/batch");
}
