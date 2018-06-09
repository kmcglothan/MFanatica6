<?php
/**
 * Paradigmusic Api Calls module
 *
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function xxApiCalls_meta()
{
    $_tmp = array(
        'name'        => 'Api Calls',
        'url'         => 'api_calls',
        'version'     => '1.0.0',
        'developer'   => 'Paradigmusic, &copy;' . strftime('%Y'),
        'description' => 'api calls module',
        'license'     => 'mpl',
        'category'    => 'custom'
    );
    return $_tmp;
}

/**
 * init
 */
function xxApiCalls_init()
{
    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'xxApiCalls', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'xxApiCalls', 'update');

    // Core support
    jrCore_register_module_feature('jrCore', 'quota_support', 'xxApiCalls', 'on');
    jrCore_register_module_feature('jrCore', 'pending_support', 'xxApiCalls', 'on');
    jrCore_register_module_feature('jrCore', 'max_item_support', 'xxApiCalls', 'on');
    jrCore_register_module_feature('jrCore', 'action_support', 'xxApiCalls', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'xxApiCalls', 'update', 'item_action.tpl');

    // We have fields that can be searched
    jrCore_register_module_feature('jrSearch', 'search_fields', 'xxApiCalls', 'testing_title', 1);
    jrCore_register_module_feature('jrCore', 'css', 'xxApiCalls', 'test.css');

    // Integrate with payment system
    jrCore_register_event_listener('jrFoxyCart', 'my_items_row', 'xxApiCalls_my_items_row_listener');
    jrCore_register_event_listener('jrFoxyCart', 'adding_item_to_purchase_history', 'xxApiCalls_adding_item_to_purchase_history_listener');
    jrCore_register_event_listener('jrFoxyCart', 'my_earnings_row', 'xxApiCalls_my_earnings_row_listener');
    // Added API Item Purchased Listener
    jrCore_register_event_listener('jrPayment', 'register_entry', 'xxApiCalls_register_entry_listener');

    //API Calls hourly event listeners
    jrCore_register_event_listener('jrUser', 'signup_activated', 'xxApiCalls_user_signup_activated_listener');
    jrCore_register_event_listener('jrUser', 'user_updated', 'xxApiCalls_user_updated_listener');

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'xxApiCalls', 'profile_xxApiCalls_item_count', 1);

    return true;
}

/**
 * Get information about an item for FoxyCart
 * @param $_data array incoming data array from jrCore_save_media_testing()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function xxApiCalls_my_items_row_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['purchase_module']) && $_args['purchase_module'] == 'xxApiCalls') {
        $url = jrCore_get_module_url('xxApiCalls');

        $_data[2]['title'] = $_args['testing_title'];
        $_data[5]['title'] = jrCore_page_button("a{$_args['_item_id']}", 'download', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/vault_download/testing_file/{$_args['_item_id']}')");
    }
    return $_data;
}

/**
 * display the sale info to the seller of the item for FoxyCart
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function xxApiCalls_my_earnings_row_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['purchase_module']) && $_args['purchase_module'] == 'xxApiCalls') {
        $_data[1]['title'] = $_args['testing_title'];
    }
    return $_data;
}

/**
 * fired when foxycart sends a confirmed order in the background back to our system.
 * If there are files in the order, those files need to be kept in the system vault
 * so they can be downloaded.  do that moving here.
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return mixed
 */
function xxApiCalls_adding_item_to_purchase_history_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'xxApiCalls') {
        // a file has been sold, copy it to our system vault.
        // Make sure file is copied over to system vault
        $nam = jrCore_get_media_file_path($_args['module'], $_args['product_field'], $_data);
        if (!isset($nam) || !is_file($nam)) {
            // BAD FILE!
            jrCore_logger('CRI', "transaction received with no valid media file: {$_args['txn']['txn_id']}");
            return $_data;
        }
        $dir = APP_DIR . '/data/media/vault';
        $fil = $dir . '/' . basename($nam);
        if (!is_file($fil)) {
            if (!copy($nam, $fil)) {
                jrCore_logger('CRI', "unable to copy sold media file to system vault: {$_args['txn']['txn_id']}");
                return $_data;
            }
        }
    }
    return $_data;
}

function array2object($array) {

    if (is_array($array)) {
        $obj = new StdClass();

        foreach ($array as $key => $val){
            $obj->$key = $val;
        }
    }
    else { $obj = $array; }

    return $obj;
}

