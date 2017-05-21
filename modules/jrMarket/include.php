<?php
/**
 * Jamroom Marketplace module
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
 * meta
 */
function jrMarket_meta()
{
    $_tmp = array(
        'name'        => 'Marketplace',
        'url'         => 'marketplace',
        'version'     => '1.4.5',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Browse, Install and Update modules and skins from the Marketplace',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2865/marketplace',
        'category'    => 'core',
        'requires'    => 'jrCore:6.0.4',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrMarket_init()
{
    // Marketplace event triggers
    jrCore_register_event_trigger('jrMarket', 'installed_module', 'Fired when a new module is successfully installed from the Marketplace');
    jrCore_register_event_trigger('jrMarket', 'installed_skin', 'Fired when a new skin is successfully installed from the Marketplace');
    jrCore_register_event_trigger('jrMarket', 'updated_module', 'Fired when a module is successfully updated in System Update');
    jrCore_register_event_trigger('jrMarket', 'updated_skin', 'Fired when a skin is successfully updated in System Update');

    jrCore_register_module_feature('jrCore', 'tool_view', 'jrMarket', 'systems', array('Marketplace Systems', 'Create and Update Marketplace Systems'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrMarket', 'release_channels', array('Marketplace Channels', 'Add and Remove Marketplace Release Channels for System modules and skins'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrMarket', 'system_update/all=1', array('Reload Modules or Skins', 'Reload a Module or Skin from the Marketplace'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrMarket', 'system_archive', array('Release Archive', 'Previous version of modules and skins that can be restored'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrMarket', 'history', array('Install History', 'View information about previous module and skin installs'));

    jrCore_register_event_listener('jrCore', 'system_check', 'jrMarket_system_check_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrMarket_verify_module_listener');

    // Custom tabs
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrMarket', 'browse', 'Marketplace');
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrMarket', 'system_update', 'System Updates');

    // Our default master view
    jrCore_register_module_feature('jrCore', 'default_admin_view', 'jrMarket', 'browse');

    // Register our JS and CSS
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMarket', 'jrMarket.js', 'admin');
    jrCore_register_module_feature('jrCore', 'css', 'jrMarket', 'jrMarket.css');

    // Our tips
    jrCore_register_module_feature('jrTips', 'tip', 'jrMarket', 'tip');

    // Check for updates
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrMarket_daily_maintenance_listener');

    // Master notifications
    $_tmp = array(
        'label' => 'updates available',
        'help'  => 'Do you want to be notified when Marketplace updates are available for the system?',
        'group' => 'master'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrMarket', 'updates_notify', $_tmp);

    // Our queue worker
    jrCore_register_queue_worker('jrMarket', 'check_for_updates', 'jrMarket_check_for_updates_worker', 1, 1, 3900);

    return true;
}

//-----------------------------------
// QUEUE WORKER
//-----------------------------------

/**
 * Check for updates in the Marketplace
 * @param $_queue array Queue entry
 * @return bool
 */
function jrMarket_check_for_updates_worker($_queue)
{
    global $_mods, $_conf;
    if ($_tmp = jrMarket_get_system_updates()) {
        $snd = false;
        $_up = array(
            'module' => array(),
            'skin'   => array()
        );
        if (isset($_tmp['module']) && is_array($_tmp['module'])) {
            foreach ($_tmp['module'] as $mod => $_m) {
                if (isset($_mods[$mod]['module_version']) && version_compare($_mods[$mod]['module_version'], $_m['v']) === -1) {
                    $_up['module'][$mod] = $_mods[$mod];
                    $snd                 = true;
                }
            }
        }
        if (isset($_tmp['skin']) && is_array($_tmp['skin'])) {
            foreach ($_tmp['skin'] as $skin => $_s) {
                $_inf = jrCore_skin_meta_data($skin);
                if (isset($_inf['version']) && version_compare($_inf['version'], $_s['v']) === -1) {
                    $_up['skin'][$skin] = $_inf;
                    $snd                = true;
                }
            }
        }
        if ($snd) {
            $_ad = jrUser_get_master_user_ids();
            if ($_ad && is_array($_ad)) {
                $_up['system_name'] = $_conf['jrCore_system_name'];
                list($sub, $msg) = jrCore_parse_email_templates('jrMarket', 'updates_available', $_up);
                foreach ($_ad as $uid) {
                    jrUser_notify($uid, 0, 'jrMarket', 'updates_notify', $sub, $msg);
                }
            }
        }
    }
    return true;
}

//-----------------------------------
// EVENT LISTENERS
//-----------------------------------

/**
 * Make sure any symlinks in modules and skins are setup right
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrMarket_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    // module invalid symlinks
    $_sl = glob(APP_DIR . '/modules/*-release-*');
    if ($_sl && is_array($_sl)) {
        foreach ($_sl as $dir) {
            if (is_link($dir)) {
                // We should NOT have this
                unlink($dir);
            }
        }
    }

    // skin invalid symlinks
    $_sl = glob(APP_DIR . '/skins/*-release-*');
    if ($_sl && is_array($_sl)) {
        foreach ($_sl as $dir) {
            if (is_link($dir)) {
                // We should NOT have this
                unlink($dir);
            }
        }
    }

    // Archive cleanup
    jrMarket_prune_archives();

    return $_data;
}

/**
 * Check for available system updates
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrMarket_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_conf['jrMarket_update_check']) && $_conf['jrMarket_update_check'] == 'on') {

        // We are checking for updates...
        $sleep = mt_rand(0, 3599);
        jrCore_queue_create('jrMarket', 'check_for_updates', array('sleep' => $sleep), $sleep);

    }

    // Archive cleanup
    jrMarket_prune_archives();

    return $_data;
}

//-----------------------------------
// functions
//-----------------------------------

/**
 * Prune the release archives
 * @return bool
 */
function jrMarket_prune_archives()
{
    global $_conf;
    if (isset($_conf['jrMarket_archive_versions']) && jrCore_checktype($_conf['jrMarket_archive_versions'], 'number_nz')) {
        // Cleanup is enabled
        // First do modules
        $tbl = jrCore_db_table_name('jrCore', 'module');
        $req = "SELECT module_directory FROM {$tbl} ORDER BY module_directory ASC";
        $_md = jrCore_db_query($req, 'module_directory', false, 'module_directory');
        if ($_md && is_array($_md)) {
            $num = 0;
            foreach ($_md as $mod) {
                $num += jrMarket_prune_release_archive('module', $mod, $_conf['jrMarket_archive_versions']);
            }
            if ($num > 0) {
                jrCore_logger('INF', "successfully pruned {$num} old module release archives");
            }
        }
        // Next do skins..
        $tbl = jrCore_db_table_name('jrCore', 'skin');
        $req = "SELECT skin_directory FROM {$tbl} ORDER BY skin_directory ASC";
        $_sk = jrCore_db_query($req, 'skin_directory', false, 'skin_directory');
        if ($_sk && is_array($_sk)) {
            $num = 0;
            foreach ($_sk as $skin) {
                $num += jrMarket_prune_release_archive('skin', $skin, $_conf['jrMarket_archive_versions']);
            }
            if ($num > 0) {
                jrCore_logger('INF', "successfully pruned {$num} old skin release archives");
            }
        }
    }
    return true;
}

/**
 * Cleanup the release archive for a module or skin
 * @param $type string module|skin
 * @param $dir string module or skin directory name
 * @param $versions int Number of versions to keep
 * @return bool
 */
function jrMarket_prune_release_archive($type, $dir, $versions)
{
    $_tmp = glob(APP_DIR . "/{$type}s/{$dir}-release-*");
    if ($_tmp && is_array($_tmp)) {
        // What is our LIVE version?
        if (is_link(APP_DIR . "/{$type}s/{$dir}")) {
            if ($current = readlink(APP_DIR . "/{$type}s/{$dir}")) {
                // We have our CURRENT version which we know we will never
                // remove - we need to get our versions in order
                $_old = array();
                foreach ($_tmp as $path) {
                    $path        = basename($path);
                    $temp        = explode('-', $path);
                    $temp        = end($temp);
                    $_old[$path] = $temp;
                }
                if (count($_old) > $versions) {
                    uasort($_old, 'version_compare');
                    $_old = array_slice($_old, 0, (count($_old) - $versions), true);
                    if ($_old && count($_old) > 0) {
                        foreach ($_old as $path => $ver) {
                            // Double check...
                            if (is_dir(APP_DIR . "/{$type}s/{$path}") && $ver != $current) {
                                if (jrCore_delete_dir_contents(APP_DIR . "/{$type}s/{$path}", false)) {
                                    rmdir(APP_DIR . "/{$type}s/{$path}");
                                }
                            }
                        }
                        return count($_old);
                    }
                }
            }
        }
    }
    return 0;
}

/**
 * Get license for a skin
 * @param $skin string Skin to get license for
 * @return mixed
 */
function jrMarket_get_skin_license($skin)
{
    $tbl = jrCore_db_table_name('jrCore', 'setting');
    $req = "SELECT `module`, `value` FROM {$tbl} WHERE `module` = '" . jrCore_db_escape($skin) . "' AND `name` = 'license' LIMIT 1";
    return jrCore_db_query($req, 'module', false, 'value');
}

/**
 * Get an array of available marketplace updates
 * @return bool|mixed|string
 */
function jrMarket_get_system_updates()
{
    global $_mods;
    if (!$_mkt = jrMarket_get_active_release_system()) {
        return false;
    }

    // Make sure we are subscribed to at least 1 channel
    $tbl = jrCore_db_table_name('jrMarket', 'channel');
    $req = "SELECT * FROM {$tbl} WHERE channel_active = '1'";
    $_rt = jrCore_db_query($req, 'channel_id');
    if (!isset($_rt) || !is_array($_rt)) {
        return false;
    }

    // Modules and Versions
    $_ad = array();
    foreach ($_mods as $_mod) {
        if (isset($_mod['module_license']{15})) {
            $_ad[] = "m[]={$_mod['module_directory']}&v[]={$_mod['module_version']}&l[]={$_mod['module_license']}";
        }
        else {
            $_ad[] = "m[]={$_mod['module_directory']}&v[]={$_mod['module_version']}&l[]=r";
        }
    }

    $_skins = jrCore_get_skins();
    if ($_skins && is_array($_skins)) {
        foreach ($_skins as $skin => $change) {
            $_sk           = jrCore_skin_meta_data($skin);
            $_skins[$skin] = $_sk;
            if (isset($_sk) && is_array($_sk)) {
                $_el = jrMarket_get_skin_license($skin);
                if ($_el && isset($_el[$skin])) {
                    $_ad[] = "sn[]={$skin}&sv[]={$_sk['version']}&sl[]=" . $_el[$skin];
                }
                else {
                    $_ad[] = "sn[]={$skin}&sv[]={$_sk['version']}&sl[]=r";
                }
            }
        }
    }
    // Subscribed Channels
    $_ch = array();
    foreach ($_rt as $_chan) {
        switch ($_chan['channel_name']) {
            case 'stable':
            case 'beta':
                $_ch[] = "c[]={$_chan['channel_name']}";
                break;
            default:
                if (isset($_chan['channel_code']{1})) {
                    $_ch[] = "c[]={$_chan['channel_code']}";
                }
                break;
        }
    }

    // System Info
    $_si = jrMarket_get_active_system_info();

    // Get any updates
    $_rs = jrCore_load_url("{$_mkt['system_url']}/networkmarket/updates?dev=1&" . implode('&', $_ad) . '&' . implode('&', $_ch), $_si, 'POST', jrMarket_get_port(), null, null, true, 60);
    if (isset($_rs) && strpos($_rs, '{') === 0) {
        $_rs = json_decode($_rs, true);
        return $_rs;
    }
    return false;
}

/**
 * Return active Marketplace system
 * @return bool
 */
function jrMarket_get_active_release_system()
{
    if (isset($_SESSION['JRMARKET_RELEASE_SYSTEM']) && $_SESSION['JRMARKET_RELEASE_SYSTEM'] != false) {
        return json_decode($_SESSION['JRMARKET_RELEASE_SYSTEM'], true);
    }
    return jrMarket_set_active_release_system();
}

/**
 * Set the Active release system
 * @param $sid int Active System ID
 * @return array|bool
 */
function jrMarket_set_active_release_system($sid = null)
{
    $tbl = jrCore_db_table_name('jrMarket', 'system');
    if (is_null($sid) || $sid == 'default') {
        $req = "SELECT * FROM {$tbl} WHERE system_default = 'on' LIMIT 1";
    }
    else {
        $sid = intval($sid);
        $req = "SELECT * FROM {$tbl} WHERE system_id = '{$sid}' LIMIT 1";
    }
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!is_array($_rt)) {
        // See if we just don't have one that is default
        $req = "SELECT * FROM {$tbl} LIMIT 1";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if (!is_array($_rt)) {
            $_SESSION['JRMARKET_RELEASE_SYSTEM'] = false;
            return false;
        }
    }
    $_SESSION['JRMARKET_RELEASE_SYSTEM'] = json_encode($_rt);
    return $_rt;
}

/**
 * Get Active System info array
 * @return array
 */
function jrMarket_get_active_system_info()
{
    global $_conf;
    $msi = jrCore_db_connect();
    $_mk = jrMarket_get_active_release_system();
    return array(
        'email'  => (isset($_mk['system_email']) && jrCore_checktype($_mk['system_email'], 'email')) ? jrCore_url_encode_string($_mk['system_email']) : '',
        "sysid"  => (isset($_mk['system_code']) && jrCore_checktype($_mk['system_code'], 'md5')) ? jrCore_url_encode_string($_mk['system_code']) : '',
        "phpv"   => jrCore_url_encode_string(phpversion()),
        'mysqlv' => jrCore_url_encode_string(mysqli_get_server_info($msi)),
        'host'   => jrCore_url_encode_string($_conf['jrCore_base_url'])
    );
}

/**
 * Get Active marketplace systems
 * @return mixed
 */
function jrMarket_get_active_systems()
{
    if (!$_rt = jrCore_get_flag('jrmarket_market_systems')) {
        $tbl = jrCore_db_table_name('jrMarket', 'system');
        $req = "SELECT system_id, system_name FROM {$tbl} ORDER BY system_name ASC";
        $_rt = jrCore_db_query($req, 'system_id', false, 'system_name');
        jrCore_set_flag('jrmarket_market_systems', $_rt);
    }
    return $_rt;
}

/**
 * Create Active marketplace system jumper
 * @return string
 */
function jrMarket_system_jumper()
{
    global $_conf, $_post;
    $_sy = jrMarket_get_active_systems();
    if (is_array($_sy) && count($_sy) > 1) {
        $_mk = jrMarket_get_active_release_system();
        // We have more than 1 configured marketplace system - show jumper
        $url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/set_active_system/id=";
        $out = '<select name="active_marketplace_system" class="form_select form_select_item_jumper market_jumper" onchange="var v=this.options[this.selectedIndex].value; jrCore_window_location(\'' . $url . "'+ v)\">\n";
        foreach ($_sy as $sid => $name) {
            if ($sid == $_mk['system_id']) {
                $out .= '<option value="' . $sid . '" selected="selected"> ' . $name . "</option>\n";
            }
            else {
                $out .= '<option value="' . $sid . '"> ' . $name . "</option>\n";
            }
        }
        $out .= '</select>';
        return $out;
    }
    return '';
}

/**
 * Get remote PORT for marketplace URL
 * @return int
 */
function jrMarket_get_port()
{
    $_mkt = jrMarket_get_active_release_system();
    if (strpos($_mkt['system_url'], 'https') === 0) {
        return 443;
    }
    return 80;
}

/**
 * marketplace browser tabs
 * @param $active
 * @return bool
 */
function jrMarket_browse_tabs($active)
{
    global $_conf, $_post;
    $_tbs = array(
        "module" => array(
            'label' => 'modules',
            'url'   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse"
        ),
        "skin"   => array(
            'label' => 'skins',
            'url'   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse/skin"
        ),
        "bundle" => array(
            'label' => 'bundles',
            'url'   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse/bundle"
        ),
    );

    // We're going to add in our installed tab if we have installed something
    if (jrCore_db_number_rows('jrMarket', 'install') > 0) {
        $_tbs['installed'] = array(
            'label' => 'installed',
            'url'   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse/installed"
        );
    }

    $_tbs['promo'] = array(
        'label' => 'promo codes',
        'url'   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/promo"
    );
    if ($_inf = jrMarket_get_market_info()) {
        if (isset($_inf['providers']) && is_array($_inf['providers'])) {
            $_tbs['methods'] = array(
                'label' => 'payment methods',
                'url'   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/payment_methods"
            );
        }
    }
    $_tbs[$active]['active'] = true;
    jrCore_page_tab_bar($_tbs);
    return true;
}

/**
 * Get information about the active marketplace
 * @return bool
 */
function jrMarket_get_market_info()
{
    $_mkt = jrMarket_get_active_release_system();
    if (!$_mkt || !is_array($_mkt)) {
        return false;
    }
    if (!isset($_mkt['system_url'])) {
        $_SESSION['JRMARKET_INFO']["{$_mkt['system_id']}"] = false;
        return false;
    }
    if (isset($_SESSION['JRMARKET_INFO']["{$_mkt['system_id']}"])) {
        return $_SESSION['JRMARKET_INFO']["{$_mkt['system_id']}"];
    }
    $_rs = jrCore_load_url("{$_mkt['system_url']}/networkmarket/marketplace_info", null, 'GET', jrMarket_get_port(), null, null, false, 10);
    if (isset($_rs) && strpos($_rs, '{') === 0) {
        $_rs = json_decode($_rs, true);
        if (isset($_rs['error'])) {
            jrCore_logger('MAJ', "Unable to retrieve marketplace info from marketplace backend");
            $_SESSION['JRMARKET_INFO']["{$_mkt['system_id']}"] = false;
            return false;
        }
    }
    $_SESSION['JRMARKET_INFO']["{$_mkt['system_id']}"] = $_rs;
    return $_rs;
}

/**
 * Install a new Module
 * @param $module string Module directory to update
 * @param $license string Module license
 * @param $item_id int Marketplace ID
 * @return bool
 */
function jrMarket_install_module($module, $license, $item_id = 0)
{
    global $_conf;

    // module directory must be writable
    if (!is_writable(APP_DIR . '/modules')) {
        // See if we are configured for FTP...
        if (!isset($_conf['jrMarket_ftp_user']) || strlen($_conf['jrMarket_ftp_user']) === 0 || !isset($_conf['jrMarket_ftp_pass']) || strlen($_conf['jrMarket_ftp_pass']) === 0) {
            jrCore_set_form_notice('error', 'Your modules directory is not writable - make sure you have entered FTP settings into the Global Config so the system can install and update modules.');
            jrCore_location('referrer');
        }
    }
    // Must have a valid module license (or "r" for requesting a license)
    if (!$license || strlen($license) !== 16) {
        if ($license !== 'r') {
            jrCore_set_form_notice('error', 'Invalid Module License - please refresh and try again');
            jrCore_location('referrer');
        }
    }
    // Must have a valid system id
    $_mkt = jrMarket_get_active_release_system();
    if (!$_mkt || !isset($_mkt['system_code']) || !jrCore_checktype($_mkt['system_code'], 'md5')) {
        jrCore_set_form_notice('error', 'Invalid System ID - ensure you have entered a valid System ID in Tools -> Marketplace Systems');
        jrCore_location('referrer');
    }

    // Get Update Info
    $_rp = array(
        'sysid'   => $_mkt['system_code'],
        'type'    => 'module',
        'item'    => $module,
        'mid'     => intval($item_id),
        'license' => $license,
        'host'    => $_conf['jrCore_base_url']
    );
    $_rs = jrCore_load_url("{$_mkt['system_url']}/networkmarket/update_info", $_rp, 'POST', jrMarket_get_port(), null, null, true, 60);
    if (!$_rs || strlen($_rs) === 0) {
        jrCore_set_form_notice('error', 'Unable to communicate with update server (1)');
        jrCore_location('referrer');
    }
    $_rs = json_decode($_rs, true);
    if ($_rs && isset($_rs['error'])) {
        jrCore_set_form_notice('error', $_rs['error']);
        jrCore_location('referrer');
    }

    // 'name' => jrCore
    // 'version' => '5.1.0',
    // 'size' => 12345,
    // 'hash' => md5(file_data),
    // 'url'  => 'download location URL'

    // Validate our return
    if (!isset($_rs['name']) || $_rs['name'] != $module) {
        jrCore_set_form_notice('error', 'Module name returned in update info does not match requested module - please try again');
        jrCore_location('referrer');
    }
    if (!isset($_rs['size']) || !jrCore_checktype($_rs['size'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Module size returned in update info is 0 bytes or invalid');
        jrCore_location('referrer');
    }
    if (!isset($_rs['hash']) || !jrCore_checktype($_rs['hash'], 'md5')) {
        jrCore_set_form_notice('error', 'Module hash returned in update info is invalid - please try again');
        jrCore_location('referrer');
    }
    if (!isset($_rs['url']) || !jrCore_checktype($_rs['url'], 'url')) {
        jrCore_set_form_notice('error', 'Module url returned in update info is invalid - please try again');
        jrCore_location('referrer');
    }

    // Go get the actual file
    $cdr = jrCore_get_module_cache_dir('jrMarket');
    $fil = jrCore_load_url($_rs['url'], null, 'GET', jrMarket_get_port(), null, null, true, 360);
    if (isset($fil) && strlen($fil) > 0) {
        jrCore_write_to_file("{$cdr}/{$module}.tar", $fil);
    }
    else {
        jrCore_set_form_notice('error', 'Unable to communicate with update server (2)');
        jrCore_location('referrer');
    }
    // validate
    if (md5_file("{$cdr}/{$module}.tar") != $_rs['hash']) {
        unlink("{$cdr}/{$module}.tar");
        jrCore_set_form_notice('error', 'Corrupted update file recieved - md5 checksum mismatch');
        jrCore_location('referrer');
    }
    // Untar and move into location
    if (is_dir("{$cdr}/{$module}")) {
        // old one exists - remove
        jrCore_delete_dir_contents("{$cdr}/{$module}");
        unlink("{$cdr}/{$module}");
    }
    jrCore_extract_tar_archive("{$cdr}/{$module}.tar", $cdr);
    if (is_dir("{$cdr}/{$module}")) {

        $fix = false;
        // See if we are doing an FTP install or directory install
        if (!is_writable(APP_DIR . '/modules')) {
            if (!@chmod(APP_DIR . '/modules', $_conf['jrCore_dir_perms'])) {
                if (!jrMarket_ftp_set_permissions('module', $module, 'open')) {
                    jrCore_set_form_notice('error', "Unable to change permissions on module directory /modules/{$module} - unable to install module");
                    jrCore_location('referrer');
                }
                $fix = true;
            }
        }

        // Move into place
        jrMarket_move_update_to_live('module', $module, $_rs['version'], "{$cdr}/{$module}");
        if ($error = jrCore_get_flag('jrmarket_update_error')) {
            jrCore_set_form_notice('error', $error);
            jrCore_location('referrer');
        }

        if ($fix) {
            jrMarket_ftp_set_permissions('module', $module, 'close');
        }

        // Validate module
        define('IN_JAMROOM_INSTALLER', 1);
        jrCore_verify_module($module);

        // Make sure module_system_id and license are updated
        $tbl = jrCore_db_table_name('jrCore', 'module');
        $req = "UPDATE {$tbl} SET module_system_id = '{$_mkt['system_id']}', module_license = '" . jrCore_db_escape($license) . "' WHERE module_directory = '{$module}' LIMIT 1";
        jrCore_db_query($req);

        // Reset settings/module cache so new module is picked up
        unlink("{$cdr}/{$module}.tar");
        jrCore_delete_all_cache_entries('jrCore', 0);
        jrMarket_reset_opcode_caches();
        jrCore_trigger_event('jrMarket', 'installed_module', $_rs);
        jrCore_logger('INF', "successfully installed new module {$module}, version {$_rs['version']}");
        return true;
    }
    else {
        unlink("{$cdr}/{$module}.tar");
        jrCore_set_form_notice('error', 'Unable to prepare module directory in item staging area - please try again');
        jrCore_location('referrer');
    }
    return false;
}

/**
 * Install a new Skin
 * @param $skin string Skin directory to install
 * @param $license string Skin license
 * @param $item_id int Marketplace ID
 * @return bool
 */
function jrMarket_install_skin($skin, $license, $item_id = 0)
{
    global $_conf;
    if (!isset($skin)) {
        return false;
    }
    // skin directory must be writable
    if (!is_writable(APP_DIR . '/skins')) {
        // See if we are configured for FTP...
        if (!isset($_conf['jrMarket_ftp_user']) || strlen($_conf['jrMarket_ftp_user']) === 0 || !isset($_conf['jrMarket_ftp_pass']) || strlen($_conf['jrMarket_ftp_pass']) === 0) {
            jrCore_set_form_notice('error', 'Your skins directory is not writable - make sure you have entered FTP settings into the Global Config so the system can install and update skins.');
            jrCore_location('referrer');
        }
    }
    // Must have a valid module license (or "r" for requesting a license)
    if (!isset($license) || strlen($license) !== 16) {
        jrCore_set_form_notice('error', 'Invalid Skin License - please refresh and try again');
        jrCore_location('referrer');
    }
    // Must have a valid system id
    $_mkt = jrMarket_get_active_release_system();
    if (!isset($_mkt['system_code']) || !jrCore_checktype($_mkt['system_code'], 'md5')) {
        jrCore_set_form_notice('error', 'Invalid System ID - ensure you have entered a valid System ID in Tools -> Marketplace Systems');
        jrCore_location('referrer');
    }

    // Get Update Info
    $_rp = array(
        'sysid'   => $_mkt['system_code'],
        'type'    => 'skin',
        'item'    => $skin,
        'license' => $license,
        'host'    => $_conf['jrCore_base_url']
    );
    if (isset($item_id) && jrCore_checktype($item_id, 'number_nz')) {
        $_rp['mid'] = intval($item_id);
    }
    $_rs = jrCore_load_url("{$_mkt['system_url']}/networkmarket/update_info", $_rp, 'POST', jrMarket_get_port(), null, null, true, 60);
    if (!isset($_rs) || strlen($_rs) === 0) {
        jrCore_set_form_notice('error', 'Unable to communicate with update server (1)');
        jrCore_location('referrer');
    }
    $_rs = json_decode($_rs, true);
    if (isset($_rs['error'])) {
        jrCore_set_form_notice('error', $_rs['error']);
        jrCore_location('referrer');
    }

    // 'name' => jrCore
    // 'version' => '5.1.0',
    // 'size' => 12345,
    // 'hash' => md5(file_data),
    // 'url'  => 'download location URL'

    // Validate our return
    if (!isset($_rs['name']) || $_rs['name'] != $skin) {
        jrCore_set_form_notice('error', 'Skin name returned in install info does not match requested skin - please try again');
        jrCore_location('referrer');
    }
    if (!isset($_rs['size']) || !jrCore_checktype($_rs['size'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Skin size returned in install info is 0 bytes or invalid');
        jrCore_location('referrer');
    }
    if (!isset($_rs['hash']) || !jrCore_checktype($_rs['hash'], 'md5')) {
        jrCore_set_form_notice('error', 'Skin hash returned in install info is invalid - please try again');
        jrCore_location('referrer');
    }
    if (!isset($_rs['url']) || !jrCore_checktype($_rs['url'], 'url')) {
        jrCore_set_form_notice('error', 'Skin url returned in install info is invalid - please try again');
        jrCore_location('referrer');
    }

    // Go get the actual file
    $cdr = jrCore_get_module_cache_dir('jrMarket');
    $fil = jrCore_load_url($_rs['url'], null, 'GET', jrMarket_get_port(), null, null, true, 360);
    if (isset($fil) && strlen($fil) > 0) {
        jrCore_write_to_file("{$cdr}/{$skin}.tar", $fil);
    }
    else {
        jrCore_set_form_notice('error', 'Unable to communicate with update server (2)');
        jrCore_location('referrer');
    }
    // validate
    if (md5_file("{$cdr}/{$skin}.tar") != $_rs['hash']) {
        unlink("{$cdr}/{$skin}.tar");
        jrCore_set_form_notice('error', 'Corrupted update file recieved - md5 checksum mismatch');
        jrCore_location('referrer');
    }
    // Untar and move into location
    if (is_dir("{$cdr}/{$skin}")) {
        // old one exists - remove
        jrCore_delete_dir_contents("{$cdr}/{$skin}");
        unlink("{$cdr}/{$skin}");
    }
    jrCore_extract_tar_archive("{$cdr}/{$skin}.tar", $cdr);
    if (is_dir("{$cdr}/{$skin}")) {

        $fix = false;
        // See if we are doing an FTP install or directory install
        if (!is_writable(APP_DIR . '/skins')) {
            if (!@chmod(APP_DIR . '/skins', $_conf['jrCore_dir_perms'])) {
                if (!jrMarket_ftp_set_permissions('skin', $skin, 'open')) {
                    jrCore_set_form_notice('error', "Unable to change permissions on skin directory /skins/{$skin} - unable to install skin");
                    jrCore_location('referrer');
                }
                $fix = true;
            }
        }

        // Move into place
        jrMarket_move_update_to_live('skin', $skin, $_rs['version'], "{$cdr}/{$skin}");
        if ($error = jrCore_get_flag('jrmarket_update_error')) {
            jrCore_set_form_notice('error', $error);
            jrCore_location('referrer');
        }

        if ($fix) {
            jrMarket_ftp_set_permissions('skin', $skin, 'close');
        }

        // Validate skin
        jrCore_verify_skin($skin);

        // Make sure module_system_id is updated
        $_mk = jrMarket_get_active_release_system();
        if (is_array($_mk)) {
            $tbl = jrCore_db_table_name('jrCore', 'skin');
            $req = "UPDATE {$tbl} SET skin_system_id = '{$_mk['system_id']}', skin_license = '" . jrCore_db_escape($license) . "' WHERE skin_directory = '{$skin}' LIMIT 1";
            jrCore_db_query($req);
        }

        // Reset settings/module cache so new module is picked up
        unlink("{$cdr}/{$skin}.tar");
        jrCore_delete_all_cache_entries('jrCore', 0);
        jrMarket_reset_opcode_caches();
        jrCore_trigger_event('jrMarket', 'installed_skin', $_rs);
        jrCore_logger('INF', "successfully installed new skin {$skin}, version {$_rs['version']}");
        return true;
    }
    else {
        unlink("{$cdr}/{$skin}.tar");
        jrCore_set_form_notice('error', 'Unable to prepare skin directory in item staging area - please try again');
        jrCore_location('referrer');
    }
    return false;
}

/**
 * Move an Update directory to be "live"
 * @param string $type module|skin
 * @param string $name directory name
 * @param string $version NEW version
 * @param string $tmpdir directory for update
 * @return bool
 */
function jrMarket_move_update_to_live($type, $name, $version, $tmpdir)
{
    global $_mods;
    $version = trim($version);
    $old_dir = getcwd();
    if ($type == 'module') {
        $symlink = APP_DIR . "/modules/{$name}";
        $old_ver = (isset($_mods[$name]['module_version'])) ? trim($_mods[$name]['module_version']) : '0.0.0';
        chdir(APP_DIR . '/modules');
    }
    else {
        $_smeta  = jrCore_skin_meta_data($name);
        $symlink = APP_DIR . "/skins/{$name}";
        $old_ver = (isset($_smeta['version'])) ? trim($_smeta['version']) : '0.0.0';
        chdir(APP_DIR . '/skins');
    }

    // Make sure any errors are reset
    jrCore_delete_flag('jrmarket_update_error');

    // Do we have a versioned directory?
    if (is_dir($symlink) && !is_link($symlink)) {
        // If our targeted symlink is a DIRECTORY, this is a new install.
        // Directory we are moving to already exists - remove it
        // NOTE: This should really never happen
        if (is_dir("{$symlink}-release-{$version}")) {
            jrCore_delete_dir_contents("{$symlink}-release-{$version}", false);
            rmdir("{$symlink}-release-{$version}");
        }
    }

    // Does our target release directory already exist?
    elseif (is_link($symlink) && is_dir("{$symlink}-release-{$version}")) {

        // Our target directory already exists.  Is it the "LIVE" directory?
        $target = readlink($symlink);
        if ($target && basename($target) == basename("{$symlink}-release-{$version}")) {
            // This is our LIVE directory!
            // Note: This can happen on a Marketplace RELOAD
            // In this case what we do is:
            // 1) We make a COPY of the existing live release directory to a tmp release directory
            // 2) Rename the symlink to point to the copy
            // 3) Move the NEW release into the proper release directory
            // 4) Rename the symlink again to now point to the NEW release directory
            // 5) Delete the tmp release directory
            if (is_dir("{$symlink}-release-0.0.0")) {
                jrCore_delete_dir_contents("{$symlink}-release-0.0.0", false);
                rmdir("{$symlink}-release-0.0.0");
            }
            jrMarket_recursive_copy("{$symlink}-release-{$version}", "{$symlink}-release-0.0.0");

            // Create temp symlink and rename
            if (!symlink(basename("{$symlink}-release-0.0.0"), "{$symlink}-tmp")) {
                chdir($old_dir);
                jrCore_set_flag('jrmarket_update_error', "ERROR: unable to rename {$type} symlink {$name} - check file permissions");
                return false;
            }
            if (!rename("{$symlink}-tmp", $symlink)) {
                chdir($old_dir);
                jrCore_set_flag('jrmarket_update_error', "ERROR: unable to rename new {$type} directory for {$name} - check file permissions");
                return false;
            }

        }
        else {
            // It is NOT the live directory - we can remove it
            jrCore_delete_dir_contents("{$symlink}-release-{$version}", false);
            rmdir("{$symlink}-release-{$version}");
        }

    }

    // Move in our NEW content to the release dir
    if (!rename($tmpdir, "{$symlink}-release-{$version}")) {
        jrMarket_recursive_copy($tmpdir, "{$symlink}-release-{$version}");
        if (!is_dir("{$symlink}-release-{$version}")) {
            chdir($old_dir);
            jrCore_set_flag('jrmarket_update_error', "ERROR: unable to rename new {$type} directory for {$name} - check file permissions (2)");
            return false;
        }
        else {
            jrCore_delete_dir_contents($tmpdir);
            rmdir($tmpdir);
        }
    }
    if (is_dir($symlink) && !is_link($symlink)) {
        // If we are a directory we RENAME, SYMLINK, then delete
        // NOTE: This move is not atomic, but ideally only happens on the FIRST
        // time a module or skin is updated from the marketplace
        if (is_dir("{$symlink}-release-{$old_ver}")) {
            jrCore_delete_dir_contents("{$symlink}-release-{$old_ver}", false);
            rmdir("{$symlink}-release-{$old_ver}");
        }
        if (!rename($symlink, "{$symlink}-release-{$old_ver}")) {
            chdir($old_dir);
            jrCore_set_flag('jrmarket_update_error', "ERROR: unable to rename new {$type} directory for {$name} - check file permissions (3)");
            return false;
        }
        if (!symlink(basename("{$symlink}-release-{$version}"), $symlink)) {
            chdir($old_dir);
            jrCore_set_flag('jrmarket_update_error', "ERROR: unable to rename {$type} symlink {$name} - check file permissions (2)");
            return false;
        }
    }
    else {
        // To ensure this is an ATOMIC operation, we first create our
        // symlink to a TEMP dir, then RENAME it
        if (!symlink(basename("{$symlink}-release-{$version}"), "{$symlink}-tmp")) {
            chdir($old_dir);
            jrCore_set_flag('jrmarket_update_error', "ERROR: unable to rename {$type} symlink {$name} - check file permissions (3)");
            return false;
        }
        if (!rename("{$symlink}-tmp", $symlink)) {
            chdir($old_dir);
            jrCore_set_flag('jrmarket_update_error', "ERROR: unable to rename new {$type} directory for {$name} - check file permissions (4)");
            return false;
        }
        // Cleanup from any rename
        if (is_dir("{$symlink}-release-0.0.0")) {
            jrCore_delete_dir_contents("{$symlink}-release-0.0.0", false);
            rmdir("{$symlink}-release-0.0.0");
        }
    }
    chdir($old_dir);
    return true;
}

/**
 * Recursively copy one directory to another
 * @param string $src
 * @param string $dst
 * @return bool
 */
function jrMarket_recursive_copy($src, $dst)
{
    global $_conf;
    $dir = opendir($src);
    @mkdir($dst, $_conf['jrCore_dir_perms']);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                jrMarket_recursive_copy($src . '/' . $file, $dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
    return true;
}

/**
 * Update an existing module to a new version
 * @param $module string Module directory to update
 * @param $_set array Update Set
 * @param $force bool Set to TRUE to force a reload
 * @param $modal bool Set to TRUE if this update is happening in a modal window
 * @param $item_id int marketplace ID
 * @param $reload bool set to TRUE if reloading a module/skin
 * @return bool
 */
function jrMarket_update_module($module, $_set, $force = false, $modal = false, $item_id = 0, $reload = false)
{
    global $_mods, $_conf;
    if (!$module) {
        return false;
    }
    if (!$reload) {
        if (!$_set || !is_array($_set)) {
            return false;
        }
        // see if we have already done this module...
        if (!$force && !isset($_set[$module])) {
            // already handled by a dependency
            return true;
        }
    }
    $url = jrCore_get_local_referrer();

    // Must get a valid item_id
    if (!jrCore_checktype($item_id, 'number_nz')) {
        if ($modal) {
            jrCore_form_modal_notice('error', 'Invalid marketplace id received - please refresh and try again');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Invalid marketplace id received - please refresh and try again');
        jrCore_json_response(array('url' => $url));
    }

    // Get module info directly from DB in case it is cached
    $tbl = jrCore_db_table_name('jrCore', 'module');
    $req = "SELECT * FROM {$tbl} WHERE module_directory = '" . jrCore_db_escape($module) . "' LIMIT 1";
    $_md = jrCore_db_query($req, 'SINGLE');
    if (!$_md || !is_array($_md)) {
        if ($modal) {
            jrCore_form_modal_notice('error', 'Invalid module received - please refresh and try again');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Invalid module received - please refresh and try again');
        jrCore_json_response(array('url' => $url));
    }

    // pre-update checks
    if (!is_writable(APP_DIR . '/modules')) {
        // Try to change...
        @chmod(APP_DIR . '/modules', $_conf['jrCore_dir_perms']);
        // Still not writable...
        if (!is_writable(APP_DIR . '/modules')) {
            // See if we are configured for FTP...
            if (!isset($_conf['jrMarket_ftp_user']) || strlen($_conf['jrMarket_ftp_user']) === 0 || !isset($_conf['jrMarket_ftp_pass']) || strlen($_conf['jrMarket_ftp_pass']) === 0) {
                if ($modal) {
                    jrCore_logger('CRI', 'The modules directory is not writable - make sure you have entered FTP settings into the Global Config so the system can install and update modules.');
                    jrCore_form_modal_notice('error', 'Your modules directory is not writable');
                    jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
                    exit;
                }
                jrCore_set_form_notice('error', 'Your modules directory is not writable - make sure you have entered FTP settings into the Global Config so the system can install and update modules.');
                jrCore_json_response(array('url' => $url));
            }
        }
    }

    // Must have a valid module license
    if (!isset($_md['module_license']) || strlen($_md['module_license']) !== 16) {
        if ($modal) {
            jrCore_form_modal_notice('update', "ERROR: Invalid Module License for {$_md['module_name']} - skipping");
            return false;
        }
        jrCore_set_form_notice('error', 'Invalid Module License - please refresh the <b>System Updates</b> tab and try again', false);
        jrCore_json_response(array('url' => $url));
    }
    // Must have a valid system id
    $_mkt = jrMarket_get_active_release_system();
    if (!isset($_mkt['system_code']) || !jrCore_checktype($_mkt['system_code'], 'md5')) {
        if ($modal) {
            jrCore_form_modal_notice('error', 'Invalid System ID - check Tools -> Marketplace Systems');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Invalid System ID - ensure you have entered a valid System ID in Tools -> Marketplace Systems');
        jrCore_json_response(array('url' => $url));
    }

    // Get Update Info
    $_rp = array(
        'sysid'   => $_mkt['system_code'],
        'type'    => 'module',
        'item'    => $module,
        'mid'     => intval($item_id),
        'license' => $_md['module_license'],
        'host'    => $_conf['jrCore_base_url']
    );
    $ups = false;
    if (is_file(APP_DIR . "/modules/{$module}/icon.png")) {
        $_rp['icon'] = '@' . APP_DIR . "/modules/{$module}/icon.png";
        $ups         = true;
    }
    $_rs = jrCore_load_url("{$_mkt['system_url']}/networkmarket/update_info", $_rp, 'POST', jrMarket_get_port(), null, null, true, 60, null, $ups);
    if (!$_rs || strlen($_rs) === 0) {
        if ($modal) {
            jrCore_form_modal_notice('update', 'ERROR: Unable to communicate with update server (1)');
            return false;
        }
        jrCore_set_form_notice('error', 'Unable to communicate with update server');
        jrCore_json_response(array('url' => $url));
    }
    $_rs = json_decode($_rs, true);
    if (isset($_rs['error'])) {
        if ($modal) {
            jrCore_form_modal_notice('update', "error: {$_rs['error']}");
            return false;
        }
        jrCore_set_form_notice('error', $_rs['error']);
        jrCore_json_response(array('url' => $url));
    }

    // See if we got a new license
    if (isset($_rs['license']) && strlen($_rs['license']) === 16) {
        // We were given a new license - update
        $tbl = jrCore_db_table_name('jrCore', 'module');
        $req = "UPDATE {$tbl} SET module_license = '" . jrCore_db_escape($_rs['license']) . "' WHERE module_directory = '" . jrCore_db_escape($module) . "' LIMIT 1";
        jrCore_db_query($req);
        jrCore_delete_config_cache();
    }

    // 'name' => jrCore
    // 'version' => '5.1.0',
    // 'size' => 12345,
    // 'hash' => md5(file_data),
    // 'url'  => 'download location URL'

    // Validate our return
    if (!isset($_rs['name']) || $_rs['name'] != $module) {
        if ($modal) {
            jrCore_form_modal_notice('error', 'Module name returned in update info does not match requested module');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Module name returned in update info does not match requested module - please try again');
        jrCore_json_response(array('url' => $url));
    }
    if (!isset($_rs['version']) || version_compare($_rs['version'], $_mods[$module]['module_version']) === -1) {
        if ($modal) {
            jrCore_form_modal_notice('error', 'Module version returned in update info is lower than the currently installed version');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Module version returned in update info is lower than the currently installed version');
        jrCore_json_response(array('url' => $url));
    }
    if (!isset($_rs['size']) || !jrCore_checktype($_rs['size'], 'number_nz')) {
        if ($modal) {
            jrCore_form_modal_notice('error', 'Module size returned in update info is 0 bytes or invalid');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Module size returned in update info is 0 bytes or invalid');
        jrCore_json_response(array('url' => $url));
    }
    if (!isset($_rs['hash']) || !jrCore_checktype($_rs['hash'], 'md5')) {
        if ($modal) {
            jrCore_form_modal_notice('error', 'Module hash returned in update info is invalid');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Module hash returned in update info is invalid - please try again');
        jrCore_json_response(array('url' => $url));
    }
    if (!isset($_rs['url']) || !jrCore_checktype($_rs['url'], 'url')) {
        if ($modal) {
            jrCore_form_modal_notice('error', 'Module url returned in update info is invalid');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Module url returned in update info is invalid - please try again');
        jrCore_json_response(array('url' => $url));
    }

    // Go get the actual file
    $cdr = jrCore_get_module_cache_dir('jrMarket');
    $fil = jrCore_load_url($_rs['url'], null, 'GET', jrMarket_get_port(), null, null, true, 360);
    if ($fil && strlen($fil) > 0) {
        jrCore_write_to_file("{$cdr}/{$_rs['name']}.tar", $fil);
    }
    else {
        if ($modal) {
            jrCore_form_modal_notice('error', 'Unable to download update from update server');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Unable to download update from update server (2)');
        jrCore_json_response(array('url' => $url));
    }
    // validate
    if (md5_file("{$cdr}/{$_rs['name']}.tar") != $_rs['hash']) {
        unlink("{$cdr}/{$_rs['name']}.tar");
        if ($modal) {
            jrCore_form_modal_notice('error', 'Corrupted update file recieved - md5 checksum mismatch');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Corrupted update file recieved - md5 checksum mismatch');
        jrCore_json_response(array('url' => $url));
    }

    // Untar and move into location
    if (is_dir("{$cdr}/{$_rs['name']}")) {
        // old one exists - remove
        jrCore_delete_dir_contents("{$cdr}/{$_rs['name']}");
        unlink("{$cdr}/{$_rs['name']}");
    }
    jrCore_extract_tar_archive("{$cdr}/{$_rs['name']}.tar", $cdr);
    if (is_dir("{$cdr}/{$_rs['name']}")) {

        $fix = false;
        // See if we are doing an FTP install or directory install
        if (!is_writable(APP_DIR . '/modules')) {
            if (!@chmod(APP_DIR . '/modules', $_conf['jrCore_dir_perms'])) {
                if (!jrMarket_ftp_set_permissions('module', $_rs['name'], 'open')) {
                    if ($modal) {
                        jrCore_form_modal_notice('error', 'Unable to change permissions on module directory via FTP');
                        jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
                        exit;
                    }
                    jrCore_set_form_notice('error', 'Unable to change permissions on module directory via FTP - unable to update module');
                    jrCore_json_response(array('url' => $url));
                }
                $fix = true;
            }
        }

        // Move into place
        jrMarket_move_update_to_live('module', $module, $_rs['version'], "{$cdr}/{$module}");
        if ($error = jrCore_get_flag('jrmarket_update_error')) {
            jrCore_set_form_notice('error', $error);
            jrCore_json_response(array('url' => $url));
        }

        if ($fix) {
            jrMarket_ftp_set_permissions('module', $_rs['name'], 'close');
        }

        // Reset module cache
        @unlink("{$cdr}/{$_rs['name']}.tar");
        jrCore_delete_config_cache();
        jrCore_delete_all_cache_entries('jrMarket');
        jrCore_delete_all_cache_entries($module);
        jrMarket_reset_opcode_caches();

        // Reset Templates
        if ($module != 'jrImage') {
            $cdr = jrCore_get_module_cache_dir($module);
            if (is_dir($cdr)) {
                jrCore_delete_dir_contents($cdr);
            }
        }
        if ($module == 'jrCore' && is_dir(APP_DIR . "/data/cache/{$_conf['jrCore_active_skin']}")) {
            jrCore_delete_dir_contents(APP_DIR . "/data/cache/{$_conf['jrCore_active_skin']}");
            jrCore_set_setting_value('jrCore', "{$_conf['jrCore_active_skin']}_javascript_version", '');
        }

        // Rebuild JS and CSS
        jrCore_create_master_css($_conf['jrCore_active_skin']);
        jrCore_create_master_javascript($_conf['jrCore_active_skin']);

        jrCore_trigger_event('jrMarket', 'updated_module', $_mods[$module], $_rs);
        jrCore_logger('INF', "successfully updated module {$module} from version {$_mods[$module]['module_version']} to {$_rs['version']}");
        return true;
    }
    @unlink("{$cdr}/{$_rs['name']}.tar");
    return false;
}

/**
 * Update an existing skin to a new version
 * @param $skin string Module directory to update
 * @param $_set array Update Set
 * @param $force bool Set to TRUE to force a reload
 * @param $modal bool Set to TRUE if this update is happening in a modal window
 * @param $item_id int Marketplace Item ID
 * @param $reload bool set to TRUE if reloading
 * @return bool
 */
function jrMarket_update_skin($skin, $_set, $force = false, $modal = false, $item_id = 0, $reload = false)
{
    global $_conf;
    if (!$skin) {
        return false;
    }
    if (!$reload) {
        if (!$_set || !is_array($_set)) {
            return false;
        }
        // see if we have already done this module...
        if (!$force && !isset($_set[$skin])) {
            // already handled by a dependency
            return true;
        }
    }
    $url  = jrCore_get_local_referrer();
    $_mta = jrCore_skin_meta_data($skin);
    if (!isset($_mta) || !is_array($_mta)) {
        if ($modal) {
            jrCore_form_modal_notice('error', 'Invalid Skin - no meta data');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Invalid Skin - no meta data');
        jrCore_json_response(array('url' => $url));
    }

    // Must get a valid item_id
    if (!jrCore_checktype($item_id, 'number_nz')) {
        if ($modal) {
            jrCore_form_modal_notice('error', 'Invalid marketplace id received - please refresh and try again');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Invalid marketplace id received - please refresh and try again');
        jrCore_json_response(array('url' => $url));
    }

    if (!is_writable(APP_DIR . '/skins')) {
        // Try to change...
        @chmod(APP_DIR . '/skins', $_conf['jrCore_dir_perms']);
        // Still not writable...
        if (!is_writable(APP_DIR . '/skins')) {
            // See if we are configured for FTP...
            if (!isset($_conf['jrMarket_ftp_user']) || strlen($_conf['jrMarket_ftp_user']) === 0 || !isset($_conf['jrMarket_ftp_pass']) || strlen($_conf['jrMarket_ftp_pass']) === 0) {
                if ($modal) {
                    jrCore_form_modal_notice('error', 'Your skins directory is not writable - check FTP settings');
                    jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
                    exit;
                }
                jrCore_set_form_notice('error', 'Your skins directory is not writable - make sure you have entered FTP settings into the Global Config so the system can install and update skins.');
                jrCore_json_response(array('url' => $url));
            }
        }
    }

    // Skin settings are not all loaded - must load here
    $_el = jrMarket_get_skin_license($skin);

    // Must have a valid skin license
    if (!isset($_el[$skin]) || strlen($_el[$skin]) !== 16) {
        if ($modal) {
            jrCore_form_modal_notice('update', "ERROR: Invalid Skin License for {$skin} - skipping");
            return false;
        }
        jrCore_set_form_notice('error', 'Invalid Skin License - please refresh the <b>System Updates</b> tab and try again', false);
        jrCore_json_response(array('url' => $url));
    }
    // Must have a valid system id
    $_mkt = jrMarket_get_active_release_system();
    if (!isset($_mkt['system_code']) || !jrCore_checktype($_mkt['system_code'], 'md5')) {
        if ($modal) {
            jrCore_form_modal_notice('error', 'Invalid System ID - check Tools -> Marketplace Systems');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Invalid System ID - ensure you have entered a valid System ID in Tools -> Marketplace Systems');
        jrCore_json_response(array('url' => $url));
    }

    // Get Update Info
    $_rp = array(
        'sysid'   => $_mkt['system_code'],
        'type'    => 'skin',
        'item'    => $skin,
        'mid'     => intval($item_id),
        'license' => $_el[$skin],
        'host'    => $_conf['jrCore_base_url']
    );
    $ups = false;
    if (is_file(APP_DIR . "/skins/{$skin}/icon.png")) {
        $_rp['icon'] = '@' . APP_DIR . "/skins/{$skin}/icon.png";
        $ups         = true;
    }
    $_rs = jrCore_load_url("{$_mkt['system_url']}/networkmarket/update_info", $_rp, 'POST', jrMarket_get_port(), null, null, true, 60, null, $ups);
    if (!$_rs || strlen($_rs) === 0) {
        if ($modal) {
            jrCore_form_modal_notice('update', 'ERROR: Unable to communicate with update server (1)');
            return false;
        }
        jrCore_set_form_notice('error', 'Unable to communicate with update server (1)');
        jrCore_json_response(array('url' => $url));
    }
    $_rs = json_decode($_rs, true);
    if (isset($_rs['error'])) {
        if ($modal) {
            jrCore_form_modal_notice('update', "error: {$_rs['error']}");
            return false;
        }
        jrCore_set_form_notice('error', $_rs['error']);
        jrCore_json_response(array('url' => $url));
    }

    // 'name' => jrElastic
    // 'version' => '1.0.5',
    // 'size' => 12345,
    // 'hash' => md5(file_data),
    // 'url'  => 'download location URL'

    // Validate our return
    if (!isset($_rs['name']) || $_rs['name'] != $skin) {
        if ($modal) {
            jrCore_form_modal_notice('error', 'Skin name returned in update info does not match requested skin');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Skin name returned in update info does not match requested skin - please try again');
        jrCore_json_response(array('url' => $url));
    }
    if (!isset($_rs['version']) || version_compare($_rs['version'], $_mta['version']) === -1) {
        if ($modal) {
            jrCore_form_modal_notice('error', 'Skin version returned in update info is lower than the currently installed version');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Skin version returned in update info is lower than the currently installed version');
        jrCore_json_response(array('url' => $url));
    }
    if (!isset($_rs['size']) || !jrCore_checktype($_rs['size'], 'number_nz')) {
        if ($modal) {
            jrCore_form_modal_notice('error', 'Skin size returned in update info is 0 bytes or invalid');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Skin size returned in update info is 0 bytes or invalid');
        jrCore_json_response(array('url' => $url));
    }
    if (!isset($_rs['hash']) || !jrCore_checktype($_rs['hash'], 'md5')) {
        if ($modal) {
            jrCore_form_modal_notice('error', 'Skin hash returned in update info is invalid');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Skin hash returned in update info is invalid - please try again');
        jrCore_json_response(array('url' => $url));
    }
    if (!isset($_rs['url']) || !jrCore_checktype($_rs['url'], 'url')) {
        if ($modal) {
            jrCore_form_modal_notice('error', 'Skin url returned in update info is invalid');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Skin url returned in update info is invalid - please try again');
        jrCore_json_response(array('url' => $url));
    }

    // Go get the actual file
    $cdr = jrCore_get_module_cache_dir('jrMarket');
    $fil = jrCore_load_url($_rs['url'], null, 'GET', jrMarket_get_port(), null, null, true, 360);
    if (isset($fil) && strlen($fil) > 0) {
        jrCore_write_to_file("{$cdr}/{$_rs['name']}.tar", $fil);
    }
    else {
        if ($modal) {
            jrCore_form_modal_notice('error', 'Unable to communicate with update server (2)');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Unable to communicate with update server (2)');
        jrCore_json_response(array('url' => $url));
    }
    // validate
    if (md5_file("{$cdr}/{$_rs['name']}.tar") != $_rs['hash']) {
        unlink("{$cdr}/{$_rs['name']}.tar");
        if ($modal) {
            jrCore_form_modal_notice('error', 'Corrupted update file recieved - md5 checksum mismatch');
            jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
            exit;
        }
        jrCore_set_form_notice('error', 'Corrupted update file recieved - md5 checksum mismatch');
        jrCore_json_response(array('url' => $url));
    }
    // Untar and move into location
    if (is_dir("{$cdr}/{$_rs['name']}")) {
        // old one exists - remove
        jrCore_delete_dir_contents("{$cdr}/{$_rs['name']}");
        unlink("{$cdr}/{$_rs['name']}");
    }
    jrCore_extract_tar_archive("{$cdr}/{$_rs['name']}.tar", $cdr);
    if (is_dir("{$cdr}/{$_rs['name']}")) {

        $fix = false;
        // See if we are doing an FTP install or directory install
        if (!is_writable(APP_DIR . '/skins')) {
            if (!@chmod(APP_DIR . '/skins', $_conf['jrCore_dir_perms'])) {
                if (!jrMarket_ftp_set_permissions('skin', $_rs['name'], 'open')) {
                    if ($modal) {
                        jrCore_form_modal_notice('error', 'Unable to change permissions on skin directory - unable to update skin');
                        jrCore_form_modal_notice('complete', 'Errors were encountered updating the module');
                        exit;
                    }
                    jrCore_set_form_notice('error', 'Unable to change permissions on skin directory - unable to update skin');
                    jrCore_json_response(array('url' => $url));
                }
                $fix = true;
            }
        }

        // Move into place
        jrMarket_move_update_to_live('skin', $skin, $_rs['version'], "{$cdr}/{$skin}");
        if ($error = jrCore_get_flag('jrmarket_update_error')) {
            jrCore_set_form_notice('error', $error);
            jrCore_json_response(array('url' => $url));
        }

        if ($fix) {
            jrMarket_ftp_set_permissions('skin', $_rs['name'], 'close');
        }

        unlink("{$cdr}/{$_rs['name']}.tar");

        // Reset module cache
        if ($skin == $_conf['jrCore_active_skin']) {

            jrCore_delete_config_cache();
            jrCore_delete_all_cache_entries('jrMarket');
            jrCore_delete_all_cache_entries('jrCore');
            jrMarket_reset_opcode_caches();

            if (is_dir(APP_DIR . "/data/cache/{$_conf['jrCore_active_skin']}")) {
                jrCore_delete_dir_contents(APP_DIR . "/data/cache/{$_conf['jrCore_active_skin']}");
            }

            // Rebuild JS and CSS
            jrCore_create_master_css($_conf['jrCore_active_skin']);
            jrCore_create_master_javascript($_conf['jrCore_active_skin']);
        }

        jrCore_trigger_event('jrMarket', 'updated_skin', $_mta, $_rs);
        jrCore_logger('INF', "successfully updated skin {$skin} from version {$_mta['version']} to {$_rs['version']}");
        return true;
    }
    unlink("{$cdr}/{$_rs['name']}.tar");
    return false;
}

/**
 * Create a purchase checkout button
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrMarket_purchase_button($params, $smarty)
{
    global $_mods, $_conf;
    if (!isset($params['item']) || !is_array($params['item']) || !isset($params['item']['_item_id'])) {
        return '';
    }
    if (!isset($params['key']) || strlen($params['key']) < 10) {
        return '';
    }
    $val = 'Purchase';
    if (isset($params['label'])) {
        $val = trim($params['label']);
    }
    $url = jrCore_get_module_url('jrMarket');
    $iid = (int) $params['item']['_item_id'];
    // See if we are doing a bundle
    $typ = $params['item']['market_type'];
    $nam = $params['item']['market_name'];
    $prc = $params['item']['market_file_item_price'];
    $opt = "StripeCheckout.open({
            key: '{$params['key']}',
            image: '{$params['item']['market_image_url']}',
            address: true,
            amount: " . smarty_modifier_jrMarket_price_to_cents($prc) . ",
            currency: 'usd',
            name: 'Purchase {$params['item']['market_type']}:',
            description: '{$params['item']['market_title']}',
            panelLabel:  'Install Now - {$prc}',
            token: token
        });";
    $out = '';
    $pmt = '';
    $ins = false; // allow bundle install
    $sbe = false; // show bundle savings explanation
    if (isset($params['type']) && $params['type'] == 'bundle') {

        // If they already own some of the items in this bundle, then
        // we can show them a pro-rated price for the remaining items
        // that they have not purchased yet
        if (isset($params['item']['bundle_items']) && is_array($params['item']['bundle_items'])) {
            $typ = 'bundle';
            $nam = array();
            // First get our total
            $tot = 0;
            $own = 0;
            $lic = 0;
            foreach ($params['item']['bundle_items'] as $v) {
                $amt = (isset($v['market_file_item_price']) && $v['market_file_item_price'] > 0) ? $v['market_file_item_price'] : 0;
                // See if this item is already installed on the system
                switch ($v['market_type']) {
                    case 'module':
                        if (isset($_mods["{$v['market_name']}"]) || (isset($v['market_allow_license_install']) && $v['market_allow_license_install'] == '1')) {
                            $own += $amt;
                        }
                        else {
                            $nam[] = $v['market_name'];
                        }
                        break;
                    case 'skin':
                        if (!isset($_skins)) {
                            $_skins = jrCore_get_skins();
                        }
                        if (isset($_skins["{$v['market_name']}"]) || (isset($v['market_allow_license_install']) && $v['market_allow_license_install'] == '1')) {
                            $own += $amt;
                        }
                        else {
                            $nam[] = $v['market_name'];
                        }
                        break;
                    default:
                        // Something else
                        continue 2;
                        break;
                }
                $tot += $amt;
                if (isset($v['market_allow_license_install']) && $v['market_allow_license_install'] == '1') {
                    $lic += $amt;
                }
            }
            // If we own ALL of the items...
            if ($tot == $lic) {
                // We're going to allow a quick install if any are not installed
                $val = 'Install Bundle';
                $pmt = "You own licenses for all items in this bundle - install?";
                $out .= "<span style=\"display:inline-block;margin-bottom:10px\"><h3>OWNED&nbsp;&nbsp;&nbsp;<span style=\"text-decoration:line-through;\">&#36;{$params['item']['bundle_item_price']}</span></h3></span><br>";
                $sav = '';
                $ins = true;
                $prc = '0.00';
            }
            elseif ($own > 0) {
                // So $tot is the total bundle price, and $own is the amount of the items that we already own.
                $dif = round(($params['item']['bundle_item_price'] / $tot), 2);
                // $dif is the percentage of the total value the bundle gives you (i.e. what percent of the actual value is the bundle price)
                $sav = number_format($tot - $own, 2);
                $own = number_format(round((($tot - $own) * $dif), 2), 2);
                $val = 'Complete Bundle';
                $pmt = "Purchase the remaining items in this bundle for USD " . number_format($own, 2) . "?";
                $bpc = number_format($params['item']['bundle_item_price'], 2);
                $out .= "<span style=\"display:inline-block;margin-bottom:10px\"><h3>&#36;{$own}&nbsp;&nbsp;&nbsp;<span style=\"text-decoration:line-through;\">&#36;{$bpc}</span></h3></span><br>";
                $params['item']['bundle_item_price'] = $own;
                $sav                                 = number_format($sav - $own, 2);
                $prc                                 = $own;
                $sbe                                 = true;
            }
            else {
                $own = $params['item']['bundle_item_price'];
                if (isset($params['quick']) && $params['quick'] === true) {
                    $val = 'Quick Purchase';
                }
                else {
                    $val = 'Purchase Bundle';
                }
                $own = number_format($own, 2);
                $pmt = "Quick purchase this bundle for USD " . number_format($own, 2) . "?  Please be patient while the bundle installs - it could take several minutes depending on the size of the bundle.";
                $out .= "<span style=\"display:inline-block;margin-bottom:10px\"><h3>&#36;{$own}</h3></span><br>";
                $sav = number_format($tot - $own, 2);
                $prc = $own;
            }
            $nam = jrCore_url_encode_string(implode(',', $nam));
            $opt = "StripeCheckout.open({
                    key: '{$params['key']}',
                    address: true,
                    amount: " . smarty_modifier_jrMarket_price_to_cents($own) . ",
                    currency: 'usd',
                    name: '{$val}',
                    description: '{$params['item']['bundle_title']}',
                    panelLabel:  'Install Now - ',
                    token: token
                });";
        }
        else {
            return 'jrMarket_purchase_button: invalid bundle items';
        }
    }
    $out .= '<img id="fsi_' . $iid . '" src="' . $_conf['jrCore_base_url'] . '/skins/' . $_conf['jrCore_active_skin'] . '/img/submit.gif" width="24" height="24" style="display:none" alt="working...">&nbsp;';
    if ($ins) {
        $out .= "<input id=\"p{$iid}\" type=\"button\" class=\"form_button\" style=\"width:150px;\" value=\"" . addslashes($val) . "\" onclick=\"if (confirm('{$pmt}')) { jrMarket_quick_purchase('bundle', '{$prc}','{$iid}', '{$nam}'); }\">";
    }
    elseif (isset($params['quick']) && $params['quick'] === true) {
        $out .= "<input id=\"p{$iid}\" type=\"button\" class=\"form_button\" style=\"width:150px;\" value=\"" . addslashes($val) . "\" onclick=\"if (confirm('{$pmt}')) { jrMarket_quick_purchase('bundle', '{$prc}','{$iid}', '{$nam}'); }\">";
    }
    else {
        $out .= '<img id="fsi_' . $iid . '" src="' . $_conf['jrCore_base_url'] . '/skins/' . $_conf['jrCore_active_skin'] . '/img/submit.gif" width="24" height="24" style="display:none" alt="working...">&nbsp;';
        $out .= "<input id=\"p{$iid}\" type=\"button\" class=\"form_button\" style=\"width:150px;\" value=\"" . addslashes($val) . "\">
        <script>
        $('#p{$iid}').click(function() {
            var datap = { type: '{$typ}', market_id: '{$iid}', price: '{$prc}', item: '{$nam}' };
            var token = function(res) {
                datap.token = res.id;
                var purl = '" . $_conf['jrCore_base_url'] . '/' . $url . "/purchase/__ajax=1';
                jrCore_set_csrf_cookie(purl);
                $.ajax({
                    type: 'POST',
                    data: datap,
                    cache: false,
                    dataType: 'json',
                    url: purl,
                    success: function(msg) {
                        if (typeof msg.error != 'undefined') {
                            alert(msg.error);
                        }
                        else {
                            $('#fsi_{$iid}').show(300, function() {
                                var iurl = '{$_conf['jrCore_base_url']}/{$url}/install_item/{$typ}/{$nam}/{$iid}/license=' + msg.license;
                                jrCore_set_csrf_cookie(iurl);
                                $.get(iurl, function(res) {
                                    // Check for error
                                    if (typeof res.error !== \"undefined\") {
                                        $('#fsi_" . $iid . "').hide(300, function() {
                                            alert(res.error);
                                        });
                                    }
                                    else {
                                        jrCore_window_location(res.url);
                                    }
                                });
                            });
                        }
                    }
                });
            };
            {$opt}
            return false;
        });
        </script>";
    }
    if (isset($sav) && strlen($sav) > 0) {
        $out .= '<br><br><h3>Save &#36;' . $sav . '</h3>';
    }
    if ($sbe) {
        $out .= "<br><br><div class=\"p10\" style=\"white-space:normal\"><small>You already own some of the items in this bundle, and can purchase the remaining bundle items at the same savings as the original bundle.</small></div>";
    }
    return $out;
}

/**
 * Convert a Price tag to Cents
 * @param $price string Price to convert
 * @return string
 */
function smarty_modifier_jrMarket_price_to_cents($price)
{
    if (!isset($price) || strlen($price) < 4 || !jrCore_checktype($price, 'price')) {
        return $price;
    }
    return round($price * 100);
}

/**
 * Reset any configured opcode caches
 * @return bool
 */
function jrMarket_reset_opcode_caches()
{
    if (function_exists('apc_clear_cache')) {
        apc_clear_cache();
    }
    if (function_exists('xcache_clear_cache')) {
        $on = ini_get('xcache.admin.enable_auth');
        if ($on != 1 && $on != 'on') {
            /** @noinspection PhpUndefinedConstantInspection */
            @xcache_clear_cache(XC_TYPE_PHP, 0);
        }
        else {
            // [xcache.admin]
            // xcache.admin.enable_auth = Off
            // ; Configure this to use admin pages
            // ; xcache.admin.user = "mOo"
            // ; xcache.admin.pass = md5($your_password)
            // ; xcache.admin.pass = ""
            // See if we have been setup properly
            if (strlen(ini_get('xcache.admin.user')) > 0 && ini_get('xcache.admin.user') !== 'mOo') {
                /** @noinspection PhpUndefinedConstantInspection */
                @xcache_clear_cache(XC_TYPE_PHP, 0);
            }
        }
    }
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    clearstatcache();
    return true;
}

//-----------------------------------
// FTP functions
//-----------------------------------

/**
 * Connect to an FTP Server
 * @param $host string Host Name
 * @param $port int Port Number
 * @param $user string Username
 * @param $pass string Password
 * @return bool|mixed|resource
 */
function jrMarket_ftp_connect($host, $port, $user, $pass)
{
    $key = md5("{$host}:{$port}@{$user}:{$pass}");
    $tmp = jrCore_get_flag('jrnetwork_ftp_connect_' . $key);
    if ($tmp) {
        return $tmp;
    }
    $res = ftp_connect($host, $port, 10);
    if (!isset($res) || !is_resource($res)) {
        return false;
    }
    $tmp = ftp_login($res, $user, $pass);
    if (!isset($tmp) || $tmp === false) {
        return false;
    }
    jrCore_set_flag('jrnetwork_ftp_connect_' . $key, $res);
    return $res;
}

/**
 * Set permissions on modules/skins directory
 * @param $type string module|skin
 * @param $item string module/skin directory name
 * @param $mode int Directory Mode open|close
 * @return bool
 */
function jrMarket_ftp_set_permissions($type, $item, $mode)
{
    global $_conf;
    // Make sure we are configured
    $_to_check = array('ftp_host', 'ftp_user', 'ftp_pass');
    foreach ($_to_check as $chk) {
        if (!isset($_conf["jrMarket_{$chk}"]) || strlen($_conf["jrMarket_{$chk}"]) === 0) {
            return false;
        }
    }
    // Connect up
    $res = jrMarket_ftp_connect($_conf['jrMarket_ftp_host'], $_conf['jrMarket_ftp_port'], $_conf['jrMarket_ftp_user'], $_conf['jrMarket_ftp_pass']);
    if (!$res) {
        return false;
    }
    // ftp_exec ($res, 'epsv4 off');
    ftp_pasv($res, true);

    // Get our current working FTP directory
    $pwd = ftp_pwd($res);
    if (!isset($pwd) || strlen($pwd) === 0) {
        return false;
    }
    // First - see if we can simply chdir using the full path
    if (!@ftp_chdir($res, APP_DIR)) {
        if ($pwd != APP_DIR) {
            // We need to figure out if we have to use a relative portion
            // of our APP_DIR, or if we can reference things by full path
            if (strpos(APP_DIR, $pwd) === 0) {
                // We are starting in a lower directory than our APP DIR
                $dir = str_replace("{$pwd}/", '', APP_DIR);
                // See if we can change to our directory
                if (!@ftp_chdir($res, $dir)) {
                    // We don't know our directory - we need to cycle through and
                    // find out where our files are living
                    $_dr = array();
                    $_pt = explode('/', APP_DIR);
                    foreach ($_pt as $k => $part) {
                        if (@ftp_chdir($res, $part)) {
                            // We found a good part?
                            $_dr[] = $part;
                        }
                        else {
                            unset($_pt[$k]);
                            if (count($_dr) > 0) {
                                $_dr = array(); // restart
                            }
                        }
                    }
                    if (count($_dr) === 0) {
                        // Could not figure out root FTP dir
                        return false;
                    }
                }
            }
        }
    }
    switch ($mode) {
        case 'open':
            $perm = 0777;
            if (@ftp_chmod($res, $perm, "{$type}s")) {
                return true;
            }
            break;
        case 'close':
            $perm = 0755;
            if (@ftp_chmod($res, $perm, "{$type}s")) {
                return true;
            }
            break;
        default:
            return false;
            break;
    }
    if (ftp_chdir($res, "{$type}s")) {
        $_files = ftp_rawlist($res, '.');
        if (!is_array($_files)) {
            return false;
        }
        foreach ($_files as $list) {
            // Make sure directories are permissioned correctly
            if (strpos($list, 'd') === 0 && strpos($list, $item)) {
                $idir = jrCore_string_field($list, 'END');
                @ftp_chmod($res, $perm, $idir);
            }
        }
    }
    return false;
}

/**
 * Add some items to the System Check
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrMarket_system_check_listener($_data, $_user, $_conf, $_args, $event)
{
    // find the marketplace system marked as 'default'
    $tbl = jrCore_db_table_name('jrMarket', 'system');
    $req = "SELECT * FROM {$tbl} WHERE system_default = 'on'";
    $_rt = jrCore_db_query($req, 'SINGLE');

    $dat             = array();
    $dat[1]['title'] = 'Marketplace';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = 'API Settings';
    $dat[2]['class'] = 'center';

    $murl = jrCore_get_module_url('jrMarket');
    $conf = "<a href=\"{$_conf['jrCore_base_url']}/{$murl}/release_system_update/id={$_rt['system_id']}\" style=\"text-decoration:underline\">Update Marketplace Config</a>";
    if ($_rt && is_array($_rt)) {

        if (!jrCore_checktype_md5($_rt['system_code'])) {
            $dat[3]['title'] = $_args['fail'];
            $dat[4]['title'] = "System ID is missing - {$conf}";
        }
        elseif (!isset($_rt['system_email']) || !jrCore_checktype_email($_rt['system_email'])) {
            $dat[3]['title'] = $_args['fail'];
            $dat[4]['title'] = "System Email is missing - {$conf}";
        }
        else {
            $dat[3]['title'] = $_args['pass'];
            $dat[4]['title'] = "Marketplace is properly configured - <a href=\"{$_conf['jrCore_base_url']}/{$murl}/system_update\" style=\"text-decoration:underline\">Check for Updates</a>";
        }
    }
    else {
        $dat[3]['title'] = $_args['fail'];
        $dat[4]['title'] = "Missing Marketplace settings - {$conf}";
    }
    $dat[3]['class'] = 'center';
    jrCore_page_table_row($dat);

    // modules
    if (!is_writable(APP_DIR . '/modules')) {
        // See if we are configured for FTP...
        if (!isset($_conf['jrMarket_ftp_user']) || strlen($_conf['jrMarket_ftp_user']) === 0 || !isset($_conf['jrMarket_ftp_pass']) || strlen($_conf['jrMarket_ftp_pass']) === 0) {
            $dat             = array();
            $dat[1]['title'] = 'Modules directory';
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = 'writable by web user';
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $_args['fail'];
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = "Your modules directory is not writable - make sure you have entered FTP settings into the <a href=\"{$_conf['jrCore_base_url']}/{$murl}/admin/global\" style=\"text-decoration:underline\">Global Config here</a> so the system can install and update modules.";
            jrCore_page_table_row($dat);
        }
    }

    // skins
    if (!is_writable(APP_DIR . '/skins')) {
        if (!isset($_conf['jrMarket_ftp_user']) || strlen($_conf['jrMarket_ftp_user']) === 0 || !isset($_conf['jrMarket_ftp_pass']) || strlen($_conf['jrMarket_ftp_pass']) === 0) {
            $dat             = array();
            $dat[1]['title'] = 'Skins directory';
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = 'writable by web user';
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $_args['fail'];
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = "Your skins directory is not writable - make sure you have entered FTP settings into the Marketplace <a href=\"{$_conf['jrCore_base_url']}/{$murl}/admin/global\" style=\"text-decoration:underline\">Global Config here</a> so the system can install and update skins.";
            jrCore_page_table_row($dat);
        }
    }

    // lastly check for proper xcache setup
    if (ini_get('xcache.admin.enable_auth') == 1 && (ini_get('xcache.admin.user') == 'mOo' || strlen(ini_get('xcache.admin.user')) === 0)) {
        // Xcache is not setup properly
        $dat             = array();
        $dat[1]['title'] = 'Xcache Configuration';
        $dat[1]['class'] = 'center';
        $dat[2]['title'] = 'admin account setup properly';
        $dat[2]['class'] = 'center';
        $dat[3]['title'] = $_args['fail'];
        $dat[3]['class'] = 'center';
        $dat[4]['title'] = 'Xcache is not setup properly - xcache cannot be reset when upgrading modules and skins.<br>Make sure and either <strong>disable</strong> Xcache authentication, or properly set a user name and password - see:<br><a href="http://xcache.lighttpd.net/wiki/XcacheIni#XCacheAdministration" target="_blank"><u>Xcache Administration</u></a><br>Contact your hosting provider for assistance if needed.';
        jrCore_page_table_row($dat);
    }
    return $_data;
}
