<?php

Class Template {

/*
 * @the registry
 * @access private
 */
private $module;

/*
 * @Variables array
 * @access private
 */
private $vars = array();

/**
 *
 * @constructor
 *
 * @access public
 *
 * @return void
 *
 */
function __construct($module) {
        $this->module = $module;
}


 /**
 *
 * @set undefined vars
 *
 * @param string $index
 *
 * @param mixed $value
 *
 * @return void
 *
 */
 public function __set($index, $value)
 {
        $this->vars[$index] = $value;
 }
 
function show($view,$name,$data) {
	$moduleName = $name;
	$path = 'show view : ' .$this->module->getCore()->getModulesPath();
	$path .= $moduleName;
	$path .= '/ressources/';
	$path .= $view;
	$path .= 'View.php';
	echo $path ."<br/>";
	if(file_exists($this->module->getCore()->getModulesPath().$moduleName.'/ressources/'.$view.'View.php'))
		require_once($this->module->getCore()->getModulesPath().$moduleName.'/ressources/'.$view.'View.php');
}

}

?>