<?php

$form_inscription = new Form('Login');

$form_inscription->method('POST');

$form_inscription->add('Text', 'pseudo','pseudo')
                 ->label("Votre nom d'utilisateur");

$form_inscription->add('Password', 'pass')
                 ->label("Votre mot de passe");

$form_inscription->add('Submit', 'submit')
                 ->value("Connexion");

$form_inscription->bound($_POST);

echo $form_inscription;

echo "<a href='".$this->baseURL."/Inscription'>Pas encore inscrit ?</a>";
?>