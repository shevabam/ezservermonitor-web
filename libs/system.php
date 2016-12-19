<?php
require __DIR__.'/../autoload.php';
date_default_timezone_set('Europe/Paris');

$config = Config::instance();
// Hostname
$hostname = php_uname('n');

// OS
if (!is_readable($file = '/usr/bin/lsb_release')) {
  if (!is_readable($file = '/etc/system-release')) {
    if (!is_readable($file = '/etc/os-release')) {
      $file = false;
    }
  }
}
if (!$file) {
  if (!($os = Misc::shellexec("find /etc/*-release -type f -exec cat {} \\; | grep PRETTY_NAME | tail -n 1 | cut -d= -f2 | tr -d '\"'")))
  {
    $os = 'N.A';
  }
}
else {
  if ($file == '/etc/os-release') {
    $os = Misc::shellexec('cat /etc/os-release | grep PRETTY_NAME | tail -n 1 | cut -d= -f2 | tr -d \'"\'');
  } else {
    $os = Misc::shellexec($file . " -ds | cut -d= -f2 | tr -d '\"'");
  }
}
$os = trim($os, '"');
$os = str_replace("\n", '', $os);

// Kernel
$cmd = $config->get('system:cmdKernel');
if (!($kernel = Misc::shellexec($cmd)))
{
    $kernel = 'N.A';
}

// Uptime
$cmd = $config->get('system:cmdUptime');
if (!($totalSeconds = Misc::shellexec($cmd)))
{
    $uptime = 'N.A';
}
else
{
    $uptime = Misc::getHumanTime($totalSeconds);
}

// Last boot
$cmd = $config->get('system:cmdLastBoot');
if (!($upt_tmp = Misc::shellexec($cmd)))
{
    $last_boot = 'N.A';
}
else
{
    $upt = explode(' ', $upt_tmp);
    $last_boot = date('Y-m-d H:i:s', time() - intval($upt[0]));
}

// Current users
$cmd = $config->get('system:cmdCurrentUser');
if (!($current_users = Misc::shellexec($cmd)))
{
    $current_users = 'N.A';
}

$cmd = $config->get('system:cmdServerDate');
// Server datetime
if (!($server_date = Misc::shellexec($cmd)))
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
