<?php
/**
 * Jamroom System Core module
 *
 * copyright 2017 The Jamroom Network
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
 * @package MySQL
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Get total number of Rows in a MyISAM Table - estimate only for InnoDB
 * @param string $module Module to return count of
 * @param string $table Table to return count of
 * @return int Returns the number of rows in the table
 */
function jrCore_db_number_rows($module, $table)
{
    $tbl = jrCore_db_table_name($module, $table);
    $req = "SHOW TABLE STATUS LIKE '{$tbl}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && isset($_rt['Rows']) && is_numeric($_rt['Rows'])) {
        return intval($_rt['Rows']);
    }
    return 0;
}

/**
 * Check if a MySQL table exists
 * @param string $module Module to check table for
 * @param string $table Table to check
 * @return bool
 */
function jrCore_db_table_exists($module, $table)
{
    $key = "jrcore_db_table_exists";
    if (!$_tm = jrCore_get_flag($key)) {
        $_tm = array();
    }
    $tbl = jrCore_db_table_name($module, $table);
    if (isset($_tm[$tbl])) {
        return ($_tm[$tbl] == 1) ? true : false;
    }
    $req = "SELECT 1 FROM {$tbl} LIMIT 1";
    $res = jrCore_db_query($req, null, false, null, false, null, false, true);
    if ($res && is_object($res)) {
        $_tm[$tbl] = 1;
        jrCore_set_flag($key, $_tm);
        return true;
    }
    $_tm[$tbl] = 0;
    jrCore_set_flag($key, $_tm);
    return false;
}

/**
 * Return array of column definitions in a table
 * @param string $module Module to return column names for
 * @param string $table Table to return column names for
 * @return mixed Returns an array of column names/data
 */
function jrCore_db_table_columns($module, $table)
{
    $table = jrCore_db_table_name($module, $table);
    // See if we have already done this table
    $_uniq = jrCore_get_flag("jrcore_db_table_columns_{$table}");
    if ($_uniq) {
        return $_uniq;
    }
    $req = "DESCRIBE {$table}";
    $_rt = jrCore_db_query($req, 'NUMERIC', false, null, false, null, false);
    if ($_rt && is_array($_rt)) {
        $_tmp = array();
        foreach ($_rt as $_col) {
            $_tmp["{$_col['Field']}"] = $_col;
            if (isset($_col['Extra']) && strpos(' ' . $_col['Extra'], 'auto_increment')) {
                $_tmp['JRCORE_DB_TABLE_AUTO_INCREMENT_FIELD'] = $_col['Field'];
            }
        }
        jrCore_set_flag("jrcore_db_table_columns_{$table}", $_tmp);
        return $_tmp;
    }
    return false;
}

/**
 * Returns constructed table name for module/table
 * @param string $module Module Name
 * @param string $table Table Name
 * @return string Returns constructed table name
 */
function jrCore_db_table_name($module, $table)
{
    global $_conf;
    if (!isset($_conf['jrCore_db_prefix'])) {
        $_conf['jrCore_db_prefix'] = 'jr_';
    }
    return strtolower("{$_conf['jrCore_db_prefix']}{$module}_{$table}");
}

/**
 * Escape a string for DB insertion
 * @param mixed $data String or Array to have data escaped for MySQL insertion
 * @return mixed Returns String or Array ready for db insertion
 */
function jrCore_db_escape($data)
{
    if (is_array($data)) {
        foreach ($data as $key => $val) {
            $data[$key] = jrCore_db_escape($val);
        }
    }
    else {
        $temp = jrCore_db_connect();
        $data = mysqli_real_escape_string($temp, $data);
    }
    return $data;
}

/**
 * Connect to the MySQL database
 *
 * This function does not need to be called directly in Jamroom.
 * The jrCore_db_query() function will call it as needed if it sees that the
 * Jamroom Database has not been connected to.
 *
 * @param bool $force set to TRUE to force a new connection
 * @param bool $found_rows set to FALSE to disable MYSQLI_CLIENT_FOUND_ROWS flag on mysqli_real_connect()
 * @return mysqli Returns database resource on success, exits on failure
 */
