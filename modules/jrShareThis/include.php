<?php
/**
 * Jamroom ShareThis module
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

/**
 * meta
 */
function jrShareThis_meta()
{
    $_tmp = array(
        'name'        => 'ShareThis',
        'url'         => 'sharethis',
        'version'     => '1.4.2',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Adds ShareThis social media sharing to item pages (free account required)',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2598/sharethis',
        'category'    => 'item features',
        'requires'    => 'jrCore:6.0.5,jrMeta',
        'priority'    => 250,
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrShareThis_init()
{
    // We offer a module detail feature for Item Tags
    $_tmp = array(
        'function' => 'jrShareThis_share_feature',
        'label'    => 'ShareThis',
        'help'     => 'Adds a &quot;ShareThis&quot; section to Item Detail pages'
    );
    jrCore_register_module_feature('jrCore', 'item_detail_feature', 'jrShareThis', 'sharethis', $_tmp);

    // Allow enable/disable per quota
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrShareThis', 'on');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrShareThis', true);
    return true;
}

//------------------------
// FUNCTIONS
//------------------------

/**
 * Strip all HTML from a string
 * @deprecated - use jrMeta_strip_all_html()
 * @param $str string to strip HTML from
 * @return string
 */
function jrShareThis_strip_all_html($str)
{
    $str = smarty_modifier_jrCore_format_string($str, 'allow_all_formatters');
    return preg_replace('!\s+!', ' ', str_replace(array("\r", "\n"), ' ', jrCore_strip_html($str)));
}

/**
 * Add a ShareThis section to item detail pages
 * @param string $module Module item belongs to
 * @param array $_item Item info (from DS)
 * @param array $params Smarty function parameters
 * @param array $smarty current Smarty object
 * @return string
 */
function jrShareThis_share_feature($module, $_item, $params, $smarty)
{
    global $_conf, $_post;
    // Must have a PUB key
    if (!isset($_conf['jrShareThis_pub_key']) || strlen($_conf['jrShareThis_pub_key']) === 0) {
        return '';
    }
    // See if we are enabled in this quota
    if (isset($_item['quota_jrShareThis_show_detail']) && $_item['quota_jrShareThis_show_detail'] == 'off') {
        return '';
    }
    if (isset($_conf['jrShareThis_chicklets']) && strlen($_conf['jrShareThis_chicklets']) > 1) {
        $_tm                = array();
        $_cn                = jrShareThis_get_chicklets();
        $_post['chicklets'] = array();
        foreach (explode(',', $_conf['jrShareThis_chicklets']) as $chk) {
            $_tm[]                    = '"' . trim($chk) . '"';
            $_post['chicklets'][$chk] = $_cn[$chk];
        }
        $_post['chicklets_cs'] = implode(',', $_tm);
    }
    $_post['copy_share'] = ', doNotHash: true, doNotCopy: true';
    if (isset($_conf['jrShareThis_copy_share']) && $_conf['jrShareThis_copy_share'] == 'on') {
        $_post['copy_share'] = ', doNotHash: false, doNotCopy: false';
    }

    $out = '';
    if (!jrCore_get_flag('jrsharethis_added_js')) {
        if (strpos($_conf['jrCore_base_url'], 'https') === 0) {
            // Cannot use "bar" type ShareThis on SSL per:
            // http://support.sharethis.com/customer/portal/questions/888763#sthash.37lo6PRM.dpbs
            $out .= '<script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script>';
        }
        else {
            $out .= '<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
                <script type="text/javascript" src="http://s.sharethis.com/loader.js"></script>';
        }
        jrCore_set_flag('jrsharethis_added_js', 1);
    }

    if (isset($params['template']) && strlen($params['template']) > 0) {
        $out .= jrCore_parse_template($params['template'], $_post, $_conf['jrCore_active_skin']);
    }
    else {
        $out .= jrCore_parse_template("{$_conf['jrShareThis_style']}.tpl", $_post, 'jrShareThis');
    }
    if (!empty($params['assign'])) {
        /** @noinspection PhpUndefinedMethodInspection */
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * ShareThis smarty template function
 * @param array $params Smarty function parameters
 * @param array $smarty current Smarty object
 * @return string
 */
function smarty_function_jrShareThis($params, $smarty)
{
    if (!isset($params['module'])) {
        return jrCore_smarty_missing_error('module');
    }
    $_it = array();
    if (isset($params['item_id'])) {
        if (!jrCore_checktype($params['item_id'], 'number_nz')) {
            return jrCore_smarty_invalid_error('item_id');
        }
        $_it = jrCore_db_get_item($params['module'], $params['item_id']);
        if (!$_it) {
            return '';
        }
    }
    $out = jrShareThis_share_feature($params['module'], $_it, $params, $smarty);
    if (!empty($params['assign'])) {
        /** @noinspection PhpUndefinedMethodInspection */
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * Get list of available chicklets
 * @return array
 */
function jrShareThis_get_chicklets()
{
    $_ch = array(
        'digg'        => 'Digg',
        'email'       => 'E-mail',
        'evernote'    => 'Evernote',
        'facebook'    => 'Facebook',
        'googleplus'  => 'Google+',
        'hyves'       => 'Hyves',
        'linkedin'    => 'LinkedIn',
        'livejournal' => 'Live Journal',
        'myspace'     => 'MySpace',
        'pinterest'   => 'Pinterest',
        'reddit'      => 'Reddit',
        'tumblr'      => 'Tumblr',
        'twitter'     => 'Twitter',
        'wordpress'   => 'Wordpress',
        'sharethis'   => 'ShareThis',
        'whatsapp'    => 'WhatsApp'

    );
    natcasesort($_ch);
    return $_ch;
}
