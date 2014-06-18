<?php
require 'Utils/Misc.class.php';

// Hostname
$hostname = php_uname('n');

// OS
if (!($os = shell_exec('/usr/bin/lsb_release -ds')))
{
    if (!($os = shell_exec('cat /etc/fedora-release')))
    {
        if (!($os = shell_exec('cat /etc/redhat-release')))
        {
            if (!($os = shell_exec('cat /etc/mandriva-release')))
            {
                if (!($os = shell_exec('cat /etc/SuSE-release')))
                {
                    if (!($os = shell_exec('cat /etc/centos-release')))
                    {
                        $os = 'N.A';
                    }
                }
            }
        }
    }
}

// Kernel
if (!($kernel = shell_exec('/bin/uname -r')))
{
    $kernel = 'N.A';
}

// Uptime
if (!($totalSeconds = shell_exec('/usr/bin/cut -d. -f1 /proc/uptime')))
{
    $uptime = 'N.A';
}
else
{
    $totalMin   = $totalSeconds / 60;
    $totalHours = $totalMin / 60;

    $days  = floor($totalHours / 24);
    $hours = floor($totalHours - ($days * 24));
    $min   = floor($totalMin - ($days * 60 * 24) - ($hours * 60));

    $uptime = '';
    if ($days != 0)
        $uptime .= $days.' day'.Misc::pluralize($days).' ';

    if ($hours != 0)
        $uptime .= $hours.' hour'.Misc::pluralize($hours).' ';

    if ($min != 0)
        $uptime .= $min.' minute'.Misc::pluralize($min);
}

// Last boot
if (!($upt_tmp = shell_exec('cat /proc/uptime')))
{
    $last_boot = 'N.A';
}
else
{
    $upt = explode(' ', $upt_tmp);
    $last_boot = date('Y-m-d H:i:s', time() - intval($upt[0]));
}

// Current users
if (!($current_users_cmd = shell_exec('who -q')))
{
    $current_users = 'N.A';
}
else
{
    $arr_user = explode('=', $current_users_cmd);
    $current_users = $arr_user[1];
}

// Server datetime
if (!($server_date = shell_exec('/bin/date')))
{
    $server_date = date('Y-m-d H:i:s');
}


$datas = array(
    'hostname'      => $hostname,
    'os'            => $os,
    'kernel'        => $kernel,
    'uptime'        => $uptime,
    'last_boot'     => $last_boot,
    'current_users' => $current_users,
    'server_date'   => $server_date,
);

echo json_encode($datas);