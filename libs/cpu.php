<?php
require '../autoload.php';
$Config = new Config();

// Number of cores
$num_cores = Misc::getCpuCoresNumber();


// CPU info
$model      = 'N.A';
$frequency  = 'N.A';
$cache      = 'N.A';
$bogomips   = 'N.A';
$temp       = 'N.A';

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

if ($frequency == 'N.A')
{
    if ($f = shell_exec('cat /sys/devices/system/cpu/cpu0/cpufreq/cpuinfo_max_freq'))
    {
        $f = $f / 1000;
        $frequency = $f.' MHz';
    }
}

// CPU Temp
if ($Config->get('cpu:enable_temperature'))
{
    if (exec('/usr/bin/sensors | grep -E "^(CPU Temp|Core 0)" | cut -d \'+\' -f2 | cut -d \'.\' -f1', $t))
    {
        if (isset($t[0]))
            $temp = $t[0].' °C';
    }
    else
    {
        if (exec('cat /sys/class/thermal/thermal_zone0/temp', $t))
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