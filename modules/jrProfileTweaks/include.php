<?php
/**
 * Jamroom Profile Tweaks module
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
 * can be found in the "contrib" directory within this module.
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function jrProfileTweaks_meta()
{
    $_tmp = array(
        'name'        => 'Profile Tweaks',
        'url'         => 'profiletweaks',
        'version'     => '1.3.14',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Allow Profile owners to customize elements of their profile page',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2910/profile-tweaks',
        'category'    => 'profiles',
        'requires'    => 'jrCore:6.0.7',
        'license'     => 'jcl',
        'priority'    => 251 // LOW load priority (we want other listeners to run first)
    );
    return $_tmp;
}

/**
 * init
 */
function jrProfileTweaks_init()
{
    // Core module support
    $_options = array(
        'label' => 'Allow Customization',
        'help'  => 'If checked, profiles in this Quota will be allowed to customize their profile.'
    );
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrProfileTweaks', 'on', $_options);

    // Setup our "Customize" tab for the User Account section
    jrCore_register_module_feature('jrUser', 'account_tab', 'jrProfileTweaks', 'customize', 1);

    // No session needed for background image
    jrCore_register_module_feature('jrUser', 'skip_session', 'jrProfileTweaks', 'logo');
    jrCore_register_module_feature('jrUser', 'skip_session', 'jrProfileTweaks', 'background');

    // We listen for profile view events
    jrCore_register_event_listener('jrProfile', 'profile_view', 'jrProfileTweaks_profile_view_listener');
    jrCore_register_event_listener('jrProfile', 'profile_index', 'jrProfileTweaks_profile_index_listener');

    // Listen for custom logo replacement
    jrCore_register_event_listener('jrImage', 'skin_image', 'jrProfileTweaks_skin_image_listener');

    // Custom Skin
    jrCore_register_event_listener('jrCore', 'profile_template', 'jrProfileTweaks_profile_template_listener');
    jrCore_register_event_listener('jrCore', 'template_file', 'jrProfileTweaks_template_file_listener');

    jrCore_register_module_feature('jrCore', 'javascript', 'jrProfileTweaks', true);

    jrCore_register_module_feature('jrCore', 'javascript', 'jrProfileTweaks', 'jrProfileTweaks.js');

    return true;
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Check for custom skin
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrProfileTweaks_profile_template_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_conf;
    if (isset($_data['module_url']) && strlen($_data['module_url']) > 0) {
        $_rt = jrCore_db_get_item_by_key('jrProfile', 'profile_url', $_data['module_url']);
        // Custom Profile Skin ( chosen by the profile owner )
        if ($_rt && isset($_rt['profile_custom_skin']) && strlen($_rt['profile_custom_skin']) > 0) {

            // Is the skin the profile is configured with allowed in the profile?
            if (strpos(' ,' . $_rt['quota_jrProfileTweaks_allow_skin'] . ',', ',' . $_rt['profile_custom_skin'] . ',')) {
                jrProfileTweaks_set_profile_skin($_rt['profile_custom_skin']);
            }
            elseif (strpos(' ,' . $_rt['quota_jrProfileTweaks_allow_skin'] . ',', ',' . $_rt['quota_jrProfileTweaks_default_skin'] . ',')) {
                jrProfileTweaks_set_profile_skin($_rt['quota_jrProfileTweaks_default_skin']);
            }
        }
        // Custom Profile Skin ( default for this quota from the jrProfileTweaks module )
        elseif (strpos(' ,' . $_rt['quota_jrProfileTweaks_allow_skin'] . ',', ',' . $_rt['quota_jrProfileTweaks_default_skin'] . ',')) {
            jrProfileTweaks_set_profile_skin($_rt['quota_jrProfileTweaks_default_skin']);
        }

    }
    return $_data;
}

/*
 * Set the profile skin
 */
function jrProfileTweaks_set_profile_skin($skin)
{
    global $_conf;
    jrCore_set_flag('jrprofiletweaks_custom_skin', $skin);
    $_conf = jrCore_load_skin_config($skin, $_conf);
    $fnc   = "{$skin}_skin_init";
    if (!function_exists($fnc)) {
        if (is_file(APP_DIR . "/skins/{$skin}/include.php")) {
            require_once APP_DIR . "/skins/{$skin}/include.php";
        }
    }
    if (function_exists($fnc)) {
        $_conf['jrCore_active_skin'] = $skin;
        $fnc();
    }
    return;
}

