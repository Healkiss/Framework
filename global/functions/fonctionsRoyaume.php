<?php
function processRoyaume(){
	$idJoueur = $_SESSION['idJoueur'];
	//calculs des coordones max et min du terrain du joueur ET on set le terrain du joueur
	$query = "
		SELECT province.idProvince,y,x FROM province 
		INNER JOIN provinces_joueur 
		ON provinces_joueur.idProvince=province.idProvince 
		INNER JOIN joueurs 
		ON joueurs.idJoueur = provinces_joueur.idJoueur 
		WHERE provinces_joueur.idJoueur = '$idJoueur'
	";
	$result = mysql_query($query)or die("Query error :". mysql_error());
	$case = mysql_fetch_assoc($result) ;
	$y_min = $case['y'];
	$y_max = $case['y'];
	$x_max = $case['x'];
	$x_min = $case['x'];
	while ($case = mysql_fetch_assoc($result)){
		if ($case['x'] < $x_min){
			$x_min = $case['x'];
		}
		if ($case['x'] > $x_max){
			$x_max = $case['x'];
		}
		if ($case['y'] < $y_min){
			$y_min = $case['y'];
		}
		if ($case['y'] > $y_max){
			$y_max = $case['y'];
		}
	}
	$query = "UPDATE joueurs SET y_min=$y_min, y_max=$y_max, x_min=$x_min, x_max=$x_max, connecte=1 WHERE idJoueur = '$idJoueur'";
	$result = mysql_query($query) or die("query error dans l'initialisation des variables : " .mysql_error());
	$tailleX = $x_max-$x_min ;
	$tailleY = $y_max-$y_min ;
	setDetailsRoyaumeJoueur($idJoueur);
}
?>