function jrCore_db_connect($force = false, $found_rows = true)
{
    global $_conf;
    $tmp = jrCore_get_flag('jrcore_dbconnect_mysqli_object');
    if (!$force && $tmp) {
        return $tmp;
    }
    if ($force) {
        // Forcing new connection - close any previous connection
        jrCore_db_close();
    }
    $myi = mysqli_init();
    if (!$myi || !is_object($myi)) {
        jrCore_notice('Error', "unable to initialize database connection - check Error Log");
    }
    // See if we are using persistent connections
    $pfx = '';
    if (isset($_conf['jrCore_db_persistent']) && $_conf['jrCore_db_persistent'] == 'on') {
        $pfx = 'p:';
    }
    $flag = null;
    if ($found_rows) {
        $flag = MYSQLI_CLIENT_FOUND_ROWS;
    }
    // Multiple MySQL Masters?
    if (isset($_conf['jrCore_db_servers']) && is_array($_conf['jrCore_db_servers'])) {
        $idx = array_rand($_conf['jrCore_db_servers']);
        $host = $_conf['jrCore_db_servers'][$idx]['jrCore_db_host'];
        $user = $_conf['jrCore_db_servers'][$idx]['jrCore_db_user'];
        $pass = $_conf['jrCore_db_servers'][$idx]['jrCore_db_pass'];
        $name = $_conf['jrCore_db_servers'][$idx]['jrCore_db_name'];
        $port = $_conf['jrCore_db_servers'][$idx]['jrCore_db_port'];
    }
    else {
        $host = $_conf['jrCore_db_host'];
        $user = $_conf['jrCore_db_user'];
        $pass = $_conf['jrCore_db_pass'];
        $name = $_conf['jrCore_db_name'];
        $port = $_conf['jrCore_db_port'];
    }
    $tmp = @mysqli_real_connect($myi, "{$pfx}{$host}", $user, $pass, $name, $port, null, $flag);
    if (!$tmp) {
        // sleep for a bit and try again
        sleep(250000);
        $tmp = @mysqli_real_connect($myi, "{$pfx}{$host}", $user, $pass, $name, $port, null, $flag);
        if (!isset($tmp) || !$tmp) {
            jrCore_notice('Error', "error connecting to database server: " . mysqli_connect_error() . ' (#' . mysqli_connect_errno() . ') - contact hosting provider for assistance.');
        }
    }
    mysqli_set_charset($myi, 'utf8');
    jrCore_set_flag('jrcore_dbconnect_mysqli_object', $myi);

    // Allow sites to set their own SQL mode (or turn it off)
    if (isset($_conf['jrCore_sql_mode'])) {
        if (strlen($_conf['jrCore_sql_mode']) > 1) {
            jrCore_db_query("SET SESSION sql_mode = '{$_conf['jrCore_sql_mode']}'");
        }
    }
    else {
        jrCore_db_query("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION,STRICT_TRANS_TABLES'");
    }
    return $myi;
}

/**
 * Close the connection to the MySQL database
 *
 * <b>Note:</b> There is no need to call this function unless your script enters
 * a long processing segment where the database connection is no longer
 * needed, then it is a good idea to free the database resource.
 *
 * @return bool Returns true on success
 */
function jrCore_db_close()
{
    $tmp = jrCore_get_flag('jrcore_dbconnect_mysqli_object');
    if ($tmp) {
        if (mysqli_close($tmp)) {
            jrCore_delete_flag('jrcore_dbconnect_mysqli_object');
            return true;
        }
        return false;
    }
    return true;
}

/**
 * Send a Query to the MySQL database
 *
 * The jrCore_db_query function is the main function used to send MySQL queries to the database.
 *
 * Valid <b>$return</b> values are:
 *<br>
 * * (null)     - if empty or null, the Database connection resource is returned for raw processing<br>
 * * <b>NUMERIC</b>    - returns a multi-dimensional array, numerically indexed beginning at 0<br>
 * * <b>SINGLE</b>     - a single-dimension array is returned<br>
 * * <b>COUNT</b>      - the number of rows affected by an INSERT, DELETE or UPDATE query<br>
 * * <b>INSERT_ID</b>  - the id from the auto_increment column of the table from the last INSERT query<br>
 * * <b>NUM_ROWS</b>   - the number of rows returned from a SELECT query<br>
 * * <b>$column</b>    - if name of valid column given, will be used as key in associative array. NOTE: the $column used <b>must</b> be a column that is returned as part of the SELECT statement.<br>
 *<br>
 * @param string $query The MySQL query to send to the database
 * @param string $return format of the results you want returned from the query.
 * @param bool $multi Set to true if Query is a Transaction
 * @param string $only_val Set to a valid database table COLUMN name, then ONLY the value for that column will be returned (instead of an array of values)
 * @param bool $exit_on_error By default we exit when a database error is encountered
 * @param mysqli $con MySQL DB Connection object - default will create one to the JR DB
 * @param bool $log_error set to FALSE to prevent error logging
 * @param bool $skip_triggers set to TRUE to skip query triggers
 * @return mixed Returns multiple formats - see list of $return values above for details
 */
