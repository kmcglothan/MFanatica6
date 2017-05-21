<?php
/**
 * Jamroom 5 Sitemap Generator module
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
 * @copyright 2012 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * jrSitemap_meta
 */
function jrSitemap_meta()
{
    $_tmp = array(
        'name'        => 'Sitemap Generator',
        'url'         => 'sitemap',
        'version'     => '1.1.3',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Create and maintain an XML Sitemap used by search engines',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/741/sitemap-generator',
        'license'     => 'mpl',
        'category'    => 'tools'
    );
    return $_tmp;
}

/**
 * jrSitemap_init
 */
function jrSitemap_init()
{
    // After the core has parsed the URL, we can check for a sitemap call
    jrCore_register_event_listener('jrCore', 'parse_url', 'jrSitemap_parse_url_listener');

    // Tool to manually create sitemap
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrSitemap', 'create', array('Create Site Map', 'Create or Update the Sitemap'));

    // Maintain our Sitemap on a daily basis
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrSitemap_daily_maintenance_listener');

    // Our "map" event trigger
    jrCore_register_event_trigger('jrSitemap', 'sitemap_site_pages', 'Fired when gathering relative URLs for sitemap');

    // We don't need a session when getting a sitemap
    jrCore_register_module_feature('jrUser', 'skip_session', 'jrSitemap', 'default');

    return true;
}

//---------------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------------

/**
 * Generates an XML Sitemap
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrSitemap_parse_url_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['module_url']) && $_data['module_url'] === 'sitemap.xml') {

        $out = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $out .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        if (jrCore_checktype($_conf['jrSitemap_file_count'], 'number_nz')) {

            $tbl = jrCore_db_table_name('jrCore', 'setting');
            $req = "SELECT `updated` FROM {$tbl} WHERE `module` = 'jrSitemap' AND `name` = 'file_count'";
            $_rt = jrCore_db_query($req, 'SINGLE');

            $mod = (isset($_rt['updated'])) ? $_rt['updated'] : time();
            $mod = date('c', $mod);
            $mrl = jrCore_get_module_url('jrSitemap');
            $num = 1;

            while ($num <= $_conf['jrSitemap_file_count']) {
                $url = "{$_conf['jrCore_base_url']}/{$mrl}/sitemap{$num}.xml";
                $out .= "<sitemap><loc>{$url}</loc><lastmod>{$mod}</lastmod></sitemap>\n";
                $num++;
            }

        }
        $out .= '</sitemapindex>';
        header("Content-Type: text/xml; charset=utf-8");
        echo $out;
        exit;
    }
    return $_data;
}

/**
 * Keep sitemap.xml up to date
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrSitemap_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    jrSitemap_create_sitemap(false);
    return $_data;
}

/**
 * Create the site map
 * @param $update bool set to TRUE to show modal updates
 * @return bool
 */
