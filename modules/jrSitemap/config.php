<?php
/**
 * Jamroom 5 Sitemap Generator module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
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
 * @copyright 2015 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * config
 */
function jrSitemap_config()
{
    $_opt = array(
        'hourly'  => 'hourly',
        'daily'   => 'daily',
        'weekly'  => 'weekly',
        'monthly' => 'monthly',
        'yearly'  => 'yearly'
    );

    // Site Change Frequency
    $_tmp = array(
        'name'    => 'site_freq',
        'type'    => 'select',
        'options' => $_opt,
        'default' => 'daily',
        'label'   => 'site page frequency',
        'help'    => 'Select the recommendation for how often site pages should be updated in the sitemap.xml file.<br><br><strong>Note:</strong> This is only a recommendation - search engines are free to ignore this value.',
        'order'   => 1
    );
    jrCore_register_setting('jrSitemap', $_tmp);

    // Profile Change Frequency
    $_tmp = array(
        'name'    => 'profile_freq',
        'type'    => 'select',
        'options' => $_opt,
        'default' => 'daily',
        'label'   => 'profile page frequency',
        'help'    => 'Select the recommendation for how often profile pages should be updated in the sitemap.xml file.<br><br><strong>Note:</strong> This is only a recommendation - search engines are free to ignore this value.',
        'order'   => 2
    );
    jrCore_register_setting('jrSitemap', $_tmp);

    // File Count
    $_tmp = array(
        'name'    => 'file_count',
        'type'    => 'hidden',
        'default' => 0,
        'label'   => 'XML File Count (hidden)',
        'help'    => 'This hidden field keeps track of the number of XML files created - do not modify',
        'order'   => 10
    );
    jrCore_register_setting('jrSitemap', $_tmp);

    return true;
}
