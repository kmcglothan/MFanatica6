<?php
/**
 * Jamroom Developer Tools module
 *
 * copyright 2018 The Jamroom Network
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
function jrDeveloper_meta()
{
    $_tmp = array(
        'name'        => 'Developer Tools',
        'url'         => 'developer',
        'version'     => '1.5.2',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Developer tools for working with modules and skins',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/932/developer-tools',
        'license'     => 'mpl',
        'requires'    => 'jrCore:6.0.0',
        'category'    => 'developer'
    );
    return $_tmp;
}

/**
 * init
 */
function jrDeveloper_init()
{
    global $_conf;
    jrCore_register_module_feature('jrCore', 'javascript', 'jrDeveloper', 'jrDeveloper.js', 'admin');
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrDeveloper', "{$_conf['jrCore_base_url']}/modules/jrDeveloper/adminer.php", array('Database Admin', 'Browse your Database Tables - <b>carefully!</b>'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrDeveloper', "{$_conf['jrCore_base_url']}/modules/jrDeveloper/apc.php", array('Memory Cache Admin', 'View the local Memory Cache statistics and usage'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrDeveloper', 'clone_skin', array('Clone Skin', 'Save a copy of an existing skin to a new name'));
    if (isset($_conf['jrDeveloper_developer_prefix']{1}) && isset($_conf['jrDeveloper_developer_name']{1})) {
        jrCore_register_module_feature('jrCore', 'tool_view', 'jrDeveloper', 'package_module', array('Package Module', 'Create a Module ZIP Package that can be uploaded to the Jamroom Marketplace'));
        jrCore_register_module_feature('jrCore', 'tool_view', 'jrDeveloper', 'package_skin', array('Package Skin', 'Create a Skin ZIP Package that can uploaded to the Jamroom Marketplace'));
    }
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrDeveloper', 'rebase_modules', array('Rebase Modules', 'Move marketplace modules back to their root folder and remove symlinks'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrDeveloper', 'rebase_skins', array('Rebase Skins', 'Move marketplace skins back to their root folder and remove symlinks'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrDeveloper', 'reset_system', array('Reset System', 'Reset the system to the state of a fresh install'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrDeveloper', 'reset_categories', array('Reset Categories', 'Return each module to its default module category in the ACP'));

    // Our default view for admins
    jrCore_register_module_feature('jrCore', 'default_admin_view', 'jrDeveloper', 'admin/tools');

    // Loader listeners
    jrCore_register_event_listener('jrCore', 'process_init', 'jrDeveloper_process_init_listener');
    jrCore_register_event_listener('jrCore', 'parsed_template', 'jrDeveloper_parsed_template_listener');
    jrCore_register_event_listener('jrCore', 'db_query_init', 'jrDeveloper_db_query_init_listener');
    jrCore_register_event_listener('jrCore', 'db_query_exit', 'jrDeveloper_db_query_exit_listener');

    // We have an event trigger
    jrCore_register_event_trigger('jrDeveloper', 'reset_system', 'Fired when the Reset System tool is run');

    return true;
}

//----------------------
// FUNCTIONS
//----------------------

/**
 * Get the version number for a local module
 * @param $mod string module
 * @return bool|string
 */
function jrDeveloper_get_local_module_version($mod)
{
    global $_mods;
    // Get version ON DISK if we can
    $vers = false;
    $_mta = @file(APP_DIR . "/modules/{$mod}/include.php");
    if ($_mta && is_array($_mta)) {
        $meta = false;
        foreach ($_mta as $line) {
            if (strpos(' ' . $line, "function") && strpos($line, '_meta()')) {
                $meta = true;
                continue;
            }
            if ($meta) {
                $line = trim(trim(str_replace(array('"', "'"), '', $line)), ',');
                $mkey = jrCore_string_field($line, 1);
                switch ($mkey) {
                    case 'version':
                        list(, $text) = explode('=>', $line);
                        if (isset($text) && strlen(trim($text)) > 0) {
                            $vers = trim($text);
                            break 2;
                        }
                        break;
                }
            }
        }
    }
    return ($vers) ? $vers : $_mods[$mod]['module_version'];
}

/**
 * Reset any configured opcode caches
 * @return bool
 */
function jrDeveloper_reset_opcode_caches()
{
    if (function_exists('apc_clear_cache')) {
        apc_clear_cache();
    }
    if (function_exists('xcache_clear_cache') && defined('XC_TYPE_PHP')) {
        $on = ini_get('xcache.admin.enable_auth');
        if ($on != 1 && $on != 'on') {
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
                @xcache_clear_cache(XC_TYPE_PHP, 0);
            }
        }
    }
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    return true;
}

