<?php
///////////////////////////////////////////////////////////////
	//fonctions de calculs sur les batiments//
///////////////////////////////////////////////////////////////
	function getUnites($idJoueur){
		include("connBdd.php");
		$query = "SELECT unites FROM joueurs WHERE idJoueur='$idJoueur'";
		$result = mysql_query($query) or die("Query error dans getUnites: ". mysql_error());
		if(mysql_num_rows($result) != 0){
			$unitesNS = mysql_result($result,0);
			$unites = unserialize($unitesNS);
			if ($unites == ""){
				$unites = array();
			}
		}else{
			$unites = array();
		}
		return($unites);
	}
	
	function getRecherches($idJoueur){
		include("connBdd.php");
		$query = "SELECT recherches FROM joueurs WHERE idJoueur='$idJoueur'";
		$result = mysql_query($query) or die("Query error dans getUnites: ". mysql_error());
		if(mysql_num_rows($result) != 0){
			$recherchesNS = mysql_result($result,0);
			$recherches = unserialize($recherchesNS);
			if ($recherches == ""){
				$recherches = array();
			}
		}else{
			$recherches = array();
		}
		return($recherches);
	}
	
	function getIdJoueur($nomjoueur){
		include("connBdd.php");
		$query = "SELECT idJoueur FROM joueurs WHERE pseudo='$nomjoueur'";
		$result = mysql_query($query) or die("Query error dans getIdJoueur : ". mysql_error());
		$row=mysql_fetch_array($result);
		if ($row == ""){
			$idJoueur = "inexistant";
		}else{
			$idJoueur = $row['idJoueur'];
		}
		return($idJoueur);
	}
	
	function getNameJoueur($idJoueur){
		include("connBdd.php");
		if($idJoueur > 1){
			$query = "SELECT pseudo FROM joueurs WHERE idJoueur='$idJoueur'";
			$result = mysql_query($query) or die("Query error dans getNameJoueur: ". mysql_error());
			$nomJoueur = mysql_result($result,0);
		}else{
			if($idJoueur == 1){
				$nomJoueur = 'inhabit&eacute;';
			}else{
				$nomJoueur = 'inconnu';
			}			
		}
		return($nomJoueur);
	}
	
	function getMailJoueur($idJoueur){
		include("connBdd.php");
		if($idJoueur > 1){
			$query = "SELECT mail FROM joueurs WHERE idJoueur='$idJoueur'";
			$result = mysql_query($query) or die("Query error dans getNameJoueur: ". mysql_error());
			$mailJoueur = mysql_result($result,0);
		}else{
			return(false);		
		}
		return($mailJoueur);
	}
	
	function acceptMailNotification($idJoueur){
		include("connBdd.php");
		if($idJoueur > 1){
			$query = "SELECT mailNotification FROM joueurs WHERE idJoueur='$idJoueur'";
			$result = mysql_query($query) or die("Query error dans getNameJoueur: ". mysql_error());
			$mailNotification = mysql_result($result,0);
		}else{
			return(false);		
		}
		if($mailNotification == 1 ){
			$accept = true;
		}else{
			$accept = false;
		}
		return($accept);	
	}
		
	function processDefenseBonus($idJoueur){
		$batimentsRoyaume = getElementsReelRoyaume($idJoueur);
		$defense = 0;
		foreach ($batimentsRoyaume as $numeroProvince => $element) {
			if($element['sigleBatiment'] == "muraille"){
				$niveauBatiment  = $element['niveauBatimentProvince'] ;
				$defense += 10 ;
				$defense += $niveauBatiment ;
			}/*
			if($element['sigleBatiment'] == "tour_de_guet"){
				$niveauBatiment  = $element['niveauBatimentProvince'] ;
				$defense += $niveauBatiment ;
			}*/
		}
		return($defense);
	}
	
	function addUnitesByOneType($sigleUnite, $nombre, $idJoueur){
		$currentUnites = getUnites($idJoueur);
		$unites = $currentUnites;
		foreach($currentUnites as $currentSigle => $currentNb){
			if($currentSigle == $sigleUnite){
				$currentNb += $nombre;
			}
			$unites[$currentSigle] = $currentNb;
		}
		$unitesS = serialize($unites);
		$query = "UPDATE joueurs SET unites='$unitesS' WHERE idJoueur='$idJoueur'";
		$result = mysql_query($query) or die ("query error dans tuerUnites update1 : " . mysql_error);
	}
	
	function removeUnitesByOneType($sigleUnite, $nombre, $idJoueur){
		$currentUnites = getUnites($idJoueur);
		$unites = $currentUnites;
		foreach($currentUnites as $currentSigle => $currentNb){
			if($currentSigle == $sigleUnite){
				$currentNb -= $nombre;
			}
			$unites[$currentSigle] = $currentNb;
		}
		$unitesS = serialize($unites);
		$query = "UPDATE joueurs SET unites='$unitesS' WHERE idJoueur='$idJoueur'";
		$result = mysql_query($query) or die ("query error dans tuerUnites update1 : " . mysql_error);
	}
	
	function getNameTerritoire($idTerritoire){
		include("connBdd.php");
		$query = "SELECT nomTerritoire FROM territoire WHERE idTerritoire='$idTerritoire'";
		$result = mysql_query($query) or die("Query error dans getNameTerritoire : ". mysql_error());
		$row=mysql_fetch_array($result);
		if ($row == ""){
			$territoire = "ce territoire a &eacute;t&eacute; supprim&eacute;/n'existe plus";
		}else{
			$territoire = $row['nomTerritoire'];
		}
		return($territoire);
	}
	
	function getNameElement($idElement){
		require_once("arbre.php");
		global $grilleNamesElements ;
		return($grilleNamesElements[$idElement]);
	}
	
	function getSigleElement($idElement){
		require_once("arbre.php");
		global $grilleSiglesElements ;
		return($grilleSiglesElements[$idElement]);
	}

	function getNameElementBySigle($sigleElement){
		require_once("arbre.php");
		global $grilleSiglesElements ;
		$idElement = getIdElement($sigleElement);
		return(getNameElement($idElement));
	}
	
	function getIdElement($sigleElement){
		require_once("arbre.php");
		global $grilleSiglesElements ;
		return(array_search($sigleElement, $grilleSiglesElements));
	}
	
	function getValeurElement($idElement){
		require_once("arbre.php");
		global $grillePointsElements ;
		return($grillePointsElements[$idElement]);
	}
	
	function getPointsJoueur($idJoueur){
		$query = "SELECT points FROM joueurs WHERE idJoueur=$idJoueur";
		$result = mysql_query($query) or die("Query error dans getNiveau: ". mysql_error());
		return( mysql_result ($result,0));
	}
	
	function getRangJoueur($idJoueur){
		$query = "SELECT rang FROM joueurs WHERE idJoueur=$idJoueur";
		$result = mysql_query($query) or die("Query error dans getNiveau: ". mysql_error());
		return( mysql_result ($result,0));
	}
	
	function addRessource($idJoueur,$type,$aAjouter){
		$ressourcesJoueur = getRessources($idJoueur);
		$argentJoueur = $ressourcesJoueur[$type] ;
		$nouvelleSomme =  round($argentJoueur + $aAjouter) ;
		if ($nouvelleSomme > getMaxRessource($idJoueur, $type)){
			$nouvelleSomme = getMaxRessource($idJoueur, $type) ;
		}
		include_once("connBdd.php");
		$query = "UPDATE joueurs SET $type=$nouvelleSomme WHERE idJoueur=$idJoueur";
		$result = mysql_query($query) or die ("query error dans addRessource update1 : " . mysql_error());
	}
	
	function rmvRessource($idJoueur,$type,$aEnlever){
		$ressourcesJoueur = getRessources($idJoueur);
		$argentJoueur = $ressourcesJoueur[$type] ;
		$nouvelleSomme =  round($argentJoueur - $aEnlever) ;
		if ($nouvelleSomme < 0){
			$nouvelleSomme = 0 ;
		}
		include_once("connBdd.php");
		$query = "UPDATE joueurs SET $type=$nouvelleSomme WHERE idJoueur=$idJoueur";
		$result = mysql_query($query) or die ("query error dans rmvRessource update1 : " . mysql_error());
	}
	//grace a l'id de l'element
	function isIncremental($idElement){
		require_once("arbre.php");
		global $reslist ;
		return(array_key_exists($idElement,$reslist['incremental']));
	}
	
	function afficherDate($date){
		return ($date["mday"]."/".$date["month"]."/".$date["year"]."  ".$date["hours"].":".$date["minutes"].":".$date["seconds"]);
	}
	
	//grace a l'id de l'element
	function isUnique($idElement){
		require_once("arbre.php");
		global 	$reslist;
		return(in_array($idElement,$reslist['unique']));
	}
	
	function getNiveau($province){
		$query = "SELECT niveauBatimentProvince FROM provinces_joueur WHERE provinces_joueur.idProvince=$province";
		$result = mysql_query($query) or die("Query error dans getNiveau: ". mysql_error());
		$row = mysql_fetch_array ($result);
		return($row ['niveauBatimentProvince']);
	}
	
	function getNiveauRecherche($idRecherche,$recherches){
		$sigleRecherche = getSigleElement($idRecherche);
		if(isset($recherches[$sigleRecherche])){
			$niveau = $recherches[$sigleRecherche] ;
		}else{
			$niveau = 0 ;
		}
		return($niveau);
	}
	
	function getImageBatiment($idBatiment,$niveau){
		$nomBatiment = getSigleElement($idBatiment);
		return("./style/images/batiments/$nomBatiment/$niveau.gif");
	}
	
	function getNiveauMax($idElement){
		require("arbre.php");
		global $reslit ;
		return ($reslist['incremental'][$idElement]);
	}
