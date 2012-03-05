<?php

class HeaderController extends baseController
{
    public function process($data)
    {
		$idProvince = $data[0];
    	echo 'chargement province.class.php : ' . $this->core->getBasePath() .'Entities/province.class.php <br/>';
		require_once $this->core->getBasePath() .'Entities/province.class.php';
		$province = new province($this->core,$idProvince);
		$bat = $province->batimentProvince;
		echo 'resultat retourn&eacute; par le __get(\'batimentProvince\'): '. $bat .'<br/>';
		$bat = $province->__get('idJoueur');
		echo 'resultat retourn&eacute; par le __get(\'idJoueur\'): '. $bat .'<br/>';
		$this->setChildModule('user','');
		$this->startChildModule('user');
		
		$this->core->template->show('header',$data);
    	$this->showChildModule('user');
	    $this->template->show('province',$data);
		$this->core->template->show('footer',$data);
    }
	
	
}