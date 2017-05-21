<?php
/**
 * Jamroom Image Galleries module
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
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//------------------------------
// profile_default
//------------------------------
function profile_view_jrGallery_default($_profile, $_post, $_user, $_conf)
{
    // Make sure we get a valid title to show
    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        return false;
    }

    $_lng = jrUser_load_lang_strings();

    // View a specific image in a gallery
    if (jrCore_checktype($_post['_1'], 'number_nz') && (!isset($_post['_2']) || $_post['_2'] != 'all')) {
        $_rt = jrCore_db_get_item('jrGallery', $_post['_1']);
        if ($_rt && is_array($_rt) && isset($_rt['_profile_id']) && $_rt['_profile_id'] == $_profile['_profile_id']) {

            // Check for pending
            if (isset($_rt['gallery_pending']) && $_rt['gallery_pending'] == '1' && !jrUser_is_profile_owner($_rt['_profile_id'])) {
                jrCore_page_not_found();
            }

            if (isset($_rt['gallery_image_title']) && strlen($_rt['gallery_image_title']) > 0) {
                $title = $_rt['gallery_image_title'];
            }
            else {
                $title = jrGallery_title_name($_rt['gallery_image_name']);
            }
            jrCore_page_title("{$title} - {$_lng['jrGallery']['menu']} - {$_profile['profile_name']}");
            $_profile['item'] = $_rt;
            $key              = md5("{$_rt['_profile_id']}-{$_rt['gallery_title_url']}");
            if (!isset($_SESSION['jrGallery_active_gallery'])) {
                $_SESSION['jrGallery_active_gallery'] = $key;
            }
            elseif ($_SESSION['jrGallery_active_gallery'] != $key) {
                // We've changed galleries - reset
                $_SESSION['jrGallery_active_gallery'] = $key;
                $_SESSION['jrGallery_page_num']       = 1;
            }

            // Do we have a bad gallery_title_url?
            if (isset($_rt['gallery_title']) && strlen(trim($_rt['gallery_title'])) > 0 && (!isset($_rt['gallery_title_url']) || strlen(trim($_rt['gallery_title_url'])) === 0)) {
                $_up = array(
                    'gallery_title_url' => jrCore_url_string($_rt['gallery_title'])
                );
                jrCore_db_update_item('jrGallery', $_rt['_item_id'], $_up);
                $_rt['gallery_title_url'] = $_up['gallery_title_url'];
            }

            // get the NEXT and PREV ids so the user can move to the next item.
            $_sp = array(
                'search'                       => array(
                    "_profile_id = {$_rt['_profile_id']}",
                    "gallery_title_url = {$_rt['gallery_title_url']}",
                ),
                'return_keys'                  => array('_item_id', 'gallery_order', 'gallery_image_name', 'gallery_image_title_url', 'profile_url'),
                'order_by'                     => array('gallery_order' => 'numerical_asc'),
                'exclude_jrUser_keys'          => true,
                'exclude_jrProfile_quota_keys' => true,
                'limit'                        => 10000
            );
            // See how we are ordering these images
            if (isset($_profile['quota_jrGallery_gallery_order']) && $_profile['quota_jrGallery_gallery_order'] == 'off') {
                $_sp['order_by'] = array('_created' => 'desc');
            }
            if (jrUser_is_admin() || jrProfile_is_profile_owner($_profile['_profile_id'])) {
                $_sp['ignore_pending'] = true;
            }
            $_im = jrCore_db_search_items('jrGallery', $_sp);
            if ($_im && is_array($_im) && isset($_im['_items'])) {
                $prev = 0;
                $next = 0;
                foreach ($_im['_items'] as $k => $i) {
                    if ($i['_item_id'] == $_rt['_item_id']) {
                        $nxt = ($k + 1);
                        if (isset($_im['_items'][$nxt])) {
                            $next = $_im['_items'][$nxt];
                        }
                        $prv = ($k - 1);
                        if (isset($_im['_items'][$prv])) {
                            $prev = $_im['_items'][$prv];
                        }
                        break;
                    }
                }
                $_profile['prev'] = $prev;
                $_profile['next'] = $next;
            }

            // Meta social tags
            if (isset($_rt) && is_array($_rt)) {
                // meta for detail page
                $_rep = array(
                    'item'   => $_rt,
                    'method' => jrCore_get_server_protocol()
                );
                $html = jrCore_parse_template('item_detail_meta.tpl', $_rep, 'jrGallery');
                jrCore_set_flag('meta_html', $html);
            }
            return jrCore_parse_template('item_detail.tpl', $_profile, 'jrGallery');
        }
        // Fall through for gallery named as a number
    }

    // View a specific Gallery
    $page = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $page = (int) $_post['p'];
    }
    $_sp = array(
        'search'    => array(
            "_profile_id = {$_profile['_profile_id']}",
        ),
        'order_by'  => array('gallery_order' => 'numerical_asc'),
        'pagebreak' => (isset($_conf['jrGallery_all_pagebreak']) && jrCore_checktype($_conf['jrGallery_all_pagebreak'], 'number_nz')) ? $_conf['jrGallery_all_pagebreak'] : 40,
        'page'      => $page
    );
    // See how we are ordering these images
    if (isset($_profile['quota_jrGallery_gallery_order']) && $_profile['quota_jrGallery_gallery_order'] == 'off') {
        $_sp['order_by'] = array('_created' => 'desc');
    }
    if (isset($_post['_1']) && strlen($_post['_1']) > 0 && $_post['_1'] != 'all') {
        $_sp['search'][] = "gallery_title_url = " . rawurlencode($_post['_1']);
    }

    if (jrUser_is_admin() || jrProfile_is_profile_owner($_profile['_profile_id'])) {
        $_sp['ignore_pending'] = true;
    }

    // Get results
    $_it = jrCore_db_search_items('jrGallery', $_sp);

    if ($_it && is_array($_it) && isset($_it['_items'])) {

        if ($_post['_1'] == 'all') {
            $_profile['show_all_galleries'] = true;
        }

        $_profile['_items']    = $_it['_items'];
        $_profile['info']      = $_it['info'];
        $_profile['img_width'] = (isset($_COOKIE['jr_gallery_xup_width'])) ? $_COOKIE['jr_gallery_xup_width'] : '24.5';
        jrCore_page_title("{$_profile['_items'][0]['gallery_title']} - {$_lng['jrGallery']['menu']} - {$_profile['profile_name']}");

        // Meta social tags
        if (isset($_it['_items']) && is_array($_it['_items'])) {
            // meta for detail page
            $_rep = array(
                'item'   => $_it['_items'][0],
                '_items' => $_it['_items'],
                'method' => jrCore_get_server_protocol()
            );
            $html = jrCore_parse_template('item_gallery_meta.tpl', $_rep, 'jrGallery');
            jrCore_set_flag('meta_html', $html);
        }
        unset($_it);
        return jrCore_parse_template('item_gallery.tpl', $_profile, 'jrGallery');
    }

    jrCore_page_not_found();
    return false;
}
