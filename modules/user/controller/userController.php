<?php

class UserController extends baseController
{
    public function process()
    {
    	session_start();
    	if(isset($_SESSION['joueur'])){
    		$connected = true;
    	}else{
    		$connected = false;
    	}
		$this->module->addModuleData('connected',$connected);
    }
	public function askLogin($connected){
		$this->module->getTemplate()->show('user',$connected);
	}
	
	public function login($idJoueur){
		echo 'chargement joueur.class.php : ' . $this->core->getBasePath() .'Entities/joueur.class.php <br/>';
		require_once $this->core->getBasePath() .'Entities/joueur.class.php';
		$joueur = new joueur($this->core,$idJoueur);
	}
}