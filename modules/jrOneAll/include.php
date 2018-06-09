<?php
/**
 * Jamroom OneAll Social module
 *
 * copyright 2018 The Jamroom Network
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
function jrOneAll_meta()
{
    $_tmp = array(
        'name'        => 'OneAll Social',
        'url'         => 'oneall',
        'version'     => '1.6.5',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Users can signup, login and share to configured Social Networks',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/284/oneall-social',
        'category'    => 'users',
        'requires'    => 'jrUser:2.2.10,jrAction:2.0.11',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrOneAll_init()
{
    global $_conf;

    // JS and CSS
    jrCore_register_module_feature('jrCore', 'javascript', 'jrOneAll', 'jrOneAll.js');
    jrCore_register_module_feature('jrCore', 'css', 'jrOneAll', 'jrOneAll.css');

    // Setup our "Networks" tab for the User Account section
    if (isset($_conf['jrOneAll_public_key']) && strlen($_conf['jrOneAll_public_key']) > 0) {
        jrCore_register_module_feature('jrUser', 'account_tab', 'jrOneAll', 'networks', 5);
    }

    // We provide a share-to-networks trigger
    jrCore_register_event_trigger('jrOneAll', 'network_share_text', 'Get shared text for module action');

    // Listen for Actions to share
    jrCore_register_event_listener('jrAction', 'create', 'jrOneAll_share_action_listener');

    // Add in our social connections to signup/login
    jrCore_register_event_listener('jrCore', 'form_display', 'jrOneAll_form_display_listener');
    jrCore_register_event_listener('jrCore', 'parsed_template', 'jrOneAll_parsed_template_listener');

    // Cleanup connections when users are deleted
    jrCore_register_event_listener('jrCore', 'db_delete_item', 'jrOneAll_db_delete_item_listener');

    // Our social network sharing is handled by a queue
    jrCore_register_queue_worker('jrOneAll', 'share_action', 'jrOneAll_share_action_worker', 0, 1);

    // We provide a tool for exploring social connections
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrOneAll', 'connections', array('Social Connections', 'Browse Existing Social Connections created by your Users'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrOneAll', 'log_browser', array('API Log Browser', 'Browse the API communication log'));
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrOneAll', 'system_feed', array('System Feed', 'Send all system actions to a linked social account'));

    // event listeners
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrOneAll_verify_module_listener');
    jrCore_register_event_listener('jrCore', 'hourly_maintenance', 'jrOneAll_hourly_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrOneAll_daily_maintenance_listener');

    // Add / remove networks tab based on Quota options
    jrCore_register_event_listener('jrUser', 'account_tabs', 'jrOneAll_account_tabs_listener');
    jrCore_register_event_listener('jrUser', 'site_privacy_check', 'jrOneAll_site_privacy_check_listener');

    // System reset listener
    jrCore_register_event_listener('jrDeveloper', 'reset_system', 'jrOneAll_reset_system_listener');

    // Core support
    $_tmp = array(
        'label' => 'Enable Networks',
        'help'  => 'If checked, User Accounts associated with Profiles in this quota will see a &quot;Networks&quot; tab in their Account Settings section where they can link their Activity Stream to configured social networks.'
    );
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrOneAll', 'on', $_tmp);

    // Queue Worker
    jrCore_register_queue_worker('jrOneAll', 'verify_connections', 'jrOneAll_verify_connections_worker', 0, 1, 3600, LOW_PRIORITY_QUEUE);

    return true;
}

//----------------------
// QUEUE WORKER
//----------------------

/**
 * Verify existing OneAll connections
 * @param $_queue
 * @return bool
 */
