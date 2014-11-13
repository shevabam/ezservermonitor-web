<?php
session_start();
require 'Misc.class.php';
require 'Config.class.php';
$config = new Config();
if (!isset($_SESSION['server'])) {
    $_SESSION = array(
        'server' => $config->get("servers:hosts")[0],
        'serverList' => $config->get("servers:hosts"),
    );
}

if (isset($_REQUEST['server'])) {
    $_SESSION['server'] = $_REQUEST['server'];
}


?>