<?php
/**
 * Jamroom System Core module
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
 * @copyright 2014 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Core provided Tips
 * @param $_post array
 * @param $_user array
 * @param $_conf array
 * @return mixed
 */
function jrCore_tips($_post, $_user, $_conf)
{
    global $_mods;

    if (!isset($_post['module']) || !isset($_mods["{$_post['module']}"])) {
        return false;
    }

    $module      = $_post['module'];
    $module_name = $_mods[$module]['module_name'];

    // Get registered tool views
    $_tools = jrCore_get_registered_module_features('jrCore', 'tool_view');
    $_quota = jrCore_get_registered_module_features('jrCore', 'quota_support');

    $murl       = jrCore_get_module_url($module);
    $cmurl      = jrCore_get_module_url('jrCore');
    $_skin_tabs = array();

    //-----------------------
    // module tabs
    //-----------------------
    $_t = array(
        'info'
    );
    // determine the tabs this module has.
    if (jrCore_module_has_visible_config($module)) {
        $_t[] = 'global';
    }
    if (isset($_quota[$module]) || is_file(APP_DIR . "/modules/{$module}/quota.php")) {
        $_t[] = 'quota';
    }
    if (isset($_tools[$module]) || jrCore_db_get_prefix($module)) {
        $_t[] = 'tools';
    }
    $_lang = jrUser_load_lang_strings();
    if (isset($_lang[$module])) {
        $_t[] = 'language';
    }
    if (is_dir(APP_DIR . "/modules/{$module}/img")) {
        $_t[] = 'images';
    }
    if (is_dir(APP_DIR . "/modules/{$module}/templates")) {
        $_t[] = 'templates';
    }

    $_module_tabs = array(
        array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => '#tinfo',
            'title'       => "{$module_name} &raquo;  Info",
            'text'        => 'Every module has an <b>Info</b> tab where you can find information about the module, any requirements the module needs, module notes, as well as the ability to activate, disable or delete the module.',
            'position'    => 'bottom center',
            'my_position' => 'top center'
        )
    );

    if (in_array('global', $_t)) {
        $_module_tabs[] = array(
            'view'     => 'VIEW_ACP_MODULES',
            'selector' => '#tglobal',
            'title'    => "{$module_name} &raquo; Global Config",
            'text'     => "The <strong>Global Config</strong> tab contains <b>global</b> settings that affect the way the <b>{$module_name}</b> module works system wide.",
            'position' => 'bottom center'
        );
    }
    if (in_array('quota', $_t)) {
        $_module_tabs[] = array(
            'view'      => 'VIEW_ACP_MODULES',
            'selector'  => '#tquota',
            'title'     => "{$module_name} &raquo; Quota Config",
            'text'      => "The <strong>Quota Config</strong> tab contains settings that can be adjusted per Quota for the <b>{$module_name}</b> module. <br><br> Every Profile on your site will be in a specific Quota. Use this secton to decide which Quotas are allowed access to features provided by the <b>{$module_name}</b> module.",
            'position'  => 'bottom center',
            'doc_url'   => 'https://www.jamroom.net/the-jamroom-network/documentation/using-jamroom/2985/quota',
            'doc_title' => 'Jamroom Admin Handbook &raquo; Quota'
        );
    }
    if (in_array('tools', $_t)) {
        $_module_tabs[] = array(
            'view'     => 'VIEW_ACP_MODULES',
            'selector' => '#ttools',
            'title'    => "{$module_name} &raquo; Tools",
            'text'     => "<strong>Tools</strong> provided by the <b>{$module_name}</b> module are found here.<br><br>Some modules will provide custom tools that help with their functionality.",
            'position' => 'bottom center'
        );
    }
    if (in_array('language', $_t)) {
        $_module_tabs[] = array(
            'view'     => 'VIEW_ACP_MODULES',
            'selector' => '#tlanguage',
            'title'    => "{$module_name} &raquo; Language",
            'text'     => "User-facing language strings of the <b>{$module_name}</b> module can be customized here.<br><br><strong>Note:</strong> some modules may not provide user-facing functionality - those modules will not have language strings that can be customized.",
            'position' => 'bottom center'
        );
    }
    if (in_array('images', $_t)) {
        $_module_tabs[] = array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => '#timages',
            'title'       => "{$module_name} &raquo; Images",
            'text'        => "You can override the default images provided by the <b>{$module_name}</b> module by uploading images of your own here.",
            'position'    => 'bottom center',
            'my_position' => 'top center',
            'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/jamroom-admin-handbook/3321/module-skin-images-tab',
            'doc_title'   => 'Jamroom Admin Handbook &raquo; Module/Skin IMAGES (tab)'
        );
    }
    if (in_array('templates', $_t)) {
        $_module_tabs[] = array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => '#ttemplates',
            'title'       => "{$module_name} &raquo; Templates",
            'text'        => "The <strong>templates</strong> section shows you the templates that exist in the <b>{$module_name}</b> module and allows you to customize them to suit your specific needs.<br><br><strong>Note:</strong> All custom template modifications are stored in the database so they remain even when a module is updated.",
            'position'    => 'bottom center',
            'my_position' => 'top center',
            'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/jamroom-admin-handbook/3183/using-the-template-editor',
            'doc_title'   => 'Jamroom Admin Handbook &raquo; Using the Template Editor'
        );
    }

    //-----------------------
    // accordion headers
    //-----------------------
    $_accordion = array(
        array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => '.accordion_section_admin',
            'title'       => 'Module Category: Admin',
            'text'        => 'Modules in the <b>Admin</b> category are related to functions that Master and Profile Admin users perform.<br><br>Click to expand this category and see the modules.',
            'position'    => 'top right',
            'my_position' => 'left top'
        ),
        array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => '.accordion_section_communication',
            'title'       => 'Module Category: Communication',
            'text'        => 'Modules in the <b>Communication</b> category are related to communicating with the users of the system - i.e. Newsletters, Email Support, Private Notes, etc.<br><br>Click to expand this category and see the modules.',
            'position'    => 'top right',
            'my_position' => 'left top'
        ),
        array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => '.accordion_section_cloud',
            'title'       => 'Module Category: Cloud',
            'text'        => 'Modules in the <b>Cloud</b> category are all used in the Jamroom Cloud package.<br><br>Click to expand this category and see the modules.',
            'position'    => 'top right',
            'my_position' => 'left top'
        ),
        array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => '.accordion_section_core',
            'title'       => 'Module Category: Core',
            'text'        => 'Modules in the <b>Core</b> category are the heart of the system - they provide the core functionality other modules rely on.<br><br>Click to expand this category and see the modules.',
            'position'    => 'top right',
            'my_position' => 'left top'
        ),
        array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => '.accordion_section_custom',
            'title'       => 'Module Category: Custom',
            'text'        => 'Modules in the <b>Custom</b> category specific to your site.  They have been created by other developers or by the Aparna module and are not available in the marketplace.<br><br>Click to expand this category and see the modules.',
            'position'    => 'top right',
            'my_position' => 'left top'
        ),
        array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => '.accordion_section_developer',
            'title'       => 'Module Category: Developer',
            'text'        => 'Modules in the <b>Developer</b> category are for developers to help build and debug the system.  Here you gain access to your database and other tools for construction.<br><br>Click to expand this category and see the modules.',
            'position'    => 'top right',
            'my_position' => 'left top'
        ),
        array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => '.accordion_section_ecommerce',
            'title'       => 'Module Category: ECommerce',
            'text'        => 'Modules in the <b>ECommerce</b> category are for selling things from your site. Payment related modules appear here.<br><br>Click to expand this category and see the modules.',
            'position'    => 'top right',
            'my_position' => 'left top'
        ),
        array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => '.accordion_section_forms',
            'title'       => 'Module Category: Forms',
            'text'        => 'Modules in the <b>Forms</b> category relate to forms. A form is a location where you recieve information from users of your site. For example - the Editor Embedded Media module is here since it allows your users to use their existing content in a variety of ways.<br><br>Click to expand this category and see the modules.',
            'position'    => 'right bottom',
            'my_position' => 'left bottom',
        ),
        array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => '.accordion_section_item-features',
            'title'       => 'Module Category: Item Features',
            'text'        => 'Modules in the <b>Item Features</b> category add <b>features</b> to existing module items on their Detail page. This includes features such as Item Tags, Comments, Item Ratings and Likes.<br><br>Click to expand this category and see the modules.',
            'position'    => 'right bottom',
            'my_position' => 'left bottom',
        ),
        array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => '.accordion_section_listing',
            'title'       => 'Module Category: Listing',
            'text'        => 'Modules in the <b>Listing</b> category relate to how data is displayed on your site when being listed.<br><br>Click to expand this category and see the modules.',
            'position'    => 'right bottom',
            'my_position' => 'left bottom',
        ),
        array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => '.accordion_section_profiles',
            'title'       => 'Module Category: Profiles',
            'text'        => 'Modules in the <b>Profiles</b> category show modules that will put an extra section on a profile.  These modules have a Quota Config tab to allow you to configure which Quotas show the features the modules provide.<br><br>Click to expand this category and see the modules.',
            'position'    => 'right bottom',
            'my_position' => 'left bottom',
            'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/jamroom-admin-handbook/2984/profile',
            'doc_title'   => 'Jamroom Admin Handbook &raquo; Profile'
        ),
        array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => '.accordion_section_site',
            'title'       => 'Module Category: Site',
            'text'        => 'Modules in the <b>Site</b> category relate to the non-profile section of the system visible to visitors. Top level pages and pages built with Site Builder are all site level pages. Also here are site-wide level feature modules like google analytics and the url scanner.<br><br>Click to expand this category and see the modules.',
            'position'    => 'right bottom',
            'my_position' => 'left bottom',
        ),
        array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => '.accordion_section_tools',
            'title'       => 'Module Category: Tools',
            'text'        => 'Modules in the <b>Tools</b> category provide site tools. Here you will find import tools to bring your data from other systems, the sitemap creator, meta tag manager, etc.<br><br>Click to expand this category and see the modules.',
            'position'    => 'right bottom',
            'my_position' => 'left bottom',
        ),
        array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => '.accordion_section_users',
            'title'       => 'Module Category: Users',
            'text'        => 'Modules in the <b>Users</b> category relate to the person sitting at the keyboard looking at your site. That user may have multiple profiles but only 1 user account.  User login security modules, playlists, daily limits and related modules are found here.<br><br>Click to expand this category and see the modules.',
            'position'    => 'right bottom',
            'my_position' => 'left bottom',
            'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/jamroom-admin-handbook/2982/user',
            'doc_title'   => 'Jamroom Admin Handbook &raquo; User'
        ),
    );

    //-----------------------------
    // modules info as tool tips
    //-----------------------------
    $_module_info = array();
    foreach ($_mods as $dir => $_m) {
        $title = "Module: {$_m['module_name']}";
        $text  = $_m['module_description'];
        if ($_m['module_active'] != 1) {
            $text .= "<br><br><b>This module is currently disabled</b>";
        }
        $_tmp = array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => ".tt{$dir}",
            'title'       => $title,
            'text'        => $text,
            'position'    => 'top right',
            'my_position' => 'left top'
        );
        $_mt = jrCore_module_meta_data($dir);
        if (isset($_mt['doc_url'])) {
            $_tmp['doc_url']   = $_mt['doc_url'];
            $_tmp['doc_title'] = $_m['module_name'] . ' documentation';
        }
        $_module_info[] = $_tmp;
    }

    //-----------------------------
    // skin info as tool tips
    //-----------------------------
    $_skin_info = array();
    $skin_name  = '';
    if (isset($_post['option']) && $_post['option'] == 'skin_admin' && isset($_post['skin'])) {
        $_skins = jrCore_get_skins();
        if (in_array($_post['skin'], $_skins)) {
            $_sk       = jrCore_skin_meta_data($_post['skin']);
            $skin_name = $_sk['title'];
        }
        foreach ($_skins as $dir) {
            $_s    = jrCore_skin_meta_data($dir);
            $title = "Skin: {$_s['title']}";
            $text  = $_s['description'];
            $_tmp  = array(
                'view'        => 'VIEW_ACP_SKINS',
                'selector'    => ".tt{$dir}",
                'title'       => $title,
                'text'        => $text,
                'position'    => 'top right',
                'my_position' => 'left top',
            );
            if (isset($_docs[$dir]) && is_array($_docs[$dir])) {
                $_tmp['doc_url']   = $_docs[$dir]['doc_url'];
                $_tmp['doc_title'] = $_docs[$dir]['doc_title'] . ' documentation';
            }
            $_skin_info[] = $_tmp;
        }
        //----------------------
        // Skin Tabs
        //----------------------
        $_skin_tabs = array(
            array(
                'view'     => 'VIEW_ACP_SKINS',
                'selector' => '#tglobal',
                'title'    => "{$skin_name} &raquo; Global Config",
                'text'     => "The <strong>Global Config</strong> tab skin specifc settings that add extra sections or options to the current skin. <br><br>Settings in this section only effect the <strong>{$skin_name}</strong> skin when it is active.",
                'position' => 'bottom center'
            ),
            array(
                'view'     => 'VIEW_ACP_SKINS',
                'selector' => '#tstyle',
                'title'    => "{$skin_name} &raquo; Style",
                'text'     => "The <strong>Style</strong> tab provides access to the CSS styles that make your site look how it looks.  <br><br>Adjust the <strong>{$skin_name}</strong> skin styles from here.",
                'position' => 'bottom center'
            ),
            array(
                'view'        => 'VIEW_ACP_SKINS',
                'selector'    => '#timages',
                'title'       => "{$skin_name} &raquo; Images",
                'text'        => "You can override the default images provided by this module by uploading images of your own here. <br><br>All images here are provided by the <strong>{$skin_name}</strong> skin.",
                'position'    => 'bottom center',
                'my_position' => 'top center',
                'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/jamroom-admin-handbook/3321/module-skin-images-tab',
                'doc_title'   => 'Jamroom Admin Handbook &raquo; Module/Skin IMAGES (tab)'

            ),
            array(
                'view'      => 'VIEW_ACP_SKINS',
                'selector'  => '#tlanguage',
                'title'     => "{$skin_name} &raquo; Language",
                'text'      => "The <strong>Language</strong> tab allows you to alter the language strings provided by the skin. <br><br>All language strings here are only found in the <strong>{$skin_name}</strong> skin.",
                'position'  => 'bottom center',
                'doc_url'   => 'https://www.jamroom.net/the-jamroom-network/documentation/jamroom-admin-handbook/541/translate-jamroom-into-another-language',
                'doc_title' => 'Jamroom Admin Handbook &raquo; Translate Jamroom into another Language'
            ),
            array(
                'view'        => 'VIEW_ACP_SKINS',
                'selector'    => '#ttemplates',
                'title'       => "{$skin_name} &raquo; Templates",
                'text'        => "The <strong>Templates</strong> section shows you the templates that exist in the <strong>{$skin_name}</strong> skin. You can customize them to suit your specific needs.<br><br><strong>Note:</strong> All custom template modifications are stored in the database so they remain even when a module is updated.",
                'position'    => 'bottom center',
                'my_position' => 'top center',
                'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/jamroom-admin-handbook/3183/using-the-template-editor',
                'doc_title'   => 'Jamroom Admin Handbook &raquo; Using the Template Editor'
            ),
            array(
                'view'        => 'VIEW_ACP_SKINS',
                'selector'    => '#tinfo',
                'title'       => "{$skin_name} &raquo;  Info",
                'text'        => 'Every skin has an <b>Info</b> tab where you can find information about the skin. This is where you set the skin as the active skin for the site. <br><br>Tip: inactive skins can be deleted from the system from here too using the DELETE SKIN button.',
                'position'    => 'bottom center',
                'my_position' => 'top center'
            ),
        );
    }

    //------------------------------
    // Dashboard, Modules, Skins
    //------------------------------
    $_dash_mod_skin = array(
        array(
            'view'      => 'VIEW_ACP_ALL',
            'selector'  => '#dtab',
            'title'     => 'Dashboard',
            'text'      => 'The Dashboard makes it easy to keep an eye on your system. You will find easy access to the Activity Logs, Who\'s Online, Pending Items and more.<br><br><b>Tip:</b> Master Admin and Profile Admins have access to the Dashboard.',
            'position'  => 'bottom right',
            'doc_url'   => 'https://www.jamroom.net/the-jamroom-network/documentation/jamroom-admin-handbook/2982/user#user-group',
            'doc_title' => 'Jamroom Admin Handbook &raquo; User : User Group'
        ),
        array(
            'view'      => 'VIEW_ACP_ALL',
            'selector'  => '#mtab',
            'title'     => 'Modules',
            'text'      => "All modules installed in this system are found here.<br><br>Modules provide <strong>specific functionality</strong> for your site.<br><br>New modules can be installed using the <a href=\"{$_conf['jrCore_base_url']}/market/browse\"><strong>Marketplace</strong></a>.<br><br><b>Tip:</b> Only Master Admins have access to the Modules section.",
            'position'  => 'bottom right',
            'doc_url'   => 'https://www.jamroom.net/the-jamroom-network/documentation/getting-started/1767/keeping-jamroom-up-to-date',
            'doc_title' => 'Keeping Jamroom Up-To-Date'
        ),
        array(
            'view'      => 'VIEW_ACP_ALL',
            'selector'  => '#stab',
            'title'     => 'Skins',
            'text'      => 'You can view installed Skins by clicking on the &quot;Skins&quot; tab.<br><br>Skins define the <strong>look and feel</strong> of your site, and can be customized to suit your needs.<br><br><b>Tip:</b> Only Master Admins have access to the Skins section.',
            'position'  => 'bottom right',
            'doc_url'   => 'https://www.jamroom.net/the-jamroom-network/documentation/skins/709/changing-the-site-skin',
            'doc_title' => 'Changing your Jamroom Skin'
        )
    );

    //-----------------------
    // Core module specific
    //-----------------------
    $_system_core = array(
        array(
            'view'        => "{$cmurl}/system_check",
            'selector'    => '.page_banner_left',
            'title'       => 'System Check',
            'text'        => 'The System Check tool validates your server to ensure it is setup properly to run Jamroom.<br><br>Entries marked with a <strong>red</strong> result indicate a possible problem - check the <strong>Note</strong> section for help that addresses the issue.',
            'position'    => 'bottom center',
            'my_position' => 'top center',
            'button'      => 'Close',
        ),
        array(
            'view'        => "{$cmurl}/admin/global",
            'selector'    => '#tgeneral',
            'title'       => "Global Config &raquo;  General",
            'text'        => 'General system settings for the <b>System Core</b> module.',
            'position'    => 'bottom center',
            'my_position' => 'top left'
        ),
        array(
            'view'        => "{$cmurl}/admin/global",
            'selector'    => '#tdate-and-time',
            'title'       => "Global Config &raquo;  Date and Time",
            'text'        => 'Set your server date and time settings.  Choose the format you want date and time entries to be displayed in.',
            'position'    => 'bottom center',
            'my_position' => 'top left'
        ),
        array(
            'view'        => "{$cmurl}/admin/global",
            'selector'    => '#tmaintenance',
            'title'       => "Global Config &raquo; Maintenance",
            'text'        => 'Maintanence mode will disable your site for non-admin users.  This is commonly used when you need to perform maintenance on your system and want to prevent users from logging in while the site work is being done.',
            'position'    => 'bottom center',
            'my_position' => 'top left',
            'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/jamroom-admin-handbook/2851/maintenance-mode',
            'doc_title'   => 'Jamroom Admin Handbook &raquo; Maintenance Mode'
        ),
        array(
            'view'        => "{$cmurl}/admin/global",
            'selector'    => '#tdatastore',
            'title'       => "Global Config &raquo; DataStore",
            'text'        => 'A datastore is a special type of database table which most modules use to store their items. Settings related to DataStore performance and functionality can be found here.',
            'position'    => 'bottom center',
            'my_position' => 'top left',
            'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/jamroom-developers-guide/1023/datastores',
            'doc_title'   => 'Jamroom Developers Guide &raquo; datastores '
        ),
        array(
            'view'        => "{$cmurl}/admin/global",
            'selector'    => '#tmedia-system',
            'title'       => "Global Config &raquo; Media System",
            'text'        => 'Jamroom can store media on different file systems. The default file system is the hard drive in your server - if modules that provide alternate media file systems are installed, you will be able to select the active Media System here.',
            'position'    => 'bottom center',
            'my_position' => 'top right',
            'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/jamroom-admin-handbook/3387/use-amazon-s3-storage-to-lower-hosting-costs',
            'doc_title'   => 'Use Amazon S3 to lower storage costs'
        ),
        array(
            'view'        => "{$cmurl}/admin/global",
            'selector'    => '#trecycle-bin',
            'title'       => "Global Config &raquo; Recycle Bin",
            'text'        => 'This section contains configuration settings for the Recycle Bin (found in the Dashboard).',
            'position'    => 'bottom center',
            'my_position' => 'top right',
            'doc_url'     => "{$_conf['jrCore_base_url']}/{$cmurl}/dashboard/recycle_bin",
            'doc_title'   => 'Dashboard &raquo; Recycle Bin'
        ),
    );

    //-----------------------
    // Common sections
    //-----------------------
    $_common = array(
        array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => '.form_select_item_jumper',
            'title'       => 'Jump between Modules',
            'text'        => 'You can quickly switch to the same tab on any other module using the <strong>module jumper</strong>.  Jump from the <i>Quota Config</i> tab on one module to the <i>Quota Config</i> tab on another module.<br><br>Tip: The module jumper is just another form of navigation meant to save you clicks - you can always use the main module navigation menu on the left.',
            'position'    => 'bottom center',
            'my_position' => 'top right'
        ),
        array(
            'view'        => 'VIEW_ACP_MODULES',
            'selector'    => '.form_admin_search',
            'title'       => 'Search the module section',
            'text'        => 'Use the search box to search the <b>Modules</b> section of the ACP',
            'position'    => 'bottom center',
            'my_position' => 'top left'
        ),
        array(
            'view'        => 'VIEW_ACP_SKINS',
            'selector'    => '.form_select_item_jumper',
            'title'       => 'Jump between Skins',
            'text'        => 'You can quickly switch to the same tab on any other skin using the <strong>skin jumper</strong>.  Jump from the <i>Global Config</i> tab on one skin to the <i>Global Config</i> tab on another skin.<br><br>Tip: The skin jumper is just another form of navigation meant to save you clicks - you can always use the main skin navigation menu on the left.',
            'position'    => 'bottom center',
            'my_position' => 'top right'
        ),
        array(
            'view'        => 'VIEW_ACP_SKINS',
            'selector'    => '.form_admin_search',
            'title'       => 'Search the skin section',
            'text'        => 'Use the search box to search the <b>Skins</b> section of the ACP.',
            'position'    => 'bottom center',
            'my_position' => 'top left'
        ),
        array(
            'view'        => 'VIEW_ACP_ALL',
            'selector'    => '.form_help_button',
            'title'       => 'Get Help for Form Fields',
            'text'        => 'Most form fields have a &quot;help&quot; button to the right of the form field - click on it to get detailed help for the field including valid options, last updated by and reset options.',
            'position'    => 'bottom center',
            'my_position' => 'top right'
        ),
        array(
            'view'        => "{$murl}/admin/info",
            'selector'    => '#delete-module',
            'title'       => "{$module_name} &raquo; Delete",
            'text'        => "If you want to delete this module from the system, click this button.  This can only be done after the module has been deactivated.",
            'position'    => 'bottom center',
            'my_position' => 'top right',
        ),
        array(
            'view'        => "VIEW_ACP_SKINS",
            'selector'    => '#delete-skin',
            'title'       => "{$skin_name} &raquo; Delete",
            'text'        => "If you want to delete this skin from the system, click this button.  This can only be if the skin is not active.",
            'position'    => 'bottom center',
            'my_position' => 'top right',
        ),
        array(
            'view'        => "{$murl}/admin/info",
            'selector'    => '#delete-module',
            'title'       => "MODULE NAME &raquo; Delete",
            'text'        => "If you want to delete this module from the system click this button - this can only be done after the module has been deactivated.",
            'position'    => 'bottom center',
            'my_position' => 'top right',
        ),
    );

    //----------------------
    // Dashboard
    //----------------------
    $_dashboard              = array(
        array(
            'view'        => "VIEW_DASHBOARD",
            'selector'    => '#tacp',
            'title'       => "ACP &nbsp;<span style='font-weight:normal'>(<b>A</b>dmin <b>C</b>ontrol <b>P</b>anel)</span>",
            'text'        => "The <b>Admin Control Panel</b> is where the Master Admin sets up how the site is going to work.  Enabling modules, setting up quotas and changing the active skin are all done in the ACP.",
            'position'    => 'bottom center',
            'my_position' => 'top left',
            'doc_url'     => "https://www.jamroom.net/the-jamroom-network/documentation/jamroom-admin-handbook/3067/admin-control-panel-acp",
            'doc_title'   => 'Jamroom Admin Handbook  &raquo; Admin Control Panel (ACP)'
        ),
        array(
            'view'        => "VIEW_DASHBOARD",
            'selector'    => '#tbigview',
            'title'       => "Dashboard",
            'text'        => "The <b>Dashboard</b> shows a selection of panels that report on metrics available in your system.  You can add extra panels by customizing the dashboard to show just the metrics that most interest you.",
            'position'    => 'bottom center',
            'my_position' => 'top left',
            'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/jamroom-admin-handbook/3072/dashboard',
            'doc_title'   => 'Jamroom Admin Handbook &raquo; Dashboard'
        ),
        array(
            'view'        => "VIEW_DASHBOARD",
            'selector'    => '#tonline',
            'title'       => "Dashboard &raquo; Users Online",
            'text'        => "This <b>Users Online</b> section shows you who has been active on your site in the last 15 minutes. It includes visitors and logged in users and shows you the page they are looking at.",
            'position'    => 'bottom center',
            'my_position' => 'top left',
        ),
        array(
            'view'        => "VIEW_DASHBOARD",
            'selector'    => '#tpending',
            'title'       => "Dashboard &raquo; Pending",
            'text'        => "The <b>Pending</b> section is where you will find items waiting to be approved before they become visible in your system. Items in Quotas that are setup to require approval will be found here.",
            'position'    => 'bottom center',
            'my_position' => 'top center',
            'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/jamroom-admin-handbook/1853/the-pending-items-approval-system',
            'doc_title'   => 'Jamroom Admin Handbook &raquo; The Pending Items Approval System '
        ),
        array(
            'view'        => "VIEW_DASHBOARD",
            'selector'    => '#tactivity',
            'title'       => "Dashboard &raquo; Activity Log",
            'text'        => "The <b>Activity Log</b> contains important messages about activity that has occured in your system.<br><br>You will find tabs for the Debug Log and Error Log here as well.",
            'position'    => 'bottom center',
            'my_position' => 'top center',
            'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/jamroom-admin-handbook/4290/activity-log',
            'doc_title'   => 'Jamroom Admin Handbook &raquo; Activity Log'
        ),
        array(
            'view'        => "VIEW_DASHBOARD",
            'selector'    => '#tdebug',
            'title'       => "Debug Log",
            'text'        => "The <b>Debug Log</b> can contain information to help Jamroom Developers &quot;debug&quot; an issue if something has gone wrong.",
            'position'    => 'bottom center',
            'my_position' => 'top center',
            'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/jamroom-admin-handbook/4290/activity-log',
            'doc_title'   => 'Jamroom Admin Handbook &raquo; Activity Log'
        ),
        array(
            'view'        => "VIEW_DASHBOARD",
            'selector'    => '#terror',
            'title'       => "Error Log",
            'text'        => "The <b>Error Log</b> can contain PHP ERROR messages (and NOTICE messages if you are running in Developer Mode).  This information can be useful to Jamroom Developers.",
            'position'    => 'bottom center',
            'my_position' => 'top center',
            'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/jamroom-admin-handbook/4290/activity-log',
            'doc_title'   => 'Jamroom Admin Handbook &raquo; Activity Log'
        ),
        array(
            'view'        => "VIEW_DASHBOARD",
            'selector'    => '#tbrowser',
            'title'       => "Dashboard &raquo;  Data Browser",
            'text'        => "The Data Browser allows you to view data stored in a module DataStore.<br><br>Most modules store their data in a DataStore - this tool allows you to search the data. Profiles and Users are all searchable from here.",
            'position'    => 'bottom center',
            'my_position' => 'top center',
            'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/jamroom-developers-guide/1023/datastores',
            'doc_title'   => 'Jamroom Developers Guide &raquo; datastores '
        ),
        array(
            'view'        => "VIEW_DASHBOARD",
            'selector'    => '#trecycle-bin',
            'title'       => "Dashboard &raquo;  Recycle Bin",
            'text'        => "When an item is deleted from profile it ends up here in the Recycle Bin.  One an item is in the Recycle Bin, Profile Admins and Master Admins can return the item to the system if it was deleted by accident.<br><br> Settings for the Recycle bin can be adjusted by Master Admins in the Core Global Config.",
            'position'    => 'bottom center',
            'my_position' => 'top right',
            'doc_url'     => "{$_conf['jrCore_base_url']}/{$cmurl}/admin/global/section=recycle+bin",
            'doc_title'   => 'System Core &raquo; Global Config &raquo; Recycle Bin'
        ),
        array(
            'view'        => "VIEW_DASHBOARD",
            'selector'    => '#tqueue-viewer',
            'title'       => "Dashboard &raquo; Queue Viewer",
            'text'        => "Jamroom uses Queues to let modules perform background tasks such as emailing, media conversions, cache maintenance, etc.  <br><br>The <b>Queue Viewer</b> lets you see what tasks are active or waiting to be performed.",
            'position'    => 'bottom center',
            'my_position' => 'top right',
            'doc_url'     => "https://www.jamroom.net/the-jamroom-network/documentation/jamroom-developers-guide/1543/the-queueing-system",
            'doc_title'   => 'Jamroom Developers Guide &raquo; The Queueing System'
        ),
    );
    $_dashboard_online       = array(
        array(
            'view'        => "{$murl}/dashboard/online",
            'selector'    => '#sb',
            'title'       => "Users Online &raquo; Show Bots",
            'text'        => "The <b>Show/Hide bots</b> button turns on/off the visibility of bots in your users online section. Search engines send out automated programs to view your site, these show up in the list of online users. You can choose to have them visible along with human users or hide them.",
            'position'    => 'bottom center',
            'my_position' => 'top right',
            'doc_url'     => "https://en.wikipedia.org/wiki/Internet_bot",
            'doc_title'   => 'Wikipedia: Internet Bot'
        ),
    );
    $_dashboard_data_browser = array(
        array(
            'view'        => "{$murl}/dashboard/browser",
            'selector'    => '#export',
            'title'       => "Export CSV",
            'text'        => "Download the contents of this modules Datastore as a CSV file.  CSV files can be imported into spreadsheet programs like Microsoft Excel.",
            'position'    => 'bottom center',
            'my_position' => 'top right',
            'doc_url'     => "https://en.wikipedia.org/wiki/Comma-separated_values",
            'doc_title'   => 'Wikipedia: Comma Separated Values'
        ),
        array(
            'view'        => "{$murl}/dashboard/browser",
            'selector'    => '#raw',
            'title'       => "View Keys",
            'text'        => "The <b>View Keys</b> / <b>View Browser</b> switch allows you to switch between two different ways of looking at the data in the currently selected datastore.  The Browser tries to show the format in a way that is easily readable, while the Keys view shows the same data as it is stored in the datastore.",
            'position'    => 'bottom center',
            'my_position' => 'top right',
            'doc_url'     => "https://en.wikipedia.org/wiki/Comma-separated_values",
            'doc_title'   => 'Wikipedia: Comma Separated Values'
        ),
        array(
            'view'        => "{$murl}/dashboard/browser",
            'selector'    => '.form_select_item_jumper',
            'title'       => "Select datastore",
            'text'        => "Switch between modules. Use this drop down menu to select the datastore of the module you're interested in.  The list is arranged in the same order as the modules are displayed in the categories of the ACP.",
            'position'    => 'bottom center',
            'my_position' => 'top right',
        ),
    );
    if (isset($_post['option']) && $_post['option'] == 'dashboard') {
        $_out = array_merge($_dashboard, $_dashboard_online, $_dashboard_data_browser);
    }
    elseif (isset($_post['option']) && $_post['option'] == 'skin_admin') {
        $_out = array_merge($_skin_tabs, $_skin_info, $_dash_mod_skin);
    }
    else {
        $_out = array_merge($_system_core, $_accordion, $_dash_mod_skin, $_module_tabs, $_module_info, $_common);
    }

    // If we have a new install, let's show a small tip to create account on the front page
    if (!jrUser_is_logged_in() && jrCore_db_get_datastore_item_count('jrUser') === 0) {
        $_out[] = array(
            'view'        => $_conf['jrCore_base_url'] . '$',
            'selector'    => '#user-create-account',
            'title'       => 'Create your User Account',
            'text'        => 'Click on the <strong>Create Account</strong> to create your User Account.<br><br>The first User Account created is created as a <strong>Master Admin</strong>.',
            'position'    => 'bottom center',
            'my_position' => 'top right',
            'group'       => 'visitor',
            'cookie'      => false,
        );
    }

    return $_out;
}
