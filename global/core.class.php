<?php
	class Core
	{	
		public function __construct()
		{
			$this->readSettings('global/configuration.xml');
			
		    header('Content-type:text/html; charset='.$this->pageEncoding);
		    setlocale(LC_ALL, $this->pageLanguage);
			require_once($this->getBasePath().'global/functions/fonctionsAffichage.php');
			require_once($this->getBasePath().'global/functions/fonctionsAttaques.php');
			require_once($this->getBasePath().'global/functions/fonctionsCommunes.php');
			require_once($this->getBasePath().'global/functions/fonctionsDates.php');
			require_once($this->getBasePath().'global/functions/fonctionsMail.php');
			require_once($this->getBasePath().'global/functions/fonctionsMessages.php');
			require_once($this->getBasePath().'global/functions/fonctionsProvince.php');
			require_once($this->getBasePath().'global/functions/fonctionsRapports.php');
			require_once($this->getBasePath().'global/form.php');
		}
		
		public function loadBDD(){
			try{
				error_reporting(E_ALL ^ E_WARNING); // Don't show php errors concerning DB Connection
		    	$this->database = new PDO($this->databaseDriver.':host='.$this->databaseHost.';dbname='.$this->databaseBase, $this->databaseUsername, $this->databasePassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
				error_reporting(E_ALL ^ E_NOTICE); // Turn error reporting back to default
			}catch(PDOexeption $e){
				echo 'La base de donn&eacute; n est pas disponible<br>';
				echo 'Erreur:'.$e.getMessage().'<br>';
				echo 'Numero:'.$e.getCode();
			}
		}
		
		public function readSettings($configurationFileName)
		{
			$document = new DOMDocument();
			$document->load($configurationFileName);
			$settingsList = $document->getElementsByTagName('setting');
			foreach($settingsList as $setting)
			{
				$value = $setting->nodeValue;
				switch($setting->getAttribute('id'))
				{
				case 'basePath' : $this->basePath = $value; break;
				case 'libsPath' : $this->libsPath = $value; break;
				case 'themesPath' : $this->themesPath = $value; break;
				case 'imagesPath' : $this->imagesPath = $value;	break;
				case 'modulesPath' : $this->modulesPath = $value; break;
				case 'scriptsPath' : $this->scriptsPath = $value; break;		
				case 'baseURL' : $this->baseURL = $value; break;
				case 'libsURL' : $this->libsURL = $value; break;
				case 'themesURL' : $this->themesURL = $value; break;
				case 'imagesURL' : $this->imagesURL = $value; break;
				case 'modulesURL' : $this->modulesURL = $value; break;
				case 'scriptsURL' : $this->scriptsURL = $value; break;
				case 'urlDataSeparator' : $this->urlDataSeparator = $value; break;
				case 'urlParameterName' : $this->urlParameterName = $value; break;
				case 'urlParameterSeparator' : $this->urlParameterSeparator = $value; break;
				case 'databaseDriver' : $this->databaseDriver = $value; break;
				case 'databaseHost' : $this->databaseHost = $value; break;
				case 'databaseBase' : $this->databaseBase = $value; break;
				case 'databaseUsername' : $this->databaseUsername = $value; break;
				case 'databasePassword' : $this->databasePassword = $value; break;
				case 'pageTitle' : $this->pageTitle = $value; $this->pageTitleBase = $value; break;
				case 'pageDescription' : $this->pageDescription = $value; break;
				case 'pageKeywords' : $this->pageKeywords = explode(', ', $value); break;
				case 'pageLanguage' : $this->pageLanguage = $value; break;
				case 'pageCanonicalLink' : $this->pageCanonicalLink = $value; break;
				case 'pageReadingDir' : $this->pageReadingDir = $value; break;
				case 'pageEncoding' : $this->pageEncoding = $value; break;
				case 'defaultModule' : $this->defaultModule = $value; break;
				default : break;
				}
			}
		}

		public function readModuleSettings()
		{
			$this->moduleSettings = array();
			$settingsFile = $this->modulePath.'configuration.xml';
			
			if(file_exists($settingsFile))
			{
				$document = new DomDocument();
				$document->load($settingsFile);
				
				foreach($document->getElementsByTagName('setting') as $setting)
				{
					switch($setting->getAttribute('type'))
					{
					case 'integer':
						$this->moduleSettings[$setting->getAttribute('id')] = intval($setting->nodeValue);
						break;
						
					case 'boolean':
						$this->moduleSettings[$setting->getAttribute('id')] = $setting->nodeValue == 'true';
						break;
						
					default :
						$this->moduleSettings[$setting->getAttribute('id')] = $setting->nodeValue;
						break;
					}
				}
			}
		}
		
		public function parseURL()
		{
			$this->modulePath = $this->modulesPath;
			
			if (empty($_GET[$this->urlParameterName]))
				$_GET[$this->urlParameterName] = $this->defaultModule;
			
			// Parse the url parameter
			$modules = explode($this->urlParameterSeparator, $_GET[$this->urlParameterName]);
			
			// loop through modules specified in the url parameter
			$moduleExists = true;
			for ($i = 0; $i < count($modules) && $moduleExists; $i++)
			{
				// Define the exact module name
				$this->moduleName = $modules[$i];
				
				if($i == count($modules)-1)
				{
					// This is the last module, extract module's name and data
					$moduleAndData = explode($this->urlDataSeparator, $modules[$i]);
					$this->moduleName = $moduleAndData[0];
					$datas['Parameter'][0] = $moduleAndData[1] ;
					$this->datas = $datas;
				}
				if (is_dir($this->modulePath . $this->moduleName))
				{
					// If module exists, add the module to module's path
					$this->modulePath .= $this->moduleName . '/';
					$modules[$i] = '';
					
					if($i == 0) // Top level module, read configuration
						$this->readModuleSettings($this->modulePath);
				}
				else
				{
					$path = $this->modulePath;
					$path .= $this->moduleName;
					$this->addTrace('core class says','module : ' .  $path ." non trouv&eacute, redirection vers erreur");
					// If module doesn't exist, redirect to the 404 error module
					$this->moduleName = 'Error';
					$this->datas = array(0=>'404');
				}
			}
		}

		public function startModule()
		{
			$module = $this->getModuleName().'Controller';
			
			$this->module = new module($this, $this->moduleName,$this->datas);
			$this->module->start();
			
		}
		            
		public function displayModule()
		{
			$this->getModule()->display();
		}
		public function getDatabase()
		{
			return $this->database;
		}
		public function getModule()
		{
			return $this->module;
		}
		public function getModuleName()
		{
			return $this->moduleName;
		}
		public function getController()
		{
			return $this->controller;
		}
		public function getModulePath()
		{
			return $this->modulePath;
		}
		public function getModuleData()
		{
			return $this->moduleData;
		}
		public function getModuleSettings()
		{
			return $this->moduleSettings;
		}
		public function getBasePath()
		{
			return $this->basePath;
		}
		public function getLibsPath()
		{
			return $this->libsPath;
		}
		public function getThemesPath()
		{
			return $this->themesPath;
		}
		public function getImagesPath()
		{
			return $this->imagesPath;
		}
		public function getModulesPath()
		{
			return $this->modulesPath;
		}
		public function getScriptsPath()
		{
			return $this->scriptsPath;
		}
		public function getBaseURL()
		{
			return $this->baseURL;
		}
		public function getLibsURL()
		{
			return $this->libsURL;
		}
		public function getThemesURL()
		{
			return $this->themesURL;
		}
		public function getImagesURL()
		{
			return $this->imagesURL;
		}
		public function getModulesURL()
		{
			return $this->modulesURL;
		}
		public function getScriptsURL()
		{
			return $this->scriptsURL;
		}
		public function getUrlDataSeparator()
		{
			return $this->urlDataSeparator;
		}
		public function getUrlParameterName()
		{
			return $this->urlParameterName;
		}
		public function getUrlParameterSeparator()
		{
			return $this->urlParameterSeparator;
		}
		 function getDatabaseDriver()
		{
			return $this->databaseDriver;
		}
		public function getDatabaseHost()
		{
			return $this->databaseHost;
		}
		public function getDatabaseBase()
		{
			return $this->databaseBase;
		}
		public function getDatabaseUsername()
		{
			return $this->databaseUsername;
		}
		public function getDatabasePassword()
		{
			return $this->databasePassword;
		}
		public function getPageTitle()
		{
			return $this->pageTitle;
		}
		public function getPageTitleBase()
		{
			return $this->pageTitleBase;
		}
		public function getPagePath()
		{
			return $this->pagePath;
		}
		public function getPageDescription()
		{
			return $this->pageDescription;
		}
		public function getPageKeywords()
		{
			return $this->pageKeywords;
		}
		public function getPageLanguage()
		{
			return $this->pageLanguage;
		}
		public function getPageCanonicalLink()
		{
			return $this->pageCanonicalLink;
		}
		public function getPageReadingDir()
		{
			return $this->pageReadingDir;
		}
		public function getPageEncoding()
		{
			return $this->pageEncoding;
		}
		public function getDefaultModule()
		{
			return $this->defaultModule;
		}
		public function getDatas()
		{
			return $this->datas;
		}
		
	    public function setPageTitle($pageTitle)
		{
			$this->pageTitle = $pageTitle;
		}
		public function setPageCanonicalLink($pageCanonicalLink)
		{
			$this->pageCanonicalLink = $pageCanonicalLink;
		}
	    public function addPagePathStep($pageName, $pageLink)
		{
			$this->pagePath[] = array($pageName, $pageLink);
		}
		
		public function setPageKeywords($pageKeywords)
		{
			$this->pageKeywords = $pageKeywords;
		}
		
		public function setPageDescription($pageDescription)
		{
			$this->pageDescription = $pageDescription;
		}
		public function setModuleName($name)
		{
			$this->moduleName = $name;
		}
		public function setModuleData($data)
		{
			$this->moduleData = $data;
		}
		public function setDatas($data)
		{
			$this->datas = $data;
		}
		public function addData($nameModule, $nameData, $data){
			if($this->datas == ''){
				$this->datas = array($nameData => $data);
			}else{
				$datas = $this->datas;
				$datas[$nameModule][$nameData] = $data ;
				$this->datas = $datas;
			}
		}
		
		public function addTrace($nameTrace,$trace){
			$this->addData('Trace',$nameTrace,$trace);
		}
		// Database
		private $database;
		
		// Module
		private $module;
		private $moduleName;
		private $controller;
		private $modulePath;
		private $moduleData;
		private $moduleSettings;
		
		//datas
		private $datas;

		// Settings
		private $basePath;
		private $libsPath;
		private $themesPath;
		private $imagesPath;
		private $modulesPath;
		private $scriptsPath;
		private $baseURL;
		private $libsURL;
		private $themesURL;
		private $imagesURL;
		private $modulesURL;
		private $scriptsURL;
		private $urlDataSeparator;
		private $urlParameterName;
		private $urlParameterSeparator;
		private $databaseDriver;
		private $databaseHost;
		private $databaseBase;
		private $databaseUsername;
		private $databasePassword;
		private $pageTitle;
		private $pageTitleBase;
		private $pagePath = array();
		private $pageDescription;
		private $pageKeywords;
		private $pageLanguage;
		private $pageCanonicalLink;
		private $pageReadingDir;
		private $pageEncoding;
		private $defaultModule;
	}
?>