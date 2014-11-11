<?php
require 'Utils/Misc.class.php';

$datas   = array();
$network = array();

$ifconfig = trim(shell_exec('which ifconfig'));

if (!(exec($ifconfig.' |awk -F \'[/  |: ]\' \'{print $1}\' |sed -e \'/^$/d\'', $getInterfaces)))
{
    $datas[] = array('interface' => 'N.A', 'ip' => 'N.A');
}
else
{
    foreach ($getInterfaces as $name)
    {
        $ip = null;
        exec($ifconfig.' '.$name.' | awk \'/inet / {print $2}\' | cut -d \':\' -f2', $ip);

        if (!isset($ip[0]))
            $ip[0] = '';

        $network[] = array(
            'name' => $name,
            'ip'   => $ip[0],
        );
    }

    foreach ($network as $interface)
    {
        // Get transmit and receive datas by interface
        exec('cat /sys/class/net/'.$interface['name'].'/statistics/tx_bytes', $getBandwidth_tx);
        exec('cat /sys/class/net/'.$interface['name'].'/statistics/rx_bytes', $getBandwidth_rx);

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