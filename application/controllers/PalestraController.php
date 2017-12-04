<?php

class PalestraController extends Zend_Controller_Action
{

    private $status = array( array("1","Evento não realizado"), 
        array("-1", "Não veio"), array("0" ,"Presente"));
    
    public function init()
    {
        if(Zend_Auth::getInstance()->hasIdentity() != 1){
            /*Se o usuario não estiver logado, mandar para index*/
            $this->_redirect('/index/');
        }
        $this->view->listastatus = $this->status;
    }

    public function indexAction()
    {
        $palestraDao = new Application_Model_Palestras();
        $cursoDao = new Application_Model_Cursos();
        
        if($this->getRequest()->isPost())
        {
            $curso = $this->getRequest()->getParam("curso");
            $status = $this->getRequest()->getParam("status",1);
            $param = array();
            if($curso != 0)
                $param["curso"] = $curso;
            
            $param["status"] = $status;
            
            $data = $palestraDao->buscar($param);
            
            $this->view->data = $data;
            $this->view->status = $status;
            $this->view->curso = $curso;
        }
        $this->view->listaCurso = $cursoDao->consultarCurso(array('status' => 1));
        
    }

    public function excluirAction(){
        
        $id = $this->getRequest()->getParam("params", 0);
        if($id != 0)
        {
            $palestraDao = new Application_Model_Palestras();
            $palestraDao->delete("idpalestra = ".$id);
            $this->view->resp = "Palestrante excluido com sucesso!";
        }
        
        $this->indexAction();
        $this->render("index");
    }
    public function editAction(){
        
        $id = $this->getRequest()->getParam("params", 0);
        if($id != 0)
        {
            $palestraDao = new Application_Model_Palestras();
            
            $opc = $this->getRequest()->getParam("opc", 1);
            
            $palestraDao->update(array('status' => $opc), "idpalestra = ".$id);
            $this->view->resp = "Status alterado com sucesso!";
        }
        
        $this->indexAction();
        $this->render("index");
    }
    
   
}

