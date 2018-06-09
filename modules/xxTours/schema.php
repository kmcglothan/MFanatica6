<?php
/**
 * Paradigmusic Tours module
 *
*/
// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * xxTours_db_schema
 */
function xxTours_db_schema()
{
    // This module uses a Data Store - create it.
    jrCore_db_create_datastore('xxTours','tours');


    // Tour dates
    $_tmp = array(
        "_item_id int(10) UNSIGNED NOT NULL",
        "_profile_id int(11) DEFAULT NULL",
        "band_name varchar (128) DEFAULT NULL",
        "tourdate_number int(11) DEFAULT NULL",
        "tour_title varchar(128) DEFAULT NULL",
        "venue varchar(512) DEFAULT NULL",
        "venue_address varchar(128) DEFAULT NULL",
        "datetime int(11) DEFAULT NULL",
        "tours_id int(10) UNSIGNED DEFAULT NULL",
        "prices float(8,2) DEFAULT NULL",
        "rewards_member_discount float(8,2) DEFAULT NULL",
        "sales_limits int(11) DEFAULT NULL",
         "ticket_limits int(11) DEFAULT NULL",
         "pre_release_date int(11) DEFAULT NULL",
         "general_release_date int(11) DEFAULT NULL",
        "lat float DEFAULT NULL",
        "lng float DEFAULT NULL",
        "icon varchar(128) DEFAULT NULL",
         "tour_show char(3) DEFAULT 'yes'",
    );
    jrCore_db_verify_table('xxTours', 'tour_date', $_tmp, 'InnoDB');
    return true;
}
?>
