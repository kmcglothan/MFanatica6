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
 * @package Media and File
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

// FORCE_LOCAL - flag used to indicate that the media function should reference
// the local file system regardless of the configured active media system plugin
define('FORCE_LOCAL', true);

/**
 * Return TRUE if module is a "media" module (accepts file uploads)
 * @param $module string module to check
 * @return bool
 */
function jrCore_is_media_module($module)
{
    return (jrCore_is_datastore_module($module) && is_file(APP_DIR . "/modules/{$module}/templates/urlscan_player.tpl")) ? true : false;
}

/**
 * Generate a unique string of X length
 * @param $length int Length of code to generate
 * @return string
 */
function jrCore_create_unique_string($length)
{
    $chr = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    $len = strlen($chr) - 1;
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= substr($chr, mt_rand(0, $len), 1);
    }
    return $key;
}

/**
 * Delete old upload directories
 * @param int $old_hours hours without update to be deleted
 * @return int returns number of directories removed
 */
function jrCore_delete_old_upload_directories($old_hours = 4)
{
    $rem = 0;
    $old = (int) $old_hours;
    if ($old < 1) {
        $old = 1;
    }
    $dir = jrCore_get_module_cache_dir('jrCore');
    if ($h = opendir(realpath($dir))) {
        while (false !== ($file = readdir($h))) {
            if (is_dir("{$dir}/{$file}") && strlen($file) === 32 && jrCore_checktype($file, 'md5')) {
                // This is an upload temp directory - delete it if it has been here for over 4 hours
                if (filemtime("{$dir}/{$file}") < (time() - ($old * 86400))) {
                    jrCore_delete_dir_contents("{$dir}/{$file}");
                    rmdir("{$dir}/{$file}");
                    $rem++;
                }
            }
        }
        closedir($h);
    }
    return $rem;
}

/**
 * Cleanup old Minute Maintenance lock files
 * @return bool
 */
function jrCore_delete_old_maintenance_lock_files()
{
    $dir = jrCore_get_module_cache_dir('jrCore');
    if ($_lk = glob("{$dir}/minute_maintenance*.lock", GLOB_NOSORT)) {
        $old = (time() - 180);  // 3 minutes or older
        foreach ($_lk as $file) {
            if (filemtime($file) < $old) {
                unlink($file);  // OK
            }
        }
    }
    if ($_lk = glob("{$dir}/hourly_maintenance*.lock", GLOB_NOSORT)) {
        $old = (time() - 10800); // 3 hours or older
        foreach ($_lk as $file) {
            if (filemtime($file) < $old) {
                unlink($file);  // OK
            }
        }
    }
    if ($_lk = glob("{$dir}/daily_maintenance*.lock", GLOB_NOSORT)) {
        $old = (time() - (3 * 86400));  // 3 days or older
        foreach ($_lk as $file) {
            if (filemtime($file) < $old) {
                unlink($file);  // OK
            }
        }
    }
    return true;
}

/**
 * Delete debug and error logs not written to in 3 days
 */
function jrCore_delete_old_error_and_debug_logs()
{
    $old = (time() - 259200);
    foreach (array('error_log', 'debug_log') as $log) {
        $log_file = APP_DIR . "/data/logs/{$log}";
        if (is_file($log_file) && filemtime($log_file) < $old) {
            unlink($log_file);  // OK
        }
    }
    return true;
}

/**
 * Delete debug and error logs not written to in 3 days
 */
function jrCore_delete_old_test_templates()
{
    $dir = jrCore_get_module_cache_dir('jrCore');
    if ($_tpl = glob("{$dir}/test_template_*.tpl", GLOB_NOSORT)) {
        $old = (time() - 30);  // 30 seconds
        foreach ($_tpl as $file) {
            if (filemtime($file) < $old) {
                unlink($file);  // OK
            }
        }
    }
    return true;
}

/**
 * Delete old datastore export CSV files
 */
function jrCore_delete_old_datastore_csv_files()
{
    $dir = jrCore_get_module_cache_dir('jrCore');
    $_ex = glob("{$dir}/*_datastore_*.csv", GLOB_NOSORT);
    if ($_ex && is_array($_ex)) {
        $old = (time() - 7200);  // 2 hours
        foreach ($_ex as $file) {
            if (filemtime($file) < $old) {
                unlink($old); // OK
            }
        }
    }
}

/**
 * Return TRUE if referrer is local or allowed
 * @return bool
 */
function jrCore_media_is_allowed_referrer_domain()
{
    global $_conf;
    if (isset($_SERVER['HTTP_REFERER']{1}) && strpos($_SERVER['HTTP_REFERER'], $_conf['jrCore_base_url']) === 0) {
        // We are a LOCAL referrer
        return true;
    }
    if (isset($_conf['jrCore_allowed_domains']{0}) && strpos(' ' . $_conf['jrCore_allowed_domains'], 'ALLOW_ALL_DOMAINS')) {
        // We allow EVERYTHING
        return true;
    }
    if (isset($_SERVER['HTTP_REFERER']{1})) {
        // We are not local - check for allowed domains
        if (isset($_conf['jrCore_allowed_domains']{0})) {
            $domain = str_replace('www.', '', parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST));
            if (strpos(' ' . $_conf['jrCore_allowed_domains'], $domain)) {
                return true;
            }
        }
    }
    // Let other modules check for allowed
    $_data = array(
        'referrer' => (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : false
    );
    $_data = jrCore_trigger_event('jrCore', 'media_allowed_referrer', $_data);
    if (isset($_data['allowed_domain']) && $_data['allowed_domain'] === true) {
        return true;
    }
    return false;
}

//---------------------------
// PLAY KEYS
//---------------------------

/**
 * Get the active form session plugin
 * @note: form_sessions follow the active user session plugin
 * @return string
 */
function jrCore_get_active_play_key_system()
{
    global $_conf;
    if (isset($_conf['jrUser_active_session_system']{1})) {
        // Make sure it is valid...
        $func = "_{$_conf['jrUser_active_session_system']}_media_set_play_key";
        if (function_exists($func)) {
            return $_conf['jrUser_active_session_system'];
        }
    }
    return 'jrCore_mysql';
}

/**
 * Get a previously saved Play Key
 * @param $key string
 * @return bool|mixed
 */
function jrCore_media_get_play_key($key)
{
    $temp = jrCore_get_active_play_key_system();
    $func = "_{$temp}_media_get_play_key";
    if (function_exists($func)) {
        return $func($key);
    }
    return _jrCore_mysql_media_get_play_key($key);
}

/**
 * Add a session based media key
 * @param $page string HTML contents of page
 * @return bool
 */
function jrCore_media_set_play_key($page)
{
    global $_conf, $_post;
    if (strpos($page, '[jrCore_media_play_key]')) {
        if (strpos(' ' . $_conf['jrCore_allowed_domains'], 'ALLOW_ALL_DOMAINS')) {
            // We are allowing ALL DOMAINS - no need to enforce play keys
            $page = str_replace('[jrCore_media_play_key]', '1', $page);
        }
        else {
            // See if we are being excluded on this view
            $_tmp = jrCore_get_registered_module_features('jrCore', 'skip_play_keys');
            if ($_tmp && is_array($_tmp)) {
                foreach ($_tmp as $mod => $_opts) {
                    if (isset($_post['option']) && isset($_opts["{$_post['option']}"]) && ($mod == $_post['module'] || $_opts["{$_post['option']}"] == 'magic_view')) {
                        // We are excluded...
                        return $page;
                    }
                }
            }
            $pkey = jrCore_create_unique_string(8);
            $temp = jrCore_get_active_play_key_system();
            $func = "_{$temp}_media_set_play_key";
            if (function_exists($func)) {
                $kexp = 8;
                if (isset($_conf['jrCore_play_key_expire_hours']) && jrCore_checktype($_conf['jrCore_play_key_expire_hours'], 'number_nz')) {
                    $kexp = (int) $_conf['jrCore_play_key_expire_hours'];
                }
                $func($pkey, $kexp);
            }
            else {
                _jrCore_mysql_media_set_play_key($pkey);
            }
            $page = str_replace('[jrCore_media_play_key]', $pkey, $page);
        }
    }
    return $page;
}

