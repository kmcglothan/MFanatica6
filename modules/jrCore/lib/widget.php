<?php
/**
 * Jamroom System Core module
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
 * @package Widgets
 * @copyright 2015 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Display CONFIG screen for Item List widget
 * @param $_post array Post info
 * @param $_user array User array
 * @param $_conf array Global Config
 * @param $_wg array Widget info
 * @return bool
 */
function jrCore_widget_list_config($_post, $_user, $_conf, $_wg)
{
    global $_mods;

    // module
    $_opt = jrCore_get_datastore_modules();
    foreach ($_opt as $mod => $url) {
        if (!jrCore_module_is_active($mod)) {
            unset($_opt[$mod]);
            continue;
        }
        switch ($mod) {
            // Some modules we don't support or they support themselves
            case 'jrSeamless':
            case 'jrSmiley':
                unset($_opt[$mod]);
                break;

            default:
                if (is_file(APP_DIR . "/modules/{$mod}/templates/item_list.tpl")) {
                    $_opt[$mod] = $_mods[$mod]['module_name'];
                }
                else {
                    unset($_opt[$mod]);
                }
        }
    }
    $_opt['_'] = '- select a module -';
    natcasesort($_opt);

    $_tmp = array(
        'name'     => 'list_module',
        'label'    => 'List Module',
        'help'     => 'Select the module whos items you want to list',
        'options'  => $_opt,
        'onchange' => 'jrCore_widget_list_get_module_info(this)',
        'type'     => 'select',
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'  => 'active_module',
        'type'  => 'hidden',
        'value' => 'none'
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_section_header('list options');

    if (isset($_wg['list_order_by'])) {
        list($obv, $obd) = explode(' ', $_wg['list_order_by']);
    }
    if (isset($_wg['list_group_by']) && strlen($_wg['list_group_by']) > 0) {
        $gby = $_wg['list_group_by'];
    }
    else {
        $gby = '';
    }

    $_sel = array();
    $_opt = array();
    $_obs = array();
    $_ogs = array();
    $_ops = array(0 => array(), 1 => array(), 2 => array());
    $_val = array(0 => '', 1 => '', 2 => '');
    if (isset($_wg['list_module'])) {
        foreach (array(0, 1, 2) as $k) {
            if (isset($_wg["list_search{$k}"]{1})) {
                list($_sel[$k], $_opt[$k], $_val[$k]) = explode(' ', $_wg["list_search{$k}"], 3);
                if (strlen($_opt[$k]) > 0) {
                    switch ($_opt[$k]) {
                        case '=':
                            $_opt[$k] = 'eq';
                            break;
                        case '!=':
                            $_opt[$k] = 'neq';
                            break;
                        case '<':
                            $_opt[$k] = 'lt';
                            break;
                        case '>':
                            $_opt[$k] = 'gt';
                            break;
                        case 'like':
                            // We use LIKE for "contains", "begins with" and "ends with"
                            if (strpos($_val[$k], '%') === 0 && substr($_val[$k], -1, 1) === '%') {
                                $_opt[$k] = 'like';
                            }
                            elseif (substr($_val[$k], -1, 1) === '%') {
                                $_opt[$k] = 'bw';
                            }
                            elseif (strpos($_val[$k], '%') === 0) {
                                $_opt[$k] = 'ew';
                            }
                            $_val[$k] = trim(trim($_val[$k]), '%');
                            break;
                    }
                }
            }
        }
        $mod = $_wg['list_module'];
        if (jrCore_module_is_active($mod)) {
            $_fl = jrCore_db_get_unique_keys($mod);
            if ($_fl && is_array($_fl)) {
                switch ($mod) {
                    case 'jrUser':
                    case 'jrProfile':
                        break;
                    default:
                        $_fl['_item_id'] = '_item_id';
                        break;
                }
                sort($_fl);
                $_ogs[] = "<option value=\"\"> </option>";
                $_obs[] = "<option value=\"\"> </option>";
                foreach (array(0, 1, 2) as $k) {
                    $_ops[$k][] = "<option value=\" \"> </option>";
                }
                foreach ($_fl as $fld) {
                    if (strpos($fld, '_file_')) {
                        continue;
                    }
                    foreach (array(0, 1, 2) as $k) {
                        if (isset($_sel[$k]) && $_sel[$k] == $fld) {
                            $_ops[$k][] = "<option selected value=\"{$fld}\"> {$fld}</option>";
                        }
                        else {
                            $_ops[$k][] = "<option value=\"{$fld}\"> {$fld}</option>";
                        }
                    }
                    if (isset($obv) && $obv == $fld) {
                        $_obs[] = "<option selected value=\"{$fld}\"> {$fld}</option>";
                    }
                    else {
                        $_obs[] = "<option value=\"{$fld}\"> {$fld}</option>";
                    }
                    if (isset($gby) && $gby == $fld) {
                        $_ogs[] = "<option selected value=\"{$fld}\"> {$fld}</option>";
                    }
                    else {
                        $_ogs[] = "<option value=\"{$fld}\"> {$fld}</option>";
                    }
                }
            }
        }
    }

    // Options
    $_sop = array(
        'eq'          => 'is equal to',
        'neq'         => 'does not equal',
        'lt'          => 'is less than',
        'gt'          => 'is greater than',
        'like'        => 'contains',
        'bw'          => 'begins with',
        'ew'          => 'ends with',
        'not_like'    => 'does not contain',
        'in'          => 'is in comma list',
        'not_in'      => 'is not in comma list',
        'regexp'      => 'matches expression',
        'between'     => 'is between',
        'not_between' => 'is not between',
    );
    $_mop = array();
    foreach ($_sop as $sk => $sv) {
        foreach (array(0, 1, 2) as $k) {
            if (isset($_opt[$k]) && $_opt[$k] == $sk) {
                $_mop[$k][] = "<option selected value=\"{$sk}\"> {$sv}</option>";
            }
            else {
                $_mop[$k][] = "<option value=\"{$sk}\"> {$sv}</option>";
            }
        }
    }

    $show = true;
    $_shw = array();
    $_mps = array();
    $_cls = array();
    $_att = array();
    foreach (array(0, 1, 2) as $k) {
        $_ops[$k] = implode("\n", $_ops[$k]);
        $_shw[$k] = '';
        if (isset($_sel[$k]) || ($k == 0 && isset($_wg['list_module']) && strlen($_wg['list_module']) > 0)) {
            $_cls[$k] = '';
            $_att[$k] = '';
        }
        else {
            $_cls[$k] = ' form_element_disabled';
            $_att[$k] = ' disabled="disabled"';
            if ($k > 0) {
                $old = ($k - 1);
                if (!isset($_sel[$old])) {
                    $_shw[$k] = ' style="display:none"';
                }
            }
            else {
                $show = false;
            }
        }
        $_mps[$k] = implode("\n", $_mop[$k]);
    }

    $_obc = array();
    $_obd = array(
        'desc'           => 'descending',
        'asc'            => 'ascending',
        'numerical_desc' => 'descending (numerical)',
        'numerical_asc'  => 'ascending (numerical)',
        'random'         => 'random'
    );
    foreach ($_obd as $k => $v) {
        if (isset($obd) && $obd == $k) {
            $_obc[] = "<option selected value=\"{$k}\"> {$v}</option>";
        }
        else {
            $_obc[] = "<option value=\"{$k}\"> {$v}</option>";
        }
    }

    $html = '
    <tr><td class="element_left form_input_left text_left list_search_element_left"><a id="ff-list_search_key_0"></a>Search Condition 1</td>
    <td class="element_right form_select_right select_right list_search_element_right" style="position:relative">
        <select name="list_search_key[]" class="form_select list_search_key' . $_cls[0] . '" ' . $_att[0] . ' style="width:30%" onchange="var v = $(this).val(); if (v.length > 0) { $(\'#ls2\').slideDown(); }">' . $_ops[0] . '</select>&nbsp;
        <select name="list_search_op[]" class="form_select list_search_op' . $_cls[0] . '" style="width:17%">' . $_mps[0] . '</select>&nbsp;
        <input type="text" name="list_search_val[]" value="' . $_val[0] . '" class="form_text list_search_text' . $_cls[0] . '" style="width:25%">
        <input type="button" value="?" class="form_button form_help_button" title="expand help" onclick="$(\'#h_list_search\').slideToggle(250);">
    </td></tr>
    <tr id="ls2" ' . $_shw[1] . '><td class="element_left form_input_left text_left list_search_element_left"><a id="ff-list_search_key_1"></a>Search Condition 2</td>
    <td class="element_right form_select_right select_right list_search_element_right" style="position:relative">
        <select name="list_search_key[]" class="form_select list_search_key" style="width:30%" onchange="var v = $(this).val(); if (v.length > 0) { $(\'#ls3\').slideDown(); }">' . $_ops[1] . '</select>&nbsp;
        <select name="list_search_op[]" class="form_select list_search_op" style="width:17%">' . $_mps[1] . '</select>&nbsp;
        <input type="text" name="list_search_val[]" value="' . $_val[1] . '" class="form_text list_search_text" style="width:25%">
    </td></tr>
    <tr id="ls3" ' . $_shw[2] . '><td class="element_left form_input_left text_left list_search_element_left"><a id="ff-list_search_key_2"></a>Search Condition 3</td>
    <td class="element_right form_select_right select_right list_search_element_right" style="position:relative">
        <select name="list_search_key[]" class="form_select list_search_key" style="width:30%">' . $_ops[2] . '</select>&nbsp;
        <select name="list_search_op[]" class="form_select" style="width:17%">' . $_mps[2] . '</select>&nbsp;
        <input type="text" name="list_search_val[]" value="' . $_val[2] . '" class="form_text" style="width:25%">
    </td></tr>
    <tr><td class="element_left form_input_left" style="padding:0;height:0"></td><td>
    <div id="h_list_search" class="form_help" style="display:none"><table class="form_help_drop"><tr><td class="form_help_drop_left">
        You can add up to 3 search parameters to help create a more focused list of items.<br><br><b>Example:</b> If you wanted to create a list of blog entries from a specific blog_category - i.e. &quot;featured&quot;, you would:<br><br>&bull; Select the "blog_category" as the search key.<br>&bull; Select &quot;is equal to&quot; as the search option.<br>&bull; Enter &quot;featured&quot; (without the quotes) in the search value text field.<br><br><b>Note:</b> When using the &quot;is between&quot; or &quot;is not between&quot; operators enter the low and high numeric values separated by a comma - i.e. &quot;5,15&quot; would find values between (and including) 5 and 15.
    </td></tr></table></div></td></tr>
    ';
    jrCore_page_custom($html);

    $cls = ' form_element_disabled';
    $att = ' disabled="disabled"';
    if ((isset($obv) && strlen($obv) > 0) || strlen($_att[0]) === 0) {
        $cls = '';
        $att = '';
    }
    $html = '<select id="list_order_by_key" name="list_order_by_key" class="form_select list_search_key' . $cls . '"' . $att . ' style="width:30%">' . implode("\n", $_obs) . '</select>&nbsp;
             <select id="list_order_by_dir" name="list_order_by_dir" class="form_select list_search_dir' . $cls . '"' . $att . ' style="width:17%">' . implode("\n", $_obc) . '</select>';
    jrCore_page_custom($html, 'Order By', null, 'If you would like the Item List to be ordered by a specific key, select it here.<br><br><b>Example:</b> To create a list of &quot;newest&quot; items, order by the <b>_item_id</b> key, descending.');

    $_num = array(
        0 => ' '
    );
    foreach (range(1, 100) as $v) {
        $_num[$v] = $v;
    }

    // limit
    $_tmp = array(
        'name'     => 'list_limit',
        'label'    => 'Result Limit',
        'help'     => 'Used to limit the number of items you want to show in the list.',
        'type'     => 'select',
        'options'  => $_num,
        'validate' => 'number_nz',
        'required' => false
    );
    if (!$show) {
        $_tmp['disabled'] = 'disabled';
        $_tmp['class']    = 'form_element_disabled';
    }
    if (!isset($_wg['list_limit'])) {
        $_tmp['default'] = 5; // could be that the user has removed the result limit, don't want it being added back in on update.
    }
    jrCore_form_field_create($_tmp);

    // pagebreak
    $_tmp = array(
        'name'     => 'list_pagebreak',
        'label'    => 'Results per Page',
        'help'     => 'If this list is going to be on a page by itself, enter the number of items that should appear on each page.',
        'type'     => 'select',
        'options'  => $_num,
        'validate' => 'number_nz',
        'required' => false
    );
    if (!$show) {
        $_tmp['disabled'] = 'disabled';
        $_tmp['class']    = 'form_element_disabled';
    }
    if (!isset($_wg['list_pagebreak'])) {
        $_tmp['default'] = 0;
    }
    jrCore_form_field_create($_tmp);

    $cls = ' form_element_disabled';
    $att = ' disabled="disabled"';
    if (isset($_wg['list_group_by']{1}) || strlen($_cls[0]) === 0) {
        $cls = '';
        $att = '';
    }
    $html = '<select id="list_group_by" name="list_group_by" class="form_select ' . $cls . '"' . $att . ' style="width:30%">' . implode("\n", $_ogs) . '</select>';
    jrCore_page_custom($html, 'Group By', null, 'The Group By option is used to limit the type of results shown to only <b>one item</b> per Group By key.<br><br><b>Example:</b> If you wanted to create a list of Blog Categories, you would select the <b>blog_category</b> key here.  This would return one entry for each unique blog_category value that is found in the Blog DataStore.');

    // Get templates
    $rows = 'display:none';
    $_tpl = array();
    if (isset($_wg['list_module'])) {
        $_tmp = jrCore_widget_list_get_module_templates($_wg['list_module']);
        if ($_tmp && is_array($_tmp)) {
            foreach ($_tmp as $k => $v) {
                if (isset($_wg['list_template']) && $_wg['list_template'] == $k) {
                    $_tpl[] = "<option selected value=\"{$k}\"> {$v}</option>";
                }
                else {
                    $_tpl[] = "<option value=\"{$k}\"> {$v}</option>";
                }
            }
        }
        if (isset($_wg['list_template']) && $_wg['list_template'] == 'custom') {
            $rows   = '';
            $_tpl[] = "<option selected value=\"custom\"> custom</option>";
        }
        else {
            $_tpl[] = "<option value=\"custom\"> custom</option>";
        }
    }
    else {
        if (isset($_wg['list_template']) && $_wg['list_template'] == 'custom') {
            $rows   = '';
            $_tpl[] = "<option value=\"item_list.tpl\"> default</option>";
            $_tpl[] = "<option selected value=\"custom\"> custom</option>";
        }
        else {
            $_tpl[] = "<option selected value=\"item_list.tpl\"> default</option>";
            $_tpl[] = "<option value=\"custom\"> custom</option>";
        }
    }

    $html = '<select id="list_template" name="list_template" class="form_select list_template_select ' . $cls . '"' . $att . ' style="width:30%" onchange="if($(this).val() == \'custom\') { $(\'#ff-row-list_custom_template\').slideDown(250, function() { jrSiteBuilder_activate_editor(); } ); } else { $(\'#ff-row-list_custom_template\').slideUp(50); }">' . implode("\n", $_tpl) . '</select>';
    jrCore_page_custom($html, 'Template', null, 'Select the template that will be used for each entry in the output list');

    // custom template
    // #ff-row-list_custom_template { display: none; }
    $_tmp = array(
        'name'      => 'list_custom_template',
        'label'     => 'custom template',
        'sublabel'  => '<a onclick="jrSiteBuilder_load_default_code();">click to load default template</a>',
        'help'      => 'You can provide custom template code that will be used in place of the selected List template.<br><br><b>Note:</b> Must be valid template code',
        'type'      => 'textarea',
        'validate'  => 'not_empty',
        'required'  => false,
        'row_style' => $rows
    );
    jrCore_form_field_create($_tmp);

    $_rep = array();
    $html = jrCore_parse_template('widget_structure.tpl', $_rep, 'jrCore');
    jrCore_page_custom($html);

    return true;
}

/**
 * Get Widget results from posted Config data
 * @param $_post array Post info
 * @return mixed
 */
function jrCore_widget_list_config_save($_post)
{
    global $_conf;
    // check custom list template for errors
    if (isset($_post['list_template']) && $_post['list_template'] == 'custom' && strlen($_post['list_custom_template']) > 0) {
        $err = jrCore_test_template_for_errors($_conf['jrCore_active_skin'], $_post['list_custom_template']);
        if ($err && strpos($err, 'error') === 0) {
            jrCore_set_form_notice('error', substr($err, 7), false);
            return jrCore_form_result();
        }
    }

    $_out = array();
    // Get our search conditions
    if (isset($_post['list_search_val']) && is_array($_post['list_search_val'])) {
        foreach ($_post['list_search_val'] as $k => $v) {
            $v = trim($v);
            if (strlen($v) > 0) {
                switch ($_post['list_search_op'][$k]) {
                    case 'eq':
                        $op = '=';
                        break;
                    case 'neq':
                        $op = '!=';
                        break;
                    case 'lt':
                        $op = '<';
                        break;
                    case 'gt':
                        $op = '>';
                        break;
                    case 'bw':
                        $op = 'like';
                        $v  = trim(trim($v), '%') . '%';
                        break;
                    case 'ew':
                        $op = 'like';
                        $v  = '%' . trim(trim($v), '%');
                        break;
                    case 'like':
                        $op = 'like';
                        $v  = '%' . trim(trim($v), '%') . '%';
                        break;
                    case 'not_like':
                        $op = 'not_like';
                        $v  = '%' . trim(trim($v), '%') . '%';
                        break;
                    default:
                        $op = $_post['list_search_op'][$k];
                        break;
                }
                $_out["list_search{$k}"] = "{$_post['list_search_key'][$k]} {$op} {$v}";
            }
        }
    }
    unset($_post['list_search_key'], $_post['list_search_op'], $_post['list_search_val']);

    // Get order_by
    if (isset($_post['list_order_by_key']) && strlen($_post['list_order_by_key']) > 1) {
        $_post['list_order_by'] = "{$_post['list_order_by_key']} {$_post['list_order_by_dir']}";
    }
    unset($_post['list_order_by_key'], $_post['list_order_by_dir']);

    foreach ($_post as $k => $v) {
        if (strpos($k, 'list_') === 0 && strlen($v) > 0) {
            $_out[$k] = $v;
        }
    }

    // Pager
    if (isset($_out['list_pagebreak']) && jrCore_checktype($_out['list_pagebreak'], 'number_nz')) {
        $_out['list_pager'] = true;
    }

    return $_out;
}

/**
 * HTML Editor Widget DISPLAY
 * @param $_widget array Page Widget settings
 * @param $_full array Page Widget info
 * @param $_config array Container config
 * @return string
 */
function jrCore_widget_list_display($_widget, $_full, $_config)
{
    global $_post, $_conf;
    $params = array();
    foreach ($_widget as $k => $v) {
        $key          = substr($k, 5);
        $params[$key] = $v;
    }

    // Check for page
    if (isset($params['pagebreak']) && $params['pagebreak'] > 0) {
        if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
            $params['page'] = $_post['p'];
        }
    }
    else {
        unset($params['pagebreak']);
    }

    // Check for valid limit/pagebreak
    if (isset($params['limit']) && intval($params['limit']) === 0) {
        // We have a bad limit
        if (!isset($params['pagebreak'])) {
            $params['limit'] = 5;
        }
        else {
            unset($params['limit']);
        }
    }
    if (isset($params['pagebreak']) && jrCore_checktype($params['pagebreak'], 'number_nz') && is_array($_config) && isset($_config['ct_layout']) && $_config['ct_layout'] == 'tab') {
        $params['pager_load_id']  = "#widget_id-{$_full['widget_id']}";
        $params['pager_load_url'] = "{$_conf['jrCore_base_url']}/" . jrCore_get_module_url('jrSiteBuilder') . "/view_widget/id={$_full['widget_id']}";
    }

    // Check for custom template overriding default template
    if (isset($params['template']) && $params['template'] == 'custom') {
        if (isset($params['custom_template']) && strlen(trim($params['custom_template'])) > 1) {
            $params['template'] = $params['custom_template'];
            unset($params['custom_template']);
        }
        else {
            unset($params['template'], $params['custom_template']);
        }
    }
    elseif (isset($params['template']) && $params['template'] == 'default') {
        unset($params['template']);
    }
    else {
        $params['tpl_dir'] = $params['module'];
    }

    $params['widget_item_list_active'] = 1;
    $params['ignore_missing']          = true;

    // Generate our jrCore_list call
    $smarty = new stdClass;
    return smarty_function_jrCore_list($params, $smarty);
}

/**
 * Get list templates for a given module
 * @param $module string Module
 * @return array|bool
 */
function jrCore_widget_list_get_module_templates($module)
{
    // Get module widget templates
    $_lt = false;
    $_tp = glob(APP_DIR . "/modules/{$module}/templates/*list*");
    if ($_tp && is_array($_tp)) {
        $_lt = array();
        foreach ($_tp as $tpl) {
            $nam = basename($tpl);
            switch ($nam) {
                case 'item_list.tpl':
                    $_lt['item_list.tpl'] = 'default';
                    break;
                default:
                    $_lt[$nam] = trim(str_replace(array('_', '.tpl'), array(' ', ''), str_replace('item_list_', '', $nam)));
                    break;
            }
        }
    }
    if (is_file(APP_DIR . "/modules/{$module}/templates/item_grid.tpl")) {
        $_lt['item_grid.tpl'] = 'grid';
    }

    return $_lt;
}