function jrCore_db_query($query, $return = null, $multi = false, $only_val = null, $exit_on_error = true, $con = null, $log_error = true, $skip_triggers = false)
{
    global $_post;
    // make sure we get a query
    if (!isset($query{1})) {
        return false;
    }
    if (!$con || is_null($con)) {
        $con = jrCore_db_connect();
    }

    // Trigger Init Event
    if (!$skip_triggers) {
        $query = jrCore_trigger_event('jrCore', 'db_query_init', $query, func_get_args());
    }

    // Some help to find this query if it ends up in a log
    $usr = '[visitor]';
    if (jrCore_get_flag('jrcore_logger_system_user_active')) {
        $usr = '[system]';
    }
    else {
        if (isset($_SESSION) && isset($_SESSION['user_name'])) {
            $usr = "[{$_SESSION['user_name']}]";
        }
    }
    $uri   = (isset($_REQUEST['_uri'])) ? $_REQUEST['_uri'] : '/';
    $query = "/* {$usr}:{$uri} */ {$query}";

    // If our $multi flag is true we use our multi_query function so multiple SQL
    // queries can be run in one shot - InnoDB tables only!
    if ($multi) {
        $res = mysqli_multi_query($con, $query) or $err = 'Query Error: ' . mysqli_error($con);
    }
    else {
        $res = mysqli_query($con, $query) or $err = 'Query Error: ' . mysqli_error($con);
    }
    if (isset($err{0})) {

        $exit = 1;
        if (!jrCore_get_flag('jrCore_db_query_recursive') && strpos($query, 'DESCRIBE') !== 0) {
            jrCore_set_flag('jrCore_db_query_recursive', 1);
            // See if this is a "MySQL Server has gone away error" - if it is, we try
            // to force a reconnect here and see if we can continue
            if (stripos($err, 'server has gone away') || stripos($err, 'deadlock found')) {
                unset($err);
                $con = jrCore_db_connect(true);
                if ($multi) {
                    $res = mysqli_multi_query($con, $query) or $err = 'Query Error: ' . mysqli_error($con);
                }
                else {
                    $res = mysqli_query($con, $query) or $err = 'Query Error: ' . mysqli_error($con);
                }
                if (isset($err{0})) {
                    jrCore_db_close();
                    if ($log_error) {
                        $_er = array(
                            '_post' => $_post,
                            'query' => substr($query, 0, 900000)
                        );
                        jrCore_logger('CRI', $err, $_er);
                        fdebug($_er); // OK
                    }
                }
                else {
                    $exit = 0;
                }
            }
            else {
                if ($log_error) {
                    $_er = array(
                        'error' => $err,
                        '_post' => $_post,
                        'query' => substr($query, 0, 900000)
                    );
                    jrCore_logger('CRI', $err, $_er);
                    fdebug($_er); // OK
                }
            }
        }
        if ($exit == 1) {
            if ($exit_on_error) {
                jrCore_notice('Error', $err);
            }
            // Trigger Exit Event
            if (!$skip_triggers) {
                jrCore_trigger_event('jrCore', 'db_query_exit', array('error' => $err), func_get_args());
            }
            return false;
        }
    }

    if (is_null($return) || $return === false) {
        if ($multi) {
            jrCore_db_close();
        }
        if (!$skip_triggers) {
            jrCore_trigger_event('jrCore', 'db_query_exit', array('result' => $res), func_get_args());
        }
        return $res;
    }
    switch ($return) {

        case 'SINGLE':
            $_tmp = mysqli_fetch_assoc($res);
            if ($multi) {
                jrCore_db_close();
            }
            if (!$skip_triggers) {
                jrCore_trigger_event('jrCore', 'db_query_exit', array('result' => $_tmp), func_get_args());
            }
            return $_tmp;
            break;

        case 'COUNT':
            $num = (int) mysqli_affected_rows($con);
            if ($multi) {
                jrCore_db_close();
            }
            if (!$skip_triggers) {
                jrCore_trigger_event('jrCore', 'db_query_exit', array('result' => $num), func_get_args());
            }
            return $num;
            break;

        case 'NUM_ROWS':
            $num = (int) mysqli_num_rows($res);
            if ($multi) {
                jrCore_db_close();
            }
            if (!$skip_triggers) {
                jrCore_trigger_event('jrCore', 'db_query_exit', array('result' => $num), func_get_args());
            }
            return $num;
            break;

        case 'INSERT_ID':
            $num = (int) mysqli_insert_id($con);
            if ($multi) {
                jrCore_db_close();
            }
            if (!$skip_triggers) {
                jrCore_trigger_event('jrCore', 'db_query_exit', array('result' => $num), func_get_args());
            }
            return $num;
            break;

        default:
            if ($res) {
                $num = mysqli_num_rows($res);
            }
            else {
                if (!$skip_triggers) {
                    jrCore_trigger_event('jrCore', 'db_query_exit', array('result' => null), func_get_args());
                }
                return false;
            }
            // more than 1 row - return multidimensional array that is
            // either numeric based (base 0) or associative based if $akey given
            if ($num >= 1) {
                $_rt = array();
                $i = 0;
                while ($row = mysqli_fetch_assoc($res)) {
                    if ($return == 'NUMERIC') {
                        $_rt[$i] = $row;
                        $i++;
                    }
                    elseif (isset($only_val{0})) {
                        $_rt["{$row[$return]}"] = $row[$only_val];
                    }
                    else {
                        $_rt["{$row[$return]}"] = $row;
                    }
                }
                mysqli_free_result($res);
                if ($multi) {
                    jrCore_db_close();
                }
                if (isset($_rt) && is_array($_rt) && count($_rt) > 0) {
                    if (!$skip_triggers) {
                        jrCore_trigger_event('jrCore', 'db_query_exit', array('result' => $_rt), func_get_args());
                    }
                    return $_rt;
                }
                if (!$skip_triggers) {
                    jrCore_trigger_event('jrCore', 'db_query_exit', array('result' => null), func_get_args());
                }
                return false;
            }
            // 0 rows
            if ($multi) {
                jrCore_db_close();
            }
    }
    if (!$skip_triggers) {
        jrCore_trigger_event('jrCore', 'db_query_exit', array('result' => null), func_get_args());
    }
    return false;
}

