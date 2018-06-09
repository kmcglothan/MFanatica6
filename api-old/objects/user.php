<?php
/**
 * Created by PhpStorm.
 * User: KevinM
 * Date: 10/9/17
 * Time: 1:21 PM
 */

class User{

    // database connection and table name
    private $conn;
    private $table_name = "jr_jruser_cookie";

    // object properties
    public $cookie_id;
    public $cookie_time;
/*    public $_profile_id;
    public $user_name;
    public $user_email;
    public $user_timezone;
    public $user_group;
    public $user_rewards_member;
*/
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
    // read users
    function read(){

        // select all query
        $query = "SELECT *
                
            FROM
                " . $this->table_name . " 
            ORDER BY
                user_name DESC";

/*        $params = array(
            'search' => array(
                '_user_id >= 1'
            ),
            'return_keys' => array(
                '_user_id',
                '_profile_id',
                'user_name',
                'user_email',
                'user_timezone',
                'user_group',
                'user_rewards_member'
            )
        );
      $query =  jrCore_db_search_items('jrUser', $params);
*/
        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        return $stmt;
    }
}