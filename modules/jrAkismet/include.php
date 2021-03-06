<?php
/**
 * Jamroom Spam Blocker module
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

/**
 * meta
 */
function jrAkismet_meta()
{
    $_tmp = array(
        'name'        => 'Spam Blocker',
        'url'         => 'akismet',
        'version'     => '1.2.1',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Tools and Settings to help prevent spammers from being active on your site',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2958/spam-blocker',
        'category'    => 'tools',
        'license'     => 'mpl',
        'priority'    => 1,
        'requires'    => 'jrCore:5.1.0'
    );
    return $_tmp;
}

/**
 * init
 */
function jrAkismet_init()
{
    // Core module support
    $_options = array(
        'label' => 'Spam Check Entries',
        'help'  => 'If checked, profiles in this quota will have their textarea and editor text checked for spam.  If spam is detected, the item will marked as &quot;inactive&quot; pending review, and admin users will be notified.'
    );
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrAkismet', 'on', $_options);

    // We listen for new items being created as well as updated
    jrCore_register_event_listener('jrCore', 'db_create_item', 'jrAkismet_db_item_listener');
    jrCore_register_event_listener('jrCore', 'db_update_item', 'jrAkismet_db_item_listener');

    jrCore_register_event_trigger('jrAkismet', 'strip_html', 'Fired when preparing to strip HTML from an entry');

    // notifications
    $_tmp = array(
        'label' => 'Offsite URL detected',
        'help'  => 'If the Spam Blocker module detects an offsite URL in text by a new user, how do you want to be notified?'
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrAkismet', 'url_detected', $_tmp);

    return true;
}

//---------------------------------------------------
// EVENT LISTENERS
//---------------------------------------------------

/**
 * Make sure our signup form fields are always required
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrAkismet_db_item_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_mods;
    if (!jrUser_is_admin() && is_array($_data) && !empty($_args['module'])) {

        // See if this module is
        $mod = $_args['module'];
        $_rm = jrCore_get_registered_module_features('jrAkismet', 'spam_check');
        if (isset($_rm[$mod]) || is_file(APP_DIR . "/modules/{$mod}/templates/item_detail.tpl")) {

            //-------------------------
            // User Probation
            //-------------------------
            if (jrUser_is_logged_in() && isset($_data['_user_id']) && $_data['_user_id'] == $_user['_user_id'] && isset($_conf['jrAkismet_probation']) && $_conf['jrAkismet_probation'] > 0) {

                // Is this user in probation
                $old = (time() - (86400 * $_conf['jrAkismet_probation']));
                if ($_user['_created'] > $old) {

                    // check if the item is going into pending, if it is, do nothing.
                    $pfx = jrCore_db_get_prefix($mod);
                    if (isset($_data[$pfx . '_pending']) && $_data[$pfx . '_pending'] >= 1) {
                        return $_data;
                    }

                    $string = jrAkismet_get_text_from_entry($mod, $_data);

                    // Check for off site URLs
                    $stripped = false;
                    if (isset($_conf['jrAkismet_report_urls']) && $_conf['jrAkismet_report_urls'] !== 'ignore') {
                        $_urls = array();
                        if (strpos(' ' . $string, 'http')) {
                            preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', preg_replace('#<[^>]+>#', ' ', strip_tags(str_replace(array('<br', '<p>', '[', ']'), array(' ', ' ', ' [', '] '), $string))), $_urls);
                            $_urls = array_unique($_urls[0]);
                            if ($_urls && is_array($_urls) && count($_urls) > 0) {
                                $base_url = str_replace(array('www.', 'https://', 'http://'), '', $_conf['jrCore_base_url']);
                                foreach ($_urls as $url) {
                                    // Is this one off site?
                                    if (!strpos($url, $base_url)) {
                                        // This URL is OFF SITE
                                        switch ($_conf['jrAkismet_report_urls']) {

                                            case 'report':
                                                // Send email to admins
                                                $_user['offsite_url'] = $url;
                                                $_user['content_url'] = jrCore_is_profile_referrer();
                                                list($sub, $msg) = jrCore_parse_email_templates('jrAkismet', 'url_detected', $_user);
                                                $_us = jrUser_get_admin_user_ids();
                                                if ($_us && is_array($_us)) {
                                                    foreach ($_us as $uid) {
                                                        jrUser_notify($uid, 0, 'jrAkismet', 'url_detected', $sub, $msg);
                                                    }
                                                }
                                                $_data    = jrAkismet_strip_html_from_item($mod, $_data, $_urls);
                                                $stripped = true;
                                                break;

                                            case 'active':
                                                // We are going to set this user's account INACTIVE
                                                $_up = array(
                                                    'profile_active' => 0
                                                );
                                                $pid = jrUser_get_profile_home_key('_profile_id');
                                                jrCore_db_update_item('jrProfile', $pid, $_up);
                                                jrProfile_reset_cache($pid);

                                                // Send email to admins
                                                $_user['offsite_url'] = $url;
                                                $_user['content_url'] = jrCore_is_profile_referrer();
                                                list($sub, $msg) = jrCore_parse_email_templates('jrAkismet', 'url_detected', $_user);
                                                $_us = jrUser_get_admin_user_ids();
                                                if ($_us && is_array($_us)) {
                                                    foreach ($_us as $uid) {
                                                        jrUser_notify($uid, 0, 'jrAkismet', 'url_detected', $sub, $msg);
                                                    }
                                                }

                                                $_data    = jrAkismet_strip_html_from_item($mod, $_data, $_urls);
                                                $stripped = true;
                                                break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    // Strip all HTML
                    if (!$stripped && isset($_conf['jrAkismet_block_html']) && $_conf['jrAkismet_block_html'] == 'on') {
                        $_data = jrAkismet_strip_html_from_item($mod, $_data);
                    }
                }
            }

            //-------------------------
            // Akismet Spam Check
            //-------------------------
            if (jrAkismet_spam_check_api_is_configured()) {

                // A module either needs to have Pending support
                // OR have registered explicitly for Spam Checking
                if (jrUser_is_logged_in()) {
                    if (isset($_user['quota_jrAkismet_allowed']) && $_user['quota_jrAkismet_allowed'] == 'on') {

                        // This user is in a quota that is checking for Spam
                        $_pn = jrCore_get_registered_module_features('jrCore', 'pending_support');
                        if (($_pn && isset($_pn[$mod])) || isset($_rm[$mod])) {

                            // This module has pending support OR has registered for spam support - check
                            if (jrAkismet_is_spam($mod, $_data)) {

                                // We've got a spam entry
                                if (isset($_pn[$mod])) {
                                    // This module support PENDING - set pending flag
                                    $pfx                            = jrCore_db_get_prefix($mod);
                                    $_data["{$pfx}_pending"]        = '1';
                                    $_data["{$pfx}_pending_reason"] = 'Spam Blocker reports this entry is Spam';
                                }
                                else {
                                    // How are we handling this?
                                    foreach ($_rm[$mod] as $type => $action) {
                                        // The module can tell us what to do - $action can be one of:
                                        // - "reject" - outright reject the entry for spam
                                        // - "strip" - strip all HTML from the entry
                                        switch (strtolower($action)) {
                                            case 'reject':
                                                jrCore_logger('MIN', "Spam Blocker: rejected {$_mods[$mod]['module_name']} entry due to reported spam", $_data);
                                                $_ln = jrUser_load_lang_strings();
                                                jrCore_set_form_notice('error', $_ln['jrAkismet'][2]);
                                                jrCore_form_result();
                                                break;
                                            default:
                                                // By default strip HTML
                                                $_data = jrAkismet_strip_html_from_item($mod, $_data);
                                                jrCore_logger('MIN', "Spam Blocker: stripped all HTML from {$_mods[$mod]['module_name']} entry due to reported spam", $_data);
                                                break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                else {
                    // This user is NOT logged in - see if this module has registered for Spam Checking
                    if (isset($_rm[$mod])) {
                        foreach ($_rm[$mod] as $type => $action) {
                            // This module wants to be spam checked - See what TYPE of checking is being done:
                            // comment: A blog comment.
                            // forum-post: A top-level forum post.
                            // reply: A reply to a top-level forum post.
                            // blog-post: A blog post.
                            // contact-form: A contact form or feedback form submission.
                            // signup: A new user account.
                            // message: A message sent between just a few users.
                            if (jrAkismet_is_spam($mod, $_data, $type)) {

                                // The module can tell us what to do - $action can be one of:
                                // - "reject" - outright reject the entry for spam
                                // - "strip" - strip all HTML from the entry
                                switch (strtolower($action)) {
                                    case 'reject':
                                        jrCore_logger('MIN', "Spam Blocker: rejected {$_mods[$mod]['module_name']} entry due to reported spam", $_data);
                                        $_ln = jrUser_load_lang_strings();
                                        jrCore_set_form_notice('error', $_ln['jrAkismet'][2]);
                                        jrCore_form_result();
                                        break;
                                    default:
                                        // By default strip HTML
                                        $_data = jrAkismet_strip_html_from_item($mod, $_data);
                                        jrCore_logger('MIN', "Spam Blocker: stripped all HTML from {$_mods[$mod]['module_name']} entry due to reported spam", $_data);
                                        break;
                                }

                            }
                        }
                    }
                    else {
                        // Logged out - check for spam
                        if (jrAkismet_is_spam($mod, $_data)) {
                            $_data = jrAkismet_strip_html_from_item($mod, $_data);
                            jrCore_logger('MIN', "Spam Blocker: stripped all HTML from {$_mods[$mod]['module_name']} entry due to reported spam", $_data);
                        }
                    }
                }
            }
        }
    }
    return $_data;
}

/**
 * Return TRUE if Akismet API key is configured
 * @return bool
 */
function jrAkismet_spam_check_api_is_configured()
{
    global $_conf;
    if (isset($_conf['jrAkismet_enabled']) && $_conf['jrAkismet_enabled'] == 'on' && !empty($_conf['jrAkismet_api_key'])) {
        return true;
    }
    return false;
}

/**
 * Strip all HTML from an item
 * @param $module string Module
 * @param $_data array Item
 * @param $_urls array URLs to strip
 * @return mixed
 */
function jrAkismet_strip_html_from_item($module, $_data, $_urls = null)
{
    global $_mods;
    $_tm = jrCore_trigger_event('jrAkismet', 'strip_html', $_data, array('module' => $module));
    // Did any listener tell us NOT to work?
    if ($_tm && is_array($_tm)) {
        $_ln = jrUser_load_lang_strings();
        // Construct our text
        $pfx = jrCore_db_get_prefix($module);
        $fix = false;
        $_fx = array();
        foreach ($_data as $k => $v) {
            if (strpos($k, $pfx) === 0 && !strpos($k, '_image') && !is_numeric($v) && strpos(' ' . $v, '<')) {
                // Looks like we MIGHT have HTML in this item - let's remove the HTML
                $temp = jrCore_strip_html($v);
                if (is_array($_urls)) {
                    $temp = str_replace($_urls, $_ln['jrAkismet'][1], $temp);
                }
                if ($temp != $v) {
                    // We were changed
                    $_fx[$k]   = $v;
                    $_data[$k] = $temp;
                    $fix       = true;
                }
            }
        }
        if ($fix) {
            jrCore_logger('MIN', "Spam Blocker: stripped all HTML from suspected " . $_mods[$module]['module_name'] . " entry", array('_data' => $_data, '_orig' => $_fx, '_urls' => $_urls));
        }
    }
    return $_data;
}

/**
 * Given an item, get it's text strings into a single string
 * @param $module string Module
 * @param $_data array Item
 * @return string
 */
function jrAkismet_get_text_from_entry($module, $_data)
{
    // Construct our text
    $pfx = jrCore_db_get_prefix($module);
    $txt = '';
    foreach ($_data as $k => $v) {
        if (strpos($k, $pfx) === 0 && !strpos($k, 'image') && !is_numeric($v) && strlen($v) > 11 && !strpos($k, '_url')) {
            $txt .= $v . "\n";
        }
    }
    return $txt;
}

/**
 * Check a DataStore Item for spam entries
 * @see https://akismet.com/development/api/#comment-check
 * @param $module string Module
 * @param mixed $_data array DS Item array
 * @param string $ip_address IP Address item was added from
 * @param string $type one of the Akismet types for checking
 * @return bool False if item is NOT spam, TRUE if spam is detected
 */
function jrAkismet_is_spam($module, $_data, $ip_address = null, $type = 'comment')
{
    global $_conf;

    // comment: A blog comment.
    // forum-post: A top-level forum post.
    // reply: A reply to a top-level forum post.
    // blog-post: A blog post.
    // contact-form: A contact form or feedback form submission.
    // signup: A new user account.
    // message: A message sent between just a few users.

    $pfx = jrCore_db_get_prefix($module);
    if (!$pfx) {
        return false;
    }
    // Construct our text
    $text = jrAkismet_get_text_from_entry($module, $_data);
    if (strlen($text) > 0) {
        // Sign in
        $_ps = array(
            'key'  => $_conf['jrAkismet_api_key'],
            'blog' => $_conf['jrCore_base_url']
        );
        // https://akismet.com/development/api/#verify-key
        $res = jrCore_load_url('http://rest.akismet.com/1.1/verify-key', $_ps, 'POST');
        if ($res && $res == 'valid') {

            if (is_null($ip_address)) {
                $ip_address = jrCore_get_ip();
            }
            $_ps = array(
                'blog'            => $_conf['jrCore_base_url'],
                'user_ip'         => $ip_address,
                'user_agent'      => $_SERVER['HTTP_USER_AGENT'],
                'referrer'        => $_SERVER['HTTP_REFERER'],
                'comment_type'    => $type,
                'comment_content' => $text
            );
            // Additional info if we have it
            if (isset($_data['user_email']) && jrCore_checktype($_data['user_email'], 'email')) {
                $_ps['comment_author']       = $_data['user_name'];
                $_ps['comment_author_email'] = $_data['user_email'];
            }
            // https://akismet.com/development/api/#comment-check
            $res = jrCore_load_url("http://{$_conf['jrAkismet_api_key']}.rest.akismet.com/1.1/comment-check", $_ps, 'POST');
            if ($res && $res == 'true') {
                // SPAM!
                return true;
            }
        }
        else {
            jrCore_logger('CRI', "Spam Blocker: Invalid Akismet API Key - double check Akismet config");
        }
    }
    return false;
}