////////////////////////////////////////////////////////////////

	function territoireLibre($nomTerritoire){
		require_once("connBdd.php");
		$query = "SELECT complet FROM territoire WHERE nomTerritoire = '$nomTerritoire'";
		$result = mysql_query($query)or die("Query error territoire complet : ". mysql_error());
		$resultat=mysql_fetch_array($result);
		return($resultat);
	}

///////////////////////////////////////////////////////////////
	//fonction sur les joueurs//
///////////////////////////////////////////////////////////////
	function isHumain($ennemi){
		if($ennemi > 1){
			$humain = true ;
		}else{
			$humain = false ;
		}	
		return($humain);
	}
	
	function isAdmin(){
		$idJoueur = $_SESSION['idJoueur'] ;
		include("connBdd.php");
		$query = "SELECT statut FROM joueurs WHERE idJoueur='$idJoueur'";
		$result = mysql_query($query) or die("Query error dans isAdmin : ". mysql_error());
		$row = mysql_fetch_array($result);
		if ($row['statut'] == "administrateur"){
			return (true);
		}else{
			return (false);
		}
	}
	
	function statut(){
		$idJoueur = $_SESSION['idJoueur'] ;
		include("connBdd.php");
		$query = "SELECT statut FROM joueurs WHERE idJoueur='$idJoueur'";
		$result = mysql_query($query) or die("Query error dans statut: ". mysql_error());
		$row = mysql_fetch_array($result);
		return ($row['statut']);
	}
	
	function getCouleurJoueur($idJoueur){
		if($idJoueur == 'vide'){
			 $couleur = '#B9121B';
		}else{
			if($idJoueur == 'occupe'){
				$couleur = "#E6E2AF";
			}else{
				include("connBdd.php");
				if($idJoueur > 1){
					$query = "SELECT couleur FROM joueurs WHERE idJoueur='$idJoueur'";
					$result = mysql_query($query) or die("Query error dans getCouleurJoueur: ". mysql_error());
					$couleur = mysql_result($result,0);
				}else{
					if($idJoueur == 1){
						$couleur = '#ffffff';
					}
				}
			}
		}
		return ($couleur);
	}
	
	function getCouleurNeutre($idJoueur){
		if($idJoueur == 'vide'){
			 $couleur = '#B9121B';
		}else{
			if($idJoueur == 'occupe'){
				$couleur = "#E6E2AF";
			}
		}
		return ($couleur);
	}
	
	function setCouleur($idJoueur, $couleur){
		$query = "UPDATE joueurs SET couleur = '$couleur' WHERE idJoueur='$idJoueur'";
		$result = mysql_query($query) or die("erreur dans UPDATE1 de setCouleur : " . mysql_error()) ;
	}
	
