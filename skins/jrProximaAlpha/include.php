<?php
/**
 * Jamroom jrProximaAlpha skin
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
 * Jamroom 5 Elastic skin
 * @copyright 2003 - 2014 by The Jamroom Network - All Rights Reserved
 * @author Brian Johnson - brian@jamroom.net
 */

// We are never called directly
if (!defined('APP_DIR')) {
    exit;
}

/**
 * jrProximaAlpha_meta
 */
function jrProximaAlpha_skin_meta()
{
    $_tmp = array(
        'name'        => 'jrProximaAlpha',
        'title'       => 'Proxima Alpha',
        'version'     => '1.1.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'The default Proxima Skin - easy to use and customize',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * jrProximaAlpha_init
 * unlike with a module, init() is NOT called on each page load, but is
 * called when the core needs to rebuild CSS or Javascript for the skin
 */
function jrProximaAlpha_skin_init()
{
    // Bring in all our CSS files
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'html.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'grid.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'site.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'page.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'banner.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'header.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'footer.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'form_input.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'form_select.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'form_layout.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'form_button.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'form_notice.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'form_element.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'list.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'table.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'tabs.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'image.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'profile.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'skin.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'text.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'base.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'index.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'blog.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'admin_menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'admin_log.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'admin_modal.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'override_tablet.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'override_mobile.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrProximaAlpha', 'slidebar.css');

    // Slidebars
    jrCore_register_module_feature('jrCore', 'javascript', 'jrProximaAlpha', APP_DIR . '/skins/jrProximaAlpha/contrib/slidebars/slidebars.min.js');

    // Tell the core the default icon set to use (black or white)
    jrCore_register_module_feature('jrCore', 'icon_color', 'jrProximaAlpha', 'black');

    // Tell the core the size of our action buttons (width in pixels, up to 64)
    jrCore_register_module_feature('jrCore', 'icon_size', 'jrProximaAlpha', 30);

    // Our default media player skins
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrProximaAlpha', 'jrAudio', 'jrAudio_player_dark');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrProximaAlpha', 'jrVideo', 'jrVideo_player_dark');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrProximaAlpha', 'jrPlaylist', 'jrPlaylist_player_dark');

    // Hide module icons
    jrCore_register_module_feature('jrCore', 'module_icons', 'jrProximaAlpha', 'show', false);

    return true;
}
