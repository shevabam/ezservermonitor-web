<?php

// Number of cores
if (!($num_cores = shell_exec('/bin/grep -c ^processor /proc/cpuinfo')))
{
    $num_cores = 'N.A';
}


// CPU info
if (!($cpuinfo = shell_exec('cat /proc/cpuinfo')))
{
    $model      = 'N.A';
    $frequency  = 'N.A';
    $cache      = 'N.A';
    $bogomips   = 'N.A';
}
else
{
    $processors = preg_split('/\s?\n\s?\n/', trim($cpuinfo));

    foreach ($processors as $processor)
    {
        $details = preg_split('/\n/', $processor, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($details as $detail)
        {
            list($key, $value) = preg_split('/\s*:\s*/', trim($detail));

            switch ($key)
            {
                case 'model name':
                case 'cpu model':
                case 'cpu':
                    $model = $value;
                break;

                case 'cpu MHz':
                case 'clock':
                    $frequency = $value.' MHz';
                break;

                case 'cache size':
                case 'l2 cache':
                    $cache = $value;
                break;

                case 'bogomips':
                case 'BogoMIPS':
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
