<?php

function eSMAutoload($class)
{
    include __DIR__.'/libs/Utils/'.$class.'.php';
}

spl_autoload_register('eSMAutoload');