<?php
require 'Utils/Misc.class.php';

// Free
if (!($free = shell_exec('/usr/bin/free -t | sed -n \'3p\' | awk \'{print $5}\'')))
{
    $free = 0;
}

// Total
if (!($total = shell_exec('/usr/bin/free -t | sed -n \'3p\' | awk \'{print $3}\'')))
{
    $total = 0;
}

// Used
if (!($used = shell_exec('/usr/bin/free -t | sed -n \'3p\' | awk \'{print $4}\'')))
{
    $used = 0;
}

// Percent used
if ($total == 0)
{
    $percent_used = 0;
}
else
{
    $percent_used = 100 - (round($free / $total * 100));
}

$datas = array(
    'used'          => Misc::getSize($used * 1024),
    'free'          => Misc::getSize($free * 1024),
    'total'         => Misc::getSize($total * 1024),
    'percent_used'  => $percent_used,
);

echo json_encode($datas);
