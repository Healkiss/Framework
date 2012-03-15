<?php

class ErrorController extends baseController
{
	protected $layout = "error";
    public function process()
    {
        $this->getModule()->setTemplate(new template($this->module));
    }
}
?>