/**
 * Run play key maintenance
 * @return bool|mixed
 */
function jrCore_media_play_key_maintenance()
{
    global $_conf;
    $kexp = 8;
    if (isset($_conf['jrCore_play_key_expire_hours']) && jrCore_checktype($_conf['jrCore_play_key_expire_hours'], 'number_nz')) {
        $kexp = (int) $_conf['jrCore_play_key_expire_hours'];
    }
    $temp = jrCore_get_active_play_key_system();
    $func = "_{$temp}_media_play_key_maintenance";
    if (function_exists($func)) {
        return $func($kexp);
    }
    return _jrCore_mysql_media_play_key_maintenance($kexp);
}

/**
 * Get a previously saved Play Key
 * @param string $key
 * @return bool|mixed
 */
function _jrCore_mysql_media_get_play_key($key)
{
    $tbl = jrCore_db_table_name('jrCore', 'play_key');
    $req = "SELECT key_time FROM {$tbl} WHERE key_code = '" . jrCore_db_escape($key) . "'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        return true;
    }
    return false;
}

/**
 * Add a session based media key
 * @param string $key
 * @return bool
 */
function _jrCore_mysql_media_set_play_key($key)
{
    $tbl = jrCore_db_table_name('jrCore', 'play_key');
    $req = "INSERT INTO {$tbl} (key_time, key_code) VALUES (UNIX_TIMESTAMP(), '{$key}') ON DUPLICATE KEY UPDATE key_time = UNIX_TIMESTAMP()";
    if (jrCore_db_query($req, 'INSERT_ID') > 0) {
        return true;
    }
    return false;
}

/**
 * Cleanup play keys
 * @param int $hours
 * @return bool
 */
function _jrCore_mysql_media_play_key_maintenance($hours)
{
    // Delete old play keys
    $hrs = (int) $hours;
    $tbl = jrCore_db_table_name('jrCore', 'play_key');
    $req = "SELECT key_id FROM {$tbl} WHERE key_time < (UNIX_TIMESTAMP() - ({$hrs} * 3600))";
    $_rt = jrCore_db_query($req, 'key_id', false, 'key_id');
    if ($_rt && is_array($_rt)) {
        $req = "DELETE FROM {$tbl} WHERE key_id IN(" . implode(',', $_rt) . ')';
        jrCore_db_query($req);
    }
    return true;
}

//---------------------------

/**
 * Get list of registered Media players by type
 * @param $type string one of: audio|video|mixed
 * @return array|bool
 */
function jrCore_get_registered_media_players($type)
{
    $_tmp = jrCore_get_registered_module_features('jrCore', 'media_player');
    if (!$_tmp || !is_array($_tmp)) {
        return false;
    }
    $_out = array();
    foreach ($_tmp as $module => $_players) {
        foreach ($_players as $pname => $ptype) {
            if ($ptype == $type || $type == 'all') {
                $_out[$pname] = $module;
            }
        }
    }
    if (count($_out) > 0) {
        return $_out;
    }
    return false;
}

/**
 * Get media player options registered by modules
 * @param string $type
 * @return array|bool
 */
function jrCore_get_media_player_options($type = 'all')
{
    if ($_pl = jrCore_get_registered_media_players($type)) {
        $_tm = array();
        foreach ($_pl as $n => $p) {
            $player       = trim(str_replace($p, '', $n), '_');
            $_tm[$player] = ucwords(str_replace('_', ' ', $player));
        }
        return $_tm;
    }
    return false;
}

/**
 * Uses FFMpeg to retrieve information about audio and video files
 * @param string $file File to get data for
 * @param string $field_prefix Prefix for return array keys
 * @return mixed
 */
