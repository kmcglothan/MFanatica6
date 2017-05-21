<?php
/**
 * Jamroom Favicon Creator module
 *
 * copyright 2017 The Jamroom Network
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
// browse (browse existing)
//------------------------------
function view_jrFavicon_browse($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrFavicon', 'browse');
    jrCore_page_banner('Favicon Creator');
    if (!is_writeable(APP_DIR)) {
        jrCore_set_form_notice('notice', 'For compatibility with older browsers, download the active favicon and upload it to your Jamroom root directory');
    }
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value'     => 'save changes',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    $dat             = array();
    $dat[1]['title'] = 'favicon';
    $dat[1]['width'] = '30%';
    $dat[2]['title'] = 'created';
    $dat[2]['width'] = '25%';
    $dat[3]['title'] = 'size';
    $dat[3]['width'] = '25%';
    $dat[4]['title'] = 'active';
    $dat[4]['width'] = '10%';
    $dat[5]['title'] = 'delete';
    $dat[5]['width'] = '5%';
    $dat[6]['title'] = 'download';
    $dat[6]['width'] = '5%';
    jrCore_page_table_header($dat);

    // Get existing favicons
    $_icn = jrCore_get_media_files(0, 'favicon*');
    if ($_icn && is_array($_icn) && isset($_icn[0])) {

        $_rp = array(
            'favicon_' => '',
            '.ico'     => ''
        );
        foreach ($_icn as $k => $_file) {

            if (!strpos($_file['name'], 'favicon')) {
                continue;
            }
            $ext = jrCore_file_extension($_file['name']);
            if ($ext != 'ico') {
                continue;
            }

            $icon = basename($_file['name']);
            $name = (int) str_replace(array_keys($_rp), $_rp, $icon);

            $dat[1]['title'] = '<img src="' . jrCore_get_media_url(0) . "/{$icon}" . '" width="32" height="32" alt="' . jrCore_entity_string($icon) . '">';
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = jrCore_format_time($_file['time']);
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = jrCore_format_size($_file['size']);
            $dat[3]['class'] = 'center';
            if (isset($_conf['jrFavicon_active']) && $_conf['jrFavicon_active'] == $name) {
                $dat[4]['title'] = '<input type="radio" name="active" class="form_radio" value="' . $name . '" checked="checked">';
            }
            else {
                $dat[4]['title'] = '<input type="radio" name="active" class="form_radio" value="' . $name . '">';
            }
            $dat[4]['class'] = 'center';
            if (isset($_conf['jrFavicon_active']) && $_conf['jrFavicon_active'] == $name) {
                $dat[5]['title'] = jrCore_page_button("d{$k}", 'delete', 'disabled');
            }
            else {
                $dat[5]['title'] = jrCore_page_button("d{$k}", 'delete', "if(confirm('Are you sure you want to delete this favicon?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete_save/{$name}') }");
            }
            $dat[6]['title'] = jrCore_page_button("l{$k}", 'download', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/download/{$name}')");
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = "<p>no favicon images have been created yet - upload a new one below.</p>";
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    // Image
    $_tmp = array(
        'name'     => 'ico_image',
        'label'    => 'favicon image',
        'sublabel' => '(PNG, minimum 250x250)',
        'help'     => 'Upload a PNG image that will be used to create the favicon.ico file',
        'type'     => 'image',
        'allowed'  => 'png',
        'required' => false
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// browse_save
//------------------------------
function view_jrFavicon_browse_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // See if we have an image
    $_ico = jrCore_get_uploaded_media_files('jrFavicon', 'ico_image');
    if ($_ico && isset($_ico[0])) {

        // Create Favicon with 4 sizes
        require_once APP_DIR . '/modules/jrFavicon/contrib/php-ico/class-php-ico.php';
        $dir = jrCore_get_module_cache_dir('jrFavicon');
        $tag = time();
        $out = "{$dir}/favicon_{$tag}.ico";
        $ico = new PHP_ICO($_ico[0], array(array(16, 16), array(24, 24), array(32, 32), array(48, 48), array(64, 64)));
        $ico->save_ico($out);
        if (is_file($out)) {
            jrCore_write_media_file(0, "favicon_{$tag}.ico", $out, 'public-read');
            foreach (jrFavicon_get_image_sizes() as $size) {
                jrImage_resize_image($_ico[0], "{$dir}/favicon_{$tag}_{$size}.png", $size);
                if (is_file("{$dir}/favicon_{$tag}_{$size}.png")) {
                    jrCore_write_media_file(0, "favicon_{$tag}_{$size}.png", "{$dir}/favicon_{$tag}_{$size}.png", 'public-read');
                }
            }
            // Can we write to our root directory?
            if (is_writeable(APP_DIR)) {
                @symlink("data/media/0/0/favicon_{$tag}.ico", APP_DIR . '/favicon.ico');
            }
        }
        else {
            jrCore_set_form_notice('error', 'An error was encountered resizing the PNG image - please try again');
            jrCore_location('referrer');
        }
    }

    if (isset($_post['active']) && jrCore_checktype($_post['active'], 'number_nz')) {

        // If we are changing active icons, and can write to the root dir..
        if (is_writeable(APP_DIR)) {
            @unlink(APP_DIR . '/favicon.ico');
            @symlink("data/media/0/0/favicon_{$_post['active']}.ico", APP_DIR . '/favicon.ico');
        }

        jrCore_set_setting_value('jrFavicon', 'active', $_post['active']);
        jrCore_delete_config_cache();
    }
    jrCore_form_delete_session();
    if ($_ico && isset($_ico[0])) {
        jrCore_set_form_notice('success', 'The new favicon has been successfully created');
    }
    else {
        jrCore_set_form_notice('success', 'The changes were successfully saved');
    }
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse");
}

//------------------------------
// delete
//------------------------------
function view_jrFavicon_delete_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid favicon - please try again');
        jrCore_location('referrer');
    }
    jrCore_delete_media_file(0, "favicon_{$_post['_1']}.ico");
    foreach (jrFavicon_get_image_sizes() as $size) {
        jrCore_delete_media_file(0, "favicon_{$_post['_1']}_{$size}.png");
    }
    jrCore_location('referrer');
}

//------------------------------
// download
//------------------------------
function view_jrFavicon_download($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid favicon - please try again');
        jrCore_location('referrer');
    }
    jrCore_media_file_download(0, "favicon_{$_post['_1']}.ico", 'favicon.ico');
    exit();
}
