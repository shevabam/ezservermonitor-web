<?php
require __DIR__.'/../autoload.php';
$config = Config::instance();

$datas = array();

if ($config->get('last_login:enable'))
{
    if (!(Misc::exec($config->get('last_login:cmd'), $users)))
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

if (!isset($_SERVER['argv']) || !in_array('--quiet', $_SERVER['argv']))
	echo json_encode($datas);
