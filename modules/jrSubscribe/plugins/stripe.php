<?php
/**
 * Jamroom Subscriptions module
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
 * @copyright 2003 - 2016 Talldude Networks, LLC.
 */
// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Get meta data about this payment plugin
 * @return array
 */
function jrSubscribe_plugin_stripe_metadata()
{
    return array(
        'delete_api_support' => 1,
        'expire_notify'      => 1,
        'prorate_sub_change' => 1
    );
}

/**
 * Update a Credit Card with new one
 * @param array $_sub
 * @return bool
 */
function jrSubscribe_plugin_stripe_subscribe_update_onclick($_sub)
{
    global $_conf, $_user;
    $_lng = jrUser_load_lang_strings();
    $_cfg = jrPayment_get_plugin_config('stripe');
    $curr = (isset($_cfg['store_currency'])) ? $_cfg['store_currency'] : 'USD';
    $murl = jrCore_get_module_url('jrSubscribe');
    $iurl = jrCore_get_module_url('jrImage');

    $temp = array('source' => 'https://checkout.stripe.com/checkout.js');
    jrCore_create_page_element('javascript_footer_href', $temp);

    $temp = array("
    function jrSubscribe_stripe_update_payment_source() {
        var datap = {
            mode: 'update_card'
        };
        var token = function(res) {
            datap.token = res.id;
            datap.email = res.email;
            var purl = '" . $_conf['jrCore_base_url'] . '/' . $murl . "/update_payment_source_save/__ajax=1';
            jrCore_set_csrf_cookie(purl);
            $.ajax({
                type: 'POST',
                data: datap,
                cache: false,
                dataType: 'json',
                url: purl,
                success: function(msg) {
                    if (typeof msg.error != 'undefined') {
                        jrCore_alert(msg.error);
                    }
                    else {
                        jrCore_window_location(msg.url);
                    }
                }
            });
        };
        StripeCheckout.open({
            key: '{$_cfg['publish_key']}',
            image: '{$_conf['jrCore_base_url']}/{$iurl}/img/module/jrSubscribe/update-cc.png?_v=" . time() . "',
            billingAddress: false,
            currency: '{$curr}',
            email: '{$_user['user_email']}',
            description: '" . addslashes($_lng['jrSubscribe'][54]) . "',
            panelLabel: '" . addslashes($_lng['jrSubscribe'][54]) . "',
            token: token
        });
        return false;
    }");
    jrCore_create_page_element('javascript_footer_function', $temp);
    return "jrSubscribe_stripe_update_payment_source()";
}

/**
 * Update a Credit Card with new one - save
 * @param array $_post
 * @param array $_user
 * @param array $_conf
 */
function jrSubscribe_plugin_stripe_update_payment_source_save($_post, $_user, $_conf)
{
    jrCore_validate_location_url();
    if (isset($_post['token']) && strlen($_post['token']) > 0) {

        // Get Stripe customer key
        $key = jrCore_db_get_item_key('jrUser', $_user['_user_id'], 'user_stripe_customer_id');
        if (!$key || strlen($key) === 0) {
            // Should NOT get here
            jrCore_set_form_notice('error', 'An error was encountered retrieving your info - please try again');
            $_rs = array('url' => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/active_subscription");
            jrCore_json_response($_rs);
        }

        jrPayment_plugin_stripe_init_api();
        try {
            $cs = \Stripe\Customer::retrieve($key);
            /** @noinspection PhpUndefinedFieldInspection */
            $cs->source = $_post['token'];
            $cs->save();
        }
        catch (Exception $e) {
            jrCore_logger('CRI', 'Subscribe: error updating credit card with Stripe', array('_post' => $_post, '_us' => $_user, 'error' => $e));
            jrCore_set_form_notice('error', 'An error was encountered updating your Credit Card - please try again');
            $_rs = array('url' => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/active_subscription");
            jrCore_json_response($_rs);
        }

        // Trigger payment_source_updated even
        jrCore_trigger_event('jrSubscribe', 'payment_source_updated', $_user);

        // Notify user that their payment source has changed
        if ($_sub = jrSubscribe_get_profile_subscription($_user['user_active_profile_id'])) {
            jrSubscribe_notify_user($_user['_user_id'], $_sub['sub_plan_id'], 'subscription_payment_source_updated');
        }

        jrCore_logger('INF', 'Subscribe: customer has updated stripe payment source', array('_post' => $_post, 'user_name' => $_user['user_name']));
        $_rs = array('url' => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/active_subscription/update");
        jrCore_json_response($_rs);
    }
    jrCore_set_form_notice('error', 'An error was encountered updating your Credit Card - please try again (2)');
    $_rs = array('url' => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/active_subscription");
    jrCore_json_response($_rs);
}

/**
 * Get JS to start a subscription
 * @param array $_sub
 * @param string $action
 * @return string
 */
function jrSubscribe_plugin_stripe_subscribe_javascript($_sub, $action = 'create')
{
    global $_user, $_conf;
    $_lng = jrUser_load_lang_strings();
    $_cfg = jrPayment_get_plugin_config('stripe');
    $addr = ((isset($_cfg['address']) && $_cfg['address'] == 'on') || !jrUser_is_logged_in()) ? 'true' : 'false';
    $curr = (isset($_cfg['store_currency'])) ? $_cfg['store_currency'] : 'USD';
    $iurl = jrCore_get_module_url('jrImage');
    $murl = jrCore_get_module_url('jrSubscribe');
    $surl = "{$_conf['jrCore_base_url']}/{$murl}/plugin_view/stripe/checkout/action={$action}/__ajax=1";
    $sprc = (isset($_sub['sub_variable']) && $_sub['sub_variable'] == 'on') ? "jrSubscribe_get_sub_price({$_sub['_item_id']})" : jrPayment_price_to_cents($_sub['sub_item_price']);
    $desc = (isset($_sub['sub_variable']) && $_sub['sub_variable'] == 'on') ? "jrSubscribe_get_sub_price({$_sub['_item_id']},1)" : "'" . jrPayment_currency_format($_sub['sub_item_price']) . "'";
    $labl = (isset($_sub['sub_variable']) && $_sub['sub_variable'] == 'on') ? "'" . addslashes($_lng['jrSubscribe'][53]) . "' + ' " . html_entity_decode(jrPayment_get_currency_code()) . "' + jrSubscribe_get_sub_price({$_sub['_item_id']},1)" : "'" . addslashes($_lng['jrSubscribe'][53]) . "'";
    $html = '';
    if (!jrCore_get_flag('jrsubscribe_plugin_stripe_included')) {
        $html = '<script type="text/javascript" src="https://checkout.stripe.com/checkout.js"></script>';
        jrCore_set_flag('jrsubscribe_plugin_stripe_included', 1);
    }
    $html .= "<script type=\"text/javascript\">
    function jrSubscribe_plugin_stripe_checkout_{$_sub['_item_id']}() {
        var datap = {
            profile_id: " . intval($_user['user_active_profile_id']) . ",
            sub_id: " . intval($_sub['_item_id']) . "
        };
        var token = function(res) {
            datap.token = res.id;
            datap.email = res.email;
            datap.amount = {$sprc};
            var purl = '{$surl}';
            jrCore_set_csrf_cookie(purl);
            $.ajax({
                type: 'POST',
                data: datap,
                cache: false,
                dataType: 'json',
                url: purl,
                success: function(msg) {
                    if (typeof msg.error !== 'undefined') {
                        jrCore_alert(msg.error);
                    }
                    else {
                        jrCore_window_location(msg.url);
                    }
                }
            });
        };
        StripeCheckout.open({
            key: '{$_cfg['publish_key']}',
            image: '{$_conf['jrCore_base_url']}/{$iurl}/img/module/jrSubscribe/checkout.png?_v=" . $_sub['_updated'] . "',
            amount: {$sprc},
            billingAddress: {$addr},
            currency: '{$curr}',
            email: '{$_user['user_email']}',
            name: '" . addslashes($_sub['sub_title']) . "',
            description: '" . html_entity_decode(jrPayment_get_currency_code()) . "' + " . $desc . " + ' / " . jrSubscribe_get_text_duration($_sub['sub_duration']) . "',
            panelLabel: {$labl},
            token: token
        });
        return false;
    }</script>";
    return $html;
}

/**
 * Custom function for when a subscription is created
 * @param $_sub array subscription info
 * @param string $action
 * @return float
 */
function jrSubscribe_plugin_stripe_subscribe_onclick($_sub, $action = 'create')
{
    return "jrSubscribe_plugin_stripe_checkout_{$_sub['_item_id']}()";
}

/**
 * Change an existing plan to a NEW plan
 * @param array $_old existing subscription
 * @param array $_new new subscription
 * @param int $credit_days - delay billing until X days from now
 * @return bool
 */
function jrSubscribe_plugin_stripe_subscription_change_plan($_old, $_new, $credit_days = 0)
{
    // Is this a stripe subscription?
    $_md = jrSubscribe_get_sub_meta_data($_old['sub_profile_id'], $_old);
    if (isset($_md['subscription_id']) && strpos($_md['subscription_id'], '_')) {

        // We are changing subscriptions - update
        jrPayment_plugin_stripe_init_api();
        try {
            $sub = \Stripe\Subscription::retrieve($_md['subscription_id']);
            /** @noinspection PhpUndefinedFieldInspection */
            $sub->plan = $_new['sub_plan_id'];
            /** @noinspection PhpUndefinedFieldInspection */
            $sub->prorate = true;
            $sub->save();
            return true;
        }
        catch (Exception $e) {
            jrCore_logger('CRI', "Subscribe: error updating subscription_id {$_md['subscription_id']} via Stripe API", array('_sub' => $_new, 'error' => $e));
        }
    }
    return false;
}

/**
 * Immediately delete a subscription
 * @param array $_sub existing subscription
 * @return bool
 */
function jrSubscribe_plugin_stripe_subscription_delete($_sub)
{
    // Is this a stripe subscription?
    $_md = jrSubscribe_get_sub_meta_data($_sub['sub_profile_id'], $_sub);
    if (isset($_md['subscription_id']) && strpos($_md['subscription_id'], '_')) {

        // We are changing subscriptions - update
        jrPayment_plugin_stripe_init_api();
        try {
            $sub = \Stripe\Subscription::retrieve($_md['subscription_id']);
            // NOTE: do NOT set "at_period_end" to TRUE here
            // https://stripe.com/docs/api#cancel_subscription
            $sub->cancel();
        }
        catch (Exception $e) {
            jrCore_logger('CRI', "Subscribe: error canceling subscription_id {$_md['subscription_id']} via Stripe API", array('_sub' => $_sub, 'error' => $e));
        }
    }
    return true;
}

/**
 * URL to cancel a subscription
 * @param $_sub array subscription info
 * @return mixed
 */
function jrSubscribe_plugin_stripe_subscribe_cancel_onclick($_sub)
{
    global $_post, $_conf;
    return "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/plugin_view/stripe/subscription_cancel')";
}

/**
 * Show subscription cancel form
 * @param array $_post
 * @param array $_user
 * @param array $_conf
 */
function jrSubscribe_plugin_view_stripe_subscription_cancel($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    $_ln = jrUser_load_lang_strings();
    // Does this user have a subscription?
    if (!$_sub = jrSubscribe_get_profile_subscription($_user['user_active_profile_id'])) {
        jrCore_notice_page('error', $_ln['jrSubscribe'][13], 'referrer', $_ln['jrCore'][87]);
    }
    if (isset($_sub['sub_status']) && $_sub['sub_status'] == 'canceled') {
        jrCore_notice_page('success', $_ln['jrSubscribe'][14], 'referrer', $_ln['jrCore'][87]);
    }
    $_pl = jrCore_db_get_item('jrSubscribe', $_sub['sub_plan_id']);

    jrCore_page_banner($_ln['jrSubscribe'][15]);
    jrCore_set_form_notice('success', "{$_ln['jrSubscribe'][17]}<br><br><h3>{$_pl['sub_title']}</h3><br><br>{$_ln['jrSubscribe'][12]}: " . jrCore_format_time($_sub['sub_expires'], true), false);

    // Form init
    $_tmp = array(
        'submit_value'  => $_ln['jrSubscribe'][15],
        'cancel'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/active_subscription",
        'action'        => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/plugin_view/stripe/subscription_cancel_save",
        'submit_title'  => $_ln['jrSubscribe'][15],
        'submit_prompt' => $_ln['jrSubscribe'][16]
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'  => 'sub_id',
        'type'  => 'hidden',
        'value' => intval($_sub['sub_id'])
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

/**
 * Process subscription cancellation
 * @param array $_post
 * @param array $_user
 * @param array $_conf
 */
function jrSubscribe_plugin_view_stripe_subscription_cancel_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_form_validate($_post);
    if ($_sub = jrSubscribe_get_subscription_by_id($_post['sub_id'])) {

        $sub_id = false;
        if (isset($_sub['sub_data']) && strlen($_sub['sub_data']) > 0) {
            if ($_tmp = json_decode($_sub['sub_data'], true)) {
                if (isset($_tmp['subscription_id'])) {
                    $sub_id = $_tmp['subscription_id'];
                }
            }
        }

        // We have a Stripe subscription ID - cancel it
        if ($sub_id) {
            jrPayment_plugin_stripe_init_api();
            try {
                $sub = \Stripe\Subscription::retrieve($sub_id);
                $sub->cancel(array('at_period_end' => true));
                jrCore_logger('INF', "Subscribe: successfully canceled subscription_id {$sub_id} via Stripe API");
            }
            catch (Exception $e) {
                jrCore_logger('CRI', "Subscribe: error canceling subscription_id {$sub_id} via Stripe API", array('_sub' => $_sub, 'error' => $e));
            }
        }

        if (jrSubscribe_cancel_subscription($_sub['sub_profile_id'])) {
            jrCore_form_delete_session();
            jrCore_logger('INF', "Subscribe: subscription for profile_id {$_sub['sub_profile_id']} has been canceled at end of term: " . jrCore_format_time($_sub['sub_expires']), $_sub);
            jrCore_set_form_notice('success', 'your subscription has been successfully canceled');
            jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/active_subscription/cancel");
        }
        jrCore_set_form_notice('error', 'an error was encountered canceling the subscription - please try again');
        jrCore_location('referrer');
    }
    jrCore_set_form_notice('error', 'unable to retrieve active subscription - please try again');
    jrCore_location('referrer');
}

/**
 * Process checkout
 * @param $_post array posted params
 * @param $_user array current User
 * @param $_conf array Global Conf
 * @return bool
 */
function jrSubscribe_plugin_view_stripe_checkout($_post, $_user, $_conf)
{
    jrCore_validate_location_url();
    if (!isset($_post['profile_id']) || !jrCore_checktype($_post['profile_id'], 'number_nz')) {
        $_rs = array('error' => 'Invalid profile id');
        jrCore_json_response($_rs);
    }
    if (!isset($_post['sub_id']) || !jrCore_checktype($_post['sub_id'], 'number_nz')) {
        $_rs = array('error' => 'Invalid subscription id');
        jrCore_json_response($_rs);
    }
    $_sub = jrCore_db_get_item('jrSubscribe', $_post['sub_id']);
    if (!$_sub || !is_array($_sub)) {
        $_rs = array('error' => 'Invalid subscription id - data not found');
        jrCore_json_response($_rs);
    }

    // Did we get a price?  Note that we ignore the posted price UNLESS it is
    // a "pay what you want" subscription
    $quantity = 1;
    if (isset($_sub['sub_variable']) && $_sub['sub_variable'] == 'on' && isset($_post['amount']) && jrCore_checktype($_post['amount'], 'number_nz')) {
        $quantity = (int) $_post['amount'];
    }

    // Charge it
    jrPayment_plugin_stripe_init_api();

    // Our token is unique for our customer
    $token = (isset($_post['token'])) ? $_post['token'] : '';

    // Create new Stripe customer if needed
    $new_customer = false;
    if (!isset($_user['user_stripe_customer_id']) || strlen($_user['user_stripe_customer_id']) === 0) {

        // Must get a token
        if (!isset($_post['token']) || strlen($_post['token']) === 0) {
            jrCore_logger('CRI', 'Subscribe: invalid token received in checkout', array('_post' => $_post, '_us' => $_user));
            $_er = array('error' => 'an error was encountered checking out - please try again');
            jrCore_json_response($_er);
        }
        // Create new Customer
        try {
            $customer = \Stripe\Customer::create(array(
                'card'        => $token,
                'email'       => $_user['user_email'],
                'description' => $_user['user_name']
            ));
            /** @noinspection PhpUndefinedFieldInspection */
            $cid = $customer->id;
        }
        catch (Exception $e) {
            jrCore_logger('CRI', 'Subscribe: error creating customer account with Stripe', array('_us' => $_user, 'error' => $e));
            $_er = array('error' => 'Unable to successfully create the transaction - please try again');
            jrCore_json_response($_er);
            return false;
        }

        if (!$cid) {
            jrCore_logger('CRI', 'Subscribe: error creating customer account with Stripe (2)', array('_us' => $_user, 'cid' => $cid));
            $_er = array('error' => 'Unable to successfully create the transaction - please try again (2)');
            jrCore_json_response($_er);
            return false;
        }

        // Fall through - Stripe customer created, update user info
        jrCore_db_update_item('jrUser', $_user['_user_id'], array('user_stripe_customer_id' => $cid));
        $_user['user_stripe_customer_id'] = $cid;
        $new_customer                     = true;
    }

    // Make sure our customer info is updated with new info first
    if (!$new_customer) {
        try {
            $cs = \Stripe\Customer::retrieve($_user['user_stripe_customer_id']);
            /** @noinspection PhpUndefinedFieldInspection */
            $cs->source = $token;
            $cs->save();
        }
        catch (Exception $e) {
            jrCore_logger('MAJ', 'Subscribe: error updating credit card with Stripe (2)', array('_post' => $_post, '_us' => $_user, 'error' => $e));
            $_er = array('error' => 'An error was encountered saving the transaction - please try again');
            jrCore_json_response($_er);
        }
    }

    // Are we getting a NEW subscription, or changing an existing subscription to a new one?
    if ($_as = jrSubscribe_get_profile_subscription($_user['user_active_profile_id'])) {

        if ($_old = jrCore_db_get_item('jrSubscribe', $_as['sub_plan_id'])) {

            // Change plan
            $_md = jrSubscribe_get_sub_meta_data($_as['sub_profile_id'], $_as);
            if (isset($_md['subscription_id']) && strpos($_md['subscription_id'], '_')) {

                try {
                    $sub = \Stripe\Subscription::retrieve($_md['subscription_id']);
                    /** @noinspection PhpUndefinedFieldInspection */
                    $sub->plan = $_sub['_item_id'];
                    /** @noinspection PhpUndefinedFieldInspection */
                    $sub->prorate = true;
                    $sub->save();
                }
                catch (Exception $e) {
                    jrCore_logger('CRI', "Subscribe: error updating subscription_id {$_md['subscription_id']} via Stripe API", array('_sub' => $_as, 'error' => $e));
                }

                // Let the user know their subscription has started
                $url = jrCore_get_module_url('jrSubscribe');
                $_rs = array('url' => "{$_conf['jrCore_base_url']}/{$url}/success");
                return jrCore_json_response($_rs);
            }
        }
        else {
            jrCore_logger('CRI', "Subcribe: plan_id for subscription id {$_as['sub_id']} no longer exists", $_as);
        }
    }

    else {

        // Has this profile already used the free trial for this plan?
        $days = 0;
        if (isset($_sub['sub_trial']) && $_sub['sub_trial'] != '0') {
            if (jrSubscribe_profile_used_trial($_post['profile_id'], $_post['sub_id'])) {
                $days = 0;
            }
            else {
                $days = jrSubscribe_convert_interval_to_days($_sub['sub_trial']);
            }
        }

        // Run charge
        try {

            $_tmp = array(
                'customer'          => $_user['user_stripe_customer_id'],
                'items'             => array(
                    array(
                        'plan'     => $_post['sub_id'],
                        'quantity' => $quantity
                    ),
                ),
                'trial_period_days' => $days,
                'metadata'          => array(
                    'plan_name'  => $_sub['sub_title'],
                    'profile_id' => $_post['profile_id'],
                    'user_email' => $_user['user_email'],
                    'user_id'    => $_user['_user_id']
                )
            );
            \Stripe\Subscription::create($_tmp);

            // Let the user know their subscription will be started
            $url = jrCore_get_module_url('jrSubscribe');
            $_rs = array('url' => "{$_conf['jrCore_base_url']}/{$url}/success");
            return jrCore_json_response($_rs);

        }
        catch (Exception $e) {
            jrCore_logger('MAJ', 'Subscribe: subscription purchase credit card declined', $e);
            $_er = array('error' => 'The purchase was declined - please check the Credit Card info and try again');
            jrCore_json_response($_er);
        }
    }

    return false;
}

/**
 * Watch for subscription webhooks and insert metadata
 * @param $_txn array incoming transaction
 * @return mixed
 */
function jrSubscribe_plugin_stripe_webhook_parse($_txn)
{
    if (isset($_txn['txn_raw'])) {
        $_raw = $_txn['txn_raw'];
        switch ($_raw['type']) {

            case 'plan.created':
            case 'plan.updated':
            case 'plan.deleted':
            case 'invoice.created':
            case 'invoice.payment_failed':
            case 'customer.subscription.created':
            case 'customer.subscription.updated':
            case 'customer.subscription.deleted':
            case 'customer.subscription.trial_will_end':
                $_txn['txn_type'] = 'information';
                break;

            case 'invoice.payment_succeeded':
                // NOTE: If the subscription plan has a FREE TRIAL period, we will get a
                // successful invoice payment for 0
                if ($_raw['data']['object']['total'] == 0 && $_raw['data']['object']['amount_due'] == 0) {
                    // We have an invoice being paid on a free trial plan
                    $_txn['txn_free_trial'] = 1;
                    $_txn['txn_total']      = 0;
                }
                else {
                    $_txn['txn_total'] = (int) $_raw['data']['object']['total'];
                }
                $_txn['txn_user_id']    = (int) $_raw['data']['object']['lines']['data'][0]['metadata']['user_id'];
                $_txn['txn_user_email'] = $_raw['data']['object']['lines']['data'][0]['metadata']['user_email'];
                $_txn['txn_type']       = 'subscription';
                break;

            case 'charge.succeeded':
                // If we have a charge, we need to find out if this is for a subscription.  If it IS part of
                // a subscription, we will get an invoice.payment_succeeded that covers the sub charge
                if (isset($_raw['data']['object']['invoice']{2})) {
                    // This charge has an INVOICE - it is for a subscription.  We need
                    // to remove the total from this and mark it as information
                    $_txn['txn_type'] = 'information';
                    unset($_txn['txn_total']);
                }
                break;
        }
    }
    return $_txn;
}

/**
 * Watch for a successful subscription webhook
 * @param $_txn array incoming transaction
 * @return mixed
 */
function jrSubscribe_plugin_stripe_webhook_process($_txn)
{
    global $_conf;
    if (isset($_txn['txn_raw'])) {
        $_raw = $_txn['txn_raw'];
        switch ($_raw['type']) {

            case 'plan.created':
                jrCore_logger('INF', "Subscribe: new plan successfully created at Stripe via API", $_raw);
                break;

            case 'plan.updated':
                jrCore_logger('INF', "Subscribe: existing plan successfully updated at Stripe via API", $_raw);
                break;

            case 'plan.deleted':
                // We have an incoming request to delete an existing plan - make sure we are deleted
                jrSubscribe_delete_plan($_raw['data']['object']['id']);
                jrCore_logger('INF', "Subscribe: existing plan successfully deleted at Stripe via API", $_raw);
                break;

            case 'customer.subscription.deleted':
                // Has this subscription been IMMEDIATELY canceled?
                // This happens when the master presses "delete subscription" in the ACP
                // Also happens after 3rd attempt to charge an invoice fails
                $pid = (int) $_raw['data']['object']['metadata']['profile_id'];
                if (isset($_raw['data']['object']['status']) && $_raw['data']['object']['status'] == 'canceled') {
                    $exp = time();
                    $now = true;
                }
                else {
                    $exp = (int) $_raw['data']['object']['current_period_end'];
                    $now = false;
                }
                $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
                $req = "UPDATE {$tbl} SET sub_status = 'canceled', sub_updated = UNIX_TIMESTAMP(), sub_expires = {$exp} WHERE sub_profile_id = {$pid}";
                $cnt = jrCore_db_query($req, 'COUNT');
                if ($cnt && $cnt === 1) {
                    if ($now) {
                        jrSubscribe_delete_subscription($pid);
                        jrCore_logger('INF', "Subscribe: subscription for profile_id {$pid} has been canceled immediately", $_raw);
                    }
                    else {
                        jrCore_logger('INF', "Subscribe: subscription for profile_id {$pid} has been canceled at end of term: " . jrCore_format_time($exp), $_raw);
                    }
                }
                else {
                    jrCore_logger('CRI', "Subscribe: error canceling subscription for profile_id {$pid}", $_raw);
                }
                break;

            case 'customer.subscription.updated':
                // A profile has moved to a NEW subscription from an OLD subscription
                $sid = (int) $_raw['data']['object']['plan']['id'];
                if ($_pl = jrCore_db_get_item('jrSubscribe', $sid)) {
                    // Move to new quota_id
                    $pid = (int) $_raw['data']['object']['metadata']['profile_id'];
                    $prc = jrPayment_price_to_cents($_pl['sub_item_price']);
                    // If this is a VARIABLE priced subscription, skip price validation
                    $val = true;
                    if (isset($_pl['sub_variable']) && $_pl['sub_variable'] == 'on') {
                        $val = false;
                    }
                    jrSubscribe_start_subscription($pid, $sid, $prc, null, $val, false, false);
                }
                else {
                    jrCore_logger('CRI', "Subscribe: plan_id received in stripe webhook does not exist", array('_txn' => $_txn));
                }
                break;

            case 'customer.subscription.trial_will_end':
                // Send out email notification about upcoming subscription charge
                // Make sure this is a good plan
                if ($uid = (int) $_raw['data']['object']['metadata']['user_id']) {
                    $sid = (int) $_raw['data']['object']['plan']['id'];
                    jrSubscribe_notify_user($uid, $sid, 'subscription_trial_expiring');
                }
                break;

            case 'customer.source.expiring':
                // A customers credit card is going to expire soon
                // Make sure we are not PAST the expiration date
                if (isset($_conf['jrSubscribe_expire_notify']) && $_conf['jrSubscribe_expire_notify'] == 'on') {
                    $y = (int) $_raw['data']['object']['exp_year'];
                    $m = str_pad($_raw['data']['object']['exp_month'], 2, '0', STR_PAD_LEFT);
                    if (intval($y . $m) >= strftime('%Y%m')) {
                        if ($_us = jrCore_db_get_item_by_key('jrUser', 'user_stripe_customer_id', $_raw['data']['object']['customer'])) {

                            // Is this user on a subscription plan?
                            if ($_sb = jrSubscribe_get_subscription_by_profile_id($_us['_profile_id'])) {

                                // Did we already notify about this expiration?
                                if (jrSubscriber_get_subscription_note($_sb['sub_id'], "exp{$y}{$m}")) {
                                    // We've already notified about this expiration
                                    continue;
                                }

                                // Fall through - let the user know that their subscription payment method is about to expire
                                $url = jrCore_get_module_url('jrSubscribe');
                                $_rp = array(
                                    '_subscription'     => $_sb,
                                    'card_expire_month' => $m,
                                    'card_expire_year'  => $y,
                                    'update_card_url'   => "{$_conf['jrCore_base_url']}/{$url}/active_subscription"
                                );
                                jrSubscribe_notify_user($_us['_user_id'], $_sb['sub_plan_id'], 'subscription_payment_method_expiring', $_rp);
                                jrCore_logger('INF', "Subscribe: notified {$_us['user_email']} about pending payment method expiration", $_rp);

                                // Save that we've notified on this expiration
                                jrSubscriber_save_subscription_note($_sb['sub_id'], "exp{$y}{$m}", 1);
                            }
                        }
                    }
                }
                break;

            case 'invoice.payment_failed':
                // When a payment fails, we follow the settings as set in Stripe
                // and check the "status" of the subscription.  See:
                // https://stripe.com/docs/subscriptions/lifecycle
                if (isset($_raw['data']['object']['status'])) {

                    switch (strtolower($_raw['data']['object']['status'])) {

                        case 'unpaid':
                            // User payment has failed
                            $pid = (int) $_raw['data']['object']['lines']['data'][0]['metadata']['profile_id'];
                            jrCore_logger('INF', "Subscribe: subscription payment for profile_id {$pid} has failed - notifying user", $_raw);
                            jrSubscribe_set_subscription_state($pid, 'unpaid');

                            if ($uid = (int) $_raw['data']['object']['lines']['data'][0]['metadata']['user_id']) {
                                $sid = (int) $_raw['data']['object']['lines']['data'][0]['metadata']['plan_id'];
                                jrSubscribe_notify_user($uid, $sid, 'subscription_past_due');
                            }

                            break;

                        case 'canceled':
                            // This user's payment has failed OR they have reached the
                            // end of their trial period and their payment failed
                            $pid = (int) $_raw['data']['object']['lines']['data'][0]['metadata']['profile_id'];
                            if ($pid && $pid > 0) {
                                jrSubscribe_delete_subscription($pid);
                                jrCore_logger('INF', "Subscribe: subscription for profile_id {$pid} has been canceled due to payment failure", $_raw);
                            }
                            break;

                    }

                }
                break;

            case 'invoice.payment_succeeded':

                // We successfully received a subscription payment - make sure it is
                // not a negative subscription payment for prorating
                if ($_raw['data']['object']['total'] >= 0) {

                    $sid = (int) $_raw['data']['object']['lines']['data'][0]['plan']['id'];
                    if ($_pl = jrCore_db_get_item('jrSubscribe', $sid)) {

                        // Start subscription
                        $uid = (int) $_raw['data']['object']['lines']['data'][0]['metadata']['user_id'];
                        $pid = (int) $_raw['data']['object']['lines']['data'][0]['metadata']['profile_id'];
                        $gid = $_raw['data']['object']['id'];
                        $sub = $_raw['data']['object']['lines']['data'][0]['id'];

                        // If this is a VARIABLE priced subscription, skip price validation
                        $val = true;
                        $prc = jrPayment_price_to_cents($_pl['sub_item_price']);
                        if (isset($_pl['sub_variable']) && $_pl['sub_variable'] == 'on') {
                            $val = false;
                            $prc = (int) $_raw['data']['object']['amount_due'];
                        }

                        if ($_txn['txn_total'] == 0) {
                            // We have an incoming 0 payment - check for trial period
                            if (isset($_pl['sub_trial']) && $_pl['sub_trial'] != '0') {
                                // This plan has a TRIAL period - turn off price validation
                                jrSubscribe_start_subscription($pid, $sid, $prc, $_pl['sub_trial'], false);
                                jrSubscribe_set_subscription_state($pid, 'trial');
                            }
                            else {
                                jrSubscribe_start_subscription($pid, $sid, $prc, null, $val);
                            }
                        }
                        else {
                            // Regular subscription payment - no need to notify
                            jrSubscribe_start_subscription($pid, $sid, $prc, null, $val);
                        }
                        $fee = 0;
                        if (isset($_txn['txn_gateway_fee'])) {
                            $fee = $_txn['txn_gateway_fee'];
                        }
                        jrSubscribe_record_sale_in_register($_txn['txn_item_id'], $prc, $gid, $uid, $_pl, $sub, $fee);

                        // We need to grab the subscription ID and save it
                        jrSubscribe_save_sub_metadata_key($pid, 'subscription_id', $sub);

                    }
                    else {
                        jrCore_logger('CRI', "Subscribe: plan_id received in stripe webhook does not exist", array('_txn' => $_txn));
                    }
                }
                break;
        }

    }
    return $_txn;
}

/**
 * When a subscription is created LOCALLY, it is created at STRIPE
 * @param $plan_id int Subscription DS ID
 * @param $_plan array plan info
 * @return bool
 */
function jrSubscribe_plugin_stripe_subscription_plan_created($plan_id, $_plan)
{
    $config = jrPayment_get_plugin_config('stripe');
    jrPayment_plugin_stripe_init_api();
    list($number, $interval) = explode(':', $_plan['sub_duration']);
    switch (trim($interval)) {
        case 'd':
            $interval = 'day';
            break;
        case 'w':
            $interval = 'week';
            break;
        case 'm':
            $interval = 'month';
            break;
        case 'y':
            $interval = 'year';
            break;
    }

    // If we are creating a VARIABLE priced subscription, we set the amount to "1" (1 cent)
    // and then adjust the QUANTITY for when a user checks out for the subscription
    $amount = jrPayment_price_to_cents($_plan['sub_item_price']);
    if (isset($_plan['sub_variable']) && $_plan['sub_variable'] == 'on') {
        $amount = 1;
    }
    $_tmp = array(
        'id'                   => $plan_id,
        'amount'               => $amount,
        'currency'             => $config['store_currency'],
        'interval'             => $interval,
        'interval_count'       => intval($number),
        'name'                 => $_plan['sub_title'],
        'statement_descriptor' => $_plan['sub_title'],
        'trial_period_days'    => jrSubscribe_convert_interval_to_days($_plan['sub_trial'])
    );
    try {
        \Stripe\Plan::create($_tmp);
    }
    catch (Exception $e) {
        // Has this site upgraded to the new Stripe products API?
        // Received unknown parameters: name,  statement_descriptor
        if (stripos(json_encode($e), 'Received unknown parameters')) {
            // Try again with new API setup
            $_tmp = array(
                'id'                => $plan_id,
                'amount'            => $amount,
                'currency'          => $config['store_currency'],
                'interval'          => $interval,
                'interval_count'    => intval($number),
                'product'           => array(
                    'name' => $_plan['sub_title']
                ),
                'trial_period_days' => jrSubscribe_convert_interval_to_days($_plan['sub_trial'])
            );
            try {
                \Stripe\Plan::create($_tmp);
            }
            catch (Exception $e) {
                jrCore_logger('MAJ', 'Subscribe: error creating subscription plan with Stripe', array('_tmp' => $_tmp, '_plan' => $_plan, 'error' => $e));
                return false;
            }
        }
        else {
            jrCore_logger('MAJ', 'Subscribe: error creating subscription plan with Stripe', array('_tmp' => $_tmp, '_plan' => $_plan, 'error' => $e));
            return false;
        }
    }
    return true;
}

/**
 * When a subscription is updated LOCALLY, it is updated at STRIPE
 * @param $plan_id int Subscription DS ID
 * @param $_plan array plan info
 * @return bool
 */
function jrSubscribe_plugin_stripe_subscription_plan_updated($plan_id, $_plan)
{
    jrPayment_plugin_stripe_init_api();
    try {
        $p = \Stripe\Plan::retrieve($plan_id);
        /** @noinspection PhpUndefinedFieldInspection */
        $p->name = $_plan['sub_title'];
        $p->save();
    }
    catch (Exception $e) {
        // Do we not have this plan at Stripe? It could be from an import
        if (strpos(json_encode($e), 'No such plan')) {
            if (jrSubscribe_plugin_stripe_subscription_plan_created($plan_id, $_plan)) {
                return true;
            }
        }
        jrCore_logger('MAJ', 'Subscribe: unable to update subscription plan with Stripe', array('_plan' => $_plan, 'error' => $e));
        return false;
    }
    return true;
}

/**
 * Convert a FoxyCart subscriber into a Stripe subscriber
 * @param array $_sub - FoxyCart subscription info
 * @param array $_plan
 * @param array $_profile
 * @return bool
 */
function jrSubscribe_plugin_stripe_convert_foxycart_subscription($_sub, $_plan, $_profile)
{
    // We need FoxyCart functions
    require_once APP_DIR . '/modules/jrPayment/plugins/stripe.php';
    require_once APP_DIR . '/modules/jrPayment/plugins/foxycart.php';
    require_once APP_DIR . '/modules/jrSubscribe/plugins/foxycart.php';

    // Get user info for this profile
    if (!$_us = jrCore_db_get_item('jrUser', $_profile['_user_id'], true)) {
        return "ERROR: unable to load User Account information for profile_id: {$_profile['_profile_id']}";
    }

    // Get this subscriptions Subscription toke
    $_mt = jrSubscribe_get_sub_meta_data($_profile['_profile_id']);
    if (empty($_mt['sub_token'])) {
        return "ERROR: subscription missing sub_token";
    }

    $_fs = jrSubscribe_plugin_foxycart_get_subscription_info($_mt['sub_token']);
    if (!$_fs || !is_array($_fs)) {
        return "ERROR: unable to retrieve FoxyCart subscription for sub_token: {$_mt['sub_token']}";
    }
    if (empty($_fs['customer_id'])) {
        return "ERROR: customer_id missing from FoxyCart subscription info";
    }

    // We have our subscription info - get subscriber info
    $_fc = jrPayment_plugin_foxycart_get_customer_info_by_id($_fs['customer_id']);
    if (!$_fc || !is_array($_fc)) {
        return "ERROR: unable to retrieve FoxyCart customer information for customer_id: {$_fs['customer_id']}";
    }
    if (empty($_fc['customer_email'])) {
        return "ERROR: customer_email missing from FoxyCart customer info";
    }

    // NOTE: FoxyCart does NOT create any subscription or customers at Stripe
    // We have to go through payments and try and find a CHARGE object for
    // this user - if we find one we can then retrieve the CHARGE id and
    // use that as our SOURCE when creating the customer at Stripe
    $email = trim($_fc['customer_email']);

    $_charge = false;
    $_rs     = array(
        'limit' => 100
    );
    jrPayment_plugin_stripe_init_api();
    while (true) {
        try {
            $c = \Stripe\Charge::all($_rs);
        }
        catch (Exception $e) {
            jrCore_logger('CRI', 'Subscribe: Stripe API error retrieving charges', array('error' => $e));
            return false;
        }
        $c = $c->__toArray(true);
        if ($c && is_array($c) && isset($c['data']) && count($c['data']) > 0) {
            // Go through charges until we find our customer
            foreach ($c['data'] as $_ch) {
                if (!empty($_ch['description']) && $_ch['description'] == $email) {
                    // We found a charge!
                    $_charge = $_ch;
                    break 2;
                }
                if (!empty($_ch['metadata']['Customer Email']) && $_ch['metadata']['Customer Email'] == $email) {
                    // We found a charge!
                    $_charge = $_ch;
                    break 2;
                }
                $id = $_ch['id'];
            }
            // Fall through - we did not find our customer - go to next page if we have one
            if (isset($c['has_more']) && $c['has_more'] == 1 && isset($id)) {
                $_rs['starting_after'] = $id;
            }
            else {
                break;
            }
        }
        else {
            // No more data
            break;
        }
    }
    if (!$_charge || !isset($_charge['source']['id'])) {
        // We did not find a charge for this user - we won't be able to set them up
        jrCore_logger('MAJ', "Subscribe: unable to find Stripe source ID for customer: {$email}", $_charge);
        return false;
    }

    // We have a SOURCE ID with which to create a customer
    try {
        $customer = \Stripe\Customer::create(array(
            'source'      => $_charge['source']['id'],
            'email'       => $email,
            'description' => $_us['user_name']
        ));
        /** @noinspection PhpUndefinedFieldInspection */
        $cid = $customer->id;
    }
    catch (Exception $e) {
        jrCore_logger('CRI', 'Subscribe: error creating converted customer account with Stripe', array('_us' => $_us, 'error' => $e));
        return false;
    }
    if (!$cid) {
        jrCore_logger('CRI', 'Subscribe: error creating converted customer account with Stripe (2)', array('_us' => $_us, 'cid' => $cid));
        return false;
    }

    // Fall through - Stripe customer created, update user info
    jrCore_db_update_item('jrUser', $_us['_user_id'], array('user_stripe_customer_id' => $cid));
    $_us['user_stripe_customer_id'] = $cid;

    // When is the next transaction date?
    // We have to figure out the TRIAL DAYS we need to setup before the next charge
    // [next_transaction_date] => 2018-02-04
    list($tyear, $tmon, $tday) = explode('-', $_sub['next_transaction_date']);
    $next_payment_time = mktime(23, 23, 59, $tmon, $tday, $tyear);
    $trial_period_days = ceil(($next_payment_time - time()) / 86400);

    // Next - we need to subscribe this user to the plan
    $_tmp = array(
        'customer'          => $cid,
        'items'             => array(
            array(
                'plan'     => $_plan['_item_id'],
                'quantity' => 1
            ),
        ),
        'trial_period_days' => $trial_period_days,
        'metadata'          => array(
            'plan_name'  => $_plan['sub_title'],
            'profile_id' => $_profile['_profile_id'],
            'user_email' => $_us['user_email'],
            'user_id'    => $_us['_user_id']
        )
    );
    try {
        $s = \Stripe\Subscription::create($_tmp);
    }
    catch (Exception $e) {
        // We had an error creating the subscription
        jrCore_logger('CRI', "Subscribe: error creating converted subscription for Stripe customer: {$cid}", array('_us' => $_us, '_tmp' => $_tmp));
        return false;
    }
    $s = $s->__toArray(true);

    // We need to grab the subscription ID and save it
    jrSubscribe_save_sub_metadata_key($_profile['_profile_id'], 'subscription_id', $s['id']);

    // Update active subscription info
    $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
    $req = "UPDATE {$tbl} SET sub_plugin = 'stripe', sub_expires = " . ($next_payment_time + 86400) . " WHERE sub_id = " . intval($_tmp['sub_id']);
    jrCore_db_query($req);

    // Fall through - mark subscription inactive at FoxyCart
    if (jrSubscribe_plugin_foxycart_delete_subscription($_tmp)) {
        jrCore_logger('INF', "Subscribe: converted FoxyCart customer {$_sub['customer_id']} ({$email}) to Stripe customer {$cid}", array('stripe' => $s, '_fc' => $_fc, '_tmp' => $_tmp));
        return true;
    }
    jrCore_logger('CRI', "Subscribe: Unable to deactivate FoxyCart subscription for customer {$_sub['customer_id']} ({$email}) - manually set inactive", array('stripe' => $s, '_fc' => $_fc, '_tmp' => $_tmp));
    return false;
}
