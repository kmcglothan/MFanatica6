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

/**
 * meta
 */
function jrFavicon_meta()
{
    $_tmp = array(
        'name'        => 'Favicon Creator',
        'url'         => 'favicon',
        'version'     => '1.0.3',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Create a site Favicon that supports multiple resolutions including Retina displays',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2954/favicon-creator',
        'license'     => 'mpl',
        'category'    => 'site'
    );
    return $_tmp;
}

/**
 * init
 */
function jrFavicon_init()
{
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrFavicon', 'browse', array('favicon creator', 'Create and Update a Favicon for your site'));
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrFavicon', 'browse', 'Favicon Creator');

    // Add favicon HTML to pages
    jrCore_register_event_listener('jrCore', 'view_results', 'jrFavicon_view_results_listener');
    return true;
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Add favicon HTML to pages
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrFavicon_view_results_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (isset($_conf['jrFavicon_enabled']) && $_conf['jrFavicon_enabled'] == 'on' && isset($_conf['jrFavicon_active']) && $_conf['jrFavicon_active'] > 0) {
        // Only add to HTML with a head
        if (strpos($_data, '</head>')) {
            $_post['media_url'] = jrCore_get_media_url(0);
            $temp               = jrCore_parse_template('favicon.tpl', $_post, 'jrFavicon');
            if (isset($temp{2}) && strpos($_data, '</title>')) {
                $_data = str_replace('</title>', "</title>\n{$temp}", $_data);
            }
        }
    }
    return $_data;
}

/**
 * Get sizes of images we are going to create favicons of
 * @see https://github.com/audreyr/favicon-cheat-sheet
 * @return array
 */
function jrFavicon_get_image_sizes()
{
    return array(57, 72, 96, 114, 120, 144, 152, 195, 228);
}
