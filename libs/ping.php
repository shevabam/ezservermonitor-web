<?php
require 'Utils/changeServer.php';
$Config = new Config();


$datas = array();

if (count($Config->get('ping:hosts')) > 0)
    $hosts = $Config->get('ping:hosts');
else
    $hosts = array('google.com', 'wikipedia.org');

foreach ($hosts as $host)
{
    $result = Misc::execServer('/bin/ping -qc 1 '.$host.' | awk -F/ \'/^rtt/ { print $5 }\'');

    if (!isset($result[0]))
    {
        $result[0] = 0;
    }
    
    $datas[] = array(
        'host' => $host,
        'ping' => $result[0],
    );

    unset($result);
}

echo json_encode($datas);