<?php
require '../autoload.php';

const Under_Voltage_Detected = 0b00000000000000000001;
const ARM_Frequency_Capped = 0b00000000000000000010;
const Currently_Throttled = 0b00000000000000000100;
const Soft_Temperature_Limit_Active = 0b00000000000000001000;
const Under_Voltage_Has_Occurred = 0b00010000000000000000;
const ARM_Frequency_Capping_Has_Occurred = 0b00100000000000000000;
const Throttling_Has_Occurred = 0b01000000000000000000;
const Soft_Temperature_Limit_Has_Occurred = 0b10000000000000000000;


$Config = new Config();


$datas = array();

if ($Config->get('pistatus:enable'))
{

    
    $vcgencmd_output = hexdec(trim(shell_exec('vcgencmd get_throttled'), 'throttled='));
    
    if ($vcgencmd_output & Under_Voltage_Detected) { $datas[] = array( 'name' => "Under voltage detected" , 'status' => 1 ); }
    else { $datas[] = array( 'name' => "Under Voltage detected" , 'status' => 0 ); }
    
    if ($vcgencmd_output & ARM_Frequency_Capped) { $datas[] = array( 'name' => "ARM frequency capped" , 'status' => 1 ); }
    else { $datas[] = array( 'name' => "ARM frequency capped" , 'status' => 0 ); }
    
    if ($vcgencmd_output & Currently_Throttled) { $datas[] = array( 'name' => "Currently throttled" , 'status' => 1 ); }
    else { $datas[] = array( 'name' => "Currently throttled" , 'status' => 0 ); }
    
    if ($vcgencmd_output & Soft_Temperature_Limit_Active) { $datas[] = array( 'name' => "Soft temperature limit active" , 'status' => 1 ); }
    else { $datas[] = array( 'name' => "Soft temperature limit active" , 'status' => 0 ); }
    
    if ($vcgencmd_output & Under_Voltage_Has_Occurred) { $datas[] = array( 'name' => "Under voltage has occurred" , 'status' => 1 ); }
    else { $datas[] = array( 'name' => "Under voltage has occurred" , 'status' => 0 ); }
    
    if ($vcgencmd_output & ARM_Frequency_Capping_Has_Occurred) { $datas[] = array( 'name' => "ARM frequency capping has occurred" , 'status' => 1 ); }
    else { $datas[] = array( 'name' => "ARM frequency capping has occurred" , 'status' => 0 ); }
    
    if ($vcgencmd_output & Throttling_Has_Occurred) { $datas[] = array( 'name' => "Throttling has occurred" , 'status' => 1 ); }
    else { $datas[] = array( 'name' => "Throttling has occurred" , 'status' => 0 ); }
    
    if ($vcgencmd_output & Soft_Temperature_Limit_Has_Occurred) { $datas[] = array( 'name' => "Soft temperature limit has occurred" , 'status' => 1 ); }
    else { $datas[] = array( 'name' => "Soft temperature limit has occurred" , 'status' => 0 ); }
}

echo json_encode($datas);

