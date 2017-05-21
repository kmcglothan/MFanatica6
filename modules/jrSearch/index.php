<?php
/**
 * Jamroom Search module
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
// Rebuild Search Index
//------------------------------
function view_jrSearch_rebuild($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrSearch');
    jrCore_page_banner('Rebuild Search Index');
    jrCore_page_notice('success', 'If you have made modifications to the Search Global Config you will<br>need to rebuild the Search Index for your changes to take effect.', false);

    // Form init
    $_tmp = array(
        'submit_value'  => 'rebuild search index',
        'cancel'        => 'referrer',
        'submit_prompt' => 'Rebuild the Search Index?  On large systems this could take a few minutes to complete'
    );
    jrCore_form_create($_tmp);

    // Rebuild
    $_tmp = array(
        'name'     => 'rebuild_index',
        'label'    => 'rebuild search index',
        'help'     => 'If this is checked, the search index will be rebuilt',
        'type'     => 'checkbox',
        'value'    => 'on',
        'validate' => 'onoff'
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// Rebuild Search Index
//------------------------------
function view_jrSearch_rebuild_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Cleanup
    $tbl = jrCore_db_table_name('jrSearch', 'fulltext');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");

    $_md = jrCore_get_datastore_modules();
    if ($_md && is_array($_md)) {
        foreach ($_md as $mod => $pfx) {
            if (!jrSearch_is_excluded_module($mod)) {
                $_queue = array(
                    'action' => 'create',
                    'module' => $mod
                );
                jrCore_queue_create('jrSearch', 'search_index', $_queue);
            }
        }
    }
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/global");
}

//------------------------------
// Search results
// In: $_post['search_string']
// In: $_post['_1'] = module
// In: $_post['_2'] = page
// In: $_post['_3'] = pagebreak
//------------------------------
function view_jrSearch_results($_post, $_user, $_conf)
{
    global $_mods;
    if (empty($_post['search_string'])) {
        if (isset($_SESSION['jrsearch_last_search_string'])) {
            $_post['search_string'] = $_SESSION['jrsearch_last_search_string'];
        }
        else {
            jrCore_page_not_found();
        }
    }

    $_post['search_string']                  = trim($_post['search_string']);
    $_SESSION['jrsearch_last_search_string'] = jrCore_entity_string(strip_tags(str_replace('"', '', $_post['search_string'])));

    // First - find modules we are going to be searching
    $_rm = jrCore_get_registered_module_features('jrSearch', 'search_fields');

    // Allow other modules to inject into search
    $_rm = jrCore_trigger_event('jrSearch', 'search_fields', $_rm);

    // Specific modules
    $_ln = jrUser_load_lang_strings();
    $ttl = $_ln['jrSearch'][8] . ' &quot;' . $_SESSION['jrsearch_last_search_string'] . '&quot;';
    if (!empty($_post['_1']) && $_post['_1'] != 'all') {
        $_tm = explode(',', $_post['_1']);
        if ($_tm && is_array($_tm)) {
            $_at = array();
            foreach ($_tm as $mod) {
                if (isset($_rm[$mod])) {
                    $_at[$mod] = $_rm[$mod];
                    $ttl       = $_mods[$mod]['module_name'] . ' ' . $_ln['jrSearch'][8] . ' &quot;' . $_SESSION['jrsearch_last_search_string'] . '&quot;';
                }
            }
            if (count($_at) > 0) {
                $_rm = $_at;
            }
        }
    }

    jrCore_page_title($ttl);
    $out = jrCore_parse_template('header.tpl');

    // figure pagebreak
    $page = 1;
    if (!empty($_post['_2'])) {
        $page = (int) $_post['_2'];
    }
    $pbrk = (isset($_conf['jrSearch_index_limit'])) ? intval($_conf['jrSearch_index_limit']) : 4;
    if (!empty($_post['_3'])) {
        $pbrk = (int) $_post['_3'];
    }

    // Search string must be 3 chars or longer
    if (strlen($_post['search_string']) >= 3) {

        // Check for custom/additional search fields
        if (isset($_conf['jrSearch_search_fields']) && strlen($_conf['jrSearch_search_fields']) > 0) {
            $_af = explode("\n", $_conf['jrSearch_search_fields']);
            if ($_af && is_array($_af)) {
                $_pf = array();
                foreach ($_mods as $dir => $_in) {
                    if (isset($_rm[$dir])) {
                        if (isset($_in['module_prefix']) && strlen($_in['module_prefix']) > 0) {
                            $_pf["{$_in['module_prefix']}"] = $dir;
                        }
                    }
                }
                foreach ($_af as $fld) {
                    if (strpos($fld, ',')) {
                        list($fld,) = explode(',', $fld);
                        $fld = trim($fld);
                    }
                    $fld = trim($fld);
                    // See if we have a lang string
                    if (strpos($fld, ':')) {
                        list($fld, $lng) = explode(':', $fld, 2);
                        $lng = intval($lng);
                    }
                    else {
                        $lng = $fld;
                    }
                    list($pfx,) = explode('_', $fld, 2);
                    if (isset($_pf[$pfx])) {
                        $smod = $_pf[$pfx];
                        if (!isset($_rm[$smod])) {
                            $_rm[$smod]       = array();
                            $_rm[$smod][$fld] = $lng;
                        }
                        else {
                            $tkey = array_keys($_rm[$smod]);
                            $tkey = reset($tkey);
                            if (!function_exists($tkey)) {
                                $fval = $_rm[$smod][$tkey];
                                unset($_rm[$smod]);
                                $_rm[$smod]["{$tkey},{$fld}"] = $fval;
                            }
                        }
                    }
                }
            }
        }

        if (is_array($_rm)) {

            $_fn = array(
                'titles'  => array(),
                'results' => array()
            );
            $_ln = jrUser_load_lang_strings();
            $ltl = '';
            $ttl = 0;

            // Are we doing natural language or boolean?
            // http://dev.mysql.com/doc/refman/5.1/en/fulltext-boolean.html
            // http://dev.mysql.com/doc/refman/5.1/en/fulltext-natural-language.html
            $nat = true;
            $_fm = array();
            $_fo = array();
            $len = 4;
            if (strlen($_post['search_string']) < 5) {
                $len = jrSearch_get_ft_min_word_length();
            }
            $use_like = false;
            if (strlen($_post['search_string']) >= $len && strpos(trim($_post['search_string']), '"') !== 0) {
                $_mt = explode(' ', $_post['search_string']);
                if (stripos($_post['search_string'], ' AND ') || stripos($_post['search_string'], ' OR ') || stripos($_post['search_string'], ' NOT ')) {
                    // apple and pine not pineapple
                    // apple and pine or banana
                    // Clean boolean operators
                    foreach ($_mt AS $k => $word) {
                        $word = strtolower($word);
                        if ($word == 'and') {
                            // With an AND we add + to the preceding AND next word in the search phrase
                            $prv = ($k - 1);
                            if (isset($_mt[$prv])) {
                                $_mt[$prv] = '+' . str_replace(array('+', '-'), '', $_mt[$prv]);
                            }
                            $nxt = ($k + 1);
                            if (isset($_mt[$nxt])) {
                                $_mt[$nxt] = '+' . str_replace(array('+', '-'), '', $_mt[$nxt]);
                            }
                            unset($_mt[$k]);
                        }
                        elseif ($word == 'or') {
                            // Or is the default, so we just remove our OR
                            unset($_mt[$k]);
                        }
                        elseif ($word == 'not') {
                            // With NOT with add a "-" sign to the next word in the search phrase
                            $nxt = ($k + 1);
                            if (isset($_mt[$nxt])) {
                                $_mt[$nxt] = '-' . str_replace(array('+', '-'), '', $_mt[$nxt]);
                            }
                            unset($_mt[$k]);
                        }
                    }
                    $_post['original_search_string'] = $_post['search_string'];
                    $_post['search_string']          = implode(' ', $_mt);
                }

                $_mt = explode(' ', $_post['search_string']);
                if ($_mt && is_array($_mt)) {
                    foreach ($_mt as $str) {
                        $char = substr($str, 0, 1);
                        switch ($char) {
                            case '+';
                            case '-';
                            case '"';
                            case '~';
                                $nat = false;
                                break 2;
                        }
                        if (strpos($str, '*')) {
                            $nat = false;
                            break;
                        }
                    }
                }
                $sst = jrCore_db_escape($_post['search_string']);
                $tbl = jrCore_db_table_name('jrSearch', 'fulltext');

                // Get search method
                if (!isset($_conf['jrSearch_method'])) {
                    $_conf['jrSearch_method'] = 'both';
                }
                $smd = 'IN BOOLEAN MODE';
                switch ($_conf['jrSearch_method']) {
                    case 'natural':
                        $smd = 'IN NATURAL LANGUAGE MODE';
                        break;
                    default:
                        if ($nat || count($_mt) > 4) {
                            $smd = 'IN NATURAL LANGUAGE MODE';
                        }
                        break;
                }
                $req = "SELECT `s_module` AS m, `s_id` AS i, (MATCH(`s_text`) AGAINST('{$sst}' {$smd}) * `s_mod`) AS s FROM {$tbl}
                     WHERE MATCH(`s_text`) AGAINST('{$sst}' {$smd}) AND `s_module` IN('" . implode("','", array_keys($_rm)) . "')";

                $_rt = jrCore_db_query($req, 'NUMERIC');
                if ($_rt && is_array($_rt)) {
                    // These results are coming out un-ordered - order
                    $_to = array();
                    foreach ($_rt as $v) {
                        if (!isset($_to["{$v['m']}"])) {
                            $_to["{$v['m']}"] = array();
                            $_fm["{$v['m']}"] = array();
                        }
                        $_to["{$v['m']}"]["{$v['i']}"] = $v['s'];
                    }
                    if (count($_to) > 0) {
                        foreach ($_to as $smod => $_svals) {
                            arsort($_to[$smod]);
                            $_fm[$smod] = array_keys($_to[$smod]);
                        }
                    }
                    unset($_to);
                    $use_like = false;
                }
                else {
                    // We came out of our FULLTEXT search with no results - are we going to do a partial match?
                    if (isset($_conf['jrSearch_partial']) && $_conf['jrSearch_partial'] == 'on') {
                        $use_like = true;
                    }
                }
            }
            else {
                $use_like = true;
            }

            // Fall through - did we get anything?
            if ($use_like) {
                // We are searching with a search string that is too short for full text
                // OR we came out of the FULLTEXT search with no matches
                $sst = jrCore_db_escape(str_replace('"', '', $_post['search_string']));
                $tbl = jrCore_db_table_name('jrSearch', 'fulltext');
                $req = "SELECT `s_module` AS m, `s_id` AS i FROM {$tbl} WHERE `s_text` LIKE '%{$sst}%'";
                $_rt = jrCore_db_query($req, 'NUMERIC');
                if ($_rt && is_array($_rt)) {
                    foreach ($_rt as $v) {
                        if (!isset($_fm["{$v['m']}"])) {
                            $_fm["{$v['m']}"] = array();
                            $_fo["{$v['m']}"] = 1;
                        }
                        $_fm["{$v['m']}"][] = $v['i'];
                    }
                    unset($_rt);
                }
                // Reorder so most relevant results are at top
                if (count($_fo) > 0) {
                    $_nm = array();
                    foreach ($_fo as $m => $v) {
                        if (isset($_rm[$m])) {
                            $_nm[$m] = $_rm[$m];
                            unset($_rm[$m]);
                        }
                    }
                    $_rm = array_merge($_rm, $_nm);
                    unset($_nm);
                }
            }

            // Did we get results?
            if (count($_fm) > 0) {
                foreach ($_rm as $mod => $_mod) {

                    if (!jrCore_module_is_active($mod)) {
                        continue;
                    }
                    if (jrSearch_is_excluded_module($mod)) {
                        continue;
                    }
                    $pfx = jrCore_db_get_prefix($mod);
                    if ($pfx) {

                        $fnc = false;
                        foreach ($_mod as $fields => $title) {
                            // A module can give us a custom search function
                            if (function_exists($fields)) {
                                $fnc = $fields;
                            }
                            $_fn['titles'][$mod] = (!empty($_ln[$mod][$title])) ? $_ln[$mod][$title] : $_mods[$mod]['module_name'];
                        }

                        if (!$fnc) {

                            if (!isset($_fm[$mod])) {
                                // no results...
                                continue;
                            }

                            // Let our module handle it if needed
                            $_ag = array(
                                'page'      => $page,
                                'pagebreak' => $pbrk
                            );
                            $_rt = jrCore_trigger_event('jrSearch', 'search_item_ids', $_fm[$mod], $_ag, $mod);
                            if (!$_rt || !is_array($_rt) || !isset($_rt['_items'])) {

                                $key = '_item_id';
                                switch ($mod) {
                                    case 'jrProfile':
                                        $key = '_profile_id';
                                        break;
                                    case 'jrUser':
                                        $key = '_user_id';
                                        break;
                                }
                                $_sc = array(
                                    'page'      => $page,
                                    'pagebreak' => $pbrk
                                );

                                // Prune down our result set if we can...
                                $rcnt = count($_fm[$mod]);
                                if ($pbrk > 0 && $rcnt > $pbrk && $rcnt > 200 && (!isset($_conf['jrSearch_optimize']) || $_conf['jrSearch_optimize'] == 'on')) {
                                    $_fm[$mod] = array_slice($_fm[$mod], 0, (($page * 3) * $pbrk), true);
                                }
                                $_sc['use_total_row_count'] = count($_fm[$mod]);
                                $_sc['search']              = array("{$key} in " . implode(',', $_fm[$mod]));

                                $_rt = jrCore_db_search_items($mod, $_sc);
                            }

                        }
                        else {
                            // Trigger to get actual included item_ids
                            if (isset($_fm[$mod]) && count($_fm[$mod]) > 0) {
                                $_ag = array(
                                    'page'      => $page,
                                    'pagebreak' => $pbrk
                                );
                                $_rt = jrCore_trigger_event('jrSearch', 'search_item_ids', $_fm[$mod], $_ag, $mod);
                                if (!$_rt || !is_array($_rt) || !isset($_rt['_items'])) {
                                    // Custom module function for results
                                    $_rt = $fnc($_post['search_string'], $pbrk, $page);
                                }
                            }
                            else {
                                $_rt = $fnc($_post['search_string'], $pbrk, $page);
                            }
                        }

                        if ($_rt && is_array($_rt) && isset($_rt['_items']) && count($_rt['_items']) > 0) {
                            if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$mod}_item_search.tpl")) {
                                $_fn['results'][$mod] = jrCore_parse_template("{$mod}_item_search.tpl", $_rt);
                            }
                            elseif (is_file(APP_DIR . "/modules/{$mod}/templates/item_search.tpl")) {
                                $_fn['results'][$mod] = jrCore_parse_template('item_search.tpl', $_rt, $mod);
                            }
                            elseif (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$mod}_item_list.tpl")) {
                                $_fn['results'][$mod] = jrCore_parse_template("{$mod}_item_list.tpl", $_rt);
                            }
                            elseif (is_file(APP_DIR . "/modules/{$mod}/templates/item_list.tpl")) {
                                $_fn['results'][$mod] = jrCore_parse_template('item_list.tpl', $_rt, $mod);
                            }
                            $_fn['info'][$mod] = $_rt['info'];
                            $ttl += count($_rt['_items']);
                            $ltl = $_fn['titles'][$mod];
                        }
                    }
                }
            }
            else {
                if (isset($_conf['jrSearch_log_no_result']) && $_conf['jrSearch_log_no_result'] == 'on') {
                    jrCore_logger('MIN', "no search results found for &quot;" . jrCore_entity_string($_post['search_string']) . '&quot;');
                }
            }
            if (isset($_post['original_search_string'])) {
                $_fn['search_string'] = jrCore_entity_string(strip_tags(trim($_post['original_search_string'])));
            }
            else {
                $_fn['search_string'] = jrCore_entity_string(strip_tags(trim($_post['search_string'])));
            }
            $_fn['pagebreak']    = $pbrk;
            $_fn['page']         = $page;
            $_fn['modules']      = (isset($_post['_1']) && isset($_mods["{$_post['_1']}"])) ? $_post['_1'] : 'all';
            $_fn['module_count'] = count($_fn['results']);
            if ($_fn['module_count'] === 1) {
                $_fn['titles']['all'] = $ltl;
            }
            $out .= jrCore_parse_template('search_results.tpl', $_fn, 'jrSearch');

            // Save search details
            if (jrUser_is_logged_in()) {
                $_data = array(
                    'search_string'  => $_post['search_string'],
                    'search_module'  => (isset($_post['_1']) && isset($_mods["{$_post['_1']}"])) ? $_post['_1'] : 'all',
                    'search_results' => $ttl
                );
                jrCore_db_create_item('jrSearch', $_data, null, false);
            }
        }
    }
    else {
        // We have too short of a search string
        $_fn = array(
            'pagebreak'     => $pbrk,
            'page'          => $page,
            'modules'       => 'all',
            'results'       => array(),
            'search_string' => jrCore_entity_string(strip_tags(trim($_post['search_string'])))
        );
        $out .= jrCore_parse_template('search_results.tpl', $_fn, 'jrSearch');
    }
    $out .= jrCore_parse_template('footer.tpl');
    ini_set('session.cache_limiter', 'private');
    return $out;
}
