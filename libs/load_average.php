<?php
require __DIR__.'/../autoload.php';

$config = Config::instance();

if (!($load_tmp = Misc::shellexec($config->get('load_average:cmd'))))
{
    $load = array(0, 0, 0);
}
else
{
    // Number of cores
    $cores = Misc::getCpuCoresNumber();

    $load_exp = explode(' ', $load_tmp);
    //var_dump($load_exp);

    $load = array_map(
        function ($value, $cores) {
            $v = ($value * 100 / $cores);
            if ($v > 100)
                $v = 100;
            return $v;
        }, 
        $load_exp,
        array_fill(0, 3, $cores)
    );
}


$datas = $load;

echo json_encode($datas);