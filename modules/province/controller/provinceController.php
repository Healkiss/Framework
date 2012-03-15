<?php

class ProvinceController extends baseController
{
    
    
    public function process()
    {
        
    	$childs = array();
		$datas = $this->core->getDatas();
		$this->addDatasModule('idProvince', $datas['Parameter'][0]);
		$Parameter = $this->getDatasModule('Parameter');
		$Province = $this->getDatasModule('Province');
		$idProvince = $Parameter['idProvince'];
		require_once $this->core->getBasePath() .'entities/province.class.php';
		$province = new province($this->core,$idProvince);
		$this->addDatasModule('objectProvince',$province);
		
		//start and execute user module
		$childs['User'] = $this->newChildModule('User');
		$moduleUser = $childs['User'];
    	$moduleUser ->start();
    }
	
}