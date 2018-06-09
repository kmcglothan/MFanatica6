<?php
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);


function adminer_object() {

    // required to run any plugin
    include_once "./contrib/adminer/plugins/plugin.php";
    include_once "./contrib/adminer/plugins/quickfilter.php";


    $plugins = array(
        // specify enabled plugins here
        new AdminerQuickFilterTables,
    );

    return new AdminerPlugin($plugins);
}

// include original Adminer PHP script
include "./contrib/adminer/adminer-4.6.1.php";