/**
 * Custom Skin support for Profiles
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrProfileTweaks_template_file_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($dir = jrCore_get_flag('jrprofiletweaks_custom_skin')) {
        if (isset($_data['directory']) && $_data['directory'] == $_conf['jrCore_active_skin']) {
            // See if we have an override for this file
            if (is_file(APP_DIR . "/skins/{$dir}/{$_data['template']}")) {
                $_data['directory'] = $dir;
            }
        }
    }
    return $_data;
}

/**
 * Add in custom site logo
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrProfileTweaks_skin_image_listener($_data, $_user, $_conf, $_args, $event)
{
    // $_data = full img src URL
    if (isset($_args['params']['custom']) && $_args['params']['custom'] == 'logo') {
        if (isset($_args['smarty']->tpl_vars['profile_logo_image_size'])) {
            // See if we are allowed...
            $all = $_args['smarty']->tpl_vars['quota_jrProfileTweaks_allow_logo']->value;
            $qta = $_args['smarty']->tpl_vars['quota_jrProfileTweaks_allowed']->value;
            if ($all && $all == 'on' && $qta && $qta == 'on') {
                $pid  = (int) $_args['smarty']->tpl_vars['_profile_id']->value;
                $ext  = $_args['smarty']->tpl_vars['profile_logo_image_extension']->value;
                $time = $_args['smarty']->tpl_vars['profile_logo_image_time']->value;
                // we have a Custom Profile Logo - get URL
                if (jrCore_get_active_media_system() == 'jrCore_local') {
                    $pturl = jrCore_get_module_url('jrProfileTweaks');
                    $_data = "{$_conf['jrCore_base_url']}/{$pturl}/logo/{$pid}/{$ext}?_v={$time}";
                }
                else {
                    $_data = jrCore_get_media_url($pid) . "/jrProfile_{$pid}_profile_logo_image.{$ext}?_v={$time}";
                }
            }
        }
    }
    return $_data;
}

/**
 * Add in custom background image if this profile has one
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrProfileTweaks_profile_view_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['_profile_id']) && isset($_data['quota_jrProfileTweaks_allowed']) && $_data['quota_jrProfileTweaks_allowed'] == 'on' && isset($_data['quota_jrProfileTweaks_allow_background_image']) && $_data['quota_jrProfileTweaks_allow_background_image'] == 'on' && isset($_data['profile_bg_image_size']) && jrCore_checktype($_data['profile_bg_image_size'], 'number_nz')) {
        // we have a Custom Profile Background Image - get URL
        if (jrCore_get_active_media_system() == 'jrCore_local') {
            $url = jrCore_get_module_url('jrProfileTweaks');
            $url = "{$_conf['jrCore_base_url']}/{$url}/background/{$_data['_profile_id']}/{$_data['profile_bg_image_time']}";
        }
        else {
            $url = jrCore_get_media_url($_data['_profile_id']) . "/jrProfile_{$_data['_profile_id']}_profile_bg_image.jpg?_v={$_data['profile_bg_image_time']}";
        }
        $add = ' background-size:100% 100%; -webkit-background-size:100%; background-repeat:no-repeat;background-position:top center; background-attachment:fixed;';
        if (isset($_data['profile_bg_tile']) && $_data['profile_bg_tile'] == 'on') {
            $add = ' background-position:0 0; background-repeat:repeat;';
        }
        $_bg = array("html{ background-image:url(\"{$url}\");{$add} }\nbody{ background-color:transparent;background-image:none;}\n#wrapper{background: transparent !important; background-color:transparent !important}");
        jrCore_create_page_element('css_embed', $_bg);
    }
    return $_data;
}

/**
 * Listen for Profile Index view and redirect to custom index
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrProfileTweaks_profile_index_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['_profile_id']) && jrCore_checktype($_data['_profile_id'], 'number_nz') && isset($_data['quota_jrProfileTweaks_allowed']) && $_data['quota_jrProfileTweaks_allowed'] == 'on') {

        // Custom Profile Index
        if (isset($_data['profile_index_page']{1}) && isset($_data['quota_jrProfileTweaks_allow_index_redirect']) && $_data['quota_jrProfileTweaks_allow_index_redirect'] == 'on') {
            $pid = false;
            if (strpos($_data['profile_index_page'], ':') && jrCore_module_is_active('jrPage')) {
                // Custom Page Module page
                list($mod, $pid) = explode(':', $_data['profile_index_page']);
                $mod = trim($mod);
                $pid = intval($pid);
            }
            else {
                $mod = $_data['profile_index_page'];
            }
            if (jrCore_module_is_active($mod) && isset($_data["quota_{$mod}_allowed"]) && $_data["quota_{$mod}_allowed"] == 'on') {
                $crl = $_conf['jrCore_base_url'];
                if (jrUser_is_logged_in() && isset($_conf['jrUser_force_ssl']) && $_conf['jrUser_force_ssl'] == 'on' && strpos($crl, 'https') !== 0) {
                    $crl = str_replace('http://', 'https://', $_conf['jrCore_base_url']);
                }
                if ($pid) {
                    $url = jrCore_db_get_item_key('jrPage', $pid, 'page_title_url');
                    if ($url && strlen($url) > 0) {
                        $purl = jrCore_get_module_url('jrPage');
                        jrCore_location("{$crl}/{$_data['profile_url']}/{$purl}/{$pid}/{$url}");
                    }
                }
                $url = jrCore_get_module_url($mod);
                jrCore_location("{$crl}/{$_data['profile_url']}/{$url}");
            }
        }

        // Default Profile Index
        elseif (isset($_data['quota_jrProfileTweaks_default_index']) && $_data['quota_jrProfileTweaks_default_index'] != '0') {
            // Make sure quota is allowed
            $mod = $_data['quota_jrProfileTweaks_default_index'];
            if (jrCore_module_is_active($mod) && $mod != 'jrAction' && isset($_data["quota_{$mod}_allowed"]) && $_data["quota_{$mod}_allowed"] == 'on') {
                $url = jrCore_get_module_url($_data['quota_jrProfileTweaks_default_index']);
                $crl = $_conf['jrCore_base_url'];
                if (jrUser_is_logged_in() && isset($_conf['jrUser_force_ssl']) && $_conf['jrUser_force_ssl'] == 'on' && strpos($crl, 'https') !== 0) {
                    $crl = str_replace('http://', 'https://', $_conf['jrCore_base_url']);
                }
                jrCore_location("{$crl}/{$_data['profile_url']}/{$url}");
            }
        }
    }
    return $_data;
}

//---------------------------------------------------------
// FUNCTIONS
//---------------------------------------------------------

/**
 * Get available skins for Profiles
 * @return array|bool
 */
