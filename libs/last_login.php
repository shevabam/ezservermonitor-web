<?php
require __DIR__.'/../autoload.php';
$config = Config::instance();

$datas = array();

if ($config->get('last_login:enable'))
{
    if (!(exec('/usr/bin/lastlog --time 365 | awk \'{ printf $1";"; for (i=4; i<NF; i++) printf $i" "; print $NF; }\'', $users)))
    {
        $datas[] = array(
            'user' => 'N.A',
            'date' => 'N.A',
        );
    }
    else
    {
        $max = $config->get('last_login:max');

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