function jrCore_get_media_file_metadata($file, $field_prefix)
{
    $ffmpeg = jrCore_get_tool_path('ffmpeg', 'jrCore');
    if (!is_file($ffmpeg)) {
        jrCore_logger('CRI', 'required ffmpeg binary not found in modules/jrCore/tools');
        return false;
    }
    if (!is_executable($ffmpeg)) {
        jrCore_logger('CRI', 'ffmpeg binary in modules/jrCore/tools is not executable!');
        return false;
    }
    $dir = jrCore_get_module_cache_dir('jrCore');
    $tmp = tempnam($dir, 'media_meta_');

    // Audio (WMA)
    // Duration: 00:03:15.0, start: 1.579000, bitrate: 162 kb/s
    // Stream #0.0: Audio: wmav2, 44100 Hz, stereo, 160 kb/s

    // Audio (MP3)
    // Duration: 00:02:17.3, bitrate: 191 kb/s
    // Stream #0.0: Audio: mp3, 44100 Hz, stereo, 192 kb/s

    // Audio (M4A)
    // Duration: 00:00:28.61, start: 0.023220, bitrate: 117 kb/s
    // Stream #0:0(eng): Audio: aac (mp4a / 0x6134706D), 44100 Hz, stereo, s16, 116 kb/s

    // Audio (FLAC)
    // Duration: N/A, bitrate: N/A
    // Duration: 00:05:27.76, start: 0.000000, bitrate: 728 kb/s
    // Stream #0.0: Audio: flac, 44100 Hz, stereo

    // Audio (OGG)
    // Duration: 00:00:27.9, start: 0.686440, bitrate: 91 kb/s
    // Stream #0.0: Audio: vorbis, 44100 Hz, stereo, 96 kb/s

    // Video (WMV)
    // Duration: 00:02:39.9, start: 4.000000, bitrate: 913 kb/s
    // Stream #0.0: Audio: wmav2, 48000 Hz, stereo, 128 kb/s
    // Stream #0.1: Video: wmv3, yuv420p, 640x512, 766 kb/s, 25.00 fps(r)

    // Video MOV (with unsupported M4A audio)
    // Duration: 00:01:19.6, start: 0.000000, bitrate: 1288 kb/s
    // Stream #0.0(eng): Video: h264, yuv420p, 640x368 [PAR 0:1 DAR 0:1], 25.00 tb(r)
    // Stream #0.1(eng): Audio: mp4a / 0x6134706D, 11025 Hz, mono

    // Video (FLV)
    // Duration: 08:25:32.0, start: 0.000000, bitrate: 64 kb/s
    // Stream #0.0: Video: flv, yuv420p, 320x240, 25.00 fps(r)
    // Stream #0.1: Audio: mp3, 22050 Hz, stereo, 64 kb/s

    // Stream #0:0(eng): Video: mpeg4 (Simple Profile) (mp4v / 0x7634706D), yuv420p, 176x144 [SAR 1:1 DAR 11:9], 122 kb/s, 29.97 fps, 29.97 tbr, 90k tbn, 30k tbc

    // Metadata:
    //  encoder         : Audiograbber 1.81.03, LAME dll 3.92, 160 Kbit/s, Joint Stereo, Normal quality
    //  title           : Pastichio Medley
    //  artist          : Smashing Pumpkins
    //  publisher       : Hut
    //  genre           : Rock
    //  album           : The Aeroplane Flies High - Zero
    //  track           : 7
    //  album_artist    : Smashing Pumpkins
    //  composer        : Billy Corgan
    //  date            : 1996
    // Duration: 00:23:00.44, start: 0.000000, bitrate: 160 kb/s

    ob_start();
    $file = str_replace('"', '\"', $file);
    system("nice -n 9 {$ffmpeg} -analyzeduration 30000000 -probesize 30000000 -i \"{$file}\" >/dev/null 2>{$tmp}", $ret);
    ob_end_clean();

    $_out = array();
    $meta = false;
    if ($tmp && is_file($tmp)) {
        $_tmp = file($tmp);
        if ($_tmp && is_array($_tmp)) {
            foreach ($_tmp as $line) {
                $line = trim($line);
                if (strpos($line, 'Duration:') === 0) {
                    $meta = false;
                    // Duration: 00:07:21.18, start: 0.000000, bitrate: 128 kb/s
                    $length = jrCore_string_field($line, 2);
                    if (strpos($length, '.')) {
                        list($sec,) = explode('.', $length, 2);
                        if (strlen($sec) >= 8) {
                            $length = $sec;
                        }
                    }
                    else {
                        $length = substr($length, 0, 8);
                    }
                    $_out["{$field_prefix}_length"] = $length;

                    // FLAC's bitrate will only be found on the duration line
                    // Duration: 00:05:27.76, bitrate: 728 kb/s
                    // Get bitrate
                    $bitrate = false;
                    $prv     = false;
                    foreach (explode(' ', $line) as $prt) {
                        if (strtolower($prt) == 'kb/s') {
                            $bitrate = (int) $prv;
                            break;
                        }
                        $prv = $prt;
                    }
                    if (jrCore_checktype($bitrate, 'number_nz')) {
                        $_out["{$field_prefix}_bitrate"] = (int) $bitrate;
                    }
                }
                elseif (strpos($line, 'Stream') === 0 && strpos($line, 'Audio') && !isset($save)) {
                    $meta = false;
                    $line = trim(str_replace(array('(default)', ','), '', $line));
                    // Stream #0:0: Audio: mp3, 44100 Hz, stereo, s16, 128 kb/s
                    // Stream #0:0(eng): Audio: aac (mp4a / 0x6134706D), 44100 Hz, stereo, fltp, 116 kb/s (default)

                    // Get bitrate
                    $bitrate = false;
                    $prv     = false;
                    foreach (explode(' ', $line) as $prt) {
                        if (strtolower($prt) == 'kb/s') {
                            $bitrate = (int) $prv;
                            break;
                        }
                        $prv = $prt;
                    }
                    if (jrCore_checktype($bitrate, 'number_nz')) {
                        $_out["{$field_prefix}_bitrate"] = (int) $bitrate;
                    }
                    // Get smprate
                    $smprate = false;
                    $prv     = false;
                    foreach (explode(' ', $line) as $prt) {
                        if (strtolower($prt) == 'hz') {
                            $smprate = (int) $prv;
                            break;
                        }
                        $prv = $prt;
                    }
                    if (jrCore_checktype($smprate, 'number_nz')) {
                        $_out["{$field_prefix}_smprate"] = (int) $smprate;
                    }
                }
                elseif (strpos($line, 'Audio:') === 0 && !isset($save)) {
                    $meta = false;
                    // Stream #0:0: Audio: mp3, 44100 Hz, stereo, s16, 128 kb/s
                    $_out["{$field_prefix}_bitrate"] = (int) jrCore_string_field($line, -2);
                    $_out["{$field_prefix}_smprate"] = (int) jrCore_string_field($line, 5);
                }
                elseif (strpos($line, 'Video:') && !strpos($line, 'Video: png')) {
                    $meta = false;
                    $save = false;
                    // This is a video file - get our details
                    foreach (explode(' ', $line) as $word) {
                        if (strtolower($word) == 'kb/s,') {
                            $_out["{$field_prefix}_bitrate"] = $save;
                        }
                        elseif (strpos($word, 'x')) {
                            $_wrd = explode('x', $word);
                            if (count($_wrd) === 2 && (strlen($_wrd[0]) > 1 && strlen($_wrd[0]) < 5) && (strlen($_wrd[1]) > 1 && strlen($_wrd[1]) < 5)) {
                                $_out["{$field_prefix}_resolution"] = trim(str_replace(',', '', $word));
                            }
                        }
                        $save = $word;
                    }
                }
                elseif (strpos($line, 'Metadata:') === 0) {
                    $meta = true;
                }
                elseif ($meta && strpos($line, ':')) {
                    list($tag, $val) = explode(':', $line, 2);
                    $tag = strip_tags(trim($tag));
                    switch ($tag) {
                        case 'title':
                        case 'artist':
                        case 'composer':
                        case 'publisher':
                        case 'album':
                        case 'genre':
                        case 'date':
                            if (!isset($_out["{$field_prefix}_{$tag}"])) {
                                // Take the first one
                                $_out["{$field_prefix}_{$tag}"] = strip_tags(trim($val));
                            }
                            break;
                        case 'track':
                            // Our "track" becomes our order field used for
                            // ordering of items in albums.  Note that some track fields
                            // can contain a '/' - i.e. 5/12 - we only want the first
                            if (!isset($_out["{$field_prefix}_track"])) {
                                $val = trim(strip_tags($val));
                                if (strpos($val, '/')) {
                                    list($val,) = explode('/', $val, 2);
                                }
                                $_out["{$field_prefix}_track"] = intval($val);
                            }
                            break;
                    }
                }
            }
        }
    }
    @unlink($tmp); // OK
    return $_out;
}

/**
 * Get full path to a media file
 * @param $module string Module Name to save file for
 * @param $field_name string Unique File Name field
 * @param $_item array Array of item information from jrCore_db_get_item()
 * @return string
 */
function jrCore_get_media_file_path($module, $field_name, $_item)
{
    // We don't need a PLUGIN for this function - it works
    // because $dir will change depending on the media plugin!
    if (!isset($_item["{$field_name}_size"])) {
        return false;
    }
    $dir = jrCore_get_media_directory($_item['_profile_id']);
    return "{$dir}/{$module}_{$_item['_item_id']}_{$field_name}." . $_item["{$field_name}_extension"];
}

/**
 * Get full URL to a media file
 * @param $module string Module Name to save file for
 * @param $file_name string Unique File Name field
 * @param $_item array Array of item information from jrCore_db_get_item()
 * @return string
 */
function jrCore_get_media_file_url($module, $file_name, $_item)
{
    // NOTE: This function simply returns the URL - it is up to the
    // calling module to ensure the URL is actually accessible, as
    // the core places restrictions on direct access to media items
    if (!isset($_item["{$file_name}_size"])) {
        return false;
    }
    $url = jrCore_get_media_url($_item['_profile_id']);
    return "{$url}/{$module}_{$_item['_item_id']}_{$file_name}." . $_item["{$file_name}_extension"];
}

/**
 * Delete attached media file(s) for a given item_id
 * @note Despite the name, this function will remove ALL files for the given profile_id/item_id/file_name
 * @param string $module Module Name to save file for
 * @param string $file_name Name of file field in form
 * @param int $profile_id the Profile ID to save the media file for
 * @param int $unique_id Unique Item ID from DataStore
 * @param bool $item_check set to FALSE to not check that the DS item is deleted as well
 * @return bool
 */
function jrCore_delete_item_media_file($module, $file_name, $profile_id, $unique_id, $item_check = true)
{
    if (!jrCore_checktype($unique_id, 'number_nz')) {
        return false;
    }
    $type = jrCore_get_active_media_system();
    $func = "_{$type}_media_delete_file";
    if (function_exists($func)) {

        // Delete media file keys from DataStore
        if ($item_check) {
            $_it = jrCore_db_get_item($module, $unique_id, SKIP_TRIGGERS);
            if ($_it && is_array($_it)) {
                $_ky = array();
                foreach ($_it as $k => $v) {
                    if (preg_match("/{$file_name}_[a-z]/", $k)) {
                        $_ky[] = $k;
                    }
                }
                if (count($_ky) > 0) {
                    jrCore_db_delete_multiple_item_keys($module, $unique_id, $_ky, false);
                }
            }
        }
        return $func($module, $file_name, $profile_id, $unique_id);

    }
    jrCore_logger('CRI', "jrCore_delete_item_media_file: required function: {$func} does not exist!");
    return false;
}

/**
 * Disable the automatic upload handling by the Core
 * @return bool
 */
function jrCore_disable_automatic_upload_handling()
{
    return jrCore_set_flag('jrcore_disable_automatic_upload_handling', 1);
}

/**
 * Check if automatic upload handling is enabled
 * @return bool
 */
function jrCore_is_automatic_upload_handling_enabled()
{
    return (jrCore_get_flag('jrcore_disable_automatic_upload_handling') === 1) ? false : true;
}

/**
 * Delete the temp directory used for file uploads in a form
 * @param null $upload_token string MD5 hash
 * @return bool
 */
