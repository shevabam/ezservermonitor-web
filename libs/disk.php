<?php
require 'Utils/Misc.class.php';

$datas = array();

if (!(exec('/bin/df | awk \'{print $2","$3","$4","$5","$6}\'', $df)))
{
    $datas[] = array(
        'total'         => 'N.A',
        'used'          => 'N.A',
        'free'          => 'N.A',
        'percent_used'  => 0,
        'mount'         => 'N.A',
    );
}
else
{
    $first_line = false;

    $mounted_points = array();

    foreach ($df as $mounted)
    {
        if ($first_line === false)
        {
            $first_line = true;
            continue;
        }

        list($total, $used, $free, $percent, $mount) = explode(',', $mounted);

        if (!in_array($mount, $mounted_points))
        {
            $mounted_points[] = trim($mount);

            $datas[] = array(
                'total'         => Misc::getSize($total * 1024),
                'used'          => Misc::getSize($used * 1024),
                'free'          => Misc::getSize($free * 1024),
                'percent_used'  => trim($percent, '%'),
                'mount'         => $mount,
            );
        }
    }

}


echo json_encode($datas);