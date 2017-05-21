<?php
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);
function adminer_object() {

    // required to run any plugin
    include_once "./contrib/adminer/plugins/plugin.php";
    include_once "./contrib/adminer/plugins/tables-filter.php";

    $plugins = array(
        // specify enabled plugins here
        new AdminerTablesFilter
    );

    return new AdminerPlugin($plugins);
}

// include original Adminer or Adminer Editor
include "./contrib/adminer/adminer-4.2.1.php";