function jrCore_delete_upload_temp_directory($upload_token = null)
{
    global $_post;
    if (is_null($upload_token)) {
        if (isset($_post['upload_token']) && strlen($_post['upload_token']) === 32 && jrCore_checktype($_post['upload_token'], 'md5')) {
            $upload_token = $_post['upload_token'];
        }
    }
    if (!is_null($upload_token)) {
        $dir = jrCore_get_upload_temp_directory($upload_token);
        if (is_dir($dir)) {
            jrCore_delete_dir_contents($dir);
            rmdir($dir);
            return true;
        }
    }
    return false;
}

/**
 * Get the upload temp directory used for file uploads
 * @param $upload_token string MD5 upload token
 * @return mixed
 */
function jrCore_get_upload_temp_directory($upload_token)
{
    if (jrCore_checktype($upload_token, 'md5')) {
        $dir = jrCore_get_module_cache_dir('jrCore');
        return "{$dir}/{$upload_token}";
    }
    return false;
}

/**
 * Get media files that have been uploaded from a form
 * NOTE: $module is no longer used in this function, but was
 * at one time so is left there for backwards compatibility
 * @param string $module Module Name to check file for
 * @param string $file_name Name of file field in form
 * @return mixed
 */
function jrCore_get_uploaded_media_files($module = null, $file_name = null)
{
    global $_post;
    if (isset($_post['upload_token']{0})) {
        $dir = jrCore_get_upload_temp_directory($_post['upload_token']);
        if (is_dir($dir)) {
            if (is_null($file_name)) {
                $_tmp = glob("{$dir}/*.tmp", GLOB_NOSORT);
            }
            else {
                $_tmp = glob("{$dir}/*_{$file_name}.tmp", GLOB_NOSORT);
            }
            if ($_tmp && is_array($_tmp) && count($_tmp) > 0) {
                foreach ($_tmp as $k => $v) {
                    $_tmp[$k] = substr($v, 0, strlen($v) - 4);
                }
                sort($_tmp, SORT_NATURAL);
                return $_tmp;
            }
        }
    }
    return false;
}

/**
 * Checks to see if a media file has been uploaded for the given $file_name
 * @param string $module Module Name to check file for
 * @param string $file_name Name of file field in form
 * @param int $profile_id the Profile ID the file(s) were uploaded under
 * @return bool
 */
function jrCore_is_uploaded_media_file($module, $file_name, $profile_id)
{
    global $_post;
    if (isset($_post['upload_token']{0})) {
        $dir = jrCore_get_upload_temp_directory($_post['upload_token']);
        if (is_dir($dir)) {
            $_tmp = glob("{$dir}/*_{$file_name}.tmp", GLOB_NOSORT);
            if ($_tmp && is_array($_tmp) && count($_tmp) > 0) {
                return true;
            }
        }
    }
    return false;
}

/**
 * Saves all uploaded media files for a given Item ID
 * @param string $module Module Name to save file for
 * @param string $view View to save files for
 * @param int $profile_id the Profile ID to save the media file for
 * @param int $unique_id Unique Item ID from DataStore
 * @param array $_existing Item Array (for update checking)
 * @return bool
 */
function jrCore_save_all_media_files($module, $view, $profile_id, $unique_id, $_existing = null)
{
    global $_post;
    if (isset($_post['jr_html_form_token'])) {
        $_form = jrCore_form_get_session($_post['jr_html_form_token']);
        if (!isset($_form['form_fields']) || !is_array($_form['form_fields'])) {
            return true;
        }
        foreach ($_form['form_fields'] as $_field) {
            if (jrCore_is_uploaded_media_file($module, $_field['name'], $profile_id)) {
                jrCore_save_media_file($module, $_field['name'], $profile_id, $unique_id, null, $_existing);
            }
        }
    }
    return true;
}

/**
 * Saves an uploaded media file to the proper profile directory
 * @param string $module Module Name to save file for
 * @param string $file_name Name of file field in form
 * @param int $profile_id the Profile ID to save the media file for
 * @param int $unique_id Unique Item ID from DataStore
 * @param string $field Field to save as (defaults to field name from file)
 * @param array $_existing Item Array (for update checking)
 * @return bool
 */
function jrCore_save_media_file($module, $file_name, $profile_id, $unique_id, $field = null, $_existing = null)
{
    global $_post;
    if (!$unique_id || !jrCore_checktype($unique_id, 'number_nz')) {
        return false;
    }
    // make sure this module is using a DataStore
    if (jrCore_db_get_prefix($module) === false) {
        jrCore_logger('CRI', "module: {$module} is not using a DataStore - unable to automatically handle file uploads");
        return false;
    }
    // Did we already process this upload token?
    $_up = array();

    // See if we have been given a FULL PATH FILE - if so, that's the one we use,
    // otherwise we figure it out from the current post.
    if (is_file($file_name)) {
        // 1_audio_file_2
        if (is_null($field)) {
            list(, $field) = explode('_', basename($file_name), 2);
        }
        if (is_file("{$file_name}.tmp")) {
            $fname = trim(file_get_contents("{$file_name}.tmp"));
        }
        else {
            $fname = basename($file_name);
        }
        $_up[$field] = array(
            'tmp_name' => $file_name,
            'name'     => $fname,
            'size'     => filesize($file_name),
            'type'     => jrCore_mime_type($fname),
            'error'    => 0
        );
    }
    else {
        // Don't mess with this structure or multiple uploads will fail!
        if (isset($_post['upload_token']{0})) {
            $_up = jrCore_get_uploaded_meter_files($_post['upload_token']);
        }
    }

    // See if we are UPDATING an existing ITEM with new items
    // or replacing what is already there
    if (!is_null($_existing) && is_array($_existing)) {
        $new = 0;
        $cnt = 2;
        $idx = array_keys($_up);
        $idx = reset($idx);
        // we have an item - we are just adding more media to an existing item
        // we need to cycle through keys to find our first OPEN index
        while (true) {
            $str = $cnt;
            foreach ($_existing as $k => $v) {
                if (strpos($k, $idx) === 0 && strpos($k, '_size')) {
                    list(, , $num,) = explode('_', $k);
                    if (is_numeric($num) && $num == $cnt) {
                        $cnt++;
                        break;
                    }
                }
            }
            if ($str == $cnt) {
                // We found our open index
                $new = $cnt;
                break;
            }
        }
        if ($new > 0) {
            $_nw = array();
            foreach ($_up as $v) {
                if ($new == 2 && !isset($_existing["{$idx}_size"]) && !isset($_nw[$idx])) {
                    $_nw[$idx] = $v;
                }
                else {
                    $_nw["{$idx}_{$new}"] = $v;
                    $new++;
                }
            }
            $_up = $_nw;
            unset($_nw);
        }
    }

    // Not uploaded...
    if (!is_array($_up) || count($_up) === 0) {
        return false;
    }

    $_data = false;
    // Save off each media file that was uploaded
    foreach ($_up as $fname => $_file) {

        $ext = jrCore_file_extension($_file['name']);
        // If we do NOT have a file extension, we need to grab the mime type and add the file extension on
        if (!$ext || strlen($ext) === 0 || strlen($ext) > 4) {
            $typ = jrCore_mime_type($_file['tmp_name']);
            $ext = jrCore_file_extension_from_mime_type($typ);
        }

        $nam = "{$module}_{$unique_id}_{$fname}.{$ext}";
        if (!jrCore_write_media_file($profile_id, $nam, $_file['tmp_name'])) {
            jrCore_logger('CRI', "error saving media file: {$profile_id}/{$nam}");
            return false;
        }

        // We need to cleanup old items
        jrCore_delete_old_media_items($profile_id, $module, $unique_id, $fname, array($nam));

        // Okay we've saved it.  Next, we need to update the datastore
        // entry with the info from the file
        $pdir      = jrCore_get_media_directory($profile_id);
        $save_name = $_file['name'];
        if (!stripos($save_name, ".{$ext}")) {
            $save_name = "{$save_name}.{$ext}";
        }
        $_data = array(
            "{$fname}_time"      => 'UNIX_TIMESTAMP()',
            "{$fname}_name"      => $save_name,
            "{$fname}_size"      => $_file['size'],
            "{$fname}_type"      => jrCore_mime_type($nam),
            "{$fname}_extension" => $ext
        );

        // We have some extra info we want to make available to our listeners,
        // but we don't want it to be part of the data
        $_args = array(
            'module'     => $module,
            'file_name'  => $fname,
            'profile_id' => $profile_id,
            'unique_id'  => $unique_id,
            'saved_file' => "{$pdir}/{$nam}",
            '_file'      => $_file,
            '_existing'  => $_existing
        );

        // Trigger our save media file event
        $_data = jrCore_trigger_event('jrCore', 'save_media_file', $_data, $_args);
        jrCore_set_flag("jrcore_created_pending_item_{$module}_{$unique_id}", 1);
        jrCore_db_update_item($module, $unique_id, $_data);
    }
    return $_data;
}

