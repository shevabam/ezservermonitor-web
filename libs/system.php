<?php
require __DIR__.'/../autoload.php';
date_default_timezone_set('Europe/Paris');

$config = Config::instance();
// Hostname
$hostname = php_uname('n');

// OS
if (!file_exists('/usr/bin/lsb_release') || !($os = shell_exec('/usr/bin/lsb_release -ds | cut -d= -f2 | tr -d \'"\'')))
{
    if (!file_exists('/etc/system-release') || !($os = shell_exec('cat /etc/system-release | cut -d= -f2 | tr -d \'"\'')))
    {
        if (!file_exists('/etc/os-release') || !($os = shell_exec('cat /etc/os-release | grep PRETTY_NAME | tail -n 1 | cut -d= -f2 | tr -d \'"\'')))
        {
            if (!($os = shell_exec('find /etc/*-release -type f -exec cat {} \; | grep PRETTY_NAME | tail -n 1 | cut -d= -f2 | tr -d \'"\'')))
            {
                $os = 'N.A';
            }
        }
    }
}
$os = trim($os, '"');
$os = str_replace("\n", '', $os);

// Kernel
if (!($kernel = Misc::shellexec('/bin/uname -r')))
{
    $kernel = 'N.A';
}

// Uptime
if (!($totalSeconds = Misc::shellexec('/usr/bin/cut -d. -f1 /proc/uptime')))
{
    $uptime = 'N.A';
}
else
{
    $uptime = Misc::getHumanTime($totalSeconds);
}

// Last boot
if (!($upt_tmp = Misc::shellexec('cat /proc/uptime')))
{
    $last_boot = 'N.A';
}
else
{
    $upt = explode(' ', $upt_tmp);
    $last_boot = date('Y-m-d H:i:s', time() - intval($upt[0]));
}

// Current users
if (!($current_users = Misc::shellexec('who -u | awk \'{ print $1 }\' | wc -l')))
{
    $current_users = 'N.A';
}

// Server datetime
if (!($server_date = Misc::shellexec('/bin/date')))
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
