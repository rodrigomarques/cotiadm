<?php

class Plugins_CheckAuth extends Zend_Controller_Plugin_Abstract{

    protected $_acl;

    public function __construct(Zend_Acl $acl){
        $this->_acl = $acl;
    }

    public function  dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        $module = $request->getModuleName();
        $resource = ucfirst(strtolower($request->getControllerName()));
        $action = (strtolower($request->getActionName()));

        if(!Zend_Auth::getInstance()->hasIdentity()){
            $request->setControllerName('usuario')
                    ->setActionName('logar');
            return ;
        }  else {
            if(!$this->_acl->isAllowed(Zend_Registry::get('ROLE'), $resource, $action)){
                throw new Exception("Usuário sem acesso!");
                $request->setControllerName('error')
                        ->setActionName('index');
            }
        }
    }

}
?>
