<?php
/**
 * Jamroom RSS Feed and Reader module
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

/**
 * meta
 */
function jrFeed_meta()
{
    $_tmp = array(
        'name'        => 'RSS Feed and Reader',
        'url'         => 'feed',
        'version'     => '1.2.4',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Import an external RSS Feed into a template and create RSS feeds of site content.',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/277/rss-feed-and-reader',
        'license'     => 'mpl',
        'category'    => 'listing'
    );
    return $_tmp;
}

/**
 * init
 */
function jrFeed_init()
{
    // Register jrFeed tools
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrFeed', 'create', array('Create / Update', 'Pull an RSS feed into this site / Update an existing feed'));

    // We provide an event listener for modules to generate an RSS feed
    jrCore_register_event_trigger('jrFeed', 'create_rss_feed', 'Fired in feeder view to get RSS data');

    // Site Builder widget
    jrCore_register_module_feature('jrSiteBuilder', 'widget', 'jrFeed', 'widget_rss', 'RSS Feed');

    return true;
}

//------------------------------------
// WIDGETS
//------------------------------------

/**
 * CONFIG Widget
 * @param $_post array Post info
 * @param $_user array User array
 * @param $_conf array Global Config
 * @param $_wg array Widget info
 * @return bool
 */
function jrFeed_widget_rss_config($_post, $_user, $_conf, $_wg)
{
    $_opt = array();
    $murl = jrCore_get_module_url('jrFeed');
    // Get available forms
    $_sc = array(
        "limit"    => 100,
        "order_by" => array("feed_name" => 'asc')
    );
    $_rt = jrCore_db_search_items('jrFeed', $_sc);
    if ($_rt && is_array($_rt) && is_array($_rt['_items'])) {
        foreach ($_rt['_items'] as $k => $_f) {
            $_opt[$_f['feed_name']] = $_f['feed_name'];
        }
    }

    $_tmp = array(
        'name'     => 'name',
        'label'    => 'RSS Feed',
        'help'     => 'Select the RSS Feed to embed in the page<br><br>New RSS Feeds can be created in the <a href="' . $_conf['jrCore_base_url'] . '/' . $murl . '/create"><u>Feed Reader Module</a>',
        'options'  => $_opt,
        'default'  => '',
        'type'     => 'select',
        'validate' => 'printable',
        'size'     => 8
    );
    jrCore_form_field_create($_tmp);
    return true;
}

/**
 * Save Widget
 * @param $_post array Post info
 * @return array
 */
function jrFeed_widget_rss_config_save($_post)
{
    return array('name' => $_post['name']);
}

/**
 * DISPLAY Widget
 * @param $_widget array Widget info
 * @return string
 */
function jrFeed_widget_rss_display($_widget)
{
    return jrCore_parse_template('widget_rss_feed.tpl', $_widget, 'jrFeed');
}

/**
 * jrFeed_list
 * Smarty function show specified feed
 * @param $params array parameters for function
 * @param $smarty object Smarty object
 * @return string
 */
function smarty_function_jrFeed_list($params, $smarty)
{
    global $_conf;
    if (!jrCore_module_is_active('jrFeed')) {
        return '';
    }
    $ckey = json_encode($params);
    if (!$out = jrCore_is_cached('jrFeed', $ckey)) {
        // Get feed item
        if (isset($params['name']) && $params['name'] != '') {
            $params['name'] = trim($params['name']);
            $_rt            = jrCore_db_get_item_by_key('jrFeed', 'feed_name', $params['name']);
            if (!$_rt || !is_array($_rt)) {
                return '';
            }
            $url = $_rt['feed_url'];
        }
        else {
            return 'jrFeed - feed name required';
        }
        // Check the incoming parameters
        if (!isset($params['type']) || $params['type'] == '') {
            $params['type'] = 'rss';
        }
        if (!jrCore_checktype($params['limit'], 'number_nz')) {
            $params['limit'] = 5;
        }
        if (isset($params['template']) && $params['template'] != '') {
            $params['tpl_dir'] = $_conf['jrCore_active_skin'];
        }
        else {
            $params['template'] = "{$params['type']}_list.tpl";
            $params['tpl_dir']  = 'jrFeed';
        }
        $_tmp = array();
        foreach ($params as $k => $v) {
            $_tmp['jrFeed']['info'][$k] = $v;
        }
        // Get the feed
        $_x  = array();
        $atm = false;
        $xml = jrCore_load_url($url);
        if ($xml && strpos($xml, '/Atom') && !stripos($xml, '<channel>')) {
            $atm = true;
        }
        $xml = @simplexml_load_string($xml, null, LIBXML_NOCDATA);
        if (!$xml) {
            // Unable to parse XML for feed
            return '';
        }
        if ($atm) {
            // This is an ATOM feed
            /** @noinspection PhpUndefinedFieldInspection */
            $_x['title'] = (string) $xml->title;
            $i           = 0;
            /** @noinspection PhpUndefinedFieldInspection */
            foreach ($xml->entry as $item) {
                if ($i == $params['limit']) {
                    break;
                }
                $item                          = json_decode(json_encode($item), true);
                $_x['item'][$i]['title']       = $item['title'];
                $_x['item'][$i]['link']        = $item['link']['@attributes']['href'];
                $_x['item'][$i]['pubDate']     = $item['updated'];
                $_x['item'][$i]['description'] = $item['content'];
                if (isset($item['id'])) {
                    $_x['item'][$i]['guid'] = $item['id'];
                }
                $i++;
            }
        }
        else {
            // This is an RSS Feed
            /** @noinspection PhpUndefinedFieldInspection */
            $_x['title'] = (string) $xml->channel->title;
            /** @noinspection PhpUndefinedFieldInspection */
            $_x['description'] = (string) $xml->channel->description;
            $i                 = 0;
            /** @noinspection PhpUndefinedFieldInspection */
            foreach ($xml->channel->item as $item) {
                if ($i == $params['limit']) {
                    break;
                }
                $_x['item'][$i]['title']       = (string) $item->title;
                $_x['item'][$i]['link']        = (string) $item->link;
                $_x['item'][$i]['pubDate']     = strtotime((string) $item->pubDate);
                $_x['item'][$i]['description'] = (string) $item->description;
                $i++;
            }
        }
        $_tmp['jrFeed']['feed'] = $_x;

        // Call the appropriate template and return
        $out = jrCore_parse_template($params['template'], $_tmp, $params['tpl_dir']);

        // add in caching
        jrCore_add_to_cache('jrFeed', $ckey, $out);
    }


    if (isset($params['assign']) && $params['assign'] != '') {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}