function jrOneAll_verify_connections_worker($_queue)
{
    // Verify our user connections are still valid
    $old = (30 * 86400);
    $tbl = jrCore_db_table_name('jrOneAll', 'link');
    $req = "SELECT `user_id`, `provider`, `token` FROM {$tbl} WHERE (`data` LIKE '%user_jrOneAll%' OR `checked` < (UNIX_TIMESTAMP() - {$old})) ORDER BY `updated` ASC LIMIT 100";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        $tot = 0;
        $_up = array();
        $_dl = array();
        $_un = array();
        foreach ($_rt as $_us) {
            // Get latest data for this connection
            $_data = jrOneAll_api_call("identities/{$_us['token']}.json");
            if ($_data && is_array($_data)) {
                $req = false;
                switch ($_data['response']['request']['status']['code']) {
                    case '404':
                        // This identity is no longer valid
                        $req   = "DELETE FROM {$tbl} WHERE `user_id` = '{$_us['user_id']}' AND `provider` = '{$_us['provider']}' LIMIT 1";
                        $_dl[] = $_us;
                        break;

                    case '200':
                        // We're good - update data
                        $req   = "UPDATE {$tbl} SET `checked` = UNIX_TIMESTAMP(), `data` = '" . jrCore_db_escape(json_encode($_data)) . "' WHERE `user_id` = '{$_us['user_id']}' AND `provider` = '{$_us['provider']}' LIMIT 1";
                        $_up[] = $_us;
                        break;

                    default:
                        $_un[] = $_data;
                        break;
                }
                if ($req) {
                    jrCore_db_query($req);
                }
                $tot++;
            }
            sleep(1);
        }
        if ($tot > 0) {
            $_db = array(
                'updated' => $_up,
                'deleted' => $_dl,
                'unknown' => $_un
            );
            jrCore_logger('INF', "successfully validated " . jrCore_number_format($tot) . " OneAll user connections", $_db);
        }
    }
    return true;
}

//----------------------
// EVENT LISTENERS
//----------------------

