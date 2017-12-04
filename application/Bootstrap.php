<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

     private $_acl;

    protected function _initViewHelpers(){
        
        $this->bootstrap('layout');
        $layout = $this->getResource("layout");
        $view = $layout->getView();
        Zend_Session::start();
        $session = new Zend_Session_Namespace();
        $view->perfil = $session->perfil;
                
        $util = new Util_Utilitarios();
        $view->util = $util;
    
    }

    public function _initAcl(){
        require_once APPLICATION_PATH. "/../plugins/CheckAuth.php";
        
        Zend_Registry::set("ROLE", "VISIT");
        
        if(Zend_Auth::getInstance()->hasIdentity()){
            //$userInfo = Zend_Auth::getInstance()->getStorage()->read();
            $session = new Zend_Session_Namespace();
            if($session->perfil != ""){
                Zend_Registry::set("ROLE", $session->perfil);
            }else{
                Zend_Registry::set("ROLE", "VISIT");
            }
            $this->_acl = new Application_Model_AdminAcl();
            $front = Zend_Controller_Front::getInstance();
            $front->registerPlugin(new Plugins_CheckAuth($this->_acl));
        }
    }
    /*
    protected function _initSessions() { Zend_Session::start(); }
     */


}

