<?php

class UserController extends baseController {
	public function process() {
		session_start();
		require_once $this -> core -> getBasePath() . 'entities/user.class.php';
        
        $datas = $this->core->getDatas();
        $this->addDatasModule('view', $datas['Parameter'][0]);
        $Parameter = $this->getDatasModule('Parameter');
        $view = $Parameter['view'];
        print_r( $Parameter);
        
		//check if form from LoginView is submit
		if($view == 'inscription') {
            $this->getModule()->setTemplate(new template($this->module,'inscription'));
        }else{
            $this->getModule()->setTemplate(new template($this->module));
        }
		if ($_POST['uniqid'] == 'Login') {
			$login = $_POST['pseudo'];
			$mdp = $_POST['pass'];
			$message = $this -> login($login, $mdp);
			
			if($message != 'ok'){
				$this->addDatasModule('connected',0);
				$this -> addDatasModule('erreurLogin', $message);
			}else{
				$this->addDatasModule('connected',1);
				$joueur = new joueur($this -> core, $login);
				$this -> addDatasModule('player', $joueur);
			}
		}
		
		if ($_POST['uniqid'] == 'Unlogin') {
			$this->addDatasModule('connected',0);
		}
		
		if ($_POST['uniqid'] == 'Inscription') {
			$message = $this -> inscription($login, $territoire);
		}
	}

	public function login($login, $mdp) {
		require_once $this -> module-> getCore() -> getBasePath() . 'Entities/user.class.php';
		$joueur = new joueur($this -> core, $login);
		
		// Si une erreur survient
		if (!$joueur->pseudo) {
			$message = "Le nom d'utilisateur " . $nom . " n'existe pas";
		} else {
			// Vérification du mot de passe
			if ($mdp != $joueur -> pass) {
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
					$message = 'ok';
				}
			}
		}
	$this->addTrace('pass ',$joueur->pass);
	return $message;
	}

public function inscription($pseudo, $nomTerritoire){
	$pseudoOK = true;
	if (strlen($pseudo) > 15) {
		echo "Votre pseudo est trop long ! (20 caracteres max)";
		$pseudoOK = false;
	}
	if (strlen($pseudo) < 4) {
		echo "Votre pseudo est trop court ! (3 caracteres min)";
		$pseudoOK = false;
	}
	if ($pseudoOK) {

		if (territoireLibre($nomTerritoire)) {
			$query = "SELECT idTerritoire FROM territoire WHERE nomTerritoire = '$nomTerritoire'";
			$result = mysql_query($query) or die("Query error territoire complet : " . mysql_error());
			$row = mysql_fetch_array($result);
			$idTerritoire = $row['idTerritoire'];
			$query = "SELECT * FROM joueurs WHERE idTerritoire = '$idTerritoire' AND pseudo = '$pseudo'";
			$result = mysql_query($query) or die("Query error dans ajouterJoueur SELECT2 : " . mysql_error());
			$resultat = mysql_fetch_row($result);

			if ($resultat == 0) {

				$query = "SELECT * FROM territoire WHERE idTerritoire = '$idTerritoire'";
				$result = mysql_query($query) or die("Query error dans ajouterJoueur SELECT3 :  " . mysql_error());
				$resultat = mysql_fetch_array($result);

				if ($resultat['complet'] == 0) {

					// Le message
					$message = "Bienvenue $pseudo,<br/>
						<p>
						Tu es maintenant inscrit sur Bellone.<br/>
						Ton territoire est pret et attend d'etre transform� en un grand royaume !  <br/>
						Rappel de tes identifiants :<br/>
						Ton pseudo : $pseudo<br/>
						Ton mot de passe : $pass<br/>
						</p>
						Administrateur Full-ripper";
					// Dans le cas o� nos lignes comportent plus de 70 caract�res, nous les coupons en utilisant wordwrap()
					$message = wordwrap($message, 70);
					// Envoi du mail

					// Pour envoyer un mail HTML, l'en-t�te Content-type doit �tre d�fini
					$headers = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= "To: $pseudo <$mail>" . "\r\n";
					$headers .= 'From: Admin Bellone <admin@Bellone.fr>' . "\r\n";
					$headers .= "Cc: $mail" . "\r\n";
					$headers .= "Bcc: $mail" . "\r\n";
					if (mail("$mail", 'Inscription � Bellone', $message, $headers)) {

					} else {
						//		mail("$mail", 'Inscription � Bellone', $message);
						echo "Le mail n'a pas fonctionn&eacute;";
					}
					$date = getdate();
					$date = serialize($date);
					$query = "INSERT INTO joueurs ( pseudo, pass, mail, idTerritoire, statut,dateInscription, bois, argent, pierre, ble, chevaux, derniereMaj, couleur) VALUES ('$pseudo','$pass','$mail','$idTerritoire','joueur','$date','8800','7900','0','0','0','$date', '#00ff00')";
					$result = mysql_query($query) or die("Query error dans  ajouterJoueur INSERT1 : " . mysql_error());
					$idJoueur = mysql_insert_id();

					$constructionPossible = calculerRegion($idTerritoire, $idJoueur);

					$query = "SELECT count(idProvince) from provinces_joueur WHERE provinces_joueur.idJoueur = '$idJoueur'";
					$result = mysql_query($query) or die(mysql_error() . "<br/>");
					$resultat = mysql_fetch_row($result);
					echo " Vous avez a present $resultat[0] provinces a votre disposition, disposez en a bon escient !<br/>";
					echo "<b style='color:green;'>Votre inscription &agrave; bien ete prise en compte, vous pouvez &agrave; present vous connecte avec vos identifiants</b><br/>";

					echo " D'ici quelques instants vous recevrez un mail de bienveue avec vos identifiants, pensez &agrave; verifier vos indesirables !<br/>";
				} else {
					echo "Territoire complet veuillez en choisir un autre !";
					$query = "UPDATE territoire SET  complet = 1 WHERE idTerritoire = '$idTerritoire'";
					$result = mysql_query($query) or die("Query error: " . mysql_error());
				}
			} else {
				echo "vous devez choisir un autre pseudo";
			}
		} else {
			echo "Territoire complet veuillez en choisir un autre !";
		}
	}
}
}
