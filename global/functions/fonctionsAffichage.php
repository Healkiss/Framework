<?php
///////////////////////////////////////////////////////////////
	//elements de decors et d'affichage//
///////////////////////////////////////////////////////////////
	function afficherPrix($prix){
		$idJoueur = $_SESSION['idJoueur'];
		global $grilleRessources;
		foreach ($grilleRessources as $type){
			$cout = $prix[$type];
			echo "<td class='cout' >";
			$ressourcesJoueur = getRessources($idJoueur);
			if ($cout > $ressourcesJoueur[$type]){
					echo  "<b  style='color:red;'>" . shortly_number($cout) . "</b>";
			}else{
					echo  shortly_number($cout) ;
			}
			echo "</td>";
		}
	}
	
	function afficherRoyaume($x_min,$y_min,$y_max,$x_max){
		$nom = $_SESSION['pseudo'];
		$idJoueur = $_SESSION['idJoueur'];
		$query = "SELECT idTerritoire FROM joueurs WHERE idJoueur = '$idJoueur'";
		$result = mysql_query($query)or die("Query error: ". mysql_error());
		$row = mysql_fetch_assoc($result);
		$idTerritoire = $row['idTerritoire'];
		$batimentsRoyaume = getElementsReelRoyaume($idJoueur);
		$tailleX = $x_max - $x_min ;
		echo "<table id='cartePerso' style='background-image:url(./style/images/texture.jpg); width:100*$tailleX;' border='0' cellspacing='0' cellpadding='0'>";
			echo "<tbody>";
				for ($x = $x_min ; $x <= $x_max ; $x++){
					echo "<tr>";
					for ($y = $y_min ; $y <= $y_max ; $y++){
						$numProvince = getNumProvince($x,$y,$idTerritoire);
						if (isProvinceJoueurWithDetails($numProvince,$batimentsRoyaume)){
							if (isUnderConstruction($numProvince)){
								$imageBatiment = "./style/images/batiments/underConstruction/0.gif";
								if(tempsRestantBatiment($numProvince) <= 0){
									construire($numProvince);
									$imageBatiment = getImageBatimentProvince($numProvince);
								}
							}else{
								$imageBatiment = getImageBatimentProvince($numProvince);
							}
							$nomBatiment = getNameElement(getIdElement(getElementProvince($numProvince)));
							$niveau = getNiveauBatimentProvince($numProvince);
							if (isUnderConstruction($numProvince)){
								$tempsRestant = tempsRestantBatiment($numProvince);
								echo "<td class='province active' onclick='allerProvince($numProvince)' style='background-image:url($imageBatiment)' title='$nomBatiment niveau : $niveau'><label class='chrono'>$tempsRestant</label></td>";
							}else{
								if ($nomBatiment == "terrain constructible"){
									echo "<td class='province active' onclick='allerProvince($numProvince)' title='$nomBatiment niveau : $niveau'></td>";
								}else{
									echo "<td class='province active' onclick='allerProvince($numProvince)' style='background-image:url($imageBatiment)' title='$nomBatiment niveau : $niveau'></td>";
								}
							}
						}else{
							echo "<td class='province NonPossede' onclick='allerProvince($numProvince)'></td>";
						}
					}
					echo "</tr>";
				}
			echo "</tbody>";
		echo "</table>";
	}
	
	//ancienne fonction
	function textRessource(){
		require_once("arbre.php");
		require_once("fonctionsCommunes.php");
		require_once("fonctionsProvince.php");
		global $grilleRessources ;
		$idJoueur = $_SESSION['idJoueur'] ;
		$textRessources = "<div id='block-center'><span class='underline'><h4>Ressources</h4></span></div>";
			foreach ($grilleRessources as $ResType){
				$textRessources .= "<img class='icone' alt='$ResType' src='./style/images/ressources/".$ResType.".gif' title='$ResType'/>";
				$array3 = getRessources($idJoueur);
				$textRessources .=shortly_number(floor($array3[$ResType])) . "/".shortly_number(floor(getMaxRessource($idJoueur, $ResType)));
			}
		return($textRessources );
	}
	
	//nouvelle fonction
	function textRessource2(){
		require_once("arbre.php");
		require_once("fonctionsCommunes.php");
		require_once("fonctionsProvince.php");
		global $grilleRessources ;
		$idJoueur = $_SESSION['idJoueur'] ;
		$textRessources = "";
			foreach ($grilleRessources as $ResType){
				$textRessources .= "<img class='icone_menu' alt='$ResType' src='./style/images/ressources/".$ResType.".gif' title='$ResType'/>";
				$array3 = getRessources($idJoueur);
				$textRessources .= shortly_number(floor($array3[$ResType]));
			}
		return($textRessources );
	}
	
	//ancienne fonction
	function textPopulation(){
		require_once("fonctionsCommunes.php");
		$idJoueur = $_SESSION['idJoueur'] ;
		$textPopulation = "<div id='block-center'>";
		$textPopulation .= "<span class='underline'>Population</span></div>";
		if (getPopulation($idJoueur) >= getPopulationMax($idJoueur)){
			$textPopulation .= "<b  style='color:red;'>".getPopulation($idJoueur) ." </b>/ " . getPopulationMax($idJoueur);
		}else{
			$textPopulation .= getPopulation($idJoueur) ." / " . getPopulationMax($idJoueur);
		}
		return($textPopulation);
	}
	
	//nouvelle fonction
	function textPopulation2(){
		require_once("fonctionsCommunes.php");
		$idJoueur = $_SESSION['idJoueur'] ;
		$textPopulation = "<span class='underline'>Population</span></br>";
		if (getPopulation($idJoueur) >= getPopulationMax($idJoueur)){
			$textPopulation .= "<b  style='color:red;'>".getPopulation($idJoueur) ." </b>/ " . getPopulationMax($idJoueur);
		}else{
			$textPopulation .= getPopulation($idJoueur) ." / " . getPopulationMax($idJoueur);
		}
		return($textPopulation);
	}
	//ancienne fonction
	function textUnites(){
		require_once("arbre.php");
		require_once("fonctionsCommunes.php");
		require_once("fonctionsProvince.php");
		global $reslist ;
		$idJoueur = $_SESSION['idJoueur'] ;
		$unites = getUnites($idJoueur);
		$textUnites = "<div id='block-center'><span class='underline'><h4>Unites</h4></span></div>";
			foreach ($unites as $sigleUnite => $nombreUnites){
				if($nombreUnites>0){
					$nameUnite = getNameElementBySigle($sigleUnite);
					$textUnites .= "<img class='icone' src='./style/images/unites/$nameUnite/".$nameUnite.".gif' alt='$nameUnite' title='$nameUnite'/>";
					$textUnites .= shortly_number($nombreUnites);
				}
			}
		return($textUnites);
	}
	
	//nouvelle fonction
	function textUnites2(){
		require_once("arbre.php");
		require_once("fonctionsCommunes.php");
		require_once("fonctionsProvince.php");
		global $reslist ;
		$idJoueur = $_SESSION['idJoueur'] ;
		$unites = getUnites($idJoueur);
		$textUnites = "";
			foreach ($unites as $sigleUnite => $nombreUnites){
				$nameUnite = getNameElementBySigle($sigleUnite);
				$textUnites .= "<img class='icone_menu' src='./style/images/unites/$nameUnite/".$nameUnite.".gif' alt='$nameUnite' title='$nameUnite'/>";
				$textUnites .= shortly_number($nombreUnites);
			}
		return($textUnites);
	}
	
	function afficherArbreBatiments(){
		global $reslist, $grilleSuivant ;
		echo "<table class='achat' class='border1'>";
			echo "<tr>";
			echo "<th>Batiments</th>";
			echo "<th>Evolutions</th>";
			echo "</tr>";
			foreach($reslist['batiments'] as $element){
			echo "<tr>";
			echo "<th>";
				echo getNameElement($element);
			echo "</th>";
				foreach($grilleSuivant[$element] as $elementSuivant){
					echo "<td>";
						echo getNameElement($elementSuivant);
					echo "</td>";
				}
			echo "</tr>";
			}
		echo "</table>";
	}
	
	function afficherArbreBatimentsJoueur(){
	$idJoueur =  $_SESSION['idJoueur'] ;
		global $reslist, $grilleSuivant ;
		$elementsRoyaume = getElementsRoyaume($idJoueur) ;
		echo "<table id='achat' border='1' style='border: medium solid #FFFFFF' cellspacing = '0' >";
			echo "<tr>";
			echo "<th>Batiments</th>";
			echo "<th>Permet</th>";
			echo "</tr>";
			foreach($reslist['batiments'] as $element){
			echo "<tr>";
					$nomElement = getNameElement($element);
					if (array_key_exists(getSigleElement($element),$elementsRoyaume)){
						echo "<th bgcolor = green>$nomElement</th>";
					}else{
						echo "<th bgcolor = red>$nomElement</th>";
					}
				foreach($grilleSuivant[$element] as $elementSuivant){
					$nomElement = getNameElement($elementSuivant);
					if (array_key_exists(getSigleElement($elementSuivant),$elementsRoyaume)){
						echo "<td bgcolor = green>$nomElement</td>";
					}else{
						echo "<td bgcolor = red>$nomElement</td>";
					}
				}
			echo "</tr>";
			}
		echo "<caption style=' border: 1; height: 30px; text-align: left; vertical-align: middle; font-weight: bold '><th style='color:black;' bgcolor = green>Construit</th><th style='color:black;' bgcolor = red>Non Construit</th></caption>";
		echo "</table>";
	}
	
	function afficherArbreHommes(){
		global $reslist, $grilleRequireProvince ;
		echo "<table id='achat' border='1' style='border: medium solid #FFFFFF' cellspacing = '0' >";
			echo "<tr>";
			echo "<th>Unites</th>";
			echo "<th>Requierent</th>";
			echo "</tr>";
			foreach($reslist['unites'] as $element){
			echo "<tr>";
			echo "<th>";
				echo getNameElement($element);
			echo "</th>";
				foreach($grilleRequireProvince[$element] as $elementSuivant => $niveau){
					echo "<td>";
						echo getNameElement($elementSuivant);
					echo "</td>";
				}
			echo "</tr>";
			}
		echo "</table>";
	}
	
	function afficherProductionRessource(){
		global $reslist,$grilleProduction, $grilleRessources ;
		$idJoueur = $_SESSION['idJoueur'] ;
		$elementsRoyaume = getElementsRoyaume($idJoueur) ;
		echo "<table id='achat' border='1' style='border: medium solid #FFFFFF' cellspacing = '0' >";
			echo "<tr>";
			echo "<th class='iconeRessource'>Ressources</th>";
			echo "<th>Stocks</th>";
			echo "<th>Stockage maximale</th>";
			echo "<th>Production / heure </th>";
			echo "<th>Production / minute </th>";
			echo "<th>Production / seconde </th>";
			echo "</tr>";
			$pair = 1;
			foreach($grilleRessources as $ressource){
			if ($pair == 1){
				echo "<tr class='impair'>";
			}else{
				echo "<tr class='pair'>";
			}
				$ressourceP = $ressource . "P" ;
				$stock = floor(getRessource($ressource,$idJoueur));
				$productionP = getPRessource($ressource,$idJoueur);
				$productionPM = floor($productionP /60);
				$productionPS =floor($productionP /3600) ;
				echo "<td class='iconeRessource'><img class='icone' src='./style/images/ressources/$ressource.gif' title='$ressource' alt='$ressource'/></td>";
				echo "<td>".shortly_number($stock)."</td>";
				echo "<td>".getMaxRessource($idJoueur, $ressource)."</td>";
				echo "<td>".shortly_number($productionP)." /h</td>";
				echo "<td>".shortly_number($productionPM)." /m</td>";
				echo "<td>".shortly_number($productionPS)." /s</td>";

			echo "</tr>";
			if ($pair == 1){
				$pair = 0;
			}else{
				$pair = 1;
			}
			}
		echo "</table>";
	}
	
	function afficherEvenements($idJoueur){
		include_once("connBdd.php");
		$query = "SELECT * FROM evenement WHERE idJoueur=$idJoueur ORDER BY idEvenement";
		$result = mysql_query($query) or die("Query error dans afficherEvenements: ". mysql_error());
		echo "<table>";
		while($row = mysql_fetch_array($result)){
			$idEvenement = $row['idEvenement'];
			echo"<tr>";
				if($row['typeEvenement'] == 'construction'){
					echo "<td>";
						echo 'une ';
						echo $row['typeEvenement'];
						echo " sur la province ";
						echo $row['provinceDepart'];
						echo ' de ';
						echo $row['sigleBatimentEnConstruction'];
						echo " niveau : ";
						echo $row['niveauBatimentEnConstruction'];
						echo " Il reste : ";
						$tempsRestant = tempRestantByIdEvenement($idEvenement) +1 ;
						echo "<label class='chrono'>$tempsRestant</label>";
						echo ' secondes';
					echo "</td>";
				}
				if($row['typeEvenement'] == 'attaque_sortante'){
					echo "<td>";
						echo 'une attaque sortante ';
						echo " de la province ";
						echo $row['provinceDepart'];
						echo " vers la province ";
						echo $row['provinceArrivee'];
						echo ' avec : ';
						$unites = unserialize($row['unitesEnMouvement']);
						foreach($unites as $sigleUnite => $nbUnites){
							echo " ".$nbUnites." ".$sigleUnite." ;";
						}
						echo " Il reste : ";
						$tempsRestant = tempRestantByIdEvenement($idEvenement) + 1 ;
						echo "<label class='chrono'>$tempsRestant</label>";
						echo ' secondes';
					echo "</td>";
				}
				if($row['typeEvenement'] == 'attaque_sortante_retour'){
					echo "<td>";
						echo 'une attaque sortante sur le retour';
						echo " statut bataille : ";
						echo $row['statut'];
						echo " de la province ";
						echo $row['provinceDepart'];
						echo " vers la province ";
						echo $row['provinceArrivee'];
						echo ' avec : ';
						$unites = unserialize($row['unitesEnMouvement']);
						foreach($unites as $sigleUnite => $nbUnites){
							echo " ".$nbUnites." ".$sigleUnite." ;";
						}
						if($row['statut'] == 'Gagnant'){
							echo ' avec comme ressources pillees : ';
							$ressources = unserialize($row['ressources']);
							foreach($ressources as $type => $montant){
								echo " ".$montant." ".$type." ;";
							}
						}
						echo " Il reste : ";
						$tempsRestant = tempRestantByIdEvenement($idEvenement) + 1 ;
						echo "<label class='chrono'>$tempsRestant</label>";
						echo ' secondes';
					echo "</td>";
				}
			echo "</tr>";
		}
		echo "</table>";
	}
?>