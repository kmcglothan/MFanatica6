<?php
/**
 * Created by PhpStorm.
 * User: KevinM
 * Date: 10/9/17
 * Time: 2:49 PM
 */
// make sure we are not being called directly
//defined('APP_DIR') or exit();

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/database.php';
include_once '../objects/user.php';

// instantiate database and product object
$database = new Database();
$db = $database->getConnection();

// initialize object
$user = new User($db);

// query products
$stmt = $user->read();
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){

    // user array
    $user_arr=array();
    $user_arr["records"]=array();

    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);

        $user_item=array(
            "cookie_id" => $cookie_id,
            "cookie_time" => $cookie_time,
 /*           "profile_id" => $_profile_id,
            "username" => $user_name,
            "useremail" => $user_email,
            "usertzone" => $user_timezone,
            "usergroup" => $user_group,
            "userrmember" => $user_rewards_member
*/        );

        array_push($user_arr["records"], $user_item);
    }

    echo json_encode($user_arr);
}

else{
    echo json_encode(
        array("message" => "No products found.")
    );
}
?>