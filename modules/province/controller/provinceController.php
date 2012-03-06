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
		$childs['User'] = $this->newChildModule('User','');
		$moduleUser = $childs['User'];
    	$moduleUser ->start();
		$data['User'] = $moduleUser->getModuleData();
    }
	
}