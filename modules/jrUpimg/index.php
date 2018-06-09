<?php
/**
 * Jamroom Editor Image Upload module
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
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//------------------------------
// create_save
//------------------------------
function view_jrUpimg_create_save($_post, &$_user, &$_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrUpimg');

    // Get uploaded files
    $_files = jrCore_get_uploaded_media_files('jrUpimg', 'upimg_file');
    if (!$_files || !is_array($_files)) {
        $_ret = array(
            'success'     => false,
            'success_msg' => 'no file could be found.'
        );
        jrCore_json_response($_ret);
    }

    $aid    = false;
    $_items = array();
    foreach ($_files as $file_name) {
        $_rt = array(
            'upimg_title_url' => jrCore_url_string(basename($file_name))
        );
        $aid = jrCore_db_create_item('jrUpimg', $_rt);
        if (!$aid) {
            $_ret = array(
                'success'     => false,
                'success_msg' => 'datastore item creation for the file failed.'
            );
            jrCore_json_response($_ret);
        }
        jrCore_save_media_file('jrUpimg', $file_name, $_user['user_active_profile_id'], $aid);
        $_items[] = jrCore_db_get_item('jrUpimg', $aid);
    }

    // Clean up any file uploads so  multiple uploads don't re-process
    if (isset($_post['upload_token'])) {
        $cdir = jrCore_get_module_cache_dir('jrCore');
        if (is_dir("{$cdir}/{$_post['upload_token']}")) {
            jrCore_delete_dir_contents("{$cdir}/{$_post['upload_token']}");
            rmdir("{$cdir}/{$_post['upload_token']}");
        }
    }

    jrProfile_reset_cache();
    // remove the upload
    $_ret = array(
        'success'     => true,
        'success_msg' => 'file uploaded',
        'image_url'   => $_conf['jrCore_base_url'] . '/upimg/image/upimg_file/' . $aid,
        '_items'      => $_items
    );
    jrCore_json_response($_ret);
}

//------------------------------
// delete_image
//------------------------------
function view_jrUpimg_delete_image($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrUpimg');
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 14);
        jrCore_form_result('referrer');
    }
    $_rt = jrCore_db_get_item('jrUpimg', $_post['id']);
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    jrCore_db_delete_item('jrUpimg', $_post['id']);
    jrCore_logger('INF', "uploaded image id {$_post['id']} was successfully deleted");
    jrProfile_reset_cache();
    jrCore_form_result('referrer');
}

//---------------------------------------------
// Upimg Widget Config Body (loaded via ajax)
//---------------------------------------------
function view_jrUpimg_widget_config_body($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }

    $ss = array();

    // specific ids
    if (isset($_post['ids']) && $_post['ids'] !== "false" && $_post['ids'] !== "undefined" && $_post['ids'] !== "") {
        $ss[] = "_item_id IN {$_post['ids']}";
    }
    // search string
    if (isset($_post['sstr']) && $_post['sstr'] !== "false" && $_post['sstr'] !== "undefined" && $_post['sstr'] !== "") {
        if (strpos($_post['sstr'], ':')) {
            list($k,$v) = explode(':',$_post['sstr']);
            $ss[] = "{$k} = {$v}";
        }
        else {
            $ss[] = "upimg_% LIKE %{$_post['sstr']}%";
        }
    }

    // Create search params from $_post
    $_sp = array(
        'search'                       => $ss,
        'pagebreak'                    => 5,
        'page'                         => $_post['p'],
        'exclude_jrUser_keys'          => true,
        'exclude_jrProfile_quota_keys' => true,
        'order_by'                     => array('_created' => 'numerical_desc'),
    );

    $_rt = jrCore_db_search_items('jrUpimg', $_sp);
    return jrCore_parse_template('widget_config_body.tpl', $_rt, 'jrUpimg');
}
