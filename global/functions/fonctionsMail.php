<?php
	function sendMailJoueur($idJoueur, $sujetMail, $enteteMessage, $sujetMessage, $contenuMessage){
		
		$nameJoueur = getNameJoueur($idJoueur);
		$mail = getMailJoueur($idJoueur);
		$contenuMessage = "
		$enteteMessage </br></br>
		$sujetMessage	</br></br>
		$mailContent";
		
		// Dans le cas où nos lignes comportent plus de 70 caractères, nous les coupons en utilisant wordwrap()
		$message = wordwrap($message, 70);
		// Envoi du mail
							
		// Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= "To: $nameJoueur <$mail>" . "\r\n";
		$headers .= 'From: Admin Bellone <admin@Bellone.fr>' . "\r\n";
		$headers .= "Cc: $mail" . "\r\n";
		$headers .= "Bcc: $mail" . "\r\n";
		if(mail("$mail",  $sujetMail, $message, $headers)){
		}else{
			mail("$mail", $sujetMail, $message);
		}
	}
?>