/**
 * Rebase/reset the modules or skins directory
 * @param $dir string one of "modules" or "skins"
 * @param string $delete on|off delete old version dirs
 * @return mixed
 */
function jrDeveloper_rebase_directory($dir, $delete = 'off')
{
    switch ($dir) {
        case 'modules':
            $tag = 'module';
            break;
        case 'skins':
            $tag = 'skin';
            break;
        default:
            return false;
            break;
    }
    // do stuff
    $base_dir = APP_DIR . '/' . $dir;
    $_dir     = glob($base_dir . '/*');
    $i        = 0;
    foreach ($_dir as $link) {
        if (is_link($link)) {
            // its a symlink, move it to its base dir.
            $module    = basename($link);
            $linked_to = readlink($link);
            if (strpos($linked_to, '-release-')) {

                // remove symlink
                if (unlink("{$base_dir}/{$module}")) {
                    // move newest release to the basedir.
                    if (!rename("{$base_dir}/{$linked_to}", "{$base_dir}/{$module}")) {
                        // We have a problem - recreate symbolic link or system will be in an unusable state
                        chdir($base_dir);
                        if (!symlink($linked_to, $module)) {
                            jrCore_set_form_notice('error', "Unable to rename {$tag} OR re-link old {$tag} {$module} - restore {$tag} directory via FTP");
                            jrCore_form_result();
                        }
                        jrCore_set_form_notice('error', "Unable to rename old {$tag} {$module} - check file permissions");
                        jrCore_form_result();
                    }
                }
                $i++;
            }
        }
    }

    // delete old versions.
    $x = 0;
    if ($delete == 'on') {
        $_dir = glob("{$base_dir}/*-release-*");
        foreach ($_dir as $link) {
            // remove it
            jrCore_delete_dir_contents($link, false);
            if (!rmdir($link)) {
                jrCore_set_form_notice('error', "Unable to delete old version directory for {$tag}: " . $link);
                jrCore_form_result();
            }
            $x++;
        }
    }

    // Reset smarty caches
    $_tmp = glob(APP_DIR . "/data/cache/*");
    if (is_array($_tmp)) {
        $_dirs = array();
        foreach ($_tmp as $path) {
            if (is_dir($path)) {
                $_dirs[] = basename($path);
            }
        }
        $_dirs = jrCore_trigger_event('jrCore', 'template_cache_reset', $_dirs); // "template_cache_reset" event trigger
        foreach ($_dirs as $dir) {
            if ($dir == 'jrImage') {
                // Image cache is handled by separate Image cache reset tool
                continue;
            }
            if (is_dir(APP_DIR . "/data/cache/{$dir}")) {
                jrCore_delete_dir_contents(APP_DIR . "/data/cache/{$dir}");
            }
        }
    }

    jrCore_delete_all_cache_entries();
    jrDeveloper_reset_opcode_caches();
    clearstatcache(true);
    return array($i, $x);
}

/**
 * Add A license header to a file
 * @param $type string type of license
 * @param $name string module name
 * @param $file string file
 * @param $license string license
 * @return bool
 */
function jrDeveloper_add_license_header($type, $name, $file, $license)
{
    global $_mods;
    if (!is_file($file)) {
        return false;
    }
    switch ($type) {
        case 'module':
            $_rep = array(
                'item_name'      => $_mods[$name]['module_name'],
                'item_directory' => $_mods[$name]['module_directory'],
                'item_type'      => 'module'
            );
            break;
        case 'skin':
            $_tmp = jrCore_skin_meta_data($name);
            $_rep = array(
                'item_name'      => $_tmp['name'],
                'item_directory' => $name,
                'item_type'      => 'skin'
            );
            break;
        default:
            jrCore_logger('CRI', "jrDeveloper_add_license_header() invalid item type received - must be one of module,skin");
            return false;
            break;
    }
    $open = false;
    $temp = "<?php\n" . jrCore_parse_template("{$license}_header.tpl", $_rep, 'jrDeveloper');
    $_tmp = file($file);
    foreach ($_tmp as $line) {
        if (strpos($line, '<?php') === 0 && !$open) {
            $open = true;
            continue;
        }
        elseif ($open && strpos(trim($line), '/*') === 0) {
            $open = false;
            continue;
        }
        $temp .= rtrim($line) . "\n";
    }
    return jrCore_write_to_file($file, $temp);
}

//----------------------
// EVENT LISTENERS
//----------------------

