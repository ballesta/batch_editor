<?php

/**
 * Created by PhpStorm.
 * User: bernard
 * Date: 29/09/2016
 * Time: 13:13
 */
class Module
{
    var $nom;
    var $explications;
    var $relations_one_to_many=[];

    function __construct($nom, $explications)
    {
        echo "Crée Module $nom ($explications)<br>";
        $this->nom          = $nom;
        $this->explications = $explications;
    }
}