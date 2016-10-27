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

    // Compiles model at end of definition
    function compile()
    {
	    echo "<br>Modèle $this->nom<hr>";
	    foreach ($this->modules as $m)
	    {
		    echo "--Module $m->nom<br>";
		    $m->relations_belongs_to_one = [];
		    foreach ($m->relations_one_to_many as $r)
		    {
			    $nom_module = $r->module_detail->nom;
			    // Relation inverse de détail vers parent
			    $r->module_detail->relations_belongs_to_one[] = $m;
			    echo "----Has many $nom_module<br>";
		    }
	    }
    }

	function breadcrumb()
	{
		echo "<br>Breadcrum Modèle $this->nom<hr>";
		foreach ($this->modules as $m)
		{
			echo "--Module $m->nom<br>";
			foreach ($m->relations_belongs_to_one as $r)
			{
				$nom_module = $r->nom;
				echo "----1..1 $nom_module<br>";
			}
		}
	}

	function breadcrumb_ascendants(Module $m)
	{
		//echo "<br>Breadcrum Modèle $m->nom<hr>";
	    $ascendants = [];
		foreach ($m->relations_belongs_to_one as $a)
		{
			echo "----1..1 $a->nom<br>";

			$ascendants = array_merge($ascendants,
				                      $this->breadcrumb_ascendants($a),
				                      [$a->nom]) ;
			var_dump($ascendants);
		}
		return $ascendants;
	}

    // Visite le modèle et appell le générateur de code pour chaque entité rencontrée.
    function generate_code(Code_generator $g)
    {
        $g->modele_begin($this);
        //echo "<br>Modèle $this->nom<hr>";
        foreach ($this->modules as $m)
        {
            $g->module_begin($this,$m);
            //echo "--Module $m->nom<br>";
            foreach ($m->relations_one_to_many as $r)
            {
                $g->has_many_begin($this,$m,$r);
                $nom_module = $r->module_detail->nom;
                $g->has_many_end($this,$m,$r);
            }
            foreach ($m->relations_belongs_to_one as $bt)
            {
                $g->belongs_to_begin($this,$m,$bt);
                $nom_module = $r->module_detail->nom;
                $g->belongs_to_end($this,$m,$bt);
            }
            $g->module_end($this,$m);
        }
        $g->modele_end($this);
    }
}