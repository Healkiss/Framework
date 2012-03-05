<?php
	$province = $data[0];
	$connected = $data['user']['connected'];
if($connected){
	echo 'Bienvenue sur ';
}else{
	echo 'Vous devez vous logguer pour acceder a ';
}
	echo 'la province '.$province.'<br/>';
?>