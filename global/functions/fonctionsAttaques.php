<?php
	function combattre($idAttaquant, $unitesAttaquant, $heureAttaque, $idDefenseur,$idProvince){
		if(file_exists("./fonctionsCommunes.php")){
			require_once("./fonctionsCommunes.php");
		}else{
			if(file_exists("./controleurs/fonctionsCommunes.php")){
				require_once("./controleurs/fonctionsCommunes.php");
			}
		}
		if(file_exists("./fonctions/processRoyaume.php")){
			require_once("./fonctions/processRoyaume.php");
		}else{
			if(file_exists("./controleurs/fonctions/processRoyaume.php")){
				require_once("./controleurs/fonctions/processRoyaume.php");
			}
		}
				if(file_exists("./arbre.php")){
			require_once("./arbre.php");
		}else{
			if(file_exists("./controleurs/arbre.php")){
				require_once("./controleurs/arbre.php");
			}
		}
		
		global $grilleSpecs ;
		$unitesDefenseur = getUnites($idDefenseur);
		$degatsAttaque = (getDegatsAttaque($unitesAttaquant)/100)*(100-processDefenseBonus($idDefenseur)) ;
		$degatsDefense = getDegatsDefense($idDefenseur);
		$nbAttaquant = getNombreUnites($unitesAttaquant) ;
		$nbDefenseur = getNombreUnites($unitesDefenseur) ;
		
		$vulnerabiliteAttaquant = getVunerabiliteArmee($unitesAttaquant);
		$vulnerabiliteDefenseur = getVunerabiliteArmee($unitesDefenseur);
		
		$differenceDegatsAttaque = $degatsAttaque - $degatsDefense  ;
		$differenceDegatsDefense = $degatsDefense - $degatsAttaque ;
		
		//ATTAQUE :
		$tableauDefenseursMorts = calculateDeads($unitesDefenseur, $degatsAttaque);
		$tableauDefenseursSurvivants = calculateSurvivors($unitesDefenseur, $tableauDefenseursMorts);
		//DEFENSE :
		$tableauAttaquantsMorts = calculateDeads($unitesAttaquant, $degatsDefense);
		$tableauAttaquantsSurvivants = calculateSurvivors($unitesAttaquant, $tableauAttaquantsMorts);
		
		//on tue les unites apres les combats pour pas influencer le cours de la bataille
		tuerUnites($tableauDefenseursMorts, $idDefenseur);
		
		
		//calcul le nombre de survivants et en deduit si il y a un gagnant
		if (!isAWinner($tableauAttaquantsSurvivants, $tableauDefenseursSurvivants)){
			$gagnant[99] = "egalite";
			$gagnant[1] = $idDefenseur;
			$gagnant[0] = $idAttaquant;
			setRetourAttaqueEgalite($idProvince, $idProvince, $tableauAttaquantsSurvivants, "egalite", $heureAttaque, $idAttaquant);
		}else{
			$totalSurvivantsDefenseur = getNombreUnites($tableauDefenseursSurvivants) ;
			if($totalSurvivantsDefenseur > 0){
				$gagnant[99] = "defenseur";
				$gagnant[0] = $idDefenseur;
				$gagnant[1] = $idAttaquant;
				addProvinceOccupe($idAttaquant,$idProvince);
			}else{
				$gagnant[99] = "attaquant";
				$gagnant[1] = $idDefenseur;
				$gagnant[0] = $idAttaquant;
				$butinGagnant = piller($tableauAttaquantsSurvivants, $idDefenseur);
				setRetourAttaqueGagnant($idProvince, $idProvince, $tableauAttaquantsSurvivants, $butinGagnant,"gagnant", $heureAttaque, $idAttaquant);

			}
		}
		$gagnant[2] = $tableauAttaquantsMorts ;
		$gagnant[3] = $tableauDefenseursMorts ;
		$gagnant[4] = $tableauAttaquantsSurvivants ;
		$gagnant[5] = $tableauDefenseursSurvivants ;
		$gagnant[6] = $idProvince;
		$gagnant[7] = $differenceDegatsAttaque;
		$gagnant[8] = $unitesDefenseur ;
		$gagnant[9] = $unitesAttaquant ;
		$gagnant[10] = $degatsDefense ;
		$gagnant[11] = $degatsAttaque ;
		$gagnant[14] = $nbDefenseur ;
		return($gagnant);
	}
	
	function isAWinner($survivantsAttaquant,$survivantsDefenseur){
		$totalSurvivantsAttaquant = getNombreUnites($survivantsAttaquant);
		$totalSurvivantsDefenseur = getNombreUnites($survivantsDefenseur);
		if ($totalSurvivantsDefenseur > 0 && $totalSurvivantsAttaquant > 0){
			return(false);
		}
		return (true);
	}
	
	//calcul les morts et renvoi le tableau des morts
	//unitesDefenderesses : unites de la defense, puis de l'attaque qui prenne les degats.
	//degatsAttaque : degats des unites qui attaquent, attaque puis defense.
	function calculateDeads($unitesDefenderesses, $degatsAttaque){
		include_once("arbre.php");
		global $reslist, $grilleRessources, $grilleSpecs ;
		$tableauMorts = array();
		$vulnerabiliteDefendant = getVunerabiliteArmee($unitesDefenderesses);
		foreach($unitesDefenderesses as $sigleUnite => $nbUnite){
			if ($nbUnite > 0){
				$idUnite = getIdElement($sigleUnite);
				$degatsRecus = ((($grilleSpecs[$idUnite]['vulnerabilite']*$nbUnite)/$vulnerabiliteDefendant)*$degatsAttaque);
				$morts = round($degatsRecus / $grilleSpecs[$idUnite]['pv']);
				$tableauDegatsRecu[$sigleUnite] = $degatsRecus  ;
				if ($morts > $nbUnite){
					$morts  = $nbUnite ;
				}
				$tableauMorts[$sigleUnite] =  $morts;
			}else{
				$tableauMorts[$sigleUnite] =  0;
			}
		}
		return($tableauMorts);
	}
	
	function calculateSurvivors($unitesDeparts, $tableauMorts){
		foreach($unitesDeparts as $sigleUnite => $nbUnite){
			$morts = $tableauMorts[$sigleUnite];
			$survivants = $nbUnite-$morts ;
			if ($survivants < 0){
				$survivants  = 0 ;
			}
			$tableauSurvivants[$sigleUnite] = $survivants ;
		}
		return ($tableauSurvivants);
	}
	
	function getRecompense($gagnant){
		include_once("arbre.php");
		global $reslist, $grilleRessources, $grilleSpecs ;
		$idJoueur = $gagnant[0] ;
		$idGagnant = $gagnant[0] ;
		$idPerdant = $gagnant[1] ;
		$tableauAttaquantsMorts = $gagnant[2] ;
		$tableauDefenseursMorts = $gagnant[3] ;
		$attaquantsSurvivants = $gagnant[4] ;
		$defenseursSurvivants = $gagnant[5] ;
		$idProvince = $gagnant[6] ;
		$differenceDegatsAttaque = $gagnant[7] ;
		$unitesDefenseur = $gagnant[8] ;
		$unitesAttaquant = $gagnant[9] ;
		$degatsDefense = $gagnant[10] ;
		$degatsAttaque = $gagnant[11] ;
		//$tableauDegatsRecuAttaque = $gagnant[12] ;
		//$tableauDegatsRecuDefense = $gagnant[13] ;
		$nbDefenseur = $gagnant[14] ;
		updateRessources($idPerdant);
		$textRecompense['gagnant'] = "";
		$textRecompense['perdant'] = "" ;
		$textRecompense['egaliteAttaquant']= "" ;
		$textRecompense['egaliteDefenseur']= "" ;
		$date = getdate();
		$dateF = getDateFormatee($date);
		$nomPerdant = getNameJoueur($idPerdant);
		/////////////////////////////////////////////////
		////////LA DEFENSE GAGNE//////////////////////
		////////////////////////////////////////////////
		//elle voit les unites qui l'ont attaque
		//elle capture une unites sur 5 disparues de l'ennemi
		if ($gagnant[99] == "defenseur"){
			$textRecompense['gagnant'] .= "Vous avez &eacute;t&eacute; attaqu&eacute; sur la province <a class='lien_province' href='province.php?province=".$idProvince."'>". shortly_number($idProvince) ."</a> par <b>$nomPerdant</b> le $dateF.<br/>";
			$textRecompense['gagnant'] .= "Vous avez attaqu&eacute; <b>??????????</b> sur sa province <a class='lien_province' href='province.php?province=".$idProvince."'>". shortly_number($idProvince) ."</a> le $dateF.<br/>";
			
			$textRecompense['perdant'] .= "<br/><b> ATTAQUE : </b><br/>";
			$textRecompense['gagnant'] .= "<br/><b> ATTAQUE : </b><br/>";
			foreach($unitesAttaquant as $sigleUnite => $nbUnite){
				if ($nbUnite > 0){
					//singulier -> pluriel
					if ($nbUnite > 1){
						$textRecompense['gagnant'] .= "*<u>".ucfirst($nbUnite)." ".$sigleUnite."s </u><br/>";
						$textRecompense['perdant'] .= "*<u>".ucfirst($nbUnite)." ".$sigleUnite."s </u><br/>";
					}else{
						$textRecompense['gagnant'] .= "*<u>".ucfirst($nbUnite)." $sigleUnite </u><br/>";
						$textRecompense['perdant'] .= "*<u>".ucfirst($nbUnite)." $sigleUnite </u><br/>";
					}
					//$textRecompense['gagnant'] .= "<i>Degats re&ccedil;u : ".$tableauDegatsRecuAttaque[$sigleUnite]."</i> <br/>";
					//$textRecompense['perdant'] .= "<i>Degats re&ccedil;u : ".$tableauDegatsRecuAttaque[$sigleUnite]."</i> <br/>";
					$textRecompense['gagnant'] .= "<b> ".$tableauAttaquantsMorts[$sigleUnite]."</b> morts  -> <b>".$attaquantsSurvivants[$sigleUnite]."</b> survivants<br/>";
					$textRecompense['perdant'] .= "<b> ".$tableauAttaquantsMorts[$sigleUnite]."</b> morts  -> <b>".$attaquantsSurvivants[$sigleUnite]."</b> survivants<br/>";
				}
			}
			$textRecompense['gagnant'] .= "<b>Total des degats re&ccedil;us : $degatsDefense</b><br/>";
		
			$textRecompense['perdant'] .= "<br/><br/><b> DEFENSE : </b><br/>";
			$textRecompense['perdant'] .= "Votre arm&eacute;e n'a pu vous ramener aucun renseignement de la defense ennemi, tous vos hommes sont morts <br/><br/>";
			$textRecompense['gagnant'] .= "<br/><br/><b> DEFENSE : </b><br/>";
			foreach($unitesDefenseur as $sigleUniteDefense => $nbUniteDefense){
				if ($nbUniteDefense > 0){
					//singulier -> pluriel
					if ($nbUniteDefense > 1){
						$textRecompense['gagnant'] .= "*<u> $nbUniteDefense ".$sigleUniteDefense."s </u><br/>";
					}else{
						$textRecompense['gagnant'] .= "*<u>$nbUniteDefense $sigleUniteDefense </u><br/>";
					}
					//$textRecompense['gagnant'] .= "<i>Degats re&ccedil;u : ".$tableauDegatsRecuDefense[$sigleUniteDefense]."</i> <br/>";
					$textRecompense['gagnant'] .= "<b> ".$tableauDefenseursMorts[$sigleUniteDefense]."</b> $sigleUniteDefense morts  -> <b>".$defenseursSurvivants[$sigleUniteDefense]."</b> survivants<br/>";
				}
				$textRecompense['gagnant'] .= "<b>Total des degats re&ccedil;us : $degatsAttaque </b><br/><br/>";
			}
			$textRecompense['gagnant'] .= "L'ennemi se retire et laisse ses prisonnier dans votre camps" ;
			$textRecompense['perdant'] .= "Vous avez perdu de nombreux hommes sur le champ de batille, et vous &ecirc;tes parti sans avoir le temps de recuperer vos prisonniers. <br/>" ;
			$textRecompense['perdant'] .= "Aucun des survivants n'a eu l'occasion de voir un drapeau ennemi. Le mystere entoure toujours la province. <br/>";
			$textRecompense['gagnant'] .= "Votre victoire vous permet de conserver votre anonymat ! <br/>";
			$textRecompense['gagnant'] .= "Vous avez captur&eacute; : <br/>";
			$textRecompense['perdant'] .= "Vous avez laiss&eacute; derriere vous : <br/>";
			$unites = getUnites($idGagnant);
			foreach ($gagnant[2] as $sigleUnite => $nbUnites){
				if ($nbUnites > 0){
					//TODO :
					//faire un nombre variable pour le pourcentage des hommes captur�s
					$unitesCaptures = round($nbUnites/5) ;
					$unites[$sigleUnite] += $unitesCaptures;
					$unitesS = serialize($unites);
					$query = "UPDATE joueurs SET unites='$unitesS' WHERE idJoueur='$idGagnant'";
					$result = mysql_query($query) or die ("query error dans getTextRecompense : " . mysql_error());
					$textRecompense['gagnant'] .= "- $unitesCaptures $sigleUnite <br/>";
					$textRecompense['perdant'] .= "- $unitesCaptures $sigleUnite <br/>";
				}
				//aucun retour, tous les hommes sont morts !!
			}
		}else{
			/////////////////////////////////////////////////
			////////L'ATTAQUE GAGNE//////////////////////
			////////////////////////////////////////////////
			if ($gagnant[99] == "attaquant"){
				//require_once("./fonctionsCommunes.php");
				//require_once("./fonctionsProvince.php");
				$joueurEnnemi = getNameJoueur($idPerdant);
				$textRecompense['perdant'] .= "Vous avez &eacute;t&eacute; attaqu&eacute; sur la province <a class='lien_province' href='province.php?province=".$idProvince."'>". shortly_number($idProvince) ."</a> par <b>??????????</b> le $dateF.<br/>";
				$textRecompense['gagnant'] .= "Vous avez attaqu&eacute; <b>$joueurEnnemi</b> sur sa province <a class='lien_province' href='province.php?province=".$idProvince."'>". shortly_number($idProvince) ."</a> le $dateF.<br/>";
				$textRecompense['gagnant'] .= "<br/>Vous avez remport&eacute; la victoire !<br/>";
				//elle voit les unites en defense
				//en fonction de la difference de degats :
				//0-1000 elle decouvre � qui est le territoire
				//0-1000 elle decouvre � qui est le territoire
				$textRecompense['perdant'] .= "<br/><b> ATTAQUE : </b><br/>";
				$textRecompense['perdant'] .= "Votre arm�e n'a pu vous ramener aucun renseignement de l'attaque ennemi, tous vos hommes sont morts<br/><br/>";
				$textRecompense['gagnant'] .= "<br/><b> ATTAQUE : </b><br/>";
				if ($nbDefenseur > 0){
					foreach($unitesAttaquant as $sigleUnite => $nbUnite){
						if ($nbUnite > 0){
							//singulier -> pluriel
							if ($nbUnite > 1){
								$textRecompense['gagnant'] .= "*<u> $nbUnite ".$sigleUnite."s </u><br/>";
							}else{
								$textRecompense['gagnant'] .= "*<u> $nbUnite $sigleUnite </u><br/>";
							}
							//$textRecompense['gagnant'] .= "<i>Degats re&ccedil;u : ".$tableauDegatsRecuAttaque[$sigleUnite]."</i> <br/>";
							$textRecompense['gagnant'] .= "<b> ".$tableauAttaquantsMorts[$sigleUnite]."</b> $sigleUnite morts  -> <b>".$attaquantsSurvivants[$sigleUnite]."</b> survivants<br/>";
						}
						$textRecompense['gagnant'] .= "<b>Total des degats re&ccedil;us : $degatsDefense</b><br/>";
					}
				}else{
					$textRecompense['gagnant'] .= "<b>Aucune perte, aucune resistance rencontr&eacute;e ! </b><br/><br/>";
				}
			
				$textRecompense['perdant'] .= "<br/><br/><b> DEFENSE : </b><br/>";
				$textRecompense['gagnant'] .= "<br/><br/><b> DEFENSE : </b><br/>";
				if ($nbDefenseur > 0){
					foreach($unitesDefenseur as $sigleUniteDefense => $nbUniteDefense){
						if ($nbUniteDefense > 0){
							//singulier -> pluriel
							if ($nbUniteDefense > 1){
								$textRecompense['gagnant'] .= "*<u> $nbUniteDefense ".$sigleUniteDefense."s </u><br/>";
								$textRecompense['perdant'] .= "*<u> $nbUniteDefense ".$sigleUniteDefense."s </u><br/>";
							}else{
								$textRecompense['gagnant'] .= "*<u>$nbUniteDefense $sigleUniteDefense </u><br/>";
								$textRecompense['perdant'] .= "*<u>$nbUniteDefense $sigleUniteDefense </u><br/>";
							}
							//$textRecompense['gagnant'] .= "<i>Degats re&ccedil;u : ".$tableauDegatsRecuDefense[$sigleUniteDefense]."</i> <br/>";
							//$textRecompense['perdant'] .= "<i>Degats re&ccedil;u : ".$tableauDegatsRecuDefense[$sigleUniteDefense]."</i> <br/>";
							$textRecompense['gagnant'] .= "<b> ".$tableauDefenseursMorts[$sigleUniteDefense]."</b> $sigleUniteDefense morts  -> <b>".$defenseursSurvivants[$sigleUniteDefense]."</b> survivants<br/>";
							$textRecompense['perdant'] .= "<b> ".$tableauDefenseursMorts[$sigleUniteDefense]."</b> $sigleUniteDefense morts  -> <b>".$defenseursSurvivants[$sigleUniteDefense]."</b> survivants<br/>";
						}
					}
					$textRecompense['gagnant'] .= "<b>Total des degats re&ccedil;us : $degatsAttaque </b><br/><br/>";
				}else{
					$textRecompense['gagnant'] .= "<b>La province est desert&eacute;e de toute troupe ennemi ! </b><br/><br/>";
				}
				//on regarde la force totale des unites survivantes
				$differenceDegatsAttaque = getDegatsAttaque($attaquantsSurvivants) ;
				//////////////////////////////////////////////////////////////////////////////////////////////////
				/////////si la difference et qu'il y a qu'un terrain constructible est grande on prend la province
				if ((getIdBatimentProvince($idProvince) == 1) && ($differenceDegatsAttaque > 1000)){
					rmvProvince($idProvince, $idGagnant);
					allouerRegionJoueur($idProvince, $idGagnant);
					$textRecompense['gagnant'] .= "<b>Vous capturez la province !  </b><br/>";
					$textRecompense['perdant'] .= "<b>Vous avez perdu votre province! </b><br/>";
					processRoyaume();
					setDetailsRoyaumeJoueur($idGagnant);
				}else{
					////////////////////////////////////////////////////////////////////////////
					/////////si la difference est grande on retrograde l'element de la province				
					if ($differenceDegatsAttaque >= 1000){
						$niveauASupprimer = floor($differenceDegatsAttaque / 1000) ;
						for($i = 1 ; $i <= $niveauASupprimer ; $i++){
							downgradeElementProvince($idProvince);
						}
						$textRecompense['gagnant'] .= "<b>Vous faites perdre &acute; la province  <u>" . floor($niveauASupprimer) . "</u> niveaux !  </b><br/>";
						$textRecompense['perdant'] .= "<b>Votre province perd <u>" . floor($niveauASupprimer) . "</u> niveaux ! </b><br/>";
					}
					
					if(!provinceEnnemiConnu($idGagnant, $idProvince)){
						addProvinceEnnemi($idGagnant, $idProvince, $idPerdant);
						$textRecompense['gagnant'] .= "Vos survivants vous rapporte des informations sur l'ennemi : leur chef est : ".getNameJoueur($idPerdant)." <br/>";
						$textRecompense['perdant'] .= "Votre defaite laisse fuir avec elle des informations sur votre identit&eacute; ! <br/>";
						setDetailsRoyaumeJoueur($idPerdant);
					}
					if ($differenceDegatsAttaque > 200){
						$textRecompense['gagnant'] .= "Vos survivants vous rapporte des informations sur l'ennemi : le batiment construit sur la province est : ".getNameElementBySigle(getElementProvince($idProvince))." au niveau ".getNiveauBatimentProvince($idProvince)."<br/>";
						$textRecompense['perdant'] .= "Votre defaite laisse fuir avec elle des informations sur le batiment de la province ! <br/>";
						addBatimentProvince($idGagnant, $idProvince, $idPerdant);
					}
				}
				////////////////////////////////////////
				/////////on calcule la capacite de butin
				//set retour avec hommes restants et ressources pill�es
				$textRecompenseButin =	textPillage($attaquantsSurvivants,$idPerdant,$idGagnant);
				$textRecompense['gagnant'] .= $textRecompenseButin['gagnant'];
				$textRecompense['perdant'] .= $textRecompenseButin['perdant'];
			}else{
			////////////////////////////////////////////
			////////////EGALITE////////////////////////
			//////////////////////////////////////////
				$textRecompense['egaliteAttaquant'] = "Vous avez attaqu&eacute; <b>??????????</b> sur sa province <a class='lien_province' href='province.php?province=".$idProvince."'>". shortly_number($idProvince) ."</a> le $dateF.<br/>";
				$textRecompense['egaliteDefenseur'] = "Vous avez &eacute;t&eacute; attaqu&eacute; sur la province <a class='lien_province' href='province.php?province=".$idProvince."'>". shortly_number($idProvince) ."</a> par <b>??????????</b> le $dateF.<br/>";
				
				$textRecompense['egaliteAttaquant'] .= "<br/><b> ATTAQUE : </b><br/>";
				$textRecompense['egaliteDefenseur'] .= "<br/><b> ATTAQUE : </b><br/>";
				foreach($unitesAttaquant as $sigleUnite => $nbUnite){
					if ($nbUnite > 0){
						//singulier -> pluriel
						if ($nbUnite > 1){
							$textRecompense['egaliteAttaquant'] .= "*<u>$nbUnite ".$sigleUnite."s </u><br/>";
							$textRecompense['egaliteDefenseur'] .= "*<u>$nbUnite ".$sigleUnite."s </u><br/>";
						}else{
							$textRecompense['egaliteAttaquant'] .= "*<u>$nbUnite $sigleUnite </u><br/>";
							$textRecompense['egaliteDefenseur'] .= "*<u>$nbUnite $sigleUnite </u><br/>";
						}
						//$textRecompense['gagnant'] .= "<i>Degats re&ccedil;u : ".$tableauDegatsRecuAttaque[$sigleUnite]."</i> <br/>";
						//$textRecompense['perdant'] .= "<i>Degats re&ccedil;u : ".$tableauDegatsRecuAttaque[$sigleUnite]."</i> <br/>";
						$textRecompense['egaliteAttaquant'] .= "<b> ".$tableauAttaquantsMorts[$sigleUnite]."</b> morts  -> <b>".$attaquantsSurvivants[$sigleUnite]."</b> survivants<br/>";
						$textRecompense['egaliteDefenseur'] .= "<b> ".$tableauAttaquantsMorts[$sigleUnite]."</b> morts  -> <b>".$attaquantsSurvivants[$sigleUnite]."</b> survivants<br/>";
					}
				}
				$textRecompense['egaliteAttaquant'] .= "<b>Total des degats re&ccedil;us : $degatsDefense</b><br/>";
				$textRecompense['egaliteDefenseur'] .= "<b>Total des degats inflig&eacute;s : $degatsAttaque </b><br/><br/>";
			
				$textRecompense['egaliteDefenseur'] .= "<br/><br/><b> DEFENSE : </b><br/>";
				$textRecompense['egaliteAttaquant'] .= "<br/><br/><b> DEFENSE : </b><br/>";
				foreach($unitesDefenseur as $sigleUniteDefense => $nbUniteDefense){
					if ($nbUniteDefense > 0){
						//singulier -> pluriel
						if ($nbUniteDefense > 1){
							$textRecompense['egaliteAttaquant'] .= "*<u>".ucfirst($nbUniteDefense)." ".$sigleUniteDefense."s </u><br/>";
							$textRecompense['egaliteDefenseur'] .= "*<u>".ucfirst($nbUniteDefense)." ".$sigleUniteDefense."s </u><br/>";
						}else{
							$textRecompense['egaliteAttaquant'] .= "*<u>$nbUniteDefense $sigleUniteDefense </u><br/>";
							$textRecompense['egaliteDefenseur'] .= "*<u>$nbUniteDefense $sigleUniteDefense </u><br/>";
						}
						//$textRecompense['gagnant'] .= "<i>Degats re&ccedil;u : ".$tableauDegatsRecuAttaque[$sigleUnite]."</i> <br/>";
						//$textRecompense['perdant'] .= "<i>Degats re&ccedil;u : ".$tableauDegatsRecuAttaque[$sigleUnite]."</i> <br/>";
						$textRecompense['egaliteAttaquant'] .= "<b> ".$tableauDefenseursMorts[$sigleUniteDefense]."</b> $sigleUniteDefense morts  -> <b>".$defenseursSurvivants[$sigleUniteDefense]."</b> survivants<br/>";
						$textRecompense['egaliteDefenseur'] .= "<b> ".$tableauDefenseursMorts[$sigleUniteDefense]."</b> $sigleUniteDefense morts  -> <b>".$defenseursSurvivants[$sigleUniteDefense]."</b> survivants<br/>";
					}
				}
				$textRecompense['egaliteAttaquant'] .= "<b>Total des degats inflig&eacute;s : $degatsAttaque </b><br/><br/>";
				$textRecompense['egaliteDefenseur'] .= "<b>Total des degats re&ccedil;us : $degatsAttaque </b><br/><br/>";
					
				$textRecompense['egaliteAttaquant'] .= "Vous repartez sans aucune informations, le combats a ete rude et personne n'en ressort vainqueur." ;
				$textRecompense['egaliteDefenseur'] .= "Vous vous etes bien defendu, l'ennemi est reparti sans aucune informations, et vous n'en savez pas plus sur ce soudain envahisseur. <br/>" ;

			}
		}
		return($textRecompense);
	}
	
	////////////////////////////////////////
	/////////on calcule la capacite de butin
	function piller($attaquantsSurvivants, $idPerdant){
		include_once("arbre.php");
		global $reslist, $grilleRessources, $grilleSpecs ;
		$capacite = 0 ;
		$butinGagnant = array();
		foreach ($attaquantsSurvivants as $sigleUnite => $nbUnites){
			if ($nbUnites > 0){
				$idUnite = getIdElement($sigleUnite) ;
				$capaciteUnites = capaciteButin($idUnite)*$nbUnites ;
				$capacite += $capaciteUnites ;
			}
		}
		foreach ($grilleRessources as $type){
			//on recupere 50 * moins de chevaux que des autres ressources
			if ($type == 'chevaux'){
				$butin = round($capacite/50);
			}else{
				$butin = $capacite;
			}
			$query = "SELECT $type FROM joueurs WHERE idJoueur='$idPerdant'";
			$result = mysql_query($query) or die ("query error dans la fonction textPillage au premier select : " . mysql_error());
			$ressourcesDispos = mysql_result($result,0);
			// si le butin est superieur aux ressources diponible du perdant
			if ($butin > $ressourcesDispos){
				$butin = $ressourcesDispos ;
			}
			rmvRessource($idPerdant,$type,$butin);
			$butinGagnant[$type] = $butin;
		}
		return($butinGagnant);
	}

	function textPillage($attaquantsSurvivants, $idPerdant, $idGagnant){
		include_once("arbre.php");
		global $reslist, $grilleRessources, $grilleSpecs ;
		$capacite = 0 ;
		$textRecompense['perdant'] = "</br>Les troupes ennemis vous pillent : <br/>";
		$textRecompense['gagnant'] = "</br>Vous pillez : <br/>";
		foreach ($attaquantsSurvivants as $sigleUnite => $nbUnites){
			if ($nbUnites > 0){
				$idUnite = getIdElement($sigleUnite) ;
				$capaciteUnites = capaciteButin($idUnite)*$nbUnites ;
				$capacite += $capaciteUnites ;
			}
		}
		foreach ($grilleRessources as $type){
			//on recupere 50 * moins de chevaux que des autres ressources
			if ($type == 'chevaux'){
				$butin = round($capacite/50);
			}else{
				$butin = $capacite;
			}
			$query = "SELECT $type FROM joueurs WHERE idJoueur='$idPerdant'";
			$result = mysql_query($query) or die ("query error dans la fonction textPillage au premier select : " . mysql_error());
			$ressourcesDispos = mysql_result($result,0);
			// si le butin est superieur aux ressources diponible du perdant
			if ($butin > $ressourcesDispos){
				$butin = $ressourcesDispos ;
			}
			$textRecompense['gagnant'] .= "- $butin $type<br/>";
			$textRecompense['perdant'] .= "- $butin $type <br/>";
		}
		return($textRecompense);
	}
	
	function tuerUnites($tableauMorts, $idJoueur){
		$unites = getUnites($idJoueur);
		foreach($tableauMorts as $sigleUnite => $nbUnitesMortes){
			$unites[$sigleUnite] = $unites[$sigleUnite] - $nbUnitesMortes ;
			$unitesS = serialize($unites);
			$query = "UPDATE joueurs SET unites='$unitesS' WHERE idJoueur='$idJoueur'";
			$result = mysql_query($query) or die ("query error dans tuerUnites update1 : " . mysql_error);
		}
	}
	
	function getNombreUnites($unites){
		$nombreUnites = 0 ;
		if ($unites != ""){
			foreach($unites as $sigleUnite => $nbUnite){
				$nombreUnites += $nbUnite;
			}
		}else{
			$nombreUnites = 0;
		}
		return($nombreUnites);
	}
	
	function setRetourAttaqueVisite($idProvinceDepart, $idProvinceArrivee, $unites, $statut, $heureAttaque, $idJoueur){
		require_once("connBdd.php");
		require_once("fonctionsDates.php");
		
		$temps = 10;
		$unitesS = serialize($unites);
		$heureDebut = $heureAttaque;
		$heureFin = $heureAttaque + $temps;
		$query = "INSERT INTO evenement (provinceDepart, provinceArrivee, unitesEnMouvement, dateDebut, dateFin,typeEvenement,statut,idJoueur) VALUES ('$idProvinceDepart','$idProvinceArrivee','$unitesS', '$heureDebut','$heureFin','attaque_sortante_retour','$statut','$idJoueur')";
		$result = mysql_query($query) or die("Query error dans set retour attaque en cas d'egalite: ". mysql_error());
	}
	
	function setRetourAttaqueVide($evenement){
		require_once("connBdd.php");
		require_once("fonctionsDates.php");
		$idJoueur = $evenement['idJoueur'];
		$idProvinceDepart = $evenement['provinceArrivee'];
		$idProvinceArrivee = $evenement['provinceDepart'];
		$unites = $evenement['unitesEnMouvement'];
		$unitesS = serialize($unites);
		$heureDebut = $evenement['dateFin'];
		$temps = 10;
		$heureFin = $heureDebut + $temps;
		$query = "INSERT INTO evenement (provinceDepart, provinceArrivee, unitesEnMouvement, dateDebut, dateFin,typeEvenement,statut,idJoueur) VALUES ('$idProvinceDepart','$idProvinceArrivee','$unitesS', '$heureDebut','$heureFin','attaque_sortante_retour','vide','$idJoueur')";
		$result = mysql_query($query) or die("Query error dans set retour attaque en cas de victoire: ". mysql_error());
	}
		
	function setRetourAttaqueGagnant($idProvinceDepart, $idProvinceArrivee, $unites, $butin, $statut, $heureAttaque, $idJoueur){
		require_once("connBdd.php");
		require_once("fonctionsDates.php");
		
		$temps = 10;
		$unitesS = serialize($unites);
		$butinS = serialize($butin);
		$heureDebut = $heureAttaque;
		$heureFin = $heureAttaque + $temps;
		$query = "INSERT INTO evenement (provinceDepart, provinceArrivee, unitesEnMouvement, ressources, dateDebut, dateFin,typeEvenement,statut,idJoueur) VALUES ('$idProvinceDepart','$idProvinceArrivee','$unitesS', '$butinS', '$heureDebut','$heureFin','attaque_sortante_retour','$statut','$idJoueur')";
		$result = mysql_query($query) or die("Query error dans set retour attaque en cas de victoire: ". mysql_error());
	}
	
	function setRetourAttaqueEgalite($idProvinceDepart, $idProvinceArrivee, $unites, $statut, $heureAttaque, $idJoueur){
		require_once("connBdd.php");
		require_once("fonctionsDates.php");
		
		$temps = 10;
		$unitesS = serialize($unites);
		$heureDebut = $heureAttaque;
		$heureFin = $heureAttaque + $temps;
		$query = "INSERT INTO evenement (provinceDepart, provinceArrivee, unitesEnMouvement, dateDebut, dateFin,typeEvenement,statut,idJoueur) VALUES ('$idProvinceDepart','$idProvinceArrivee','$unitesS', '$heureDebut','$heureFin','attaque_sortante_retour','$statut','$idJoueur')";
		$result = mysql_query($query) or die("Query error dans set retour attaque en cas d'egalite: ". mysql_error());
	}
	
	function afficherElementsEnnemi($idProvince, $ennemis){
		$joueurEnnemiDecouvert = provinceConnu($idProvince, $ennemis);
		$batimentEnnemiDeouvert = batimentConnu($idProvince, $ennemis);
		echo "joueur : " . $joueurEnnemiDecouvert ."<br/>";
		echo "batiment : " .getNameElementBySigle($batimentEnnemiDeouvert['batiment']) ."<br/>";
		echo "niveau : " .$batimentEnnemiDeouvert['niveau'] ."<br/>";
	}
	
	function getJoueurEnnemiProvince($idProvince){
		$query = "SELECT idJoueur FROM provinces_joueur WHERE provinces_joueur.idProvince='$idProvince'";
		$result = mysql_query($query) or die("Query error dans getJoueurEnnemiProvince : ". mysql_error());
		if(mysql_num_rows($result) != 0){
			$joueurEnnemi = mysql_result($result,0);
		}else{
			$joueurEnnemi = "1" ;
		}
		return($joueurEnnemi);
	}
	
	function setEnnemis($idJoueur, $ennemis){
		$ennemisS = serialize($ennemis);
		$query = "UPDATE joueurs SET ennemis='$ennemisS' WHERE idJoueur='$idJoueur'";
		$result = mysql_query($query) or die("Query error dans update setEnnemis : ". mysql_error());
	}
	
	function getEnnemis($idJoueur){
		$query = "SELECT ennemis FROM joueurs WHERE idJoueur='$idJoueur'";
		$result = mysql_query($query) or die("Query error dans la fonction getEnnemis au premier select : ". mysql_error());
		if(mysql_num_rows($result) != 0){
			//ennemi non unserialise
			$ennemisNU = mysql_result($result,0);
			$ennemis = unserialize($ennemisNU);
			if ($ennemis == ""){
				$ennemis = array();
			}
		}else{
			$ennemis = array();
		}
		return($ennemis);
	}
	
	function provinceConnu($province,$ennemis){
		if (is_array($ennemis)){
			foreach ($ennemis as $infos){
				if($infos['provinces']){
					if(array_key_exists($province,$infos['provinces'])){
						return(true);
					}
				}
			}
		}
		return(false);
	}
	
	//TODO a terminer
	function idEnnemiOnProvince($province,$ennemis){
		if (is_array($ennemis)){
			foreach ($ennemis as $ennemi => $infos){
				foreach ($infos as $info => $detail){
					if($info == 'provinces'){
						if(array_key_exists($province,$detail)){
							return($ennemi);
						}
					}
				}
			}
		}
		return(false);
	}
	
	function provinceEnnemiConnu($idJoueur, $province){
		$ennemis = getEnnemis($idJoueur) ;
		if (is_array($ennemis)){
			foreach ($ennemis as $infos){
				if($infos['provinces']){
					if(array_key_exists($province,$infos['provinces'])){
						return(true);
					}
				}
			}
		}
		return(false);
	}
	
	function batimentConnu($province, $ennemis){
		$res = array();
		if (is_array($ennemis)){
			foreach ($ennemis as $ennemi => $infos){
				foreach ($infos as $nomInfo => $provinces){
					if($nomInfo == "provinces"){
						foreach($provinces as $idProvince => $sigleBatiment){
							if ($idProvince == $province){
								$res['batiment'] = $sigleBatiment[1];
								$res['niveau'] = $sigleBatiment[2];
								return($res);
							}
						}
					}
				}				
			}
		}
		return(false);
	}
	
	function ennemiConnu($idJoueur, $ennemi){
		$ennemis = getEnnemis($idJoueur) ;
		return(array_key_exists($ennemi,$ennemis));
	}
	
	function addEnnemi($joueur, $ennemi){
		$ennemis = getEnnemis($joueur) ;
		$ennemis[$ennemi] = array("provinces" => array(), "ressources" => array(),  "ressourcesP" => array(),  "unites" => array(),  "recherches" => array());
		setEnnemis($joueur, $ennemis);
	}
	
	function addVide($idJoueur){
		$ennemis = getEnnemis($idJoueur) ;
		$ennemis['vide'] = array("provinces" => array());
		setEnnemis($idJoueur, $ennemis);
	}
	
	function addOccupe($idJoueur){
		$ennemis = getEnnemis($idJoueur) ;
		$ennemis['occupe'] = array("provinces" => array());
		setEnnemis($idJoueur, $ennemis);
	}
	function allouerRegionJoueur($idProvince, $idJoueur){
		$query = "UPDATE provinces_joueur SET idJoueur=$idJoueur WHERE idProvince=$idProvince";
		$result = mysql_query($query) or die("Query error dans update allouerRegionJoueur : ". mysql_error());
		setDetailsRoyaumeJoueur($idJoueur);
	}
	
	function addProvinceEnnemi($idJoueur, $idProvince, $ennemi){
		if(!ennemiConnu($idJoueur, $ennemi)){
			addEnnemi($idJoueur, $ennemi);
		}
		$ennemis = getEnnemis($idJoueur) ;
		$provinces = $ennemis[$ennemi]["provinces"] ;
		$ennemis[$ennemi]["provinces"] = $provinces ;
		setEnnemis($idJoueur, $ennemis) ;
	}
	
	function addProvinceVide($idJoueur, $idProvince){
		if(!ennemiConnu($idJoueur, 'vide')){
			addVide($idJoueur);
		}
		$ennemis = getEnnemis($idJoueur) ;
		$provinces = $ennemis['vide']["provinces"] ;
		$provinces[$idProvince] = "desert" ;
		$ennemis['vide']["provinces"] = $provinces ;
		setEnnemis($idJoueur, $ennemis) ;
	}
	
	function addProvinceOccupe($idJoueur, $idProvince){
		if(!ennemiConnu($idJoueur, 'occupe')){
			addOccupe($idJoueur);
		}
		$ennemis = getEnnemis($idJoueur) ;
		$provinces = $ennemis['occupe']["provinces"] ;
		$provinces[$idProvince] = "inconnu" ;
		$ennemis['occupe']["provinces"] = $provinces ;
		setEnnemis($idJoueur, $ennemis) ;
	}
	
	function rmvProvince($idProvince, $idJoueur){
		$ennemis = getEnnemis($idJoueur) ;
		foreach ($ennemis as $ennemi => $infos){
			$provinces = $ennemis[$ennemi]["provinces"] ;
			foreach ($provinces as $province => $batiment){
				if($province == $idProvince){
					unset($provinces[$idProvince]);
					$provinces2["provinces"] = $provinces ;
					$ennemis[$ennemi] = $provinces2 ;
				}
			}
		}
		setEnnemis($idJoueur, $ennemis);
	}
	
	//TODO
	function setCouleurEnnemi($idJoueur, $idEnnemi){
		$ennemis = getEnnemis($idJoueur) ;
		if($idEnnemi != 'vide' && $idEnnemi != 'occupe'){
			$couleurEnnemi = getCouleurJoueur($idEnnemi) ;
		}else{
			$couleurEnnemi = getCouleurNeutre($idEnnemi) ;
		}
		$ennemis[$idEnnemi]["couleur"] = $couleurEnnemi ;
		setEnnemis($idJoueur, $ennemis);
	}
	/*
	 *@function getCouleurEnnemi
	 * Se base sur les connaisance du joueur $joueur
	*/
	function getCouleurEnnemi($joueur, $ennemi){
		if($ennemi == 'vide'){
			 $couleur = '#E6E2AF';
		}else{
			if($ennemi == 'occupe'){
				$couleur = "#B9121B";
			}else{
				$ennemis = getEnnemis($joueur) ;
				if(isset($ennemis[$ennemi]["couleur"])){
					$couleurEnnemi = $ennemis[$ennemi]["couleur"] ;
					$couleur = $couleurEnnemi;
				}else{
					return(null);
				}
			}
		}
		return $couleur;
	}

	function majCouleurEnnemi($idJoueur){
		$ennemis = getEnnemis($idJoueur) ;
		foreach($ennemis as $ennemi => $infos){
			setCouleurEnnemi($idJoueur, $ennemi);
		}
	}
	
	function addBatimentProvince($joueur, $idProvince, $ennemi){
		$ennemis = getEnnemis($joueur) ;
		$provinces = $ennemis[$ennemi]["provinces"] ;
		$provinces[$idProvince] = array(1=>getElementProvince($idProvince),getNiveauBatimentProvince($idProvince));
		$ennemis[$ennemi]["provinces"] = $provinces ;
		setEnnemis($joueur, $ennemis);
	}
	
	function rmvBatimentProvince($idProvince, $joueurEnnemi, $ennemis){
		$infos = $ennemis[$ennemi] ;
		$provinces = $infos["provinces"];
		$provinces[$idProvince] = "";
		$infos2["provinces"] = $provinces ;
		$ennemis[$ennemi] = $infos2 ;
		return($ennemis);
	}
	
	function getProvincesEnnemis($idJoueur){
		$ennemis = $ennemis = getEnnemis($idJoueur) ;
		$tableauProvinces = array();
		$provinces = array();
		if ($ennemis != ""){
			foreach ($ennemis as $ennemi => $infos){
				$provinces = $ennemis[$ennemi]["provinces"] ;
				foreach ($provinces as $numProvince => $infosProvince){
					if (empty($tableauProvinces)){
						$tableauProvinces[1] = $numProvince;
					}else{
						$tableauProvinces[] = $numProvince ;
					}
				}
			}
		}
		return($tableauProvinces);
	}
	
	function getProvincesEnnemi($ennemiADecouvrir){
		$ennemis = getEnnemis() ;
		$tableauProvinces = array();
		foreach ($ennemis as $ennemi => $infos){
			if($ennemiADecouvrir == $ennemi){
				foreach ($infos as $nomInfo => $provinces){
					if($nomInfo == "provinces"){
						foreach($provinces as $idProvince => $sigleBatiment){
							$tableauProvinces[] = $idProvince ;
						}
					}
				}
			}
		}
		return($tableauProvinces);
	}
	
	function addRessourcesEnnemi($ennemi, $ennemis){
		require_once("./fonctionsCommunes.php");
		if ($ennemi != "personne"){
			$ressources = getRessources($ennemi);
			$ennemis[$ennemi]["ressources"] = $ressources ;
		}
		return($ennemis);
	}
	
	function addUnitesEnnemi($ennemi, $ennemis){
		require_once("./fonctionsCommunes.php");
		$unites = getUnites($ennemi);
		if ($ennemi != "personne"){
			$ennemis[$ennemi]["unites"] = $unites  ;
		}
		return($ennemis);
	}
	
	function addRessourcesPEnnemi($ennemi, $ennemis){
		require_once("./fonctionsCommunes.php");
		if ($ennemi != "personne"){
			$ressources = getPRessources($ennemi);
			$ennemis[$ennemi]["ressourcesP"] = $ressources ;
		}
		return($ennemis);
	}
	
	function getAttaque($pseudo){
		require_once("./fonctionsCommunes.php");		
		include_once("./arbre.php");
		global $grilleSpecs  ;
		$unites = getUnites($pseudo);
		$attaque = 0 ;
		foreach($unites as $sigleUnite => $nbUnite){
			$idUnite = getIdElement($sigleUnite);
			$attaque += $grilleSpecs[$idUnite]['attaque']*$nbUnite;
		}
		return($attaque);
	}
	
	function getDegatsAttaque($armee){
		//require_once("./fonctionsCommunes.php");		
		//include_once("./arbre.php");
		global $grilleSpecs  ;
		$attaque = 0 ;
		foreach($armee as $sigleUnite => $nbUnite){
			$idUnite = getIdElement($sigleUnite);
			$attaque += $grilleSpecs[$idUnite]['attaque']*$nbUnite;
		}
		return($attaque);
	
	}
	
	function getDegatsDefense($pseudo){
		//require_once("./fonctionsCommunes.php");
		//include_once("./arbre.php");
		global $grilleSpecs ;
		$unites = getUnites($pseudo);
		$defense = 0 ;
		foreach($unites as $sigleUnite => $nbUnite){
			$idUnite = getIdElement($sigleUnite);
			$defense += $grilleSpecs[$idUnite]['defense']*$nbUnite;
		}
		return($defense);
	}
	
	function getVunerabiliteArmee($armee){
		//require_once("./fonctionsCommunes.php");
		//include_once("./arbre.php");
		global $grilleSpecs ;

		$vunerabilite = 0 ;
		foreach($armee as $sigleUnite => $nbUnite){
			$idUnite = getIdElement($sigleUnite);
			$vunerabilite += $grilleSpecs[$idUnite]['vulnerabilite']*$nbUnite;
		}	
		return($vunerabilite);
	}
?>