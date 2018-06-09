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
 * @copyright 2003 - 2015 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Share a message to configured OneAll systems
 * @param $_queue array Queue entry
 * @return bool
 */
function plugin_jrOneAll_share_simple($_queue)
{
    global $_conf;
    // Create our OneAll JSON body for posting
    $_json = array(
        'request' => array(
            'message' => array(
                'providers' => explode(',', $_queue['providers']),
                'parts'     => array()
            )
        )
    );

    // Check for listeners
    // NOTE: We are not going to do the normal "broadcast" event trigger
    // here since we know which module we are sharing for - instead we
    // can just load up that module's listener function
    $_tmp = jrCore_get_event_listeners('jrOneAll', 'network_share_text');

    // [jrOneAll_network_share_text] => Array
    // (
    //     [0] => jrAudio_network_share_text_listener
    // )

    // We are going to see if we have a listener for this module
    $_us = array();
    if ($_tmp && is_array($_tmp)) {
        if (isset($_queue['action_data']) && is_array($_queue['action_data'])) {
            $_queue['action_data'] = json_encode($_queue['action_data']);
        }
        if (isset($_queue['user_id']) && jrCore_checktype($_queue['user_id'], 'number_nz')) {
            $_us = jrCore_db_get_item('jrUser', $_queue['user_id']);
        }
        foreach ($_tmp as $listener) {
            if (strpos($listener, $_queue['action_module']) === 0) {
                $_txt = $listener($_queue, $_us, $_conf, $_json, 'network_share_text');
                if ($_txt && is_array($_txt) && isset($_txt['text'])) {
                    $_json['request']['message']['parts']['text'] = array(
                        'body' => $_txt['text']
                    );
                    // Check for additional options
                    foreach (array('picture', 'link') as $tag) {
                        if (isset($_txt[$tag])) {
                            $_json['request']['message']['parts'][$tag] = $_txt[$tag];
                        }
                    }
                }
            }
        }
    }

    // Check for action text
    if ((!isset($_json['request']['message']['parts']['text']) || strlen($_json['request']['message']['parts']['text']['body']) === 0) && isset($_queue['action_text']) && strlen($_queue['action_text']) > 0) {
        $_json['request']['message']['parts']['text'] = array(
            'body' => jrCore_strip_html($_queue['action_text'])
        );
        // link: url, name, caption, description
        if ($_it = jrCore_db_get_item($_queue['action_module'], $_queue['item_id'])) {
            $pfx = jrCore_db_get_prefix($_queue['action_module']);
            $url = jrCore_get_module_url('jrAction');
            $ttl = '';
            if (isset($_it["{$pfx}_title_url"])) {
                $ttl = '/' . $_it["{$pfx}_title_url"];
            }
            $_json['request']['message']['parts']['link'] = array(
                'url'     => "{$_conf['jrCore_base_url']}/{$_it['profile_url']}/{$url}/{$_queue['item_id']}{$ttl}",
                'name'    => "@{$_it['profile_url']}",
                'caption' => $_it['profile_name']
            );
        }
        else {
            // Could not get item...
            return true;
        }
    }
    if (!isset($_json['request']['message']['parts']['text'])) {
        // No text to share
        return true;
    }

    // Fire off call to OneAll and get our result
    $_conf['jrOneAll_domain'] = str_replace('http:', 'https:', rtrim(trim($_conf['jrOneAll_domain']), '/'));

    $res = jrCore_load_url("{$_conf['jrOneAll_domain']}/users/{$_queue['user_token']}/publish.json", json_encode($_json), 'POST', 443, $_conf['jrOneAll_public_key'], $_conf['jrOneAll_private_key']);
    $_rs = json_decode($res, true);

    switch ($_rs['response']['request']['status']['code']) {
        case '200':
            // Post is good!
            break;
        case '207':
        case '400':
            // Some parts are NOT good - i.e. we posted OK to some networks, but some failed.  We need
            // to add these errors to an error log the user can see in their sharing page
            if (isset($_rs['response']['result']['data']['message']['publications'])) {
                $_pub = $_rs['response']['result']['data']['message']['publications'];
                if (is_array($_pub)) {
                    $tbl = jrCore_db_table_name('jrOneAll', 'link');
                    foreach ($_pub as $_ent) {
                        if (isset($_ent['status']['code']) && $_ent['status']['code'] != 200) {
                            $prv = jrCore_db_escape($_ent['provider']);
                            $msg = jrCore_db_escape($_ent['status']['message']);
                            $req = "UPDATE {$tbl} SET `error` = '{$msg}' WHERE user_id = '{$_queue['user_id']}' AND provider = '{$prv}' LIMIT 1";
                            jrCore_db_query($req);
                        }
                    }
                }
            }
            break;
        default:
            $_rs = $_rs['response']['request']['status'];
            jrCore_logger('MAJ', "jrOneAll error {$_rs['code']} sharing to {$_queue['providers']}: {$_rs['info']}");
            break;
    }
    return true;
}

/*
{
    "response": {
        "request": {
            "date": "Wed, 26 Nov 2014 22:55:04 +0100",
            "resource": "/users/xxxxxxxxxxxxxxxxxxxxxxxxxxxx/publish.json",
            "status": {
                "flag": "error",
                "code": 400,
                "info": "Your request could not be processed due to an error"
            }
        },
        "result": {
            "data": {
                "message": {
                    "sharing_message_token": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
                    "date_creation": "Wed, 26 Nov 2014 22:55:04 +0100",
                    "publications": [ {
                        "provider": "facebook",
                        "user_token": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
                        "identity_token": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
                        "status": {
                            "flag": "error_message_is_not_permitted",
                            "code": 403,
                            "message": "The provider did not accept the message: you do not have the permission to post on behalf of the user"
                        }
                    } ]
                }
            }
        }
    }
}
*/