function jrSitemap_create_sitemap($update = false)
{
    global $_mods, $_conf;
    jrCore_logger('INF', 'create XML site map - starting');

    // Cleanup old Site map XML files
    if (jrCore_checktype($_conf['jrSitemap_file_count'], 'number_nz')) {
        $i = 1;
        while ($i <= $_conf['jrSitemap_file_count']) {
            jrCore_delete_media_file(0, "sitemap{$i}.xml");
            $i++;
        }
    }

    // SITE PAGES
    $updv = 'daily';
    if (isset($_conf['jrSitemap_site_freq']) && strlen($_conf['jrSitemap_site_freq']) > 0) {
        $updv = $_conf['jrSitemap_site_freq'];
    }

    $_map   = array();
    $_map[] = '/';

    // Include all active module indexes...
    foreach ($_mods as $mod => $_inf) {
        if (isset($_inf['module_prefix']) && strlen($_inf['module_prefix']) > 0 && is_file(APP_DIR . "/modules/{$mod}/templates/index.tpl")) {
            switch ($mod) {
                case 'jrUser':
                    break;
                default:
                    if (jrCore_module_is_active($mod)) {
                        $_map[] = "/{$_inf['module_url']}";
                    }
                    break;
            }
        }
    }

    // Let modules know we are looking for pages
    if ($update) {
        jrCore_form_modal_notice('update', 'triggering modules for site map URLs');
    }
    $_map = jrCore_trigger_event('jrSitemap', 'sitemap_site_pages', $_map);
    jrCore_create_media_directory(0);

    // Create our first output
    $cdr = jrCore_get_module_cache_dir('jrSitemap');
    $now = strftime('%Y-%m-%d');
    $out = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach ($_map as $url) {
        if (strpos($url, $_conf['jrCore_base_url']) !== 0) {
            $url = "{$_conf['jrCore_base_url']}{$url}";
        }
        $out .= "\n<url>\n<loc>{$url}</loc>\n<priority>1.0</priority>\n<changefreq>{$updv}</changefreq>\n<lastmod>{$now}</lastmod>\n</url>";
    }
    $out .= "\n</urlset>";
    jrCore_write_to_file("{$cdr}/sitemap1.xml", $out);
    jrCore_write_media_file(0, 'sitemap1.xml', "{$cdr}/sitemap1.xml", 'public-read');
    unlink("{$cdr}/sitemap1.xml");

    // PROFILES
    $updv = 'daily';
    if (isset($_conf['jrSitemap_profile_freq']) && strlen($_conf['jrSitemap_profile_freq']) > 0) {
        $updv = $_conf['jrSitemap_profile_freq'];
    }

    // Get privacy settings for our quotas
    $_qt = array();
    $tbl = jrCore_db_table_name('jrProfile', 'quota_value');
    $req = "SELECT `quota_id`, `name`, `value` FROM {$tbl} WHERE `module` = 'jrProfile' AND `name` IN('default_privacy', 'privacy_changes')";
    $_tm = jrCore_db_query($req, 'NUMERIC');
    if ($_tm && is_array($_tm)) {
        foreach ($_tm as $_q) {
            $qid = (int) $_q['quota_id'];
            if (!isset($_qt[$qid])) {
                $_qt[$qid] = array();
            }
            $_qt[$qid]["{$_q['name']}"] = $_q['value'];
        }
    }

    // Go through our profiles (1000 at a time)
    $mapid = 2;
    $start = 0;
    $found = true;
    while ($found) {
        $_src = array(
            'search'         => array(
                "_item_id > {$start}"
            ),
            'return_keys'    => array('_profile_id', 'profile_url', 'profile_private', 'profile_quota_id'),
            'order_by'       => array(
                '_item_id' => 'asc'
            ),
            'limit'          => 1000,
            'skip_triggers'  => true,
            'ignore_pending' => true,
            'privacy_check'  => false
        );
        $_rt  = jrCore_db_search_items('jrProfile', $_src);
        if ($_rt && is_array($_rt) && isset($_rt['_items']) && count($_rt['_items']) > 0) {

            $out = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            foreach ($_rt['_items'] as $_profile) {

                $start = $_profile['_profile_id'];

                // Check for PRIVATE profiles
                // 0 = Private
                // 1 = Global
                // 2 = Shared
                // 3 = Shared but Visible in Search
                $prv = $_profile['profile_private'];
                $qid = (int) $_profile['profile_quota_id'];
                if (isset($_qt[$qid]) && isset($_qt[$qid]['privacy_changes']) && $_qt[$qid]['privacy_changes'] == 'off') {
                    $prv = (isset($_qt[$qid]['default_privacy'])) ? (int) $_qt[$qid]['default_privacy'] : '1';
                }
                if ($prv == 0 || $prv == 2) {
                    // profile is NOT global or searchable - do not add to Sitemap
                    continue;
                }

                $out .= "\n<url>\n<loc>{$_conf['jrCore_base_url']}/{$_profile['profile_url']}</loc>\n<priority>1.0</priority>\n<changefreq>{$updv}</changefreq>\n<lastmod>{$now}</lastmod>\n</url>";
            }
            $out .= "\n</urlset>";

            jrCore_write_to_file("{$cdr}/sitemap{$mapid}.xml", $out);
            jrCore_write_media_file(0, "sitemap{$mapid}.xml", "{$cdr}/sitemap{$mapid}.xml", 'public-read');
            unlink("{$cdr}/sitemap{$mapid}.xml");

            $mapid++;
            if ($update) {
                jrCore_form_modal_notice('update', "created XML Sitemap for " . count($_rt['_items']) . " profiles");
            }
        }
        else {
            $found = false;
        }
    }
    jrCore_set_setting_value('jrSitemap', 'file_count', ($mapid - 1));
    jrCore_logger('INF', 'create XML site map - completed');
    return true;
}
