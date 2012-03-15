<?php
	$datas = $this->getTrace();
	echo "<link rel='stylesheet' type='text/css' href='".$this->module->getCore()->getBaseURL()."scripts/css/dafault/jquery-ui-1.8.18.custom.css'/>";
	echo "<div id='resizeDiv' class='ui-widget-content'>";
		echo "<h3 class='ui-widget-header'>Trace</h3>";
		if(is_array($datas)){
			foreach($datas as $name => $content){
				if(is_array($content)){
					echo "<p>$name : ";
					print_r($content);
					echo "</p><br/>";
				}else{
				echo "$name : $content <br/>";
			}
			}
		}else{
			echo "$name : $content <br/>";
		}
	echo "</div>";
	echo "<script type='text/javascript' src='".$this->module->getCore()->getBaseURL()."scripts/js/jquery-1.7.1.min.js'></script>";
	echo "<script type='text/javascript' src='".$this->module->getCore()->getBaseURL()."scripts/js/jquery-ui-1.8.18.custom.min.js'></script>";
	echo "<script src='".$this->module->getCore()->getBaseURL()."scripts/trace.js'></script>";
?>
