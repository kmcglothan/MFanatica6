<?php
/**
 * Jamroom Advanced Charts module
 *
 * copyright 2017 The Jamroom Network
 *
 * This Jamroom file is LICENSED SOFTWARE, and cannot be redistributed.
 *
 * This Source Code is subject to the terms of the Jamroom Network
 * Commercial License -  please see the included "license.html" file.
 *
 * This module may include works that are not developed by
 * The Jamroom Network
 * and are used under license - any licenses are included and
 * can be found in the "contrib" directory within this module.
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
// get_fields
//------------------------------
function view_jrCharts_get_fields($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrCharts');
    jrCore_page_banner('available chart fields');
    jrCore_get_form_notice();

    $dat = array();
    $dat[1]['title'] = 'module';
    $dat[1]['width'] = '30%;';
    $dat[2]['title'] = 'template chart_field name';
    $dat[2]['width'] = '55%;';
    $dat[3]['title'] = 'number items';
    $dat[3]['width'] = '15%;';
    jrCore_page_table_header($dat);

    // Get modules with a datastore
    $_ds = jrCore_get_datastore_modules();
    if (isset($_ds) && is_array($_ds)) {
        foreach ($_ds as $module => $prefix) {
            if (jrCore_db_get_prefix($module)) {
                $tbl = jrCore_db_table_name($module, 'item_key');
                $req = "SELECT `key`, COUNT(`key`) AS kcount FROM {$tbl} WHERE `key` LIKE '%_count' GROUP BY `key` ORDER BY `key` ASC";
                $_rt = jrCore_db_query($req, 'key', false, 'kcount');
                if (isset($_rt) && is_array($_rt)) {
                    foreach ($_rt as $k => $v) {
                        $dat             = array();
                        $dat[1]['title'] = $_mods[$module]['module_name'];
                        $dat[1]['class'] = 'center';
                        $dat[2]['title'] = $k;
                        $dat[2]['class'] = 'center';
                        $dat[3]['title'] = $v;
                        $dat[3]['class'] = 'center';
                        jrCore_page_table_row($dat);
                    }
                }
            }
        }
    }
    else {
        $dat = array();
        $dat[1]['title'] = '<p>No Fields found that can be charted!</p>';
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools");
    jrCore_page_display();
}
