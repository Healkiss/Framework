<?php

class ErrorController extends baseController
{
    public function process($data)
    {		
	    $this->core->template->show('erreur',$data);
    }
}