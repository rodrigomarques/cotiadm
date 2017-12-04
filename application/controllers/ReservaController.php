<?php

class ReservaController extends Zend_Controller_Action
{

    
    public function init()
    {
        if(Zend_Auth::getInstance()->hasIdentity() != 1){
            /*Se o usuario não estiver logado, mandar para index*/
            $this->_redirect('/index/');
        }
       
    }

    public function indexAction()
    {
        
    }
}