////////////////////////////////////////////////
	//fonctions sur les get//
///////////////////////////////////////////////
	function getRessources($idJoueur){
		include("arbre.php");
		include("connBdd.php");
		foreach($grilleRessources as $restype){
			$query = "SELECT $restype FROM joueurs WHERE idJoueur=$idJoueur";
			$result = mysql_query($query) or die("Query error dans getRessources : ". mysql_error());
			$ressource = mysql_result($result,0);
			$ressources[$restype] = $ressource;
		}
		return ($ressources);
	}
	
	function getRessource($ressource,$idJoueur){
		include("connBdd.php");
		$query = "SELECT $ressource FROM joueurs WHERE idJoueur='$idJoueur'";
		$result = mysql_query($query) or die("Query error dans getRessource : ". mysql_error());
		return mysql_result($result,0);
	}
	
	function getPRessources($idJoueur){
		include_once("fonctionsProvince.php");
		include_once("arbre.php");
		global $reslist, $grilleProduction, $grilleRequireProvince, $grilleRessources;
		$batimentsRoyaume = getElementsReelRoyaume($idJoueur);
		$ressources = array();
		foreach ($batimentsRoyaume as $numeroProvince => $element) {
			$sigleBatiment = $element['sigleBatiment'] ;
			$niveauBatiment  = $element['niveauBatimentProvince'] ;
			$idElement = getIdElement($sigleBatiment);
			if (isBatimentProduction($idElement)){
				foreach($grilleRessources as $typeRessource){
					if(!isset($ressources[$typeRessource])){$ressources[$typeRessource] = 0 ;}
					$ressources[$typeRessource] += $grilleProduction[$idElement][$typeRessource] * $niveauBatiment;
				}
				foreach($grilleRequireProvince[$idElement] as $idBatimentPrecedent => $niveauMax){
					foreach($grilleRessources as $typeRessource){
						$ressources[$typeRessource] += $grilleProduction[$idElement][$typeRessource] * $niveauMax;
					}
				}
			}
		}
		return($ressources);
	}
	
	function getPRessource($typeRessource,$idJoueur){
		include_once("fonctionsProvince.php");
		include_once("arbre.php");
		global $reslist, $grilleProduction, $grilleRequireProvince, $grilleRessources;
		$batimentsRoyaume = getElementsReelRoyaume($idJoueur);
		$ressource = 0;
		foreach ($batimentsRoyaume as $numeroProvince => $element) {
			$sigleBatiment = $element['sigleBatiment'] ;
			$niveauBatiment  = $element['niveauBatimentProvince'] ;
			$idElement = getIdElement($sigleBatiment);
			if (isBatimentProduction($idElement)){
				if(!isset($ressources[$typeRessource])){$ressources[$typeRessource] = 0 ;}
				$ressource += $grilleProduction[$idElement][$typeRessource] * $niveauBatiment;
				foreach($grilleRequireProvince[$idElement] as $idBatimentPrecedent => $niveauMax){
					$ressource += $grilleProduction[$idElement][$typeRessource] * $niveauMax;
				}
			}
		}
		return($ressource);
	}
	
	function calculateRessourceParSeconde($pRessource){
		return($pRessource/3600 );
	}
	
	function getPopulation($idJoueur){
		$population = 0 ;
		$unites = getUnites($idJoueur);
		$events = getEvenements($idJoueur);
		if($events){
			foreach($events as $event){
				foreach($event['unitesEnMouvement'] as $sigleUnite => $nbUnite){
					$population += $nbUnite ;
				}
			}
		}
		foreach($unites as $sigleUnite => $nbUnite){
			$population += $nbUnite ;
		}
		return($population);
	}
	
	function getPopulationMax($idJoueur){
		global $reslist, $grilleHabitations, $grilleRequireProvince;
		$batimentsRoyaume = getElementsReelRoyaume($idJoueur);
		$populationMax = 0;
		foreach ($batimentsRoyaume as $numProvince => $element) {
			$sigleBatiment = $element['sigleBatiment'] ;
			$niveauBatiment  = $element['niveauBatimentProvince'] ;
			$idElement = getIdElement($sigleBatiment);
			if (isBatimentHabitation($idElement)){
				$populationMax += $grilleHabitations[$idElement] * $niveauBatiment;
				foreach($grilleRequireProvince[$idElement] as $idBatimentPrecedent => $niveauMax){
					if (isBatimentHabitation($idBatimentPrecedent)){
						$populationMax += $grilleHabitations[$idBatimentPrecedent] * $niveauMax;
					}
					
				}
			}
		}
		return($populationMax);
	}
	//getMaxRessources
	function getMaxRessource($idJoueur, $type){
		global $reslist, $grilleRequireProvince, $grilleStockage;
		$batimentsRoyaume = getElementsReelRoyaume($idJoueur);
		if ($type == 'bois' || $type == 'argent'){
			$ressourcesMax = 5000;
		}else{
			$ressourcesMax = 0;
		}
		foreach ($batimentsRoyaume as $numeroProvince => $element) {
			$sigleBatiment = $element['sigleBatiment'] ;
			$niveauBatiment  = $element['niveauBatimentProvince'] ;
			$idElement = getIdElement($sigleBatiment);
			if (isBatimentStockage($idElement)){
				$ressourcesMax += $grilleStockage[$type][$idElement] * $niveauBatiment;
				foreach($grilleRequireProvince[$idElement] as $idBatimentPrecedent => $niveauMax){
					if (isBatimentHabitation($idBatimentPrecedent)){
						$ressourcesMax += $grilleStockage[$type][$idBatimentPrecedent]* $niveauMax;
					}	
				}
			}
		}
		return($ressourcesMax);
	}
