<?php
require 'Utils/Misc.class.php';
require 'Utils/Config.class.php';
$Config = new Config();

$datas = array();

if (!(exec('/bin/df -T | awk -v c=`/bin/df -T | grep -bo "Type" | awk -F: \'{print $1}\'` \'{print substr($0,c);}\' | tail -n +2 | awk \'{print $1","$2","$3","$4","$5","$6}\'', $df)))
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
    $mounted_points = array();

    foreach ($df as $mounted)
    {
        list($type, $total, $used, $free, $percent, $mount) = explode(',', $mounted);

        if (strpos($type, 'tmpfs') !== false && $Config->get('disk:show_tmpfs') === false)
            continue;

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