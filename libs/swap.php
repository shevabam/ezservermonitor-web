<?php
require __DIR__.'/../autoload.php';

$config = Config::instance();

// Free
if (!($free = shell_exec('grep SwapFree /proc/meminfo | awk \'{print $2}\'')))
{
    $free = 0;
}

// Total
if (!($total = shell_exec('grep SwapTotal /proc/meminfo | awk \'{print $2}\'')))
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