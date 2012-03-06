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
 
function show($name,$data) {
	$moduleName = $name;
	$path = 'show : ' .$this->module->getCore()->getModulesPath();
	$path .= $moduleName;
	$path .= '/ressources/';
	$path .= $moduleName;
	$path .= 'View.php';
	echo 'view : ' . $path ."<br/>";
	if(file_exists($this->module->getCore()->getModulesPath().$moduleName.'/ressources/'.$moduleName.'View.php'))
		require_once($this->module->getCore()->getModulesPath().$moduleName.'/ressources/'.$moduleName.'View.php');

}

}

?>