function jrProfileTweaks_get_skins()
{
    $_sk = jrCore_get_skins();
    if (is_array($_sk) && count($_sk) === 1) {
        // Only our one skin - no choice
        return false;
    }
    $_ot = array();
    foreach ($_sk as $skin) {
        $_tm        = jrCore_skin_meta_data($skin);
        $_ot[$skin] = $_tm['title'];
    }
    return $_ot;
}

/**
 * Get module indexes that can be used as a profile index
 * @return array
 */
function jrProfileTweaks_get_index_modules()
{
    global $_mods;
    $_idx = array('0' => '(default) Skin Profile Index');
    foreach (array_keys($_mods) as $module) {
        switch ($module) {
            case 'jrCore':
            case 'jrProfile':
            case 'jrUser':
            case 'jrMailer':
                continue 2;
                break;
            default:
                // Module is NOT active
                if (!jrCore_module_is_active($module)) {
                    continue 2;
                }
                // Our module must have an item_index.tpl file...
                if (!is_file(APP_DIR . "/modules/{$module}/templates/item_index.tpl")) {
                    continue 2;
                }
                $_idx[$module] = $_mods[$module]['module_name'];
                break;
        }
    }

    if (count($_idx) > 0) {
        natcasesort($_idx);
    }
    return $_idx;
}
