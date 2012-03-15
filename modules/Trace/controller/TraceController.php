<?php

class HeaderController extends baseController
{
    public function process($data)
    {
        $this->getModule()->setTemplate(new template($this->module));
    }
	
	
}