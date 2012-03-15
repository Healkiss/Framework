<?php

Class Template {

	/*
	 * @the registry
	 * @access private
	 */
	private $module;
	private $vars = array();
    private $view;

	/**
	 *
	 * @constructor
	 *
	 * @access public
	 *
	 * @return void
	 *
	 */
	function __construct($module, $view = 'default') {
		$this -> module = $module;
        $this->view = $view;
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
	public function __set($index, $value) {
		$this -> vars[$index] = $value;
	}

	function showModule($name, $data) {
	    if ($this->view == 'default'){
            if (file_exists($this -> module -> getCore() -> getModulesPath() . $name . '/ressources/' . $name . 'View.php'));
                require_once ($this -> module -> getCore() -> getModulesPath() . $name . '/ressources/' . $name . 'View.php');
        }else{
    		if (file_exists($this -> module -> getCore() -> getModulesPath() . $name . '/ressources/' . $this->view  . 'View.php'));
                require_once ($this -> module -> getCore() -> getModulesPath() . $name . '/ressources/' . $this->view  . 'View.php');
        }
	}
    function showView($view, $name) {
        if (file_exists($this -> module -> getCore() -> getModulesPath()  . $name . '/ressources/' . $view  . 'View.php'));
            require_once ($this -> module -> getCore() -> getModulesPath()  . $name . '/ressources/' . $view  . 'View.php');
    }
        
    function showLayout($layout){
        if (file_exists($this -> module -> getCore() -> getBasePath(). '/global/layouts/' . $layout . '.php'))
            require_once ($this -> module -> getCore() -> getBasePath(). '/global/layouts/' . $layout . '.php');
    }
    
	function getDatasModule($nameModule) {
		$datas = $this -> module -> getCore() -> getDatas();
		foreach ($datas as $name => $content) {
			if ($name == $nameModule) {
				return $content;
			}
		}
		return false;
	}
	function getTrace() {
		$datas = $this -> module -> getCore() -> getDatas();
		/*foreach ($datas as $name => $content) {
			if ($name == 'Trace') {*/
				return $datas;
			/*}
		}
		return false;*/
	}
}
?>