/**
 * Send multiple SELECT statements to the DB at once
 *
 * The jrCore_db_multi_select function is used to send multiple SELECT
 * statements to the MySQL server in one shot - each result set will
 * be returned as a key in the result array.  Uses TRANSACTIONS.
 *
 * @param $_queries array Array of SQL Queries
 * @return mixed Returns true on success
 */
function jrCore_db_multi_select($_queries)
{
    if (!isset($_queries) || !is_array($_queries)) {
        return false;
    }
    $cnt = count($_queries);
    if (!isset($cnt) || $cnt === 0) {
        return false;
    }
    $req = "SET AUTOCOMMIT=0; START TRANSACTION;\n". implode(';',$_queries) .";\nCOMMIT; SET AUTOCOMMIT=1;";
    $con = jrCore_db_connect();
    $res = mysqli_multi_query($con,$req) or jrCore_notice('Error','query error: '. mysqli_error($con));
    if (isset($err)) {
        jrCore_db_close();
        return false;
    }
    if ($res) {
        $_out = array();
        $i = -1;
        while (mysqli_next_result($con)) {
            if ($result = mysqli_store_result($con)) {
                $i++;
                while ($row = mysqli_fetch_assoc($result)) {
                    $_out[$i][] = $row;
                }
            }
        }
        return $_out;
    }
    return false;
}

