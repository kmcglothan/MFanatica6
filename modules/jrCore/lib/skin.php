<?php
/**
 * Jamroom System Core module
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
 * @package Skin
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Get info for a skin from the DB skin table
 * @param string $skin
 * @return mixed
 */
function jrCore_get_skin_db_info($skin)
{
    $tbl = jrCore_db_table_name('jrCore', 'skin');
    $skn = jrCore_db_escape($skin);
    $req = "SELECT * FROM {$tbl} WHERE skin_directory = '{$skn}'";
    return jrCore_db_query($req, 'SINGLE');
}

/**
 * Verify a skin is installed properly
 * @param string $skin Skin to verify
 * @return bool
 */
function jrCore_verify_skin($skin)
{
    // Is this a BRAND NEW skin?
    $ins = false;
    $_sk = jrCore_get_skin_db_info($skin);
    if (!$_sk || !is_array($_sk)) {
        // New Skin
        $tbl = jrCore_db_table_name('jrCore', 'skin');
        $skn = jrCore_db_escape($skin);
        $req = "INSERT INTO {$tbl} (skin_directory, skin_updated, skin_custom_css, skin_custom_image) VALUES ('{$skn}',UNIX_TIMESTAMP(),'','') ON DUPLICATE KEY UPDATE skin_updated = UNIX_TIMESTAMP()";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (!$cnt || $cnt !== 1) {
            // We did NOT install correctly
            return false;
        }
        // This is a NEW install of this skin
        $ins = true;
    }

    if (is_file(APP_DIR . "/skins/{$skin}/include.php")) {
        $func = "{$skin}_skin_init";
        if (!function_exists($func)) {
            require_once APP_DIR . "/skins/{$skin}/include.php";
        }
        if (function_exists($func)) {
            $func();
        }
    }

    // Global Config
    if (is_file(APP_DIR . "/skins/{$skin}/config.php")) {
        $func = "{$skin}_skin_config";
        if (!function_exists($func)) {
            require_once APP_DIR . "/skins/{$skin}/config.php";
        }
        if (function_exists($func)) {
            $func();
        }
    }

    // Quota
    if (is_file(APP_DIR . "/skins/{$skin}/quota.php")) {
        $func = "{$skin}_skin_quota_config";
        if (!function_exists($func)) {
            require_once APP_DIR . "/skins/{$skin}/quota.php";
        }
        if (function_exists($func)) {
            $func();
        }
    }

    // Install Language strings for Skin
    jrUser_install_lang_strings('skin', $skin);

    // If this is a NEW install of this skin, run installer
    if ($ins && is_file(APP_DIR . "/skins/{$skin}/install.php")) {
        $func = "{$skin}_skin_install";
        if (!function_exists($func)) {
            require_once APP_DIR . "/skins/{$skin}/install.php";
        }
        if (function_exists($func)) {
            $func();
        }
    }

    // Let other modules do their work
    jrCore_trigger_event('jrCore', 'verify_skin', jrCore_skin_meta_data($skin));

    return true;
}

/**
 * Delete a skin from the system
 * @param string $skin
 * @return bool|string
 */
function jrCore_delete_skin($skin)
{
    global $_conf;
    // Make sure we are NOT the active skin
    if ($skin == $_conf['jrCore_active_skin']) {
        return 'error: cannot delete the active skin';
    }

    // Remove from skins
    $tbl = jrCore_db_table_name('jrCore', 'skin');
    $skn = jrCore_db_escape($skin);
    $req = "DELETE FROM {$tbl} WHERE skin_directory = '{$skn}' LIMIT 1";
    jrCore_db_query($req);

    // Remove cache dir
    $cdr = jrCore_get_module_cache_dir($skin);
    jrCore_delete_dir_contents($cdr);
    rmdir($cdr);

    // Remove skin directories
    $_dirs = glob(APP_DIR . "/skins/{$skin}*");
    if ($_dirs && is_array($_dirs) && count($_dirs) > 0) {
        foreach ($_dirs as $dir) {
            $nam = basename($dir);
            if ($nam == $skin || strpos($nam, "{$skin}-release-") === 0) {
                if (is_link($dir)) {
                    unlink($dir);  // OK
                }
                elseif (is_dir($dir)) {
                    if (jrCore_delete_dir_contents($dir, false)) {
                        rmdir($dir);
                    }
                }
            }
        }
    }

    // Remove custom lang entries
    $tbl = jrCore_db_table_name('jrUser', 'language');
    $req = "DELETE FROM {$tbl} WHERE `lang_module` = '{$skn}'";
    jrCore_db_query($req);

    // Remove custom templates
    $tbl = jrCore_db_table_name('jrCore', 'template');
    $req = "DELETE FROM {$tbl} WHERE `template_module` = '{$skn}'";
    jrCore_db_query($req);

    // Remove settings
    $tbl = jrCore_db_table_name('jrCore', 'setting');
    $req = "DELETE FROM {$tbl} WHERE `module` = '{$skn}'";
    jrCore_db_query($req);
    return true;
}

/**
 * Generate a CSS Sprite background image from existing Icon images
 * @param $skin string Skin to use for overriding default icon images
 * @param $color string Icon color set to use (black||white)
 * @param $width int Pixel width for Icons
 * @return bool
 */
