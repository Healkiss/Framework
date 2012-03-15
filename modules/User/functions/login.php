<?php
extract($_POST);
$nameJoueur = mysql_real_escape_string($_POST['pseudo']);
$pass = mysql_real_escape_string($_POST['pass']);
// SÃ©lection de l'utilisateur concerné
echo 'chargement joueur.class.php : ' . $this -> core -> getBasePath() . 'Entities/joueur.class.php <br/>';
require_once $this -> core -> getBasePath() . 'Entities/joueur.class.php';
$joueur = new joueur($this -> core, $nameJoueur);

$query = "SELECT banni,pseudo, pass FROM joueurs WHERE pseudo = '$nom' ";
$result = mysql_query($query) or die(mysql_error());
// Si une erreur survient
if (!$joueur) {
	$message = "Le nom d'utilisateur " . $nom . " n'existe pas";
} else {
	// Vérification du mot de passe
	if ($pass != $joueur -> pass) {
		$message = "Votre mot de passe est incorrect";
	} else {
		if ($joueur -> banni > 0) {
			$temps = $joueur -> banni;
			$message = "Vous avez &eacute;t&eacute; banni, un mail vous a &eacute;t&eacute; envoy&eacute; avec le motif, il vous reste $temps heures Ã  attendre, pour toutes reclamations vous pouvez contacter un administrateur.";
		} else {
			$message = "ok";
			$_SESSION['pseudo'] = $joueur -> pseudo;
			$_SESSION['idJoueur'] = $joueur -> idJoueur;
			$idJoueur = $joueur -> idJoueur;
			//heure de connexion
			$date = getdate();
			$date = serialize($date);
			//ip du client
			$ip = $_SERVER['REMOTE_ADDR'];
			//url de provenance
			if (IsSet($_SERVER['HTTP_REFERER'])) {
				$url_provenance = $_SERVER['HTTP_REFERER'];
			} else {
				$url_provenance = "direct";
			}
			//config
			$navigateur = $_SERVER["HTTP_USER_AGENT"];
			$query = "INSERT INTO journal (idSession,joueur,dateConnexion,ipProvenance,urlProvenance,navigateur) VALUES ('','$nom','$date','$ip','$url_provenance','$navigateur')";
			$result = mysql_query($query) or die("Query error: " . mysql_error());
			$query = "SELECT idSession FROM journal WHERE idSession=LAST_INSERT_ID()";
			$result = mysql_query($query) or die("Query error: " . mysql_error());
			$row = mysql_fetch_assoc($result);
			$idSession = $row['idSession'];
			$_SESSION['idSession'] = $idSession;
			processRoyaume();
		}
	}
}
header('Location: ../../index.php');
?>