/**
 * Move all files uploaded by the meter into an array
 * @param string $upload_token Form Token
 * @return array
 */
function jrCore_get_uploaded_meter_files($upload_token)
{
    $_up = false;
    $dir = jrCore_get_upload_temp_directory($upload_token);
    if (is_dir($dir)) {
        // We've got uploaded files via the progress meter
        $_tmp = glob("{$dir}/*", GLOB_NOSORT);
        // [0] => data/cache/jrCore/12046f3177d5079e5528aa7a34175c73/1_audio_file       <- contains actual file
        // [1] => data/cache/jrCore/12046f3177d5079e5528aa7a34175c73/1_audio_file.tmp   <- contains file name
        if ($_tmp && is_array($_tmp)) {
            $_up = array();
            $_nm = array();
            foreach ($_tmp as $file) {
                if (is_file($file)) {
                    $ext = jrCore_file_extension($file);
                    if ($ext && $ext == 'tmp') {
                        list($f_num, $field) = explode('_', basename($file), 2);
                        $field = str_replace('.tmp', '', $field);
                        $fname = file_get_contents($file);
                        $fdata = "{$dir}/{$f_num}_{$field}";
                        if (is_file($fdata)) {
                            $key = $field;
                            if (!isset($_nm[$field])) {
                                $_nm[$field] = 0;
                            }
                            if ($_nm[$field] > 0) {
                                $key = "{$field}_" . $_nm[$field];
                            }
                            $_up[$key] = array(
                                'tmp_name' => $fdata,
                                'name'     => $fname,
                                'size'     => filesize($fdata),
                                'type'     => jrCore_mime_type($fname),
                                'error'    => 0
                            );
                            $_nm[$field]++;
                        }
                    }
                }
            }
        }
    }
    return $_up;
}

/**
 * @ignore
 * jrCore_get_media_system_plugins
 * @return array
 */
function jrCore_get_media_system_plugins()
{
    return jrCore_get_system_plugins('media');
}

/**
 * jrCore_get_active_media_system
 * @return string
 */
function jrCore_get_active_media_system()
{
    global $_conf;
    if (isset($_conf['jrCore_active_media_system']{1})) {
        return $_conf['jrCore_active_media_system'];
    }
    return 'jrCore_local';
}

/**
 * Get the active media function
 * @param string $type active media system prefix
 * @param string $func function to get
 * @param array $_args function arguments
 * @return mixed
 */
function jrCore_get_active_media_function($type, $func, $_args = null)
{
    $temp = "_{$type}_{$func}";
    if (!function_exists($temp)) {
        $temp = "_jrCore_local_{$func}";
    }
    $_tmp = array('type' => $type, 'func' => $func, 'active_function' => $temp);
    $_tmp = jrCore_trigger_event('jrCore', 'get_media_function', $_tmp, $_args);
    if (isset($_tmp['active_function'])) {
        return $_tmp['active_function'];
    }
    return $temp;
}

/**
 * Get the media directory for a Profile ID
 * @param $profile_id int Profile ID to get media directory for
 * @param $force_local bool set to TRUE to force local file system
 * @return bool
 */
function jrCore_get_media_directory($profile_id, $force_local = false)
{
    if ($profile_id !== 'system' && !jrCore_checktype($profile_id, 'number_nn')) {
        return false;
    }
    $type = ($force_local) ? 'jrCore_local' : jrCore_get_active_media_system();
    $func = jrCore_get_active_media_function($type, 'media_get_directory', func_get_args());
    if (function_exists($func)) {
        return $func($profile_id);
    }
    jrCore_logger('CRI', "jrCore_get_media_directory: required function: {$func} does not exist!");
    return false;
}

/**
 * Get the media URL for a Profile ID
 * @param $profile_id int Profile ID to get media directory for
 * @param $force_local bool set to TRUE to force local file system
 * @return bool
 */
function jrCore_get_media_url($profile_id, $force_local = false)
{
    $type = ($force_local) ? 'jrCore_local' : jrCore_get_active_media_system();
    $func = jrCore_get_active_media_function($type, 'media_get_url', func_get_args());
    if (function_exists($func)) {
        return $func($profile_id);
    }
    jrCore_logger('CRI', "jrCore_get_media_url: required function: {$func} does not exist!");
    return false;
}

/**
 * Create a new Media Directory for a Profile ID
 * @param $profile_id int Profile ID to get media directory for
 * @param $force_local bool set to TRUE to force local file system
 * @return bool
 */
function jrCore_create_media_directory($profile_id, $force_local = false)
{
    $type = ($force_local) ? 'jrCore_local' : jrCore_get_active_media_system();
    $func = jrCore_get_active_media_function($type, 'media_create_directory', func_get_args());
    if (function_exists($func)) {
        return $func($profile_id);
    }
    jrCore_logger('CRI', "jrCore_create_media_directory: required function: {$func} does not exist!");
    return false;
}

/**
 * Delete a Media Directory for a Profile ID
 * @param $profile_id int Profile ID to get media directory for
 * @param $force_local bool set to TRUE to force local file system
 * @return bool
 */
function jrCore_delete_media_directory($profile_id, $force_local = false)
{
    $type = ($force_local) ? 'jrCore_local' : jrCore_get_active_media_system();
    $func = jrCore_get_active_media_function($type, 'media_delete_directory', func_get_args());
    if (function_exists($func)) {
        return $func($profile_id);
    }
    jrCore_logger('CRI', "jrCore_delete_media_directory: required function: {$func} does not exist!");
    return false;
}

/**
 * Clean up "old" (replaced) media items in a profile directory
 * @param $profile_id int Profile ID
 * @param $module string Module Name
 * @param $unique_id int Item_ID
 * @param $field string Unique item field name
 * @param null $_exclude option array of file names to exclude
 * @return bool
 */
function jrCore_delete_old_media_items($profile_id, $module, $unique_id, $field, $_exclude = null)
{
    $type = jrCore_get_active_media_system();
    $func = jrCore_get_active_media_function($type, 'media_delete_old_items', func_get_args());
    if (function_exists($func)) {
        return $func($profile_id, $module, $unique_id, $field, $_exclude);
    }
    jrCore_logger('CRI', "jrCore_delete_old_media_items: required function: {$func} does not exist!");
    return false;
}

/**
 * Get size of a Profile media directory
 * @param int $profile_id Profile ID to create media directory for
 * @return int
 */
function jrCore_get_media_directory_size($profile_id)
{
    $type = jrCore_get_active_media_system();
    $func = jrCore_get_active_media_function($type, 'media_get_directory_size', func_get_args());
    if (function_exists($func)) {
        return $func($profile_id);
    }
    jrCore_logger('CRI', "jrCore_get_media_directory_size: required function: {$func} does not exist!");
    return false;
}

/**
 * Get an array of all media files in a profile directory
 * @param int $profile_id Profile ID
 * @param string $pattern optional glob pattern
 * @return mixed
 */
function jrCore_get_media_files($profile_id, $pattern = null)
{
    $type = jrCore_get_active_media_system();
    $func = jrCore_get_active_media_function($type, 'media_get_files', func_get_args());
    if (function_exists($func)) {
        return $func($profile_id, $pattern);
    }
    jrCore_logger('CRI', "jrCore_get_media_files: required function: {$func} does not exist!");
    return false;
}

