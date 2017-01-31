<?php
	/**
	 * Created by PhpStorm.
	 * User: bernard
	 * Date: 03/11/2016
	 * Time: 11:47
	 */

    include '../../Modele/Modele.php';
    include '../../Modele/Module.php';
    include '../../Modele/Has_many.php';

    include '../../Generators/Sximo_code_generator.php';
    echo '<h1>Test descendants module</h1>';

	// 1-Crée le modèle de l'application
	$modele = cree_modele_football();

	// 2-Teste l'application

	$top_module = $modele->modules[0];

	$descendants  = $top_module->breadcrumb_descendants($top_module);

	//var_dump($descendants);
	echo "<h2>Descendants du module '$top_module->nom' </h2>";
	$i=1;
	foreach ($descendants as $d)
	{
		echo $i++,': ',$d->nom, '<br>';
	}

	function cree_modele_football()
	{
		$modele = new Modele('football', 'Complexes sportifs de Football en salle');

		//-- Modules --//

		$rs = new Module('reseauxsalles', 'Réseau de salles', 'club_id', 'nom',
			'Réseaux de complexes sportifs');

		$cs = new Module('complexesportif', 'Centres sportifs', 'complexe_salle_id','nom',
			'Locations de terrains de football indoors');

		$s =  new Module('salle'   , 'Terrains', 'salle_id', 'identifiant',
			'Salles indoors');

		$csj =  new Module('joueurCentre'   , 'Joueurs', 'joueur', 'nom',
			'Joueurs du centre');

		//$mc = new Module('malette', 'Malette', 'malette_capteurs_id', 'identifiant',
		//                 'Malette contenant 10 capteurs');

		$c =  new Module('capteur' , 'Capteurs', 'capteurs_id', 'numero_serie',
			'Capteur contenu dans une malette');

		//$e =  new Module('equipe'  , 'Equipe', 'equipe_id', 'nom',
		//                 'Equipes de joueurs');

		$j =  new Module('joueur'  , 'Joueurs', 'joueur_id', 'nom',
			'Joueurs en équipe ou individuels');

		$p =  new Module('partie'  , 'Parties', 'partie_id', 'debut',
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

		$rs_has_many_cs = new Has_many($rs, 'Complexes sportifs gérés par ce réseau', $cs);
		$rs->relations_one_to_many[] = $rs_has_many_cs;

		//$cs_has_many_mc = new Has_many($cs, 'Malettes du complexe sportif', $mc);
		//$cs->relations_one_to_many[] = $cs_has_many_mc;

		$cs_has_many_s = new Has_many($cs, 'Terrains du complexe sportif', $s);
		$cs->relations_one_to_many[] = $cs_has_many_s;

		$cs_has_many_csj = new Has_many($cs, 'Joueurs du complexe sportif', $csj);
		$cs->relations_one_to_many[] = $cs_has_many_csj;

		$cs_has_many_c = new Has_many($cs, 'Capteurs du centre sportif', $c);
		$cs->relations_one_to_many[] = $cs_has_many_c;
		//$mc_has_many_c = new Has_many($mc, 'Capteurs contenus dans la malette', $c);
		//$mc->relations_one_to_many[] = $mc_has_many_c;

		//    $cs_has_many_e = new Has_many
		//        ($cs,
		//         'Equipes de joueurs pratiquant régulièrement ensemble',
		//         $e);
		//    $cs->relations_one_to_many[] = $cs_has_many_e;

		$s_has_many_p = new Has_many($s, 'Parties ayant eu lieu sur ce terrain', $p);
		$s->relations_one_to_many[] = $s_has_many_p;
		// ++++ Deux chemins pour atteindre les joueurs
		// ++++ Faut se rappeler par ou on arrive et utiliser le bon filtre
		// ++++ Enlever le filtre précédent à l'arrivée
		// ++++POur changer de filtre, revenir au niveau supérieur
		//$cs_has_many_j = new Has_many($cs, 'Joueurs', $j);
		//$cs->relations_one_to_many[] = $cs_has_many_j;

		//$e_has_many_j = new Has_many($e, 'Joueurs membres de l\'equipe (jouent fréquement ensembles)', $j);
		//$e->relations_one_to_many[] = $e_has_many_j;

		$p_has_many_js = new Has_many($p, 'Joueurs Sélectionnés pour la partie', $js);
		$p->relations_one_to_many[] = $p_has_many_js;

		$js_has_many_sm = new Has_many($js,
			'Session mesures avec le même capteur ' .
			'(Chaque changement de capteur donne lieu '.
			'à une nouvelle session)',
			$sm);
		$js->relations_one_to_many[] = $js_has_many_sm;

		$sm_has_many_m = new Has_many($sm, 'Mesures enregistrées au cours de la session', $mesure);
		$sm->relations_one_to_many[] = $sm_has_many_m;

		// Ajoute modules au modèle
		$modele->modules[] = $rs;
		$modele->modules[] = $cs;
		$modele->modules[] = $csj;
		//$modele->modules[] = $mc;
		$modele->modules[] = $c;
		//$modele->modules[] = $e;
		$modele->modules[] = $j;
		$modele->modules[] = $s;
		$modele->modules[] = $p;
		$modele->modules[] = $js;
		$modele->modules[] = $sm;
		$modele->modules[] = $mesure;
		$modele->affiche();

		return $modele;
	}