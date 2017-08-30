<?php
session_start();
if (!isset($_SESSION["username"]) && !in_array(basename(get_included_files()[0]),array("index.php"))) exit();

function eSMAutoload($class)
{
    include __DIR__.'/libs/Utils/'.$class.'.php';
}

spl_autoload_register('eSMAutoload');