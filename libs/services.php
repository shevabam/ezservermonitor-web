<?php
require __DIR__.'/../autoload.php';
$config = Config::instance();


$datas = array();

$available_protocols = array('tcp', 'udp');

$show_port = $config->get('services:show_port');

if (count($config->get('services:list')) > 0)
{
    foreach ($config->get('services:list') as $service)
    {
        $host     = $service['host'];
        $port     = $service['port'];
        $name     = $service['name'];
        $protocol = isset($service['protocol']) && in_array($service['protocol'], $available_protocols) ? $service['protocol'] : 'tcp';

        if (Misc::scanPort($host, $port, $protocol))
            $status = 1;
        else
            $status = 0;

        $datas[] = array(
            'port'      => $show_port === true ? $port : '',
            'name'      => $name,
            'status'    => $status,
        );
    }
}


if (!isset($_SERVER['argv']) || !in_array('--quiet', $_SERVER['argv']))
	echo json_encode($datas);
