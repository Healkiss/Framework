<?php
class Module {
	private $moduleName;
	private $moduleSettings;
	private $moduleData;
	private $controller;
	private $modulePath;
	private $moduleControllerName;
	private $template;
	private $core;
	
	public function __construct($core, $moduleName, $moduleData) 
	{
		$moduleName = ucfirst($moduleName);
		$this->core = $core;
		$this->moduleController = $moduleName . 'Controller';
		$this->setModuleData($moduleData);
		$this->setModuleSettings($path);
		$this->setModuleName($moduleName);
		$this->setController($moduleName);
		$this->template = new template($this);
	}
	
	public function start(){
		$this->controller->process($this->moduleData);
	}
	
	public function display($view)
	{
		$this->template->show($view,$this->getModuleName(),$this->getmoduleData());
	}
	//GETTERS
	public function getModuleName() {
		return $this -> moduleName;
	}

	public function getController() {
		return $this -> controller;
	}

	public function getModulePath() {
		return $this -> modulePath;
	}

	public function getModuleData() {
		return $this -> moduleData;
	}

	public function getModuleSettings() {
		return $this -> moduleSettings;
	}
	
	public function getTemplate() {
		return $this -> template;
	}
	
	public function getCore() {
		return $this -> core;
	}
	//SETTERS
	public function setModuleName($name) {
		$this -> moduleName = $name;
	}

	public function setModulePath($modulePath) {
		$this -> modulePath = $modulePath;
	}

	public function setModuleData($data) {
			$this -> moduleData = $data ;
	}
	public function addModuleData($nameData, $data){
		if($this -> moduleData == ''){
			$this -> moduleData = array($nameData => $data);
		}else{
			$datas = $this -> moduleData;
			$datas[$nameData] = $data ;
			$this -> moduleData = $datas;
		}
	}
	public function setModuleSettings() {
		$this -> moduleSettings = array();
		$settingsFile = $this -> modulePath . 'configuration.xml';

		if (file_exists($settingsFile)) {
			$document = new DomDocument();
			$document -> load($settingsFile);

			foreach ($document->getElementsByTagName('setting') as $setting) {
				switch($setting->getAttribute('type')) {
					case 'integer' :
						$this -> moduleSettings[$setting -> getAttribute('id')] = intval($setting -> nodeValue);
						break;

					case 'boolean' :
						$this -> moduleSettings[$setting -> getAttribute('id')] = $setting -> nodeValue == 'true';
						break;

					default :
						$this -> moduleSettings[$setting -> getAttribute('id')] = $setting -> nodeValue;
						break;
				}
			}
		}
	}
	public function setController($name) {
		// Include controller
		echo 'chargement controller : ' . $this->core->getModulesPath().$name.'/controller/'.$name.'Controller.php <br/>';
		// Include controller
		$moduleName = $name.'Controller';
		
		if(file_exists($this->core->getModulesPath().$name.'/Controller/'.$name.'Controller.php'))
			require_once($this->core->getModulesPath().$name.'/Controller/'.$name.'Controller.php');
		
		$this->controller = new $moduleName($this->core,$this);
	}

}
?>