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
    echo '<h1>Application de salles de football indoors</h1>';

	// 1-Crée le modèle de l'application
	$modele = cree_modele_football();

	// 2-Génère l'application
    // Location of source code to enhance.
	// 'H:\wamp-3-32\www\ms_football_salles\2-site';

    $local_laravel_site =  'H:\wamp-3-32\www\ms_football_salles\2-site';

    $g = new Sixmo_code_generator($local_laravel_site);
	//$modele->compile();
	$modele->breadcrumb();
    $modele->generate_code($g);


	function cree_modele_football()
	{
		$modele = new Modele('football', 'Complexes sportifs de Football en salle');

		//-- Modules --//

		$rs = new Module('reseauxsalles', 'Réseau de salles',
						 'fbs_reseaux_salles','club_id', 'nom',
						 'Réseaux de complexes sportifs');

		$cs = new Module('complexesportif', 'Centres sportifs',
						 'fbs_complexe_salles', 'complexe_salle_id','nom',
						 'Locations de terrains de football indoors');

		$aj = new Module('accueiljoueurs', 'Accueil Joueurs',
			'fbs_inscription', 'inscription_id', '',
			'Inscription match et remise des capteurs par l\'accueil');

		$s =  new Module('salle'   , 'Terrains',
						 'fbs_salles', 'salle_id', 'identifiant',
						 'Terrains indoors');

		$csj =  new Module('joueurCentre'   , 'Joueurs',
						   'fb_joueurs', 'joueur_id', 'nom',
			               'Joueurs du centre');

		//$mc = new Module('malette', 'Malette', 'malette_capteurs_id', 'identifiant',
		//                 'Malette contenant 10 capteurs');

		$c =  new Module('capteur' , 'Capteurs',
						 'fb_capteurs','capteurs_id', 'numero_serie',
						 'Capteur contenu dans une malette');

		$e =  new Module('equipe'  , 'Equipe',
			             'fb_equipe','equipe_id', 'nom',
		                 'Equipes de joueurs');

/*	 	$j =  new Module('joueur'    , 'Joueurs', 'joueur_id', 'nom',
			'Joueurs en équipe ou individuels'); */

		$p =  new Module('partie'  , 'Parties',
						 'fb_partie', 'partie_id', 'debut',
						 'Partie d\'une équipe dans une salle');

		$inscription = new Module('inscription', 'Inscriptions',
			'fbs_inscription', 'inscription_id', '',
			'Inscription et remise des capteurs par l\'accueil');

		$mesure = new Module('mesure', 'Mesures',
							 'fb_mesures', 'mesure_id', '',
							 'Mesures de la partie');


		//-- Relations 'has many' --//

		$rs_has_many_cs = new Has_many($rs,
			                           'Complexes sportifs gérés par ce réseau',
			                           $cs);
		$rs->relations_one_to_many[] = $rs_has_many_cs;

		$cs_has_many_s = new Has_many($cs, 'Terrains du complexe sportif', $s);
		$cs->relations_one_to_many[] = $cs_has_many_s;

		$cs_has_many_csj = new Has_many($cs, 'Joueurs du complexe sportif', $csj);
		$cs->relations_one_to_many[] = $cs_has_many_csj;

		$cs_has_many_c = new Has_many($cs, 'Capteurs du centre sportif', $c);
		$cs->relations_one_to_many[] = $cs_has_many_c;

        $cs_has_many_e = new Has_many
		      ($cs,
		       'Equipes de joueurs pratiquant régulièrement ensemble',
		       $e);
		$cs->relations_one_to_many[] = $cs_has_many_e;

		$s_has_many_p = new Has_many($s,
			                         'Parties ayant eu lieu sur ce terrain',
			                         $p);
		$s->relations_one_to_many[] = $s_has_many_p;

		// Une partie a plusieurs joueurs inscrits avec affectation capteur
		$p_has_many_i = new Has_many(
			$p,
			'Parties avec plusieurs inscription de joueurs avec capteur',
			$inscription);
		$p->relations_one_to_many[] = $p_has_many_i;

		// Ajoute modules au modèle
		$modele->modules[] = $rs;
		$modele->modules[] = $cs;
		$modele->modules[] = $csj;
		//$modele->modules[] = $mc;
		$modele->modules[] = $c;
		$modele->modules[] = $e;
		//$modele->modules[] = $j;
		$modele->modules[] = $s;
		$modele->modules[] = $p;
		$modele->modules[] = $inscription;
		$modele->modules[] = $mesure;
		$modele->affiche();

		return $modele;
	}
?>