<?php
/**
 * Jamroom jrSoloArtist skin
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
 * @copyright 2017 The Jamroom Network - All Rights Reserved
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrSoloArtist_skin_config
 */
function jrSoloArtist_skin_config()
{
    // Main Profile ID
    $_tmp = array(
        'name'     => 'main_id',
        'type'     => 'text',
        'default'  => '1',
        'validate' => 'not_empty',
        'label'    => 'Main Profile ID',
        'help'     => 'This is the main profile ID, the first account created when Jamroom was installed. Note: The ID should be 1 if installed correctly',
        'section'  => 'Main',
        'order'    => 1
    );
    jrCore_register_setting('jrSoloArtist', $_tmp);

    // Main Profile Quota ID
    $_tmp = array(
        'name'     => 'main_quota_id',
        'type'     => 'text',
        'default'  => '1',
        'validate' => 'not_empty',
        'label'    => 'Main Quota ID',
        'help'     => 'This is the main profile Quota ID, the first quota created when Jamroom was installed. Note: The ID should be 1 if installed correctly',
        'section'  => 'Main',
        'order'    => 2
    );
    jrCore_register_setting('jrSoloArtist', $_tmp);

    // Main Profile Name
    $_tmp = array(
        'name'     => 'main_profile_url',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Main Profile Name',
        'help'     => 'This is the main profile name, the first account created when Jamroom was installed.',
        'section'  => 'Main',
        'order'    => 3
    );
    jrCore_register_setting('jrSoloArtist', $_tmp);

    // Index Album Name
    $_tmp = array(
        'name'     => 'index_album',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Index Album',
        'help'     => 'Enter the Album Title you want to play in the home page player.',
        'section'  => 'Main',
        'order'    => 4
    );
    jrCore_register_setting('jrSoloArtist', $_tmp);

    // Index Content Selection
    $_cnt = array(
        'timeline' => 'Time-Line',
        'blog'     => 'Newest Blogs',
        'comments' => 'Latest Comments'
    );
    $_tmp = array(
        'name'     => 'index_content',
        'default'  => 'timeline',
        'type'     => 'select',
        'options'  => $_cnt,
        'required' => 'on',
        'label'    => 'Index Content',
        'help'     => 'Choose which content you would like to be on the index page, the options are Time-Line, Blogs or Latest Comments.<br><span class="form_help_small">Note: Default is the Time-Line</span>',
        'section'  => 'Main',
        'order'    => 5
    );
    jrCore_register_setting('jrSoloArtist', $_tmp);

    // Fan Quota ID
    $_tmp = array(
        'name'     => 'fan_quota_id',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Fan Quota ID',
        'help'     => 'If you are allowing fan signup, enter the Quota ID for your Fan Quota.',
        'section'  => 'Main',
        'order'    => 6
    );
    jrCore_register_setting('jrSoloArtist', $_tmp);

    // Admin Profile Menu
    $_tmp = array(
        'name'     => 'admin_pro_menu',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Admin Profile Menu',
        'help'     => 'Enabling this option will turn on your profile menu for the Admin profile.<br><span class="form_help_small">Note: This is only for the Master Admin Profile.</span>',
        'section'  => 'Profile',
        'order'    => 8
    );
    jrCore_register_setting('jrSoloArtist', $_tmp);

    // Profile Comments
    $_tmp = array(
        'name'     => 'profile_comments',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Show Profile Comments',
        'help'     => 'Enabling this option will show profile comments on the profile homepage.',
        'section'  => 'Profile',
        'order'    => 9
    );
    jrCore_register_setting('jrSoloArtist', $_tmp);

    // Social Media
    $num = 10;
    foreach (array('twitter', 'facebook', 'google', 'linkedin', 'youtube', 'pinterest') as $network) {

        // App Store URL
        $_tmp = array(
            'name'     => "{$network}_name",
            'type'     => 'text',
            'default'  => '',
            'validate' => 'printable',
            'label'    => ucfirst($network) . " profile",
            'help'     => "If you have an account for your site on " . ucfirst(str_replace('_', ' ', $network)) . ", enter the profile url, profile name or profile id and the network icon will show in your footer.  Leave blank to disable.",
            'order'    => $num++,
            'section'  => 'social networks'
        );
        jrCore_register_setting('jrSoloArtist', $_tmp);
    }

    // Player Type
    $_ptype = array(
        'blue_monday'          => 'Blue Monday Player',
        'gray_overlay_player'  => 'Gray Overlay Player',
        'player_dark'          => 'Midnight Player',
        'black_overlay_player' => 'Black Overlay Player',
        'solo_player'          => 'Solo Artist Player',
    );
    $_tmp   = array(
        'name'    => 'player_type',
        'label'   => 'Player Type',
        'help'    => 'Select the type of media player you want to use on your site.',
        'type'    => 'select',
        'options' => $_ptype,
        'default' => 'solo_player',
        'order'   => 30,
        'section' => 'Players'
    );
    jrCore_register_setting('jrSoloArtist', $_tmp);

    // Player Auto Play
    $_tmp = array(
        'name'     => 'auto_play',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Auto Play',
        'help'     => 'Enabling this option will turn on your players auto play feature.<br><span class="form_help_small">Note: This is for the following profile players only. Audio, Playlist and Video.</span>',
        'section'  => 'Players',
        'order'    => 31
    );
    jrCore_register_setting('jrSoloArtist', $_tmp);

    return true;
}
