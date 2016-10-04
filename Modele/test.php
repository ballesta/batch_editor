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

    $m = new Modele('football', 'Complexes sportifs de Football en salle');

    // Modules
    $rs = new Module('reseauxsalles', 'club_id', 'Réseaux de complexes sportifs');
    $cs = new Module('complexesportif', 'complexe_salle_id', 'Locations de terrains de football indoors');

    // Relation has many
    $rs_hm_cs = new Has_many($rs, 'Complexes sportifs gérés par ce réseau', $cs);
    $rs->relations_one_to_many[] = $rs_hm_cs;

    // Ajoute modules au modèle
    $m->modules[] = $rs;
    $m->modules[] = $cs;

    //$m->affiche();
    // Location of source code to enhance.
    $local_laravel_site = 'H:\wamp-3-32\www\ms_football_salles\2-site';
    $g = new Sixmo_code_generator($local_laravel_site);
    $m->generate_code($g);
?>
