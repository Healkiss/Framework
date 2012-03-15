<?php
    $datas = $this -> getDatasModule('Province');
    $Province = $datas['objectProvince'];
    $idProvince = $Province -> idProvince;
    $idJoueur = $Province -> idJoueur;
    $batimentProvince = $Province -> batimentProvince;
    $connected = $datas['User']['connected'];
    if ($connected) {
        echo 'Bienvenue sur ';
    } else {
        echo 'Vous devez vous logguer pour acceder a ';
    }
    echo "la province $idProvince /<br/>";
    echo "La province appartient a : $idJoueur <br/>";
    echo "La province contient : $batimentProvince <br/>";
?>