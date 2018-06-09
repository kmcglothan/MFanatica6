<?php
/**
 * Jamroom 5 Proxima Stats module
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
 * jrProximaStat_server_browser
 */
function view_jrProximaStat_view_graph($_post, $_user, $_conf)
{
    global $_mods;
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrProximaStat', 'view_graph');

    if (!isset($_post['name'])) {
        $_post['name'] = 'response_time';
    }
    if (!isset($_post['days']) || !jrCore_checktype($_post['days'], 'number_nz')) {
        $_post['days'] = 1;
    }

    // Figure our base URL
    $url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/view_graph";

    // Gather up available charts
    $tmp = false;
    $_tm = jrCore_get_registered_module_features('jrGraph', 'graph_config');
    if ($_tm && is_array($_tm)) {
        $tmp = '<select class="form_select form_select_item_jumper" onchange="var v=this.options[this.selectedIndex].value; jrCore_window_location(\'' . $url . "/name='+ v)\">";
        foreach ($_tm as $m => $_fnc) {
            if (strpos($m, 'jrProxima') === 0) {
                foreach ($_fnc as $name => $_inf) {
                    if ($name == $_post['name']) {
                        $tmp .= '<option value="' . $name . '" selected="selected"> ' . $_inf['title'] . "</option>\n";
                    }
                    else {
                        $tmp .= '<option value="' . $name . '"> ' . $_inf['title'] . "</option>\n";
                    }
                }
            }
        }
        $tmp .= '</select>';
    }
    jrCore_page_banner('stat browser', $tmp);

    $url = "{$url}/name={$_post['name']}";
    $_td = array('mod', 'mtd', 'days');
    foreach ($_td as $opt) {
        if (isset($_post[$opt])) {
            $url .= "/{$opt}=" . urlencode($_post[$opt]);
        }
    }

    // Our jumpers
    $tmp = '<div class="item form_select_jumper" style="margin-top:0">
    <select class="form_select form_graph_select" onchange="var v=this.options[this.selectedIndex].value; jrCore_window_location(\'' . jrCore_strip_url_params($url, array('mod')) . "/mod='+ v)\">";

    if (!isset($_post['mod']) || !isset($_mods["{$_post['mod']}"])) {
        $tmp .= '<option value="" selected="selected"> All Proxima Modules</option>';
    }
    else {
        $tmp .= '<option value="' . $_post['mod'] . '" selected="selected"> ' . $_mods["{$_post['mod']}"]['module_name'] . "</option>\n";
    }
    foreach ($_mods as $dir => $_inf) {
        if ($dir != $_post['mod'] && (function_exists("{$dir}_px_method_post") || function_exists("{$dir}_px_method_get") || function_exists("{$dir}_px_method_put") || function_exists("{$dir}_px_method_delete"))) {
            if (isset($_post['mod']) && $_post['mod'] == $dir) {
                $tmp .= '<option value="' . $dir . '" selected="selected"> ' . $_mods[$dir]['module_name'] . "</option>\n";
            }
            else {
                $tmp .= '<option value="' . $dir . '"> ' . $_mods[$dir]['module_name'] . "</option>\n";
            }
        }
    }
    $tmp .= '</select>&nbsp;&nbsp;';
    $tmp .= '<select class="form_select form_graph_select" onchange="var v=this.options[this.selectedIndex].value; jrCore_window_location(\'' . jrCore_strip_url_params($url, array('days')) . "/days='+ v)\">";
    foreach (array(1, 2, 3, 4, 5, 6, 7, 14, 21, 28) as $day) {
        $tag = 'days';
        if ($day == 1) {
            $tag = 'day';
        }
        if (isset($_post['days']) && $_post['days'] == $day) {
            $tmp .= '<option value="' . $day . '" selected="selected"> ' . $day . " {$tag}</option>\n";
        }
        else {
            $tmp .= '<option value="' . $day . '"> ' . $day . " {$tag}</option>\n";
        }
    }
    $tmp .= '</select>&nbsp;&nbsp;';
    $tmp .= '<select class="form_select form_graph_select" onchange="var v=this.options[this.selectedIndex].value; jrCore_window_location(\'' . jrCore_strip_url_params($url, array('mtd')) . "/mtd='+ v)\">";
    $_mt = array(
        'all'    => 'All Request Methods',
        'post'   => 'POST Requests',
        'get'    => 'GET Requests',
        'put'    => 'PUT Requests',
        'delete' => 'DELETE Requests'
    );
    foreach ($_mt as $mtd => $dsc) {
        if (isset($_post['mtd']) && $_post['mtd'] == $mtd) {
            $tmp .= '<option value="' . $mtd . '" selected="selected"> ' . $dsc . "</option>\n";
        }
        else {
            $tmp .= '<option value="' . $mtd . '"> ' . $dsc . "</option>\n";
        }
    }
    $tmp .= '</select></div>';
    jrCore_page_custom($tmp);

    $_post['width']  = '100%';
    $_post['height'] = '300px';
    $temp            = new stdClass();
    $html            = smarty_function_jrGraph_embed($_post, $temp);
    jrCore_page_custom($html);
    jrCore_page_display();
}
