<?php
/**
 * Jamroom Profile Tweaks module
 *
 * copyright 2017 The Jamroom Network
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
// customize
//------------------------------
function view_jrProfileTweaks_customize($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_session_require_login();
    jrUser_check_quota_access('jrProfileTweaks');

    if (!isset($_post['profile_id']) || !jrCore_checktype($_post['profile_id'], 'number_nz')) {
        $_post['profile_id'] = jrUser_get_profile_home_key('_profile_id');
    }

    // We need to make sure the viewing user has access to this profile
    if (!jrProfile_is_profile_owner($_post['profile_id'])) {
        jrUser_not_authorized();
    }

    // Check for admin/power user working on an owned profile
    if ((jrUser_is_admin() || jrUser_is_power_user() || jrUser_is_multi_user()) && isset($_post['profile_id']) && jrCore_checktype($_post['profile_id'], 'number_nz')) {
        $_profile = jrCore_db_get_item('jrProfile', $_post['profile_id'], true, true);
        $_quota   = jrProfile_get_quota($_profile['profile_quota_id']);
        $_profile = array_merge($_profile, $_quota);
    }
    else {
        $_profile = jrCore_db_get_item('jrProfile', $_user['_profile_id'], true, true);
        $_quota   = jrProfile_get_quota($_profile['profile_quota_id']);
        $_profile = array_merge($_profile, $_quota);
    }

    if (!$_profile || !is_array($_profile)) {
        jrCore_notice_page('error', 41);
    }

    jrUser_account_tabs('customize', $_profile);

    $_ln = jrUser_load_lang_strings();
    jrCore_page_banner(2, jrCore_page_button('p', $_profile['profile_name'], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_profile['profile_url']}')"));
    jrCore_get_form_notice();

    if ($_profile['_profile_id'] != jrUser_get_profile_home_key('_profile_id')) {
        jrCore_set_form_notice('notice', "{$_ln['jrProfile'][35]} <strong>{$_profile['profile_name']}</strong>", false);
    }

    // Form init
    $_tmp = array(
        'submit_value'     => 3,
        'cancel'           => jrCore_is_profile_referrer(),
        'form_ajax_submit' => false,
        'values'           => $_profile
    );
    jrCore_form_create($_tmp);

    // Profile ID
    $_tmp = array(
        'name'  => 'pid',
        'type'  => 'hidden',
        'value' => intval($_profile['_profile_id'])
    );
    jrCore_form_field_create($_tmp);

    if (isset($_profile['quota_jrProfileTweaks_allow_logo']) && $_profile['quota_jrProfileTweaks_allow_logo'] == 'on') {
        // Custom Site Logo
        $_tmp = array(
            'name'         => 'profile_logo_image',
            'label'        => 16,
            'help'         => 17,
            'text'         => 18,
            'type'         => 'image',
            'allowed'      => 'jpg,jpeg,jfif,jpe,png,gif',
            'max'          => 262144,
            'image_module' => 'jrProfile',
            'image_delete' => true,
            'required'     => false
        );
        if (isset($_profile['profile_logo_image_time']) && jrCore_checktype($_profile['profile_logo_image_time'], 'number_nz')) {
            $_tmp['unique'] = $_profile['profile_logo_image_time'];
        }
        jrCore_form_field_create($_tmp);
    }

    if (isset($_profile['quota_jrProfileTweaks_allow_background_image']) && $_profile['quota_jrProfileTweaks_allow_background_image'] == 'on') {
        // Custom Background
        $_tmp = array(
            'name'         => 'profile_bg_image',
            'label'        => 4,
            'help'         => 6,
            'text'         => 7,
            'type'         => 'image',
            'allowed'      => 'jpg,jpeg,jfif,jpe',
            'image_module' => 'jrProfile',
            'image_delete' => true,
            'required'     => false
        );
        if (isset($_profile['profile_bg_image_time']) && jrCore_checktype($_profile['profile_bg_image_time'], 'number_nz')) {
            $_tmp['unique'] = $_profile['profile_bg_image_time'];
        }
        jrCore_form_field_create($_tmp);

        // Tile Image
        $_tmp = array(
            'name'     => 'profile_bg_tile',
            'label'    => 9,
            'help'     => 10,
            'type'     => 'checkbox',
            'default'  => 'off',
            'validate' => 'onoff',
            'required' => true
        );
        jrCore_form_field_create($_tmp);
    }

    if (isset($_profile['quota_jrProfileTweaks_allow_index_redirect']) && $_profile['quota_jrProfileTweaks_allow_index_redirect'] == 'on') {
        // Default Index page
        $_idx = array('0' => $_ln['jrProfileTweaks'][15]);

        // Get profile options this quota has access to
        foreach (array_keys($_mods) as $module) {
            switch ($module) {
                case 'jrCore':
                case 'jrProfile':
                case 'jrUser':
                case 'jrMailer':
                case 'jrComment':
                    continue 2;
                    break;
                default:
                    // Module is NOT active
                    if (!jrCore_module_is_active($module)) {
                        continue 2;
                    }
                    // See if this Profile's Quota allows access to the module
                    if (isset($_profile["quota_{$module}_allowed"]) && $_profile["quota_{$module}_allowed"] != 'on') {
                        continue 2;
                    }
                    // Our module must have an item_index.tpl file...
                    if (!is_file(APP_DIR . "/modules/{$module}/templates/item_index.tpl")) {
                        continue 2;
                    }
                    $_idx[$module] = $_mods[$module]['module_name'];
                    break;
            }
        }

        if (count($_idx) > 0) {
            natcasesort($_idx);
        }

        // Add in pages
        if (jrCore_module_is_active('jrPage') && isset($_profile['quota_jrPage_allowed']) && $_profile['quota_jrPage_allowed'] == 'on') {
            $_sc = array(
                'search'        => array(
                    "_profile_id = {$_profile['_profile_id']}",
                    "page_location = 1"
                ),
                'return_keys'   => array('_item_id', 'page_title'),
                'skip_triggers' => true,
                'privacy_check' => false,
                'limit'         => 100
            );
            $_pg = jrCore_db_search_items('jrPage', $_sc);
            if ($_pg && is_array($_pg) && isset($_pg['_items']) && is_array($_pg['_items'])) {
                $_ln = jrUser_load_lang_strings();
                foreach ($_pg['_items'] as $_itm) {
                    $_idx["jrPage:{$_itm['_item_id']}"] = "{$_ln['jrProfileTweaks'][14]}: {$_itm['page_title']}";
                }
            }
        }

        if (count($_idx) > 0) {
            $_tmp = array(
                'name'     => 'profile_index_page',
                'label'    => 11,
                'help'     => 12,
                'type'     => 'select',
                'options'  => $_idx,
                'default'  => (isset($_profile['quota_jrProfileTweaks_default_index'])) ? $_profile['quota_jrProfileTweaks_default_index'] : '0',
                'value'    => (isset($_profile['profile_index_page'])) ? $_profile['profile_index_page'] : '0',
                'required' => false
            );
            jrCore_form_field_create($_tmp);
        }
    }

    // Custom Skin
    if (isset($_profile['quota_jrProfileTweaks_allow_skin']) && strlen($_profile['quota_jrProfileTweaks_allow_skin']) > 0) {
        $_sk = explode(',', $_profile['quota_jrProfileTweaks_allow_skin']);
        if ($_sk && is_array($_sk) && count($_sk) > 0) {
            $_opt = array();
            foreach ($_sk as $skin) {
                $_tm = jrCore_skin_meta_data($skin);
                if ($_tm && is_array($_tm)) {
                    $_opt[$skin] = $_tm['title'];
                }
            }

            // If a QUOTA CONFIG default has been set, its the default
            if (strpos(' ,' . $_profile['quota_jrProfileTweaks_allow_skin'] . ',', ',' . $_profile['quota_jrProfileTweaks_default_skin'] . ',')) {
                $_opt[$_profile['quota_jrProfileTweaks_default_skin']] .= ' ' . $_ln['jrProfileTweaks'][21];
                $default = $_profile['quota_jrProfileTweaks_default_skin'];

                // a custom skin had been set, but its no longer an option in this quota
                if (!strpos(' ,' . $_profile['quota_jrProfileTweaks_allow_skin'] . ',', ',' . $_profile['profile_custom_skin'] . ',')) {
                    $_profile['profile_custom_skin'] = $default;
                }
            }
            else {
                // otherwise, if Admin Skin is active and set, its the default
                if (jrCore_module_is_active('jrAdminSkin') && isset($_conf['jrAdminSkin_profile_skin']) && $_conf['jrAdminSkin_profile_skin'] != $_conf['jrCore_active_skin']) {
                    $default = $_conf['jrAdminSkin_profile_skin'];
                }
                else {
                    // or else fall back to the main skin as default.
                    $default = $_conf['jrCore_active_skin'];
                }

                if (!isset($_opt[$default])) {
                    $_tm            = jrCore_skin_meta_data($default);
                    $_opt[$default] = $_tm['title'];
                }
                $_opt[$default] .= ' ' . $_ln['jrProfileTweaks'][21];
            }

            $_tmp = array(
                'name'     => 'profile_custom_skin',
                'label'    => 19,
                'help'     => 20,
                'type'     => 'select',
                'options'  => $_opt,
                'default'  => $_conf['jrCore_active_skin'],
                'value'    => (isset($_profile['profile_custom_skin'])) ? $_profile['profile_custom_skin'] : $default,
                'required' => false
            );
            jrCore_form_field_create($_tmp);
        }
    }

    jrCore_page_display();
}

//------------------------------
// customize_save
//------------------------------
function view_jrProfileTweaks_customize_save($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrProfileTweaks');

    // See if we have an image
    $pid = $_user['user_active_profile_id'];
    if (jrUser_is_admin() && isset($_post['pid']) && jrCore_checktype($_post['pid'], 'number_nz')) {
        $pid = $_post['pid'];
    }

    // get info about this profile
    $_pr = jrCore_db_get_item('jrProfile', $pid);

    $_data = array();

    //-----------------------------
    // Logo
    //-----------------------------
    $_image = jrCore_get_uploaded_media_files('jrProfileTweaks', 'profile_logo_image');
    if (is_array($_image)) {

        if (is_file("{$_image[0]}.tmp")) {
            $name = file_get_contents("{$_image[0]}.tmp");
            if ($ext = jrCore_file_extension($name)) {

                // If we have an existing image, delete it first
                if (isset($_pr['profile_logo_image_size']) && jrCore_checktype($_pr['profile_logo_image_size'], 'number_nz')) {
                    jrCore_delete_media_file($pid, "jrProfile_{$pid}_profile_logo_image." . $_pr['profile_logo_image_extension']);
                }

                $_data['profile_logo_image_extension'] = $ext;
                $_data['profile_logo_image_name']      = $name;
                $_data['profile_logo_image_size']      = filesize($_image[0]);
                $_data['profile_logo_image_time']      = time();
                $_data['profile_logo_image_type']      = jrCore_mime_type($_image[0]);
                jrCore_write_media_file($pid, "jrProfile_{$pid}_profile_logo_image.{$ext}", $_image[0], 'public-read');
            }
        }
    }

    //-----------------------------
    // Background image
    //-----------------------------
    $_image = jrCore_get_uploaded_media_files('jrProfileTweaks', 'profile_bg_image');
    if (is_array($_image)) {

        if (is_file("{$_image[0]}.tmp")) {
            $name = file_get_contents("{$_image[0]}.tmp");
            if ($ext = jrCore_file_extension($name)) {

                // If we have an existing image, delete it first
                if (isset($_pr['profile_bg_image_size']) && jrCore_checktype($_pr['profile_bg_image_size'], 'number_nz')) {
                    jrCore_delete_media_file($pid, "jrProfile_{$pid}_profile_bg_image.jpg");
                }

                // We have an uploaded image - we don't want to save a massive background file,
                // so we're going to resize it here if it is larger than 2560px height/width
                list($iw, $ih) = getimagesize($_image[0]);
                if (isset($iw) && $iw > 2560 || isset($ih) && $ih > 2560) {
                    jrImage_resize_image($_image[0], $_image[0], 2560);
                }

                $_data['profile_bg_image_extension'] = 'jpg';
                $_data['profile_bg_image_name']      = $name;
                $_data['profile_bg_image_size']      = filesize($_image[0]);
                $_data['profile_bg_image_time']      = time();
                $_data['profile_bg_image_type']      = 'image/jpeg';
                jrCore_write_media_file($pid, "jrProfile_{$pid}_profile_bg_image.jpg", $_image[0], 'public-read');
            }
        }
    }

    // See if we are changing the tiling
    if (isset($_post['profile_bg_tile']{0})) {
        $_data['profile_bg_tile'] = $_post['profile_bg_tile'];
    }

    //-----------------------------
    // INDEX
    //-----------------------------
    if (isset($_user['quota_jrProfileTweaks_allow_index_redirect']) && $_user['quota_jrProfileTweaks_allow_index_redirect'] == 'on') {
        if (isset($_post['profile_index_page']) && strlen($_post['profile_index_page']) > 0) {
            $_data['profile_index_page'] = $_post['profile_index_page'];
        }
        else {
            jrCore_db_delete_item_key('jrProfile', $pid, 'profile_index_page');
        }
    }
    else {
        jrCore_db_delete_item_key('jrProfile', $pid, 'profile_index_page');
    }

    //-----------------------------
    // SKIN
    //-----------------------------
    if (isset($_user['quota_jrProfileTweaks_allow_skin']) && strlen($_user['quota_jrProfileTweaks_allow_skin']) > 0) {

        if (isset($_post['profile_custom_skin']) && strlen($_post['profile_custom_skin']) > 0 && $_post['profile_custom_skin'] != $_conf['jrCore_active_skin']) {

            if ($_post['profile_custom_skin'] == $_pr['quota_jrProfileTweaks_default_skin']) {
                jrCore_db_delete_item_key('jrProfile', $pid, 'profile_custom_skin');
            }
            else {
                $_data['profile_custom_skin'] = $_post['profile_custom_skin'];
            }

        }
        else {
            jrCore_db_delete_item_key('jrProfile', $pid, 'profile_custom_skin');
        }
    }
    else {
        jrCore_db_delete_item_key('jrProfile', $pid, 'profile_custom_skin');
    }

    if (count($_data) > 0) {
        jrCore_db_update_item('jrProfile', $pid, $_data);
    }

    jrCore_form_delete_session();
    jrProfile_reset_cache($pid);
    jrCore_set_form_notice('success', 13);
    jrCore_form_result();
}

//------------------------------
// logo
//------------------------------
function view_jrProfileTweaks_logo($_post, $_user, $_conf)
{
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice_page('error', 'invalid profile id');
        return false;
    }
    $dir = jrCore_get_media_directory($_post['_1']);
    $img = "{$dir}/jrProfile_{$_post['_1']}_profile_logo_image.{$_post['_2']}";
    if (!is_file($img)) {
        jrCore_notice_page('error', 'unable to open logo image file');
        return false;
    }
    // Show it
    $mime = jrCore_mime_type("image.{$_post['_2']}");
    header("Content-type: {$mime}");
    header('Content-Disposition: inline; filename="profile_logo_image.' . $_post['_2'] . '"');
    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 8640000));
    echo file_get_contents($img);
    session_write_close();
    jrCore_db_close();
    exit;
}

//------------------------------
// background
//------------------------------
function view_jrProfileTweaks_background($_post, $_user, $_conf)
{
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice_page('error', 'invalid profile id');
        return false;
    }
    $dir = jrCore_get_media_directory($_post['_1']);
    $img = "{$dir}/jrProfile_{$_post['_1']}_profile_bg_image.jpg";
    if (!is_file($img)) {
        jrCore_notice_page('error', 'unable to open background image file');
        return false;
    }
    // Show it
    header("Content-type: image/jpeg");
    header('Content-Disposition: inline; filename="profile_bg_image.jpg"');
    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 8640000));
    echo file_get_contents($img);
    session_write_close();
    jrCore_db_close();
    exit;
}
