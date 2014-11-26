<?php
require 'Utils/changeServer.php';

$datas   = array();
$network = array();

$ifconfig = trim(Misc::execShellServer('which ifconfig'));
$cmd = $getInterfaces = Misc::execServer($ifconfig.' |awk -F \'[/ ]\' \'{print $1}\' |sed -e \'/^$/d\'');

if (!$cmd)
{
    $datas[] = array('interface' => 'N.A', 'ip' => 'N.A');
}
else
{
    foreach ($getInterfaces as $name)
    {
        $ip = null;
        $ip = Misc::execShellServer($ifconfig.' '.$name.' | awk \'/inet / {print $2}\' | cut -d \':\' -f2');

        if (!isset($ip[0]))
            $ip[0] = '';

        $network[] = array(
            'name' => $name,
            'ip'   => is_array($ip) ? '' : trim($ip),
        );
    }

    foreach ($network as $interface)
    {
        // Get transmit and receive datas by interface
        $getBandwidth_tx = Misc::execShellServer('cat /sys/class/net/'.$interface['name'].'/statistics/tx_bytes');
        $getBandwidth_rx = Misc::execShellServer('cat /sys/class/net/'.$interface['name'].'/statistics/rx_bytes');

        $datas[] = array(
            'interface' => $interface['name'],
            'ip'        => $interface['ip'],
            'transmit'  => Misc::getSize($getBandwidth_tx[0]),
            'receive'   => Misc::getSize($getBandwidth_rx[0]),
        );

        unset($getBandwidth_tx, $getBandwidth_rx);
    }
}


echo json_encode($datas);