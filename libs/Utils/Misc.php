<?php

class Misc
{
    public static $cache = true;
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
     * Seconds to human readable text
     * Eg: for 36545627 seconds => 1 year, 57 days, 23 hours and 33 minutes
     * 
     * @return string Text
     */
    public static function getHumanTime($seconds)
    {
        $units = array(
            'year'   => 365*86400,
            'day'    => 86400,
            'hour'   => 3600,
            'minute' => 60,
            // 'second' => 1,
        );
     
        $parts = array();
     
        foreach ($units as $name => $divisor)
        {
            $div = floor($seconds / $divisor);
     
            if ($div == 0)
                continue;
            else
                if ($div == 1)
                    $parts[] = $div.' '.$name;
                else
                    $parts[] = $div.' '.$name.'s';
            $seconds %= $divisor;
        }
     
        $last = array_pop($parts);
     
        if (empty($parts))
            return $last;
        else
            return join(', ', $parts).' and '.$last;
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

            if (!$handle)
            {
                return false;
            }
            else
            {
                fclose($handle);
                return true;
            }
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

    public static function exec($command, &$output, &$return_var = null)
    {
        $config = Config::instance();
        $output = Misc::cache($command);
        //echo "on passe par Misc::exec";
        if ($output)
        {
            //echo "found in cache";
            return $output;
            // exec
        }
        elseif ($config->get('esm:mode') == 'cron' && PHP_SAPI != 'cli')
        {
            //echo "mode CRON absent\n";
            return false;
        }
        else
        {
            //echo "On EXECUTE\n";
            if ($return_var) {
                $return = exec($command, $output, $return_var);
                if ($return_var != 0)
                {
                    //echo "\n\tERROR with $command\n\n";
                    header('HTTP/1.0 500 Server Error');
                    return false;
                }
            }
            else {
                $return = exec($command, $output);
            }

            if (isset($_SERVER['argv']) && in_array('--save', $_SERVER['argv']))
            {
                $written = Misc::cache($command, $output);
            }
        }
        return $return;
    }

    public static function shellexec($command, $args = null)
    {
        $config = Config::instance();
        $output = Misc::cache($command.$args);
        if ($output)
        {
            //echo "$command.$args found in cache<br>";
            //var_dump($output);
            return $output;
        }
        elseif ($config->get('esm:mode') == 'cron' && PHP_SAPI != 'cli')
        {
            return null;
        }
        else
        {
            $output = shell_exec($command.$args);
            if (is_array($output))
            {
                die("wtf output of shell_exec is an array ???");
            }
            else
            {
                //echo "output is a string: $output";
            }
            if (isset($_SERVER['argv']) && in_array('--save', $_SERVER['argv']))
                Misc::cache($command.$args, $output);
        }
        return $output;
    }

    static public function cache($name, $data = null, $lifetime = 0)
    {
        if (empty($name))
            throw new Exception("invalid cache key");
        $config = Config::instance();
        $salt   = $config->get('esm:salt');
        $file = sha1($name.$salt).'.txt';
        //echo "$name \n<br>\t$file\n<br>";
        $dir = ROOTPATH.DIRECTORY_SEPARATOR.$config->get('esm:cron_output_path').DIRECTORY_SEPARATOR;
        if ($data === NULL)
        {

            if (isset($_SERVER['argv']) && in_array('--save', $_SERVER['argv']))
            {
                //echo "<br>\n\nCACHE REMOVED\n\n<br>";
                Misc::$cache = false;
                return false;
            }


            if (is_file($dir.$file))
            {
                if (!Misc::$cache || !$lifetime || ((time() - filemtime($dir.$file)) < $lifetime))
                {
                    try
                    {
                        return unserialize(file_get_contents($dir.$file));
                    }
                    catch(Exception $e)
                    {
                    }
                }
                else
                {
                    try{
                        unlink($dir.$file);
                    }
                    catch(Exception $e)
                    {
                    }
                }
            }
            else
            {
                //echo "file not exists: <b>$name ($file)</b> \n<br>";
            }
            //echo "nothing found in cache for $name\n";
            return NULL;
        }
        $data = serialize($data);
        try
        {
            $written = file_put_contents($dir.$file, $data, LOCK_EX);
            //echo "\nWRITE for $name \n\tIN $file:\n\t\t$data\nENDOFWRITE";
            return (bool)$written;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    function ago($time)
    {
        $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
        $lengths = array("60","60","24","7","4.35","12","10");

        $now = time();

        $difference     = $now - $time;
        $tense         = "ago";

        for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
            $difference /= $lengths[$j];
        }

        $difference = round($difference);

        if($difference != 1) {
            $periods[$j].= "s";
        }

        return "$difference $periods[$j] 'ago' ";
    }
}

