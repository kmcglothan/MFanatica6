<?php
/**
 * Jamroom jrMSkin skin
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
 * @copyright 2017 The Jamroom Network - All Rights Reserved
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrMSkin_skin_config
 */
function jrMSkin_skin_config()
{

    global $_conf;

    // Section Active
    $_tmp = array(
        'name'     => "randomize",
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'label'    => "Randomize Videos",
        'help'     => "If this box is checked your video backgrounds will be selected randomly.",
        'section'  => "video BGs",
        'order'    => 0
    );
    jrCore_register_setting('jrMSkin', $_tmp);

    $_videos = array(
        '1'     => 'video 1',
        '2'     => 'video 2',
        '3'     => 'video 3',
        '4'     => 'video 4',
        '5'     => 'video 5',
    );

    // Blog Profile IDs
    $_tmp = array(
        'name'     => "default_video",
        'type'     => 'select',
        'default'  => '1',
        'options'  => $_videos,
        'validate' => 'printable',
        'label'    => "Default Video",
        'help'     => "If you choose not to randomize videos, select which video you'd like to play.",
        'order'    => 1,
        'section'  => "video BGs"
    );
    jrCore_register_setting('jrMSkin', $_tmp);


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////// START SECTION 1 /////////////////////// START SECTION 1 ////////////////////////// START SECTION 1 /////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $_mods = array(
        'jrProfile'     => 'jrProfile',
        'jrAudio'       => 'jrAudio',
        'jrVideo'       => 'jrVideo',
        'jrBlog'        => 'jrBlog',
        'jrEvent'       => 'jrEvent',
        'jrGallery'     => 'jrGallery'
    );

    $_orders = array(
        '_item_id numerical_desc' => 'Newest',
        '_item_id numerical_asc' => 'Oldest',
        '*_title desc' => 'Alphabetically',
    );

    foreach (range(1, 3) as $num) {

        // Section Active
        $_tmp = array(
            'name'     => "list_{$num}_active",
            'type'     => 'checkbox',
            'default'  => 'on',
            'validate' => 'onoff',
            'label'    => "Active",
            'help'     => "Select if section #{$num} is active on the index page",
            'section'  => "list {$num}",
            'order'    => ((100 + ($num * 10)) + 0)
        );
        jrCore_register_setting('jrMSkin', $_tmp);

        // Blog Profile IDs
        $_tmp = array(
            'name'     => "list_{$num}_headline",
            'type'     => 'text',
            'default'  => "List {$num} Headline",
            'validate' => 'printable',
            'label'    => "List {$num} Headline",
            'help'     => 'The headline appears above the list.',
            'order'    => ((100 + ($num * 10)) + 1),
            'section'  => "list {$num}"
        );
        jrCore_register_setting('jrMSkin', $_tmp);

        // list order
        $_tmp = array(
            'name'     => "list_{$num}_type",
            'type'     => 'select',
            'default'  => 'jrAudio',
            'options'  => $_mods,
            'validate' => 'printable',
            'label'    => "Type",
            'help'     => "Enter the descriptive text for feature box #{$num} on the index page",
            'section'  => "list {$num}",
            'order'    => ((100 + ($num * 10)) + 2)
        );
        jrCore_register_setting('jrMSkin', $_tmp);

        // list order
        $_tmp = array(
            'name'     => "list_{$num}_order",
            'type'     => 'select',
            'default'  => 'text',
            'options'  => $_orders,
            'validate' => 'printable',
            'label'    => "Order",
            'help'     => "Enter the descriptive text for feature box #{$num} on the index page",
            'section'  => "list {$num}",
            'order'    => ((100 + ($num * 10)) + 3)
        );
        jrCore_register_setting('jrMSkin', $_tmp);

        // Override IDs
        $_tmp = array(
            'name'     => "list_{$num}_ids",
            'type'     => 'text',
            'default'  => '',
            'validate' => 'printable',
            'label'    => "Override IDs",
            'help'     => 'If you would like to choose which items appear on this list, enter the item IDs for those items. Separate entries by commas.',
            'order'    => ((100 + ($num * 10)) + 4),
            'section'  => "list {$num}"
        );

        jrCore_register_setting('jrMSkin', $_tmp);

        // Section Active
        $_tmp = array(
            'name'     => "list_{$num}_more_show",
            'type'     => 'checkbox',
            'default'  => 'on',
            'validate' => 'onoff',
            'label'    => "Show More Button",
            'help'     => "Check this box if you want to show the more button for this list.",
            'section'  => "list {$num}",
            'order'    => ((100 + ($num * 10)) + 5)
        );
        jrCore_register_setting('jrMSkin', $_tmp);

        // More URL
        $_tmp = array(
            'name'     => "list_{$num}_more_text",
            'type'     => 'text',
            'default'  => 'See More',
            'validate' => 'printable',
            'label'    => "More URL",
            'help'     => 'Enter the URL of the page containing more of this list.',
            'order'    => ((100 + ($num * 10)) + 6),
            'section'  => "list {$num}"
        );

        jrCore_register_setting('jrMSkin', $_tmp);

        // More URL
        $_tmp = array(
            'name'     => "list_{$num}_more_url",
            'type'     => 'text',
            'default'  => '#',
            'validate' => 'printable',
            'label'    => "More URL",
            'help'     => 'Enter the URL of the page containing more of this list.',
            'order'    => ((100 + ($num * 10)) + 7),
            'section'  => "list {$num}"
        );

        jrCore_register_setting('jrMSkin', $_tmp);
    }

    // List Active
    $_tmp = array(
        'name'     => "bottom_text",
        'type'     => 'textarea',
        'default'  => "<h2 style=\"margin: 0 auto;\"> {$_conf['jrCore_system_name']}  is the music community you've been waiting to join.</h2><button class=\"see_more\">Get Started Now!</button>",
        'validate' => 'allowed_html',
        'label'    => "Bottom Text",
        'help'     => "Enter the descriptive text for bottom tag of section #{$num} on the index page",
        'section'  => "bottom content",
        'order'    => 200
    );
    jrCore_register_setting('jrMSkin', $_tmp);

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $_tmp = array(
        'name'     => 'auto_play',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Auto Play',
        'help'     => 'If this box is checked your players will play when loaded.',
        'order'    => 210,
        'section'  => 'General Settings'
    );
    jrCore_register_setting('jrMSkin',$_tmp);

    // Player Auto Play
    $_tmp = array(
        'name'     => 'bio_right',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Bio Right',
        'help'     => '<p>If this is set to yes, your bios will appear above the timeline on the profile index page.</a></span>',
        'order'    => 211,
        'section'  => 'General Settings'
    );
    jrCore_register_setting('jrMSkin',$_tmp);

    // Forum Profile
    $_tmp = array(
        'name'     => 'forum_profile',
        'default'  => '',
        'type'     => 'text',
        'validate' => 'url',
        'label'    => 'Forum Profile URL',
        'sublabel' => 'Check the help section.',
        'help'     => 'If you have a Site Forum, enter the <b>Full URL</b> to the forum (usually the site admin Profile URL)<br><br><b>Note:</b> If you are using Site Builder, add the Discussion Link via the Site Builder menu manager',
        'section'  => 'General Settings',
        'order'    => 213,
    );
    jrCore_register_setting('jrMSkin', $_tmp);

    // Show Profile Header
    $_tmp = array(
        'name'     => 'show_followed',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'label'    => 'Profiles Show Followed',
        'help'     => 'If this option is checked, profile owners will see profiles they follow on their timeline.',
        'section'  => 'General Settings',
        'order'    => 214
    );
    jrCore_register_setting('jrMSkin', $_tmp);

    $ops = array(
        'full'    => 'full',
        'compact' => 'compact',
        'minimal' => 'minimal',
    );

    // Show Profile Header
    $_tmp = array(
        'name'     => 'show_header',
        'default'  => 'minimal',
        'type'     => 'select',
        'validate' => 'printable',
        'label'    => 'Show Profile Header',
        'options'  => $ops,
        'help'     => 'When a visiting user views a profile page that is NOT the index page this option determines how the header is displayed. <br><br><b>Full</b> is normal, it shows the full profile header image. <br><b>compact</b> shows the profile header image at 1/3rd its height.  <br><b>minimal</b> doesnt show the profile header image.',
        'section'  => 'General Settings',
        'order'    => 215
    );
    jrCore_register_setting('jrMSkin', $_tmp);

    $_tmp = array(
        'name'     => 'show_menu',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Show Menu',
        'help'     => 'If you have the profile header set to minimal, check this box if you would still like to show the profile menu.',
        'order'    => 216,
        'section'  => 'General Settings'
    );
    jrCore_register_setting('jrMSkin', $_tmp);

    // Social Media
    $num = 220;
    foreach (array('twitter', 'facebook', 'google', 'youtube', 'linkedin') as $network) {

        // App Store URL
        $_tmp = array(
            'name'     => "{$network}_url",
            'type'     => 'text',
            'default'  => '#',
            'validate' => 'printable',
            'label'    => ucfirst($network) . " page",
            'help'     => "If you have an account for your site on " . ucfirst(str_replace('_', ' ', $network)) .", enter the page url.  Enter # to disable.",
            'order'    => $num++,
            'section'  => 'social networks'
        );
        jrCore_register_setting('jrMSkin', $_tmp);
    }

    return true;
}
