<?php
/**
 * Jamroom jrVideoPro skin
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
 * jrVideoPro_skin_config
 */
function jrVideoPro_skin_config()
{
    global $_conf;

    $murl = jrCore_get_module_url('jrCore');

    $styles = array('light', 'dark');

    // Style
    $_tmp = array(
        'name'     => "style",
        'type'     => 'select',
        'default'  => 'light',
        'options'  => $styles,
        'validate' => 'printable',
        'label'    => "Skin Style",
        'help'     => "Video Pro includes an alternative dark style.",
        'section'  => 'Style',
        'order'    => 0,
    );
    jrCore_register_setting('jrVideoPro', $_tmp);



    // Forum Profile
    $_tmp = array(
        'name'     => 'staff_picks',
        'default'  => '',
        'type'     => 'text',
        'validate' => 'printable',
        'label'    => 'Staff Picks',
        'sublabel' => 'comma separated IDs',
        'help'     => 'Insert comma separated video IDs for the Staff Picks list.',
        'section'  => 'Lists',
        'order'    => 1,
    );
    jrCore_register_setting('jrVideoPro', $_tmp);

    // Forum Profile
    $_tmp = array(
        'name'     => 'watched_days',
        'default'  => '14',
        'type'     => 'text',
        'validate' => 'number_nz',
        'label'    => 'Most Watched Days',
        'sublabel' => 'integer',
        'help'     => 'Enter the number of days the Most Watched list will use to calculate play counts. Example: 7 would show the most watched videos for the last 7 days. Default is 14',
        'section'  => 'Lists',
        'order'    => 2,
    );
    jrCore_register_setting('jrVideoPro', $_tmp);

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    foreach (range(1, 6) as $num) {
        $_tmp = array(
            'name'     => "slide_{$num}_active",
            'type'     => 'checkbox',
            'default'  => 'on',
            'validate' => 'onoff',
            'label'    => "Slide {$num} Active",
            'help'     => 'If this box is checked this slide will be active. Edit images <a href="' . $_conf['jrCore_base_url'] . '/' . $murl . '/skin_admin/images/skin=jrVideoPro">here</a>.',
            'order'    => ($num * 10) + 1,
            'section'  => 'Slides'
        );
        jrCore_register_setting('jrVideoPro', $_tmp);

        // Featured Artist IDs
        $_tmp = array(
            'name'     => "slide_{$num}_url",
            'type'     => 'text',
            'default'  => '#',
            'validate' => 'printable',
            'label'    => "Slide {$num} URL",
            'help'     => 'If you would like to choose which items appear on this list, enter the item IDs for those items. Separate entries by commas.',
            'order'    => ($num * 10) + 2,
            'section'  => 'Slides'
        );
        jrCore_register_setting('jrVideoPro', $_tmp);
    }

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
        'section'  => 'Settings'
    );
    jrCore_register_setting('jrVideoPro',$_tmp);

    // Forum Profile
    $_tmp = array(
        'name'     => 'forum_profile',
        'default'  => '',
        'type'     => 'text',
        'validate' => 'url',
        'label'    => 'Forum Profile URL',
        'sublabel' => 'Check the help section.',
        'help'     => 'If you have a Site Forum, enter the <b>Full URL</b> to the forum (usually the site admin Profile URL)<br><br><b>Note:</b> If you are using Site Builder, add the Discussion Link via the Site Builder menu manager',
        'section'  => 'Settings',
        'order'    => 213,
    );
    jrCore_register_setting('jrVideoPro', $_tmp);




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
            'help'     => "If you have an account for your site on " . ucfirst(str_replace('_', ' ', $network)) .", enter the page url, page name or page ID.  Enter 0 to disable.",
            'order'    => $num++,
            'section'  => 'social networks'
        );
        jrCore_register_setting('jrVideoPro', $_tmp);
    }

    return true;
}
