<?php
/**
 * Jamroom Terms of Service module
 *
 * copyright 2017 The Jamroom Network
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
// display Terms
//------------------------------
function view_jrTOS_view_tos($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid terms of service page id');
    }
    $_pg = array(
        'item' => jrCore_db_get_item('jrPage', $_post['_1'])
    );
    if (!is_array($_pg['item'])) {
        jrCore_notice_page('error', 'Invalid terms of service page id');
    }
    $_ln = jrUser_load_lang_strings();

    // Prevent clicks outside the TOS area
    $_js = array("$('html').click(function() { alert('" . addslashes($_ln['jrTOS'][4]) . "'); return false; }); $('.page_content').click(function(event){ event.stopPropagation(); });");
    jrCore_create_page_element('javascript_ready_function', $_js);

    jrCore_page_title($_pg['item']['page_title']);
    jrCore_set_form_notice('success', 1);
    jrCore_get_form_notice();

    // Form init
    $murl = jrCore_get_module_url('jrUser');
    $_tmp = array(
        'submit_value' => 2,
        'cancel_value' => 3,
        'cancel'       => "{$_conf['jrCore_base_url']}/{$murl}/logout",
        'onclick'      => "if (!$('#tos_agree').prop('checked')) { alert('" . addslashes($_ln['jrTOS'][4]) . "'); return false; }"
    );
    jrCore_form_create($_tmp);

    $html = '<div class="item">' . smarty_modifier_jrCore_format_string($_pg['item']['page_body'], $_user['profile_quota_id']) . '</div>';
    jrCore_page_custom($html);

    // Terms page id
    $_tmp = array(
        'name'  => 'id',
        'type'  => 'hidden',
        'value' => $_post['_1']
    );
    jrCore_form_field_create($_tmp);

    // Agree to terms
    $_tmp = array(
        'name'     => 'tos_agree',
        'label'    => 5,
        'help'     => 6,
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'default'  => 'off',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// display Terms
//------------------------------
function view_jrTOS_view_tos_save($_post, &$_user, &$_conf)
{
    jrUser_session_require_login();
    jrCore_form_validate($_post);
    if (!isset($_post['tos_agree']) || $_post['tos_agree'] != 'on') {
        jrCore_set_form_notice('error', 1);
        jrCore_form_field_hilight('tos_agree');
        jrCore_form_result();
    }
    $_pg = jrCore_db_get_item('jrPage', $_post['id'], true);
    if (!is_array($_pg)) {
        jrCore_set_form_notice('error', 7);
        jrCore_form_result();
    }
    $_data = array(
        "user_jrTOS_{$_post['id']}_agreed" => $_pg['_updated']
    );
    if (jrCore_db_update_item('jrUser', $_user['_user_id'], $_data)) {

        // event
        jrCore_trigger_event('jrTOS', 'tos_agreed', array());

        // Get any saved location from login
        $url = jrUser_get_saved_location();
        // Redirect to Profile or Saved Location
        if (isset($url) && jrCore_checktype($url, 'url') && strpos($url, $_conf['jrCore_base_url']) === 0 && $url != $_conf['jrCore_base_url'] && $url != $_conf['jrCore_base_url'] . '/' && !strpos($url, '/signup')) {
            jrCore_form_result($url);
        }
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}");

    }
}