/**
 * Read a file from a profile directory
 * <code>
 * Php equivalent: file_get_contents()
 * </code>
 * @param int $profile_id Profile ID
 * @param string $file File name to read
 * @param string $save_as Save AS to alternate path
 * @return bool Returns True/False
 */
function jrCore_read_media_file($profile_id, $file, $save_as = null)
{
    $type = jrCore_get_active_media_system();
    $func = jrCore_get_active_media_function($type, 'media_read', func_get_args());
    if (function_exists($func)) {
        return $func($profile_id, $file, $save_as);
    }
    jrCore_logger('CRI', "jrCore_read_media_file: required function: {$func} does not exist!");
    return false;
}

/**
 * Write a file to a profile directory
 * @param int $profile_id Profile ID
 * @param string $file File to write data to
 * @param string $data Data to write to file
 * @param string $access Access permissions
 * @return bool
 */
function jrCore_write_media_file($profile_id, $file, $data, $access = null)
{
    $type = jrCore_get_active_media_system();
    $func = jrCore_get_active_media_function($type, 'media_write', func_get_args());
    if (function_exists($func)) {
        return $func($profile_id, $file, $data, $access);
    }
    jrCore_logger('CRI', "jrCore_write_media_file: required function: {$func} does not exist!");
    return false;
}

/**
 * Delete a file from a profile directory
 * @param int $profile_id Profile ID
 * @param string $file File name to delete
 * @return bool
 */
function jrCore_delete_media_file($profile_id, $file)
{
    $type = jrCore_get_active_media_system();
    $func = jrCore_get_active_media_function($type, 'media_delete', func_get_args());
    if (function_exists($func)) {
        return $func($profile_id, $file);
    }
    jrCore_logger('CRI', "jrCore_delete_media_file: required function: {$func} does not exist!");
    return false;
}

/**
 * wrapper function to check if a file of the specified file type exists.
 * @param int $profile_id Profile ID
 * @param string $file_name File name to check
 * @return bool
 */
function jrCore_media_file_exists($profile_id, $file_name)
{
    $type = jrCore_get_active_media_system();
    $func = jrCore_get_active_media_function($type, 'media_exists', func_get_args());
    if (function_exists($func)) {
        return $func($profile_id, basename($file_name));
    }
    jrCore_logger('CRI', "jrCore_media_file_exists: required function: {$func} does not exist!");
    return false;
}

/**
 * Confirm a media file is on the local server
 * @param int $profile_id Profile ID
 * @param $file string Unique File Name field
 * @param $local_save string save to this file instead of the media directory
 * @param $force bool set to TRUE to force overwrite of existing local file
 * @return string
 */
function jrCore_confirm_media_file_is_local($profile_id, $file, $local_save = null, $force = false)
{
    $type = jrCore_get_active_media_system();
    $func = jrCore_get_active_media_function($type, 'media_confirm_is_local', func_get_args());
    if (function_exists($func)) {
        return $func($profile_id, $file, $local_save, $force);
    }
    jrCore_logger('CRI', "jrCore_confirm_media_file_is_local: required function: {$func} does not exist!");
    return false;
}

/**
 * jrCore_media_file_stream
 * @param int $profile_id Profile ID
 * @param string $file File name to Download
 * @param string $send_name File name to use in download dialog
 * @return bool
 */
function jrCore_media_file_stream($profile_id, $file, $send_name)
{
    $type = jrCore_get_active_media_system();
    $func = jrCore_get_active_media_function($type, 'media_stream', func_get_args());
    if (function_exists($func)) {
        jrCore_db_close();
        return $func($profile_id, $file, $send_name);
    }
    jrCore_logger('CRI', "jrCore_media_file_stream: required function: {$func} does not exist!");
    return false;
}

/**
 * The jrCore_media_file_download function will download a media file
 * @param string $profile_id Profile ID
 * @param string $file File name to Download
 * @param string $send_name File name to use in download dialog
 * @return bool
 */
function jrCore_media_file_download($profile_id, $file, $send_name)
{
    $type = jrCore_get_active_media_system();
    $func = jrCore_get_active_media_function($type, 'media_download', func_get_args());
    if (function_exists($func)) {
        jrCore_db_close();
        return $func($profile_id, $file, $send_name);
    }
    jrCore_logger('CRI', "jrCore_media_file_download: required function: {$func} does not exist!");
    return false;
}

/**
 * Copy an existing file to a media file in a profile directory
 * @param int $target_profile_id target profile_id
 * @param string $source_file_path Source File
 * @param string $target_file_name Target File
 * @return bool
 */
function jrCore_copy_media_file($target_profile_id, $source_file_path, $target_file_name)
{
    $type = jrCore_get_active_media_system();
    $func = jrCore_get_active_media_function($type, 'media_copy', func_get_args());
    if (function_exists($func)) {
        return $func($target_profile_id, $source_file_path, $target_file_name);
    }
    jrCore_logger('CRI', "jrCore_copy_media_file: required function: {$func} does not exist!");
    return false;
}

/**
 * The jrCore_rename_media_file function is a media wrapper
 * @param string $profile_id Directory file is located in
 * @param string $file File old (existing) name
 * @param string $new_name File new name
 *
 * @return bool Returns True/False
 */
function jrCore_rename_media_file($profile_id, $file, $new_name)
{
    $type = jrCore_get_active_media_system();
    $func = jrCore_get_active_media_function($type, 'media_rename', func_get_args());
    if (function_exists($func)) {
        return $func($profile_id, $file, $new_name);
    }
    jrCore_logger('CRI', "jrCore_rename_media_file: required function: {$func} does not exist!");
    return false;
}

//--------------------------------------
// Local FileSystem media plugins
//--------------------------------------
/**
 * The _local_media_get_directory_group function will return the
 * directory "group" that a given profile_id belongs to.  This is
 * to overcome ext3 limitations on dirs in dirs.
 * @param int $profile_id Profile ID
 * @return mixed Returns string on success, bool false on failure
 */
function _jrCore_local_media_get_directory_group($profile_id)
{
    if (jrCore_checktype($profile_id, 'number_nn')) {
        $sub = (int) ceil($profile_id / 1000);
        return $sub;
    }
    elseif ($profile_id === 'system') {
        return '1';
    }
    return false;
}

/**
 * Local FileSystem Get Media Directory function
 * @param int $profile_id Profile ID
 * @return string
 */
function _jrCore_local_media_get_directory($profile_id)
{
    if ($profile_id === 'system') {
        $group_dir  = 1;
        $profile_id = 0;
    }
    else {
        $group_dir = _jrCore_local_media_get_directory_group($profile_id);
    }
    $media_dir = APP_DIR . "/data/media/{$group_dir}/{$profile_id}";
    if (!is_dir($media_dir)) {
        jrCore_create_directory($media_dir, true);
    }
    return $media_dir;
}

/**
 * Local FileSystem Get Media URL function
 * @param int $profile_id Profile ID
 * @return string
 */
function _jrCore_local_media_get_url($profile_id)
{
    global $_conf;
    if ($profile_id === 'system') {
        $group_dir  = 1;
        $profile_id = 0;
    }
    else {
        $group_dir = _jrCore_local_media_get_directory_group($profile_id);
    }
    return "{$_conf['jrCore_base_url']}/data/media/{$group_dir}/{$profile_id}";
}

/**
 * Local FileSystem Get Media Directory function
 * @param int $profile_id Profile ID
 * @return bool
 */
function _jrCore_local_media_create_directory($profile_id)
{
    global $_conf;
    // First our media directory
    $media_dir = _jrCore_local_media_get_directory($profile_id);

    if (!is_dir($media_dir)) {
        if (!jrCore_create_directory($media_dir, true)) {
            jrCore_logger('CRI', '_local_media_create_directory: unable to create profile media directory: ' . str_replace(APP_DIR . '/', '', $media_dir));
            return false;
        }
    }
    if (!is_writable($media_dir)) {
        if (!chmod($media_dir, $_conf['jrCore_dir_perms'])) {
            jrCore_logger('CRI', '_local_media_create_directory: unable to properly permission profile media directory: ' . str_replace(APP_DIR . '/', '', $media_dir));
            return false;
        }
    }
    return true;
}

