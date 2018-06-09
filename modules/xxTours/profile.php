<?php
// Paradigmusic Tour profile.php

// make sure we are not being called directly
defined('APP_DIR') or exit();

//------------------------------
// default
//------------------------------

function profile_view_xxTours_default($_profile, $_post, $_user, $_conf)
{

    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('xxTours');
    jrProfile_check_disk_usage();

    // Get language strings
    $_lang = jrUser_load_lang_strings();
    
    if (isset($_post['_1']) && jrCore_checktype($_post['_1'], 'number_nz')) {
        $_rp = array();
        $_rp['item'] = jrCore_db_get_item('xxTours', $_post['_1']);
        return jrCore_parse_template('item_detail.tpl', $_rp, 'xxTours');
    }

    else {
        $_sc = array(
            'search'  =>  array(
            "_user_id = {$_user['_user_id']}"
            )
        );
        $_rp = jrCore_db_search_items('xxTours', $_sc);
        $_rp = array_merge($_profile);
        return jrCore_parse_template('item_index.tpl', $_rp, 'xxTours');
    }
}
