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

//------------------------------
// Recommend results
// In: $_post['recommend_string']
// In: $_post['_1'] = page
// In: $_post['_2'] = pagebreak
//------------------------------
function view_jrRecommend_results($_post, $_user, $_conf)
{
    if (!isset($_post['recommend_string']) || strlen($_post['recommend_string']) === 0) {
        jrCore_form_result('referrer');
    }
    // Do recommend search and get results
    $out = jrCore_parse_template('header.tpl');
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        $_post['_1'] = 1;
    }
    if (!isset($_post['_2']) || !jrCore_checktype($_post['_2'], 'number_nz')) {
        $_post['_2'] = 10;
    }
    // Find profiles that are like what we are looking for
    $_sc = array(
        'search'   => array(
            "profile_influences like %{$_post['recommend_string']}%",
        ),
        'order_by' => array(
            '_created' => 'numerical_desc'
        ),
        'limit'    => 100
    );
    // Get profiles this user belongs to
    $_pr = jrProfile_get_user_linked_profiles($_user['_user_id']);
    if ($_pr && is_array($_pr)) {
        $_sc['search'][] = '_profile_id not_in ' . implode(',', array_keys($_pr));
    }
    $_rt = jrCore_db_search_items('jrProfile', $_sc);
    if ($_rt && isset($_rt['_items']) && is_array($_rt['_items'])) {
        $_id = array();
        foreach ($_rt['_items'] as $_item) {
            if (isset($_item['quota_jrRecommend_allowed']) && $_item['quota_jrRecommend_allowed'] == 'on') {
                $_id[] = (int) $_item['_profile_id'];
            }
        }
        if (count($_id) > 0) {
            $_sc = array(
                'search'    => array(
                    '_profile_id in ' . implode(',', $_id),
                ),
                'order_by'  => array(
                    'audio_file_stream_count' => 'numerical_desc'
                ),
                'page'      => (int) $_post['_1'],
                'pagebreak' => (int) $_post['_2'],
            );
            $_rt = jrCore_db_search_items('jrAudio', $_sc);
            if ($_rt && isset($_rt['_items']) && is_array($_rt['_items'])) {
                // Process items and pager
                $out .= jrCore_parse_template('item_list.tpl', $_rt, 'jrAudio');
                $out .= jrCore_parse_template('list_pager.tpl', $_rt, 'jrCore');
            }
        }
    }
    else {
        // No items
        $out .= jrCore_parse_template('no_results.tpl', $_rt, 'jrRecommend');
    }
    $out .= jrCore_parse_template('footer.tpl');
    echo $out;
}