/**
 * Track SQL query counts for performance
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrDeveloper_db_query_init_listener($_data, $_user, $_conf, $_args, $event)
{
    // Are we keeping track of slow SQL query calls?
    if (isset($_conf['jrDeveloper_enable_query_counts']) && $_conf['jrDeveloper_enable_query_counts'] == 'on' && jrCore_local_cache_is_enabled()) {
        $now = explode(' ', microtime());
        $now = ($now[1] + $now[0]);
        jrCore_set_flag('jrdeveloper_query_init', $now);
    }
    return $_data;
}

/**
 * Track SQL query duration for performance
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrDeveloper_db_query_exit_listener($_data, $_user, $_conf, $_args, $event)
{
    // Are we keeping track of slow SQL query calls?
    if ($beg = jrCore_get_flag('jrdeveloper_query_init')) {
        $now = explode(' ', microtime());
        $now = ($now[1] + $now[0]);
        $end = ($now - $beg) * 1000;
        $end = round($end);
        if ($end > 0 && strpos(PHP_VERSION, '5.3') !== 0 && strpos($_args[0], 'SELECT') === 0) {
            if ($_tr = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10)) {
                if (isset($_tr[0])) {
                    $_fn = false;
                    foreach ($_tr as $v) {
                        if (!strpos($v['file'], 'jrCore/lib')) {
                            $_fn = $v;
                            break;
                        }
                    }
                    if ($_fn) {
                        $key  = md5($_args[0]);
                        $file = str_replace(APP_DIR . '/', '', $_fn['file']);
                        apcu_inc("c:{$key}:{$file}:{$_fn['line']}", 1);
                        apcu_inc("d:{$key}", $end);
                    }
                }
            }
        }
        jrCore_delete_flag('jrdeveloper_query_init');
    }
    return $_data;
}

/**
 * Add Template name as HTML comments if enabled
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrDeveloper_parsed_template_listener($_data, $_user, $_conf, $_args, $event)
{
    // Turn on error logging if developer mode is on
    if (isset($_conf['jrDeveloper_template_debug']) && $_conf['jrDeveloper_template_debug'] == 'on') {
        switch ($_args['jr_template']) {
            // Some template cause issues if we put HTML comments inside
            case 'form_editor.tpl':
            case 'login_check.tpl':
                break;
            default:
                if (strpos($_args['jr_template_full_path'], 'string:') !== 0 && strpos($_args['jr_template'], 'email_') !== 0 && !strpos(' ' . $_args['jr_template'], 'rss')) {
                    $tpl   = "{$_args['jr_template_directory']}/{$_args['jr_template']}";
                    $_data = "<!-- BEGIN {$tpl} -->\n{$_data}\n<!-- END {$tpl} -->\n";
                }
                break;
        }
    }
    return $_data;
}

/**
 * Turn on PHP logging if developer mode is on
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrDeveloper_process_init_listener($_data, $_user, $_conf, $_args, $event)
{
    // Turn on error logging if developer mode is on
    if (isset($_conf['jrDeveloper_developer_mode']) && $_conf['jrDeveloper_developer_mode'] == 'on') {
        error_reporting(E_ALL);
    }
    return $_data;
}

/**
 * Export any custom Form Designer fields to a module
 * @param $module string Module to export
 * @param $path string full path to module
 * @return bool|string
 */
function jrDeveloper_export_form_designer_fields($module, $path)
{
    $_views = array('update', 'create');
    foreach ($_views as $view) {
        $tmp = jrCore_get_designer_form_fields($module, $view);
        if (is_array($tmp)) {
            $_fields[$view] = $tmp;
        }
    }
    if (isset($_fields) && is_array($_fields)) {
        $fullpath = $path . '/custom_form_fields.json';
        jrCore_write_to_file($fullpath, json_encode($_fields));
        return $fullpath;
    }
    return false;
}

/**
 * Create lang file to include custom lang strings
 * @param $module string Module to export lang files for
 * @param $path string full path to file
 * @return bool
 */
function jrDeveloper_export_lang_strings($module, $path)
{
    $tbl = jrCore_db_table_name('jrUser', 'language');
    $req = "SELECT * FROM {$tbl} WHERE lang_code = 'en-US' AND lang_module = '{$module}'";
    $_rt = jrCore_db_query($req, 'NUMERIC');

    if (isset($_rt) && is_array($_rt)) {
        $temp    = '';
        $started = false;
        $_tmp    = file($path);
        foreach ($_tmp as $line) {
            if (!$started) {
                $temp .= rtrim($line) . "\n";
            }
            else {
                break;
            }
            if (preg_match('/or exit/', $line)) {
                $started = true; // inside the writable area.
            }

        }
        // got the headers for the lang file, now write the current state of the DB as the contents.
        foreach ($_rt as $_l) {
            $temp .= '$lang[\'' . $_l['lang_key'] . '\'] =  \'' . jrCore_entity_string($_l['lang_text']) . '\';' . "\n";
        }
        jrCore_write_to_file($path, $temp);
    }
    return true;
}

