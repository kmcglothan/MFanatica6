<?php
/**
 * Jamroom Group Mailer module
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

/**
 * meta
 */
function jrGroupMailer_meta()
{
    $_tmp = array(
        'name'        => 'Group Mailer',
        'url'         => 'groupmailer',
        'version'     => '1.1.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Send an email to all members in a Profile Group',
        'category'    => 'profiles',
        'requires'    => 'jrGroup',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrGroupMailer_init()
{
    // Core support
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrGroupMailer', 'on');

    // Register our custom JS
    jrCore_register_module_feature('jrCore', 'javascript', 'jrGroupMailer', 'jrGroupMailer.js');

    // Register ourselves with the Groups core
    jrCore_register_module_feature('jrGroup', 'group_support', 'jrGroupMailer', 'on');

    // Let users unsubscribe in their notifications
    $_tmp = array(
        'label'      => 1,  // 'Group email'
        'help'       => 4,  // 'Would you like to receive emails from group admins?'
        'email_only' => true,
        'html_email' => true
    );
    jrCore_register_module_feature('jrUser', 'notification', 'jrGroupMailer', 'email', $_tmp);

    // Our email Prep worker
    jrCore_register_queue_worker('jrGroupMailer', 'prep_email', 'jrGroupMailer_prep_email_worker', 1, 1, 82800);

    // Our email send worker
    jrCore_register_queue_worker('jrGroupMailer', 'send_email', 'jrGroupMailer_send_email_worker', 8, 1, 82800);

    // Our send group email button
    $_tmp = array(
        'title'  => 'compose group email button',
        'icon'   => 'pen',
        'active' => 'on'
    );
    jrCore_register_module_feature('jrCore', 'item_detail_button', 'jrGroupMailer', 'jrGroupMailer_compose_email_button', $_tmp);
    return true;
}

//---------------------------------------
// QUEUE WORKER
//---------------------------------------

/**
 * Prep an email for sending
 * @param $_queue array Queue entry
 * @return bool
 */
function jrGroupMailer_prep_email_worker($_queue)
{
    @ini_set('memory_limit', '512M');
    // Our prep email worker will gather all email addresses
    // for all selected options and create the necessary send_email
    // queue workers ensuring each group member email only receives one copy
    $_groups = explode(',', $_queue['groupmailer_group']);
    if ($_groups && is_array($_groups)) {

        $_em  = array();
        $size = 200;
        foreach ($_groups as $id) {

            // Is this a Group ID?
            if (jrCore_checktype($id, 'number_nz')) {

                // Send email to all group members in batches of $size
                $_gt = jrCore_db_get_item('jrGroup', $id);
                if ($_gt && isset($_gt['group_member']) && count($_gt['group_member']) > 0) {
                    // Get group members
                    $_gm = array();
                    foreach ($_gt['group_member'] as $gm) {
                        if ($gm['_user_id'] != $_queue['groupmailer_sender'] ) {
                            $_gm["{$gm['_user_id']}"] = $gm['_user_id'];
                        }
                    }
                    // did we find members in this group?
                    if ($_gm && is_array($_gm) && count($_gm) > 0) {
                        // Get user_id's associated with these group members ...
                        $_s = array(
                            'search'              => array(
                                "_user_id in " . implode(',', $_gm)
                            ),
                            'skip_triggers'       => true,
                            'return_item_id_only' => true,
                            'limit'               => 1000
                        );
                        $_us = jrCore_db_search_items('jrUser', $_s);
                        if ($_us && is_array($_us) && count($_us) > 0) {
                            $_rt = jrCore_db_get_multiple_items('jrUser', $_us, array('_user_id', 'user_email'));
                            foreach ($_rt as $_us) {
                                if (jrCore_checktype($_us['user_email'], 'email')) {
                                    $uid       = (int) $_us['_user_id'];
                                    $_em[$uid] = $_us['user_email'];
                                }
                            }
                        }
                    }
                }
            }
        }

        $total = count($_em);
        if ($total > 0) {
            $_em = array_chunk($_em, $size, true);
            foreach ($_em as $_chunk) {
                // Create our send queue
                $_queue['emails'] = $_chunk;
                jrCore_queue_create('jrGroupMailer', 'send_email', $_queue);
            }
            $_tmp = array(
                'groupmailer_recipients' => $total
            );
            jrCore_db_update_item('jrGroupMailer', $_queue['groupmailer_id'], $_tmp);
            jrCore_logger('INF', "successfully queued {$total} group member emails for delivery");
        }
    }
    return true;
}

/**
 * Send an email
 * @param $_queue array Queue entry
 * @return bool
 */
function jrGroupMailer_send_email_worker($_queue)
{
    if (isset($_queue['emails']) && is_array($_queue['emails'])) {
        $_us = array(
            'search'                       => array(
                'user_email in ' . implode(',', $_queue['emails'])
            ),
            'exclude_jrProfile_quota_keys' => true,
            'skip_triggers'                => true,
            'privacy_check'                => false,
            'ignore_pending'               => true,
            'limit'                        => count($_queue['emails'])
        );
        $_us = jrCore_db_search_items('jrUser', $_us);
        if ($_us && is_array($_us) && isset($_us['_items'])) {
            $_em = array();
            $_sp = array();
            foreach ($_us['_items'] as $_u) {
                $uid = (int) $_u['_user_id'];
                // Check for unsubscribed user
                if (isset($_queue['groupmailer_ignore_unsub']) && $_queue['groupmailer_ignore_unsub'] != 'on') {
                    if (isset($_u['user_unsubscribed']) && $_u['user_unsubscribed'] == 'on') {
                        $_sp[$uid] = $_u['user_email'];
                        continue;
                    }
                    // Make sure they have not disabled group emails in notifications
                    if (isset($_u['user_jrGroupMailer_email_notifications']) && $_u['user_jrGroupMailer_email_notifications'] == 'off') {
                        $_sp[$uid] = $_u['user_email'];
                        continue;
                    }
                }
                // Replacement variables on all user keys
                $_rep = array();
                foreach ($_u as $k => $v) {
                    $_rep['{$' . $k . '}'] = $v;
                }
                $msg      = jrCore_replace_emoji(str_replace(array_keys($_rep), $_rep, $_queue['groupmailer_message']));
                jrUser_notify($uid, 0, 'jrGroupMailer', 'email', $_queue['groupmailer_title'], $msg);
                $_em[$uid] = $_u['user_email'];
            }
            if (count($_sp) > 0) {
                // We had suppressed - update for email
                jrCore_db_increment_key('jrGroupMailer', $_queue['groupmailer_id'], 'groupmailer_suppressed', count($_sp));
            }
        }
    }
    return true;
}

//---------------------------------------
// FUNCTIONS
//---------------------------------------

/**
 * Get any saved templates
 * @return mixed
 */
function jrGroupMailer_get_templates()
{
    global $_user;
    $tbl = jrCore_db_table_name('jrGroupMailer', 'template');
    $req = "SELECT t_id, t_title FROM {$tbl} WHERE `t_user_id` = '{$_user['_user_id']}' ORDER BY t_time DESC";
    return jrCore_db_query($req, 't_id', false, 't_title');
}

/**
 * Get an individual template
 * @param $template_id int Template ID
 * @return mixed
 */
function jrGroupMailer_get_template($template_id)
{
    global $_user;
    $tid = (int) $template_id;
    $tbl = jrCore_db_table_name('jrGroupMailer', 'template');
    $req = "SELECT * FROM {$tbl} WHERE t_id = '{$tid}' AND `t_user_id` = '{$_user['_user_id']}'";
    return jrCore_db_query($req, 'SINGLE');
}

/**
 * Check if an existing group mailer template exists
 * @param $title string Title
 * @return bool
 */
function jrGroupMailer_template_exists($title)
{
    global $_user;
    $ttl = jrCore_db_escape($title);
    $tbl = jrCore_db_table_name('jrGroupMailer', 'template');
    $req = "SELECT * FROM {$tbl} WHERE t_title = '{$ttl}' AND `t_user_id` = '{$_user['_user_id']}'";
    $_ex = jrCore_db_query($req, 'SINGLE');
    if ($_ex && is_array($_ex)) {
        return true;
    }
    return false;
}

/**
 * Save a template
 * @param $title string Template title
 * @param $message string Message (body) of group mailer
 * @return bool|mixed
 */
function jrGroupMailer_save_template($title, $message)
{
    global $_user;
    $ttl = jrCore_db_escape($title);
    $msg = jrCore_db_escape($message);
    $tbl = jrCore_db_table_name('jrGroupMailer', 'template');
    $req = "INSERT IGNORE INTO {$tbl} (t_time, t_title, t_template, t_user_id) VALUES (UNIX_TIMESTAMP(), '{$ttl}', '{$msg}', '{$_user['_user_id']}')";
    $tid = jrCore_db_query($req, 'INSERT_ID');
    if ($tid && $tid > 0) {
        return $tid;
    }
    return false;
}

/**
 * Delete an existing template
 * @param $template_id int Template ID
 * @return bool
 */
function jrGroupMailer_delete_template($template_id)
{
    global $_user;
    $tid = (int) $template_id;
    $tbl = jrCore_db_table_name('jrGroupMailer', 'template');
    $req = "DELETE FROM {$tbl} WHERE t_id = '{$tid}' AND `t_user_id` = '{$_user['_user_id']}'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * Return "Compose email" button for group list and detail pages
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return string
 */
function jrGroupMailer_compose_email_button($module, $_item, $_args, $smarty, $test_only = false)
{
    global $_conf, $_user;

    if ($test_only) {
        return true;
    }
    if ($module == 'jrGroup' && isset($_item['quota_jrGroupMailer_allowed']) && $_item['quota_jrGroupMailer_allowed'] == 'on' && ($_item['_user_id'] == $_user['_user_id'] || jrUser_is_admin())) {
        $murl = jrCore_get_module_url('jrGroupMailer');

        $_rt = array(
            'url'  => "{$_conf['jrCore_base_url']}/{$murl}/compose/gid={$_item['_item_id']}",
            'icon' => 'pen',
            'alt'  => 'group email'
        );
        return $_rt;
    }
    return false;
}
