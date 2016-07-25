<?php

class Config
{
    public $file = null;
    public $config = null;

    private static $instance = null;

    public static function instance(){
        if (!Config::$instance)
        {
            Config::$instance = new Config();
        }
        return Config::$instance;
    }

    private function __construct()
    {
        $this->_checkPHPVersion(5.3);

        $this->file = __DIR__.'/../../conf/esm.config.json';

        if (!file_exists($this->file))
            throw new \Exception('Config file '.basename($this->file).' not found');

        $this->_readFile();
    }

    private function _readFile()
    {
        $content = file_get_contents($this->file);
        $this->config = json_decode(utf8_encode($content), true);

        if ($this->config == null && json_last_error() != JSON_ERROR_NONE)
        {
            throw new \LogicException(sprintf("Failed to parse config file '%s'. Error: '%s'", basename($this->file) , json_last_error_msg()));
        }
    }


    /**
     * Returns a specific config variable
     * Ex : get('ping:hosts')
     */
    public function get($var)
    {
        $tab = $this->config;
        
        $explode = explode(':', $var);
        
        foreach ($explode as $vartmp)
        {
            if (isset($tab[$vartmp]))
            {
                $tab = $tab[$vartmp];
            }
        }

        // return $tab == $this->config ? null : $tab;
        return $tab;
    }
    
    
    /**
     * Returns all config variables
     */
    public function getAll()
    {
        return $this->config;
    }


    /**
     * Checks the PHP version compared to the required version
     */
    private function _checkPHPVersion($min)
    {
        if (!version_compare(phpversion(), $min, '>='))
            throw new \Exception('Your PHP version is too old ! PHP '.$min.' is required.');

        return true;
    }


    /**
     * Checks if there is an eSM`Web update available
     */
    public function checkUpdate()
    {
        if ($this->get('esm:check_updates') === false)
            return null;
        
        $response       = null;
        $this_version   = $this->get('esm:version');
        $update_url     = $this->get('esm:website').'/esm-web/update/'.$this_version;

        if (!function_exists('curl_version'))
        {
            $tmp = @file_get_contents($update_url);
            $response = json_decode($tmp, true);
        }
        else
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_CONNECTTIMEOUT  => 10,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_SSL_VERIFYPEER  => false,
                CURLOPT_TIMEOUT         => 60,
                CURLOPT_USERAGENT       => 'eZ Server Monitor `Web',
                CURLOPT_URL             => $update_url,
            ));

            $response = json_decode(curl_exec($curl), true);

            curl_close($curl);
        }

        if (!is_null($response) && !empty($response))
        {
            if (is_null($response['error']))
            {
                return $response['datas'];
            }
        }
    }
}


// PHP 5.5.0
if (!function_exists('json_last_error_msg'))
{
    function json_last_error_msg()
    {
        static $errors = array(
            JSON_ERROR_NONE             => null,
            JSON_ERROR_DEPTH            => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH   => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR        => 'Unexpected control character found',
            JSON_ERROR_SYNTAX           => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded'
        );
        $error = json_last_error();
        return array_key_exists($error, $errors) ? $errors[$error] : "Unknown error ({$error})";
    }
}
