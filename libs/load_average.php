<?php

if (!($load_tmp = shell_exec('/bin/cat /proc/loadavg | /usr/bin/awk \'{print $1","$2","$3}\'')))
{
    $load = array(0, 0, 0);
}
else
{
    $load_exp = explode(',', $load_tmp);

    $load = array_map(
        function ($value) {
            $v = (int)($value * 100);
            if ($v > 100)
                $v = 100;
            return $v;
        }, 
        $load_exp
    );
}


$datas = $load;

echo json_encode($datas);