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
	public function startChildModule()
	{
		$this->childModule->start();
	}
	public function displayChildModule()
	{
		$this->childModule->getTemplate()->show($this->moduleName,$this->childModule->getCore()->getmoduleData());
	}
	/**
	 * @all controllers must contain a process method
	 */
	abstract function process();
	
	public function getCore()
	{
		return $this->core;
	}
	public function display($moduleName,$data){
		$this->module->getTemplate()->show($moduleName,$data);
	}
}
?>