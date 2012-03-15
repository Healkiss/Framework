<?php
	if(isset($_GET['action'])){
		$action = $_GET['action'];
		if($action == "supprimer"){
			$idRapport = $_GET['idRapport'];
			supprimerMessage($idRapport) ;
			if(isset($_GET['idRapportPrecedent'])){
				$idRapportPrecedent = $_GET['idRapportPrecedent'] ;
				header('Location: ../rapports.php?rapport='.$idRapportPrecedent.'');
			}else{
				$page = $_GET['page'] ;
				header('Location: ../rapports.php?page='.$page.'');
			}
		}
	}
	
	function supprimerRapport($idRapport){
		include_once("./connBdd.php");
		$query = "DELETE FROM rapports WHERE idRapport=$idRapport";
		$result = mysql_query($query) or die("Query error dans supprimerMessage: ". mysql_error());
	}