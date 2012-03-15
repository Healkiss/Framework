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
	protected $content;
    protected $layout = "default";
        
	function __construct($core,$module) {
	        $this->core = $core;
			$this->module = $module;
	}
	function newChildModule($name){		
		$this->moduleName = $name;
					
		return new module($this->core, $name,$this->core->getDatas());
	}
	public abstract function process();
	
	public function getCore()
	{
		return $this->core;
	}
	public function getDatasModule($nameModule){
		$datas = $this->core->getDatas();
		foreach($datas as $name => $content){
			if(strcmp($name,$nameModule)){
				return $content;
			}
		}
		return false;
	}
    
    public function getLayout(){
        return $this->layout;
    }
    
    public function setLayout($layout){
        $this->layout = $layout;
    }
        
	public function getContent(){
        return $this->content;
    }
    public function setContent($content){
        $this->content = $content;
    }   
    public function getModule(){
        return $this->module;
    }
	public function addDatasModule($nameData,$data){
		$this->core->addData($this->module->getModuleName(),$nameData,$data);
	}
	
	public function addTrace($nameTrace,$trace){
		$this->core->addData('Trace',$nameTrace,$trace);
	}

}
?>