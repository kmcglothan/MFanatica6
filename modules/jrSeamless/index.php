<?php
/**
 * Jamroom Seamless module
 *
 * copyright 2018 The Jamroom Network
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
 * @copyright 2015 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//------------------------------
// widget_list_get_module_info
//------------------------------
function view_jrSeamless_widget_list_get_module_info($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['m'])) {
        jrCore_json_response(array('error' => 'invalid module'));
    }
    $_modules = explode(',', $_post['m']);
    if (!is_array($_modules)) {
        jrCore_json_response(array('error' => 'no modules selected'));
    }
    foreach ($_modules as $m) {
        if (!jrCore_module_is_active($m)) {
            jrCore_json_response(array('error' => 'module not active'));
        }
    }

    $key = json_encode($_post);
    if (!$intersection = jrCore_is_cached('jrSeamless', $key, false)) {
        $intersection = jrSeamless_get_keys($_modules);
        jrCore_add_to_cache('jrCore', $key, $intersection, 0, 0, false);
    }
    jrCore_json_response($intersection);
}

//----------------------------------------------
// default template code to customize. (ajax)
//----------------------------------------------
function view_jrSeamless_default_tpl($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_validate_location_url();
    // get the template
    if (is_file(APP_DIR . "/modules/jrSeamless/templates/item_list.tpl")) {
        $tpl = file_get_contents(APP_DIR . "/modules/jrSeamless/templates/item_list.tpl");
        $_rs = array(
            'OK'   => 1,
            'code' => $tpl
        );
        jrCore_json_response($_rs, true, false);
    }
    jrCore_json_response(array('OK' => 0, 'code' => 'unable to open default item_list.tpl file'));
}

