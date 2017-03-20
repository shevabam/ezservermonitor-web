<?php
require '../autoload.php';
$Config = new Config();


$datas = array();

if (count($Config->get('ping:hosts')) > 0)
    $hosts = $Config->get('ping:hosts');
else
    $hosts = array('google.com', 'wikipedia.org');

foreach ($hosts as $host)
{
    exec('/bin/ping -qc 1 '.$host.' | awk -F/ \'/^rtt/ { print $5 }\'', $result);

    if (!isset($result[0]))
    {
        $result[0] = "+Infinity";
    }
    
    $datas[] = array(
        'host' => $host,
        'ping' => $result[0],
    );

    unset($result);
}

echo json_encode($datas);