function object2array($object) {
    if (is_object($object)) {
        foreach ($object as $key => $value) {
            $array[$key] = $value;
        }
    }
    else {
        $array = $object;
    }
    return $array;
}

function xxApiCalls_user_signup_activated_listener($_data, $_user, $_conf, $_args, $event)
{
/*  if (isset($_data['user_rewards_member']) && ($_data['user_rewards_member'] == 'yes'))
    { */
        $upn = str_replace( ' ', '%20', $_data['profile_name']);
        $ueml = $_data['user_email'];
        $uid = $_data['_user_id'];
        $upwd = 123456789;
        $username = "Ahyy5qA6SAVNaGCB";
        $password = "080543ab44c3670a8e34dc4442c5bc9f";
        //Search the API datastore for duplicates of members
        $curl =curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://jr5.paradigmusic.com/api/data/users/search?search1=profile_name%20eq%20".$ueml);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
        $result = curl_exec($curl);
        curl_close($curl);
        $gr = json_decode($result);
        $ry = object2array($gr);
        // IF no users are found (code 404-not found)
        if ($ry['code'] == 404) {
            //Check if user is an Artist
            if (($_data['profile_quota_id'] <= 10)) {
                //Put user info into Datastore then Collection
                $curl_user_data = array(
                    'user_name' => $_data['user_name'],
                    'user_email' => $_data['user_email'],
                    'user_password' => $_data['user_password'],
                    'user_rewards_member' => $_data['user_rewards_member'],
                    'user_timezone' => $_data['user_timezone'],
                    'user_profile_active' => '0',
                    'user_affiliation_signup' => 'on',
                    'user_profile_quota_id' => $_data['profile_quota_id'],
                    'user_artist_pastsales'  => $_data['user_artist_pastsales'],
                    'user_artist_website'  => $_data['user_artist_website'],
                    'user_rewards_members'  => 'no'
                );
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, "https://jr5.paradigmusic.com/api/user?user_name=".$upn."&user_email=".$ueml."&password=".$upwd);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
                $result = curl_exec($curl);
                curl_close($curl);
                $dres = json_decode($result);
                $jarry = object2array($dres);
                $idea = object2array($jarry['data']);
                if ($idea && is_array($idea)) {
                    //Update the Info in the Datastore
                    $curl_data2 = array(
                        'user_name' => $_data['user_name'],
                        'user_email' => $_data['user_email'],
                        'user_rewards_member' => $_data['user_rewards_member'],
                        'user_timezone' => $_data['user_timezone'],
                        'user_affiliation_signup' => 'on',
                        'user_profile_quota_id' => $_data['profile_quota_id'],
                        'user_artist_pastsales'  => $_data['user_artist_pastsales'],
                        'user_artist_website'  => $_data['user_artist_website'],
                        'user_rewards_members'  => 'no'
                     );
                    $iid = $idea['_id'];
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, "https://jr5.paradigmusic.com/api/user/".$iid);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_user_data));
                    curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
                    $result = curl_exec($curl);
                    curl_close($curl);
                    //Create a new Object for Colletion in MR
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, "https://jr5.paradigmusic.com/api/data/users");
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_data2));
                    curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
                    $result = curl_exec($curl);
                    curl_close($curl);
                }
                // Adding Profile info for Quota change
                //Check if user is an Artist
                if (($_data['profile_quota_id'] == 2)) {
                    //Find _profile_id
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, "https://jr5.paradigmusic.com/api/bridge/profile/search?search1=_user_id%20eq%20" .$iid);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
            //        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_profile));
                    curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
                    $result1 = curl_exec($curl);
                    $pro = json_decode($result1);
                    $kid = object2array($pro);
                    $zid = object2array($kid['data']);
                    $pid = object2array($zid['_items']['0']);
                    if ($pid && is_array($pid))
                        curl_close($curl);
                    $_proid = $pid['_profile_id'];
                    $curl_profile = array(
                        'profile_quota_id' => '2'
                    );
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, "https://jr5.paradigmusic.com/api/bridge/profile/" . $_proid);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_profile));
                    curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
                    $result = curl_exec($curl);
                    curl_close($curl);
                }
                // Add the User info to AffiliateUserMatrix also
                // Affiliate User Authentication
                $aumuser = "VqkN2j8AL5Z3teiX";
                $aumpass = "d490b61ad81f28f646fcfb6ff5eeb5da";
                // Affiliate Information
                $_data['aff_id'] = '101';
                $_data['aff_name'] = 'RhythmGlobe.com';
              $aum = array(
                    'affiliateusermatrix_name' => $_data['user_name'],
                    'affiliateusermatrix_email' => $_data['user_email'],
                 //   'affiliateusermatrix_password' => $_data['user_password'],
                 //   'affiliateusermatrix_rewards_member' => $_data['user_rewards_member'],
                    'affiliateusermatrix_timezone' => $_data['user_timezone'],
                 //   'affiliateusermatrix_bio' => $_data['profile_bio'],
                    'affiliateusermatrix_city' => $_data['profile_city'],
                    'affiliateusermatrix_country' => $_data['profile_country'],
                  //  'affiliateusermatrix_genre' => $_data['profile_genre'],
                  //  'affiliateusermatrix_quota_id' => $_data['profile_quota_id'],
                    'affiliateusermatrix_state' => $_data['profile_state'],
                  //  'affiliateusermatrix_url' => $_data['profile_url'],
                    'affiliateusermatrix_zipcode' => $_data['profile_zipcode'],
                    'affiliateusermatrix_affiliate_user_id' => $_data['_user_id'],
                    'affiliateusermatrix_affiliate_name' => $_data['aff_name'],
                    'affiliateusermatrix_affiliate_id' => $_data['aff_id']
                );
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, "https://jr5.paradigmusic.com/api/bridge/affiliateusermatrix");
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($aum));
                curl_setopt($curl, CURLOPT_USERPWD, $aumuser . ":" . $aumpass);
                $result = curl_exec($curl);
                curl_close($curl);
            }
        }

    return $_data;
}

