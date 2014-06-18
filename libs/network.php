<?php
require 'Utils/Misc.class.php';

$datas = array();

if (!(exec('/sbin/ifconfig |awk -F \'[/  |: ]\' \'{print $1}\' |sed -e \'/^$/d\'', $getInterfaces)))
{
    $datas[] = array('interface' => 'N.A', 'ip' => 'N.A');
}
else
{
    exec('/sbin/ifconfig | awk \'/inet / {print $2}\' | cut -d \':\' -f2', $getIps);

    foreach ($getInterfaces as $key => $interface)
    {
        // Get transmit and receive datas by interface
        exec('cat /sys/class/net/'.$interface.'/statistics/tx_bytes', $getBandwidth_tx);
        exec('cat /sys/class/net/'.$interface.'/statistics/rx_bytes', $getBandwidth_rx);

        $datas[] = array(
            'interface' => $interface,
            'ip'        => $getIps[$key],
            'transmit'  => Misc::getSize($getBandwidth_tx[0]),
            'receive'   => Misc::getSize($getBandwidth_rx[0]),
        );

        unset($getBandwidth_tx, $getBandwidth_rx);
    }
}


echo json_encode($datas);