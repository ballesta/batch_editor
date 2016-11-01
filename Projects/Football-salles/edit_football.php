<?php
    /**
     * Created by PhpStorm.
     * User: bernard
     * Date: 29/09/2016
     * Time: 13:21
     */

    include '../../Modele/Modele.php';
    include '../../Modele/Module.php';
    include '../../Modele/Has_many.php';

    include '../../Generators/Sximo_code_generator.php';
    echo '<h1>Modèle de salles de football indoors</h1>';

    $modele = new Modele('football', 'Complexes sportifs de Football en salle');

    //-- Modules --//

    $rs = new Module('reseauxsalles', 'Réseau de salles', 'club_id', 'nom',
                     'Réseaux de complexes sportifs');

    $cs = new Module('complexesportif', 'Centre sportif', 'complexe_salle_id','nom',
                     'Locations de terrains de football indoors');

    $s =  new Module('salle'   , 'Terrain', 'salle_id', 'identifiant',
                     'Salles indoors');

    $csj =  new Module('joueurCentre'   , 'Joueur', 'joueur_id', 'nom',
                       'Joueurs du centre');

    $mc = new Module('malette', 'Malette', 'malette_capteurs_id', 'identifiant',
                     'Malette contenant 10 capteurs');

    $c =  new Module('capteur' , 'Capteur', 'capteurs_id', 'numero_serie',
                     'Capteur contenu dans une malette');

    $e =  new Module('equipe'  , 'Equipe', 'equipe_id', 'nom',
                     'Equipes de joueurs');

    $j =  new Module('joueur'  , 'Joueurs', 'joueur_id', 'nom',
                     'Joueurs en équipe ou individuels');

    $p =  new Module('partie'  , 'Partie', 'partie_id', 'debut',
                     'Partie d\'une équipe dans une salle');

    $js = new Module('joueurselectionne', 'Joueur sélectionné',
	                 'joueur_selectionne_id', 'joueur_id',
                     'Joueurs sélectionnés pour la partie');

    $sm = new Module('sessionmesure', 'Sessions mesures', 'session_mesure_id',
                     'date_heure',
                     'Session de mesures avec un capteur');

    $mesure = new Module('mesure', 'Mesures', 'mesure_id', '',
                         'Mesures de la partie');

    //-- Relations 'has many' --//

    $rs_hm_cs = new Has_many($rs, 'Complexes sportifs gérés par ce réseau', $cs);
    $rs->relations_one_to_many[] = $rs_hm_cs;

    $cs_hm_mc = new Has_many($cs, 'Malettes du complexe sportif', $mc);
    $cs->relations_one_to_many[] = $cs_hm_mc;

    $cs_hm_s = new Has_many($cs, 'Salles du complexe sportif', $s);
    $cs->relations_one_to_many[] = $cs_hm_s;


    $cs_hm_csj = new Has_many($cs, 'Joueurs du complexe sportif', $csj);
    $cs->relations_one_to_many[] = $cs_hm_csj;

    $mc_hm_c = new Has_many($mc, 'Capteurs contenus dans la malette', $c);
    $mc->relations_one_to_many[] = $mc_hm_c;

    $cs_hm_e = new Has_many
        ($cs,
         'Equipes de joueurs pratiquant régulièrement ensemble',
         $e);
    $cs->relations_one_to_many[] = $cs_hm_e;

    $s_hm_p = new Has_many($s, 'Parties ayant eu lieu sur ce terrain', $p);
    $s->relations_one_to_many[] = $s_hm_p;
    // ++++ Deux chemins pour atteindre les joueurs
    // ++++ Faut se rappeler par ou on arrive et utiliser le bon filtre
    // ++++ Enlever le filtre précédent à l'arrivée
    // ++++POur changer de filtre, revenir au niveau supérieur
    //$cs_hm_j = new Has_many($cs, 'Joueurs', $j);
    //$cs->relations_one_to_many[] = $cs_hm_j;

    $e_hm_j = new Has_many($e, 'Joueurs membres de l\'equipe (jouent fréquement ensembles)', $j);
    $e->relations_one_to_many[] = $e_hm_j;

    $p_hm_js = new Has_many($p, 'Joueurs Sélectionnés pour la partie', $js);
    $p->relations_one_to_many[] = $p_hm_js;

    $js_hm_sm = new Has_many($js,
                             'Session mesures avec le même capteur ' .
                             '(Chaque changement de capteur donne lieu '.
                             'à une nouvelle session)',
                             $sm);
    $js->relations_one_to_many[] = $js_hm_sm;

    $sm_hm_m = new Has_many($sm, 'Mesures enregistrées au cours de la session', $mesure);
    $sm->relations_one_to_many[] = $sm_hm_m;

    // Ajoute modules au modèle
    $modele->modules[] = $rs;
    $modele->modules[] = $cs;
    $modele->modules[] = $mc;
    $modele->modules[] = $c;
    $modele->modules[] = $e;
    $modele->modules[] = $j;
    $modele->modules[] = $s;
    $modele->modules[] = $p;
    $modele->modules[] = $js;
    $modele->modules[] = $sm;
    $modele->modules[] = $mesure;
    $modele->affiche();
    // Location of source code to enhance.
    $local_laravel_site = 'H:\wamp-3-32\www\ms_football_salles\2-site';
    $g = new Sixmo_code_generator($local_laravel_site);
	//$modele->compile();
	$modele->breadcrumb();
    $modele->generate_code($g);
?>