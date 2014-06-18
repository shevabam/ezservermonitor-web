<?php

class Config
{
    public $file = null;
    public $config = null;

    public function __construct()
    {
        $this->file = $_SERVER['DOCUMENT_ROOT'].'/esm.config.json';

        if (file_exists($this->file))
            $this->_readFile();
    }

    private function _readFile()
    {
        $content = file_get_contents($this->file);
        $this->config = json_decode($content, true);
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

        return $tab == $this->config ? null : $tab;
    }
    
    
    /**
     * Returns all config variables
     */
    public function getAll()
    {
        return $this->config;
    }


    /**
     * Checks if there is an eSM`Web update available
     */
    public function checkUpdate()
    {
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