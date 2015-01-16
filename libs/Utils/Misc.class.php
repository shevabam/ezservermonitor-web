<?php

class Misc
{
    /**
     * Returns human size
     */
    public static function getSize($filesize, $precision = 2)
    {
        $units = array('', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y');

        foreach ($units as $idUnit => $unit)
        {
            if ($filesize > 1024)
                $filesize /= 1024;
            else
                break;
        }
        
        return round($filesize, $precision).' '.$units[$idUnit].'B';
    }
    
    
    /**
     * Returns hostname
     */
    public static function getHostname()
    {
        return php_uname('n');
    }


    /**
     * Returns CPU cores number
     */
    public static function getCpuCoresNumber()
    {
        if (!($num_cores = shell_exec('/bin/grep -c ^processor /proc/cpuinfo')))
        {
            if (!($num_cores = trim(shell_exec('/usr/bin/nproc'))))
            {
                $num_cores = 1;
            }
        }

        if ((int)$num_cores <= 0)
            $num_cores = 1;

        return (int)$num_cores;
    }


    /**
     * Returns server IP
     */
    public static function getLanIp()
    {
        return $_SERVER['SERVER_ADDR'];
    }


    /**
     * Returns a command that exists in the system among $cmds
     */
    public static function whichCommand($cmds, $args = '', $returnWithArgs = true)
    {
        $return = '';

        foreach ($cmds as $cmd)
        {
            if (trim(shell_exec($cmd.$args)) != '')
            {
                $return = $cmd;
                
                if ($returnWithArgs)
                    $return .= $args;

                break;
            }
        }

        return $return;
    }


    /**
     * Allows to pluralize a word based on a number
     * Ex : echo 'mot'.Misc::pluralize(5); ==> prints mots
     * Ex : echo 'cheva'.Misc::pluralize(5, 'ux', 'l'); ==> prints chevaux
     * Ex : echo 'cheva'.Misc::pluralize(1, 'ux', 'l'); ==> prints cheval
     * 
     * @param int $nb
     * @param string $plural
     * @param string $singular
     * 
     * @return string
     */
    public static function pluralize($nb, $plural = 's', $singular = '')
    {
        return $nb > 1 ? $plural : $singular;
    }
}