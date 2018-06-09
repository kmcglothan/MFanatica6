<?php
/**
 * Jamroom Support Center module
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
function jrSupport_meta()
{
    $_tmp = array(
        'name'        => 'Support Center',
        'url'         => 'support',
        'version'     => '1.1.3',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Documentation, Support and Help for modules and skins in your system',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2867/support-center',
        'category'    => 'core',
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrSupport_init()
{
    // Our CSS and JS
    jrCore_register_module_feature('jrCore', 'css', 'jrSupport', 'jrSupport.css');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrSupport', 'jrSupport.js', 'admin');

    // Custom Support Tab
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrSupport', 'index', 'Help');

    // Our default master view
    jrCore_register_module_feature('jrCore', 'default_admin_view', 'jrSupport', 'index');

    // Magic view tab for ACP for other modules
    jrCore_register_module_feature('jrCore', 'magic_view', 'jrSupport', 'support', 'view_jrSupport_support');

    // listeners
    jrCore_register_event_listener('jrCore', 'admin_tabs', 'jrCore_admin_tabs_listener');
    jrCore_register_event_listener('jrCore', 'skin_tabs', 'jrCore_skin_tabs_listener');


    jrCore_register_module_feature('jrTips', 'tip', 'jrSupport', 'tip');

    return true;
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------
/**
 * Add a "Help and Support" tab to each modules ACP options
 * @param $_data
 * @param $_user
 * @param $_conf
 * @param $_args
 * @param $event
 * @return mixed
 */
function jrCore_admin_tabs_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if ($_post['module'] == 'jrSupport' || !jrCore_module_is_active($_post['module'])) {
        return $_data;
    }
    $url              = jrCore_get_module_url($_post['module']);
    $_data['support'] = array(
        'label' => 'Help',
        'url'   => "{$_conf['jrCore_base_url']}/{$url}/support"
    );
    if ($_args['active'] == 'support') {
        $_data['support']['active'] = true;
    }
    return $_data;
}

/**
 * Add a "Help and Support" tab to each skins ACP options
 * @param $_data
 * @param $_user
 * @param $_conf
 * @param $_args
 * @param $event
 * @return mixed.
 */
function jrCore_skin_tabs_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (!isset($_post['skin'])) {
        $_post['skin'] = $_conf['jrCore_active_skin'];
    }
    $url              = jrCore_get_module_url('jrSupport');
    $_data['support'] = array(
        'label' => 'Help',
        'url'   => "{$_conf['jrCore_base_url']}/{$url}/support/skin={$_post['skin']}"
    );
    if ($_args['active'] == 'support') {
        $_data['support']['active'] = true;
    }
    return $_data;
}

