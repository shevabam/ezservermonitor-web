<?php

if(isset($_GET['id']))
{
	$id = $_GET['id'] ;
		
	require '../autoload.php';
	$Config = new Config();

	$datas = array();

	$available_protocols = array('tcp', 'udp');
	$show_port = $Config->get('services:show_port');

	if (count($Config->get('services:list')) > 0)
	{
		$services = $Config->get('services:list');
		if(isset($services[$id]))
		{
			$service = $services[$id];
			
			$host     = $service['host'];
			$port     = $service['port'];
			$stop     = $service['stop'];
			$start     = $service['start'];
			
			$protocol = isset($service['protocol']) && in_array($service['protocol'], $available_protocols) ? $service['protocol'] : 'tcp';
			if (Misc::scanPort($host, $port, $protocol))
			{
				echo exec('sudo '.$stop);
				echo ' stop demande ';
			}
			else
			{
				echo exec('sudo '.$start);
				echo ' start demande ';
			}
				
			
		}		
	}
}


