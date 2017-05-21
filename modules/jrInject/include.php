<?php
/**
 * Jamroom 5 Template Injection module
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
 * meta
 */
function jrInject_meta()
{
    $_tmp = array(
        'name'        => 'Template Injection',
        'url'         => 'inject',
        'version'     => '1.0.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Template String and Variable Injection',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/1629/template-injection',
        'category'    => 'tools',
        'license'     => 'mpl',
        'priority'    => 250
    );
    return $_tmp;
}

/**
 * init
 */
function jrInject_init()
{
    // We're going to listen to the "save_media_file" event
    // so we can add image specific fields to the item
    jrCore_register_event_listener('jrCore', 'view_results', 'jrInject_view_results_listener');
    return true;
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Simple key => replacements for output
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrInject_view_results_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if ((!isset($_post['_uri']) || !strpos($_post['_uri'], '/admin/')) && isset($_conf['jrInject_active']) && $_conf['jrInject_active'] == 'on' && isset($_conf['jrInject_replacement_values']{1})) {
        $_tmp = explode("\n", $_conf['jrInject_replacement_values']);
        if (isset($_tmp) && is_array($_tmp)) {
            $_rep = array();
            foreach ($_tmp as $line) {
                list($key, $val) = explode('|', $line);
                $_rep[$key] = trim($val);
            }
            return str_replace(array_keys($_rep), $_rep, $_data);
        }
    }
    return $_data;
}
