<?php

/**
 * Created by PhpStorm.
 * User: bernard
 * Date: 29/09/2016
 * Time: 13:19
 */
class Has_many
{
    var $explications;
    var $module_detail;

    function __construct($explications, Module $module_detail)
    {
        echo "Crée Has_many $explications <br>";

        $this->$explications = $explications;
        $this->module_detail = $module_detail;
    }
}