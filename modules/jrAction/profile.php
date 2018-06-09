<?php
/**
 * Jamroom Timeline module
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
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//------------------------------
// profile_default
//------------------------------
function profile_view_jrAction_default($_profile, $_post, $_user, $_conf)
{
    global $_post; // leave this here!
    $_ln = jrUser_load_lang_strings();
    jrCore_page_title(jrCore_str_to_lower("{$_ln['jrAction']['menu']} - {$_profile['profile_name']}"));
    if (!isset($_post['_1'])) {
        return jrCore_parse_template('item_index.tpl', $_profile, 'jrAction');
    }
    switch ($_post['_1']) {

        // [_uri] => /brian/timeline/mentions
        // [module_url] => brian
        // [module] =>
        // [option] => action
        // [_1] => mentions
        // [p] => 2
        case 'timeline':
        case 'mentions':
        case 'feedback':
        case 'shared':
            jrCore_set_flag('jrurlscan_expand_url_cards', 1);
            $_post['profile_actions'] = $_post['_1'];
            return jrCore_parse_template('item_index.tpl', $_profile, 'jrAction');
            break;

        case 'search':

            // We're doing a search - add search flag to post..
            $_post['profile_actions'] = 'search';
            if (isset($_post['ss']) && strlen($_post['ss']) > 0) {

                // We're getting a search - we need to find matching
                // action items and inject the id's into the template
                $_rt = array(
                    'search'                       => array(
                        "_profile_id = {$_profile['_profile_id']}"
                    ),
                    'exclude_jrProfile_keys'       => true,
                    'exclude_jrProfile_quota_keys' => true,
                    'exclude_jrUser_keys'          => true,
                    'limit'                        => 500
                );
                $_rt = jrCore_db_search_items('jrAction', $_rt);
                $_id = array();
                if ($_rt && is_array($_rt) && is_array($_rt['_items'])) {
                    foreach ($_rt['_items'] as $_act) {
                        if (isset($_act['action_text']) && stripos(' ' . $_act['action_text'], $_post['ss'])) {
                            // Found in text
                            $_id[] = $_act['_item_id'];
                        }
                        elseif (isset($_act['action_data']) && $pfx = jrCore_db_get_prefix($_act['action_module'])) {
                            // We need to see if our info for this module matches
                            if (isset($_act['action_data']["{$pfx}_title"]) && stripos(' ' . $_act['action_data']["{$pfx}_title"], $_post['ss'])) {
                                $_id[] = $_act['_item_id'];
                            }
                            elseif (isset($_act['action_data']["{$pfx}_text"]) && stripos(' ' . $_act['action_data']["{$pfx}_text"], $_post['ss'])) {
                                $_id[] = $_act['_item_id'];
                            }
                        }
                    }
                }
                $_post['match_ids'] = 0;
                if (count($_id) > 0) {
                    $_post['match_ids'] = implode(',', $_id);
                }
            }
            return jrCore_parse_template('item_index.tpl', $_profile, 'jrAction');
            break;
    }
    return false;
}
