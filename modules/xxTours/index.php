<?php
/**
 * Jamroom 5 Tours module
 *
 * copyright 2003 - 2015
 * by The Jamroom Network
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
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

//--------- Gets LAT and LONG from Address

function getLocation($address)
{
//	header('Content-Type: text/xml');
    define("MAPS_HOST", "maps.googleapis.com");
    define("KEY", "AIzaSyAx6w-i5Ssa5eZ1fNgslsUND-UaSLkH6Uc");

    // google map gecoder
    // Initialize delay in geocode speed
    $delay = 0;
    $base_url = "https://" . MAPS_HOST . "/maps/api/geocode/xml?";
    //$base_url = "https://" . MAPS_HOST . "/maps/embed/v1/place" . "?key=" . KEY;
    $geocode_pending = true;
    $results = array();
    while ($geocode_pending) {
        $address = $address;
		if(strlen($address)>0) {
        $prerequest_url = $base_url . "address=" . urlencode($address);
        $request_url = $prerequest_url . "&sensor=false";
        $xml = simplexml_load_file($request_url) or die("url not loading");
        $status = $xml->status;
        if (strcmp($status, "OK") == 0) {
            // Successful geocode
            $geocode_pending = false;
            //$coordinates = $xml->Response->Placemark->Point->coordinates;
            //$coordinatesSplit = split(",", $coordinates);
            // Format: Longitude, Latitude, Altitude
            //$lat = $coordinatesSplit[1];
            //$lng = $coordinatesSplit[0];
            $lat = $xml->result->geometry->location->lat;
            $lng = $xml->result->geometry->location->lng;
            $results["lat"] = $lat;
            $results["lng"] = $lng;
			} else {
   jrCore_notice_page('error','Invalid address');
}
        }else if (strcmp($status, "620") == 0) {
            // sent geocodes too fast
            $delay += 100000;
        } else {
            // failure to geocode
            $geocode_pending = false;
            $msg = "Address " . $address . " failed to geocoded. <br/>";
            $msg .= "Received status " . $status . "\n";
            echo $msg;
            exit;
            return false;

        }
        usleep($delay);
    }

    return $results;
}

//------------------------------
// create
//------------------------------
function view_xxTours_create($_post, $_user, $_conf)
{
    // Must be logged in to create a new tours
    jrUser_session_require_login();
    jrUser_check_quota_access('xxTours');
    jrProfile_check_disk_usage();


    // Get language strings
    $_lang = jrUser_load_lang_strings();

    // Start our create form
    $_sr = array(
        "_profile_id = {$_user['user_active_profile_id']}",
    );
    $tmp = jrCore_page_banner_item_jumper('xxTours', 'tours_title', $_sr, 'create', 'update');
    jrCore_page_banner($_lang['xxTours'][2], $tmp);

    $_cal['item_id']   =  jrCore_clean_html($_post['id']);
    $_cal['tours_title'] = jrCore_clean_html($_post['tt']);
    $_cal['tours_number'] = jrCore_clean_html($_post['tn']);
    $_cal['band_name'] = jrCore_clean_html($_post['bn']);
    if (strlen($_cal['e']) > 0) {
        switch ($_cal['e']) {
            case 'date_error':
                jrCore_notice_page('error', 13);
                break;
            case 'time_error':
                jrCore_notice_page('error', 14);
                break;
            case 'title_error':
                jrCore_notice_page('error', 15);
                break;
            case 'gmf_error':
                jrCore_notice_page('error', $_cal['e_text']);
                break;
        }
        unset($_cal['e'], $_cal['e_text']);
    }

    // Form init
    $_tmp = array(
        'submit_value' => 2,
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    //These forms stay out of the loop

    // Tours id (item id)
    $_tmp = array(
        'name'       => 'tours_id',
        'label'      => 'tours id',
        'value'       => $_cal['item_id'],
        'type'       => 'hidden',
        'onkeypress' => "if (event && event.keyCode == 13) return false;"
    );
    jrCore_form_field_create($_tmp);

    // Tours number
    $_tmp = array(
        'name'       => 'tours_number',
        'label'      => 'tours Number',
        'value'       => $_cal['tours_number'],
        'type'       => 'hidden',
        'onkeypress' => "if (event && event.keyCode == 13) return false;"
    );
    jrCore_form_field_create($_tmp);

    //loop how many tour there are to make the
    $total_tours = $_cal["tours_number"];
    $k=0;
    for($i=1; $i<=$total_tours; $i++) {
        // Counter for form order
        ++$k;

    // Tours Title
    $_tmp = array(
        'name'       => 'tours_title',
        'label'      => 3,
        'help'       => 4,
        'value'       => $_cal['tours_title'],
        'type'       => 'hidden',
        'ban_check'  => 'word',
        'validate'   => 'printable',
        'required'   => true,
        'onkeypress' => "if (event && event.keyCode == 13) return false;"
    );
    jrCore_form_field_create($_tmp);

        // Band Name
        $_tmp = array(
            'name'       => 'band_name',
            'label'      => 3,
            'help'       => 4,
            'value'       => $_cal['band_name'],
            'type'       => 'hidden',
            'ban_check'  => 'word',
            'validate'   => 'printable',
            'required'   => true,
            'onkeypress' => "if (event && event.keyCode == 13) return false;"
        );
        jrCore_form_field_create($_tmp);

//   jrCore_page_link_cell("Tour No:", "" . ($i));
        // Tours number
        $_tmp = array(
            'name' => 'tourdate_number_'. $i,
            'label' => 'tourdate number '. $i,
            'value' => $i,
            'type' => 'text',
            'readonly' => true,
            'validate' => 'number_nz',
            'order' => ($k),
            'onkeypress' => "if (event && event.keyCode == 13) return false;"
        );
        jrCore_form_field_create($_tmp);

        // Tours Venue
        $_tmp = array(
            'name' => 'venue_'. $i,
            'label' => 'venue ' . $i,
            'help' => 4,
            'type' => 'text',
            'ban_check' => 'word',
            'validate' => 'printable',
            'required' => true,
            'order' => (++$k),
            'onkeypress' => "if (event && event.keyCode == 13) return false;"
        );
        jrCore_form_field_create($_tmp);

        // Tours Venue address
        $_tmp = array(
            'name' => 'venue_address_'. $i,
            'label' => 'venue address ' .$i,
            'sublabel' => 'Address,City,State (use commas!)',
            'help' => 'Address, City and State',
            'type' => 'text',
            'ban_check' => 'word',
            'validate' => 'printable',
        //    'required' => true,
            'order' => (++$k),
            'onkeypress' => "if (event && event.keyCode == 13) return false;"
        );
        jrCore_form_field_create($_tmp);


        // Tours Date
        $_tmp = array(
            'name' => 'datetime_'. $i,
            'label' => 'tour date/time ' . $i,
            'help' => 4,
            'type' => 'datetime',
            'validate' => 'date',
            'required' => true,
            'order' => (++$k),
            'onkeypress' => "if (event && event.keyCode == 13) return false;"
        );
        jrCore_form_field_create($_tmp);

        // Tours Pre-release
        $_tmp = array(
            'name' => 'pre_release_date_'. $i,
            'label' => 'pre-release date/time ' . $i,
            'help' => 4,
            'type' => 'datetime',
            'validate' => 'date',
            'required' => true,
            'order' => (++$k),
            'onkeypress' => "if (event && event.keyCode == 13) return false;"
        );
        jrCore_form_field_create($_tmp);


        // Tours General release
        $_tmp = array(
            'name' => 'general_release_date_'. $i,
            'label' => 'general release date/time ' . $i,
            'help' => 4,
            'type' => 'datetime',
            'validate' => 'date',
            'required' => true,
            'order' => (++$k),
            'onkeypress' => "if (event && event.keyCode == 13) return false;"
        );
        jrCore_form_field_create($_tmp);


        // Tour show
        $_tmp = array(
            'name' => 'tour_show_'. $i,
            'label' => 'tour show ' . $i,
            'help' => 'Show this tour date',
            'type' => 'checkbox',
            'default'  => 'on',
            'validate' => 'onoff',
            'order' => (++$k),
            'onkeypress' => "if (event && event.keyCode == 13) return false;"
        );
        jrCore_form_field_create($_tmp);
    }

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_xxTours_create_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_form_validate($_post);
    jrUser_check_quota_access('xxTours');

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('xxTours', 'create', $_post);

    //Get Lat and Long with the Addresses array
    $tour_detail_location = array();
    ksort($_rt);
    //loop how many tour there are to make the
    $total_tours = $_rt['tours_number'];
    for($i=1; $i<=$total_tours; $i++)
   {
    $_kpj = $_post['venue_address_'. $i];
    $tour_detail_location = getLocation($_kpj);
        $ka = 'lat_'. $i;
        $kn = 'lng_'. $i;
        $lat = $tour_detail_location['lat'][0];
        $lng = $tour_detail_location['lng'][0];
        $_rt[$ka] = $lat;
        $_rt[$kn] = $lng;

   //Getting variables here
       $tourdate_number = jrCore_db_escape($_post['tourdate_number_'. $i]);
       $venue = jrCore_db_escape($_post['venue_'. $i]);
       $venue_address = jrCore_db_escape($_post['venue_address_'. $i]);
       $datetime = jrCore_db_escape($_post['datetime_'. $i]);
       $tour_show = jrCore_db_escape($_post['tour_show_'. $i]);
       $pre_release = jrCore_db_escape($_post['pre_release_date_'. $i]);
       $general_release = jrCore_db_escape($_post['general_release_date_'. $i]);

    // $xid will be the INSERT_ID (_item_id) of the created item
//   $xid = jrCore_db_create_item('xxTours', $_post);
//    if (!$xid) {
//        jrCore_set_form_notice('error', 5);
//        jrCore_form_result();
//    }
    $tbl = jrCore_db_table_name('xxTours', 'tour_date');
    $req = "INSERT INTO {$tbl} (_profile_id, band_name, tourdate_number, tour_title, venue, venue_address, datetime, tours_id, pre_release_date, general_release_date, lat, lng, tour_show) VALUES ('{$_user['user_active_profile_id']}', '{$_post['band_name']}', '{$tourdate_number}', '{$_post['tour_title']}','{$venue}', '{$venue_address}', '{$datetime}', '{$_post['tours_id']}', '{$pre_release}', '{$general_release}', '{$_rt[$ka]}', '{$_rt[$kn]}', '{$tour_show}')";
    $qid = jrCore_db_query($req, 'INSERT_ID');
}
    // Save any uploaded media files added in by our
//    jrCore_save_all_media_files('xxTours', 'create', $_user['user_active_profile_id'], $xid);

    // Add to Actions...
   // jrCore_run_module_function('jrAction_save', 'create', 'xxTours', $xid);
    
    jrCore_form_delete_session();
    jrProfile_reset_cache();

    // redirect to the actual tours page, not the update page.
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}");
}

//------------------------------
// update
//------------------------------
function view_xxTours_update($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('xxTours');

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 6);
    }
    $_rt = jrCore_db_get_item('xxTours', $_post['id']);
    if (!$_rt) {
        jrCore_notice_page('error', 7);
    }
    $tbl = jrCore_db_table_name('xxTours', 'tour_date');
    $_kt = "SELECT * FROM {$tbl} WHERE tours_id = '{$_post['id']}'";
    $_zt = jrCore_db_query($_kt, 'NUMERIC');

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Start output
    $_sr = array(
        "_profile_id = {$_user['user_active_profile_id']}",
    );
    $tmp = jrCore_page_banner_item_jumper('xxTours', 'tours_title', $_sr, 'create', 'update');
    jrCore_page_banner(8, $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 9,
        'cancel'       => jrCore_is_profile_referrer(),
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // id
    $_tmp = array(
        'name'     => 'id',
        'type'     => 'hidden',
        'value'    => $_post['id'],
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    // Order loop begin
     $k=1;

    // Tours Title
    $_tmp = array(
        'name'      => 'tours_title',
        'label'     => 3,
        'help'      => 4,
        'type'      => 'text',
        'ban_check' => 'word',
        'order'      => ($k),
        'validate'  => 'printable',
        'required'  => true
    );
    jrCore_form_field_create($_tmp);

    // Events and Tours Description
    $_tmp = array(
        'name'       => 'tours_desc',
        'label'      => 'Tours Description',
        'help'       => 'Put in a description of the upcoming tour',
        'type'       => 'textarea',
        'ban_check'  => 'word',
        'order'      => (++$k),
        'validate'   => 'printable',
        'onkeypress' => "if (event && event.keyCode == 13) return false;"
    );
    jrCore_form_field_create($_tmp);

    // Number of tours
    $_tmp = array(
        'name'       => 'tours_number',
        'label'      => 'Number of tour dates',
        'help'       => 'Put the number of tour dates here',
        'type'       => 'text',
        'order'      => (++$k),
        'validate' => 'number_nz',
        'required'   => true,
        'onkeypress' => "if (event && event.keyCode == 13) return false;"
    );
    jrCore_form_field_create($_tmp);

    // Show tour
    $_tmp = array(
        'name'       => 'tours_supporting_act',
        'label'      => 'Tour Supporting Act',
        'help'       => 4,
        'type'       => 'text',
        'order'      => (++$k),
        'ban_check'  => 'word',
        'validate'   => 'printable',
        'onkeypress' => "if (event && event.keyCode == 13) return false;"
    );
    jrCore_form_field_create($_tmp);

    //loop how many tour there are to make the
//    $total_tours = $_rt["tours_number"];
     $i=0;
    if ($_zt && is_array($_zt)) {
        foreach ($_zt as $row) {
            $i++;

            // Counter for form order
            ++$k;
//   jrCore_page_link_cell("Tour No:", "" . ($i));
            // Tours number
            $_tmp = array(
                'name' => 'tourdate_number_' . $i,
                'label' => 'tourdate number ',
                'value' => $i,
                'help' => 4,
                'type' => 'text',
                'readonly' => true,
                'required' => true,
                'order' => ($k),
                'onkeypress' => "if (event && event.keyCode == 13) return false;"
            );
            jrCore_form_field_create($_tmp);

            // Tours Venue
            $_tmp = array(
                'name' => 'venue_'. $i,
                'label' => 'venue ' . $i,
                'help' => 4,
                'type' => 'text',
                'value' => $row['venue'],
                'ban_check' => 'word',
                'validate' => 'printable',
                'required' => true,
                'order' => (++$k),
                'onkeypress' => "if (event && event.keyCode == 13) return false;"
            );
            jrCore_form_field_create($_tmp);

            // Tours Venue address
            $_tmp = array(
                'name' => 'venue_address_' . $i,
                'label' => 'venue address ' . $i,
                'sublabel' => 'Address,City,State (use commas!)',
                'help' => 'Address, City and State',
                'value' => $row['venue_address'],
                'type' => 'text',
                'ban_check' => 'word',
                'validate' => 'printable',
                //    'required' => true,
                'order' => (++$k),
                'onkeypress' => "if (event && event.keyCode == 13) return false;"
            );
            jrCore_form_field_create($_tmp);


            // Tours Date
            $_tmp = array(
                'name' => 'datetime_' . $i,
                'label' => 'tour date/time ' . $i,
                'help' => 4,
                'value' => $row['datetime'],
                'type' => 'datetime',
                'validate' => 'date',
                'required' => true,
                'order' => (++$k),
                'onkeypress' => "if (event && event.keyCode == 13) return false;"
            );
            jrCore_form_field_create($_tmp);


            // Tours Pre-release
            $_tmp = array(
                'name' => 'pre_release_date_' . $i,
                'label' => 'pre-release date/time ' . $i,
                'help' => 4,
                'value' => $row['pre_release_date'],
                'type' => 'datetime',
                'validate' => 'date',
                'required' => true,
                'order' => (++$k),
                'onkeypress' => "if (event && event.keyCode == 13) return false;"
            );
            jrCore_form_field_create($_tmp);


            // Tours General release
            $_tmp = array(
                'name' => 'general_release_date_' . $i,
                'label' => 'general release date/time ' . $i,
                'help' => 4,
                'value' => $row['general_release_date'],
                'type' => 'datetime',
                'validate' => 'date',
                'required' => true,
                'order' => (++$k),
                'onkeypress' => "if (event && event.keyCode == 13) return false;"
            );
            jrCore_form_field_create($_tmp);


            // Tour show
            $_tmp = array(
                'name' => 'tour_show_' . $i,
                'label' => 'tour show ' . $i,
                'help' => 'Show this tour date',
                'value' => $row['tour_show'],
                'type' => 'checkbox',
                'default' => 'on',
                'validate' => 'onoff',
                'order' => (++$k),
                'onkeypress' => "if (event && event.keyCode == 13) return false;"
            );
            jrCore_form_field_create($_tmp);
        }
    }
    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_xxTours_update_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();

    // Validate all incoming posted data
    jrCore_form_validate($_post);
    jrUser_check_quota_access('xxTours');

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 6);
        jrCore_form_result('referrer');
    }

    // Get data
    $_rt = jrCore_db_get_item('xxTours', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_notice_page('error', 7);
        jrCore_form_result('referrer');
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // IF tour number has changed then we reload page to add the additional forms and update the datastore
    // Throw up error if user puts in lower number than existing tour number
    if($_rt['tours_number'] > $_post['tours_number']) {
        jrCore_notice_page('error', 'Please put a number HIGHER than the number of tour dates');
        jrCore_form_result('referrer');
    }
    if($_rt['tours_number'] < $_post['tours_number']) {
        //Get tour number differencial
        $_ktd = $_post['tours_number'] - $_rt['tours_number'];
        // Save all updated fields to the Data Store
        $_kg = array(
            'tours_number' => $_post['tours_number'],
        );
        jrCore_db_update_item('xxTours', $_post['id'], $_kg);
        // Inserting new rows for more tours
        $tbl = jrCore_db_table_name('xxTours', 'tour_date');
        $i = 1;
        while ($i <= $_ktd) {
            $_ki = ((int)$_rt['tours_number'] + $i);
            $_ko = "INSERT INTO {$tbl} (tourdate_number,tours_id) VALUES ({$_ki},{$_post['id']})";
            $_kq = jrCore_db_query($_ko, 'INSERT_ID');
            ++$i;
        }
            if ($_kq && jrCore_checktype($_kq, 'number_nz')) {
          //      jrCore_notice_page('notice', 'Please fill in the new tour dates below');
                jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/update/id={$_post['id']}");
            }
        }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('xxTours', 'update', $_post);

    //Get Lat and Long with the Addresses array
    $tour_detail_location = array();
    ksort($_sv);
    //loop how many tour there are to make the
    $total_tours = $_sv['tours_number'];
    for($i=1; $i<=$total_tours; $i++) {
       $_kpj = $_post['venue_address_' . $i];
        $tour_detail_location = getLocation($_kpj);
        $ka = 'tours_lat_' . $i;
        $kn = 'tours_lng_' . $i;
        $lat = $tour_detail_location['lat'][0];
        $lng = $tour_detail_location['lng'][0];
        $_sv[$ka] = $lat;
        $_sv[$kn] = $lng;

        //Getting variables here
        $tourdate_number = jrCore_db_escape($_post['tourdate_number_' . $i]);
        $venue = jrCore_db_escape($_post['venue_' . $i]);
        $venue_address = jrCore_db_escape($_post['venue_address_' . $i]);
        $datetime = jrCore_db_escape($_post['datetime_' . $i]);
        $tour_show = jrCore_db_escape($_post['tour_show_' . $i]);
        $pre_release = jrCore_db_escape($_post['pre_release_date_' . $i]);
        $general_release = jrCore_db_escape($_post['general_release_date_' . $i]);

        $tbl = jrCore_db_table_name('xxTours', 'tour_date');
        $req = "UPDATE {$tbl} SET `_profile_id` = '{$_user['user_active_profile_id']}', `tour_title` = '{$_post['tours_title']}', `venue` = '{$venue}', `venue_address` = '{$venue_address}' , `datetime` = '{$datetime}', `pre_release_date` = '{$pre_release}', `general_release_date` = '{$general_release}', `lat` = '{$_sv[$ka]}', `lng` = '{$_sv[$kn]}', `tour_show` = '{$tour_show}' WHERE tourdate_number = '{$tourdate_number}' AND tours_id = '{$_post['id']}'";
        jrCore_db_query($req);
    }

    // Add in our SEO URL names
    $_sv['tours_title_url'] = jrCore_url_string($_sv['tours_title']);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('xxTours', $_post['id'], $_sv);

    // Save any uploaded media file
    jrCore_save_all_media_files('xxTours', 'update', $_user['user_active_profile_id'], $_post['id']);

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'update', 'xxTours', $_post['id']);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/artist_tourmap?id={$_post['id']}");
}

//------------------------------
// delete
//------------------------------
function view_xxTours_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('xxTours');

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 6);
        jrCore_form_result('referrer');
    }
    $_rt = jrCore_db_get_item('xxTours', $_post['id']);

    // Make sure the calling user has permission to delete this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    //  Delete items from the tour dates
    $tbl = jrCore_db_table_name('xxTours', 'tour_date');
    $req = "DELETE FROM {$tbl} WHERE tours_id = '{$_post['id']}'";
            jrCore_db_query($req);
    
    // Delete item and any associated files
    jrCore_db_delete_item('xxTours', $_post['id']);
    jrProfile_reset_cache();
    jrCore_form_result('delete_referrer');
}

//----------------------------------------
// Creating the number of tours
//----------------------------------------
function view_xxTours_start($_post, $_user, $_conf)
{
    // Must be logged in to create a new tours
    jrUser_session_require_login();
    jrUser_check_quota_access('xxTours');
    jrProfile_check_disk_usage();

    // Get language strings
    $_lang = jrUser_load_lang_strings();
    // Start our create form
    $_sr = array(
        "_profile_id = {$_user['user_active_profile_id']}",
    );
    $tmp = jrCore_page_banner_item_jumper('xxTours', 'tours_title', $_sr, 'create', 'update');
    jrCore_page_banner($_lang['xxTours'][2], $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 'Next/Save',
        'action'       => 'start_save',
        'cancel'       => jrCore_is_profile_referrer(),
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // EventsTours Title
    $_tmp = array(
        'name'       => 'tours_title',
        'label'      => 3,
        'help'       => 4,
        'type'       => 'text',
        'ban_check'  => 'word',
        'validate'   => 'printable',
        'required'   => true,
        'onkeypress' => "if (event && event.keyCode == 13) return false;"
    );
    jrCore_form_field_create($_tmp);

    // Events and Tours Description
    $_tmp = array(
        'name'       => 'tours_desc',
        'label'      => 'Tours Description',
        'help'       => 'Put in a description of the upcoming tour',
        'type'       => 'textarea',
        'ban_check'  => 'word',
        'validate'   => 'printable',
        'onkeypress' => "if (event && event.keyCode == 13) return false;"
    );
    jrCore_form_field_create($_tmp);

    // Number of tours
    $_tmp = array(
        'name'       => 'tours_number',
        'label'      => 'Number of tour dates',
        'help'       => 'Put the number of tour dates here',
        'type'       => 'text',
        'validate' => 'number_nz',
        'required'   => true,
        'onkeypress' => "if (event && event.keyCode == 13) return false;"
    );
    jrCore_form_field_create($_tmp);

    // Supporting Act
    $_tmp = array(
        'name'       => 'tours_supporting_act',
        'label'      => 'Tour Supporting Act',
        'help'       => 4,
        'type'       => 'text',
        'ban_check'  => 'word',
        'validate'   => 'printable',
        'onkeypress' => "if (event && event.keyCode == 13) return false;"
    );
    jrCore_form_field_create($_tmp);

    // Tour Band
    $_tmp = array(
        'name'       => 'tours_band_name',
        'label'      => 'Tour Band Name',
        'type'       => 'hidden',
        'value'      =>  $_user['user_name'],
        'onkeypress' => "if (event && event.keyCode == 13) return false;"
    );
    jrCore_form_field_create($_tmp);

    // Tour added
    $_tmp = array(
        'name'       => 'tours_added',
        'label'      => 'Tour date time added',
        'value'      => 'UNIX_TIMESTAMP()',
        'type'       => 'hidden'
    );
    jrCore_form_field_create($_tmp);

// Display page with form in it
    jrCore_page_display();
}


//------------------------------
// start_save
//------------------------------
function view_xxTours_start_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_form_validate($_post);
    jrUser_check_quota_access('xxTours');

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('xxTours', 'start', $_post);

    // Add in our SEO URL names
    $_rt['tours_title_url'] = jrCore_url_string($_rt['tours_title']);

    //Get the Tour number for URL
    $tnum = $_rt['tours_number'];
    $ttit = jrCore_url_string($_rt['tours_title']);
    $bdnme = $_rt['tours_band_name'];

    // $xid will be the INSERT_ID (_item_id) of the created item
    $xid = jrCore_db_create_item('xxTours', $_rt);
    if (!$xid) {
        jrCore_set_form_notice('error', 5);
        jrCore_form_result();
    }

    // Save any uploaded media files added in by our
    jrCore_save_all_media_files('xxTours', 'start', $_user['user_active_profile_id'], $xid);

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'start', 'xxTours', $xid);

    //redirect to the create form to finish the tour creations

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/create/?id={$xid}&tn={$tnum}&bn={$bdnme}&tt={$ttit}");

    // redirect to the actual tours page, not the update page.
    //jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$xid}/{$_rt['tours_title_url']}");


}
