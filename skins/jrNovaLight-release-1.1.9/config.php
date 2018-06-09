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
 * @copyright 2017 The Jamroom Network - All Rights Reserved
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrNovaLight_skin_config
 */
function jrNovaLight_skin_config()
{
    // Artist Quota ID
    $_tmp = array(
        'name'     => 'artist_quota',
        'type'     => 'text',
        'default'  => '1',
        'validate' => 'not_empty',
        'label'    => 'Artist Quota ID',
        'help'     => 'Enter the artist Quota ID for your site.<br><br><span class="form_help_small">Note: This is only required if you have a member quota setup! Also make sure to add your member quota ID to the Member Quota ID field below.</span>',
        'section'  => 'Home Page',
        'order'    => 1
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Member Quota ID
    $_tmp = array(
        'name'     => 'member_quota',
        'type'     => 'text',
        'default'  => '1',
        'validate' => 'not_empty',
        'label'    => 'Member Quota ID',
        'help'     => 'Enter the member Quota ID for your site.<br><br><span class="form_help_small">Note: The Member menu button will not show unless this field is set!</span>',
        'section'  => 'Home Page',
        'order'    => 2
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Featured Artist ID's
    $_tmp = array(
        'name'     => 'featured_artist_ids',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Featured Artist ID\'s',
        'help'     => 'Enter the artist ID\'s you want to show in featured tab section.<br><br><span class="form_help_small">Note: Enter up to 4 ID\'s separated by a comma. ie. 1,2,3,4. If left blank the Featured Artist tab will show 4 random artists.</span>',
        'section'  => 'Home Page',
        'order'    => 3
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Featured Song ID's
    $_tmp = array(
        'name'     => 'featured_song_ids',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Featured Song ID\'s',
        'help'     => 'Enter the song ID\'s you want to show in featured tab section.<br><br><span class="form_help_small">Note: Enter up to 4 ID\'s separated by a comma. ie. 1,2,3,4. If left blank the Featured Song tab will show 4 random songs.</span>',
        'section'  => 'Home Page',
        'order'    => 4
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Featured Video ID's
    $_tmp = array(
        'name'     => 'featured_video_ids',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Featured Video ID\'s',
        'help'     => 'Enter the video ID\'s you want to show in featured tab section.<br><br><span class="form_help_small">Note: Enter up to 4 ID\'s separated by a comma. ie. 1,2,3,4. If left blank the Featured Video tab will show 4 random videos.</span>',
        'section'  => 'Home Page',
        'order'    => 5
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Site News Profile ID's
    $_tmp = array(
        'name'     => 'site_news_ids',
        'type'     => 'text',
        'default'  => '1',
        'validate' => 'not_empty',
        'label'    => 'Site News ID',
        'help'     => 'Enter the profile ID you want to use for the Site News section on the home page.<br><br><span class="form_help_small">Note: If left blank, the Site News will use the default Admin ID which is 1. Also, when creating the blog for the site news, the blog category must be set to site news.</span>',
        'section'  => 'Home Page',
        'order'    => 6
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Show Stats
    $_tmp = array(
        'name'     => 'show_stats',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Show Stats',
        'help'     => 'Enabling this option will show the stats at the bottom of the home page.<br><br><span class="form_help_small">Note: Disable this option to hide the stats.</span>',
        'section'  => 'Home Page',
        'order'    => 7
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Home Page Spotlight
    $_tmp = array(
        'name'     => 'index_spotlight',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Index Spotlight',
        'help'     => 'Enabling this option will show the spotlight on the home page.',
        'section'  => 'Spotlight',
        'order'    => 8
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Home Page Spotlight ID's
    $_tmp = array(
        'name'     => 'spotlight_ids',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Index Page Spotlight ID\'s',
        'help'     => 'Enter the 4 artist ID\'s you want to show in top spotlight section of the index page.<br><br><span class="form_help_small">Note: Separate multiple ID\'s with a comma, ie. 1,2,3,4. If left blank the spotlight will show random artists.</span>',
        'section'  => 'Spotlight',
        'order'    => 9
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Audio Chart Page Spotlight
    $_tmp = array(
        'name'     => 'audio_chart_spotlight',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Audio Chart Spotlight',
        'help'     => 'Enabling this option will show the spotlight on the audio chart page.',
        'section'  => 'Spotlight',
        'order'    => 10
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Audio Chart Page Spotlight ID's
    $_tmp = array(
        'name'     => 'audio_chart_spotlight_ids',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Audio Chart Page Spotlight ID\'s',
        'help'     => 'Enter the 4 audio ID\'s you want to show in top spotlight section of the Audio Chart page.<br><br><span class="form_help_small">Note: Separate multiple ID\'s with a comma, ie. 1,2,3,4. If left blank the spotlight will show random artists.</span>',
        'section'  => 'Spotlight',
        'order'    => 11
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Video Chart Page Spotlight
    $_tmp = array(
        'name'     => 'video_chart_spotlight',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Video Chart Spotlight',
        'help'     => 'Enabling this option will show the spotlight on the video chart page.',
        'section'  => 'Spotlight',
        'order'    => 12
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Video Chart Page Spotlight ID's
    $_tmp = array(
        'name'     => 'video_chart_spotlight_ids',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Video Chart Page Spotlight ID\'s',
        'help'     => 'Enter the 4 video ID\'s you want to show in top spotlight section of the Video Chart page.<br><br><span class="form_help_small">Note: Separate multiple ID\'s with a comma, ie. 1,2,3,4. If left blank the spotlight will show random artists.</span>',
        'section'  => 'Spotlight',
        'order'    => 13
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Artists Page Spotlight
    $_tmp = array(
        'name'     => 'artists_page_spotlight',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Artists Page Spotlight',
        'help'     => 'Enabling this option will show the spotlight on the artists page.',
        'section'  => 'Spotlight',
        'order'    => 15
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Artists Page Spotlight ID's
    $_tmp = array(
        'name'     => 'artists_page_spotlight_ids',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Artists Page Spotlight ID\'s',
        'help'     => 'Enter the 4 artist ID\'s you want to show in top spotlight section of the Artists page.<br><br><span class="form_help_small">Note: Separate multiple ID\'s with a comma, ie. 1,2,3,4. If left blank the spotlight will show random artists.</span>',
        'section'  => 'Spotlight',
        'order'    => 16
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Songs Page Spotlight
    $_tmp = array(
        'name'     => 'songs_page_spotlight',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Songs Page Spotlight',
        'help'     => 'Enabling this option will show the spotlight on the songs page.',
        'section'  => 'Spotlight',
        'order'    => 17
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Songs Page Spotlight ID's
    $_tmp = array(
        'name'     => 'songs_page_spotlight_ids',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Songs Page Spotlight ID\'s',
        'help'     => 'Enter the 4 audio ID\'s you want to show in top spotlight section of the Songs page.<br><br><span class="form_help_small">Note: Separate multiple ID\'s with a comma, ie. 1,2,3,4. If left blank the spotlight will show random artists.</span>',
        'section'  => 'Spotlight',
        'order'    => 18
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // SoundCloud Page Spotlight
    $_tmp = array(
        'name'     => 'soundcloud_page_spotlight',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'SoundCloud Page Spotlight',
        'help'     => 'Enabling this option will show the spotlight on the SoundCloud page.',
        'section'  => 'Spotlight',
        'order'    => 19
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // SoundCloud Page Spotlight ID's
    $_tmp = array(
        'name'     => 'soundcloud_page_spotlight_ids',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'SoundCloud Page Spotlight ID\'s',
        'help'     => 'Enter the 4 soundcloud ID\'s you want to show in top spotlight section of the SoundCLoud page.<br><br><span class="form_help_small">Note: Separate multiple ID\'s with a comma, ie. 1,2,3,4. If left blank the spotlight will show random artists.</span>',
        'section'  => 'Spotlight',
        'order'    => 20
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Video Page Spotlight
    $_tmp = array(
        'name'     => 'video_page_spotlight',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Video Page Spotlight',
        'help'     => 'Enabling this option will show the spotlight on the Video page.',
        'section'  => 'Spotlight',
        'order'    => 21
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Video Page Spotlight ID's
    $_tmp = array(
        'name'     => 'video_page_spotlight_ids',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Video Page Spotlight ID\'s',
        'help'     => 'Enter the 4 video ID\'s you want to show in top spotlight section of the Video page.<br><br><span class="form_help_small">Note: Separate multiple ID\'s with a comma, ie. 1,2,3,4. If left blank the spotlight will show random artists.</span>',
        'section'  => 'Spotlight',
        'order'    => 22
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // YouTube Page Spotlight
    $_tmp = array(
        'name'     => 'youtube_page_spotlight',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'YouTube Page Spotlight',
        'help'     => 'Enabling this option will show the spotlight on the YouTube page.',
        'section'  => 'Spotlight',
        'order'    => 23
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // YouTube Page Spotlight ID's
    $_tmp = array(
        'name'     => 'youtube_page_spotlight_ids',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'YouTube Page Spotlight ID\'s',
        'help'     => 'Enter the 4 youtube ID\'s you want to show in top spotlight section of the YouTube page.<br><br><span class="form_help_small">Note: Separate multiple ID\'s with a comma, ie. 1,2,3,4. If left blank the spotlight will show random artists.</span>',
        'section'  => 'Spotlight',
        'order'    => 24
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Viemo Page Spotlight
    $_tmp = array(
        'name'     => 'vimeo_page_spotlight',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Viemo Page Spotlight',
        'help'     => 'Enabling this option will show the spotlight on the Viemo page.',
        'section'  => 'Spotlight',
        'order'    => 25
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Viemo Page Spotlight ID's
    $_tmp = array(
        'name'     => 'vimeo_page_spotlight_ids',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Viemo Page Spotlight ID\'s',
        'help'     => 'Enter the 4 vimeo ID\'s you want to show in top spotlight section of the Viemo page.<br><br><span class="form_help_small">Note: Separate multiple ID\'s with a comma, ie. 1,2,3,4. If left blank the spotlight will show random artists.</span>',
        'section'  => 'Spotlight',
        'order'    => 26
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Concerts Page Spotlight
    $_tmp = array(
        'name'     => 'event_page_spotlight',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Concerts Page Spotlight',
        'help'     => 'Enabling this option will show the spotlight on the Concerts page.',
        'section'  => 'Spotlight',
        'order'    => 27
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Concerts Page Spotlight ID's
    $_tmp = array(
        'name'     => 'event_page_spotlight_ids',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Concerts Page Spotlight ID\'s',
        'help'     => 'Enter the 4 event ID\'s you want to show in top spotlight section of the Concerts page.<br><br><span class="form_help_small">Note: Separate multiple ID\'s with a comma, ie. 1,2,3,4. If left blank the spotlight will show random artists.</span>',
        'section'  => 'Spotlight',
        'order'    => 28
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Members Page Spotlight
    $_tmp = array(
        'name'     => 'members_page_spotlight',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Members Page Spotlight',
        'help'     => 'Enabling this option will show the spotlight on the Members page.',
        'section'  => 'Spotlight',
        'order'    => 29
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Members Page Spotlight ID's
    $_tmp = array(
        'name'     => 'members_page_spotlight_ids',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Members Page Spotlight ID\'s',
        'help'     => 'Enter the 4 member ID\'s you want to show in top spotlight section of the Members page.<br><br><span class="form_help_small">Note: Separate multiple ID\'s with a comma, ie. 1,2,3,4. If left blank the spotlight will show random artists.</span>',
        'section'  => 'Spotlight',
        'order'    => 30
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Blogs Page Spotlight
    $_tmp = array(
        'name'     => 'blog_page_spotlight',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Blogs Page Spotlight',
        'help'     => 'Enabling this option will show the spotlight on the Blogs page.',
        'section'  => 'Spotlight',
        'order'    => 31
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Blogs Page Spotlight ID's
    $_tmp = array(
        'name'     => 'blog_page_spotlight_ids',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Blogs Page Spotlight ID\'s',
        'help'     => 'Enter the 4 blog ID\'s you want to show in top spotlight section of the Blogs page.<br><br><span class="form_help_small">Note: Separate multiple ID\'s with a comma, ie. 1,2,3,4. If left blank the spotlight will show random artists.</span>',
        'section'  => 'Spotlight',
        'order'    => 32
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Show Radio
    $_tmp = array(
        'name'     => 'show_radio',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Show Site Radio',
        'help'     => 'Enabling this option will show the site radio section on the index page.<br><br><span class="form_help_small">Note: Disable this option to hide the site radio section.</span>',
        'section'  => 'Radio And TV',
        'order'    => 33
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Site Radio ID
    $_tmp = array(
        'name'     => 'radio_title',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Radio Title',
        'help'     => 'Enter the title of the playlist you want to use as the Community Radio.',
        'section'  => 'Radio And TV',
        'order'    => 34
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Show TV
    $_tmp = array(
        'name'     => 'show_tv',
        'default'  => 'on',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Show Site TV',
        'help'     => 'Enabling this option will show the site TV section on the index page.<br><br><span class="form_help_small">Note: Disable this option to hide the site TV section.</span>',
        'section'  => 'Radio And TV',
        'order'    => 35
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Site TV ID
    $_tmp = array(
        'name'     => 'tv_title',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'TV Title',
        'help'     => 'Enter the title of the playlist you want to use as the Community TV.',
        'section'  => 'Radio And TV',
        'order'    => 36
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

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
        'default' => 'gray_overlay_player',
        'order'   => 37,
        'section' => 'Radio And TV'
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Player Auto Play
    $_tmp = array(
        'name'     => 'auto_play',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Auto Play',
        'help'     => 'Enabling this option will turn on your players auto playe feature.<br><span class="form_help_small">Note: This is for the following profile players only. Audio, Playlist and Video.</span>',
        'section'  => 'Radio And TV',
        'order'    => 38
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Ads Off
    $_tmp = array(
        'name'     => 'ads_off',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Hide Ads',
        'help'     => 'Enabling this option will allow you to hide all ads on your site.<br><span class="form_help_small">Note: Disable this option to show ads.</span>',
        'section'  => 'Site Ads',
        'order'    => 39
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Google Ads
    $_tmp = array(
        'name'     => 'google_ads',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Use Google Ads',
        'help'     => 'Enabling this option will show Google Ads on the site.<br><span class="form_help_small">Note: Disable this option to use the Ad fields below.</span>',
        'section'  => 'Site Ads',
        'order'    => 40
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Google ID
    $_tmp = array(
        'name'     => 'google_id',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'not_empty',
        'label'    => 'Google ID',
        'help'     => 'Enter your Google Ads ID.',
        'section'  => 'Site Ads',
        'order'    => 41
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Top Ad 468x60
    $_tmp = array(
        'name'     => 'top_ad',
        'default'  => '',
        'type'     => 'textarea',
        'validate' => 'false',
        'required' => 'off',
        'label'    => '468x60 Top Ad',
        'help'     => 'Enter your Ad code here for the top 468x60 Ad.',
        'section'  => 'Site Ads',
        'order'    => 42
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Bottom Ad 180x150
    $_tmp = array(
        'name'     => 'bottom_ad',
        'default'  => '',
        'type'     => 'textarea',
        'validate' => 'false',
        'required' => 'off',
        'label'    => '180x150 Bottom Ad',
        'help'     => 'Enter your Ad code here for the bottom 180x150 Ad.',
        'section'  => 'Site Ads',
        'order'    => 43
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Require Images
    $_tmp = array(
        'name'     => 'require_images',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => 'on',
        'label'    => 'Require Images',
        'help'     => 'Enabling this option will hide entries without an image associated.',
        'section'  => 'Extra Settings',
        'order'    => 44
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Default Pagebreak
    $_tmp = array(
        'name'     => 'default_pagebreak',
        'type'     => 'text',
        'default'  => 10,
        'validate' => 'number_nz',
        'label'    => 'Default Pagebreak',
        'help'     => 'This is the default pagebreak for the Charts, Songs, Videos and Concerts pages.',
        'section'  => 'Extra Settings',
        'order'    => 45
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Artist Page Pagebreak
    $_tmp = array(
        'name'     => 'default_artist_pagebreak',
        'type'     => 'text',
        'default'  => 12,
        'validate' => 'number_nz',
        'label'    => 'Artist Page Pagebreak',
        'help'     => 'This is the pagebreak for the Artist page, should be divisible by 6.',
        'section'  => 'Extra Settings',
        'order'    => 46
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

    // Show Past Events
    $_tmp = array(
        'name'     => 'past_events',
        'default'  => 'off',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'label'    => 'Past Events',
        'help'     => 'Enabling this option will show past events on the Concerts page.',
        'section'  => 'Extra Settings',
        'order'    => 47
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

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
        'order'    => 48
    );
    jrCore_register_setting('jrNovaLight', $_tmp);

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
        jrCore_register_setting('jrNovaLight', $_tmp);
    }

    return true;
}
