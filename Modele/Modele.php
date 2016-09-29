<?php

/**
 * Created by PhpStorm.
 * User: bernard
 * Date: 29/09/2016
 * Time: 13:17
 */
class Modele
{
    var $nom;
    var $explications;
    var $modules=[];

    function __construct($nom, $explications)
    {
        echo "Crée Modèle $nom ($explications)<br>";
        $this->nom          = $nom;
        $this->explications = $explications;
    }

    function affiche()
    {
        echo "<br>Modèle $this->nom<hr>";
        foreach ($this->modules as $m)
        {
            echo "--Module $m->nom<br>";
            foreach ($m->relations_one_to_many as $r)
            {
                $nom_module = $r->module_detail->nom;
                // Relation inverse ++++
                echo "----Has many $nom_module<br>";
            }
        }
    }

    function generate_code(Code_generator $g)
    {
        $g->modele_begin($this);
        echo "<br>Modèle $this->nom<hr>";
        foreach ($this->modules as $m)
        {
            $g->module_begin($this,$m);
            echo "--Module $m->nom<br>";
            foreach ($m->relations_one_to_many as $r)
            {
                $g->has_many_begin($this,$m,$r);
                $nom_module = $r->module_detail->nom;
                // Relation inverse ++++
                echo "----Has many $nom_module<br>";
                $g->has_many_end($this,$m,$r);
            }
            $g->module_end($this,$m);
        }
        $g->modele_end($this);
    }

}