function jrCore_create_css_sprite($skin, $color = 'black', $width = 64)
{
    global $_conf;
    // Our ICON sprites live in jrCore/img/icons, and each can
    // be overridden by the skin with it's own version of the sprite

    // Standard Sprite Icons
    $swidth = 0;

    // Retina Sprite Icons
    $rwidth = 0;

    // 64px is as large as we go on some sprites
    $sval = $width;
    if ($sval > 64) {
        $sval = 64;
    }
    $rval = ($width * 2);
    if ($rval > 64) {
        $rval = 64;
    }

    // Modules
    $cdir = 'white';
    if ($color !== 'white') {
        $cdir = 'black';
    }
    $_icons = glob(APP_DIR . "/modules/*/img/icons_{$cdir}/*.png");
    if ($_icons && is_array($_icons)) {
        foreach ($_icons as $k => $v) {
            if (strpos($v, '-release-')) {
                unset($_icons[$k]);
                continue;
            }
            $name          = basename($v);
            $_icons[$name] = $v;
            unset($_icons[$k]);
            $swidth += $sval;
            $rwidth += $rval;
        }
    }
    // Override core with skin
    if (is_dir(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/img/icons_{$cdir}")) {
        $_skin = glob(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/img/icons_{$cdir}/*.png");
        if (is_array($_skin)) {
            foreach ($_skin as $v) {
                $name = basename($v);
                if (!isset($_icons[$name])) {
                    $swidth += $sval;
                    $rwidth += $rval;
                }
                $_icons[$name] = $v;
            }
        }
        unset($_skin);
    }

    // Now create our Sprite image
    $exif = false;
    if (function_exists('exif_imagetype')) {
        $exif = true;
    }

    $ssprite = imagecreatetruecolor($swidth, $sval);
    $rsprite = imagecreatetruecolor($rwidth, $rval);
    imagealphablending($ssprite, false);
    imagealphablending($rsprite, false);
    imagesavealpha($ssprite, true);
    imagesavealpha($rsprite, true);
    $sleft = 0;
    $rleft = 0;

    // Normal
    $surl = str_replace(array('http:', 'https:'), '', $_conf['jrCore_base_url']) . "/data/cache/{$_conf['jrCore_active_skin']}/sprite_{$color}_{$sval}.png?_v=" . time();
    $css  = ".sprite_icon_{$color}_{$sval}{display:inline-block;width:{$sval}px;height:{$sval}px;}\n";
    $css  .= ".sprite_icon_{$color}_{$sval}_img{background:url('{$surl}') no-repeat top left; height:100%;width:100%;}";

    // Retina / High Density
    $rurl = str_replace(array('http:', 'https:'), '', $_conf['jrCore_base_url']) . "/data/cache/{$_conf['jrCore_active_skin']}/sprite_{$color}_{$rval}.png?_v=" . time();
    $rcss = "\n@media screen and (-webkit-min-device-pixel-ratio: 2),\nscreen and (-o-min-device-pixel-ratio: 3/2),\nscreen and (min--moz-device-pixel-ratio: 2),\nscreen and (min-device-pixel-ratio: 2) {";
    $rcss .= "\n  .sprite_icon_{$color}_{$sval}_img{background:url('{$rurl}') no-repeat top left; height:100%;width:100%;}";

    $r = false;
    $g = false;
    $b = false;
    if ($color != 'black' && $color != 'white') {
        $r = hexdec(substr($color, 0, 2));
        $g = hexdec(substr($color, 2, 2));
        $b = hexdec(substr($color, 4, 2));
    }

    foreach ($_icons as $name => $image) {

        if ($exif && exif_imagetype($image) === false) {
            continue;
        }
        $img = imagecreatefrompng($image);
        if ($r) {
            // Changing color
            imagefilter($img, IMG_FILTER_COLORIZE, $r, $g, $b);
        }

        imagecopyresampled($ssprite, $img, $sleft, 0, 0, 0, $sval, $sval, 64, 64);
        imagecopyresampled($rsprite, $img, $rleft, 0, 0, 0, $rval, $rval, 64, 64);

        // Generate CSS
        $nam = str_replace('.png', '', $name);

        // Standard Resolution
        if ($sleft > 0) {
            $css .= "\n.sprite_icon_{$color}_{$sval}_{$nam}{background-position:-{$sleft}px 0}";
        }
        else {
            $css .= "\n.sprite_icon_{$color}_{$sval}_{$nam}{background-position:0 0}";
        }
        // Retina
        if ($rleft > 0) {
            $rcss .= "\n  .sprite_icon_{$color}_{$sval}_{$nam}{background-position:-{$sleft}px 0;";
        }
        else {
            $rcss .= "\n  .sprite_icon_{$color}_{$sval}_{$nam}{background-position:0 0;";
        }
        $rcss .= "background-size:{$swidth}px {$sval}px}";

        $sleft += $sval;
        $rleft += $rval;
    }
    $rcss .= "\n}";
    $dir  = jrCore_get_module_cache_dir($_conf['jrCore_active_skin']);

    // Standard resolution sprites
    jrCore_write_to_file("{$dir}/sprite_{$color}_{$sval}.css", $css . "\n" . $rcss . "\n");
    imagepng($ssprite, "{$dir}/sprite_{$color}_{$sval}.png");
    imagepng($rsprite, "{$dir}/sprite_{$color}_{$rval}.png");
    imagedestroy($ssprite);
    imagedestroy($rsprite);

    return true;
}

/**
 * Get HTML code for a given CSS icon sprite
 * @param $name string Name of CSS Icon to get
 * @param $size int Size (in pixels) for icon
 * @param $class string Additional icon HTML class
 * @param $color string Icon Color
 * @param $id string Icon DOM ID
 * @return string
 */
function jrCore_get_sprite_html($name, $size = null, $class = null, $color = null, $id = null)
{
    global $_conf;
    if (is_null($size)) {
        $_tmp = jrCore_get_registered_module_features('jrCore', 'icon_size');
        if (isset($_tmp["{$_conf['jrCore_active_skin']}"])) {
            $size = array_keys($_tmp["{$_conf['jrCore_active_skin']}"]);
            $size = reset($size);
            if (!is_numeric($size)) {
                $size = 32;
            }
        }
        else {
            $size = 32;
        }
    }
    if (is_null($color)) {
        $_tmp = jrCore_get_registered_module_features('jrCore', 'icon_color');
        if (isset($_tmp["{$_conf['jrCore_active_skin']}"])) {
            $color = array_keys($_tmp["{$_conf['jrCore_active_skin']}"]);
            $color = reset($color);
        }
        if (!$color) {
            $color = 'black';
        }
    }
    else {
        switch (strtolower($color)) {
            case 'ffffff':
                $color = 'white';
                break;
            case '000000':
                $color = 'black';
                break;
        }
    }

    // We have not included this size yet on our page - bring in now
    $dir = jrCore_get_module_cache_dir($_conf['jrCore_active_skin']);
    $mtm = @filemtime("{$dir}/sprite_{$color}_{$size}.css");
    if (!$mtm) {
        jrCore_create_css_sprite($_conf['jrCore_active_skin'], $color, $size);
        $mtm = filemtime("{$dir}/sprite_{$color}_{$size}.css");
    }
    $out = '<link rel="stylesheet" property="stylesheet" href="' . $_conf['jrCore_base_url'] . '/' . jrCore_get_module_url('jrCore') . '/icon_css/' . $size . '/' . $color . '/?_v=' . $mtm . '">';

    // See if we are doing a highlighted icon
    $did = '';
    if (strlen($id) > 0) {
        $did = " id=\"{$id}\"";
    }
    $cls = '';
    if (strlen($class) > 0) {
        $cls = " {$class}";
    }
    if (strpos($name, '-hilighted')) {
        $name = str_replace('-hilighted', '', $name);
        $out  .= "<span{$did} class=\"sprite_icon sprite_icon_hilighted sprite_icon_{$size} sprite_icon_{$color}_{$size}{$cls}\">";
    }
    else {
        $out .= "<span{$did} class=\"sprite_icon sprite_icon_{$size} sprite_icon_{$color}_{$size}{$cls}\">";
    }
    $out .= "<span class=\"sprite_icon_{$size} sprite_icon_{$color}_{$size} sprite_icon_{$size}_img sprite_icon_{$color}_{$size}_img sprite_icon_{$size}_{$name} sprite_icon_{$color}_{$size}_{$name}\">&nbsp;</span></span>";
    return $out;
}

/**
 * Get HTML for a module icon
 * @param $module string Module name
 * @param $size int Size (width/height)
 * @param null $class
 * @return bool|string
 */
function jrCore_get_module_icon_html($module, $size, $class = null)
{
    global $_conf, $_mods;
    $siz = (int) $size;
    $cls = '';
    if (!is_null($class) && strlen($class) > 0) {
        $cls = ' ' . $class;
    }
    if (!isset($_mods[$module])) {
        $cat = 'default';
        $ttl = 'default';
        $url = jrCore_get_module_url('jrImage');
        $img = "{$_conf['jrCore_base_url']}/{$url}/img/module/jrCore/default_module_icon.png?v={$_mods['jrCore']['module_updated']}";
    }
    else {
        $img = false;
        $cat = (isset($_mods[$module]['module_category'])) ? str_replace(' ', '_', trim(jrCore_str_to_lower($_mods[$module]['module_category']))) : 'default';
        $ttl = (isset($_mods[$module]['module_name'])) ? addslashes($_mods[$module]['module_name']) : 'default';
        if ($module != 'default') {
            $tmp = @filesize(APP_DIR . "/modules/{$module}/icon.png");
            if ($tmp > 0) {
                $img = "{$_conf['jrCore_base_url']}/modules/{$module}/icon.png?v={$tmp}";
            }
        }
        if (!$img) {
            $url = jrCore_get_module_url('jrImage');
            $img = "{$_conf['jrCore_base_url']}/{$url}/img/module/jrCore/default_module_icon.png?v={$_mods[$module]['module_updated']}";
        }
    }
    return "<span class=\"module_icon module_icon_{$cat}{$cls}\"><img src=\"{$img}\" alt=\"{$ttl}\" title=\"{$ttl}\" width=\"{$siz}\" height=\"{$siz}\"></span>";
}

/**
 * Get HTML for a skin icon
 * @param $skin string Skin name
 * @param $size int Size (width/height)
 * @param null $class
 * @return bool|string
 */
function jrCore_get_skin_icon_html($skin, $size, $class = null)
{
    global $_mods, $_conf;
    $_md = jrCore_skin_meta_data($skin);
    $siz = (int) $size;
    $cls = '';
    if (!is_null($class) && strlen($class) > 0) {
        $cls = ' ' . $class;
    }
    if (!$_md || !is_array($_md)) {
        $ttl = 'default';
        $url = jrCore_get_module_url('jrImage');
        $img = "{$_conf['jrCore_base_url']}/{$url}/img/module/jrCore/default_skin_icon.png?v={$_mods['jrCore']['module_updated']}";
    }
    else {
        $ttl = addslashes($_md['title']);
        $tmp = @filesize(APP_DIR . "/skins/{$skin}/icon.png");
        if ($tmp > 0) {
            $img = "{$_conf['jrCore_base_url']}/skins/{$skin}/icon.png?v={$tmp}";
        }
        else {
            $url = jrCore_get_module_url('jrImage');
            $img = "{$_conf['jrCore_base_url']}/{$url}/img/module/jrCore/default_skin_icon.png?v={$_md['version']}";
        }
    }
    return "<span class=\"module_icon skin_icon{$cls}\" style=\"width:{$siz}px\"><img src=\"{$img}\" alt=\"{$ttl}\" title=\"{$ttl}\" width=\"{$siz}\" height=\"{$siz}\"></span>";
}

/**
 * Get the configured icon size for a skin
 * @param int $default Size to use if unable to determine size
 * @return int
 */
function jrCore_get_skin_icon_size($default = 32)
{
    global $_conf;
    $_tmp = jrCore_get_registered_module_features('jrCore', 'icon_size');
    if ($_tmp && isset($_tmp["{$_conf['jrCore_active_skin']}"])) {
        $size = array_keys($_tmp["{$_conf['jrCore_active_skin']}"]);
        $size = reset($size);
        if (is_numeric($size)) {
            $default = (int) $size;
        }
    }
    return $default;
}

/**
 * jrCore_skin_meta_data - get meta data for a skin
 * @param string $skin skin string skin name
 * @return mixed returns metadata/key if found, false if not
 */
function jrCore_skin_meta_data($skin)
{
    $func = "{$skin}_skin_meta";
    if (!function_exists($func) && is_file(APP_DIR . "/skins/{$skin}/include.php")) {
        require_once APP_DIR . "/skins/{$skin}/include.php";
    }
    if (!function_exists($func)) {
        return false;
    }
    $_tmp = $func();
    if ($_tmp && is_array($_tmp)) {
        return $_tmp;
    }
    return false;
}

/**
 * Add a skin's global config to $_conf
 * @param $skin string Skin name to load
 * @param $_conf array Global Config
 * @return bool|array
 */
function jrCore_load_skin_config($skin, $_conf)
{
    $key = "loaded_skin_config_{$skin}";
    if (!jrCore_get_flag($key)) {
        $tbl = jrCore_db_table_name('jrCore', 'setting');
        $req = "SELECT CONCAT_WS('_', `module`, `name`) AS k, `value` AS v FROM {$tbl} WHERE `module` = '" . jrCore_db_escape($skin) . "'";
        $_cf = jrCore_db_query($req, 'k', false, 'v');
        if ($_cf && is_array($_cf)) {
            foreach ($_cf as $k => $v) {
                $_conf[$k] = $v;
            }
        }
        jrCore_set_flag($key, 1);
    }
    return $_conf;
}

/**
 * jrCore_get_skins
 * Retrieves a list of skins available on the system
 */
function jrCore_get_skins()
{
    $tmp = jrCore_get_flag('jrcore_get_skins');
    if ($tmp) {
        return $tmp;
    }
    $_sk = array();
    // and now do our deletion
    if ($h = opendir(APP_DIR . '/skins')) {
        while (($file = readdir($h)) !== false) {
            if ($file == '.' || $file == '..' || strpos($file, '-release-')) {
                continue;
            }
            elseif (is_file(APP_DIR . "/skins/{$file}/include.php")) {
                $_sk[$file] = $file;
            }
        }
        closedir($h);
    }
    ksort($_sk);
    jrCore_set_flag('jrcore_get_skins', $_sk);
    return $_sk;
}

/**
 * Delete an existing Skin Menu Item
 * @param $module string Module Name that created the Skin Menu Item
 * @param $unique string Unique name/tag for the Skin Menu Item
 * @return mixed
 */
function jrCore_delete_skin_menu_item($module, $unique)
{
    $tbl = jrCore_db_table_name('jrCore', 'menu');
    $req = "DELETE FROM {$tbl} WHERE menu_module = '" . jrCore_db_escape($module) . "' AND menu_unique = '" . jrCore_db_escape($unique) . "' LIMIT 1";
    return jrCore_db_query($req, 'COUNT');
}

/**
 * Parses a template and returns the result
 *
 * <br><p>
 * This is one of the main functions used to move data from php out to the smarty templates.
 * anything you put in the $_rep array becomes a template variable.  so if you have $_rep['foo'] = 'bar'; then you can call
 *  &#123;$foo} in the template to produce 'bar' output.
 * </p><br>
 * <p>
 *  If you have a template in your module, the system will look for it in the /templates directory.  so call it with <br>
 *  <i>$out = jrCore_parse_template('embed.tpl',null,'jrDisqus');</i>  will call /modules/jrDisqus/templates/embed.tpl
 * </p><br>
 * <p>
 *  Skins can over-ride the modules template by defining their own version of it. so
 * /module/jrDisqus/templates/embed.tpl can be over-ridden by the skin by defining:
 * /skin/jrElastic/jrDisqus_embed.tpl
 * </p>
 * @param string $template Name of template
 * @param array $_rep (Optional) replacement variables for use in template.
 * @param string $directory default active skin directory, module directory for module/templates
 * @param bool $disable_override - set to TRUE to disable skin template override
 * @return mixed
 */
function jrCore_parse_template($template, $_rep = null, $directory = null, $disable_override = false)
{
    global $_conf, $_post, $_user, $_mods;
    // make sure we get smarty included
    if (!isset($GLOBALS['smarty_object']) || !is_object($GLOBALS['smarty_object'])) {

        if (!class_exists('Smarty')) {
            require_once APP_DIR . '/modules/jrCore/contrib/smarty/libs/Smarty.class.php';
        }
        if (isset($_conf['jrCore_active_skin']{1})) {
            $dir = $_conf['jrCore_active_skin'];
        }
        else {
            $dir = 'jrCore';
        }
        // Set our compile dir
        $GLOBALS['smarty_object']             = new Smarty;
        $GLOBALS['smarty_object']->compile_id = md5(APP_DIR);
        $GLOBALS['smarty_object']->setCompileDir(APP_DIR . '/data/cache/' . $dir);
        $GLOBALS['smarty_object']->merge_compiled_includes = true;

        // Get plugin directories
        $_dir = array(APP_DIR . '/modules/jrCore/contrib/smarty/libs/plugins');
        $GLOBALS['smarty_object']->setPluginsDir($_dir);

        // If we are running in developer mode, make sure compiled template is removed on every call
        if (jrCore_is_developer_mode()) {
            $GLOBALS['smarty_object']->error_reporting = (E_ALL ^ E_NOTICE);
            $GLOBALS['smarty_object']->force_compile   = true;
        }
    }
    else {
        $GLOBALS['smarty_object']->clearAllAssign();
    }

    // Our template directory
    if (is_null($directory)) {
        $directory = $_conf['jrCore_active_skin'];
    }

    // Our Data
    $_data = array();
    if ($_rep && is_array($_rep)) {
        $_data = $_rep;
    }
    $_data['page_title']            = jrCore_get_flag('jrcore_html_page_title');
    $_data['jamroom_dir']           = APP_DIR;
    $_data['jamroom_url']           = $_conf['jrCore_base_url'];
    $_data['current_url']           = jrCore_get_current_url();
    $_data['_conf']                 = $_conf;
    $_data['_post']                 = $_post;
    $_data['_mods']                 = $_mods;
    $_data['_user']                 = (isset($_SESSION)) ? $_SESSION : $_user;
    $_data['jr_template']           = $template;
    $_data['jr_template_directory'] = $directory;
    $_data['jr_template_no_ext']    = str_replace('.tpl', '', $template);

    jrCore_set_flag('jrcore_parse_template_active_template', $template);
    jrCore_set_flag('jrcore_parse_template_active_directory', $directory);

    // Remove User and MySQL info - we don't want this to ever leak into a template
    unset($_data['_user']['user_password'], $_data['_user']['user_old_password'], $_data['_user']['user_forgot_key']);
    unset($_data['_conf']['jrCore_db_host'], $_data['_conf']['jrCore_db_user'], $_data['_conf']['jrCore_db_pass'], $_data['_conf']['jrCore_db_name'], $_data['_conf']['jrCore_db_port']);

    if (strpos($template, '.tpl') && jrCore_checktype($template, 'file_name')) {
        $file = jrCore_get_template_file($template, $directory, false, $disable_override);
        $tkey = "{$directory}_{$template}";
    }
    else {
        $file = 'string:' . $template;
        $tkey = md5($template);
    }
    $_data['jr_template_full_path'] = $file;

    // Lastly, see if we have already shown this template in this process
    $_data['template_already_shown'] = '1';
    if (!jrCore_get_flag("template_shown_{$tkey}")) {
        jrCore_set_flag("template_shown_{$tkey}", 1);
        $_data['template_already_shown'] = '0';
    }

    // Trigger for additional replacement vars
    $_data = jrCore_trigger_event('jrCore', 'template_variables', $_data, $_rep);

    // Take care of additional page elements in meta/footer
    switch ($template) {
        case 'meta.tpl':
        case 'footer.tpl':
            $_tmp = jrCore_get_flag('jrcore_page_elements');
            if ($_tmp) {
                $_data = array_merge($_data, $_tmp);
            }
            break;
    }

    $GLOBALS['smarty_object']->assign($_data);
    try {
        ob_start();
        $GLOBALS['smarty_object']->display($file); // DO NOT USE fetch() here!
        $out = ob_get_clean();
    }
    catch (Exception $e) {

        // No corruption on string: templates
        if (strpos($file, 'string:') !== 0) {

            // Rebuild this template
            $GLOBALS['smarty_object']->force_compile = true;
            $GLOBALS['smarty_object']->clearCache($file);
            $GLOBALS['smarty_object']->clearCompiledTemplate($file);

            // Delete the existing template
            $cdr = jrCore_get_module_cache_dir('jrCore');
            $tpl = basename($file);
            if (is_file("{$cdr}/{$tpl}")) {
                unlink("{$cdr}/{$tpl}");
            }

            // Rebuild template
            $file = jrCore_get_template_file($template, $directory, false, $disable_override);

            try {
                ob_start();
                $GLOBALS['smarty_object']->display($file);
                $out = ob_get_clean();
            }
            catch (Exception $e) {
                jrCore_logger('MAJ', "error rebuilding corrupt template file: {$tpl}");
                $out = '';
            }
            if (strlen($out) > 0) {
                if (!jrCore_get_flag("jrcore_corrupt_{$tpl}")) {
                    jrCore_logger('MAJ', "deleted and rebuilt corrupt template file: {$tpl}");
                    jrCore_set_flag("jrcore_corrupt_{$tpl}", 1);
                }
            }
            $GLOBALS['smarty_object']->force_compile = false;

        }
        else {
            $out = '';
        }
    }
    return jrCore_trigger_event('jrCore', 'parsed_template', $out, $_data);
}

/**
 * Returns the proper template to use for display.  Will also create/maintain the template cache
 * @param string $template Template file to get
 * @param string $directory Name of module or skin that the template belongs to
 * @param bool $reset Set to TRUE to reset the template cache
 * @param bool $disable_override Set to TRUE to disable Skin template override of module template
 * @return mixed Returns full file path on success, bool false on failure
 */
function jrCore_get_template_file($template, $directory, $reset = false, $disable_override = false)
{
    global $_conf;
    // Check for skin override
    if (!$disable_override && is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$directory}_{$template}")) {
        $template  = "{$directory}_{$template}";
        $directory = $_conf['jrCore_active_skin'];
    }
    if (is_null($directory) || $directory === false || strlen($directory) === 0) {
        $directory = $_conf['jrCore_active_skin'];
    }

    // Trigger template event
    $_tmp = array(
        'template'  => $template,
        'directory' => $directory
    );
    $_tmp = jrCore_trigger_event('jrCore', 'template_file', $_tmp);
    if (isset($_tmp['template']{0}) && $_tmp['template'] != $template || isset($_tmp['directory']) && $_tmp['directory'] != $directory) {
        $template  = $_tmp['template'];
        $directory = $_tmp['directory'];
    }

    // We check for our "cached" template, as that will be the proper one to display
    // depending on if the admin has customized the template or not.  If we do NOT
    // have the template in our cache, we have to go get it.
    $cdir = jrCore_get_module_cache_dir('jrCore');
    $hash = md5($_conf['jrCore_active_skin'] . '-' . $directory . '-' . $template);
    $file = "{$cdir}/{$hash}^{$directory}^{$template}";
    if (!is_file($file) || $reset || $_conf['jrCore_default_cache_seconds'] == '0' || jrCore_is_developer_mode()) {

        $_rt = false;
        if (!isset($_conf['jrCore_disable_db_templates'])) {
            $_rt = jrCore_get_flag("jrcore_get_template_cache");
            if (!$_rt) {
                // We need to check for a customized version of this template
                $tbl = jrCore_db_table_name('jrCore', 'template');
                $req = "SELECT CONCAT_WS('_',template_module,template_name) AS template_name, template_body FROM {$tbl} WHERE template_active = '1'";
                $_rt = jrCore_db_query($req, 'template_name');
                if ($_rt && is_array($_rt)) {
                    jrCore_set_flag('jrcore_get_template_cache', $_rt);
                }
                else {
                    jrCore_set_flag('jrcore_get_template_cache', 1);
                }
            }
        }
        $key = "{$directory}_{$template}";
        if ($_rt && is_array($_rt) && isset($_rt[$key]) && isset($_rt[$key]['template_body']{0})) {
            if (!jrCore_write_to_file($file, $_rt[$key]['template_body'])) {
                jrCore_notice('Error', "unable to write to template cache directory: data/cache/jrCore", false);
            }
        }
        // Check for skin template
        elseif (is_file(APP_DIR . "/skins/{$directory}/{$template}")) {
            if (!copy(APP_DIR . "/skins/{$directory}/{$template}", $file)) {
                jrCore_notice('Error', "unable to copy skins/{$directory}/{$template} to template cache directory: data/cache/jrCore", false);
            }
        }
        // Module template
        elseif (is_file(APP_DIR . "/modules/{$directory}/templates/{$template}")) {
            if (!copy(APP_DIR . "/modules/{$directory}/templates/{$template}", $file)) {
                jrCore_notice('Error', "unable to copy modules/{$directory}/templates/{$template} to template cache directory: data/cache/jrCore", false);
            }
        }
        else {
            $_tmp  = array(
                'template'  => $template,
                'directory' => $directory
            );
            $_data = jrCore_trigger_event('jrCore', 'tpl_404', $_tmp);
            if (!isset($_data['file']{1})) {
                jrCore_notice('Error', "invalid template: {$template}, or template directory: {$directory}", false);
            }
            if (!copy($_data['file'], $file)) {
                jrCore_notice('Error', "unable to copy " . str_replace(APP_DIR . '/', '', $_data['file']) . " to template cache directory: data/cache/jrCore", false);
            }
        }
    }
    return $file;
}

/**
 * Returns a 404 page not found
 * @return null
 */
function jrCore_page_not_found()
{
    global $_conf, $_post;
    jrCore_trigger_event('jrCore', '404_not_found', $_post);

    $_ln = jrUser_load_lang_strings();
    jrCore_page_title($_ln['jrCore'][84]);

    if (!$out = jrCore_is_cached('jrCore', '404_not_found_template')) {
        $out = jrCore_parse_template('404.tpl', array());
    }

    // Full page cache
    if (!jrUser_is_logged_in() && isset($_conf['jrCore_full_page']) && $_conf['jrCore_full_page'] == 'on') {
        if (!isset($_mods["{$_post['module']}"])) {
            jrCore_add_to_cache('jrCore', jrCore_get_full_page_cache_key(), $out, 0, 0, false, true);
        }
    }
    else {
        jrCore_add_to_cache('jrCore', '404_not_found_template', $out);
    }

    $out = jrCore_trigger_event('jrCore', 'view_results', $out);

    jrCore_set_custom_header('HTTP/1.0 404 Not Found');
    jrCore_set_custom_header('Connection: close');
    jrCore_set_custom_header("Content-Type: text/html; charset=utf-8");
    jrCore_send_response_and_detach($out, true);
    exit;
}

/**
 * Format Custom Skin CSS
 * @param $_custom array CSS Rules to format
 * @param $pretty bool set to TRUE to prettify
 * @return string
 */
function jrCore_format_custom_css($_custom, $pretty = false)
{
    $out = '';
    $chr = '';
    $spc = '';
    if ($pretty) {
        $chr = "\n";
        $spc = '    ';
    }
    if ($_custom && is_array($_custom)) {
        foreach ($_custom as $sel => $_rules) {
            if (strpos($sel, '@media') === 0) {
                $out .= $sel . " {{$chr}";
                foreach ($_rules as $ms => $_mr) {
                    $out .= $ms . " {{$chr}";
                    $_cr = array();
                    foreach ($_mr as $k => $v) {
                        if (!strpos($v, '!important')) {
                            $_cr[] = $k . ':' . $v . " !important;{$chr}";
                        }
                        else {
                            $_cr[] = $k . ':' . $v . $chr;
                        }
                    }
                    $out .= implode('', $_cr) . "}\n";
                }
                $out .= "}\n{$chr}";
            }
            else {
                $out .= $sel . " {{$chr}";
                $_cr = array();
                foreach ($_rules as $k => $v) {
                    if (!strpos($v, '!important')) {
                        $_cr[] = $spc . $k . ':' . $v . " !important;{$chr}";
                    }
                    else {
                        $_cr[] = $spc . $k . ':' . $v . $chr;
                    }
                }
                $out .= implode('', $_cr) . "}\n{$chr}";
            }
        }
    }
    return $out;
}

/**
 * Create a new master CSS files from module and skin CSS files
 * @param string $skin Skin to create CSS file for
 * @param bool $skip_trigger
 * @return string Returns MD5 checksum of CSS contents
 */
function jrCore_create_master_css($skin, $skip_trigger = false)
{
    global $_conf, $_mods;
    // Make sure we get a good skin
    if (!is_dir(APP_DIR . "/skins/{$skin}")) {
        return false;
    }
    $out = '';

    // First - round up any custom CSS from modules
    $_tm = jrCore_get_registered_module_features('jrCore', 'css');
    if ($_tm && is_array($_tm)) {
        foreach ($_tm as $mod => $_entries) {
            if (!jrCore_module_is_active($mod) || !is_dir(APP_DIR . "/modules/{$mod}")) {
                // Skin gets added below so it can override everything it needs
                continue;
            }
            foreach ($_entries as $script => $ignore) {
                if (strpos($script, 'http') === 0 || strpos($script, '//') === 0) {
                    continue;
                }
                if (strpos($script, APP_DIR) !== 0) {
                    $script = APP_DIR . "/modules/{$mod}/css/{$script}";
                }
                if (is_file(APP_DIR . "/skins/{$skin}/css/{$mod}_{$script}")) {
                    $script = APP_DIR . "/skins/{$skin}/css/{$mod}_{$script}";
                }
                // Developer mode OR already minimized
                if (strpos($script, '.min') || jrCore_is_developer_mode()) {
                    $out .= "\n/* " . str_replace(APP_DIR . '/', '', $script) . " */\n";
                    $out .= "\n\n" . @file_get_contents($script);
                }
                else {
                    $o    = false;
                    $_tmp = @file($script);
                    if ($_tmp && is_array($_tmp)) {
                        foreach ($_tmp as $line) {
                            $line = trim($line);
                            // check for start of comment
                            if (strpos($line, '/*') === 0 && !$o) {
                                if (!strpos(' ' . $line, '*/')) {
                                    // start of multi-line comment
                                    $o = true;
                                }
                                continue;
                            }
                            if ($o) {
                                // We're still in a comment - see if we are closing
                                if (strpos(' ' . $line, '*/')) {
                                    // Closed - continue
                                    $o = false;
                                }
                                continue;
                            }
                            elseif ($o && strpos(' ' . $line, '*/')) {
                                // Closing comment tag
                                continue;
                            }
                            if (strlen($line) > 0) {
                                $out .= $line;
                            }
                        }
                    }
                }
                $out .= "\n";
            }
        }
    }

    // Skin last (so it can override modules if needed)
    if (isset($_tm[$skin]) && is_array($_tm[$skin])) {
        foreach ($_tm[$skin] as $script => $ignore) {
            if (strpos($script, 'http') === 0 || strpos($script, '//') === 0) {
                // full URLs to external sources are handled at registration time
                continue;
            }
            if (strpos($script, APP_DIR) !== 0) {
                $script = APP_DIR . "/skins/{$skin}/css/{$script}";
            }
            if (jrCore_is_developer_mode()) {
                $out .= "\n/* " . str_replace(APP_DIR . '/', '', $script) . " */\n";
                $out .= "\n\n" . @file_get_contents($script);
            }
            else {
                $o    = false;
                $_tmp = @file($script);
                if ($_tmp && is_array($_tmp)) {
                    foreach ($_tmp as $line) {
                        $line = trim($line);
                        // check for start of comment
                        if (strpos($line, '/*') === 0 && !$o) {
                            if (!strpos(' ' . $line, '*/')) {
                                // start of multi-line comment
                                $o = true;
                            }
                            continue;
                        }
                        if ($o) {
                            // We're still in a comment - see if we are closing
                            if (strpos(' ' . $line, '*/')) {
                                // Closed - continue
                                $o = false;
                            }
                            continue;
                        }
                        elseif ($o && strpos(' ' . $line, '*/')) {
                            // Closing comment tag
                            continue;
                        }
                        if (strlen($line) > 0) {
                            $out .= $line;
                        }
                    }
                }
            }
            $out .= "\n";
        }
    }

    // Next, get our customized style from the database
    $tbl = jrCore_db_table_name('jrCore', 'skin');
    $req = "SELECT skin_custom_css FROM {$tbl} WHERE skin_directory = '" . jrCore_db_escape($skin) . "'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (isset($_rt['skin_custom_css']{1})) {
        $_custom = json_decode($_rt['skin_custom_css'], true);
        if ($_custom && is_array($_custom)) {
            $out .= jrCore_format_custom_css($_custom);
        }
    }

    $url = $_conf['jrCore_base_url'];
    $prt = jrCore_get_server_protocol();
    if ($prt && $prt === 'https') {
        $url = str_replace('http:', 'https:', $url);
    }
    // Save file
    $sum = md5($out);
    $_rp = array(
        '{$jamroom_url}' => $url,
        '/* @ignore */'  => '',
        ': '             => ':',
        ', '             => ','
    );
    $crl = jrCore_get_module_url('jrImage');
    foreach ($_tm as $mod => $_entries) {
        if (isset($_mods[$mod]['module_url'])) {
            $_rp['{$' . $mod . '_img_url}'] = "{$url}/{$crl}/img/module/{$mod}";
        }
        else {
            $_rp['{$' . $mod . '_img_url}'] = "{$url}/{$crl}/img/skin/{$mod}";
        }
    }
    $out = "/* {$_conf['jrCore_system_name']} css " . date('r') . " */\n" . str_replace(array_keys($_rp), $_rp, $out);
    $cdr = jrCore_get_module_cache_dir($skin);

    // Our SSL version of the CSS file is prefixed with an "S".
    if ($prt && $prt === 'https') {
        jrCore_write_to_file("{$cdr}/S{$sum}.css", $out, true);
    }
    else {
        jrCore_write_to_file("{$cdr}/{$sum}.css", $out, true);
    }

    // Trigger our CSS event
    if (!$skip_trigger) {
        jrCore_trigger_event('jrCore', 'create_master_css', $_tm);
    }

    // We need to store the MD5 of this file in the settings table - thus
    // we don't have to look it up on each page load, and we can then set
    // a VERSION on the css so our visitors will immediately see any CSS
    // changes without having to worry about a cached old version
    $_field = array(
        'name'     => "{$skin}_css_version",
        'type'     => 'hidden',
        'validate' => 'md5',
        'value'    => $sum,
        'default'  => $sum
    );
    jrCore_update_setting('jrCore', $_field);
    return $sum;
}

/**
 * Create the site level User and Admin javascript
 * @param string $skin Skin to create Javascript file for
 * @param bool $skip_trigger
 * @return string Returns MD5 checksum of Javascript contents
 */
function jrCore_create_master_javascript($skin, $skip_trigger = false)
{
    global $_conf, $_urls;
    // Make sure we get a good skin
    if (!is_dir(APP_DIR . "/skins/{$skin}")) {
        return false;
    }

    // Create our output
    $kurl = $_conf['jrCore_base_url'];
    $kprt = jrCore_get_server_protocol();
    if ($kprt && $kprt === 'https') {
        $kurl = str_replace('http:', 'https:', $kurl);
    }
    $top = "var jrImage_url='" . jrCore_get_module_url('jrImage') . "';\n";
    $out = '';
    $min = '';
    $adm = '';

    // We keep track of the MP5 hash of every JS script we include - this
    // keeps us from including the same JS from different modules
    $_hs = array();
    $_as = array();

    // First - round up any custom JS from modules
    $_tm = jrCore_get_registered_module_features('jrCore', 'javascript');
    // Add in custom module javascript
    if ($_tm && is_array($_tm)) {
        $_ur = array_flip($_urls);
        $_dn = array();
        foreach ($_tm as $mod => $_entries) {
            if ($mod == $skin || !jrCore_module_is_active($mod)) {
                continue;
            }
            if (isset($_ur[$mod])) {
                $url = $_ur[$mod];
                if (!isset($_dn[$url])) {
                    $top       .= "var {$mod}_url='{$url}';\n";
                    $_dn[$url] = 1;
                }
            }
        }
        foreach ($_tm as $mod => $_entries) {
            if ($mod == $skin || !jrCore_module_is_active($mod)) {
                continue;
            }
            foreach ($_entries as $script => $group) {
                // NOTE: Javascript that is external the JR system is loaded in the jrCore_enable_external_javascript() function
                if (strpos($script, 'http') === 0 || strpos($script, '//') === 0 || intval($script) === 1) {
                    continue;
                }
                if (strpos($script, APP_DIR) !== 0) {
                    $script = APP_DIR . "/modules/{$mod}/js/{$script}";
                }
                if (is_file(APP_DIR . "/skins/{$skin}/js/{$mod}_{$script}")) {
                    $script = APP_DIR . "/skins/{$skin}/js/{$mod}_{$script}";
                }
                $tmp = @file_get_contents($script);
                // This MD5 check ensures we don't include the same JS script 2 times from different modules
                $key = md5($tmp);

                if (!strpos($script, '.min')) {
                    if ($group === 'admin') {
                        if (!isset($_as[$key]) && !isset($_hs[$key])) {
                            $adm       .= $tmp . "\n";
                            $_as[$key] = 1;
                        }
                    }
                    else {
                        if (!isset($_hs[$key])) {
                            $out       .= $tmp . "\n";
                            $_hs[$key] = 1;
                        }
                    }
                }
                else {
                    if ($group === 'admin') {
                        if (!isset($_as[$key]) && !isset($_hs[$key])) {
                            $adm       .= $tmp . "\n";
                            $_as[$key] = 1;
                        }
                    }
                    else {
                        if (!isset($_hs[$key])) {
                            $min       .= $tmp . "\n";
                            $_hs[$key] = 1;
                        }
                    }
                }

            }
        }
    }

    // Skin last (so it can override modules if needed)
    if (isset($_tm[$skin]) && is_array($_tm[$skin])) {
        foreach ($_tm[$skin] as $script => $group) {
            if (strpos($script, 'http') === 0 || strpos($script, '//') === 0) {
                continue;
            }
            if (strpos($script, APP_DIR) !== 0) {
                $script = APP_DIR . "/skins/{$skin}/js/{$script}";
            }
            $tmp = @file_get_contents($script);
            $key = md5($tmp);
            if (!strpos($script, '.min')) {
                if ($group === 'admin') {
                    if (!isset($_as[$key]) && !isset($_hs[$key])) {
                        $adm       .= $tmp . "\n";
                        $_as[$key] = 1;
                    }
                }
                else {
                    if (!isset($_hs[$key])) {
                        $out       .= $tmp . "\n";
                        $_hs[$key] = 1;
                    }
                }
            }
            else {
                if ($group === 'admin') {
                    if (!isset($_as[$key]) && !isset($_hs[$key])) {
                        $adm       .= $tmp . "\n";
                        $_as[$key] = 1;
                    }
                }
                else {
                    if (!isset($_hs[$key])) {
                        $min       .= $tmp . "\n";
                        $_hs[$key] = 1;
                    }
                }
            }
        }
    }

    // Save file
    $cdr = jrCore_get_module_cache_dir($skin);
    $sum = md5($top . $min . $adm . $out);

    if (!jrCore_is_developer_mode()) {
        // Compress $out
        require_once APP_DIR . '/modules/jrCore/contrib/jsmin/jsmin.php';
        $out = JSMin::minify($out);
        if (strlen($adm) > 0) {
            $adm = JSMin::minify($adm);
        }
    }
    $out = "/* {$_conf['jrCore_system_name']} */\nvar core_system_url='{$kurl}';\nvar core_active_skin='{$skin}';\n{$top}\n{$min}\n{$out}";

    if ($kprt && $kprt === 'https') {
        jrCore_write_to_file("{$cdr}/S{$sum}.js", $out, true);
    }
    else {
        jrCore_write_to_file("{$cdr}/{$sum}.js", $out, true);
    }

    if (strlen($adm) > 0) {
        if ($kprt && $kprt === 'https') {
            jrCore_write_to_file("{$cdr}/S{$sum}-admin.js", $adm, true);
        }
        else {
            jrCore_write_to_file("{$cdr}/{$sum}-admin.js", $adm, true);
        }
    }

    // Trigger our CSS event
    if (!$skip_trigger) {
        jrCore_trigger_event('jrCore', 'create_master_javascript', $_tm);
    }

    // We need to store the MD5 of this file in the settings table - thus
    // we don't have to look it up on each page load, and we can then set
    // a VERSION on the js so our visitors will immediately see any JS
    // changes without having to worry about a cached old version
    $_field = array(
        'name'     => "{$skin}_javascript_version",
        'type'     => 'hidden',
        'validate' => 'md5',
        'value'    => $sum,
        'default'  => $sum
    );
    jrCore_update_setting('jrCore', $_field);
    return $sum;
}

/**
 * Return "order" button for item index
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return string
 */
function jrCore_item_order_button($module, $_item, $_args, $smarty, $test_only = false)
{
    // See if this module has registered for item order support
    $_tm = jrCore_get_registered_module_features('jrCore', 'item_order_support');
    if (!isset($_tm[$module])) {
        return false;
    }
    if ($test_only) {
        return true;
    }
    $_args['module'] = $module;
    return smarty_function_jrCore_item_order_button($_args, $smarty);
}

/**
 * Return "create" button for an item
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return string
 */
function jrCore_item_create_button($module, $_item, $_args, $smarty, $test_only = false)
{
    if ($test_only) {
        return true;
    }
    $_args['module'] = $module;
    if (!isset($_args['profile_id'])) {
        $_args['profile_id'] = $_item['_profile_id'];
    }
    return smarty_function_jrCore_item_create_button($_args, $smarty);
}

/**
 * Return "create" button for a bundle
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return string
 */
function jrCore_bundle_create_button($module, $_item, $_args, $smarty, $test_only = false)
{
    if ($test_only) {
        return true;
    }
    $_args['module'] = $module;
    return smarty_function_jrCore_item_create_button($_args, $smarty);
}

/**
 * Return "update" button for the item
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return string
 */
function jrCore_item_update_button($module, $_item, $_args, $smarty, $test_only = false)
{
    if ($test_only) {
        return true;
    }
    $_args['module']     = $module;
    $_args['profile_id'] = $_item['_profile_id'];
    $_args['item_id']    = $_item['_item_id'];
    return smarty_function_jrCore_item_update_button($_args, $smarty);
}

/**
 * Return "update" button for a bundle
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return string
 */
function jrCore_bundle_update_button($module, $_item, $_args, $smarty, $test_only = false)
{
    if ($test_only) {
        return true;
    }
    $_args['module'] = $module;
    return smarty_function_jrCore_item_update_button($_args, $smarty);
}

/**
 * Return "delete" button for the item
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return string
 */
function jrCore_item_delete_button($module, $_item, $_args, $smarty, $test_only = false)
{
    if ($test_only) {
        return true;
    }
    $_args['module']     = $module;
    $_args['profile_id'] = $_item['_profile_id'];
    $_args['item_id']    = $_item['_item_id'];
    return smarty_function_jrCore_item_delete_button($_args, $smarty);
}

/**
 * Return "delete" button for a bundle
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args array Smarty function parameters
 * @param $smarty object Smarty Object
 * @param $test_only bool check if button WOULD be shown for given module
 * @return string
 */
function jrCore_bundle_delete_button($module, $_item, $_args, $smarty, $test_only = false)
{
    if ($test_only) {
        return true;
    }
    $_args['module'] = $module;
    return smarty_function_jrCore_item_delete_button($_args, $smarty);
}

/**
 * Test a smarty template for errors
 * @param string $module
 * @param string $content
 * @return bool|string
 */
function jrCore_test_template_for_errors($module, $content)
{
    global $_conf;
    // We need to test this template and make sure it does not cause any Smarty errors
    $key = jrCore_create_unique_string(8);
    jrCore_set_temp_value('jrCore', "{$key}_template", $content);
    $url = jrCore_get_module_url('jrCore');
    $out = jrCore_load_url("{$_conf['jrCore_base_url']}/{$url}/test_template/{$key}/{$module}");
    jrCore_delete_temp_value('jrCore', "{$key}_template");
    if ($out && strlen($out) > 1 && (strpos($out, 'error:') === 0 || stristr($out, 'fatal error') || stristr($out, 'Smarty Compiler:'))) {
        // SmartyCompilerException: Syntax error in template "file:/.../1480710660.tpl"  on line 181 "" unclosed {if} tag in /modules/jrCore/contrib/smarty/libs/sysplugins/smarty_internal_templatecompilerbase.php on line 181
        $_ad = array();
        $_tm = explode("\n", $out);
        if ($_tm && is_array($_tm)) {
            foreach ($_tm as $line) {
                if (strpos($line, $key)) {
                    $_rp  = array(
                        'SmartyCompilerException:',
                        'file:' . APP_DIR . '/',
                        '""'
                    );
                    $line = str_replace($_rp, '', $line);
                    list($line,) = explode(APP_DIR, $line);
                    $_ad[] = rtrim(trim($line), 'in');
                }
            }
        }
        if (count($_ad) > 0) {
            return 'error: There are syntax error(s) in your template - please fix and try again:<br>' . jrCore_strip_html(implode('<br>', $_ad));
        }
        // We don't know what the error is
        return 'error: There is a syntax error in your template - please fix and try again';
    }
    return true;
}
