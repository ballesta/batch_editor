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
    $rs = new Module('reseauxsalles'  ,'Réseaux de salles'  ,'club_id'
                    ,'Réseaux de complexes sportifs'            );
    $cs = new Module('complexesportif','Centres' ,'complexe_salle_id'
                    ,'Locations de terrains de football indoors');
    $s  = new Module('salle'          ,'Terrains'             ,'salle_id'
                    ,'salles indoors');
    $mc = new Module('malette'        ,'Malettes','malette_capteurs_id'
                     ,'Malette contenant 10 capteurs');
    $c  = new Module('capteur'        ,'Capteurs'           ,'capteurs_id'
                     ,'capteur contenu dans une malette');
    $e  = new Module('equipe'         ,'Equipes'            ,'equipe_id'
                     ,'Equipes de joueurs'                       );
    $j  = new Module('joueur'         ,'Joueurs'            ,'joueur_id'
                     ,'joueurs en équipe ou individuels'         );
    $p  = new Module('partie'         ,'Parties'            ,'partie_id'
                    ,'Partie d\'une équipe dans une salle'         );
    $js  = new Module('joueurselectionne'         ,'Joueurs','partie_id'
                    ,'Joueurs sélectionnés pour la partie'         );

    // Relation has many
    $rs_hm_cs = new Has_many($rs, 'Complexes sportifs gérés par ce réseau', $cs);
    $rs->relations_one_to_many[] = $rs_hm_cs;

    $cs_hm_mc = new Has_many($cs, 'Malettes du complexe sportif', $mc);
    $cs->relations_one_to_many[] = $cs_hm_mc;

    $cs_hm_s = new Has_many($cs, 'Salles du complexe sportif', $s);
    $cs->relations_one_to_many[] = $cs_hm_s;

    $mc_hm_c = new Has_many($mc, 'Capteurs contenus dans une malette', $c);
    $mc->relations_one_to_many[] = $mc_hm_c;

    $cs_hm_e = new Has_many($cs, 'Equipes', $e);
    $cs->relations_one_to_many[] = $cs_hm_e;

    $s_hm_p = new Has_many($s, 'Parties', $p);
    $s->relations_one_to_many[] = $s_hm_p;
    // ++++ Deux chemins pour atteindre les joueurs
    // ++++ Faut se rappeler par ou on arrive et utiliser le bon filtre
    // ++++ Enlever le filtre précédent à l'arrivée
    // ++++POur changer de filtre, revenir au niveau supérieur
    //$cs_hm_j = new Has_many($cs, 'Joueurs', $j);
    //$cs->relations_one_to_many[] = $cs_hm_j;

    $e_hm_j = new Has_many($e, 'Joueurs', $j);
    $e->relations_one_to_many[] = $e_hm_j;

    $p_hm_js = new Has_many($p, 'Joueurs Sélectionnés', $js);
    $p->relations_one_to_many[] = $p_hm_js;

    // Ajoute modules au modèle
    $m->modules[] = $rs;
    $m->modules[] = $cs;
    $m->modules[] = $mc;
    $m->modules[] = $c;
    $m->modules[] = $e;
    $m->modules[] = $j;
    $m->modules[] = $s;
    $m->modules[] = $p;
    $m->modules[] = $js;
    $m->affiche();
    // Location of source code to enhance.
    $local_laravel_site = 'H:\wamp-3-32\www\ms_football_salles\2-site';
    $g = new Sixmo_code_generator($local_laravel_site);
    $m->generate_code($g);
?>
