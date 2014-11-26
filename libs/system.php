<?php
require 'Utils/changeServer.php';

// Hostname
$hostname = Misc::execShellServer('hostname');

// OS
if (!($os = Misc::execShellServer('/usr/bin/lsb_release -ds')))
{
    if(!($os = Misc::execShellServer('cat /etc/system-release'))) 
    {
        if (!($os = Misc::execShellServer('find /etc/*-release -type f -exec cat {} \; | grep NAME | tail -n 1 | cut -d= -f2 | tr -d \'"\'')))
        {
            $os = 'N.A';
        }
    }
}

// Kernel
if (!($kernel = Misc::execShellServer('/bin/uname -r')))
{
    $kernel = 'N.A';
}

// Uptime
if (!($totalSeconds = Misc::execShellServer('/usr/bin/cut -d. -f1 /proc/uptime')))
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
if (!($upt_tmp = Misc::execShellServer('cat /proc/uptime')))
{
    $last_boot = 'N.A';
}
else
{
    $upt = explode(' ', $upt_tmp);
    $last_boot = date('Y-m-d H:i:s', time() - intval($upt[0]));
}

// Current users
if (!($current_users = Misc::execShellServer('who -u | awk \'{ print $1 }\' | wc -l')))
{
    $current_users = 'N.A';
}

// Server datetime
if (!($server_date = Misc::execShellServer('/bin/date')))
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