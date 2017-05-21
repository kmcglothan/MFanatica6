<?php
/**
 * Jamroom System Core module
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * quota_config
 */
function jrCore_quota_config()
{
    // Quota Disk Space
    $_disk = array('unlimited');
    $i = 10;
    while ($i < 110) {
        $_disk[$i] = jrCore_format_size($i * 1048576);
        $i += 10;
    }
    $i = 150;
    while ($i < 1000) {
        $_disk[$i] = jrCore_format_size($i * 1048576);
        $i += 50;
    }
    $i = 1024;
    while ($i < 5632) {
        $_disk[$i] = jrCore_format_size($i * 1048576);
        $i += 512;
    }
    $i = 6144;
    while ($i < 11264) {
        $_disk[$i] = jrCore_format_size($i * 1048576);
        $i += 1024;
    }
    $_tmp = array(
        'name'     => 'disk',
        'type'     => 'select',
        'options'  => $_disk,
        'validate' => 'number_nn',
        'label'    => 'media space',
        'help'     => 'How much disk space should profiles in this quota be allowed to have for media items?',
        'default'  => '0',
        'section'  => 'resources',
        'order'    => 10
    );
    jrProfile_register_quota_setting('jrCore', $_tmp);

    // Max Allowed Upload Size
    $_tmp = array(
        'name'     => 'max_upload_size',
        'default'  => jrCore_get_max_allowed_upload(false),
        'type'     => 'select',
        'options'  => 'jrCore_get_upload_sizes',
        'validate' => 'number_nz',
        'required' => 'on',
        'label'    => 'max upload size',
        'help'     => 'Select the maximum allowed size for a file upload by a user to a profile in this quota.<br><br><b>NOTE:</b> This value is limited by the following settings in your server php.ini file: post_max_size, upload_max_filesize and memory_limit.  The upload size will be smaller than these settings due to the overhead involved in the Upload Progress Meter.  To change these values contact your hosting provider.',
        'section'  => 'resources',
        'order'    => 11
    );
    jrProfile_register_quota_setting('jrCore', $_tmp);

    $_tmp = array(
        'name'     => 'allow_all_html',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'label'    => 'allow ALL HTML tags',
        'help'     => 'If this options is checked, then ALL HTML tags will be allowed for profiles in this quota.<br><br><b>WARNING!</b> Enabling this option will allow users to embed HTML tags that could compromise your system security or affect the design and/or layout of your pages.<br><br>Enabling this option overrides the &quot;Allowed HTML Tags&quot; option below.',
        'default'  => 'off',
        'section'  => 'text permissions',
        'order'    => 1
    );
    jrProfile_register_quota_setting('jrCore', $_tmp);

    // Allowed HTML Tags
    $_tmp = array(
        'name'     => 'allowed_tags',
        'default'  => 'span,strong,em,a,b,u,i,p,div,br,img,h1,h2,h3,h4,pre,hr,ul,ol,li,sub,sup,table,tbody,th,tr,td',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => 'on',
        'label'    => 'allowed HTML tags',
        'help'     => 'For profiles in this quota, when they enter text into a textarea, what HTML tags do you want to allow in the text? Separate multiple tags with commas.<br><br><b>CAUTION:</b> The safest option is to not allow ANY HTML in any output, or only allow tags that do not have a &quot;src&quot; attribute. <br><br><b>NOTE:</b> If you are using the Editor Embedded Media, to allow your users to use all the possible editor buttons, use: <br> span,strong,em,a,b,u,i,p,div,br,img,h1,h2,h3,h4,pre,hr,ul,ol,li,sub,sup',
        'section'  => 'text permissions',
        'order'    => 2
    );
    jrProfile_register_quota_setting('jrCore', $_tmp);

    // String Format listeners
    $_tmp = array(
        'name'     => 'active_formatters',
        'default'  => 'jrCore_format_string_allowed_html,jrCore_format_string_convert_at_tags,jrCore_format_string_clickable_urls',
        'type'     => 'optionlist',
        'options'  => 'jrCore_get_format_string_listeners',
        'validate' => 'printable',
        'required' => 'on',
        'label'    => 'Active Text Formatters',
        'help'     => 'jrCore_get_format_string_help',
        'section'  => 'text permissions',
        'order'    => 3
    );
    jrProfile_register_quota_setting('jrCore', $_tmp);

    return true;
}
