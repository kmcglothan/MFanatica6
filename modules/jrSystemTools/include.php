<?php
/**
 * Jamroom System Tools module
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
 * @copyright 2003 - 2017 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrSystemTools_meta
 */
function jrSystemTools_meta()
{
    $_tmp = array(
        'name'        => 'System Tools',
        'url'         => 'systemtools',
        'version'     => '1.0.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Provides low level command line tools used by modules',
        'category'    => 'core',
        'license'     => 'mpl',
        'locked'      => true,
        'activate'    => true
    );
    return $_tmp;
}

/**
 * jrSystemTools_init
 */
function jrSystemTools_init()
{
    jrCore_register_event_listener('jrCore', 'system_check', 'jrSystemTools_system_check_listener');
    return true;
}

//--------------------------
// EVENT LISTENERS
//--------------------------

/**
 * Check that bundled binaries are executable
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrSystemTools_system_check_listener($_data, $_user, $_conf, $_args, $event)
{
    $dir = jrCore_get_module_cache_dir('jrSystemTools');
    $tmp = tempnam($dir, 'system_check_');
    foreach (jrSystemTools_get_tools() as $tool => $_inf) {
        $dat             = array();
        $dat[1]['title'] = $tool;
        $dat[1]['class'] = 'center';
        $dat[2]['title'] = 'executable';
        $dat[2]['class'] = 'center';
        // Make sure we are installed
        $mod = null;
        if (isset($_inf['alternate'])) {
            $mod = $_inf['alternate'];
        }
        if (!$cmd = jrCore_get_tool_path($tool, $mod)) {
            $dat[3]['title'] = $_args['fail'];
            $dat[4]['title'] = str_replace(APP_DIR . '/', '', $cmd) . ' is not executable';
        }
        else {
            // Make sure it actually works
            ob_start();
            system("{$cmd} {$_inf['command']} >{$tmp} 2>&1", $ret);
            ob_end_clean();
            if (!is_file($tmp) || !strpos(file_get_contents($tmp), $_inf['result'])) {
                $dat[3]['title'] = $_args['fail'];
                $dat[4]['title'] = str_replace(APP_DIR . '/', '', $cmd) . ' is not working correctly';
            }
            else {
                $dat[3]['title'] = $_args['pass'];
                $dat[4]['title'] = "{$tool} binary is working correctly";
            }
        }
        $dat[3]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    return $_data;
}

//--------------------------
// FUNCTIONS
//--------------------------

/**
 * Get provided tools
 * @return array|bool|string
 */
function jrSystemTools_get_tools()
{
    return array(
        'convert' => array(
            'command'   => '-version',
            'result'    => 'ImageMagick',
            'alternate' => 'jrImage'
        ),
        'epeg'    => array(
            'command' => '-v',
            'result'  => '[options]'
        ),
        'ffmpeg'  => array(
            'command'   => '-version',
            'result'    => 'libavutil',
            'alternate' => 'jrCore'
        ),
        'id3v2'   => array(
            'command'   => '-h',
            'result'    => '[OPTION]',
            'alternate' => 'jrAudio'
        ),
        'jpegoptim'   => array(
            'command'   => '--version',
            'result'    => 'Copyright'
        ),
        'sox'     => array(
            'command'   => '--version',
            'result'    => 'SoX',
            'alternate' => 'jrAudio'
        ),
        'wget'    => array(
            'command'   => '--version',
            'result'    => 'Wget',
            'alternate' => 'jrUrlScan'
        )
    );
}

