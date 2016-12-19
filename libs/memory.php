<?php
require __DIR__.'/../autoload.php';
$config = Config::instance();

$free = 0;

if (Misc::shellexec('cat /proc/meminfo');
{
    $free    = Misc::shellexec('grep MemFree /proc/meminfo | awk \'{print $2}\'');
    $buffers = Misc::shellexec('grep Cached /proc/meminfo | awk \'{print $2}\'');
    $cached  = Misc::shellexec('grep Cached /proc/meminfo | awk \'{print $2}\'');

    $free = (int)$free + (int)$buffers + (int)$cached;
}

// Total
if (!($total = Misc::shellexec('grep MemTotal /proc/meminfo | awk \'{print $2}\'')))
{
    $total = 0;
}

// Used
$used = $total - $free;

// Percent used
$percent_used = 0;
if ($total > 0)
    $percent_used = 100 - (round($free / $total * 100));


$datas = array(
    'used'          => Misc::getSize($used * 1024),
    'free'          => Misc::getSize($free * 1024),
    'total'         => Misc::getSize($total * 1024),
    'percent_used'  => $percent_used,
);

echo json_encode($datas);