<?php
require 'Utils/changeServer.php';
$Config = new Config();


$datas = array();
$services = $Config->get('services');
uasort($services, function ($a, $b) {
    if ($a['port'] == $b['port'] )
        return 0;
    return ( $a['port'] < $b['port'] ) ? -1 : 1;
});

if (count($services) > 0)
{
    foreach ($services as $service)
    {
        $host = $service['host'];
        if ($host != $_SESSION['server']) {
            continue;
        }
        $sock = @fsockopen($host, $service['port'], $num, $error, 5);
        
        if ($sock)
        {
            $datas[] = array(
                'host'      => $host,
                'port'      => $service['port'],
                'name'      => $service['name'],
                'status'    => 1,
            );
            
            fclose($sock);
        }
        else
        {
            $datas[] = array(
                'host'      => $host,
                'port'      => $service['port'],
                'name'      => $service['name'],
                'status'    => 0,
            );
        }
    }
}


echo json_encode($datas);