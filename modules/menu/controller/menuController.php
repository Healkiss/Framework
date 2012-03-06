<?php

class MenuController extends baseController
{
    public function process()
    {		
	    $this->module->display('menu','menu',$data);
    }
	
}