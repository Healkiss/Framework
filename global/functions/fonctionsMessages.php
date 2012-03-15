<?php
	if(isset($_GET['action'])){
		$action = $_GET['action'];
		if($action == "supprimer"){
			$idMessage = $_GET['idMessage'];
			supprimerMessage($idMessage) ;
			if(isset($_GET['messsagePrecedent'])){
				$idMessagePrecedent = $_GET['messsagePrecedent'] ;
				if(isset($_GET['envoi'])){
					header('Location: ../messagerie.php?message='.$idMessagePrecedent.'&boite_envoi=1');
				}else{
					header('Location: ../messagerie.php?message='.$idMessagePrecedent.'');
				}
			}else{
				$page = $_GET['page'] ;
				if(isset($_GET['envoi'])){
					header('Location: ../messagerie.php?page='.$page.'&boite_envoi=1');
				}else{
					header('Location: ../messagerie.php?page='.$page.'');
				}
			}
		}
	}
	
	function supprimerMessage($idMessage){
		include_once("./connBdd.php");
		$query = "DELETE FROM messages WHERE idMessage=$idMessage";
		$result = mysql_query($query) or die("Query error dans supprimerMessage: ". mysql_error());
	}
	
	function getNbUnreadInbox($idJoueur){
		 $query = "SELECT idMessage FROM messages WHERE ouvert = 0 AND recepteur=$idJoueur AND boite='reception'";
		 $result = mysql_query($query) or die("Query error dans getNbInbox: ". mysql_error());
		 $count = mysql_num_rows($result);
		 return($count);
	}
	
	function getNbInbox($idJoueur,$boite){
		 $query = "SELECT idMessage FROM messages WHERE recepteur=$idJoueur AND boite='$boite'";
		 $result = mysql_query($query) or die("Query error dans getNbInbox: ". mysql_error());
		 $count = mysql_num_rows($result);
		 return($count);
	}
	
	function sendMessage($idEmeteur, $idRecepteur, $sujet, $corps){
		$date = getDate() ;
		$dateF = getDateFormatee($date);
		$dateS = serialize($dateF);
		$query = "INSERT INTO messages VALUES ('', $idEmeteur, $idRecepteur, '$sujet', '$corps', 0 ,'$dateS','envoi')";
		$result = mysql_query($query)or die("Query error dans insert 1 sendMessage $query : ". mysql_error());
		$query2 = "INSERT INTO messages VALUES ('', $idEmeteur, $idRecepteur, '$sujet', '$corps', 0 ,'$dateS','reception')";
		$result = mysql_query($query2)or die("Query error dans insert 2 sendMessage $query : ". mysql_error());
	}
	
	function getNbUnreadRapports($idJoueur){
		 $query = "SELECT idRapport FROM rapports WHERE rapports.ouvert = 0 AND idDestinataire=$idJoueur";
		 $result = mysql_query($query) or die("Query error dans getNbUnreadRapports: ". mysql_error());
		 $count = mysql_num_rows($result);
		 return($count);
	}
	
	function getNbRapports($idJoueur){
		 $query = "SELECT idRapport FROM rapports WHERE idDestinataire=$idJoueur";
		 $result = mysql_query($query) or die("Query error dans getNbRapports: ". mysql_error());
		 $count = mysql_num_rows($result);
		 return($count);
	}
	
	function sendRapport($idRecepteur, $type, $corps){
		$idRecepteur = mysql_real_escape_string($idRecepteur);
		$type = mysql_real_escape_string($type);
		$corps = mysql_real_escape_string($corps);
		$date = getDate() ;
		$dateF = getDateFormatee($date);
		$dateS = serialize($dateF);
		$query = "INSERT INTO rapports VALUES ('','$idRecepteur', '$type', '$corps', 0 ,'$dateS')";
		$result = mysql_query($query)or die("Query error dans sendRapport : ". mysql_error());
	}
	
	
	function changerMail($idJoueur, $nouveauMail){
		$query = "UPDATE joueurs SET mail = '$nouveauMail' WHERE idJoueur='$idJoueur'";
		$result = mysql_query($query) or die("erreur dans UPDATE1 de changerMail : " . mysql_error()) ;
	}
?>