/**
 * Local FileSystem Get Media Directory function
 * @param int $profile_id Profile ID
 * @return bool
 */
function _jrCore_local_media_delete_directory($profile_id)
{
    $media_dir = _jrCore_local_media_get_directory($profile_id);
    if (is_dir($media_dir)) {
        if (jrCore_delete_dir_contents($media_dir, false)) {
            rmdir($media_dir);
        }
        else {
            return false;
        }
    }
    return true;
}

/**
 * Clean up "old" (replaced) media items in a profile directory
 * @param $profile_id int Profile ID
 * @param $module string Module Name
 * @param $unique_id int Item_ID
 * @param $field string Unique item field name
 * @param null $_exclude option array of file names to exclude
 * @return bool
 */
function _jrCore_local_media_delete_old_items($profile_id, $module, $unique_id, $field, $_exclude = null)
{
    $pdir = jrCore_get_media_directory($profile_id);
    $_old = glob("{$pdir}/{$module}_{$unique_id}_{$field}.*", GLOB_NOSORT);
    if ($_old && is_array($_old)) {
        foreach ($_old as $old_file) {
            if (!is_null($_exclude) && is_array($_exclude)) {
                $old_name = basename($old_file);
                if (!in_array($old_name, $_exclude)) {
                    unlink($old_file); // OK
                }
            }
            else {
                unlink($old_file); // OK
            }
        }
    }
    return true;
}

/**
 * Delete matching media file(s) for a given item ID
 * @param string $module Module Name to save file for
 * @param string $file_name Name of file field in form
 * @param int $profile_id the Profile ID to save the media file for
 * @param int $unique_id Unique Item ID from DataStore
 * @return bool
 */
function _jrCore_local_media_delete_file($module, $file_name, $profile_id, $unique_id)
{
    $dir = jrCore_get_media_directory($profile_id);
    $nam = "{$module}_{$unique_id}_{$file_name}.";
    if ($h = opendir(realpath($dir))) {
        while (false !== ($file = readdir($h))) {
            if (strpos($file, $nam) === 0) {
                unlink("{$dir}/{$file}"); // OK
            }
        }
        closedir($h);
    }
    return true;
}

/**
 * Local FileSystem Get Media Directory Size function
 * @param int $profile_id Profile ID
 * @return int
 */
function _jrCore_local_media_get_directory_size($profile_id)
{
    $media_dir = _jrCore_local_media_get_directory($profile_id);
    clearstatcache();
    $size = 0;
    if ($h = opendir(realpath($media_dir))) {
        while (false !== ($file = readdir($h))) {
            if ($file == '.' || $file == '..' || $file == 'cache') {
                continue;
            }
            else {
                $size += filesize("{$media_dir}/{$file}");
            }
        }
        closedir($h);
    }
    return $size;
}

/**
 * Local FileSystem Get Media Files for a Profile ID
 * @param int $profile_id Profile ID
 * @param string $pattern optional glob pattern
 * @return mixed
 */
function _jrCore_local_media_get_files($profile_id, $pattern = null)
{
    $media_dir = _jrCore_local_media_get_directory($profile_id);
    if (!is_null($pattern)) {
        $_fl = glob("{$media_dir}/{$pattern}", GLOB_NOSORT);
    }
    else {
        $_fl = array();
        if ($h = opendir(realpath($media_dir))) {
            while (false !== ($file = readdir($h))) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                $_fl[] = "{$media_dir}/{$file}";
            }
            closedir($h);
        }
    }
    $_ot = array();
    if (count($_fl) > 0) {
        foreach ($_fl as $file) {
            if (is_file($file)) {
                $_ot[] = array(
                    'name' => $file,
                    'size' => filesize($file),
                    'time' => filemtime($file)
                );
            }
        }
    }
    return (count($_ot) > 0) ? $_ot : false;
}

/**
 * Local FileSystem Read Function
 * Can potentially use a TON of RAM - only use if needed
 * @param int $profile_id Profile ID
 * @param string $file File Name
 * @param string $save_as If given, will be saved to the given path
 * @return mixed
 */
function _jrCore_local_media_read($profile_id, $file, $save_as = null)
{
    $media_dir = _jrCore_local_media_get_directory($profile_id);
    if (is_file("{$media_dir}/{$file}")) {
        if (!is_null($save_as) && strpos($save_as, APP_DIR) === 0) {
            return jrCore_chunked_copy("{$media_dir}/{$file}", $save_as);
        }
        else {
            return file_get_contents("{$media_dir}/{$file}");
        }
    }
    return false;
}

/**
 * Local FileSystem Write Function
 * This function will set permissions on the created/updated file to 0644
 * @param int $profile_id Profile ID
 * @param string $file File Name to write to
 * @param string $data Data to write to file
 * @param string $access ACL access level (not used on local file system)
 * @return bool
 */
function _jrCore_local_media_write($profile_id, $file, $data, $access)
{
    $media_dir = _jrCore_local_media_get_directory($profile_id);
    if (@is_file($data)) {
        return jrCore_chunked_copy($data, "{$media_dir}/{$file}");
    }
    else {
        if (jrCore_write_to_file("{$media_dir}/{$file}", $data, 'overwrite')) {
            return true;
        }
    }
    return false;
}

/**
 * Local FileSystem Delete Function
 * @ignore used internally
 * @param int $profile_id Profile ID
 * @param string $file file
 * @return mixed
 */
function _jrCore_local_media_delete($profile_id, $file)
{
    global $_conf;
    $media_dir = _jrCore_local_media_get_directory($profile_id);
    if (is_file("{$media_dir}/{$file}")) {
        $tmp = @unlink("{$media_dir}/{$file}"); // OK
        if (!$tmp) {
            // try to change permissions and try again
            chmod("{$media_dir}/{$file}", $_conf['jrCore_file_perms']);
            $tmp = @unlink("{$media_dir}/{$file}"); // OK
        }
        return $tmp;
    }
    elseif (is_file($file) && strpos($file, $media_dir) === 0) {
        // We've been given a full path file - handle it
        $tmp = @unlink($file); // OK
        if (!$tmp) {
            // try to change permissions and try again
            chmod($file, $_conf['jrCore_file_perms']);
            $tmp = @unlink($file); // OK
        }
        return $tmp;
    }
    return false;
}

/**
 * Local FileSystem Exist function
 * @ignore used internally
 * @param int $profile_id Profile ID
 * @param string $file file
 * @return bool
 */
function _jrCore_local_media_exists($profile_id, $file)
{
    $media_dir = _jrCore_local_media_get_directory($profile_id);
    if (is_file("{$media_dir}/{$file}")) {
        return true;
    }
    // See if we were given full path
    elseif (is_file($file) && strpos($file, $media_dir) === 0) {
        return true;
    }
    return false;
}

/**
 * Local FileSystem Confirm function
 * @ignore used internally
 * @param int $profile_id Profile ID
 * @param string $file file
 * @param $local_save string local file name to save to
 * @param $force bool
 * @return bool
 */
function _jrCore_local_media_confirm_is_local($profile_id, $file, $local_save = null, $force = false)
{
    $media_dir = _jrCore_local_media_get_directory($profile_id);
    if (is_file("{$media_dir}/{$file}")) {
        if (!is_null($local_save) && $local_save != "{$media_dir}/{$file}") {
            if (jrCore_chunked_copy("{$media_dir}/{$file}", $local_save)) {
                return $local_save;
            }
        }
        return "{$media_dir}/{$file}";
    }
    // See if we were given full path
    elseif (is_file($file) && strpos($file, $media_dir) === 0) {
        return $file;
    }
    return false;
}

/**
 * Local FileSystem Stream function - Sends HEADERS!
 * @param int $profile_id Profile ID
 * @param string $file File to Stream
 * @param string $send_name Send-As Filename
 * @return bool
 */
