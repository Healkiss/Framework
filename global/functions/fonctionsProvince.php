<?php
/////////////////////////////////////////////////////
	//fonctions sur les provinces//
/////////////////////////////////////////////////////
	function getProvincesJoueur($idJoueur){
		$query = "SELECT idProvince FROM provinces_joueur WHERE province.idJoueur='$idJoueur'";
		$result = mysql_query($query)or die("Query error dans getProvincesJoueur : ". mysql_error());
		$tableauProvince = "";
		while ($row = mysql_fetch_assoc($result)){
			$tableauProvince[] = $row['idProvince'];
		}
		return $tableauProvince;
	}
	
	function getNumProvince($x,$y,$idTerritoire){
		$query = "SELECT province.idProvince FROM province WHERE x='$x' AND y='$y' AND province.idTerritoire='$idTerritoire'";
		$result = mysql_query($query)or die("Query error dans getNumProvince : ". mysql_error());
		if(mysql_num_rows($result) != 0){
			$row = mysql_result($result,0);
		}else{
			$row = 0 ;
		}
		return($row);
	}
	
	function getPositionProvince($id){
		$query = "SELECT x,y FROM province WHERE idProvince='$id'";
		$result = mysql_query($query)or die("Query error dans getPositionProvince : ". mysql_error());
		$row = mysql_fetch_assoc($result);
		$x = $row['x'];
		$y = $row['y'];
		return(array($x,$y));
	}
	
	function isProvinceJoueurWithDetails($numProvince,$details){
		return(array_key_exists($numProvince,$details));
	}
	
	function getDescriptionElement($idElement){
		require_once("arbre.php");
		global $grilleDescriptionsElements ;
		return($grilleDescriptionsElements[$idElement]);
	}
	
	function getElementPrecedent($sigleElement){
		include_once("arbre.php");
		global $grilleRequireProvince ;
		$idElement = getIdElement($sigleElement);
		$IdEtNiveauPrecedent = $grilleRequireProvince[$idElement] ;
		return($IdEtNiveauPrecedent);
	}
	
	function getSigleElementEtNiveauPrecedent($sigleElement){
		include_once("arbre.php") ;
		global $grilleRequireProvince, $reslist;
		$IdEtNiveauPrecedent = getElementPrecedent($sigleElement) ;
		if (!empty($IdEtNiveauPrecedent)){
			foreach($IdEtNiveauPrecedent as $idElementPrecedent => $niveauElementPrecedent){
				$sigleElementPrecedent = getSigleElement($idElementPrecedent) ;
				$sigleElementEtNiveauPrecedent[$sigleElementPrecedent] = $niveauElementPrecedent;
			}
		}else{
			$sigleElementEtNiveauPrecedent = array();
		}
		return($sigleElementEtNiveauPrecedent) ;
	}
	
	function isElementConstructible($idElement, $province, $niveau){
		global $grilleRequireProvince, $grilleRequireRoyaume ;
		$idJoueur = $_SESSION['idJoueur'];
		//getElement du royaume
		$elementsRoyaume = getElementsRoyaume($idJoueur);
		$elementProvince = getIdElement(getElementProvince($province));
		$constructible = true ;
		//on verifie qu'on ait les elements requis dans la province
		foreach ($grilleRequireProvince[$idElement] as $elementRequisProvince => $niveauElementRequisProvince) {
			if (!($elementProvince == $elementRequisProvince) || !($niveauElementRequisProvince == $niveau)){
				$constructible = false ;
			}
		}
		//on verifie qu'on a les elements requis dans le royaume
		foreach ($grilleRequireRoyaume[$idElement] as $elementRequisRoyaume=> $niveauElementRequisRoyaume) {
			if (existe($elementRequisRoyaume)){
				if($niveauElementRequisRoyaume <= $elementsRoyaume[getSigleElement($elementRequisRoyaume)]){
				}else{
					$constructible = false ;
				}
			}else{
				$constructible = false ;
			}
		}
		return($constructible);
	}
	
	function isUniteConstructible($idUnite, $province, $niveau){
		global $grilleRequireProvince, $grilleRequireRoyaume ;
		$idJoueur = $_SESSION['idJoueur'];
		//getElement province
		$niveauBatiment = getNiveau($province);
		$elementProvince = getIdElement(getElementProvince($province));
		$constructible = false ;
		//on verifie qu'on ait les elements requis dans la province
		foreach ($grilleRequireProvince[$idUnite] as $elementRequisProvince => $niveauElementRequisProvince) {
			if (($elementProvince == $elementRequisProvince) && ($niveauElementRequisProvince <= $niveauBatiment)){
				$constructible = true ;
			}
		}
			return($constructible);
	}
	
	function isRechercheConstructible($idRecherche, $province, $niveauRecherche){
		$niveauBatiment = getNiveau($province);
		global $grilleRequireProvince, $grilleRequireRoyaume ;
		$idJoueur = $_SESSION['idJoueur'];
		//getElement province
		$recherches = getRecherches($idJoueur);
		$niveauRechercheEnregistre = getNiveauRecherche($idRecherche,$recherches) ;
		$elementProvince = getIdElement(getElementProvince($province));
		$constructible = false ;
		//on verifie qu'on ait les elements requis dans la province
		foreach ($grilleRequireProvince[$idRecherche] as $elementRequisProvince => $niveauElementRequisProvince) {
			if (($elementProvince == $elementRequisProvince) && ($niveauElementRequisProvince >= $niveauBatiment)){
				$niveauRecherche ++ ;
				//if ($niveauRechercheEnregistre == $niveauRecherche){
					$constructible = true ;
				//}
			}
		}
			return($constructible);
	}
	
	function existe($idElement){
		$idJoueur = $_SESSION['idJoueur'];
		$elementsRoyaume = getElementsRoyaume($idJoueur);
		return(array_key_exists(getSigleElement($idElement),$elementsRoyaume));
	}

	function getRequireElement($idElement, $province, $niveau){
		global $grilleRequireProvince, $grilleRequireRoyaume ;
		$idJoueur = $_SESSION['idJoueur'];
		//getElement du royaume
		$elementsRoyaume = getElementsRoyaume($idJoueur);
		$idElementProvince = getIdElement(getElementProvince($province));
		//on verifie qu'on ait les elements requis dans la province
		echo "<td>";
			foreach ($grilleRequireProvince[$idElement] as $elementRequisProvince => $niveauElementRequisProvince) {
				if (!($idElementProvince == $elementRequisProvince) || !($niveauElementRequisProvince == $niveau)){
					echo "Il vous manque : <b style='color:red;'>" . getNameElement($elementRequisProvince) . "</b> au niveau <b style='color:red;'>" . $niveauElementRequisProvince."</b> dans votre province<br/>";
				}
			}
			//on verifie qu'on a les elements requis dans le royaume
			foreach ($grilleRequireRoyaume[$idElement] as $elementRequisRoyaume=> $niveauElementRequisRoyaume) {
				if (array_key_exists(getSigleElement($elementRequisRoyaume),$elementsRoyaume)){
					if($niveauElementRequisRoyaume <= $elementsRoyaume[getSigleElement($elementRequisRoyaume)]){
					}else{
						echo "Il vous manque : <b style='color:red;'>" . getNameElement($elementRequisRoyaume) . "</b> au niveau <b style='color:red;'>" . $niveauElementRequisRoyaume."</b> dans votre royaume<br/>";
					}
				}else{
					echo "Il vous manque : <b style='color:red;'>" . getNameElement($elementRequisRoyaume) . "</b> au niveau <b style='color:red;'>" . $niveauElementRequisRoyaume."</b> dans votre royaume<br/>";
				}
			}
		echo "</td>";
	}

	function getElementPrix($idElement,$niveau){
		$idJoueur = $_SESSION['idJoueur'];
		global $grillePrix , $grilleRessources, $grilleSuivant ;
		foreach ($grilleRessources as $resType){
			//si c'est incrementable et un batiment
			if(isIncremental($idElement) && $niveau != 0 && !isUnite($idElement) && !isRecherche($idElement)){
				$niveauMax = getNiveauMax($idElement);
				if ($niveau == 1){
					$cout = $grillePrix[$idElement][$resType];
				}else{
					$cout = $grillePrix[$idElement][$resType] + (($grillePrix[$idElement][$resType]/2) * ($niveau-1));
				}
			}else{
				$cout = $grillePrix[$idElement][$resType];
			}
			$prix[$resType] = $cout;
		}
		return($prix);
	}

	function isAchetable($idElement, $niveau){
		$idJoueur = $_SESSION['idJoueur'];
		global $grillePrix , $grilleRessources, $grilleSuivant ;
		$achetable = true ;
		foreach ($grilleRessources as $resType){
			//si c'est incrementable et un batiment
			if(isIncremental($idElement) && $niveau != 0 && !isUnite($idElement) && !isRecherche($idElement)){
				$niveauMax = getNiveauMax($idElement);
				if ($niveau == 1){
					$cout = $grillePrix[$idElement][$resType];
				}else{
					$cout = $grillePrix[$idElement][$resType] + (($grillePrix[$idElement][$resType]/2) * ($niveau-1));
				}
			}else{
				$cout = $grillePrix[$idElement][$resType];
			}
			$ressourcesJoueur = getRessources($idJoueur);
			if ($cout > $ressourcesJoueur[$resType]){
					return(false);
			}
		}
		return $achetable ;
	}
	
	function areAchetable($idElement, $niveau, $nombre){
		$idJoueur = $_SESSION['idJoueur'];
		global $grillePrix , $grilleRessources, $grilleSuivant ;
		$achetable = true ;
		foreach ($grilleRessources as $resType){
			//si c'est incrementable et un batiment
			if(isIncremental($idElement) && $niveau != 0 && !isUnite($idElement) && !isRecherche($idElement)){
				$niveauMax = getNiveauMax($idElement);
				if ($niveau == 1){
					$cout = $grillePrix[$idElement][$resType];
				}else{
					$cout = ($grillePrix[$idElement][$resType] * ($niveau *1.5));
				}
			}else{
				$cout = $grillePrix[$idElement][$resType];
			}
			$ressourcesJoueur = getRessources($idJoueur);
			if (($cout*$nombre) > $ressourcesJoueur[$resType]){
					return(false);
			}
		}
		return $achetable ;
	}

	function acheter($idElement, $niveau){
		$idJoueur = $_SESSION['idJoueur'];
		global $grillePrix , $grilleRessources, $grilleSuivant ;
		$query = "SELECT vitesseServeur FROM territoire,joueurs WHERE idJoueur='$idJoueur'";
		$result = mysql_query($query) or die("Query error dans acheter : ". mysql_error());
		$vitesseServeur = mysql_result($result,0);
		$tempsConstruction = 0 ;
		foreach ($grilleRessources as $resType){
			if(isIncremental($idElement) && $niveau != 1 && !isUnite($idElement) && !isRecherche($idElement)){
				$niveauMax = getNiveauMax($idElement);
				if ($niveau == 1){
					$cout = $grillePrix[$idElement][$resType];
				}else{
					$cout = $grillePrix[$idElement][$resType] + (($grillePrix[$idElement][$resType]/2) * ($niveau-1));
				}
			}else{
				$cout = $grillePrix[$idElement][$resType];
			}
			$tempsConstruction = $tempsConstruction + ($cout / (1000 + $vitesseServeur));
			$ressourcesJoueur = getRessources($idJoueur);
			$argentJoueur = $ressourcesJoueur[$resType] ;
			$reste =  $argentJoueur - $cout ;
			$query = "UPDATE joueurs SET $resType = $reste WHERE idJoueur='$idJoueur'";
			$result = mysql_query($query) or die($query . " a provoqu� Query error: " . mysql_error() . " et cout = " . $cout . " et element = " . getNameElement(getIdElement($sigleElement)) . " et ResType = " . $ResType . " et grillePrix = " . $grillePrix[$element][$ResType]);
		}
		return($tempsConstruction);
	}

	function acheterMulti($idElement, $niveau, $nombre){
		$idJoueur = $_SESSION['idJoueur'];
		global $grillePrix , $grilleRessources, $grilleSuivant ;
		$query = "SELECT vitesseServeur FROM territoire,joueurs WHERE idJoueur='$idJoueur'";
		$result = mysql_query($query) or die("Query error dans acheter : ". mysql_error());
		$vitesseServeur = mysql_result($result,0);
		$tempsConstruction = 0 ;
		foreach ($grilleRessources as $resType){
			if(isIncremental($idElement) && $niveau != 1 && !isUnite($idElement) && !isRecherche($idElement)){
				$niveauMax = getNiveauMax($idElement);
				if ($niveau == 1){
					$cout = $grillePrix[$idElement][$resType]*$nombre;
				}else{
					$cout = ($grillePrix[$idElement][$resType] * ($niveau *1.5))*$nombre;
				}
			}else{
				$cout = $grillePrix[$idElement][$resType]*$nombre;
			}
			$tempsConstruction = $tempsConstruction + ($cout / (1000 + $vitesseServeur));
			$ressourcesJoueur = getRessources($idJoueur);
			$argentJoueur = $ressourcesJoueur[$resType] ;
			$reste =  $argentJoueur - $cout ;
			$query = "UPDATE joueurs SET $resType = $reste WHERE idJoueur='$idJoueur'";
			$result = mysql_query($query) or die($query . " a provoqu� Query error: " . mysql_error() . " et cout = " . $cout . " et element = " . getNameElement(getIdElement($sigleElement)) . " et ResType = " . $ResType . " et grillePrix = " . $grillePrix[$element][$ResType]);
		}
		return($tempsConstruction);
	}

	function downgradeElementProvince($idProvince){
		//on remet le niveau inferieur si il est superieur a 1
		$niveau = getNiveauBatimentProvince($idProvince);
		$sigle = getElementProvince($idProvince);
		if ($niveau > 1){
			$niveau-- ;
		}else{
			$elementPrecedent = getElementPrecedent($sigle) ;
			foreach($elementPrecedent as $idElementPrecedent => $niveauElementPrecedent){
				$sigle = getSigleElement($idElementPrecedent) ;
				$niveau = $niveauElementPrecedent;
			}
		}
		
		$query = "UPDATE provinces_joueur SET batimentProvince='$sigle', niveauBatimentProvince='$niveau' WHERE provinces_joueur.idProvince = '$idProvince'";
		$result = mysql_query($query) or die("query error dans la destruction du batiment de la province $idProvince UPDATE2: " .mysql_error());
	}
		
	function getElementProvince($idProvince){
		$query = "SELECT batimentProvince FROM provinces_joueur WHERE provinces_joueur.idProvince='$idProvince'";
		$result = mysql_query($query) or die("Query error dans getElementProvince: ". mysql_error());
		$sigleElement = mysql_result($result,0);
		return($sigleElement);
	}
	function isUnderConstruction($idProvince){
		$query = "SELECT underConstruction FROM provinces_joueur WHERE provinces_joueur.idProvince='$idProvince'";
		$result = mysql_query($query) or die("Query error dans isUnderConstruction: ". mysql_error());
		$underConstruction = mysql_result($result,0);
		return($underConstruction);
	}
	
	function getBatimentProvince($idProvince){
		$query = "SELECT batimentProvince FROM provinces_joueur WHERE provinces_joueur.idProvince=$idProvince";
		$result = mysql_query($query) or die("Query error getBatimentProvince : ". mysql_error());
		$row = mysql_fetch_array ($result);
		return($row ['batimentProvince']);
	}
	
	function getIdBatimentProvince($idProvince){
		global $grilleSiglesElements ;
		$query = "SELECT batimentProvince FROM provinces_joueur WHERE provinces_joueur.idProvince=$idProvince";
		$result = mysql_query($query) or die("Query error getIdBatimentProvince : ". mysql_error());
		$batimentProvince = mysql_result ($result,0);
		$idBatimentProvince = array_search($batimentProvince, $grilleSiglesElements);
		return($idBatimentProvince);
	}
	
	function getNiveauBatimentProvince($idProvince){
		global $grilleSiglesElements ;
		$query = "SELECT niveauBatimentProvince FROM provinces_joueur WHERE provinces_joueur.idProvince=$idProvince";
		$result = mysql_query($query) or die("Query error getNiveauBatimentProvince : ". mysql_error());
		$niveauBatimentProvince = mysql_result ($result,0);
		return($niveauBatimentProvince);
	}
	
	function getImageBatimentProvince($idProvince){
		$query = "SELECT batimentProvince,niveauBatimentProvince FROM provinces_joueur WHERE provinces_joueur.idProvince=$idProvince";
		$result = mysql_query($query) or die("Query error getImageBatimentProvince : ". mysql_error());
		$row = mysql_fetch_array ($result);
		$sigleBatiment = $row ['batimentProvince'];
		$niveau = $row ['niveauBatimentProvince'];
		return("./style/images/batiments/$sigleBatiment/$niveau.gif");
	}
	
	function isBatimentHabitation($idElement){
		include_once("arbre.php");
		global $reslist ;
		return(array_search($idElement,$reslist['batimentHabitation']));
	}
	
	function isBatimentEntrainement($idElement){
		include_once("arbre.php");
		global $reslist ;
		return(array_search($idElement,$reslist['batimentEntrainement']));
	}
	
	function isBatimentRecherche($idElement){
		include_once("arbre.php");
		global $reslist ;
		return(array_search($idElement,$reslist['batimentRecherche']));
	}
	
	function isBatimentProduction($idElement){
		include_once("arbre.php");
		global $reslist ;
		return(array_search($idElement,$reslist['batimentProduction']));
	}
		
	function isBatimentStockage($idElement){
		include_once("arbre.php");
		global $reslist ;
		return(array_search($idElement,$reslist['batimentStockage']));
	}
	
	function isBatimentUpgradable($idElement){
		include_once("arbre.php");
		global $reslist ;
		return(array_search($idElement,$reslist['incremental']));
	}
	
	function isUnite($idElement){
		include_once("arbre.php");
		global $reslist ;
		return(array_search($idElement,$reslist['unites']));
	}
	
	function isEspion($idElement){
		include_once("arbre.php");
		global $reslist ;
		return(array_search($idElement,$reslist['espions']));
	}
	
	function isRecherche($idElement){
		include_once("arbre.php");
		global $reslist ;
		return(array_search($idElement,$reslist['recherches']));
	}
	?>