/**
 * System Reset listener
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrOneAll_reset_system_listener($_data, $_user, $_conf, $_args, $event)
{
    $tbl = jrCore_db_table_name('jrOneAll', 'link');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");
    jrCore_db_query("OPTIMIZE TABLE {$tbl}");
    return $_data;
}

/**
 * Cleanup any bad linked user data
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrOneAll_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    jrCore_queue_create('jrOneAll', 'verify_connections', array('time' => time()), 30, null, 1);
    return $_data;
}

/**
 * Verify Social Connections
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrOneAll_hourly_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    jrCore_queue_create('jrOneAll', 'verify_connections', array('time' => time()), 30, null, 1);
    return $_data;
}

/**
 * Update Provider List
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrOneAll_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    jrOneAll_update_provider_list();
    return $_data;
}

/**
 * Add "Networks" tab to User Settings
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrOneAll_account_tabs_listener($_data, $_user, $_conf, $_args, $event)
{
    if (!isset($_user['quota_jrOneAll_allowed']) || $_user['quota_jrOneAll_allowed'] != 'on') {
        // Sharing is not enabled in this Quota
        unset($_data['jrOneAll/networks']);
    }
    elseif ($_args['pid'] != jrUser_get_profile_home_key('_profile_id')) {
        // We only show OneAll networks to the actual User
        unset($_data['jrOneAll/networks']);
    }
    return $_data;
}

/**
 * Site Privacy check
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrOneAll_site_privacy_check_listener($_data, $_user, $_conf, $_args, $event)
{
    // If the User module has our site in a "private" mode, we need to
    // allow the OneAll Signup links to go through so a user can signup
    if (isset($_conf['jrUser_signup_on']) && $_conf['jrUser_signup_on'] == 'on') {
        switch ($_args['option']) {
            case 'callback':
            case 'link_callback':
                $_data['allow_private_site_view'] = true;
                break;
        }
    }
    return $_data;
}

/**
 * Delete OneAll links when user account is deleted
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrOneAll_db_delete_item_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['module']) && $_args['module'] == 'jrUser') {
        // Delete this user's links
        $tbl = jrCore_db_table_name('jrOneAll', 'link');
        $req = "DELETE FROM {$tbl} WHERE user_id = '{$_args['_item_id']}'";
        jrCore_db_query($req);
    }
    return $_data;
}

/**
 * Share an Action with linked social networks
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrOneAll_share_action_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;

    jrOneAll_sitewide_feed($_data, $_user, $_conf, $_args, $event);

    // Make sure this user has access to OneAll
    if (isset($_data['action_data'])) {
        if (!isset($_data['action_data']['quota_jrOneAll_allowed']) || $_data['action_data']['quota_jrOneAll_allowed'] != 'on') {
            return $_data;
        }
    }
    // See if they are purposefully disabling this post
    if (isset($_post['oneall_share_active']) && $_post['oneall_share_active'] == 'off') {
        return $_data;
    }
    // Have we allowed any sharing networks?
    if (isset($_conf['jrOneAll_sharing_networks']) && strlen($_conf['jrOneAll_sharing_networks']) < 3) {
        return $_data;
    }

    // the sitewide feed

    // See if this user has linked any social networks for sharing
    $_rt = jrOneAll_get_shared_networks($_args['_user_id']);
    if (!$_rt || !is_array($_rt)) {
        return $_data;
    }
    if (!isset($_post['action_text'])) {
        // We are posting from an item create/update form
        // [jroneall_share_to_networks_facebook] => on
        // [jroneall_share_to_networks_twitter] => on
        foreach ($_rt as $provider => $token) {
            if (!isset($_post["jroneall_share_to_networks_{$provider}"]) || $_post["jroneall_share_to_networks_{$provider}"] != 'on') {
                unset($_rt[$provider]);
            }
        }

        // If the Meta Tag Manager module is installed and we are on SSL, use the card
        if (jrCore_module_is_active('jrMeta') && jrCore_get_server_protocol() === 'https') {
            if ($pfx = jrCore_db_get_prefix($_data['action_module'])) {
                if (isset($_data['action_data']["{$pfx}_title_url"])) {
                    $module_url           = jrCore_get_module_url($_data['action_module']);
                    $_post['action_text'] = "{$_conf['jrCore_base_url']}/{$_data['action_data']['profile_url']}/{$module_url}/{$_data['action_item_id']}/" . $_data['action_data']["{$pfx}_title_url"];
                }
            }
        }

    }

    if (count($_rt) > 0) {
        // Make sure any left over are allowed by our Global Config
        foreach ($_rt as $provider => $token) {
            // Is it allowed in config?
            if (!strpos(' ' . $_conf['jrOneAll_sharing_networks'], $provider)) {
                unset($_rt[$provider]);
            }
        }
        if (count($_rt) > 0) {
            // Create our new queue entry for this share
            $_queue = array(
                'providers'     => implode(',', array_keys($_rt)),
                'user_token'    => reset($_rt),
                'user_id'       => $_args['_user_id'],
                'item_id'       => $_args['_item_id'],
                'action_module' => $_data['action_module']
            );
            // See if we have "action text" (i.e. a tweet)
            if (isset($_post['action_text'])) {
                $_queue['action_text'] = $_post['action_text'];
            }
            if (isset($_post['action_data'])) {
                $_queue['action_data'] = $_post['action_data'];
            }
            jrCore_queue_create('jrOneAll', 'share_action', $_queue);
        }
    }
    return $_data;
}

/**
 * Add Social Network Icons to user login/signup page and share options to forms
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrOneAll_form_display_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    // We watch for 2 views: signup and login
    if (!isset($_data['form_view'])) {
        return $_data;
    }
    // Make sure we are configured
    if (!isset($_conf['jrOneAll_public_key']) || strlen($_conf['jrOneAll_public_key']) === 0) {
        return $_data;
    }
    if (!isset($_conf['jrOneAll_private_key']) || strlen($_conf['jrOneAll_private_key']) === 0) {
        return $_data;
    }
    if (!isset($_conf['jrOneAll_domain']) || strlen($_conf['jrOneAll_domain']) === 0) {
        return $_data;
    }
    switch ($_data['form_view']) {

        // On login/signup we show networks setup by the system
        case 'jrUser/login':

            // Are we allowing regular user login?
            if (!jrUser_is_admin() && isset($_conf['jrOneAll_require_social']) && $_conf['jrOneAll_require_social'] == 'on' && (!isset($_post['_1']) || $_post['_1'] != 'widget')) {
                if (!isset($_post['admin']) || $_post['admin'] != $_conf['jrOneAll_bypass_key']) {
                    jrCore_location("{$_conf['jrCore_base_url']}/" . jrCore_get_module_url('jrOneAll') . "/login");
                }
            }
            jrOneAll_add_social_login_field($_data['form_view']);
            return $_data;
            break;

        case 'jrUser/signup':
            // Are we allowing regular user signups?
            if (!jrUser_is_admin() && isset($_conf['jrOneAll_require_social']) && $_conf['jrOneAll_require_social'] == 'on' && (!isset($_post['_1']) || $_post['_1'] != 'widget')) {
                jrCore_location("{$_conf['jrCore_base_url']}/" . jrCore_get_module_url('jrOneAll') . "/signup");
            }
            jrOneAll_add_social_login_field($_data['form_view']);
            return $_data;
            break;

        case 'jrOneAll/login':
        case 'jrOneAll/signup':

            // Make sure signups are enabled
            if (($_data['form_view'] == 'jrUser/signup' || $_data['form_view'] == 'jrOneAll/signup') && isset($_conf['jrUser_signup_on']) && $_conf['jrUser_signup_on'] == 'off') {
                // Signups are NOT enabled
                return $_data;
            }

            // Add our social login
            jrOneAll_add_social_login_field($_data['form_view']);
            break;

        default:

            // See if this view has been registered to include sharing
            if (jrUser_is_logged_in() && isset($_conf['jrOneAll_sharing_networks']) && strlen($_conf['jrOneAll_sharing_networks']) > 3) {
                if (isset($_user["quota_jrOneAll_allowed"]) && $_user["quota_jrOneAll_allowed"] == 'on') {
                    list($mod, $view) = explode('/', $_data['form_view']);
                    if (!strpos(' ' . $view, 'search')) {
                        $_pn = jrCore_get_registered_module_features('jrCore', 'action_support');
                        if ($_pn && isset($_pn[$mod][$view]) && jrCore_is_profile_referrer(false)) {

                            // This module has asked for action support - now see if it handles One All
                            $_om = jrCore_get_event_listeners('jrOneAll', 'network_share_text');
                            if ($_om && strpos(' ' . implode(',', $_om), "{$mod}_")) {

                                // We are setup for OneAll
                                $allowed = false;
                                if (is_array($_pn[$mod][$view]) && isset($_pn[$mod][$view]['allowed_off_profile']) && $_pn[$mod][$view]['allowed_off_profile'] == true) {
                                    $allowed = true;
                                }
                                else {
                                    // Is this user creating an item on a profile they OWN?
                                    $_up = jrProfile_get_user_linked_profiles($_user['_user_id']);
                                    if ($_up && is_array($_up) && isset($_up["{$_user['user_active_profile_id']}"])) {
                                        $allowed = true;
                                    }
                                }
                                if ($allowed) {
                                    // See if this user has any shared networks
                                    $_rt = jrOneAll_get_shared_networks($_user['_user_id']);
                                    if ($_rt && is_array($_rt)) {
                                        $_dt = array();
                                        foreach ($_rt as $provider => $token) {
                                            $_dt[$provider] = $provider;
                                        }
                                        $_lng = jrUser_load_lang_strings();
                                        $_tmp = array(
                                            'name'          => "jroneall_share_to_networks",
                                            'label'         => $_lng['jrOneAll'][14],
                                            'help'          => $_lng['jrOneAll'][15],
                                            'type'          => 'optionlist',
                                            'options'       => $_dt,
                                            'required'      => false,
                                            'form_designer' => false
                                        );
                                        if (strpos(' ' . $view, 'create')) {
                                            $_tmp['default'] = $_dt;
                                        }
                                        jrCore_form_field_create($_tmp);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            break;
    }
    return $_data;
}

/**
 * Add Share buttons to Timeline
 * @param $_data mixed Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrOneAll_parsed_template_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['jr_template'] == 'create_entry_form.tpl' && $_args['jr_template_directory'] == 'jrAction' && strpos($_data, 'jrAction_submit()')) {
        // Add in our share to networks if configured
        if (isset($_user['quota_jrOneAll_allowed']) && $_user['quota_jrOneAll_allowed'] == 'on') {
            // OneAll only supports SHARING with facebook, twitter, linkedin and vkontakte
            $tbl = jrCore_db_table_name('jrOneAll', 'link');
            $req = "SELECT provider FROM {$tbl} WHERE user_id = '{$_user['_user_id']}' AND shared = '1' AND provider IN('facebook','twitter','linkedin','vkontakte')";
            $_rt = jrCore_db_query($req, 'provider', false, 'provider');
            if ($_rt && is_array($_rt)) {
                $_rt = array(
                    '_networks' => $_rt
                );
                if ($html = jrCore_parse_template('timeline_networks.tpl', $_rt, 'jrOneAll')) {
                    $_data = str_replace('</form>', "{$html}\n</form>", $_data);
                }
            }
        }
    }
    return $_data;
}

//----------------------
// FUNCTIONS
//----------------------

/**
 * Add social login options to a form
 * @param string $form_view
 * @return bool
 */