/**
 * Paginate result sets from the MySQL database
 *
 * The dbPagedQuery function is a "paginator" for db results.  It does
 * NOT create any of the output, but will return an array consisting
 * of the actual data, and the "prev" and "next" pages, if they exist.
 * Use in place of jrCore_db_query if you need paginated results.
 *
 * @param string $query SQL query to run
 * @param int $page_num Numerical offset (start) of first row to return (default 0)
 * @param int $rows_per_page Number of rows to return
 * @param string $_ret Return type for data.  This can be any of the valid return
 *        types as used by jrCore_db_query().
 * @param string $c_query optional Counting Query that will be used in place of primary
 *        query to retrieve row count.  If the main query is very complex, this
 *        can seriously speed up the results.  Note that an int can be provided
 *        as well, and that will be used for the total.
 * @param string $only_val Only return this value from result set
 * @return array returns array of data
 */
function jrCore_db_paged_query($query, $page_num, $rows_per_page, $_ret = 'NUMERIC', $c_query = null, $only_val = null)
{
    // LIMIT should not be included in query
    if (isset($query) && strstr($query, 'LIMIT')) {
        jrCore_notice('Error', "jrCore_db_paged_query do not include a LIMIT clause in your SQL query - it is added automatically");
    }
    if (is_null($c_query) || $c_query === false | strlen($c_query) === 0) {
        $c_query = $query;
    }
    // Get total number of rows
    if (isset($c_query) && jrCore_checktype($c_query, 'number_nn')) {
        $total = intval($c_query);
    }
    else {
        $total = jrCore_db_query($c_query, 'NUM_ROWS');
    }
    if (!jrCore_checktype($page_num, 'number_nz')) {
        $page_num = 1;
    }

    $pagebreak = (int) $rows_per_page;
    if (isset($_COOKIE['jrcore_pager_rows']) && jrCore_checktype($_COOKIE['jrcore_pager_rows'], 'number_nz')) {
        $pagebreak = (int) $_COOKIE['jrcore_pager_rows'];
    }

    // For our query, we can't use the page number - we have to figure out
    // the offset based on the number of rows per page * page number - 1
    $start = intval(($page_num - 1) * $pagebreak);
    $query .= " LIMIT {$start},{$pagebreak}";

    // get our results
    $_out = array(
        'info' => array()
    );
    // now figure out if we have "prev" or "next" links.
    if ($start === 0 && $total <= $pagebreak) {
        $_out['info']['prev_page'] = false;
        $_out['info']['next_page'] = false;
        $_out['info']['this_page'] = 1;
    }
    elseif ($start === 0) {
        $_out['info']['prev_page'] = false;
        $_out['info']['next_page'] = 2;
        $_out['info']['this_page'] = 1;
    }
    elseif (($start + $pagebreak) >= $total) {
        $_out['info']['prev_page'] = intval($page_num - 1);
        $_out['info']['next_page'] = false;
        $_out['info']['this_page'] = (int) ceil($total / $pagebreak);
    }
    else {
        $_out['info']['prev_page'] = intval($page_num - 1);
        $_out['info']['next_page'] = intval($page_num + 1);
        $_out['info']['this_page'] = intval($page_num);
    }
    $_out['info']['total_items'] = intval($total);
    $_out['info']['total_pages'] = (int) ceil($total / $pagebreak);
    // and now get our data
    $_rt = jrCore_db_query($query, $_ret, false, $only_val);
    if ($_rt && is_array($_rt)) {
        $_out['_items'] = $_rt;
    }
    return $_out;
}

/**
 * Get indexes in a MySQL table
 * @param string $module Module table belongs to
 * @param string $table Table name
 * @param null $con
 * @return array
 */
function jrCore_db_get_table_indexes($module, $table, $con = null)
{
    $tbl = jrCore_db_table_name($module, $table);
    $_in = array();
    $req = "SHOW INDEX FROM {$tbl}";
    $_tm = jrCore_db_query($req, 'NUMERIC', false, null, false, $con);
    if ($_tm && is_array($_tm)) {
        foreach ($_tm as $_idx) {
            if (isset($_in["{$_idx['Key_name']}"]) && $_idx['Seq_in_index'] > 1) {
                $_in["{$_idx['Key_name']}"] .= ",{$_idx['Column_name']}";
            }
            else {
                $_in["{$_idx['Key_name']}"] = $_idx['Column_name'];
            }
        }
    }
    return $_in;
}

