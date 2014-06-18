<?php
require 'Utils/Config.class.php';
$Config = new Config();


$datas = array();

if (count($Config->get('services')) > 0)
{
    foreach ($Config->get('services') as $service)
    {
        $ip = 'localhost';
        $sock = @fsockopen($ip, $service['port'], $num, $error, 5);
        
        if ($sock)
        {
            $datas[] = array(
                'port'      => $service['port'],
                'name'      => $service['name'],
                'status'    => 1,
            );
            
            fclose($sock);
        }
        else
        {
            $datas[] = array(
                'port'      => $service['port'],
                'name'      => $service['name'],
                'status'    => 0,
            );
        }
    }
}


echo json_encode($datas);