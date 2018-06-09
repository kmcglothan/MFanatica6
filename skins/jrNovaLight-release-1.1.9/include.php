<?php
/**
 * Jamroom jrNovaLight skin
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
 * can be found in the "contrib" directory within this skin.
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
* Jamroom Nova skin
 * @copyright 2003 - 2012 by The Jamroom Network - All Rights Reserved
 * @author Brian Johnson - brian@jamroom.net
 */

// We are never called directly
if (!defined('APP_DIR')) {
    exit;
}

/**
 * meta
 */
function jrNovaLight_skin_meta()
{
    $_tmp = array(
        'name'        => 'jrNovaLight',
        'title'       => 'Nova Light',
        'version'     => '1.1.9',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'The Nova Light skin, for Jamroom',
        'license'     => 'jcl',
        'category'    => 'legacy'
    );
    return $_tmp;
}

/**
 * init
 */
function jrNovaLight_skin_init()
{
    // Bring in all our CSS files
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'html.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'grid.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'site.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'page.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'banner.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'chat.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'header.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'footer.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'form_element.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'form_input.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'form_select.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'form_layout.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'form_button.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'form_notice.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'forum.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'list.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'table.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'tabs.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'image.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'action.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'profile.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'skin.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'slider.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'text.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'doc.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'slidebar.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'admin_menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'admin_log.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'admin_modal.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'base.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'tablet.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNovaLight', 'mobile.css');

    // Register our Javascript files with the core
    jrCore_register_module_feature('jrCore', 'javascript', 'jrNovaLight', 'jrNovaLight.js');

    // Slidebars
    jrCore_register_module_feature('jrCore', 'javascript', 'jrNovaLight', APP_DIR . '/skins/jrNovaLight/contrib/slidebars/slidebars.min.js');

    // Tell the core the default icon set to use (black or white)
    jrCore_register_module_feature('jrCore', 'icon_color', 'jrNovaLight', 'black');
    // Tell the core the size of our action buttons (width in pixels, up to 64)
    jrCore_register_module_feature('jrCore', 'icon_size', 'jrNovaLight', 24);
    // Hide module icons
    jrCore_register_module_feature('jrCore', 'module_icons', 'jrNovaLight', 'show', false);

    // Our default media player skins
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrNovaLight', 'jrAudio', 'jrAudio_blue_monday');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrNovaLight', 'jrVideo', 'jrVideo_blue_monday');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrNovaLight', 'jrPlaylist', 'jrPlaylist_blue_monday');

    return true;
}
