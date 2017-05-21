<?php
/**
 * Jamroom Seamless module
 *
 * copyright 2016 The Jamroom Network
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

/**
 * jrSeamless_meta
 */
function jrSeamless_meta()
{
    $_tmp = array(
        'name'        => 'Seamless',
        'url'         => 'seamless',
        'version'     => '1.1.11',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => "Provides a template function to create &quot;seamless&quot; lists of merged DataStore items",
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/289/seamless',
        'category'    => 'listing',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrSeamless_init()
{
    jrCore_register_module_feature('jrCore', 'javascript', 'jrSeamless', 'jrSeamless.js');

    jrCore_register_module_feature('jrSiteBuilder', 'widget', 'jrSeamless', 'widget_list', 'Item List (Combined)');
    return true;
}

//------------------------------------
// WIDGETS
//------------------------------------

/**
 * Display CONFIG screen for Item List widget
 * @param $_post array Post info
 * @param $_user array User array
 * @param $_conf array Global Config
 * @param $_wg array Widget Config
 * @return bool
 */
function jrSeamless_widget_list_config($_post, $_user, $_conf, $_wg)
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
            case 'jrCore':
            case 'jrRating':
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
    natcasesort($_opt);

    $_tmp = array(
        'name'     => 'list_modules',
        'label'    => 'List Module',
        'help'     => 'Select the modules whos items you want to list. Ctrl+click to select multiple.',
        'options'  => $_opt,
        'onchange' => 'jrSeamless_widget_list_get_module_info(this)',
        'type'     => 'select_multiple',
        'value'    => $_wg['list_modules'],
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_section_header('list options');

    if (isset($_wg['list_order_by'])) {
        list($obv, $obd) = explode(' ', $_wg['list_order_by']);
    }

    $_sel = array();
    $_opt = array();
    $_obs = array();
    $_ops = array(0 => array(), 1 => array(), 2 => array());
    $_val = array(0 => '', 1 => '', 2 => '');
    if (isset($_wg['list_modules'])) {
        foreach (array(0, 1, 2) as $k) {
            if (isset($_wg["list_search{$k}"]{1})) {
                list($_sel[$k], $_opt[$k], $_val[$k]) = explode(' ', $_wg["list_search{$k}"]);
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
                    }
                }
            }
        }
        $_modules = explode(',', $_wg['list_modules']);
        $_fl      = jrSeamless_get_keys($_modules);

        foreach ($_fl as $fld) {
            foreach (array(0, 1, 2) as $k) {
                if (isset($_sel[$k]) && $_sel[$k] == $fld) {
                    $_ops[$k][] = "<option selected=\"selected\" value=\"{$fld}\"> {$fld}</option>";
                }
                else {
                    $_ops[$k][] = "<option value=\"{$fld}\"> {$fld}</option>";
                }
            }
            if (isset($obv) && $obv == $fld) {
                $_obs[] = "<option selected=\"selected\" value=\"{$fld}\"> {$fld}</option>";
            }
            else {
                $_obs[] = "<option value=\"{$fld}\"> {$fld}</option>";
            }
        }
    }

    // Options
    $_sop = array(
        'eq'       => 'is equal to',
        'neq'      => 'does not equal',
        'lt'       => 'is less than',
        'gt'       => 'is greater than',
        'like'     => 'contains',
        'bw'       => 'begins with',
        'ew'       => 'ends with',
        'not_like' => 'does not contain',
        'in'       => 'is in comma list',
        'not_in'   => 'is not in comma list',
        'regexp'   => 'matches expression'
    );
    $_mop = array();
    foreach ($_sop as $sk => $sv) {
        foreach (array(0, 1, 2) as $k) {
            if (isset($_opt[$k]) && $_opt[$k] == $sk) {
                $_mop[$k][] = "<option selected=\"selected\" value=\"{$sk}\"> {$sv}</option>";
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
        if (isset($_sel[$k]) || ($k == 0 && isset($_wg['list_modules']) && strlen($_wg['list_modules']) > 0)) {
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
            $_obc[] = "<option selected=\"selected\" value=\"{$k}\"> {$v}</option>";
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
        You can add up to 3 search parameters to help create a more focused list of items.<br><br><b>Example:</b> If you wanted to create a list of blog entries from a specific blog_category - i.e. &quot;featured&quot;, you would:<br><br>&bull; Select the "blog_category" as the search key.<br>&bull; Select &quot;is equal to&quot; as the search option.<br>&bull; Enter &quot;featured&quot; (without the quotes) in the search value text field.
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
    jrCore_page_custom($html, 'Order By', null, 'If you would like the Item List to be ordered by a specific key, select it here.<br><br><b>Example:</b> To create a list of &quot;newest&quot; items, order by the <b>_created</b> key, descending.');

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

    // Get templates
    $rows = 'display:none';
    $_tpl = array();
    if (isset($_wg['list_template']) && $_wg['list_template'] == 'custom') {
        $rows   = '';
        $_tpl[] = "<option value=\"item_list.tpl\"> default</option>";
        $_tpl[] = "<option selected=\"selected\" value=\"custom\"> custom</option>";
    }
    else {
        $_tpl[] = "<option selected=\"selected\" value=\"item_list.tpl\"> default</option>";
        $_tpl[] = "<option value=\"custom\"> custom</option>";
    }

    $html = '<select id="list_template" name="list_template" class="form_select list_template_select ' . $cls . '"' . $att . ' style="width:30%" onchange="if($(this).val() == \'custom\') { $(\'#ff-row-list_custom_template\').slideDown(250, function() { jrSiteBuilder_activate_editor(); } ); } else { $(\'#ff-row-list_custom_template\').slideUp(50); }">' . implode("\n", $_tpl) . '</select>';
    jrCore_page_custom($html, 'Template', null, 'Select the template that will be used for each entry in the output list');

    // custom template
    // #ff-row-list_custom_template { display: none; }
    $_tmp = array(
        'name'      => 'list_custom_template',
        'label'     => 'custom template',
        'sublabel'  => '<a onclick="jrSeamless_load_default_code();">click to load default template</a>',
        'help'      => 'You can provide custom template code that will be used in place of the selected List template.<br><br><b>Note:</b> Must be valid template code',
        'type'      => 'textarea',
        'validate'  => 'not_empty',
        'required'  => false,
        'row_style' => $rows
    );
    jrCore_form_field_create($_tmp);

    return true;
}

/**
 * Get Widget results from posted Config data
 * @param $_post array Post info
 * @return mixed
 */
function jrSeamless_widget_list_config_save($_post)
{
    global $_conf;
    // check custom list template for errors
    if (isset($_post['list_template']) && $_post['list_template'] == 'custom' && strlen($_post['list_custom_template']) > 0) {
        $cdr = jrCore_get_module_cache_dir('jrCore');
        $nam = time() . ".tpl";
        jrCore_write_to_file("{$cdr}/{$nam}", $_post['list_custom_template']);
        $url = jrCore_get_module_url('jrCore');
        $out = jrCore_load_url("{$_conf['jrCore_base_url']}/{$url}/test_template/{$nam}");
        if (isset($out) && strlen($out) > 1 && (strpos($out, 'error:') === 0 || stristr($out, 'fatal error'))) {
            unlink("{$cdr}/{$nam}");
            jrCore_set_form_notice('error', 'There is a syntax error in your template - please fix and try again');
            return jrCore_form_result();
        }
        unlink("{$cdr}/{$nam}");
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
                        $v  = "{$v}%";
                        break;
                    case 'ew':
                        $op = 'like';
                        $v  = "%{$v}";
                        break;
                    case 'like':
                    case 'not_like':
                        $op = 'like';
                        if (!strpos($v, '%')) {
                            $v = "%{$v}%";
                        }
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

    // modules array
    if (!is_array($_post['list_modules']) || count($_post['list_modules']) < 2) {
        jrCore_set_form_notice('error', 'You must select at least 2 modules to create a combined list - please fix and try again');
        return jrCore_form_result();
    }
    $_out['list_modules'] = implode(',', $_post['list_modules']);
    // Pager
    if (isset($_out['list_pagebreak']) && jrCore_checktype($_out['list_pagebreak'], 'number_nz')) {
        $_out['list_pager'] = true;
    }

    return $_out;
}

/**
 * Widget DISPLAY
 * @param $_widget array Page Widget settings
 * @param $_full array Page Widget info
 * @param $_config array Container config
 * @return string
 */
function jrSeamless_widget_list_display($_widget, $_full, $_config)
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
        }
        else {
            unset($params['template'], $params['custom_template']);
        }
    }
    elseif (isset($params['template']) && $params['template'] == 'default') {
        unset($params['template']);
    }
    else {
        $params['tpl_dir'] = 'jrSeamless';
    }

    $params['widget_item_list_active'] = 1;

    // Generate our jrCore_list call
    $smarty = new stdClass;
    return smarty_function_jrSeamless_list($params, $smarty);
}

/**
 * Get list templates for a given module
 * @param $module string Module
 * @return array|bool
 */
function jrSeamless_widget_list_get_module_templates($module)
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
    return $_lt;
}

/**
 * Takes an array of modules and returns the common datastore keys
 * @param $_modules
 * @return bool|mixed
 */
function jrSeamless_get_keys($_modules)
{
    if (!array($_modules)) {
        return false;
    }
    $_mi = array();
    foreach ($_modules as $m) {

        // Get unique DataStore keys
        $_mi[$m] = jrCore_db_get_unique_keys($m);
        if (!$_mi[$m] || !is_array($_mi[$m])) {
            $_mi[$m] = array('_created', '_updated');
        }
        $_mi[$m] ['_'] = ' ';
        switch ($m) {
            case 'jrUser':
            case 'jrProfile':
                break;
            default:
                $_mi[$m]['_item_id'] = '_item_id';
                break;
        }
        sort($_mi[$m]);

    }

    // thin down to just common options *_title etc
    if (count($_modules) > 1) {
        $_all    = array();
        $_common = array();
        foreach ($_mi as $m => $_v) {
            $pfx = jrCore_db_get_prefix($m);
            foreach ($_v as $v) {
                if (strpos($v, $pfx) === 0) {
                    $val = '*' . substr($v, strlen($pfx));;
                }
                else {
                    $val = $v;
                }
                $_common[$m][] = $val;
                $_all[$val]    = $val;
            }
        }
        $intersection = call_user_func_array('array_intersect', $_common);
    }
    else {
        $intersection = array_shift($_mi);
    }

    sort($intersection);
    return $intersection;
}

//---------------------------------------------------------
// SMARTY FUNCTIONS
//---------------------------------------------------------

/**
 * Lists all specified module items seamlessly
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrSeamless_list($params, $smarty)
{
    global $_post, $_conf;
    $_out = array();
    // Check params
    if (!isset($params['modules'])) {
        if (!isset($params['module'])) {
            return jrCore_smarty_missing_error('modules');
        }
        $params['modules'] = $params['module'];
        unset($params['module']);
    }

    // Check for cache
    $key = json_encode($params);
    if ($tmp = jrCore_is_cached('jrSeamless', $key)) {
        if (!empty($params['assign'])) {
            $smarty->assign($params['assign'], $tmp);
            return '';
        }
        return $tmp . "\n<!--c-->";
    }

    // Validate modules
    $params['modules'] = trim($params['modules']);
    $backup            = $params;
    if ($_modules = explode(',', $params['modules'])) {
        foreach ($_modules as $mod) {
            $mod = trim($mod);
            if (!jrCore_module_is_active($mod)) {
                return jrCore_smarty_custom_error('inactive module specified');
            }
            if (!jrCore_db_get_prefix($mod)) {
                return jrCore_smarty_custom_error("invalid module specified: {$mod} - no datastore");
            }
        }
    }
    unset($params['modules']);

    $_lang   = jrUser_load_lang_strings();
    $tpl_dir = $_conf['jrCore_active_skin'];
    if (!isset($params['template'])) {
        $tpl_dir            = 'jrSeamless';
        $params['template'] = 'item_list.tpl';
    }
    if (isset($params['tpl_dir']) && is_file(APP_DIR . "/modules/{$params['tpl_dir']}/templates/{$params['template']}")) {
        $tpl_dir = $params['tpl_dir'];
    }

    // Setup search
    $_src = false;
    foreach ($params as $k => $v) {
        // Search
        if (strpos($k, 'search') === 0 && strlen($v) > 0) {
            if (!isset($_src)) {
                $_src = array();
            }
            $_src[] = str_replace('*', '%', $v);
        }
    }
    if ($_src && is_array($_src) && count($_src) > 0) {
        $params['search'] = $_src;
    }

    // We will do our own pagination below...
    $pn = 1;
    if (isset($params['page']) && jrCore_checktype($params['page'], 'number_nz')) {
        $pn = (int) $params['page'];
    }
    unset($params['page']);

    $pb  = false;
    $olm = false;
    if (isset($params['limit']) && jrCore_checktype($params['limit'], 'number_nz')) {
        $olm = (int) $params['limit'];
    }
    else {
        $pb = 10;
        if (isset($params['pagebreak']) && jrCore_checktype($params['pagebreak'], 'number_nz')) {
            $pb = (int) $params['pagebreak'];
        }
        unset($params['pagebreak']);
    }

    // Our "Sort Limit"
    $slm = 1000000;
    if (isset($params['sort_limit']) && jrCore_checktype($params['sort_limit'], 'number_nz')) {
        $slm = (int) $params['sort_limit'];
    }
    $params['limit'] = $slm;

    // Order By
    $cnt = 0;
    $_fl = array();
    $fld = '_item_id';
    $dir = 'numerical_asc';
    if (isset($params['order_by'])) {
        list($fld, $dir) = explode(' ', preg_replace('/\s+/', ' ', $params['order_by']));
    }

    $params['jrseamless_list_function_call_is_active'] = 1;
    $params['exclude_jrUser_keys']                     = true;
    $params['exclude_jrProfile_keys']                  = true;
    $params['exclude_jrProfile_quota_keys']            = true;

    // Run our search items for EACH module - we will then interleave the results below
    foreach ($_modules as $mod) {

        // We only bring back the minimum fields necessary here, as this could
        // be big depending on how far into the result set the user is going
        if (strpos($fld, '*') === 0) {
            if ($pfx = jrCore_db_get_prefix($mod)) {
                // some fields we have to tweak
                switch ($fld) {
                    case '*stream_count':
                    case '*_stream_count':
                    case '*file_stream_count':
                    case '*_file_stream_count':
                        switch ($mod) {
                            case 'jrAudio':
                            case 'jrVideo':
                                $tmp = "{$pfx}_file_stream_count";
                                break;
                            default:
                                $tmp = "{$pfx}_stream_count";
                                break;
                        }
                        switch (strtolower($dir)) {
                            case 'asc':
                            case 'desc':
                                $dir = "numerical_{$dir}";
                                break;
                        }
                        break;
                    case '*_display_order':
                        $tmp = "{$pfx}_display_order";
                        $dir = "numerical_asc";
                        break;
                    default:
                        $tmp = str_replace('*', $pfx, $fld);
                        break;
                }
                $params['order_by']    = array($tmp => $dir);
                $params['return_keys'] = array('_item_id', $tmp);
                $_fl[$mod]             = $tmp;
            }
        }
        else {
            if ($mod == 'jrProfile' && $fld == '_item_id') {
                $fld = '_profile_id';
            }
            elseif ($mod == 'jrUser' && $fld == '_item_id') {
                $fld = '_user_id';
            }
            $params['return_keys'] = array('_item_id', $fld);
            if (isset($params['order_by'])) {
                $params['order_by'] = array($fld => $dir);
            }
        }

        // If we are a PROFILE OWNER or ADMIN on a profile index list, ignore pending
        if (jrProfile_is_profile_view() && !jrUser_is_admin() && isset($_post['_profile_id']) && jrProfile_is_profile_owner($_post['_profile_id'])) {
            $params['privacy_check']  = false;
            $params['ignore_pending'] = true;
            $params['quota_check']    = false;
        }

        $_tmp = jrCore_db_search_items($mod, $params);
        if ($_tmp && is_array($_tmp) && isset($_tmp['_items'])) {
            $_out[$mod] = array();
            foreach ($_tmp['_items'] as $k => $v) {
                switch ($mod) {
                    case 'jrProfile':
                        $idf = '_profile_id';
                        break;
                    case 'jrUser':
                        $idf = '_user_id';
                        break;
                    default:
                        $idf = '_item_id';
                        break;
                }
                $_out[$mod]["{$v[$idf]}"] = $v;
            }
        }

        // Total Item count for pagebreak (if needed)
        if ($pb && $pb > 0 && isset($_out[$mod])) {
            $cnt += count($_out[$mod]);
        }
        unset($_tmp);
    }

    // Next - we go through each result set and find the ORDER BY key - this gets added
    // to our special $_ord array which we will use to do our final ordering
    if (count($_out) > 0) {

        $_ord = array();
        foreach ($_out as $mod => $_res) {
            if (is_array($_res)) {
                switch ($mod) {
                    case 'jrProfile':
                        $idf = '_profile_id';
                        break;
                    case 'jrUser':
                        $idf = '_user_id';
                        break;
                    default:
                        $idf = '_item_id';
                        break;
                }
                foreach ($_res as $k => $v) {
                    if (isset($_fl[$mod])) {
                        $fld                       = $_fl[$mod];
                        $_ord["{$mod}:{$v[$idf]}"] = $v[$fld];
                    }
                    elseif (isset($v[$fld])) {
                        $_ord["{$mod}:{$v[$idf]}"] = $v[$fld];
                    }
                }
            }
        }

        // See how we are ordering...
        switch (strtolower($dir)) {

            case 'asc':
                natcasesort($_ord);
                break;

            case 'numerical_asc':
                asort($_ord, SORT_NUMERIC);
                break;

            case 'desc':
                natcasesort($_ord);
                $_ord = array_reverse($_ord);
                break;

            case 'numerical_desc':
                arsort($_ord, SORT_NUMERIC);
                break;

            case 'rand':
                if ($_tmp = array_rand($_ord, count($_ord))) {
                    $_or2 = array();
                    foreach ($_tmp as $k => $v) {
                        $_or2[$v] = $_ord[$v];
                    }
                    $_ord = $_or2;
                    unset($_tmp, $_or2);
                }
                break;
        }

        // Limit
        if ($olm) {
            $_ord = array_slice($_ord, 0, $olm);
        }

        // Pagebreak
        elseif ($pb && $pb > 0) {
            $_ord = array_slice($_ord, (($pn - 1) * $pb), $pb);
        }

        // Default limit
        else {
            $_ord = array_slice($_ord, 0, 10);
        }

        // Construct result array
        $_tmp = array();
        foreach ($_ord as $k => $v) {
            if (list($mod, $iid) = explode(':', $k)) {
                if (!isset($_tmp[$mod])) {
                    $_tmp[$mod] = array();
                }
                $_tmp[$mod][] = $iid;
            }
        }
        $_res = array();
        foreach ($_tmp as $mod => $_ids) {

            // NOTE: We don't have to check for privacy, etc here since that was already done above
            $_sp = array(
                'search' => array(
                    "_item_id in " . implode(',', $_ids)
                ),
                'privacy_check'  => false,
                'ignore_pending' => true,
                'quota_check'    => false,
                'limit'          => count($_ids)
            );
            $_sp = jrCore_db_search_items($mod, $_sp);
            if ($_sp && is_array($_sp) && isset($_sp['_items'])) {
                foreach ($_sp['_items'] as $k => $v) {
                    if (!isset($_res[$mod])) {
                        $_res[$mod] = array();
                    }
                    switch ($mod) {
                        case 'jrProfile':
                            $idf = '_profile_id';
                            break;
                        case 'jrUser':
                            $idf = '_user_id';
                            break;
                        default:
                            $idf = '_item_id';
                            break;
                    }
                    $_res[$mod]["{$v[$idf]}"] = $v;
                }
            }
        }
        unset($_sp);

        // Final result set
        $_tmp                          = array(
            'info'    => array(
                'module'        => 'jrSeamless',
                'total_items'   => $cnt,
                'total_pages'   => ($cnt > 0) ? ceil($cnt / $pb) : 1,
                'page'          => $pn,
                'pagebreak'     => $pb,
                'page_base_url' => jrCore_strip_url_params(jrCore_get_current_url(), array('p')),
                'prev_page'     => ($pn > 1) ? ($pn - 1) : 0,
                'this_page'     => $pn,
                'next_page'     => ($cnt > 0) ? (ceil($cnt / $pb) > $pn) ? intval($pn + 1) : 0 : 0
            ),
            '_params' => $backup,
            '_items'  => array()
        );
        $_tmp['_params']['module']     = 'jrSeamless';
        $_tmp['_params']['module_url'] = jrCore_get_module_url('jrSeamless');
        $i                             = 0;
        foreach ($_ord as $k => $v) {
            if (list($mod, $iid) = explode(':', $k)) {
                $_tmp['_items'][$i]                           = $_res[$mod][$iid];
                $_tmp['_items'][$i]['seamless_list_rank']     = $i;
                $_tmp['_items'][$i]['seamless_module_name']   = $mod;
                $_tmp['_items'][$i]['seamless_module_prefix'] = jrCore_db_get_prefix($mod);
                $_tmp['_items'][$i]['seamless_module_title']  = (isset($_lang[$mod]['menu'])) ? $_lang[$mod]['menu'] : $mod;
                $i++;
            }
        }

        if (isset($params['template']) && $params['template'] != 'null') {
            // We have a template
            $tmp = jrCore_parse_template($params['template'], $_tmp, $tpl_dir);

            // See if we are including the default pager
            if (isset($params['pager']) && $params['pager'] == true && $params['pager'] !== "false") {
                $tpl = 'list_pager.tpl';
                $dir = 'jrCore';
                if (isset($params['pager_template'])) {
                    $tpl = $params['pager_template'];
                    $dir = $_conf['jrCore_active_skin'];
                }
                if (isset($params['pager_load_id'])) {
                    $_tmp['pager_load_id'] = $params['pager_load_id'];
                }
                if (isset($params['pager_load_template'])) {
                    $_tmp['pager_load_template'] = $params['pager_load_template'];
                }
                if (isset($params['pager_load_url'])) {
                    $_tmp['pager_load_url'] = $params['pager_load_url'];
                }
                if (isset($params['pager_show_jumper'])) {
                    $_tmp['pager_show_jumper'] = $params['pager_show_jumper'];
                }
                $tmp .= jrCore_parse_template($tpl, $_tmp, $dir);
            }
        }
        else {
            $tmp = $_tmp;
            unset($_tmp);
        }
    }
    else {
        $tmp = '';
    }

    jrCore_add_to_cache('jrSeamless', $key, $tmp);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $tmp);
        return '';
    }
    return $tmp;
}

/**
 * Parse a specific template
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrSeamless_parse_template($params, $smarty)
{
    if (!isset($params['item'])) {
        return jrCore_smarty_missing_error('item');
    }
    $_vars = array(
        '_items' => array(
            $params['item']
        )
    );
    $out   = jrCore_parse_template($params['template'], $_vars, $params['module']);
    if (isset($params['assign']) && strlen($params['assign']) > 0) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}
