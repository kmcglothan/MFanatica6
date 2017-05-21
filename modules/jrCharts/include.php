<?php
/**
 * Jamroom Advanced Charts module
 *
 * copyright 2017 The Jamroom Network
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
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function jrCharts_meta()
{
    $_tmp = array(
        'name'        => 'Advanced Charts',
        'url'         => 'charts',
        'version'     => '1.0.8',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Add Charting of counts over time to Item Lists',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/274/advanced-charts',
        'category'    => 'listing',
        'requires'    => 'jrCore:6.0.4',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrCharts_init()
{
    // We provide support for additional jrList params
    jrCore_register_event_listener('jrCore', 'db_search_params', 'jrCharts_db_search_params_listener');
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrCharts_db_search_items_listener');

    // Once a day we build the previous days stats
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrCharts_daily_maintenance_listener');

    // Delete bad history entries
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrCharts_verify_module_listener');

    // Register our tools
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrCharts', 'get_fields', array('Chart Fields', 'View DataStore fields that can be charted in your system'));
    jrCore_register_module_feature('jrCore', 'javascript', 'jrCharts', true);

    // Core Quota support
    $_tmp = array(
        'label' => 'Allowed in Charts',
        'help'  => 'If checked, items created by Users with Profiles in this Quota can have their items appear in a chart.'
    );
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrCharts', 'on', $_tmp);

    return true;
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Fix bad count values in history
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrCharts_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    $tbl = jrCore_db_table_name('jrCharts', 'history');
    $req = "DELETE FROM {$tbl} WHERE chart_field LIKE '%_count_count'";
    jrCore_db_query($req);
    return $_data;
}

/**
 * Setup chart history for comparison
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrCharts_db_search_params_listener($_data, $_user, $_conf, $_args, $event)
{
    // Params we support are:
    // chart_field=<counter_field_to_chart>
    // chart_days=<chart_over_last_x_days>
    // chart_day_start=<day_to_start_chart_on>
    // chart_day_end=<day_to_end_chart_on>
    if (isset($_data['chart_field']{0})) {

        @ini_set('memory_limit', '512M');
        // First - get our current order
        if (isset($_data['chart_day_end']) && jrCore_checktype($_data['chart_day_end'], 'number_nz') && strlen($_data['chart_day_end']) === 8) {

            // We've been given and END DATE - we are charting between 2 known dates in the past
            $tbl = jrCore_db_table_name('jrCharts', 'history');
            $req = "SELECT chart_counts FROM {$tbl} WHERE chart_field = '" . jrCore_db_escape($_data['chart_field']) . "' AND chart_date = '" . $_data['chart_day_end'] . "'";
            $_cb = jrCore_db_query($req, 'SINGLE');
            if ($_cb && isset($_cb['chart_counts']) && strlen($_cb['chart_counts']) > 0) {
                $_cb = json_decode($_cb['chart_counts'], true);
            }
            else {
                // Invalid chart start date
                return $_data;
            }
        }
        else {

            // Use current values - first we have to get the profiles that allow charting so we only
            // return items that can appear in our results
            $cky = "{$_args['module']}-{$_data['chart_field']}-{$_data['chart_days']}";
            $_cb = jrCore_is_cached($_args['module'], $cky, false);
            if (!$_cb) {

                // Get Profile Quotas that DO NOT allow charting
                $tbq = jrCore_db_table_name('jrProfile', 'quota_value');
                $req = "SELECT quota_id FROM {$tbq} WHERE `module` = 'jrCharts' AND `name` = 'allowed' AND `value` = 'off'";
                $_rt = jrCore_db_query($req, 'quota_id');
                if ($_rt && is_array($_rt)) {

                    // We have Quotas that do not allow charting - get only _item_id's for profiles that are allowed to chart
                    $tbl = jrCore_db_table_name($_args['module'], 'item_key');
                    $tbp = jrCore_db_table_name('jrProfile', 'item_key');
                    if ($_args['module'] == 'jrProfile') {
                        // Our query can be a little more compact for a profile chart
                        $req = "SELECT a.`_item_id` AS i, a.`value` AS v FROM {$tbl} a WHERE a.`key` = '" . jrCore_db_escape($_data['chart_field']) . "' AND a.`_item_id` IN(
                                    SELECT b.`_item_id` FROM {$tbp} b WHERE b.`key` = 'profile_quota_id' AND b.`value` NOT IN(" . implode(',', array_keys($_rt)) . ")
                                ) ORDER BY (a.`value` + 0) DESC, a.`_item_id` DESC";
                    }
                    else {
                        $req = "SELECT a.`_item_id` AS i, a.`value` AS v FROM {$tbl} a WHERE a.`key` = '" . jrCore_db_escape($_data['chart_field']) . "' AND a.`_item_id` IN(
                                  SELECT b.`_item_id` FROM {$tbl} b WHERE b.`key` = '_profile_id' AND b.`value` IN(
                                    SELECT c.`_item_id` FROM {$tbp} c WHERE c.`key` = 'profile_quota_id' AND c.`value` NOT IN(" . implode(',', array_keys($_rt)) . ")
                                  )
                                ) ORDER BY (a.`value` + 0) DESC, a.`_item_id` DESC";
                    }
                    $_cb = jrCore_db_query($req, 'i', false, 'v');
                    if (count($_cb) === 0) {
                        // No items match - could be ALL quotas are not allowed
                        jrCore_add_to_cache($_args['module'], $cky, 'no_quotas_allowed', 0, 0, false);
                        if (isset($_data['search'])) {
                            unset($_data['search']);
                        }
                        $_data['search'] = array("_item_id < 0"); // Set impossible condition to match so we get no results
                        return $_data;
                    }
                    // Add result set to cache
                    jrCore_add_to_cache($_args['module'], $cky, $_cb, 0, 0, false);

                }
                else {

                    // We don't have to worry about quotas
                    $_cb = jrCore_db_get_all_key_values($_args['module'], $_data['chart_field']);
                    if ($_cb && is_array($_cb)) {
                        arsort($_cb, SORT_NUMERIC);
                    }
                    jrCore_add_to_cache($_args['module'], $cky, $_cb, 0, 0, false);

                }
            }
            elseif ($_cb == 'no_quotas_allowed') {
                if (isset($_data['search'])) {
                    unset($_data['search']);
                }
                $_data['search'] = array("_item_id < 0"); // Set impossible condition to match so we get no results
                return $_data;
            }
        }
        if (!isset($_cb) || !is_array($_cb)) {
            return $_data;
        }

        // GET Start Date
        // get our historical data based on the start date for the chart
        if (isset($_data['chart_days']) && jrCore_checktype($_data['chart_days'], 'number_nz')) {
            $bdate = strftime('%Y%m%d', intval(time() - ($_data['chart_days'] * 86400)));
        }
        elseif (isset($_data['chart_day_start']) && jrCore_checktype($_data['chart_day_start'], 'number_nz') && strlen($_data['chart_day_start']) === 8) {
            $bdate = (int) $_data['chart_day_start'];
        }
        else {
            // Default is 7 days
            $bdate = strftime('%Y%m%d', (time() - (7 * 86400)));
        }

        $tbl = jrCore_db_table_name('jrCharts', 'history');
        $req = "SELECT chart_counts FROM {$tbl} WHERE chart_field = '" . jrCore_db_escape($_data['chart_field']) . "' AND chart_date = '{$bdate}'";
        $_ce = jrCore_db_query($req, 'SINGLE');
        if (isset($_ce['chart_counts']) && strlen($_ce['chart_counts']) > 0) {
            $_ce = json_decode($_ce['chart_counts'], true);
        }
        else {
            // Invalid chart start date
            $_ce = array();
        }

        // Now - to actually create the chart we need to go through the EXISTING items that are now
        // ordered by counts, and get the DIFFERENCE between now and the chart_days amount.  This will
        // give us a new set of numbers that we can then order by and use as our IN() op in a search
        // We use $GLOBALS directly here (which is normally not recommended), but this op can use
        // A LOT of RAM and a call to jrCore_set_flag() would duplicate the array.
        // These are used in jrCharts_db_search_items_listener() below
        $GLOBALS["{$_args['module']}_jrcharts_cnt"] = array();
        $GLOBALS["{$_args['module']}_jrcharts_old"] = array();
        $num = 1;
        // $_cb contains our current items, order by count
        foreach ($_cb as $iid => $count) {
            if (isset($_ce[$iid])) {
                // We have a history entry - get diff
                $GLOBALS["{$_args['module']}_jrcharts_cnt"][$iid] = (int) ($count - $_ce[$iid]);
                $GLOBALS["{$_args['module']}_jrcharts_old"][$iid] = $num++;
            }
            else {
                // This is a new entry - was created AFTER our chart begin date
                $GLOBALS["{$_args['module']}_jrcharts_cnt"][$iid] = (int) $count;
            }
            unset($_ce[$iid]);
        }
        unset($_cb);

        // Now all that is left is our items with their count during the chart_days span - order it
        arsort($GLOBALS["{$_args['module']}_jrcharts_cnt"], SORT_NUMERIC);

        // We don't need to hang on to the entire array if only doing a specific page/limit
        if (isset($_data['limit']) && jrCore_checktype($_data['limit'], 'number_nz')) {
            $_data['search'][] = '_item_id in ' . implode(',', array_slice(array_keys($GLOBALS["{$_args['module']}_jrcharts_cnt"]), 0, $_data['limit'], true));
        }
        else {
            // We have to return the entire set so pagination works properly
            $_data['search'][] = '_item_id in ' . implode(',', array_keys($GLOBALS["{$_args['module']}_jrcharts_cnt"]));
        }
    }
    return $_data;
}

/**
 * jrCharts_db_search_items_listener
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrCharts_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if (!isset($GLOBALS["{$_args['module']}_jrcharts_cnt"]) || !isset($_data['_items'])) {
        // We did not do a chart - return
        return $_data;
    }
    // Add in our chart vars
    $num = 1;
    if (isset($_data['info']['page']) && $_data['info']['page'] > 1 && isset($_data['info']['pagebreak'])) {
        $num = (($_data['info']['page'] - 1) * $_data['info']['pagebreak']);
    }
    foreach ($_data['_items'] as $k => $_inf) {
        if ($_args['module'] == 'jrProfile') {
            $iid = (int) $_inf['_profile_id'];
        }
        elseif ($_args['module'] == 'jrUser') {
            $iid = (int) $_inf['_user_id'];
        }
        else {
            $iid = (int) $_inf['_item_id'];
        }
        $_data['_items'][$k]['chart_count'] = (int) $GLOBALS["{$_args['module']}_jrcharts_cnt"][$iid];
        $_data['_items'][$k]['chart_position'] = $num;

        // See if this entry is moving up or down
        if (isset($GLOBALS["{$_args['module']}_jrcharts_old"][$iid])) {
            $_data['_items'][$k]['chart_new_entry'] = 'no';
            if ($num < $GLOBALS["{$_args['module']}_jrcharts_old"][$iid]) {
                $_data['_items'][$k]['chart_change'] = (int) ($GLOBALS["{$_args['module']}_jrcharts_old"][$iid] - $num);
                $_data['_items'][$k]['chart_direction'] = 'up';
            }
            elseif ($num > $GLOBALS["{$_args['module']}_jrcharts_old"][$iid]) {
                $_data['_items'][$k]['chart_change'] = (int) ($num - $GLOBALS["{$_args['module']}_jrcharts_old"][$iid]);
                $_data['_items'][$k]['chart_direction'] = 'down';
            }
            else {
                $_data['_items'][$k]['chart_change'] = 0;
                $_data['_items'][$k]['chart_direction'] = 'same';
            }
        }
        // See if this is a new entry in this chart
        else {
            $_data['_items'][$k]['chart_new_entry'] = 'yes';
            $_data['_items'][$k]['chart_change'] = 0;
            $_data['_items'][$k]['chart_direction'] = 'same';
        }
        $num++;
    }
    unset($GLOBALS["{$_args['module']}_jrcharts_cnt"]);
    unset($GLOBALS["{$_args['module']}_jrcharts_old"]);
    return $_data;
}

/**
 * jrCharts_daily_maintenance_listener
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrCharts_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    // Go through each data store and record our chart history
    $num = 0;
    $ctb = jrCore_db_table_name('jrCharts', 'history');
    $dat = strftime('%Y%m%d', (time() - 86400));
    $_ds = jrCore_get_datastore_modules();
    if ($_ds && is_array($_ds)) {
        foreach ($_ds as $module => $prefix) {
            if (jrCore_db_table_exists($module, 'item_key')) {
                $tbl = jrCore_db_table_name($module, 'item_key');
                $req = "SELECT `key` FROM {$tbl} WHERE `key` LIKE '%_count' GROUP BY `key`";
                $_rt = jrCore_db_query($req, 'key', false, 'key');
                if ($_rt && is_array($_rt)) {
                    foreach ($_rt as $k) {
                        $k = jrCore_db_escape($k);
                        $req = "SELECT `_item_id` AS i, `value` AS v FROM {$tbl} WHERE `key` = '{$k}' ORDER BY (`value` + 0) DESC";
                        $_cb = jrCore_db_query($req, 'i', false, 'v');
                        if ($_cb && is_array($_cb)) {
                            foreach ($_cb as $ck => $v) {
                                $_cb[$ck] = (int) $v;
                            }
                            $_cb = jrCore_db_escape(json_encode($_cb));
                            $req = "INSERT INTO {$ctb} (chart_date,chart_field,chart_counts) VALUES ('{$dat}','{$k}','{$_cb}') ON DUPLICATE KEY UPDATE chart_counts = VALUES(`chart_counts`)";
                            $cnt = jrCore_db_query($req, 'COUNT');
                            if (!$cnt || $cnt === 0) {
                                jrCore_logger('CRI', "error storing chart counts for field: {$k}");
                            }
                            else {
                                $num++;
                            }
                        }
                    }
                }
            }
        }
    }
    if ($num > 0) {
        jrCore_logger('INF', "successfully updated chart positions for {$num} DataStore fields");
    }

    // Old History cleanup
    if (isset($_conf['jrCharts_history_days']) && jrCore_checktype($_conf['jrCharts_history_days'], 'number_nz')) {
        $old = strftime('%Y%m%d', (time() - ($_conf['jrCharts_history_days'] * 86400)));
        $req = "DELETE FROM {$ctb} WHERE chart_date < '{$old}'";
        $cnt = jrCore_db_query($req, 'COUNT');
        if ($cnt && $cnt > 0) {
            jrCore_logger('INF', "successfully deleted {$cnt} Chart history entries that had expired");
        }
    }

    return $_data;
}
