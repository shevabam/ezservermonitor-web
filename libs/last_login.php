<?php
require '../autoload.php';
$Config = new Config();


$datas = array();

if ($Config->get('last_login:enable'))
{
    if (!(exec('/usr/bin/lastlog --time 365 | /usr/bin/awk -F\' \' \'{ print $1" ("$3");"$5, $6, $9, $7}\'', $users)))
    {
        $datas[] = array(
            'user' => 'N.A',
            'date' => 'N.A',
        );
    }
    else
    {
        $max = $Config->get('last_login:max');

        for ($i = 1; $i < count($users) && $i <= $max; $i++)
        {
            list($user, $date) = explode(';', $users[$i]);

            $datas[] = array(
                'user' => $user,
                'date' => $date,
            );
        }
    }
}

echo json_encode($datas);