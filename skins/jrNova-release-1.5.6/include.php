<?php
/**
 * Jamroom jrNova skin
 *
 * copyright 2016 The Jamroom Network
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
 * Jamroom 5 Nova skin
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
function jrNova_skin_meta()
{
    $_tmp = array(
        'name'        => 'jrNova',
        'title'       => 'Nova',
        'version'     => '1.5.6',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'The Nova skin, enhanced for Jamroom 5',
        'license'     => 'jcl',
        'category'    => 'legacy'
    );
    return $_tmp;
}

/**
 * init
 */
function jrNova_skin_init()
{
    // Bring in all our CSS files
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'html.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'grid.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'site.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'page.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'banner.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'chat.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'header.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'footer.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'form_element.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'form_input.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'form_select.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'form_layout.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'form_button.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'form_notice.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'forum.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'list.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'table.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'tabs.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'image.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'action.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'profile.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'skin.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'slider.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'text.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'doc.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'slidebar.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'admin_menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'admin_log.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'admin_modal.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'base.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'tablet.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrNova', 'mobile.css');

    // Register our Javascript files with the core
    jrCore_register_module_feature('jrCore', 'javascript', 'jrNova', 'jrNova.js');

    // Slidebars
    jrCore_register_module_feature('jrCore', 'javascript', 'jrNova', APP_DIR . '/skins/jrNova/contrib/slidebars/slidebars.min.js');

    // Tell the core the default icon set to use (black or white)
    jrCore_register_module_feature('jrCore', 'icon_color', 'jrNova', 'black');
    // Tell the core the size of our action buttons (width in pixels, up to 64)
    jrCore_register_module_feature('jrCore', 'icon_size', 'jrNova', 24);
    // Hide module icons
    jrCore_register_module_feature('jrCore', 'module_icons', 'jrNova', 'show', false);

    // Our default media player skins
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrNova', 'jrAudio', 'jrAudio_player_dark');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrNova', 'jrVideo', 'jrVideo_player_dark');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrNova', 'jrPlaylist', 'jrPlaylist_player_dark');

    return true;
}
