<?php
require __DIR__.'/../autoload.php';
$config = Config::instance();

if (isset($_SERVER['argv']) && in_array('--save', $_SERVER['argv']))
{
    $time = time();
    Misc::cache('last_cron', $time, 0);
}

$time = Misc::cache('last_cron');
if (!isset($_SERVER['argv']) || !in_array('--quiet', $_SERVER['argv']))
    echo json_encode(time()-$time);