function jrOneAll_add_social_login_field($form_view)
{
    global $_conf;
    // Are we configured for any login/signup networks?
    if (isset($_conf['jrOneAll_social_networks']) && strlen($_conf['jrOneAll_social_networks']) > 3) {

        list(, $view) = explode('/', $form_view);

        // Add in our custom OneAll Login box
        $ocb = str_replace(array('http://', 'https://'), '', rtrim(trim($_conf['jrOneAll_domain']), '/'));
        $_js = array('source' => jrCore_get_server_protocol() . '://' . $ocb . '/socialize/library.js');
        jrCore_create_page_element('javascript_href', $_js);

        $url = jrCore_get_module_url('jrOneAll');
        $prv = implode("','", explode(',', $_conf['jrOneAll_social_networks']));
        $htm = '<div id="oneall_social_login_container_' . $view . '" style="display:inline-block" onmouseover="jrOneAll_set_quota_id();"></div>
                <script type="text/javascript">
                    oneall.api.plugins.social_login.build("oneall_social_login_container_' . $view . '", { \'providers\' : [\'' . $prv . '\'], \'callback_uri\': \'' . $_conf['jrCore_base_url'] . '/' . $url . '/callback' . '\' });
                </script>';

        $_lng = jrUser_load_lang_strings();
        $_tmp = array(
            'type'  => 'custom',
            'label' => $_lng['jrOneAll'][3],
            'html'  => $htm,
            'help'  => $_lng['jrOneAll'][4]
        );
        jrCore_form_field_create($_tmp);
    }
    return true;
}

/**
 * Update the available providers from OneAll
 * @return mixed
 */
function jrOneAll_update_provider_list()
{
    $_rp = jrOneAll_api_call('providers.json');
    if ($_rp && is_array($_rp) && isset($_rp['response']['result']['data']['providers']['entries'])) {
        $_tm = $_rp['response']['result']['data']['providers']['entries'];
        if (is_array($_tm)) {
            $_sv = array();
            foreach ($_tm as $_pr) {
                if ($_pr['key'] != 'storage') {
                    $_sv["{$_pr['key']}"] = $_pr['name'];
                }
            }
            if (count($_sv) > 0) {
                jrCore_set_temp_value('jrOneAll', 'provider_options', $_sv);
                return $_sv;
            }
        }
    }

    // Defaults
    return array(
        'facebook' => 'Facebook',
        'linkedin' => 'LinkedIn',
        'twitter'  => 'Twitter'
    );
}

/**
 * Get Available providers
 * @return mixed
 */
function jrOneAll_get_provider_options()
{
    if (!$_pr = jrCore_get_temp_value('jrOneAll', 'provider_options')) {
        $_pr = jrOneAll_update_provider_list();
    }
    return $_pr;
}

/**
 * Get networks that can be shared to
 * @return array|bool
 */
function jrOneAll_get_sharing_providers()
{
    // OneAll currently only supports posting to Facebook, Twitter and LinkedIn
    // https://docs.oneall.com/api/resources/users/write-to-users-wall
    if ($_pr = jrOneAll_get_provider_options()) {
        $_nw = array();
        foreach ($_pr as $k => $v) {
            switch ($k) {
                case 'facebook';
                case 'twitter';
                case 'linkedin';
                    $_nw[$k] = $v;
            }
        }
        return $_nw;
    }
    return false;
}

/**
 * Return array of shared networks a user shares to
 * @param $user_id int User ID
 * @return array|bool
 */
function jrOneAll_get_shared_networks($user_id)
{
    $tbl = jrCore_db_table_name('jrOneAll', 'link');
    $req = "SELECT user_token, provider FROM {$tbl} WHERE user_id = '{$user_id}' AND shared = '1' AND user_token != ''";
    $_rt = jrCore_db_query($req, 'provider', false, 'user_token');
    if ($_rt && is_array($_rt)) {
        return $_rt;
    }
    return false;
}

/**
 * Send an API call to OneAll
 * @param string $url URL to call
 * @param string $method POST|GET|PUT|DELETE
 * @return bool|mixed
 */
function jrOneAll_api_call($url, $method = 'GET')
{
    global $_conf;
    if (strlen($_conf['jrOneAll_domain']) > 0 && isset($_conf['jrOneAll_public_key']{1}) && isset($_conf['jrOneAll_private_key']{1})) {
        $dom = rtrim(trim($_conf['jrOneAll_domain']), '/');
        $_rt = jrCore_load_url("{$dom}/{$url}", null, $method, 80, $_conf['jrOneAll_public_key'], $_conf['jrOneAll_private_key']);
        if ($_rt && strlen($_rt) > 0) {
            if ($_rs = json_decode($_rt, true)) {
                jrOneAll_log_api_call($url, $_rs);
                return $_rs;
            }
            else {
                jrCore_logger('MAJ', "unknown response from OneAll API call", $_rt);
            }
        }
    }
    return false;
}

/**
 * Log an API call and response to the OneAll log table
 * @param string $command
 * @param string $response
 * @return bool
 */
function jrOneAll_log_api_call($command, $response)
{
    $cmd = jrCore_db_escape($command);
    if (!$_tm = jrCore_get_load_url_response_headers()) {
        $_tm = 'unknown';
    }
    $_rs = array(
        'headers' => $_tm,
        'results' => $response
    );
    $tbl = jrCore_db_table_name('jrOneAll', 'api_log');
    $req = "INSERT INTO {$tbl} (log_created, log_command, log_text) VALUES (UNIX_TIMESTAMP(), '{$cmd}', '" . jrCore_db_escape(json_encode($_rs)) . "')";
    $lid = jrCore_db_query($req, 'INSERT_ID');
    if ($lid && $lid > 0) {
        return true;
    }
    return false;
}

/**
 * Retrieve a unique OneAll token for a user id
 * @param $user_id integer User ID to get token for
 * @return bool|string
 */
function jrOneAll_get_token($user_id)
{
    $uid = (int) $user_id;
    $tbl = jrCore_db_table_name('jrOneAll', 'link');
    $req = "SELECT token FROM {$tbl} WHERE user_id = '{$uid}' LIMIT 1";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt) && isset($_rt['token'])) {
        return trim($_rt['token']);
    }
    return false;
}

