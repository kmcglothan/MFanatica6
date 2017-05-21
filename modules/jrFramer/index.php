<?php
/**
 * Jamroom 5 iFrame Control module
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

//------------------------------
// browse
//------------------------------
function view_jrFramer_browse($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrFramer', 'browse');

    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }
    $tbl = jrCore_db_table_name('jrFramer', 'domain');
    $req = "SELECT * FROM {$tbl} ORDER BY dm_time DESC";
    $_rt = jrCore_db_paged_query($req, $_post['p'], 12, 'NUMERIC');

    // start our html output
    jrCore_page_banner('allowed iframe domains');
    jrCore_get_form_notice();

    $dat             = array();
    $dat[1]['title'] = 'domain';
    $dat[1]['width'] = '65%;';
    $dat[2]['title'] = 'updated';
    $dat[2]['width'] = '30%;';
    $dat[3]['title'] = 'delete';
    $dat[3]['width'] = '5%;';
    jrCore_page_table_header($dat);
    unset($dat);

    if (isset($_rt['_items']) && is_array($_rt['_items'])) {

        // Each Entry
        foreach ($_rt['_items'] as $_dm) {

            $dat             = array();
            $dat[1]['title'] = $_dm['dm_name'];
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = jrCore_format_time($_dm['dm_time']);
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = jrCore_page_button("d{$_dm['dm_id']}", 'delete', "if (confirm('Are you sure you want to delete this domain?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/domain_delete_save/id={$_dm['dm_id']}/p={$_post['p']}') }");
            $dat[3]['class'] = 'center';
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = '<p>There are no configured iframe domains</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    $_tmp = array(
        'submit_value'     => 'create new allowed domain',
        'cancel'           => 'referrer',
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // New Domain
    $_tmp = array(
        'name'     => 'dm_name',
        'label'    => 'domain name',
        'help'     => 'Enter a new domain that will be allowed as a SRC value in iframes used on your site',
        'type'     => 'text',
        'validate' => 'domain'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// browse_save
//------------------------------
function view_jrFramer_browse_save($_post, &$_user, &$_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    $tbl = jrCore_db_table_name('jrFramer', 'domain');
    $req = "INSERT INTO {$tbl} (dm_time, dm_name)
            VALUES (UNIX_TIMESTAMP(),'" . jrCore_db_escape($_post['dm_name']) . "')
            ON DUPLICATE KEY UPDATE dm_time = UNIX_TIMESTAMP()";
    jrCore_db_query($req);
    jrCore_set_form_notice('success', 'The domain has been successfully created - reset caches to activate the new domain');
    jrCore_form_delete_session();
    jrCore_form_result('referrer');
    return true;
}

//------------------------------
// domain_delete_save
//------------------------------
function view_jrFramer_domain_delete_save($_post, &$_user, &$_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid domain id');
        jrCore_form_result('referrer');
    }
    $tbl = jrCore_db_table_name('jrFramer', 'domain');
    $req = "DELETE FROM {$tbl} WHERE dm_id = '" . intval($_post['id']) . "' LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if (isset($cnt) && $cnt === 1) {
        jrCore_set_form_notice('success', 'The domain was successfully deleted - reset caches to activate the changes');
        jrCore_form_result('referrer');
    }
    jrCore_set_form_notice('error', 'An error was encountered deleting the domain - please try again');
    jrCore_form_result();
}