/**
 * Verify a table schema in the MySQL database
 *
 * The dbVerifyTable function is used to create a MySQL database
 * table (if it does not exist), or validate the columns if it does exist.
 *
 * @param string $module Module table belongs to
 * @param string $table Table to validate
 * @param array $_schema MySQL Database creation SQL query - this will be executed if the table does not exist.
 * @param string $engine MySQL engine to use for table - "InnoDB" or "MyISAM"
 * @param mysqli $con MySQL DB Connection to use
 * @return bool Returns true/false on success/fail
 */
function jrCore_db_verify_table($module, $table, $_schema, $engine = 'MyISAM', $con = null)
{
    global $_post;
    if (!$table || strlen($table) === 0) {
        jrCore_notice('Error', "jrCore_db_verify_table empty or missing table name");
    }
    if (!$_schema || !is_array($_schema)) {
        jrCore_notice('Error', "jrCore_db_verify_table invalid schema array - ensure table columns and indexes are defined in an array");
    }
    // Cleanup
    foreach ($_schema as $k => $line) {
        $_schema[$k] = trim(trim($line), ',');
    }

    // Schema event
    $_schema = jrCore_trigger_event('jrCore', 'db_verify_table', $_schema, null, false, false);

    // get our info about this table (if it exists)
    $tbl = jrCore_db_table_name($module, $table);
    $req = "DESCRIBE {$tbl}";
    $_rt = jrCore_db_query($req, 'Field', false, null, false, $con, false);
    if (!$_rt || !is_array($_rt)) {
        // Create
        jrCore_db_query("CREATE TABLE {$tbl} (" . implode(', ', $_schema) . ') ENGINE=' . $engine . ' DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci', null, false, null, false, $con);
        // Did it created?
        if (jrCore_db_table_exists($module, $table)) {
            if ($table != 'log') {
                jrCore_logger('INF', "jrCore_db_verify_table() created missing table: {$tbl}");
            }
            return true;
        }
        return false;
    }
    else {
        // Are we changing Engine types?
        if (isset($_post['repair_modules']) && $_post['repair_modules'] == 'on') {
            // We are in an integrity check with REPAIR MODULES checked
            jrCore_db_change_table_engine($module, $table, $engine);
        }
    }

    // It appears the table already exists.  Now we want to scan the incoming creation
    // schema and verify that each of our columns exist.  First load our indexes
    $_in = jrCore_db_get_table_indexes($module, $table, $con);

    foreach ($_schema as $line) {

        // TABLE start/end and PRIMARY KEY entries - skip
        if (strstr($line, 'PRIMARY KEY') || strlen($line) === 0) {
            continue;
        }

        // INDEX
        // INDEX band_id (band_id)
        // INDEX stat_index (stat_index) )
        // INDEX band_name (band_name(15))
        if (stripos($line, 'INDEX') === 0) {
            $idx_ikey = jrCore_string_field($line, 2);
            $idx_name = trim(str_replace('`', '', $idx_ikey));
            // Make sure it is built
            if (!isset($_in[$idx_name])) {
                $idx_args = trim(str_ireplace('INDEX ' . $idx_ikey, '', $line));
                $req = "ALTER TABLE {$tbl} ADD INDEX `{$idx_name}` {$idx_args}";
                jrCore_db_query($req, null, false, null, false, $con);
                // now let's see if we were successful in adding our INDEX or NOT
                $req = "SHOW INDEX FROM {$tbl}";
                $_tp = jrCore_db_query($req, 'Key_name', false, null, false, $con);
                if (is_array($_tp[$idx_name])) {
                    jrCore_logger('INF', "jrCore_db_verify_table() created missing table index: {$idx_name} in table: {$tbl}");
                }
                else {
                    jrCore_logger('CRI', "jrCore_db_verify_table() unable to create missing table index: {$idx_name} in table: {$tbl}");
                }
            }
        }

        // UNIQUE
        // UNIQUE band_id (band_id)
        // UNIQUE stat_index (stat_index) )
        // UNIQUE template_unique (template_module, template_name)
        // UNIQUE band_name (band_name(15))
        elseif (stripos($line, 'UNIQUE') === 0) {
            $idx_ikey = jrCore_string_field($line, 2);
            $idx_name = trim(str_replace('`', '', $idx_ikey));
            $idx_flds = str_replace(array(' ',')','('),'',substr($line, strpos($line, '(')));
            if (!isset($_in[$idx_name])) {
                // We are creating a NEW index that did not exist before
                $idx_args = trim(str_ireplace('UNIQUE ' . $idx_ikey, '', $line));
                $req = "ALTER TABLE {$tbl} ADD UNIQUE INDEX `{$idx_name}` {$idx_args}";
                jrCore_db_query($req, null, false, null, false, $con);
                // now let's see if we were successful in adding our INDEX or NOT
                $req = "SHOW INDEX FROM {$tbl}";
                $_tp = jrCore_db_query($req, 'Key_name', false, null, false, $con);
                if (is_array($_tp[$idx_name])) {
                    jrCore_logger('INF', "jrCore_db_verify_table() created missing table unique index: {$idx_name} in table: {$tbl}");
                }
                else {
                    jrCore_logger('CRI', "jrCore_db_verify_table() unable to create missing table unique index: {$idx_name} in table: {$tbl}");
                }
            }
            elseif ($_in[$idx_name] != $idx_flds && '`' . str_replace(',', '`,`', $_in[$idx_name]) . '`' != $idx_flds && strpos($idx_flds, ',')) {
                // Our index fields in a compound UNIQUE index have changed
                $idx_args = trim(str_ireplace('UNIQUE ' . $idx_ikey, '', $line));

                // Drop old index (< MySQL 5.7 there is no ALTER TABLE RENAME|MODIFY INDEX)
                $req = "ALTER TABLE {$tbl} DROP INDEX `{$idx_name}`";
                jrCore_db_query($req, null, false, null, false, $con);

                // Create new UNIQUE Index
                $req = "ALTER TABLE {$tbl} ADD UNIQUE INDEX `{$idx_name}` {$idx_args}";
                jrCore_db_query($req, null, false, null, false, $con);

                // now let's see if we were successful in adding our INDEX or NOT
                $req = "SHOW INDEX FROM {$tbl}";
                $_tp = jrCore_db_query($req, 'Key_name', false, null, false, $con);
                if (is_array($_tp[$idx_name])) {
                    jrCore_logger('INF', "jrCore_db_verify_table() updated table unique index: {$idx_name} in table: {$tbl}");
                }
                else {
                    jrCore_logger('CRI', "jrCore_db_verify_table() unable to update table unique index: {$idx_name} in table: {$tbl}");
                }
            }
        }

        // FULLTEXT
        // FULLTEXT band_id (band_id)
        // FULLTEXT stat_index (stat_index) )
        // FULLTEXT template_unique (template_module, template_name)
        // FULLTEXT band_name (band_name(15))
        elseif (stripos($line, 'FULLTEXT') === 0) {
            if ($engine != 'MyISAM') {
                jrCore_logger('CRI', "jrCore_db_verify_table() fulltext index on {$tbl} requires MyISAM table type!");
            }
            else {
                $idx_ikey = jrCore_string_field($line, 2);
                $idx_name = trim(str_replace('`', '', $idx_ikey));
                $idx_flds = str_replace(array(' ', ')', '('), '', substr($line, strpos($line, '(')));
                if (!isset($_in[$idx_name])) {
                    // We are creating a NEW index that did not exist before
                    $idx_args = trim(str_ireplace('FULLTEXT ' . $idx_ikey, '', $line));
                    $req      = "ALTER TABLE {$tbl} ADD FULLTEXT INDEX `{$idx_name}` {$idx_args}";
                    jrCore_db_query($req, null, false, null, false, $con);
                    // now let's see if we were successful in adding our INDEX or NOT
                    $req = "SHOW INDEX FROM {$tbl}";
                    $_tp = jrCore_db_query($req, 'Key_name', false, null, false, $con);
                    if (is_array($_tp[$idx_name])) {
                        jrCore_logger('INF', "jrCore_db_verify_table() created missing table fulltext index: {$idx_name} in table: {$tbl}");
                    }
                    else {
                        jrCore_logger('CRI', "jrCore_db_verify_table() unable to create missing table fulltext index: {$idx_name} in table: {$tbl}");
                    }
                }
                elseif ($_in[$idx_name] != $idx_flds && '`' . str_replace(',', '`,`', $_in[$idx_name]) . '`' != $idx_flds && strpos($idx_flds, ',')) {
                    // Our index fields in a compound UNIQUE index have changed
                    $idx_args = trim(str_ireplace('FULLTEXT ' . $idx_ikey, '', $line));

                    // Drop old index (< MySQL 5.7 there is no ALTER TABLE RENAME|MODIFY INDEX)
                    $req = "ALTER TABLE {$tbl} DROP INDEX `{$idx_name}`";
                    jrCore_db_query($req, null, false, null, false, $con);

                    // Create new UNIQUE Index
                    $req = "ALTER TABLE {$tbl} ADD FULLTEXT INDEX `{$idx_name}` {$idx_args}";
                    jrCore_db_query($req, null, false, null, false, $con);

                    // now let's see if we were successful in adding our INDEX or NOT
                    $req = "SHOW INDEX FROM {$tbl}";
                    $_tp = jrCore_db_query($req, 'Key_name', false, null, false, $con);
                    if (is_array($_tp[$idx_name])) {
                        jrCore_logger('INF', "jrCore_db_verify_table() updated table fulltext index: {$idx_name} in table: {$tbl}");
                    }
                    else {
                        jrCore_logger('CRI', "jrCore_db_verify_table() unable to update table fulltext index: {$idx_name} in table: {$tbl}");
                    }
                }
            }
        }

        // COLUMN
        // [Field] => modal_value
        // [Type] => varchar(128)
        // [Null] => NO
        // [Key] =>
        // [Default] =>
        // [Extra] =>
        else {
            $col_ikey = jrCore_string_field($line, 1);
            $col_name = trim(str_replace('`', '', $col_ikey));
            $col_args = trim(str_replace($col_name . ' ', '', str_replace('`', '', $line)));

            // Make sure it is built
            if (!isset($_rt[$col_name])) {
                $req = "ALTER TABLE {$tbl} ADD `{$col_name}` {$col_args}";
                jrCore_db_query($req, null, false, null, false, $con);
                // now let's see if we were successful in adding our column or NOT
                $req = "DESCRIBE {$tbl}";
                $_tp = jrCore_db_query($req, 'Field', false, null, false, $con, false);
                if ($_tp && is_array($_tp) && is_array($_tp[$col_name])) {
                    jrCore_logger('INF', "jrCore_db_verify_table() created missing table column: {$col_name} in table: {$tbl}");
                }
                else {
                    jrCore_logger('CRI', "jrCore_db_verify_table() unable to create missing table column: {$col_name} in table: {$tbl}");
                }
            }
            else {
                // See if we are changing...
                // MEDIUMINT(11) UNSIGNED NOT NULL DEFAULT '0'
                $col_type = strtolower(jrCore_string_field($col_args, 1));
                $cur_type = strtolower(jrCore_string_field($_rt[$col_name]['Type'], 1));
                if ($col_type && $cur_type && $col_type !== $cur_type) {
                    $req = "ALTER TABLE {$tbl} CHANGE `{$col_name}` `{$col_name}` {$col_args}";
                    jrCore_db_query($req, null, false, null, false, $con);
                    jrCore_logger('INF', "jrCore_db_verify_table() altered table column: {$col_name} in table: {$tbl}");
                }
            }
        }
    }
    return true;
}

/**
 * Change the MySQL Engine for a Table
 * @param string $module Module Name
 * @param string $table  Table Name
 * @param string $engine Engine = MyISAM|InnoDB
 * @param bool $truncate set to TRUE to truncate the table first (faster) but be careful!
 * @return bool
 */
function jrCore_db_change_table_engine($module, $table, $engine, $truncate = false)
{
    switch ($engine) {
        case 'MyISAM':
        case 'InnoDB':
            break;
        default:
            return false;
            break;
    }
    $tbl = jrCore_db_table_name($module, $table);
    $req = "SHOW TABLE STATUS WHERE `Name` = '{$tbl}'";
    $_tt = jrCore_db_query($req, 'SINGLE');
    if ($_tt && is_array($_tt) && isset($_tt['Engine']) && $_tt['Engine'] != $engine) {

        // We are NOT running the right engine type - change
        if ($truncate) {
            $req = "TRUNCATE TABLE {$tbl}";
            jrCore_db_query($req);
        }

        // Alter
        $req = "ALTER TABLE {$tbl} ENGINE = {$engine}";
        jrCore_db_query($req);
        jrCore_logger('INF', "changed MySQL engine type to {$engine} for table: {$tbl}");

    }
    return true;
}
