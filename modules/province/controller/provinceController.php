<?php

class ProvinceController extends baseController
{
    public function process()
    {
    	$childs = array();
		$data = $this->module->getModuledata();
		$idProvince = $data[0];
    	echo 'chargement province.class.php : ' . $this->core->getBasePath() .'entities/province.class.php <br/>';
		require_once $this->core->getBasePath() .'entities/province.class.php';
		$province = new province($this->core,$idProvince);
		
		//start and execute user module
		$childs['user'] = $this->newChildModule('user','');
		$moduleUser = $childs['user'];
    	$moduleUser ->start();
		$data['user'] = $moduleUser->getModuleData();
		
		$this->module->getTemplate()->show('header',$data);
		$moduleUser ->display();
	    $this->display('province',$data);
		$this->module->getTemplate()->show('footer',$data);
    }
	
}