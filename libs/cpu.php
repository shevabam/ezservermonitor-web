<?php
require 'Utils/Misc.class.php';

// Number of cores
$num_cores = Misc::getCpuCoresNumber();


// CPU info
$model      = 'N.A';
$frequency  = 'N.A';
$cache      = 'N.A';
$bogomips   = 'N.A';

if ($cpuinfo = shell_exec('cat /proc/cpuinfo'))
{
    $processors = preg_split('/\s?\n\s?\n/', trim($cpuinfo));

    foreach ($processors as $processor)
    {
        $details = preg_split('/\n/', $processor, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($details as $detail)
        {
            list($key, $value) = preg_split('/\s*:\s*/', trim($detail));

            switch (strtolower($key))
            {
                case 'model name':
                case 'cpu model':
                case 'cpu':
                case 'processor':
                    $model = $value;
                break;

                case 'cpu mhz':
                case 'clock':
                    $frequency = $value.' MHz';
                break;

                case 'cache size':
                case 'l2 cache':
                    $cache = $value;
                break;

                case 'bogomips':
                    $bogomips = $value;
                break;
            }
        }
    }
}


$datas = array(
    'model'      => $model,
    'num_cores'  => $num_cores,
    'frequency'  => $frequency,
    'cache'      => $cache,
    'bogomips'   => $bogomips,
);

echo json_encode($datas);