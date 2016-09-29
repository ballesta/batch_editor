<?php
/**
 * Created by PhpStorm.
 * User: bernard
 * Date: 29/09/2016
 * Time: 13:21
 */

include 'Modele.php';
include 'Module.php';
include 'Has_many.php';

include '../Generators/Sximo_code_generator.php';
echo '<h1>Test modèles</h1>';

$m = new Modele('football','COmplexes sportifs de Football en salle');

// Modules
$rs = new Module('reseauxsalles','Réseaux de complexes sportifs');
$cs = new Module('complexesportif','Locations de terrains de football indoors');
$m->modules[]=$rs;
$m->modules[]=$cs;

// Relation has many
$rs_hm_cs = new Has_many('Complexes sportifs gérés par ce réseau', $cs);
$rs->relations_one_to_many[] = $rs_hm_cs;

$m->affiche();
$g = new Sixmo_code_generator();
$m->generate_code($g);