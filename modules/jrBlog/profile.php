<?php
/**
 * Jamroom Blog module
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
// default
//------------------------------
function profile_view_jrBlog_default($_profile, $_post, $_user, $_conf)
{
    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        return false;
    }
    switch ($_post['_1']) {

        // list all categories OR blog posts in a category
        case 'category':
            $page = (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) ? $_post['p'] : 1;
            $_sp  = array(
                'search'    => array(
                    "_profile_id = {$_profile['_profile_id']}",
                    'blog_publish_date < ' . time()
                ),
                'order_by'  => array(
                    'blog_publish_date' => 'desc'
                ),
                'pagebreak' => 10,
                'page'      => $page,
                'pager'     => true
            );
            // See if we have been given a specific category
            if (isset($_post['_2']) && strlen($_post['_2']) > 0 && $_post['_2'] !== 'default') {
                $_sp['search'][] = "blog_category_url = " . rawurlencode($_post['_2']);
            }
            else {
                $_sp['group_by'] = 'blog_category_url';
            }
            // Get results
            $_it = jrCore_db_search_items('jrBlog', $_sp);
            if ($_it && is_array($_it) && isset($_it['_items'])) {
                $_profile = $_profile + $_it;
                $out  = jrCore_parse_template('item_list.tpl', $_profile, 'jrBlog');
                $out .= jrCore_parse_template('list_pager.tpl', $_it, 'jrCore');
                return $out;
            }
            break;

        // Profile Blog Feed (deprecated - do not use)
        case 'feed':

            if (jrCore_module_is_active('jrFeed')) {
                $furl = jrCore_get_module_url('jrFeed');
                $murl = jrCore_get_module_url('jrBlog');
                header('HTTP/1.1 301 Moved Permanently');
                jrCore_location("{$_conf['jrCore_base_url']}/{$furl}/{$murl}/{$_profile['profile_url']}");
            }
            jrCore_page_not_found();
            break;
    }
    return false;
}
