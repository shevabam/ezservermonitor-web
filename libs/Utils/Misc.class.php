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

    public static function getListServer() {
        $output = '';
        foreach ($_SESSION['serverList'] as $server) {
            if ($_SESSION['server'] == $server) {
                $output .= '<option value="'. $server .'" SELECTED>'. $server .'</option>';
            } else {
                $output .= '<option value="'. $server .'">'. $server .'</option>';
            }
        }
        return $output;
    }
    
    /**
     * Returns hostname
     */
    public static function getHostname()
    {
        return self::execShellServer('hostname');
    }

    /**
     * Returns server IP
     */
    public static function getLanIp()
    {
        $cmd = self::execShellServer('hostname -I | awk \'{print $1}\'');
        if (strpos($cmd, '127.')!== false) {
            $cmd = self::execShellServer('hostname -I | awk \'{print $2}\'');
        }
        return $cmd;
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
    
    /**
     * 
     * @param string $cmd
     * @return string
     */
    public function execShellServer($cmd) {
        if ($_SESSION['server'] != 'localhost') {
            $cmd = 'ssh root@' . $_SESSION['server'] .' ' . $cmd;
        }
        return shell_exec($cmd);
    }
    
    public function execServer($cmd) {
        if ($_SESSION['server'] != 'localhost') {
            $cmd = 'ssh root@' . $_SESSION['server'] .' ' . $cmd;
        }
        exec($cmd, $ret);
        return $ret;
    }
}