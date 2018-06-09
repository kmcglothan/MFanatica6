<?php
/**
 * Jamroom jrProJamLight skin
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
* Jamroom ProJamLight skin
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
function jrProJamLight_skin_meta()
{
    $_tmp = array(
        'name'        => 'jrProJamLight',
        'title'       => 'Pro Jam Light',
        'version'     => '1.5.8',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'The Pro Jam skin for Jamroom (light version)',
        'license'     => 'jcl',
        'category'    => 'music'
    );
    return $_tmp;
}

/**
 * init
 */
function jrProJamLight_skin_init()
{
    // Bring in all our CSS files
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'html.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'grid.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'site.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'page.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'banner.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'chat.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'header.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'footer.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'form_element.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'form_input.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'form_select.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'form_layout.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'form_button.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'form_notice.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'list.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'table.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'tabs.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'image.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'gallery.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'forum.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'action.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'profile.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'skin.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'slider.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'text.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'base.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'doc.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'slidebar.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'admin_menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'admin_log.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'admin_modal.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'tablet.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'mobile.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'playlist.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'buttons.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'bundle.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrProJamLight', 'rating.css');

    // Register our Javascript files with the core
    jrCore_register_module_feature('jrCore', 'javascript', 'jrProJamLight', 'responsiveslides.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrProJamLight', 'jrProJamLight.js');

    // Slidebars
    jrCore_register_module_feature('jrCore', 'javascript', 'jrProJamLight', APP_DIR . '/skins/jrProJamLight/contrib/slidebars/slidebars.min.js');

    // Tell the core the default icon set to use (black or white)
    jrCore_register_module_feature('jrCore', 'icon_color', 'jrProJamLight', 'white');
    // Tell the core the size of our action buttons (width in pixels, up to 64)
    jrCore_register_module_feature('jrCore', 'icon_size', 'jrProJamLight', 18);

    // Our default media player skins
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrProJamLight', 'jrAudio', 'jrAudio_blue_monday');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrProJamLight', 'jrVideo', 'jrVideo_blue_monday');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrProJamLight', 'jrPlaylist', 'jrPlaylist_blue_monday');

    return true;
}
