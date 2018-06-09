<?php
/**
 * Jamroom jrFlashback skin
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
 * jrFlashback_skin_config
 */
function jrFlashback_skin_config()
{
    // Artist Quota ID
    $_tmp = array(
        'name'     => 'artist_quota',
        'type'     => 'text',
        'default'  => '1',
        'validate' => 'not_empty',
        'label'    => 'Artist Quota',
        'help'     => 'Enter the Artist quota ID.',
        'section'  => 'Index',
        'order'    => 1
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Member Quota ID
    $_tmp = array(
        'name'     => 'member_quota',
        'type'     => 'text',
        'default'  => '1',
        'validate' => 'not_empty',
        'label'    => 'Member Quota',
        'help'     => 'Enter the Member quota ID.',
        'section'  => 'Index',
        'order'    => 2
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Featured Profile ID's
    $_tmp = array(
        'name'     => 'profile_ids',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Featured Profiles',
        'help'     => 'Enter the 4 profile ID(s) you want to show in the Featured Profile section on the index page.<br><span class="form_help_small">Note: Separate multiple ID\'s with a comma, ie. 1,2,3,4</span>',
        'section'  => 'Index',
        'order'    => 3
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Site Forum URL
    $_tmp = array(
        'name'     => 'forum_profile_url',
        'type'     => 'text',
        'default'  => 'admin',
        'validate' => 'not_empty',
        'label'    => 'Forum Profile URL',
        'help'     => 'Enter the profile url name to be used for the site forum.',
        'section'  => 'Index',
        'order'    => 4
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Site Docs URL
    $_tmp = array(
        'name'     => 'docs_profile_url',
        'type'     => 'text',
        'default'  => 'admin',
        'validate' => 'not_empty',
        'label'    => 'Docs Profile URL',
        'help'     => 'Enter the profile url name to be used for the site Docs.',
        'section'  => 'Index',
        'order'    => 5
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Show Stats
    $_tmp = array(
        'name'     => 'show_stats',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Show Stats',
        'help'     => 'Enabling this option will show the stats in the side section.<br><span class="form_help_small">Note: Disable this option to hide the stats.</span>',
        'section'  => 'Index',
        'order'    => 6
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Index Blog Profile ID
    $_tmp = array(
        'name'     => 'index_blog_profile',
        'type'     => 'text',
        'default'  => '1',
        'validate' => 'not_empty',
        'label'    => 'Blog Profile',
        'help'     => 'Enter the ID(s) of the profile(s) for the blogs you would like to show on the home page.<br><span class="form_help_small">Note: Separate multiple ID\'s with a comma, ie. 1,2,3</span>',
        'section'  => 'Blog',
        'order'    => 10
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Index Blog Limit
    $_tmp = array(
        'name'     => 'index_blog_limit',
        'type'     => 'text',
        'default'  => '10',
        'validate' => 'not_empty',
        'label'    => 'Blog Limit',
        'help'     => 'Enter the number of blogs you would like to show on the home page.',
        'section'  => 'Blog',
        'order'    => 11
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // House Radio
    $_tmp = array(
        'name'     => 'show_radio',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Show House Radio',
        'help'     => 'Enabling this option will show the site radio section on the side section.<br><span class="form_help_small">Note: Disable this option to hide the site radio section.</span>',
        'section'  => 'Playlists',
        'order'    => 20
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // House Radio Title
    $_tmp = array(
        'name'     => 'radio_title',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Radio Title',
        'help'     => 'Enter the title of the playlist you want to use as the House Radio.',
        'section'  => 'Playlists',
        'order'    => 21
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Show House Channel
    $_tmp = array(
        'name'     => 'show_tv',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Show House Channel',
        'help'     => 'Enabling this option will show the House Channel section.<br><span class="form_help_small">Note: Disable this option to hide the House Channel section.</span>',
        'section'  => 'Playlists',
        'order'    => 22
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // House Channel ID
    $_tmp = array(
        'name'     => 'tv_title',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'House Channel Title',
        'help'     => 'Enter the title of the playlist you want to use as the House Channel.',
        'section'  => 'Playlists',
        'order'    => 23
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Ads Off
    $_tmp = array(
        'name'     => 'ads_off',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Hide Ads',
        'help'     => 'Check this checkbox to hide all the ads on the site.',
        'section'  => 'Ads',
        'order'    => 30
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Google Ads
    $_tmp = array(
        'name'     => 'google_ads',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Use Google Ads',
        'help'     => 'Enabling this option will show Google Ads on the site.<br><span class="form_help_small">Note: Disable this option to use the Ad fields below.</span>',
        'section'  => 'Ads',
        'order'    => 31
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Google ID
    $_tmp = array(
        'name'     => 'google_id',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Google ID',
        'help'     => 'Enter your Google Ads ID.',
        'section'  => 'Ads',
        'order'    => 32
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Top Ad 468x60
    $_tmp = array(
        'name'     => 'top_ad',
        'default'  => '',
        'type'     => 'textarea',
        'validate' => 'false',
        'required' => 'off',
        'label'    => '468x60 Top Ad',
        'help'     => 'Enter your Ad code here for the top 468x60 Ad.',
        'section'  => 'Ads',
        'order'    => 33
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Side Ad 180x150
    $_tmp = array(
        'name'     => 'side_ad',
        'default'  => '',
        'type'     => 'textarea',
        'validate' => 'false',
        'required' => 'off',
        'label'    => '180x150 Side Ad',
        'help'     => 'Enter your Ad code here for the side 180x150 Ad.',
        'section'  => 'Ads',
        'order'    => 34
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Require Images
    $_tmp = array(
        'name'     => 'require_images',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Require Images',
        'help'     => 'Enabling this option will hide entries without an image associated.',
        'section'  => 'Extras',
        'order'    => 40
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Default Pagebreak
    $_tmp = array(
        'name'     => 'default_pagebreak',
        'type'     => 'text',
        'default'  => '20',
        'validate' => 'not_empty',
        'label'    => 'Default Pagebreak',
        'help'     => 'This is the default pagebreak for the Charts, Songs, Videos and Concerts pages.',
        'section'  => 'Extras',
        'order'    => 41
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Blog Page Pagebreak
    $_tmp = array(
        'name'     => 'default_blog_pagebreak',
        'type'     => 'text',
        'default'  => '8',
        'validate' => 'not_empty',
        'label'    => 'Blog Page Pagebreak',
        'help'     => 'This is the pagebreak for the blog archive page.',
        'section'  => 'Extras',
        'order'    => 42
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Artist Page Pagebreak
    $_tmp = array(
        'name'     => 'default_artist_pagebreak',
        'type'     => 'text',
        'default'  => '21',
        'validate' => 'not_empty',
        'label'    => 'Artist Page Pagebreak',
        'help'     => 'This is the pagebreak for the Artist page.<br><span class="form_help_small">Note: The number should be divisible by 4!</span>',
        'section'  => 'Extras',
        'order'    => 43
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Gallery Page Pagebreak
    $_tmp = array(
        'name'     => 'default_gallery_pagebreak',
        'type'     => 'text',
        'default'  => '21',
        'validate' => 'not_empty',
        'label'    => 'Gallery Page Pagebreak',
        'help'     => 'This is the pagebreak for the Gallery page.<br><span class="form_help_small">Note: The number should be divisible by 4!</span>',
        'section'  => 'Extras',
        'order'    => 44
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Events Page Pagebreak
    $_tmp = array(
        'name'     => 'default_event_pagebreak',
        'type'     => 'text',
        'default'  => '9',
        'validate' => 'not_empty',
        'label'    => 'Event Page Pagebreak',
        'help'     => 'This is the pagebreak for the Event page.',
        'section'  => 'Extras',
        'order'    => 45
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Default character settings for letter searches
    $_tmp = array(
        'name'     => 'letter_alphabet',
        'type'     => 'text',
        'default'  => 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z',
        'validate' => 'not_empty',
        'label'    => 'Search Alphabet',
        'help'     => 'Default character settings for letter searches',
        'section'  => 'Extras',
        'order'    => 46
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    // Social Media
    $num = 50;
    foreach (array('twitter', 'facebook', 'google', 'linkedin', 'youtube', 'pinterest') as $network) {

        // App Store URL
        $_tmp = array(
            'name'     => "{$network}_name",
            'type'     => 'text',
            'default'  => '',
            'validate' => 'printable',
            'label'    => ucfirst($network) . " profile",
            'help'     => "If you have an account for your site on " . ucfirst(str_replace('_', ' ', $network)) . ", enter the profile url, profile name or profile ID and the network icon will show in your footer.  Leave blank to disable.",
            'order'    => $num++,
            'section'  => 'social networks'
        );
        jrCore_register_setting('jrFlashback', $_tmp);
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
        'help'    => 'Select the type of player you want to use on your site.Original = Blue Monday New = New Light Player',
        'type'    => 'select',
        'options' => $_ptype,
        'default' => 'black_overlay_player',
        'order'   => 60,
        'section' => 'Players'
    );
    jrCore_register_setting('jrFlashback', $_tmp);

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
        'order'    => 62
    );
    jrCore_register_setting('jrFlashback', $_tmp);

    return true;
}

?>
