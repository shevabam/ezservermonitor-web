<?php
require __DIR__.'/../autoload.php';
$config = Config::instance();


$datas = array();

if (count($config->get('ping:hosts')) > 0)
    $hosts = $config->get('ping:hosts');
else
    $hosts = array('google.com', 'wikipedia.org');

foreach ($hosts as $host)
{
    Misc::exec('/bin/ping -qc 1 '.$host.' | awk -F/ \'/^rtt/ { print $5 }\'', $result);

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

if (!isset($_SERVER['argv']) || !in_array('--quiet', $_SERVER['argv']))
    echo json_encode($datas);
