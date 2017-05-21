<?php
/**
 * Jamroom 5 Audio (Combined) module
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
 * @copyright 2003-2015 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//--------------------------------
// create_audio
//--------------------------------
function view_jrCombinedAudio_create_audio($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrUser_check_quota_access('jrCombinedAudio');

    $url = jrCore_get_module_url('jrImage');
    $_ln = jrUser_load_lang_strings();
    $_rp = array();
    $_tm = jrCore_get_registered_module_features('jrCombinedAudio', 'combined_support');
    if ($_tm && is_array($_tm)) {
        foreach ($_tm as $mod => $_inf) {
            if (isset($_user["quota_{$mod}_allowed"]) && $_user["quota_{$mod}_allowed"] == 'on') {
                foreach ($_inf as $view => $_args) {
                    $icon_url = "{$_conf['jrCore_base_url']}/modules/{$mod}/icon.png";
                    if (is_file(APP_DIR ."/modules/{$mod}/img/combined.png")) {
                        $icon_url = "{$_conf['jrCore_base_url']}/{$url}/img/module/{$mod}/combined.png";
                    }
                    $_rp['_audios'][$mod] = array(
                        'alt'      => (is_numeric($_args['alt']) && isset($_ln[$mod]["{$_args['alt']}"])) ? $_ln[$mod]["{$_args['alt']}"] : $_args['alt'],
                        'title'    => (is_numeric($_args['title']) && isset($_ln[$mod]["{$_args['title']}"])) ? $_ln[$mod]["{$_args['title']}"] : $_args['title'],
                        'view'     => $view,
                        'icon_url' => $icon_url
                    );
                }
            }
        }
    }
    return jrCore_parse_template('create_audio.tpl', $_rp, 'jrCombinedAudio');
}

//--------------------------------
// item_display_order_update
//--------------------------------
function view_jrCombinedAudio_item_display_order_update($_post, $_user, $_conf)
{
    jrUser_session_require_login();

    // [0] => jrSoundCloud-45
    // [1] => jrAudio-3
    // [2] => jrSoundCloud-44
    $_ln = jrUser_load_lang_strings();
    $_up = array();
    $_pf = array();
    foreach ($_post['iid'] as $ord => $id) {
        list($mod, $iid) = explode('-', $id);
        $iid = (int) $iid;
        if (!isset($_up[$mod])) {
            $_up[$mod] = array();
            $_pf[$mod] = jrCore_db_get_prefix($mod);
        }
        $_up[$mod][$iid] = array($_pf[$mod] . '_display_order' => $ord);
    }
    if (count($_up) > 0) {
        foreach ($_up as $mod => $_ids) {
            jrCore_db_update_multiple_items($mod, $_ids);
        }
        jrProfile_reset_cache();
    }
    return jrCore_json_response(array('success' => $_ln['jrCore'][86]));

}