function xxApiCalls_user_updated_listener($_data, $_user, $_conf, $_args, $event)
{
    //Check if user is an Artist/Fan and a rewards member
    if (($_data['profile_quota_id'] < 10) && ($_data['user_rewards_member'] == 'yes')) {
//Update the Info in the Datastore
        $username = "Ahyy5qA6SAVNaGCB";
        $password = "080543ab44c3670a8e34dc4442c5bc9f";
        $curl_data2 = array(
            'user_name' => $_data['user_name'],
            'user_email' => $_data['user_email'],
            'user_password' => $_data['user_password'],
            'user_rewards_member' => $_data['user_rewards_member'],
            'user_timezone' => $_data['user_timezone'],
            'user_affiliation_signup' => 'on',
            'user_profile_quota_id' => $_data['profile_quota_id'],
            'user_artist_pastsales'  => $_data['user_artist_pastsales'],
            'user_artist_website'  => $_data['user_artist_website'],
            'user_rewards_members'  => 'no'
        );
        $iid = $_data['_item_id'];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://jr5.paradigmusic.com/api/user/".$iid);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_data2));
        curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
        $result = curl_exec($curl);
        curl_close($curl);
    }
    return $_data;
}

function xxApiCalls_register_entry_listener($_data, $_user, $_conf, $_args, $event)
{
    //Activated when a Fan buys an Item
    if (isset($_data['r_purchase_user_id'])) {
    // Affiliate Information
            $_data['aff_id'] = '101';
            $_data['aff_name'] = 'RhythmGlobe.com';
//Update the Info in the Datastore
        $username = "ZaNPNdsVpc1mJ4Nw";
        $password = "07addf329bcd7ebfb3cacc8fc0b7ff7f";
        $curl_data2 = array(
                'affiliatesalesmatrix_title'           => $_data['r_created'],
                'affiliatesalesmatrix_created'           => $_data['r_created'],
                'affiliatesalesmatrix_purchase_user_id'  => $_data['r_purchase_user_id'],
                'affiliatesalesmatrix_seller_profile_id' => $_data['r_seller_profile_id'],
                'affiliatesalesmatrix_item_id'           => $_data['r_item'],
                'affiliatesalesmatrix_field'             => $_data['r_field'],
                'affiliatesalesmatrix_quantity'          => $_data['r_quantity'],
                'affiliatesalesmatrix_amount'            => $_data['r_amount'],
                'affiliatesalesmatrix_affiliate_id'      => $_data['aff_id']
    );
 //       $iid = $_data['_item_id'];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://jr5.paradigmusic.com/api/bridge/affiliatesales");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_data2));
        curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
        $result = curl_exec($curl);
        curl_close($curl);
    }
    return $_data;
}