<?php
/**
 * Jamroom jrElastic2 skin
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
 * jrElastic2_skin_config
 */
function jrElastic2_skin_config()
{
    // Profile ID's
    $_tmp = array(
        'name'     => 'welcome_title',
        'type'     => 'text',
        'default'  => 'Welcome to Elastic',
        'validate' => 'not_empty',
        'label'    => 'Welcome Title',
        'help'     => 'Enter You welcome title.',
        'order'    => 1,
        'section'  => 'general'
    );
    jrCore_register_setting('jrElastic2', $_tmp);

    // Profile ID's
    $_tmp = array(
        'name'     => 'profile_ids',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'number_nz',
        'label'    => 'Admin Blog ID',
        'help'     => 'Enter the blog ID you would like featured on the index page admin blog.',
        'order'    => 2,
        'section'  => 'general'
    );
    jrCore_register_setting('jrElastic2', $_tmp);


    // Social Media
    $num = 20;
    foreach (array('twitter', 'facebook', 'google', 'linkedin', 'youtube') as $network) {

        // App Store URL
        $_tmp = array(
            'name'     => "{$network}_url",
            'type'     => 'text',
            'default'  => '#',
            'validate' => 'printable',
            'label'    => ucfirst($network) . " profile",
            'help'     => "If you have an account for your site on " . ucfirst(str_replace('_', ' ', $network)) . ", enter the profile url and the network icon will show on your index page.  Enter 0 to disable.",
            'order'    => $num++,
            'section'  => 'social networks'
        );
        jrCore_register_setting('jrElastic2', $_tmp);
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
        'default' => 'blue_monday',
        'order'   => 30,
        'section' => 'Players'
    );
    jrCore_register_setting('jrElastic2', $_tmp);

    // Player Auto Play
    $_tmp = array(
        'name'     => 'auto_play',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Auto Play',
        'help'     => 'Enabling this option will turn on your players auto play feature.<br><span class="form_help_small">Note: This is for the following profile players only. Audio, Playlist and Video.</span>',
        'order'    => 32,
        'section'  => 'Players'
    );
    jrCore_register_setting('jrElastic2', $_tmp);

    // Profiles

    // Show Online Status
    $_tmp = array(
        'name'     => 'show_online',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Show Online Status',
        'help'     => 'If this option is checked the online status of the profile users will be shown below the Profile image.',
        'order'    => 40,
        'section'  => 'Profiles'
    );
    jrCore_register_setting('jrElastic2', $_tmp);

    // Show Profile Bio
    $_tmp = array(
        'name'     => 'show_bio',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Show Profile Info',
        'help'     => 'If this option is checked and the profile has entered text for the Profile Biography or Signup question, it will be shown in the Profile sidebar.',
        'order'    => 41,
        'section'  => 'Profiles'
    );
    jrCore_register_setting('jrElastic2', $_tmp);

    // Follower Count
    $_tmp = array(
        'name'     => 'follower_count',
        'default'  => '12',
        'type'     => 'text',
        'validate' => 'number_nn',
        'required' => 'on',
        'label'    => 'Latest Follower Count',
        'help'     => 'How many &quot;Latest Followers&quot; should be shown in the Profile sidebar? Set to 0 (zero) to disable showing Profile Followers.',
        'order'    => 42,
        'section'  => 'Profiles'
    );
    jrCore_register_setting('jrElastic2', $_tmp);

    // Recently Rated Count
    $_tmp = array(
        'name'     => 'rated_count',
        'default'  => '12',
        'type'     => 'text',
        'validate' => 'number_nn',
        'required' => 'on',
        'label'    => 'Recently Rated Count',
        'help'     => 'How many &quot;Recently Rated&quot; item should be shown in the Profile sidebar? Set to 0 (zero) to disable showing Recently Rated Items.<br><br><strong>NOTE:</strong> Requires the Item Ratings Module is installed and active in the Profile Quota.',
        'order'    => 43,
        'section'  => 'Profiles'
    );
    jrCore_register_setting('jrElastic2', $_tmp);

    // Show Stats
    $_tmp = array(
        'name'     => 'show_stats',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Show Profile Stats',
        'help'     => 'If this option is checked, the &quot;Profile Stats&quot; section will show on User Profiles.',
        'order'    => 44,
        'section'  => 'Profiles'
    );
    jrCore_register_setting('jrElastic2', $_tmp);

    // Show Tag Cloud
    $_tmp = array(
        'name'     => 'show_tag_cloud',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Show Profile Tag Cloud',
        'help'     => 'If this option is checked, the &quot;Profile Tag Cloud&quot; section will show on User Profiles.<br><br><strong>NOTE:</strong> Requires the Item Tag Cloud module is installed and active in the Profile Quota.',
        'order'    => 45,
        'section'  => 'Profiles'
    );
    jrCore_register_setting('jrElastic2', $_tmp);

    return true;
}
