<?php
	function updateRessources($idJoueur){
		include("connBdd.php");
		include_once("arbre.php");
		global $grilleRessources ;
		//recuperation de l'heure actuelle et la derniere maj
		//si elles sont diferrente, calculer la difference en seconde
		$heureActuelleNF = getdate();
		$query = "SELECT derniereMaj FROM joueurs WHERE idJoueur='$idJoueur'";
		$result = mysql_query($query) or die("Query error: ". mysql_error());
		$derniereMaj = mysql_result($result,0); 
		$heureMajNF = unserialize($derniereMaj);
		$heureActuelle = getTimeStamp($heureActuelleNF);
		$heureMaj = getTimeStamp($heureMajNF);
		$diffEnSecondes = $heureActuelle - $heureMaj ;
		if ($diffEnSecondes>0){
			foreach ($grilleRessources as $ressourceType){
				$ressourceParSeconde = calculateRessourceParSeconde(getPRessource($ressourceType,$idJoueur));
				$ressourceAAjouter = $diffEnSecondes * $ressourceParSeconde ;
				addRessource($idJoueur, $ressourceType, $ressourceAAjouter);
			}
			$heureActuelle = serialize($heureActuelleNF);
			$query = "UPDATE joueurs SET derniereMaj='$heureActuelle' WHERE idJoueur='$idJoueur'";
			$result = mysql_query($query) or die("Query error dans update ressources: ". mysql_error());
		}
	}
	
	function getTimeStamp($date){
		return(mktime($date['hours'],$date['minutes'],$date['seconds'],$date['mon'],$date['mday'],$date['year']));
	}
	
	function getDateFormatee($date){
		return($date["mday"].".".$date["mon"].".".$date["year"]." ".$date["hours"].":".$date["minutes"]."-".$date["seconds"]);
	}
		
	function getDateFormateeFromTimeStamp($date){
		return date('d m Y H:i', $date);
	}
	
	function tempsRestantBatiment($province){	
		$idJoueur = $_SESSION['idJoueur'] ;
		$query = "SELECT * FROM evenement WHERE provinceDepart='$province' ";
		$result = mysql_query($query) or die("Query error dans tempsRestant: ". mysql_error());
		$row = mysql_fetch_array($result);
		$dateFin = $row['dateFin'];
		$heureActuelleNF = getdate();
		$heureActuelle = getTimeStamp($heureActuelleNF);
		return ($dateFin - $heureActuelle);
	}
	
	function tempRestant($evenement){
		$heureActuelleNF = getdate();
		$heureActuelle = getTimeStamp($heureActuelleNF);
		$dateFin = $evenement['dateFin'];
		return($dateFin - $heureActuelle);
	}
	
	function tempRestantByIdEvenement($idEvenement){
		$idJoueur = $_SESSION['idJoueur'] ;
		$query = "SELECT * FROM evenement WHERE idEvenement='$idEvenement' ";
		$result = mysql_query($query) or die("Query error dans tempsRestant: ". mysql_error());
		$row = mysql_fetch_array($result);
		$dateFin = $row['dateFin'];
		$heureActuelleNF = getdate();
		$heureActuelle = getTimeStamp($heureActuelleNF);
		return ($dateFin - $heureActuelle);
	}
	
	function checkBatimentsUnderConstruction($idJoueur){
		global $reslist ;
		$ProvincesUnderConstruction = getElementsUnderConstructionRoyaume($idJoueur);
		foreach($ProvincesUnderConstruction as $idProvince){
			if(tempsRestantBatiment($idProvince) <= 0 ){
				$idBatiment = getidElement(getElementProvince($idProvince));
				if(in_array($idBatiment,$reslist['batimentProduction'])){
					updateRessources($idJoueur);
				}
				construire($idProvince);
			}
		}
	}
	
	function getEvenements($idJoueur){
		include_once("connBdd.php");
		$query = "SELECT * FROM evenement WHERE idJoueur=$idJoueur ORDER BY idEvenement";
		$result = mysql_query($query) or die("Query error dans afficherEvenements: ". mysql_error());
		$evenements = array();
		$numEvent = 0;
		while($row = mysql_fetch_array($result)){
			$evenements[$numEvent]['idEvenement'] = $row['idEvenement'];
			$evenements[$numEvent]['provinceDepart'] = $row['provinceDepart'];
			$evenements[$numEvent]['provinceArrivee'] = $row['provinceArrivee'];
			$evenements[$numEvent]['sigleBatimentEnConstruction'] = $row['sigleBatimentEnConstruction'];
			$evenements[$numEvent]['niveauBatimentEnConstruction'] = $row['niveauBatimentEnConstruction'];
			$unites = $row['unitesEnMouvement'];
			if($unites != ""){
				$evenements[$numEvent]['unitesEnMouvement'] = unserialize($row['unitesEnMouvement']);
			}else{
				$evenements[$numEvent]['unitesEnMouvement'] = array();
			}
			$ressources = $row['ressources'];
			if($ressources){
				$evenements[$numEvent]['ressources'] = unserialize($row['ressources']);
			}else{
				$evenements[$numEvent]['ressources'] = array();
			}
			$evenements[$numEvent]['dateDebut'] = $row['dateDebut'];
			$evenements[$numEvent]['dateFin'] = $row['dateFin'];
			$evenements[$numEvent]['typeEvenement'] = $row['typeEvenement'];
			$evenements[$numEvent]['idJoueur'] = $row['idJoueur'];
			$numEvent++;
		}
		return($evenements);
	}
	
		/**
	 * Check all events and call functions needed for termineded events
	 * TODO check array from getEvenements and apply if necessary events
	 * @param $idJoueur joueur dont on va checker les events
	 * 
	 */
	function checkEvenements($idJoueur){
		$evenements = getEvenements($idJoueur);
			foreach($evenements as $evenement){
				if(tempRestant($evenement) <= 0){
					if ($evenement['typeEvenement'] == "construction"){
						$idProvince = $evenement['provinceDepart'];
						logEventConstruction($evenement);
						construire($idProvince);
					}else{
						if ($evenement['typeEvenement'] == "attaque_sortante"){
							$idEvenement = $evenement['idEvenement'];
							logEventAttaque($evenement);
							attaquer($evenement);
						}else{
							if ($evenement['typeEvenement'] == "attaque_sortante_retour"){
								logEventRetourAttaque($evenement);
								retourAttaque($evenement);
							}
						}
					}
				}
			}
	}
?>