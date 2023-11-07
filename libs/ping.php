<?php
require '../autoload.php';
$Config = new Config();


$datas = array();


if (count($Config->get('ping:hosts')) > 0)
    $hosts = $Config->get('ping:hosts');
else
    $hosts = array('google.com', 'wikipedia.org');

array_push($hosts, $_SERVER["REMOTE_ADDR"]);
$hosts = array_reverse($hosts, true);

foreach ($hosts as $host)
{
    exec('/bin/ping -qc 1 '.$host.' | awk -F/ \'/^(rtt|round-trip)/ { print $5 }\'', $result);

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

$datas[0]["host"] = "Station ({$_SERVER["REMOTE_ADDR"]})";

echo json_encode($datas);
