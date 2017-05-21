<?php
/**
 * Jamroom 5 Support Center module
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

//------------------------------
// options
//------------------------------
function view_jrSupport_options($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    if (!isset($_post['_2'])) {
        $_rs = array('error' => 'Invalid module or skin - please try again');
        jrCore_json_response($_rs);
    }
    $item = $_post['_2'];
    switch ($_post['_1']) {
        case 'module':
            if (!isset($_mods[$item])) {
                $_rs = array('error' => 'Invalid module - does not seem to be active in the system');
                jrCore_json_response($_rs);
            }
            break;
        case 'skin':
            $_skins = jrCore_get_skins();
            if (!isset($_skins[$item])) {
                $_rs = array('error' => 'Invalid skin - does not seem to be active in the system');
                jrCore_json_response($_rs);
            }
            break;
        default:
            $_rs = array('error' => 'Invalid item type - please try again');
            jrCore_json_response($_rs);
            break;
    }

    // Send off request
    if (isset($_conf['jrSupport_support_email']) && strpos($_conf['jrSupport_support_email'], '@')) {
        $_dt = array(
            'email' => (isset($_conf['jrSupport_support_email'])) ? jrCore_url_encode_string($_conf['jrSupport_support_email']) : 'null',
            'type'  => $_post['_1'],
            'item'  => $item
        );
        // Are we on a different active system?
        $url = 'https://www.jamroom.net';
        if (jrCore_module_is_active('jrMarket')) {
            $_mkt = jrMarket_get_active_release_system();
            if ($_mkt && is_array($_mkt) && isset($_mkt['system_url']) && jrCore_checktype($_mkt['system_url'], 'url')) {
                $url = $_mkt['system_url'];
            }
        }
        $_rs = jrCore_load_url("{$url}/networksupport/options", $_dt, 'POST');
        if ($_rs && strpos($_rs, '{') === 0) {
            $_rs = json_decode($_rs, true);
            return jrCore_parse_template('options.tpl', $_rs, 'jrSupport');
        }
        return '<div class="p10 error">error communicating with support server - check Support URL</div>';
    }
    $_rs = array('error' => "Make sure you have entered your Email Address in <a href=\"{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/global\"><u>Global Config</u></a> to see all support options available.");
    return jrCore_parse_template('options.tpl', $_rs, 'jrSupport');

}

//------------------------------
// index
//------------------------------
function view_jrSupport_index($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrSupport', 'index');
    // Make sure we got a good JR Network email address
    if (!isset($_conf['jrSupport_support_email']) || !jrCore_checktype($_conf['jrSupport_support_email'], 'email')) {
        jrCore_page_banner('Help and Support');
        jrCore_set_form_notice('error', "Make sure you have entered your Email Address in <a href=\"{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/global\"><u>Global Config</u></a> to see all support options available.", false);
    }
    else {
        jrCore_page_banner('Help and Support', 'Marketplace Account: ' . htmlentities($_conf['jrSupport_support_email']));
    }
    jrCore_get_form_notice();

    $_tmpm = array();
    foreach ($_mods as $mod_dir => $_info) {
        $_tmpm[$mod_dir] = $_info['module_name'];
    }
    asort($_tmpm);
    $_temp = array();
    foreach ($_tmpm as $mod_dir => $mod_name) {
        $_temp[$mod_dir] = $_mods[$mod_dir];
    }

    $_skins = jrCore_get_skins();
    $_smeta = array();
    $_tmpm  = array();
    foreach ($_skins as $skin) {
        $_smeta[$skin] = jrCore_skin_meta_data($skin);
        $_tmpm[$skin]  = (isset($_smeta[$skin]['title'])) ? $_smeta[$skin]['title'] : $_smeta[$skin]['name'];
    }
    asort($_tmpm);
    $_skin = array();
    foreach ($_tmpm as $skin_dir => $skin_name) {
        $_skin[$skin_dir] = $skin_name;
    }
    unset($_tmpm);

    $_rp = array(
        '_modules' => $_temp,
        '_skins'   => $_skin
    );

    $html = jrCore_parse_template('index.tpl', $_rp, 'jrSupport');
    jrCore_page_custom($html);
    jrCore_page_display();
}

//------------------------------
// support (magic view)
//------------------------------
function view_jrSupport_support($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();

    jrCore_page_include_admin_menu();
    if (isset($_post['skin'])) {
        // skin "Help and Support"
        jrCore_page_skin_tabs($_post['skin'], 'support');
    }
    else {
        jrCore_page_admin_tabs($_post['module'], 'support');
    }
    jrCore_get_form_notice();

    // Make sure we got a good JR Network email address
    if (!isset($_conf['jrSupport_support_email']) || !jrCore_checktype($_conf['jrSupport_support_email'], 'email')) {

        // See if we can grab it from the Marketplace
        if (jrCore_module_is_active('jrMarket')) {
            $_mkt = jrMarket_get_active_release_system();
            if ($_mkt && is_array($_mkt) && isset($_mkt['system_email']) && jrCore_checktype($_mkt['system_email'], 'email')) {
                jrCore_set_setting_value('jrSupport', 'support_email', $_mkt['system_email']);
            }
            jrCore_delete_config_cache();
            $_conf['jrSupport_support_email'] = $_mkt['system_email'];
        }
        jrCore_page_banner('Help and Support');
    }
    else {
        // Setup our module jumper
        $_mds = array();
        foreach ($_mods as $mod_dir => $_info) {
            if (jrCore_module_is_active($mod_dir)) {
                $_mds[] = $mod_dir;
            }
        }
        $temp = jrCore_get_module_jumper('module_jumper', $_post['module'], "jrCore_window_location('{$_conf['jrCore_base_url']}/'+ $(this).val() + '/support')", $_mds);
        jrCore_page_banner('Help and Support', $temp);
    }

    if (isset($_post['skin'])) {
        $_mta = jrCore_skin_meta_data($_post['skin']);
        $_rp  = array(
            'skin' => $_post['skin'],
            'icon' => jrCore_get_skin_icon_html($_post['skin'], 164)
        );
        $ttl  = $_mta['title'];
        jrCore_page_title("Help - " . $_mta['title']);
    }
    else {
        if (!jrCore_module_is_active($_post['module'])) {
            jrCore_page_notice('error', 'Module is not active');
        }
        $_rp = array(
            'module' => $_post['module'],
            'icon'   => jrCore_get_module_icon_html($_post['module'], 164)
        );
        $ttl = $_mods["{$_post['module']}"]['module_name'];
    }
    $html = jrCore_parse_template('support.tpl', $_rp, 'jrSupport');
    jrCore_page_custom($html);
    jrCore_page_title("Help - " . $ttl);
    jrCore_page_display();
}