function _jrCore_local_media_stream($profile_id, $file, $send_name)
{
    $media_dir = _jrCore_local_media_get_directory($profile_id);
    if (!is_file("{$media_dir}/{$file}")) {
        return false;
    }
    $size = filesize("{$media_dir}/{$file}");
    $type = jrCore_mime_type("{$media_dir}/{$file}");

    if (isset($_SERVER['HTTP_RANGE']) && $_SERVER['HTTP_RANGE'] != 'bytes=0-') {
        _jrCore_local_media_stream_with_range("{$media_dir}/{$file}");
        return true;
    }
    else {

        // Check for 304 not modified
        $tim = filectime("{$media_dir}/{$file}");
        if ($tim && $tim > 0) {
            $ifs = (function_exists('getenv')) ? getenv('HTTP_IF_MODIFIED_SINCE') : false;
            if (!$ifs && isset($_SERVER['HTTP_IF_MODIFIED_SINCE']{1})) {
                $ifs = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
            }
            if ($ifs && strtotime($ifs) == $tim) {
                $_tmp = jrCore_get_flag('jrcore_set_custom_header');
                if ($_tmp && is_array($_tmp)) {
                    foreach ($_tmp as $header) {
                        header($header);
                    }
                }
                header("Last-Modified: " . gmdate('D, d M Y H:i:s \G\M\T', $tim));
                header('Content-Disposition: inline; filename="' . $send_name . '"');
                header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400000));
                header('HTTP/1.1 304 Not Modified');
                return true;
            }
        }

        header('Accept-Ranges: bytes');
        header('Content-Length: ' . $size);
        header('Content-Type: ' . $type);
        header('Content-Disposition: inline; filename="' . $send_name . '"');
    }

    $handle = fopen("{$media_dir}/{$file}", 'rb');
    if (!$handle) {
        jrCore_logger('CRI', "local_media_stream: unable to create file handle for streaming: {$media_dir}/{$file}");
        return false;
    }
    $bytes_sent = 0;
    while (true) {
        fseek($handle, $bytes_sent);
        // Read 1 megabyte at a time...
        $buffer     = fread($handle, 1048576);
        $bytes_sent += strlen($buffer);
        echo $buffer;
        flush();
        unset($buffer);
        // Also - check that we have not sent out more data then the allowed size
        if ($bytes_sent >= $size) {
            fclose($handle);
            return true;
        }
    }
    fclose($handle);
    return true;
}

/**
 * Stream a file to the iPhone with RANGE support
 * @param $file
 */
function _jrCore_local_media_stream_with_range($file)
{
    $fp     = @fopen($file, 'rb');
    $size   = filesize($file); // File size
    $length = $size; // Content length
    $start  = 0; // Start byte
    $end    = $size - 1; // End byte

    // Send the accept range header
    header("Accept-Ranges: 0-$length");
    if (isset($_SERVER['HTTP_RANGE'])) {
        $c_end = $end;
        // Extract the range string
        list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
        if (strpos($range, ',') !== false) {
            jrCore_db_close();
            header('HTTP/1.1 416 Requested Range Not Satisfiable');
            header("Content-Range: bytes $start-$end/$size");
            jrCore_db_close();
            exit;
        }
        // If the range starts with an '-' we start from the beginning
        // If not, we forward the file pointer
        // And make sure to get the end byte if specified
        if ($range[0] == '-') {
            // The n-number of the last bytes is requested
            $c_start = $size - substr($range, 1);
        }
        else {
            $range   = explode('-', $range);
            $c_start = $range[0];
            $c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
        }
        // End bytes can not be larger than $end.
        $c_end = ($c_end > $end) ? $end : $c_end;
        // Validate the requested range and return an error if it's not correct.
        if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
            jrCore_db_close();
            header('HTTP/1.1 416 Requested Range Not Satisfiable');
            header("Content-Range: bytes {$start}-{$end}/{$size}");
            exit;
        }
        $start  = $c_start;
        $end    = $c_end;
        $length = $end - $start + 1; // Calculate new content length
        fseek($fp, $start);
        header('HTTP/1.1 206 Partial Content');
    }
    // Notify the client the byte range we'll be outputting
    header("Content-Range: bytes {$start}-{$end}/{$size}");
    header("Content-Length: {$length}");

    // Start buffered download
    $buffer = 1024 * 8;
    while (!feof($fp) && ($p = ftell($fp)) <= $end) {
        if ($p + $buffer > $end) {
            $buffer = $end - $p + 1;
        }
        set_time_limit(0); // Reset time limit for big files
        echo fread($fp, $buffer);
        flush();
    }
    fclose($fp);
}

/**
 * Local FileSystem Download function
 * NOTE: Sends HEADERS!
 * @param int $profile_id Profile ID
 * @param string $file File to Download
 * @param string $send_name Send-As Filename
 * @return bool
 */
function _jrCore_local_media_download($profile_id, $file, $send_name)
{
    $media_dir = _jrCore_local_media_get_directory($profile_id);
    if (!is_file("{$media_dir}/{$file}") && !is_link("{$media_dir}/{$file}")) {
        return false;
    }
    // Send headers to initiate download prompt
    $size = filesize("{$media_dir}/{$file}");
    header('Content-Length: ' . $size);
    header('Connection: close');
    header('Content-Type: application/octet-stream');
    header('Content-Transfer-Encoding: binary');
    header('Content-Disposition: attachment; filename="' . $send_name . '"');

    $handle = fopen("{$media_dir}/{$file}", 'rb');
    if (!$handle) {
        jrCore_logger('CRI', "local_media_download: unable to create file handle for download: {$media_dir}/{$file}");
        return false;
    }
    $bytes_sent = 0;
    while ($bytes_sent < $size) {
        fseek($handle, $bytes_sent);
        // Read 1 megabyte at a time...
        $buffer     = fread($handle, 1048576);
        $bytes_sent += strlen($buffer);
        echo $buffer;
        ob_flush();
        flush();
        unset($buffer);
        // Also - check that we have not sent out more data then the allowed size
        if ($bytes_sent >= $size) {
            fclose($handle);
            return true;
        }
    }
    fclose($handle);
    return true;
}

/**
 * Local FileSystem Copy function
 * @ignore used internally
 * @param int $profile_id Profile ID
 * @param string $source_file file to copy
 * @param string $target_file file to copy to
 * @return mixed
 */
function _jrCore_local_media_copy($profile_id, $source_file, $target_file)
{
    $media_dir = _jrCore_local_media_get_directory($profile_id);
    if (is_file($source_file)) {
        $max = @ini_get('max_execution_time');
        if (!$max || $max < 600) {
            @ini_set('max_execution_time', 600); // 10 minutes max
        }
        if (strpos($target_file, APP_DIR) === 0) {
            $target_file = basename($target_file);
        }
        return jrCore_chunked_copy($source_file, "{$media_dir}/{$target_file}");
    }
    return false;
}

/**
 * Local FileSystem Rename function
 * @ignore used internally
 * @param int $profile_id Profile ID
 * @param string $file file name
 * @param string $new_name new name for file
 * @return mixed
 */
function _jrCore_local_media_rename($profile_id, $file, $new_name)
{
    $media_dir = _jrCore_local_media_get_directory($profile_id);
    if (!is_file($file) && is_file("{$media_dir}/{$file}")) {
        $file = "{$media_dir}/{$file}";
    }
    if (is_file($file)) {
        if (rename($file, "{$media_dir}/{$new_name}")) {
            return true;
        }
        if (jrCore_chunked_copy($file, "{$media_dir}/{$new_name}")) {
            unlink($file); // OK
            return true;
        }
    }
    return false;
}

/**
 * Local FileSystem Stat function
 * @ignore used internally
 * @param int $profile_id Profile ID
 * @param string $file file
 * @return mixed
 */
function _jrCore_local_media_stat($profile_id, $file)
{
    $media_dir = _jrCore_local_media_get_directory($profile_id);
    if (is_file("{$media_dir}/{$file}")) {
        return stat("{$media_dir}/{$file}");
    }
    return false;
}
