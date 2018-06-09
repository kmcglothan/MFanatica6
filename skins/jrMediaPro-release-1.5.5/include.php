<?php
/**
 * Jamroom jrMediaPro skin
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
 * Jamroom 5 MediaPro skin
 * @copyright 2003 - 2012 by The Jamroom Network - All Rights Reserved
 * @author Brian Johnson - brian@jamroom.net
 */

// We are never called directly
if (!defined('APP_DIR')) {
    exit;
}

/**
 * jrMediaPro_meta
 */
function jrMediaPro_skin_meta()
{
    $_tmp = array(
        'name'        => 'jrMediaPro',
        'title'       => 'Media Pro Dark',
        'version'     => '1.5.5',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'The Media Pro skin for Jamroom 5 (dark version)',
        'license'     => 'jcl',
        'category'    => 'music'
    );
    return $_tmp;
}

/**
 * jrMediaPro_init
 * NOTE: unlike with a module, init() is NOT called on each page load, but is
 * called when the core needs to rebuild CSS or Javascript for the skin
 */
function jrMediaPro_skin_init()
{
    // Bring in all our CSS files
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'html.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'grid.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'site.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'page.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'banner.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'chat.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'header.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'footer.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'form_element.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'form_input.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'form_select.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'form_layout.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'form_button.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'form_notice.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'list.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'table.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'tabs.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'image.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'gallery.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'profile.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'action.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'forum.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'skin.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'slider.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'flexslider.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'text.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'base.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'doc.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'slidebar.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'admin_menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'admin_log.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'admin_modal.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'tablet.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'mobile.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'playlist.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'buttons.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaPro', 'bundle.css');

    // Register our Javascript files with the core
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMediaPro', 'responsiveslides.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMediaPro', 'jquery.flexslider.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMediaPro', 'jquery.flexslider-min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMediaPro', 'jquery.easing.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMediaPro', 'jquery.mousewheel.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMediaPro', 'jrMediaPro.js');

    // Slidebars
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMediaPro', APP_DIR . '/skins/jrMediaPro/contrib/slidebars/slidebars.min.js');

    // Tell the core the default icon set to use (black or white)
    jrCore_register_module_feature('jrCore', 'icon_color', 'jrMediaPro', 'white');
    // Tell the core the size of our action buttons (width in pixels, up to 64)
    jrCore_register_module_feature('jrCore', 'icon_size', 'jrMediaPro', 18);

    // Our default media player skins
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrMediaPro', 'jrAudio', 'jrAudio_player_dark');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrMediaPro', 'jrVideo', 'jrVideo_player_dark');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrMediaPro', 'jrPlaylist', 'jrPlaylist_player_dark');

    return true;
}
