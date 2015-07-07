<?php
require '../autoload.php';

$datas    = array();
$network  = array();

// Possible commands for ifconfig and ip
$commands = array(
    'ifconfig' => array('ifconfig', '/sbin/ifconfig', '/usr/bin/ifconfig', '/usr/sbin/ifconfig'),
    'ip'       => array('ip', '/bin/ip', '/sbin/ip', '/usr/bin/ip', '/usr/sbin/ip'),
);

// Returns command line for retreive interfaces
function getInterfacesCommand($commands)
{
    $ifconfig = Misc::whichCommand($commands['ifconfig'], ' | awk -F \'[/  |: ]\' \'{print $1}\' | sed -e \'/^$/d\'');

    if (!empty($ifconfig))
    {
        return $ifconfig;
    }
    else
    {
        $ip_cmd = Misc::whichCommand($commands['ip'], ' -V', false);

        if (!empty($ip_cmd))
        {
            return $ip_cmd.' -oneline link show | awk \'{print $2}\' | sed "s/://"';
        }
        else
        {
            return null;
        }
    }
}

// Returns command line for retreive IP address from interface name
function getIpCommand($commands, $interface)
{
    $ifconfig = Misc::whichCommand($commands['ifconfig'], ' '.$interface.' | awk \'/inet / {print $2}\' | cut -d \':\' -f2');

    if (!empty($ifconfig))
    {
        return $ifconfig;
    }
    else
    {
        $ip_cmd = Misc::whichCommand($commands['ip'], ' -V', false);

        if (!empty($ip_cmd))
        {
            return 'for family in inet inet6; do '.
               $ip_cmd.' -oneline -family $family addr show '.$interface.' | grep -v fe80 | awk \'{print $4}\' | sed "s/\/.*//"; ' .
            'done';
        }
        else
        {
            return null;
        }
    }
}


$getInterfaces_cmd = getInterfacesCommand($commands);

if (is_null($getInterfaces_cmd) || !(exec($getInterfaces_cmd, $getInterfaces)))
{
    $datas[] = array('interface' => 'N.A', 'ip' => 'N.A');
}
else
{
    foreach ($getInterfaces as $name)
    {
        $ip = null;

        $getIp_cmd = getIpCommand($commands, $name);        

        if (is_null($getIp_cmd) || !(exec($getIp_cmd, $ip)))
        {
            $network[] = array(
                'name' => $name,
                'ip'   => 'N.A',
            );
        }
        else
        {
            if (!isset($ip[0]))
                $ip[0] = '';

            $network[] = array(
                'name' => $name,
                'ip'   => $ip[0],
            );
        }
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