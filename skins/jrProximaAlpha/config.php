<?php
/**
 * Jamroom jrProximaAlpha skin
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
 * skin_config
 */
function jrProximaAlpha_skin_config()
{
    // Show Search
    $_tmp = array(
        'name'     => 'show_search',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'label'    => 'Show Search Option',
        'help'     => 'If this option is checked and the Search module is active, there will be a &quot;Search&quot; option shown in the site header allowing the user to search the site.',
        'order'    => 1,
        'section'  => 'settings'
    );
    jrCore_register_setting('jrProximaAlpha', $_tmp);

    // Blog Profile IDs
    $_tmp = array(
        'name'     => 'blog_profile_ids',
        'type'     => 'text',
        'default'  => '1',
        'validate' => 'printable',
        'label'    => 'Blog Profile IDs',
        'help'     => 'If you would like to link to the Site Blog from your front page, enter the Profile IDs that the blog entries should be pulled from.  Multiple Profile IDs may be entered, separated by commas.',
        'order'    => 2,
        'section'  => 'settings'
    );
    jrCore_register_setting('jrProximaAlpha', $_tmp);

    // App Store
    $num = 10;
    foreach (array('apple', 'google', 'windows') as $store) {

        // App Store Enabled
        $_tmp = array(
            'name'     => "{$store}_active",
            'type'     => 'checkbox',
            'default'  => 'on',
            'validate' => 'onoff',
            'label'    => "Enable " . ucfirst($store) . " Icon",
            'help'     => "If this option is enabled, the {$store} store icon will show - make sure and enter the URL to your App below.",
            'order'    => $num++,
            'section'  => 'app stores'
        );
        jrCore_register_setting('jrProximaAlpha', $_tmp);

        // App Store URL
        $_tmp = array(
            'name'     => "{$store}_url",
            'type'     => 'text',
            'default'  => '',
            'validate' => 'url',
            'label'    => ucfirst($store) . " App URL",
            'help'     => "Enter the URL to your app in the {$store} store",
            'order'    => $num++,
            'section'  => 'app stores'
        );
        jrCore_register_setting('jrProximaAlpha', $_tmp);
    }

    // Social Media
    $num = 30;
    foreach (array('twitter', 'facebook', 'google', 'linkedin') as $network) {

        // App Store URL
        $_tmp = array(
            'name'     => "{$network}_name",
            'type'     => 'text',
            'default'  => '',
            'validate' => 'printable',
            'label'    => ucfirst($network) . " profile",
            'help'     => "If you have an account for your app on " . ucfirst(str_replace('_', ' ', $network)) . ", enter the profile name and the network icon will show in your footer.  Leave blank to disable.",
            'order'    => $num++,
            'section'  => 'social networks'
        );
        jrCore_register_setting('jrProximaAlpha', $_tmp);
    }

    // Main Box Headline
    $_tmp = array(
        'name'     => 'mb_headline',
        'type'     => 'text',
        'default'  => 'Welcome to Proxima',
        'validate' => 'printable',
        'label'    => 'Index Main Headline',
        'help'     => 'Enter the headline for the main box on the index page',
        'order'    => 91,
        'section'  => 'header'
    );
    jrCore_register_setting('jrProximaAlpha', $_tmp);

    // Main Box Text
    $_tmp = array(
        'name'     => 'mb_text',
        'type'     => 'textarea',
        'default'  => 'Thank you for installing Proxima!  Proxima combines a <i>modular and powerful</i> RESTful/JSON backend server for powering your mobile apps, as well as a complete front end for your website.  Proxima is easy to customize and runs on <i>your server</i> - it is 100% under your control.<br><br>You can customize the text on this page by <a href="core/skin_admin/global/skin=jrProximaAlpha/section=header">clicking here</a>.',
        'validate' => 'allowed_html',
        'label'    => 'Index Main Description',
        'help'     => 'Enter the descriptive text for the main box on the index page (Allowed HTML)',
        'order'    => 92,
        'section'  => 'header'
    );
    jrCore_register_setting('jrProximaAlpha', $_tmp);

    foreach (range(1, 3) as $num) {

        // Feature Active
        $_tmp = array(
            'name'     => "ft_{$num}_active",
            'type'     => 'checkbox',
            'default'  => 'on',
            'validate' => 'onoff',
            'label'    => "Feature {$num} Active",
            'help'     => "Select if feature box #{$num} is active on the index page",
            'section'  => "Feature {$num}",
            'order'    => ((100 + ($num * 10)) + 1)
        );
        if ($num > 3) {
            $_tmp['default'] = 'off';
        }
        jrCore_register_setting('jrProximaAlpha', $_tmp);

        // Feature Headline
        $_tmp = array(
            'name'     => "ft_{$num}_headline",
            'type'     => 'text',
            'default'  => "Feature Description {$num}",
            'validate' => 'printable',
            'label'    => "Feature {$num} Headline",
            'help'     => "Enter the headline for feature box #{$num} on the index page",
            'section'  => "Feature {$num}",
            'order'    => ((100 + ($num * 10)) + 2)
        );
        jrCore_register_setting('jrProximaAlpha', $_tmp);

        // Feature Description
        $_tmp = array(
            'name'     => "ft_{$num}_text",
            'type'     => 'textarea',
            'default'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus vel ipsum eget orci vehicula pellentesque dignissim nec odio. Donec lobortis est ipsum, non tempus magna scelerisque ac. Maecenas ut facilisis nibh. Morbi hendrerit malesuada lorem, sit amet pulvinar felis tempus eget. Donec posuere, eros sagittis fermentum pretium, sapien erat fringilla felis, sed porttitor magna diam sed lorem.',
            'validate' => 'allowed_html',
            'label'    => "Feature {$num} Description",
            'help'     => "Enter the descriptive text for feature box #{$num} on the index page",
            'section'  => "Feature {$num}",
            'order'    => ((100 + ($num * 10)) + 3)
        );
        jrCore_register_setting('jrProximaAlpha', $_tmp);

    }
    return true;
}
