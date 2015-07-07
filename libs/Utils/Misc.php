<?php

class Misc
{
    /**
     * Returns human size
     *
     * @param  float $filesize   File size
     * @param  int   $precision  Number of decimals
     * @return string            Human size
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
     *
     * @return  string  Hostname
     */
    public static function getHostname()
    {
        return php_uname('n');
    }


    /**
     * Returns CPU cores number
     * 
     * @return  int  Number of cores
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
     *
     * @return string Server local IP
     */
    public static function getLanIp()
    {
        return $_SERVER['SERVER_ADDR'];
    }


    /**
     * Returns a command that exists in the system among $cmds
     *
     * @param  array  $cmds             List of commands
     * @param  string $args             List of arguments (optional)
     * @param  bool   $returnWithArgs   If true, returns command with the arguments
     * @return string                   Command
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
     * @param  int       $nb         Number
     * @param  string    $plural     String for plural word
     * @param  string    $singular   String for singular word
     * @return string                String pluralized
     */
    public static function pluralize($nb, $plural = 's', $singular = '')
    {
        return $nb > 1 ? $plural : $singular;
    }


    /**
     * Checks if a port is open (TCP or UPD)
     *
     * @param  string   $host       Host to check
     * @param  int      $port       Port number
     * @param  string   $protocol   tcp or udp
     * @param  integer  $timeout    Timeout
     * @return bool                 True if the port is open else false
     */
    public static function scanPort($host, $port, $protocol = 'tcp', $timeout = 3)
    {
        if ($protocol == 'tcp')
        {
            $handle = @fsockopen($host, $port, $errno, $errstr, $timeout);

            if ($handle)
                return true;
            else
                return false;
        }
        elseif ($protocol == 'udp')
        {
            $handle = @fsockopen('udp://'.$host, $port, $errno, $errstr, $timeout);

            socket_set_timeout($handle, $timeout);

            $write = fwrite($handle, 'x00');

            $startTime = time();

            $header = fread($handle, 1);

            $endTime = time();

            $timeDiff = $endTime - $startTime; 
            
            fclose($handle);

            if ($timeDiff >= $timeout)
                return true;
            else
                return false;
        }

        return false;
    }
}