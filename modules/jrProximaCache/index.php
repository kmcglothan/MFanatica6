<?php
/**
 * Jamroom 5 Proxima Memcache module
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
 * @copyright 2014 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrProximaCache_server_browser
 */
function view_jrProximaCache_server_browser($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrProximaCache', 'server_browser');
    jrCore_page_banner('memcache servers');

    $tbl = jrCore_db_table_name('jrProximaCache', 'server');
    $req = "SELECT * FROM {$tbl} ORDER BY server_host ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');

    $dat             = array();
    $dat[1]['title'] = 'server host name / IP address';
    $dat[1]['width'] = '55%';
    $dat[2]['title'] = 'server port';
    $dat[2]['width'] = '20%';
    $dat[3]['title'] = 'active';
    $dat[3]['width'] = '10%';
    $dat[4]['title'] = 'stats';
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = 'modify';
    $dat[5]['width'] = '5%';
    $dat[6]['title'] = 'delete';
    $dat[6]['width'] = '5%';
    jrCore_page_table_header($dat);

    $pass = jrCore_get_option_image('pass');
    $fail = jrCore_get_option_image('fail');

    if (is_array($_rt)) {
        foreach ($_rt as $k => $_server) {
            $dat             = array();
            $dat[1]['title'] = '<h3>' . $_server['server_host'] . '</h3>';
            $dat[2]['title'] = $_server['server_port'];
            $dat[2]['class'] = 'center';
            if ($_server['server_active'] == 'on') {
                $dat[3]['title'] = $pass;
            }
            else {
                $dat[3]['title'] = $fail;
            }
            $dat[3]['class'] = 'center';
            if ($_server['server_active'] == 'on') {
                $dat[4]['title'] = jrCore_page_button("s-mc-{$k}", 'stats', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/server_stats/id={$_server['server_id']}')");
            }
            else {
                $dat[4]['title'] = jrCore_page_button("s-mc-{$k}", 'stats', 'disabled');
            }
            $dat[5]['title'] = jrCore_page_button("a-mc-{$k}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/server_update/id={$_server['server_id']}')");
            $dat[6]['title'] = jrCore_page_button("d-mc-{$k}", 'delete', "if(confirm('Are you sure you want to delete this server?')) { jrCore_window_Location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/server_delete_save/id={$_server['server_id']}') }");
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>No Memcache servers have been created yet - create a new one below.</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    jrCore_page_section_header('create a new server');
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value'     => 'create new server',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // Server Name
    $_tmp = array(
        'name'     => 'server_host',
        'label'    => 'server ip address',
        'help'     => 'Enter the IP Address or Host for this memcache server',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Server Port
    $_tmp = array(
        'name'     => 'server_port',
        'label'    => 'server port',
        'help'     => 'Enter the port the Memcached server is running on',
        'type'     => 'text',
        'validate' => 'number_nz',
        'default'  => 11211,
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

/**
 * jrProximaCache_server_save
 */
function view_jrProximaCache_server_browser_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Make sure this server does not already exist
    $server = jrCore_db_escape($_post['server_host']);
    $port   = (int) $_post['server_port'];
    $tbl    = jrCore_db_table_name('jrProximaCache', 'server');
    $req    = "SELECT server_id FROM {$tbl} WHERE server_host = '{$server}' AND server_port = '{$port}' LIMIT 1";
    $_rt    = jrCore_db_query($req, 'SINGLE');
    if (is_array($_rt)) {
        jrCore_set_form_notice('error', 'There is already server using that IP and Port - please enter unique server info');
        jrCore_form_result();
    }
    $req = "INSERT INTO {$tbl} (server_host, server_port, server_active) VALUES ('{$server}', '{$port}', 'off')";
    $aid = jrCore_db_query($req, 'INSERT_ID');
    if ($aid) {
        jrCore_set_form_notice('success', 'The new server was successfully created!<br>Make sure and <strong>modify</strong> the server to make it active!', false);
        jrCore_form_delete_session();
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered creating the server - please try again');
    }
    jrCore_form_result();
}

/**
 * jrProximaCache_server_stats
 */
function view_jrProximaCache_server_stats($_post, $_user, $_conf)
{
    jrUser_master_only();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid server id');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrProximaCache', 'server');
    $req = "SELECT * FROM {$tbl} WHERE server_id = '{$_post['id']}' LIMIT 1";
    $_ap = jrCore_db_query($req, 'SINGLE');
    if (!is_array($_ap)) {
        jrCore_set_form_notice('error', 'invalid server id');
        jrCore_location('referrer');
    }
    if ($_ap['server_active'] != 'on') {
        jrCore_set_form_notice('notice', 'This Memcache server is not active');
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrProximaCache', 'server_browser');
    jrCore_page_banner('Memcache stats', jrCore_page_button('r', 'refresh', "window.location.reload()"));
    jrCore_get_form_notice();

    $dat             = array();
    $dat[1]['title'] = 'item';
    $dat[1]['width'] = '30%';
    $dat[2]['title'] = 'value';
    $dat[2]['width'] = '70%';
    jrCore_page_table_header($dat);

    $mc = jrProximaCache_connect();
    if (!$mc) {
        $dat             = array();
        $dat[1]['title'] = 'Unable to connect to configured Memcached Server - verify server is online';
        $dat[1]['class'] = 'p10 center';
        jrCore_page_table_row($dat);
    }
    else {
        $_rt = $mc->getStats();
        $svr = "{$_ap['server_host']}:{$_ap['server_port']}";
        if (is_array($_rt) && is_array($_rt[$svr])) {
            foreach ($_rt[$svr] as $k => $v) {
                $dat             = array();
                $dat[1]['title'] = $k;
                $dat[1]['class'] = 'right';
                $dat[2]['title'] = $v;
                jrCore_page_table_row($dat);
            }
        }
        jrCore_page_table_footer();
    }
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/server_browser", 'continue');
    jrCore_page_display();
}

/**
 * jrProximaCache_server_update
 */
function view_jrProximaCache_server_update($_post, $_user, $_conf)
{
    jrUser_master_only();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid server id');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrProximaCache', 'server');
    $req = "SELECT * FROM {$tbl} WHERE server_id = '{$_post['id']}' LIMIT 1";
    $_ap = jrCore_db_query($req, 'SINGLE');
    if (!is_array($_ap)) {
        jrCore_set_form_notice('error', 'invalid server id');
        jrCore_location('referrer');
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrProximaCache', 'server_browser');
    jrCore_page_banner('modify server');

    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/server_browser",
        'values'       => $_ap
    );
    jrCore_form_create($_tmp);

    // ID
    $_tmp = array(
        'name'  => 'id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    // Host
    $_tmp = array(
        'name'     => 'server_host',
        'label'    => 'server host',
        'help'     => 'Enter the Host Name or IP Address for this Memcache server',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Server Port
    $_tmp = array(
        'name'     => 'server_port',
        'label'    => 'server port',
        'help'     => 'Enter the port the Memcached server is running on',
        'type'     => 'text',
        'validate' => 'number_nz',
        'default'  => 11211,
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Active
    $_tmp = array(
        'name'     => 'server_active',
        'label'    => 'server active',
        'help'     => 'Check this box to make this server active',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

/**
 * Save Server update
 */
function view_jrProximaCache_server_update_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid server id');
        jrCore_form_result();
    }
    $tbl = jrCore_db_table_name('jrProximaCache', 'server');
    $req = "SELECT * FROM {$tbl} WHERE server_id = '{$_post['id']}' LIMIT 1";
    $_ap = jrCore_db_query($req, 'SINGLE');
    if (!is_array($_ap)) {
        jrCore_set_form_notice('error', 'invalid server id');
        jrCore_form_result();
    }
    $nam = jrCore_db_escape($_post['server_host']);
    $prt = (int) $_post['server_port'];
    $act = jrCore_db_escape($_post['server_active']);
    $req = "UPDATE {$tbl} SET server_host = '{$nam}', server_port = '{$prt}', server_active = '{$act}' WHERE server_id = '{$_post['id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        // See if we need to reset server list
        if ($_ap['server_active'] != $_post['server_active']) {
            jrCore_set_setting_value('jrProximaCache', 'cache_reload', time());
        }
        jrCore_set_form_notice('success', 'The Server has been successfully updated');
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/server_browser");
    }
    jrCore_set_form_notice('error', 'An error was encountered updating the Server - please try again');
    jrCore_form_result();
}

/**
 * jrProximaCache_server_delete_save
 */
function view_jrProximaCache_server_delete_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid server id');
        jrCore_location('referrer');
    }
    $tbl = jrCore_db_table_name('jrProximaCache', 'server');
    $req = "SELECT * FROM {$tbl} WHERE server_id = '{$_post['id']}' LIMIT 1";
    $_ap = jrCore_db_query($req, 'SINGLE');
    if (!is_array($_ap)) {
        jrCore_set_form_notice('error', 'invalid server id');
        jrCore_location('referrer');
    }
    $req = "DELETE FROM {$tbl} WHERE server_id = '{$_post['id']}' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        jrCore_set_form_notice('success', 'The Server has been successfully deleted');
    }
    else {
        jrCore_set_form_notice('error', 'An error was encountered deleting the Server - please try again');
    }
    jrCore_location('referrer');
}