/**
 * Share an Action to a user's linked social networks
 * @param array $_queue The queue entry the worker will receive
 * @return bool
 */
function jrOneAll_share_action_worker($_queue)
{
    global $_conf;
    if (!$_queue || !is_array($_queue)) {
        return false;
    }

    // Load up proper sharing plugin
    if (isset($_conf['jrOneAll_advanced']) && $_conf['jrOneAll_advanced'] == 'on') {
        $file = "share_advanced";
        $func = "plugin_jrOneAll_share_advanced";
    }
    else {
        $file = "share_simple";
        $func = "plugin_jrOneAll_share_simple";
    }
    if (!function_exists($func)) {
        require_once APP_DIR . "/modules/jrOneAll/plugins/{$file}.php";
    }
    if (function_exists($func)) {
        $func($_queue);
    }
    return true;
}

/**
 * Embed a OneAll login/signup box in a template
 * @param array $params parameters for function
 * @param object $smarty Smarty object
 * @return string
 */
function smarty_function_jrOneAll_embed_code($params, &$smarty)
{
    global $_conf;
    $url = jrCore_get_module_url('jrOneAll');
    $prv = implode("','", explode(',', $_conf['jrOneAll_social_networks']));
    $ocb = str_replace(array('http://', 'https://'), '', rtrim(trim($_conf['jrOneAll_domain']), '/'));
    $out = '<script src="' . jrCore_get_server_protocol() . '://' . $ocb . '/socialize/library.js"></script>
    <div id="oneall_social_login_container" style="display:inline-block" onmouseover="jrOneAll_set_quota_id();"></div>
    <script type="text/javascript">
      oneall.api.plugins.social_login.build("oneall_social_login_container", { \'providers\' : [\'' . $prv . '\'], \'callback_uri\': \'' . $_conf['jrCore_base_url'] . '/' . $url . '/callback' . '\' });
    </script>';
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * System Wide share to timeline
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrOneAll_sitewide_feed($_data, $_user, $_conf, $_args, $event)
{
    if (jrUser_is_logged_in() && isset($_data['action_mode'])) {

        // Have we linked up the system to any networks?
        $_rt = jrOneAll_get_shared_networks(0);
        if ($_rt && is_array($_rt)) {
            $_owner = jrCore_db_get_item('jrUser', $_data['action_data']['_user_id']);
            if (!$_owner || !is_array($_owner) || !is_array($_data)) {
                return $_data;
            }
            $_action_data                = $_owner + $_data['action_data'];
            $_action_data['action_mode'] = $_data['action_mode'];
            $_action_data['profile_url'] = jrCore_db_get_item_key('jrProfile', $_data['action_data']['_profile_id'], 'profile_url');
            $_action_data['_item_id']    = $_data['action_data']['_item_id'];

            // Create our new queue entry for this share
            // NOTE: will only get shared if the module contains a network_share_text_listener() function to provide the share text.
            $_queue = array(
                'providers'     => implode(',', array_keys($_rt)),
                'user_token'    => reset($_rt),
                'user_id'       => 0,
                'action_module' => $_data['action_module'],
                'action_data'   => json_encode($_action_data)
            );

            jrCore_queue_create('jrOneAll', 'share_action', $_queue);
        }
    }
    return $_data;
}
