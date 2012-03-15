<?php
	require_once 'global/core.class.php';
	require_once 'global/module.class.php';
	require_once 'global/template.class.php';
	require_once 'global/baseController.class.php';
	$core = new Core();
	$core->addTrace('current url',$_SERVER['REQUEST_URI']);
	$core->loadBDD();
	$core->parseURL();
	$core->startModule();
	$core->displayModule();
?>