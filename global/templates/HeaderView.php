<!doctype html>
<html>
	<head>
		<title><?php echo $this->module->getCore()->getPageTitle(); ?></title>
		
		<?php echo $this->module->getCore()->getPageCanonicalLink() != '' ? '<link rel="canonical" href="'.$this->module->getCore()->getPageCanonicalLink().'" />' : '' ; ?>
		
		<link rel="stylesheet" type="text/css" href="<?php echo $this->module->getCore()->getThemesURL(); ?>default/style.php" />
		
		<?php echo $this->module->getCore()->getPageEncoding() != '' || true ? '<meta charset="'.$this->module->getCore()->getPageEncoding().'">' : '' ; ?>
		<?php echo $this->module->getCore()->getPageKeywords() != '' || true ? '<meta name="keywords" content="'.implode(', ', array_unique($this->module->getCore()->getPageKeywords())).'" />' : '' ; ?>
		<?php echo $this->module->getCore()->getPageDescription() != '' || true ? '<meta name="description" content="'.$this->module->getCore()->getPageDescription().'" />' : '' ; ?>
	</head>
	<body>