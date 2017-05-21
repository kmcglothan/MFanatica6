<?php
/**
 * Jamroom Beta Launch Page module
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
 * jrLaunch_meta
 */
function jrLaunch_meta()
{
    $_tmp = array(
        'name'        => 'Beta Launch Page',
        'url'         => 'launch',
        'version'     => '1.2.1',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Create a Beta Signup page for users to signup pre-launch',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2953/beta-launch-page',
        'category'    => 'site',
        'requires'    => 'jrCore:6.0.0',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * jrLaunch_init
 */
function jrLaunch_init()
{
    // jrLaunch tool view
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrLaunch', 'send_emails', array('Send Emails', 'Enter a message and send it to all the collected email addresses'));

    // Listen for a call to our index template
    jrCore_register_event_listener('jrUser', 'session_started', 'jrLaunch_session_started_listener');

    // Register JS
    jrCore_register_module_feature('jrCore', 'javascript', 'jrLaunch', 'jrLaunch.js');
    return true;
}

//---------------------------------------
// EVENT LISTENERS
//---------------------------------------

/**
 * Display Beta Launch page to non-logged in users
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrLaunch_session_started_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (isset($_conf['jrLaunch_launch_active']) && $_conf['jrLaunch_launch_active'] == 'on' && !jrUser_is_logged_in()) {
        if (isset($_post['option'])) {
            // See if we have requested an allowed module/view
            switch ($_post['option']) {
                case 'login':
                case 'login_save':
                case 'logout':
                case 'forgot':
                case 'forgot_save':
                case 'new_password':
                case 'new_password_save':
                case 'form_validate':
                    return $_data;
                    break;
                default:

            }
        }
        $murl = jrCore_get_module_url('jrLaunch');
        if (isset($_post['module_url']) && isset($_post['option']) && "{$_post['module_url']}/{$_post['option']}" == "{$murl}/signup_save") {
            // We are saving a response
            require_once APP_DIR . '/modules/jrLaunch/index.php';
            view_jrLaunch_signup_save($_post, $_user, $_conf);
            exit;
        }
        else {
            jrCore_page_title($_conf['jrLaunch_launch_title']);
            $out = jrCore_parse_template('meta.tpl', $_data);
            $out .= jrCore_parse_template('index.tpl', $_data, 'jrLaunch');
            header('Connection: close');
            header("Content-Type: text/html; charset=utf-8");
            header('Content-Length: ' . strlen($out));
            echo $out;
            exit;
        }
    }
    return $_data;
}
