<?php
	require_once 'global/core.class.php';
	require_once 'global/module.class.php';
	require_once 'global/template.class.php';
	require_once 'global/baseController.class.php';
	$core = new Core();
	$core->loadBDD();
	$core->parseURL();
	$core->startModule();
	ob_start();
		$core->displayModule();
	ob_end_flush();
?>