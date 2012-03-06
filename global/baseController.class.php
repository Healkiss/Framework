<?php

Abstract Class baseController {
	/*
	 * @core object
	 */
	protected $core;
	protected $nextCore;
	protected $childModule;
	protected $module;
	protected $moduleName;
	protected $moduleData;
	protected $template;
	
	function __construct($core,$module) {
	        $this->core = $core;
			$this->module = $module;
	}
	function newChildModule($name,$data){
		$path = $this->core->getModulesPath();
		$path .= $name;
		$path .= '/Controller/';
		$path .= $name;
		$path .= 'Controller.php';
		echo 'controller : ' .  $path ."<br/>";
		
		$childModule = $name.'Controller';
		$this->moduleName = $name;
		$this->moduleData = $data;
					
		return new module($this->core, $name,$data);
	}
	public
	/**
	 * @all controllers must contain a process method
	 */
	abstract function process();
	
	public function getCore()
	{
		return $this->core;
	}/*
	public function display($view){
		$this->module->getTemplate()->show($view,$this->module->getModuleName(),$this->module->getModuleData());
	}*/
}
?>