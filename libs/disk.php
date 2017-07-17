<?php
require __DIR__.'/../autoload.php';
$config = Config::instance();

$datas = array();

# if there are more than 7 awk's colums it means the mount point name contains spaces
# so consider the first colums as a unique colum and the last 6 as real colums
$cmd = $config->get('disk:cmd');
Misc::exec($cmd, $df);
if (!$df)
{
    $datas[] = array(
        'total'         => 'N.A',
        'used'          => 'N.A',
        'free'          => 'N.A',
        'percent_used'  => 0,
        'mount'         => 'N.A',
        'filesystem'    => 'N.A',
    );
}
else
{
    $mounted_points = array();
    $key = 0;

    foreach ($df as $mounted)
    {
        list($filesystem, $type, $total, $used, $free, $percent, $mount) = explode(',', $mounted);

        if ($percent > 100)
            $percent = 100;

        if (strpos($type, 'tmpfs') !== false && $config->get('disk:show_tmpfs') === false)
            continue;

        if (!in_array($mount, $mounted_points))
        {
            $mounted_points[] = trim($mount);

            $datas[$key] = array(
                'total'         => Misc::getSize($total * 1024),
                'used'          => Misc::getSize($used * 1024),
                'free'          => Misc::getSize($free * 1024),
                'percent_used'  => trim($percent, '%'),
                'mount'         => $mount,
            );

            if ($config->get('disk:show_filesystem'))
                $datas[$key]['filesystem'] = $filesystem;
        }

        $key++;
    }

}


if (!isset($_SERVER['argv']) || !in_array('--quiet', $_SERVER['argv']))
	echo json_encode($datas);
