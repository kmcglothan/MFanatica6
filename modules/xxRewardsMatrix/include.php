<?php
/**
 * Paradigmusic Rewards Matrix
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function xxRewardsMatrix_meta()
{
    $_tmp = array(
        'name'        => 'Rewards Matrix',
        'url'         => 'rewardsmatrix',
        'version'     => '1.0.0',
        'developer'   => 'The Paradigmusic Network, &copy;' . strftime('%Y'),
        'description' => 'Assigns Users and Artists Rewards Matrix',
        'license'     => 'mpl',
        'category'    => 'custom'
    );
    return $_tmp;
}

/**
 * init
 */
function xxRewardsMatrix_init()
{
    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'xxRewardsMatrix', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'xxRewardsMatrix', 'update');

    // Core support
    jrCore_register_module_feature('jrCore', 'quota_support', 'xxRewardsMatrix', 'on');
    jrCore_register_module_feature('jrCore', 'pending_support', 'xxRewardsMatrix', 'on');
    jrCore_register_module_feature('jrCore', 'max_item_support', 'xxRewardsMatrix', 'on');
    jrCore_register_module_feature('jrCore', 'action_support', 'xxRewardsMatrix', 'create', 'item_action.tpl');
    jrCore_register_module_feature('jrCore', 'action_support', 'xxRewardsMatrix', 'update', 'item_action.tpl');

    // We have fields that can be searched
    jrCore_register_module_feature('jrSearch', 'search_fields', 'xxRewardsMatrix', 'rewardsmatrix_title', 1);

    // Integrate with payment system
    jrCore_register_event_listener('jrFoxyCart', 'my_items_row', 'xxRewardsMatrix_my_items_row_listener');
    jrCore_register_event_listener('jrFoxyCart', 'adding_item_to_purchase_history', 'xxRewardsMatrix_adding_item_to_purchase_history_listener');
    jrCore_register_event_listener('jrFoxyCart', 'my_earnings_row', 'xxRewardsMatrix_my_earnings_row_listener');

    // Integrate with the User Signup Activated system *Validates MR_member*
    jrCore_register_event_listener('jrUser', 'signup_activated', 'xxRewardsMatrix_signup_activated_listener');

    // Profile Stats
    jrCore_register_module_feature('jrProfile', 'profile_stats', 'xxRewardsMatrix', 'profile_xxRewardsMatrix_item_count', 1);
    
    //Rewards Matrix triggers
//    jrCore_register_event_trigger('xxRewardsMatrix', 'artist_matrix', 'Fired in the Rewards Matrix Artist Profile to allow modules to add data');
//    jrCore_register_event_trigger('xxRewardsMatrix',  'fan_matrix', 'Fired in the Rewards Matrix Fan Profile to allow modules to add data');
//    jrCore_register_event_trigger('xxRewardsMatrix', 'rewards_matrix_details', 'Fired in the Rewards Sales module to get payment details');
    return true;
}

/**
 * Get information about an item for FoxyCart
 * @param $_data array incoming data array from jrCore_save_media_rewardsmatrix()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function xxRewardsMatrix_my_items_row_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['purchase_module']) && $_args['purchase_module'] == 'xxRewardsMatrix') {
        $url = jrCore_get_module_url('xxRewardsMatrix');

        $_data[2]['title'] = $_args['rewardsmatrix_title'];
        $_data[5]['title'] = jrCore_page_button("a{$_args['_item_id']}", 'download', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/vault_download/rewardsmatrix_file/{$_args['_item_id']}')");
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
function xxRewardsMatrix_my_earnings_row_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['purchase_module']) && $_args['purchase_module'] == 'xxRewardsMatrix') {
        $_data[1]['title'] = $_args['rewardsmatrix_title'];
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
function xxRewardsMatrix_adding_item_to_purchase_history_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_args['module'] == 'xxRewardsMatrix') {
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

/**
 * Actives the Artist MR_membership
 * @param $_data array incoming data array from jrUser_signup_activated()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function xxRewardsMatrix_signup_activated_listener($_data, $_user, $_conf, $_args, $event)
{
    if  (isset($_data['user_artist_website']) || isset($_data['user_artist_concert_region']) || isset($_data['user_artist_pastsales']) && ($_data['profile_quota_id'] == '2')) {
        jrCore_db_update_item('jrUser', $_data['_item_id'], array('user_rewards_member'=>'yes'));
    }
    return $_data;
}
