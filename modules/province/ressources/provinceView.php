<?php
		//skip controller cause no module header was launch
		$this->module->getTemplate()->show('header','header',$data);
		$this->module->getTemplate()->show('banniere','header',$data);
		$this->module->getTemplate()->show('menu','menu',$data);
		$this->module->getTemplate()->show('footer','footer',$data);
		$province = $data[0];
		$connected = $data['user']['connected'];
		if($connected){
			echo 'Bienvenue sur ';
		}else{
			echo 'Vous devez vous logguer pour acceder a ';
		}
			echo 'la province '.$province.'<br/>';
?>