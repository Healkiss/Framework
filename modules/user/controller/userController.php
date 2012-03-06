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
		$this->module->addModuleData('namePlayer',"Healkiss");
    }
	
	public function display($view)
	{
		$this->template->show($view,$this->getModuleName(),$this->getmoduleData());
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