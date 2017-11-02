<?php
require '../autoload.php';

$free = 0;

if (shell_exec('cat /proc/meminfo'))
{
    $free    = shell_exec('grep MemFree /proc/meminfo | awk \'{print $2}\'');
    $buffers = shell_exec('grep Buffers /proc/meminfo | awk \'{print $2}\'');
    $cached  = shell_exec('grep Cached /proc/meminfo | awk \'{print $2}\'');

    $free = (int)$free + (int)$buffers + (int)$cached;
}

// Total
if (!($total = shell_exec('grep MemTotal /proc/meminfo | awk \'{print $2}\'')))
{
    $total = 0;
}
$total = (int)$total;

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