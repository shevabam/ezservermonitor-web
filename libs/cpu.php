<?php
require __DIR__.'/../autoload.php';
$config = Config::instance();

// Number of cores
$num_cores = Misc::getCpuCoresNumber();


// CPU info
$model      = 'N.A';
$frequency  = 'N.A';
$cache      = 'N.A';
$bogomips   = 'N.A';
$temp       = 'N.A';

if ($cpuinfo = Misc::shellexec('cat /proc/cpuinfo'))
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

if ($frequency == 'N.A')
{
    $cmd = $config->get('cpu:cmdMaxFreq');
    if ($f = Misc::shellexec($cmd))
    {
        $f = $f / 1000;
        $frequency = $f.' MHz';
    }
}

// CPU Temp
if ($config->get('cpu:enable_temperature'))
{
	$cmd = $config->get('cpu:cmdTemperature');
    if (Misc::exec($cmd, $t))
    {
        if (isset($t[0]))
            $temp = $t[0].' °C';
    }
    else
    {
        $cmd = $config->get('cpu:cmdThermal');
        if (Misc::exec($cmd, $t))
        {
            $temp = round($t[0] / 1000).' °C';
        }
    }
}


$datas = array(
    'model'      => $model,
    'num_cores'  => $num_cores,
    'frequency'  => $frequency,
    'cache'      => $cache,
    'bogomips'   => $bogomips,
    'temp'       => $temp,
);

echo json_encode($datas);
