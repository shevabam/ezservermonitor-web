<?php
define('ROOTPATH', __DIR__);

function eSMAutoload($class)
{
    include __DIR__.'/libs/Utils/'.$class.'.php';
}

spl_autoload_register('eSMAutoload');
// for debug purpose
// error_reporting(E_ALL);
// ini_set('display_errors', 'On');
