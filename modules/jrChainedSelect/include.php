<?php
/**
 * Jamroom 5 Chained Select module
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
 * @copyright 2013 Talldude Networks, LLC.
 * @author Paul Asher <paul [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Meta Data function
 * @return array
 */
function jrChainedSelect_meta()
{
    $_tmp = array(
        'name'        => 'Chained Select',
        'url'         => 'chained_select',
        'version'     => '1.1.4',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Manage chained_select field options and choices',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/1753/chained-select',
        'category'    => 'forms',
        'license'     => 'mpl',
        'requires'    => 'jrCore:5.1.36'
    );
    return $_tmp;
}

/**
 * init
 */
function jrChainedSelect_init()
{
    // register the custom form fields
    jrCore_register_module_feature('jrCore', 'form_field', 'jrChainedSelect', 'chained_select');

    // Bring in core javascript
    jrCore_register_module_feature('jrCore', 'javascript', 'jrChainedSelect', 'jrChainedSelect.js');

    // Register our jrChainedSelect tools
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrChainedSelect', 'browse', array('Manage Sets', 'Create and delete chained_select form field options'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrChainedSelect', 'import', array('Import a Set', 'Import chained_select form field options from a CSV file'));

    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrChainedSelect', 'browse', 'Manage Sets');
    jrCore_register_module_feature('jrCore', 'default_admin_view', 'jrChainedSelect', 'browse');

    jrCore_register_module_feature('jrUser', 'skip_session', 'jrChainedSelect', 'get');

    return true;
}

/**
 * Get a Chained Select Set from the DB
 * @param $id int set_id to retrieve
 * @return bool|mixed
 */
function jrChainedSelect_get_set($id)
{
    $sid = (int) $id;
    $tbl = jrCore_db_table_name('jrChainedSelect', 'sets');
    $req = "SELECT * FROM {$tbl} WHERE set_id = {$sid} LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    return ($_rt) ? $_rt : false;
}

/**
 * @ignore
 * jrChainedSelect_form_field_chained_select_display
 * @param array $_field Array of Field parameters
 * @param array $_att Additional HTML parameters
 * @return bool
 */
function jrChainedSelect_form_field_chained_select_display($_field, $_att = null)
{
    // Make sure we get a valid Chained Select group name
    if (!isset($_field['options']) || strlen($_field['options']) === 0) {
        return false;
    }

    $opt = trim($_field['options']);
    $tbl = jrCore_db_table_name('jrChainedSelect', 'sets');
    $req = "SELECT set_levels, set_options_1_title, set_options_2_title FROM {$tbl} WHERE set_name = '" . jrCore_db_escape($opt) . "' LIMIT 1";
    $_fm = jrCore_db_query($req, 'SINGLE');
    if (!$_fm || !isset($_fm['set_levels']) || !jrCore_checktype($_fm['set_levels'], 'number_nz')) {
        jrCore_logger('MAJ', "Invalid set_name defined in option parameter - not found", $_field);
        return false;
    }

    $level = 0;
    $fname = $_field['name'];
    $_temp = jrCore_get_flag('jrcore_form_create_values');
    while ($level < $_fm['set_levels']) {

        // Save previous value..
        $nam = "{$fname}_{$level}";
        $pnm = '-';
        if ($_temp) {
            if ($level == 1) {
                if (isset($_temp["{$fname}_0"])) {
                    $pnm = $_temp["{$fname}_0"];
                }
            }
            else {
                if (isset($_temp["{$fname}_0"]) && isset($_temp["{$fname}_1"])) {
                    $pnm = "{$_temp["{$fname}_0"]}|{$_temp["{$fname}_1"]}";
                }
            }
        }
        if ($_temp && isset($_temp[$nam])) {
            $val = $_temp[$nam];
        }
        else {
            $val = '-';
        }
        $_tm = array(
            'name'    => $nam,
            'type'    => 'select',
            'options' => array(),
            'value'   => $val
        );
        if (isset($_field['order']) && jrCore_checktype($_field['order'], 'number_nn')) {
            $_tm['order'] = $_field['order'];
        }
        if ($level < 2) {
            $_tm['onchange'] = "jrChainedSelect_get('{$fname}','{$opt}','" . ($level + 1) . "',this.value);";
        }
        if ($level == 0) {
            $_tm['label']    = $_field['label'];
            $_tm['sublabel'] = (isset($_field['sublabel'])) ? $_field['sublabel'] : '';
            $_tm['help']     = (isset($_field['help'])) ? $_field['help'] : '';
        }
        else {
            if ($val == '-') {
                $_tm['class'] = 'form_element_disabled';
            }
            $_tm['label']    = (isset($_fm["set_options_{$level}_title"]) && strlen($_fm["set_options_{$level}_title"]) > 0) ? $_fm["set_options_{$level}_title"] : '';
            $_tm['sublabel'] = '';
            $_tm['help']     = '';
        }
        jrCore_form_field_create($_tm);
        if ($level == 0) {
            $htm = "<script type=\"text/javascript\">jrChainedSelect_get('{$fname}','{$opt}','{$level}','-','{$val}')</script>";
            jrCore_page_custom($htm);
        }
        elseif ($pnm != '-') {
            $htm = "<script type=\"text/javascript\">jrChainedSelect_get('{$fname}','{$opt}','{$level}','{$pnm}','{$val}')</script>";
            jrCore_page_custom($htm);
        }
        $level++;
    }
    return true;
}

/**
 * Defines Form Designer field options
 * @return string
 */
function jrChainedSelect_form_field_chained_select_form_designer_options()
{
    return array(
        'options_help'        => 'options are created using the Chained Select module - set this to the <strong>Option Set Name</strong> you have created in the module.',
        'disable_validation'  => true,
        'disable_min_and_max' => true
    );
}

/**
 * Add additional params
 * @param array $_field Array of Field Parameters
 * @param array $_post Posted Data for checking
 * @return array
 */
function jrChainedSelect_form_field_chained_select_params($_field, $_post)
{
    $_field['validate'] = 'not_empty';
    $_field['required'] = true;
    return $_field;
}

/**
 * @ignore
 * jrChainedSelect_form_field_chained_select_validate
 * @param array $_field Array of form field info
 * @param array $_post Global $_post from jrCore_parse_url()
 * @param string $e_msg Error message to use in validation
 * @return mixed
 */
function jrChainedSelect_form_field_chained_select_validate($_field, $_post, $e_msg)
{
    // Find our options...
    foreach ($_post as $k => $v) {
        if (strpos($k, "{$_field['name']}_") === 0) {
            if (!jrCore_checktype($k, $_field['validate'])) {
                jrCore_set_form_notice('error', $e_msg);
                jrCore_form_field_hilight($k);
                return false;
            }
            if (!@jrCore_is_valid_min_max_value($_field['validate'], $v, $_field['min'], $_field['max'], $e_msg)) {
                // NOTE: jrCore_set_form_notice() called in jrCore_is_valid_min_max_value()
                jrCore_form_field_hilight($k);
                return false;
            }
        }
    }
    return $_post;
}