///////////////////////////////////////////////////////////////
	//fonctions d'initialisations et de calculs//
///////////////////////////////////////////////////////////////
	function shortly_number($n)
	{
        if($n>1000000000000) return round(($n/1000000000000),1).' Trillion';
        else if($n>1000000000) return round(($n/1000000000),1).' Milliard';
        else if($n>1000000) return round(($n/1000000),1).' Million';
       /* else if($n>1000) return round(($n/1000),1).' mille';
		else if($n>=100) return round(($n/100),1).' k';*/
        
        return number_format($n);
	}
	
	function setDetailsRoyaumeJoueur($idJoueur){
		$query = "
			SELECT province.idProvince,batimentProvince,niveauBatimentProvince,underConstruction FROM province 
			INNER JOIN provinces_joueur 
			ON provinces_joueur.idProvince=province.idProvince 
			INNER JOIN joueurs 
			ON joueurs.idJoueur = provinces_joueur.idJoueur 
			WHERE provinces_joueur.idJoueur = '$idJoueur'
		";
		$result = mysql_query($query)or die("Query error :". mysql_error());
		while ($case = mysql_fetch_assoc($result)){
			$provinces[$case['idProvince']] = array('sigleBatiment' => $case['batimentProvince'],'niveauBatimentProvince' => $case['niveauBatimentProvince'],'underConstruction' => $case['underConstruction']);
		}
		$tableauProvince = serialize($provinces);
		$query = "UPDATE joueurs SET detailsRoyaume='$tableauProvince' WHERE idJoueur = '$idJoueur'";
		$result = mysql_query($query) or die("query error dans l'initialisation des variables : " .mysql_error());
	}
	
	function getElementsRoyaume($idJoueur){
		include("connBdd.php");
		global $grilleRequireProvince ;
		$batimentsRoyaume = getElementsReelRoyaume($idJoueur) ;
		$tableauElement = array();
		foreach ($batimentsRoyaume as $numeroProvince => $element) {
			$sigleBatiment = $element['sigleBatiment'] ;
			$niveauBatiment  = $element['niveauBatimentProvince'] ;
			$underConstruction  = $element['underConstruction'] ;
			//si il n'est pas en construction on l'enregistre normalement
			if ($underConstruction == 0){
				//si il est deja dans la base on prends le niveau le plus haut
				if (array_key_exists($sigleBatiment ,$tableauElement)){
					if ($niveauBatiment > $tableauElement[$sigleBatiment ]){
						$tableauElement[$sigleBatiment ] = $niveauBatiment;
					}
				}else{
					$tableauElement[$sigleBatiment ] = $niveauBatiment;
				}
			}else{
				//si il est en construction
				//si il est incrementable et le niveau superieur a 1 on enregistre son niveau inferieur
				$idElement = getIdElement($sigleBatiment ) ;
				if(isIncremental($idElement)){
					$niveauBatiment = $niveauBatiment ;
					if ($niveauBatiment > 1){
						$tableauElement[$sigleBatiment ] = $niveauBatiment - 1;
					}
				}
			}
			//tant qu'il y a des elements inferieurs on les enregistre
			$tableauElementPrecedent = getElementPrecedent($sigleBatiment) ;
			while (!empty($tableauElementPrecedent)){
				foreach($tableauElementPrecedent as $idElementPrecedent => $niveauElementPrecedent){
					$sigleElementPrecedent = getSigleElement($idElementPrecedent) ;
					$tableauElement[$sigleElementPrecedent] = $niveauElementPrecedent;
					$sigleBatiment = $sigleElementPrecedent;
				}
				$tableauElementPrecedent = getElementPrecedent($sigleBatiment) ;
			}
		}
		return($tableauElement);
	}
	
	function getElementsReelRoyaume($idJoueur){
		include_once("connBdd.php");
		$query = "SELECT detailsRoyaume FROM joueurs WHERE idJoueur = '$idJoueur'";
		$result = mysql_query($query)or die("Query error: ". mysql_error());
		$row = mysql_fetch_assoc($result);
		$provinces = unserialize($row['detailsRoyaume']);
		return($provinces);
	}
	
	function construire($province){
		$idJoueur = $_SESSION['idJoueur'] ;
		include_once("connBdd.php");
		include_once("arbre.php");
		global $reslist,$grilleProduction, $grilleRessources, $grilleHabitations ;
		$query = "SELECT idEvenement,dateFin FROM evenement WHERE provinceDepart='$province' AND typeEvenement='construction' ";
		$result = mysql_query($query) or die("Query error dans construire: ". mysql_error());
		$row = mysql_fetch_array($result);
		$idEvenement = $row['idEvenement'];
		if(tempsRestantBatiment($province) <= 0 ){
			$query = "SELECT batimentProvince,niveauBatimentProvince FROM provinces_joueur WHERE provinces_joueur.idProvince='$province'";
			$result = mysql_query($query) or die("Query error dans construire: ". mysql_error());
			$row = mysql_fetch_array($result);
			$sigleBatiment = $row['batimentProvince'] ;
			$idBatiment = getIdElement($sigleBatiment);
			$niveau = $row['niveauBatimentProvince'] ;
			$query = "UPDATE provinces_joueur SET underConstruction='0' WHERE provinces_joueur.idProvince='$province'";
			$result = mysql_query($query) or die("Query error dans construire: ". mysql_error());
			
			$query = "DELETE FROM evenement WHERE idEvenement='$idEvenement'";
			$result = mysql_query($query) or die ("Couldn't execute query." );
			setDetailsRoyaumeJoueur($idJoueur);
			//logEventConstruction($province,$sigleBatiment);
			return(true);
		}else{
			return(false);
		}
	}
	
	function attaquer($evenement){
		include("connBdd.php");
		include_once("fonctionsAttaques.php");
		$idJoueur = $evenement['idJoueur'];
		$idProvince = $evenement['provinceArrivee'];
		$attaque = $evenement['unitesEnMouvement'];
		$idEvenement = $evenement['idEvenement'];
		$heureFin = $evenement['dateFin'];
		$habitee = isHabite($idProvince);
		if ($habitee){
			$idJoueurEnnemi = getJoueurEnnemiProvince($idProvince);
			$joueurEnnemi = getNameJoueur($idJoueurEnnemi);
		}
		$heureAttaque = getDate() ;
		$dateF = getDateFormatee($heureAttaque);
		$type = "Rapport d'attaque de ". shortly_number($idProvince) ;
		if ($habitee){
			$gagnant = combattre($idJoueur, $attaque, $heureFin, $idJoueurEnnemi, $idProvince);
			$textRecompense = getRecompense($gagnant) ;
			$textGagnant = $textRecompense['gagnant'] ;
			$textPerdant = $textRecompense['perdant'] ;
			$textEgaliteAttaquant = $textRecompense['egaliteAttaquant'] ;
			$textEgaliteDefenseur = $textRecompense['egaliteDefenseur'] ;
			if($gagnant[99] == "attaquant"){
				$textJoueur = $textGagnant;
				$textEnnemi = $textPerdant;
			}else{
				if($gagnant[99] == "defenseur"){
					$textJoueur = $textPerdant;
					$textEnnemi = $textGagnant;
				}else{
					$textJoueur = $textEgaliteAttaquant;
					$textEnnemi = $textEgaliteDefenseur;
				}
			}
			//code a supprimer oblige le texte du joueur a etre le texte du joueur gagnant
			/*
			$textRecompense = getRecompense($gagnant) ;
			$corps = "Debut </br> Tableau gagnant = ". print_r($gagnant) 
			."</br> Tableau gagnant[0]= ". $gagnant[0]
			."</br> Tableau gagnant[1]= ". $gagnant[1]
			. " </br> Gagnant = ".$gagnant."</br>Texte recompense = " . $textRecompense['gagnant']." </br> Fin";
			*/
			//ligne originale :
			$corps = "$textJoueur";
			
			$corpsEnnemi = "$textEnnemi";
			sendRapport($idJoueurEnnemi, $type,  $corpsEnnemi);
		}else{
			addProvinceVide($idJoueur,$idProvince);
			$corps = "Vous avez lanc&eacute; une attaque sur la province desertique ". shortly_number($idProvince) ." le $dateF. Votre arm&eacute;e est revenue sans butin.";
			setRetourAttaqueVide($evenement);
		}
		sendRapport($idJoueur, $type,  $corps);
		$query = "DELETE FROM evenement WHERE idEvenement='$idEvenement'";
		$result = mysql_query($query) or die ("Couldn't execute query de la suppression de l'attaque." );
	}

	function retourAttaque($evenement){
		require_once("connBdd.php");
		require_once("fonctionsDates.php");
		include_once("arbre.php");
		global $reslist, $grilleRessources, $grilleSpecs ;
		$butin = $evenement['ressources'];
		$unites = $evenement['unitesEnMouvement'];
		$idJoueur = $evenement['idJoueur'];	
		$idEvenement = $evenement['idEvenement'];	
		foreach($unites as $sigle => $nombre){
				addUnitesByOneType($sigle, $nombre, $idJoueur);
		}
		foreach ($grilleRessources as $type){
			$montant = $butin[$type];
			addRessource($idJoueur,$type,$montant);
		}
		$query = "DELETE FROM evenement WHERE idEvenement='$idEvenement'";
		$result = mysql_query($query) or die ("Couldn't execute query de retour attaque" );
	}
	
	function getElementsUnderConstructionRoyaume($idJoueur){
		include_once("connBdd.php");
		include_once("arbre.php");
		global $grilleRequireProvince ;
		$query = "SELECT idProvince,underConstruction FROM provinces_joueur WHERE provinces_joueur.idJoueur='$idJoueur'";
		$result = mysql_query($query) or die("Query error dans getElementsUnderConstructionRoyaume : ". mysql_error());
		$tableauElement = array();
		while ($row = mysql_fetch_array ($result)){
			if ($row['underConstruction'] == 1){
				$tableauElement[] = $row['idProvince'];
			}
		}
		return($tableauElement);
	}
	
	function majClassementTerritoire($idTerritoire){
		$query = "SELECT idJoueur FROM joueurs WHERE idTerritoire='$idTerritoire'";
		$result1 = mysql_query($query) or die("Query error dans majClassementTerritoire : ". mysql_error());
		while ($row = mysql_fetch_assoc($result1)){
			$currentId = $row['idJoueur']; 
			$batimentsRoyaume = getElementsReelRoyaume($currentId);
			$pointsJoueur = 0 ;
			//si le joueur n'a rien
			if($batimentsRoyaume){
				foreach ($batimentsRoyaume as $numeroProvince => $element) {
					$sigleBatiment = $element['sigleBatiment'] ;
					$niveau = $element['niveauBatimentProvince'] ;
					if ($niveau == 0){$niveau = 1;}
					$pointsJoueur = $pointsJoueur + getValeurElement(getIdElement($sigleBatiment)) * $niveau;
				}
			}
			$query = "UPDATE joueurs SET points=$pointsJoueur WHERE idJoueur='$currentId'";
			$result = mysql_query($query) or die("Query error: ". mysql_error());
		}
		$query = "SELECT idJoueur FROM joueurs WHERE idTerritoire='$idTerritoire' ORDER BY points DESC";
		$result2 = mysql_query($query) or die("Query error dans majClassementTerritoire : ". mysql_error());
		$i = 0 ;
		while ($row = mysql_fetch_assoc($result2)){
			$i++ ;
			$idJoueur = $row['idJoueur'];
			$query = "UPDATE joueurs SET rang=$i WHERE idJoueur='$idJoueur'";
			$result = mysql_query($query) or die("Query error: ". mysql_error());
		}
	}
	
	function isHabite($idProvince){
		$query = "SELECT idJoueur FROM provinces_joueur WHERE idProvince = '$idProvince'";
		$result = mysql_query($query)or die("Query error dans isHabite : ". mysql_error());
		$resultat=mysql_fetch_row($result);
		if( $resultat){
			$habitee = true;
		}else{
			$habitee = false;
		}
		return ($habitee);
	}
	
	function capaciteButin($idUnite){
		include_once("arbre.php");
		global $grilleSpecs ;
		return($grilleSpecs[$idUnite]['capacite']);
	}
	
	//TODO
	//Probleme d'import de fonctions attaque qui envoi les headers et empeche la redirection
	function logEventLanceAttaque($idProvince, $unites, $idJoueur){
		include_once('./controleurs/fonctionsAttaques.php');
		$file = fopen('logEventAttaque.txt','a');
		$res = getNameJoueur($idJoueur);
		$res .= " LANCE UNE ATTAQUE SUR ";
		$res .= getNameJoueur(getJoueurEnnemiProvince($idProvince));
		$res .= " avec ";
		foreach($unites as $sigle => $nb){
			$res .= " $nb $sigle, ";
		}
		$dateDebut = getdate();
		$heureActuelle = getDateFormatee($dateDebut);
		$res .= " le $heureActuelle \n";
		fwrite($file, $res);
	}
	
	function logEventAttaque($evenement){
		if(file_exists("./fonctionsAttaques.php")){
			require_once("./fonctionsAttaques.php");
		}else{
			if(file_exists("./controleurs/fonctionsAttaques.php")){
				require_once("./controleurs/fonctionsAttaques.php");
			}
		}
		$file = fopen('logEventAttaque.txt','a');
		$idEvent = $evenement['idEvenement'];
		$idJoueur = $evenement['idJoueur'];
		$idProvince = $evenement['provinceArrivee'];
		$unites = $evenement['unitesEnMouvement'];
		$dateFin = $evenement['dateDebut'] ;
		$res = $idEvent ." ";
		$res .= getNameJoueur($idJoueur);
		$res .= " ATTAQUE ";
		$res .= getJoueurEnnemiProvince($idProvince);
		$res .= " avec ";
		foreach($unites as $sigle => $nb){
			$res .= " $nb $sigle, ";
		}
		$heureActuelle = getDateFormateeFromTimeStamp($dateFin);
		$res .= " le $heureActuelle \n";
		fwrite($file, $res);
	}
	
	function logEventRetourAttaque($evenement){
		if(file_exists("./fonctionsAttaques.php")){
			require_once("./fonctionsAttaques.php");
		}else{
			if(file_exists("./controleurs/fonctionsAttaques.php")){
				require_once("./controleurs/fonctionsAttaques.php");
			}
		}
		$file = fopen('logEventAttaque.txt','a');
		$idEvent = $evenement['idEvenement'];
		$idJoueur = $evenement['idJoueur'];
		$idProvince = $evenement['provinceArrivee'];
		$unites = $evenement['unitesEnMouvement'];
		$dateFin = $evenement['dateFin'] ;
		$res = $idEvent ." ";
		$res .= getNameJoueur($idJoueur);
		$res .= " RETROUVE ";
		foreach($unites as $sigle => $nb){
			$res .= " $nb $sigle, ";
		}
		$res .= "qui revienennt de chez ";
		$res .= getNameJoueur(getJoueurEnnemiProvince($idProvince));
		$heureActuelle = getDateFormateeFromTimeStamp($dateFin);
		$res .= " le $heureActuelle \n";
		fwrite($file, $res);
	}
		
	function logEventLanceConstruction($idProvince, $sigleElement, $niveauBatiment, $idJoueur){
		$file = fopen('logEventConstruction.txt','a');
		$res = $last_id ." ";
		$res .= getNameJoueur($idJoueur);
		$res .= " LANCE CONSTRUCTION de ";
		$res .= " $sigleBatiment $niveauBatiment ";
		$res .= "sur ";
		$res .= $idProvince;
		$dateDebut = getdate();
		$heureActuelle = getDateFormatee($dateDebut);
		$res .= " le $heureActuelle \n";
		fwrite($file, $res);
	}
	
	function logEventConstruction($evenement){
		$file = fopen('logEventConstruction.txt','a');
		$idEvent = $evenement['idEvenement'];
		$idJoueur = $evenement['idJoueur'];
		$idProvince = $evenement['provinceArrivee'];
		$sigleBatiment = $evenement['sigleBatimentEnConstruction'];
		$niveauBatiment = $evenement['niveauBatimentEnConstruction'];
		$dateFin = $evenement['dateDebut'] ;
		$res = $idEvent ." ";
		$res .= getNameJoueur($idJoueur);
		$res .= " LANCE CONSTRUCTION de ";
		$res .= " $sigleBatiment $niveauBatiment ";
		$res .= "sur ";
		$res .= $idProvince;
		$heureActuelle = getDateFormateeFromTimeStamp($dateFin);
		$res .= " le $heureActuelle \n";
		fwrite($file, $res);
	}
?>