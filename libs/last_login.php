<?php
require 'Utils/Config.class.php';
$Config = new Config();


$datas = array();

if (!(exec('/usr/bin/lastlog --time 365 | /usr/bin/awk \'{print $1","$3","$4" "$5" "$6" "$7" "$8}\'', $users)))
{
    $datas[] = array(
        'user' => 'N.A',
        'host' => 'N.A',
        'date' => 'N.A',
    );
}
else
{
    $max = $Config->get('last_login:max');

    for ($i = 1; $i < count($users) && $i <= $max; $i++)
    {
        list($user, $host, $date) = explode(',', $users[$i]);

        $datas[] = array(
            'user' => $user,
            'host' => $host,
            'date' => $date,
        );
    }
}

echo json_encode($datas);