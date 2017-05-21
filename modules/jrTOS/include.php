<?php
/**
 * Jamroom Terms of Service module
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
 * jrTOS_meta
 */
function jrTOS_meta()
{
    $_tmp = array(
        'name'        => 'Terms of Service',
        'url'         => 'quotatos',
        'version'     => '1.0.4',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Adds Terms of Service display and acknowledgement for configured Quotas',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2952/quota-terms-of-service',
        'category'    => 'users',
        'license'     => 'mpl',
        'require'     => 'jrPage'
    );
    return $_tmp;
}

/**
 * jrTOS_init
 */
function jrTOS_init()
{
    // We're going to listen to the login success listener to
    // display any required terms of service
    jrCore_register_event_listener('jrUser', 'login_success', 'jrTOS_login_success_listener');

    // events
    jrCore_register_event_trigger('jrTOS', 'tos_agreed', 'Fired when the user accepts the Terms Of Service');
    return true;
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Display a Terms of Service to a user on successful login
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrTOS_login_success_listener($_data, $_user, $_conf, $_args, $event)
{
    if (!jrUser_is_master() && isset($_user['quota_jrTOS_show_tos']) && jrCore_checktype($_user['quota_jrTOS_show_tos'], 'number_nz')) {
        $pid = (int) $_user['quota_jrTOS_show_tos'];
        $_pg = jrCore_db_get_item('jrPage', $pid, true);
        if (is_array($_pg)) {
            if (!isset($_user["user_jrTOS_{$pid}_agreed"]) || $_user["user_jrTOS_{$pid}_agreed"] != $_pg['_updated']) {
                $murl = jrCore_get_module_url('jrTOS');
                jrCore_location("{$_conf['jrCore_base_url']}/{$murl}/view_tos/{$_user['quota_jrTOS_show_tos']}");
            }
        }
    }
    return $_data;
}

/**
 * Get Terms of Service pages from the page module
 */
function jrTOS_get_tos_pages()
{
    $_sc = array(
        'search'        => array(
            "page_title_url like %terms%",
            "page_location = 0"
        ),
        'return_keys'   => array('_item_id', 'page_title'),
        'skip_triggers' => true,
        'limit'         => 100
    );
    $_pg = jrCore_db_search_items('jrPage', $_sc);
    if (is_array($_pg) && is_array($_pg['_items'])) {
        $_out = array(
            '0' => 'Do not require agreement to Terms of Service'
        );
        foreach ($_pg['_items'] as $_p) {
            $_out["{$_p['_item_id']}"] = $_p['page_title'];
        }
        return $_out;
    }
    return false;
}
