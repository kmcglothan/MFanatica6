<?php
/**
 * Jamroom Find New Music module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
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
 * meta
 */
function jrRecommend_meta()
{
    $_tmp = array(
        'name'        => 'Find New Music',
        'url'         => 'recommend',
        'version'     => '1.1.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => "Inserts a searchable &quot;influences&quot; field in profile settings to help users &quot;Find New Music&quot;.",
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/288/find-new-music',
        'category'    => 'site',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrRecommend_init()
{
    // Add in quota support
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrRecommend', 'on');

    // Add in Recommend javascript
    jrCore_register_module_feature('jrCore', 'javascript', 'jrRecommend', 'jrRecommend.js');

    // Recommend listener
    jrCore_register_event_listener('jrCore', 'form_display', 'jrRecommend_insert_field');

    return true;
}

//----------------------
// EVENT LISTENERS
//----------------------

/**
 * Insert the "Influences" field into the Profile Settings page
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrRecommend_insert_field($_data, $_user, $_conf, $_args, $event)
{
    // Is this the jrProfile/settings form?
    if (isset($_data['form_view']) && $_data['form_view'] == 'jrProfile/settings' && isset($_user['quota_jrRecommend_allowed']) && $_user['quota_jrRecommend_allowed'] == 'on') {
        $_ln = jrUser_load_lang_strings();
        $_tm = array(
            'name'          => 'profile_influences',
            'label'         => $_ln['jrRecommend'][1],
            'help'          => $_ln['jrRecommend'][2],
            'type'          => 'text',
            'validate'      => 'printable',
            'required'      => false,
            'form_designer' => false
        );
        jrCore_form_field_create($_tm);
    }
    return $_data;
}

//----------------------
// SMARTY FUNCTIONS
//----------------------

/**
 * Build a recommend new music form
 * In: page (default:1)
 * In: pagebreak (default:10)
 * In: template (default: html_recommend_form.tpl)
 * In: class (optional)
 * In: style (optional)
 * In: assign (optional)
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrRecommend_form($params, $smarty)
{
    global $_conf;
    if (!jrCore_module_is_active('jrRecommend')) {
        return '';
    }
    // Check the incoming parameters
    if (!isset($params['page']) | !jrCore_checktype($params['page'], 'number_nz')) {
        $params['page'] = 1;
    }

    if (!isset($params['pagebreak']) || !jrCore_checktype($params['pagebreak'], 'number_nz')) {
        $params['pagebreak'] = 10;
    }

    if (!isset($params['value']) || strlen($params['value']) === 0) {
        $_ln             = jrUser_load_lang_strings();
        $params['value'] = $_ln['jrRecommend'][3];
    }

    if (!isset($params['style'])) {
        $params['style'] = '';
    }

    if (!isset($params['class'])) {
        $params['class'] = '';
    }

    if (isset($params['template']) && strlen($params['template']) > 0) {
        $params['tpl_dir'] = $_conf['jrCore_active_skin'];
    }
    else {
        $params['template'] = "html_recommend_form.tpl";
        $params['tpl_dir']  = 'jrRecommend';
    }

    $_tmp = array();
    foreach ($params as $k => $v) {
        $_tmp['jrRecommend'][$k] = $v;
    }

    // Call the appropriate template and return
    $out = jrCore_parse_template($params['template'], $_tmp, $params['tpl_dir']);
    if (isset($params['assign']) && $params['assign'] != '') {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}