/**
 * Returns a Unique Profile Name
 */
function jrDeveloper_get_unique_profile_name()
{
    $_pn = array(
        'Vivamus'      => 1,
        'Fermentum'    => 1,
        'Semper'       => 1,
        'Porta'        => 1,
        'Nunc'         => 1,
        'Diam'         => 1,
        'Velit'        => 1,
        'Adipiscing'   => 1,
        'Tristique'    => 1,
        'Vitae'        => 1,
        'Sagittis'     => 1,
        'Odio'         => 1,
        'Maecenas'     => 1,
        'Convallis'    => 1,
        'Ullamcorper'  => 1,
        'Ultricies'    => 1,
        'Curabitur'    => 1,
        'Ornare'       => 1,
        'Ligula'       => 1,
        'Consectetur'  => 1,
        'Nisi'         => 1,
        'Iaculis'      => 1,
        'Fringilla'    => 1,
        'Dictum'       => 1,
        'Pretium'      => 1,
        'Volutpat'     => 1,
        'Arcu'         => 1,
        'Ante'         => 1,
        'Placerat'     => 1,
        'Erat'         => 1,
        'Elit'         => 1,
        'Urna'         => 1,
        'Turpis'       => 1,
        'Quisque'      => 1,
        'Metus'        => 1,
        'Amet'         => 1,
        'Tincidunt'    => 1,
        'Orci'         => 1,
        'Fusce'        => 1,
        'Eget'         => 1,
        'Congue'       => 1,
        'Vestibulum'   => 1,
        'Dolor'        => 1,
        'Elementum'    => 1,
        'Porttitor'    => 1,
        'Venenatis'    => 1,
        'Pulvinar'     => 1,
        'Tellus'       => 1,
        'Gravida'      => 1,
        'Faucibus'     => 1,
        'Euismod'      => 1,
        'Justo'        => 1,
        'Nullam'       => 1,
        'Cursus'       => 1,
        'Suscipit'     => 1,
        'Ultrices'     => 1,
        'Sodales'      => 1,
        'Facilisis'    => 1,
        'Lectus'       => 1,
        'Aliquam'      => 1,
        'Massa'        => 1,
        'Ipsum'        => 1,
        'Bibendum'     => 1,
        'Purus'        => 1,
        'Nulla'        => 1,
        'Laoreet'      => 1,
        'Tortor'       => 1,
        'Viverra'      => 1,
        'Quam'         => 1,
        'Suspendisse'  => 1,
        'Tempor'       => 1,
        'Quis'         => 1,
        'Neque'        => 1,
        'Etiam'        => 1,
        'Luctus'       => 1,
        'Lorem'        => 1,
        'Rutrum'       => 1,
        'Lobortis'     => 1,
        'Nisl'         => 1,
        'Enim'         => 1,
        'Aenean'       => 1,
        'Commodo'      => 1,
        'Imperdiet'    => 1,
        'Sapien'       => 1,
        'Phasellus'    => 1,
        'Dapibus'      => 1,
        'Condimentum'  => 1,
        'Praesent'     => 1,
        'Lacus'        => 1,
        'Malesuada'    => 1,
        'Augue'        => 1,
        'Mauris'       => 1,
        'Eleifend'     => 1,
        'Egestas'      => 1,
        'Donec'        => 1,
        'Sollicitudin' => 1,
        'Cras'         => 1,
        'Class'        => 1,
        'Aptent'       => 1,
        'Taciti'       => 1,
        'Sociosqu'     => 1,
        'Litora'       => 1,
        'Torquent'     => 1,
        'Conubia'      => 1,
        'Nostra'       => 1,
        'Inceptos'     => 1,
        'Himenaeos'    => 1,
        'Molestie'     => 1,
        'Proin'        => 1,
        'Morbi'        => 1,
        'Duis'         => 1,
        'Magna'        => 1,
        'Hendrerit'    => 1,
        'Auctor'       => 1,
        'Pharetra'     => 1,
        'Tempus'       => 1,
        'Consequat'    => 1,
        'Vulputate'    => 1,
        'Eros'         => 1
    );
    $_tm = array_rand($_pn, 2);
    return $_tm[0] . ' ' . $_tm[1] . ' ' . mt_rand(111111, 999999);
}

/**
 * evaluates whether one module version is higher or lower than another.
 * @param $a string version 1
 * @param $b string version 2
 * @return string
 */
function jrDeveloper_ver_compare($a, $b)
{
    switch (version_compare($a, $b)) {
        case -1:
            return 'less';
            break;
        case 1:
            return 'greater';
            break;
    }
    return 'equal';
}
