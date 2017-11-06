<?php
require '../autoload.php';
$Config = new Config();


$datas = array();

$available_protocols = array('tcp', 'udp');

$show_port = $Config->get('services:show_port');

$check_binary_allowed = Misc::checkIfProcessRunningAvailable();

if (count($Config->get('services:list')) > 0)
{
    foreach ($Config->get('services:list') as $service)
    {
        $host     = $service['host'];
        $port     = $service['port'];
        $name     = $service['name'];
        $binary   = $service['binary'];
        $protocol = isset($service['protocol']) && in_array($service['protocol'], $available_protocols) ? $service['protocol'] : 'tcp';

        $check_port = false;
        $check_binary = false;
        
        if ( (isset($host)) && (isset($port)) && (isset($protocol)) )
        {
            $check_port = true;
        }
        
        if ($check_binary_allowed === true)
        {
            if (isset($binary))
            {
                $check_binary = true;
            }
        }
        
        $port_status = 1;
        $binary_status = 1;
        $status = 0;
        
        if ($check_port === true)
        {
            if (Misc::scanPort($host, $port, $protocol))
                $port_status = 1;
            else
                $port_status = 0;
        }

        if ($check_binary === true)
        {
            if (Misc::checkIfProcessRunning($binary))
                $binary_status = 1;
            else
                $binary_status = 0;
        }
        
        if ( ($port_status > 0) && ($binary_status > 0) )
        {
            $status = 1;
        }
        
        $detail = "";
        
        if ( ($check_port === true) && ($check_binary === true) )
        {
            if($show_port === true)
            {
                $detail = $protocol . ":" . $port . " and " . $binary;
            }
            else
            {
                $detail = $binary;
            }
        }
        elseif($check_port)
        {
            if($show_port === true)
            {
                $detail = $protocol . ":" . $port;
            }
        }
        elseif($check_binary)
        {
            $detail = $binary;
        }

        if ( ($check_port === true) || ($check_binary === true) )
        {
            $datas[] = array(
                'detail'    => $detail,
                'name'      => $name,
                'status'    => $status,
            );
        }
    }
}


echo json_encode($datas);
