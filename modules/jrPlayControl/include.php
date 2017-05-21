<?php
/**
 * Jamroom 5 Play Control module
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
 * @copyright 2012 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrPlayControl_meta
 */
function jrPlayControl_meta()
{
    $_tmp = array(
        'name'        => 'Play Control',
        'url'         => 'playcontrol',
        'version'     => '1.0.2',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Set limits on the number of streams and downloads an IP address can do in a month',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2965/user-play-control',
        'category'    => 'users',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * jrPlayControl_init
 */
function jrPlayControl_init()
{
    // listen for streams and downloads
    jrCore_register_event_listener('jrCore', 'stream_file', 'jrPlayControl_media_listener');
    jrCore_register_event_listener('jrCore', 'download_file', 'jrPlayControl_media_listener');

    // Once a day we cleanup old entries
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrPlayControl_daily_maintenance_listener');
    return true;
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Adds width/height keys to saved media info
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrPlayControl_media_listener($_data, $_user, $_conf, $_args, $event)
{
    if (!jrUser_is_admin()) {
        // If this is Safari, we get scanned 3 times - only count actual play
        if (stripos($_SERVER['HTTP_USER_AGENT'], 'safari') && isset($_SERVER['HTTP_RANGE']) && strpos($_SERVER['HTTP_RANGE'], 'bytes=0') === 0) {
            return $_data;
        }
        $key = false;
        $max = 0;
        switch ($event) {
            case 'stream_file':
                $key = 's';
                $max = $_conf['jrPlayControl_max_streams'];
                break;
            case 'download_file':
                $key = 'd';
                $max = $_conf['jrPlayControl_max_downloads'];
                break;
        }
        if ($key && jrCore_checktype($max, 'number_nz')) {
            $uip = jrCore_get_ip();
            $mon = strftime('%Y%m');
            $key = jrCore_db_escape("{$mon}{$key}");
            $tbl = jrCore_db_table_name('jrPlayControl', 'play');
            $req = "UPDATE {$tbl} SET play_count = (play_count + 1) WHERE play_key = '{$key}' AND play_ip = '{$uip}' AND play_count <= {$max} LIMIT 1";
            $cnt = jrCore_db_query($req, 'COUNT');
            if (!$cnt || $cnt === 0) {
                $req = "INSERT INTO {$tbl} (play_ip,play_key,play_count) VALUES ('{$uip}','{$key}',1) ON DUPLICATE KEY UPDATE play_count = (play_count + 1)";
                $cnt = jrCore_db_query($req, 'COUNT');
                if (!$cnt || $cnt === 0) {
                    // We've violated our primary key - over limit
                    header('HTTP/1.0 403 Forbidden');
                    header('Connection: close');
                    exit();
                }
            }
        }
    }
    return $_data;
}

/**
 * Keeps play cache table clean of old entries
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrPlayControl_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    // We will delete any cached image files that have not been accessed in the last day
    $now = strftime('%Y%m');
    $tbl = jrCore_db_table_name('jrPlayControl', 'play');
    $req = "DELETE FROM {$tbl} WHERE play_key NOT LIKE '{$now}%'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt > 0) {
        jrCore_logger('INF', "deleted {$cnt} play control entries from last month");
    }
    return $_data;
}
