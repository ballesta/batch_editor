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
    var $title;
    var $id_key;
    var $explications;

    var $relations_one_to_many=[];
    var $belongs_to=[];

    function __construct($nom, $title, $id_key, $identifier, $explications)
    {
        echo "Crée Module $nom ($explications)<br>";
        $this->nom          = $nom;
        $this->title          = $title;
        $this->id_key       = $id_key;
        $this->identifier   = $identifier;
        $this->explications = $explications;
    }
}