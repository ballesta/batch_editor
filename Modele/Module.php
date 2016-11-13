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
    var $relations_belongs_to_one=[];

    function __construct($nom, $title, $table, $id_key, $identifier, $explications)
    {
        echo "Crée Module $nom ($explications)<br>";
        $this->nom          = $nom;
        $this->title        = $title;
	    $this->table        = $table;
	    $this->id_key       = $id_key;
        $this->identifier   = $identifier;  // For Breadcrumb
        $this->explications = $explications;

	    $this->relations_one_to_many   =[];
	    $this->relations_belongs_to_one=[];
    }

	function breadcrumb_ascendants(Module $m)
	{
		//echo "<br>Breadcrum Modèle $m->nom<hr>";
		$ascendants = [];
		//var_dump($this->relations_belongs_to_one);
		foreach ($m->relations_belongs_to_one as $a)
		{
			echo "----1..1 $a->nom<br>";
			$aa = $this->breadcrumb_ascendants($a);
			//echo '$aa';
			//var_dump($aa);
			$ascendants = array_merge($ascendants, [$a], $aa);
		}
		echo '$ascendants';
		//var_dump($ascendants);
		return $ascendants;
	}

	function breadcrumb_descendants(Module $m)
	{
		//echo "<br>Breadcrum Modèle $m->nom<hr>";
		$descendants = [];
		//var_dump($this->relations_belongs_to_one);
		foreach ($m->relations_one_to_many as $hm)
		{
			// Module descendant direct
			$mdd= $hm->module_detail;
			//echo "----1..* $mdd->nom<br>";
			$descendants_1 = $this->breadcrumb_descendants($mdd);
			//echo '$aa';
			//var_dump($aa);
			$descendants = array_merge($descendants, [$mdd], $descendants_1);
		}
		return $descendants;
	}


}