<?php
/**
 * Jamroom jrMediaProLight skin
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
* Jamroom MediaProLight skin
 * @copyright 2003 - 2012 by The Jamroom Network - All Rights Reserved
 * @author Brian Johnson - brian@jamroom.net
 */

// We are never called directly
if (!defined('APP_DIR')) {
    exit;
}

/**
 * jrMediaProLight_meta
 */
function jrMediaProLight_skin_meta()
{
    $_tmp = array(
        'name'        => 'jrMediaProLight',
        'title'       => 'Media Pro Light',
        'version'     => '1.5.8',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'The Media Pro skin for Jamroom (light version)',
        'license'     => 'jcl',
        'category'    => 'music'
    );
    return $_tmp;
}

/**
 * jrMediaProLight_init
 * NOTE: unlike with a module, init() is NOT called on each page load, but is
 * called when the core needs to rebuild CSS or Javascript for the skin
 */
function jrMediaProLight_skin_init()
{
    // Bring in all our CSS files
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'html.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'grid.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'site.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'page.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'banner.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'chat.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'header.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'footer.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'form_element.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'form_input.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'form_select.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'form_layout.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'form_button.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'form_notice.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'list.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'table.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'tabs.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'image.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'gallery.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'profile.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'action.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'forum.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'skin.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'slider.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'flexslider.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'text.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'base.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'doc.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'slidebar.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'admin_menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'admin_log.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'admin_modal.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'tablet.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'mobile.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'playlist.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'buttons.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMediaProLight', 'bundle.css');

    // Register our Javascript files with the core
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMediaProLight', 'responsiveslides.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMediaProLight', 'jquery.flexslider.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMediaProLight', 'jquery.flexslider-min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMediaProLight', 'jquery.easing.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMediaProLight', 'jquery.mousewheel.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMediaProLight', 'jrMediaProLight.js');

    // Slidebars
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMediaProLight', APP_DIR . '/skins/jrMediaProLight/contrib/slidebars/slidebars.min.js');

    // Tell the core the default icon set to use (black or white)
    jrCore_register_module_feature('jrCore', 'icon_color', 'jrMediaProLight', 'black');
    // Tell the core the size of our action buttons (width in pixels, up to 64)
    jrCore_register_module_feature('jrCore', 'icon_size', 'jrMediaProLight', 18);

    // Our default media player skins
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrMediaProLight', 'jrAudio', 'jrAudio_blue_monday');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrMediaProLight', 'jrVideo', 'jrVideo_blue_monday');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrMediaProLight', 'jrPlaylist', 'jrPlaylist_blue_monday');

    return true;
}
