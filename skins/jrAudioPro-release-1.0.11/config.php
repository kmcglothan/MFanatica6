<?php
/**
 * Jamroom jrAudioPro skin
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
 * jrAudioPro_skin_config
 */
function jrAudioPro_skin_config()
{
    $styles = array('light', 'dark');
    $titles = array('Style','On Sale','Featured Artists', 'Charts');

    // Style
    $_tmp = array(
        'name'     => "style",
        'type'     => 'select',
        'default'  => 'light',
        'options'  => $styles,
        'validate' => 'printable',
        'label'    => "Skin Style",
        'help'     => "Media Pro includes an alternative dark style.",
        'section'  => $titles[0],
        'order'    => 0,
    );
    jrCore_register_setting('jrAudioPro', $_tmp);




    foreach (range(1, 3) as $num) {
        // On Sale IDs
        $_tmp = array(
            'name'     => "list_{$num}_active",
            'type'     => 'checkbox',
            'default'  => 'on',
            'validate' => 'onoff',
            'label'    => "List Active",
            'help'     => 'If you have the payment processor, like of our Foxycart module, you may require a price for songs in this list.',
            'order'    => ($num * 10) + 1,
            'section'  => $titles[$num]
        );
        jrCore_register_setting('jrAudioPro', $_tmp);

        if ($num == 3) {
            $days = array('1' => '1', '7' => '7','14' => '14','30' => '30','365' => '365');

            // Style
            $_tmp = array(
                'name'     => "chart_days",
                'type'     => 'select',
                'default'  => '30',
                'options'  => $days,
                'validate' => 'printable',
                'label'    => "Chart Days",
                'help'     => "Enter the range of days your chart will calculate. it will show the top 17 Tracks.",
                'section'  => $titles[$num],
                'order'    => ($num * 10) + 2,
            );
            jrCore_register_setting('jrAudioPro', $_tmp);
        }
        else {
            // Featured Artist IDs
            $_tmp = array(
                'name'     => "list_{$num}_ids",
                'type'     => 'text',
                'default'  => '',
                'validate' => 'printable',
                'label'    => "$titles[$num] IDs",
                'help'     => 'If you would like to choose which items appear on this list, enter the item IDs for those items. Separate entries by commas.',
                'order'    => ($num * 10) + 2,
                'section'  => $titles[$num]
            );

            jrCore_register_setting('jrAudioPro', $_tmp);

            // On Sale IDs
            $_tmp = array(
                'name'     => "list_{$num}_soundcloud",
                'type'     => 'checkbox',
                'default'  => 'on',
                'validate' => 'onoff',
                'label'    => "Show SoundCloud",
                'help'     => 'With this box checked SoundCloud items will appear in this list. Note that if require price is checked it will override this setting.',
                'order'    => ($num * 10) + 4,
                'section'  => $titles[$num]
            );

            jrCore_register_setting('jrAudioPro', $_tmp);
        }

        // On Sale IDs
        $_tmp = array(
            'name'     => "require_price_{$num}",
            'type'     => 'checkbox',
            'default'  => 'off',
            'validate' => 'onoff',
            'label'    => "Require Price",
            'help'     => 'If you have the payment processor, like of our Foxycart module, you may require a price for songs in this list. If this box is check soundcloud items will NOT be listed.',
            'order'    => ($num * 10) + 3,
            'section'  => $titles[$num]
        );

        jrCore_register_setting('jrAudioPro', $_tmp);


    }

    jrCore_delete_setting('jrAudioPro','list_3_soundcloud');


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
    jrCore_register_setting('jrAudioPro',$_tmp);

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
    jrCore_register_setting('jrAudioPro', $_tmp);


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
            'help'     => "If you have an account for your site on " . ucfirst(str_replace('_', ' ', $network)) .", enter the page url.  Enter 0 to disable.",
            'order'    => $num++,
            'section'  => 'social networks'
        );
        jrCore_register_setting('jrAudioPro', $_tmp);
    }

    return true;
}
