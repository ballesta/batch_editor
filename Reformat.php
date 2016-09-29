<?php

/**
 * Created by PhpStorm.
 * User: bernard
 * Date: 28/09/2016
 * Time: 19:41
 */


class Reformat
{
    var $tidy;
    var $config;

    function __construct($file_to_format)
    {
        // Configuration
        $this->config = array(
            'indent'         => true,
            'output-xhtml'   => true,
            'wrap'           => 200);
        $this->tidy = new Tidy($file_to_format, $this->config, 'utf8');
        $this->tidy->cleanRepair();
        if ($this->tidy->errorBuffer) {
            echo "Les erreurs suivantes ont été détectées :\n";
            echo $this->tidy->errorBuffer;
        }
        echo $this->tidy;
    }

    function process($html)
    {
        $this->tidy->parseString($html, $this->config, 'utf8');
        $this->tidy->cleanRepair();
    }
}