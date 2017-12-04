<?php

class AreaController extends Zend_Controller_Action
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
        $areaDao = new Application_Model_Areas();
        if($this->getRequest()->isPost())
        {
            
            $idarea = $this->getRequest()->getParam("id", 0);
            
            $dados = array(
                'area' => $this->getRequest()->getParam("area", "")
            );
            if($idarea == 0)
            {
                if($areaDao->insert($dados))
                    $this->view->resp = "Área cadastrada com sucesso!";
                else
                    $this->view->resp = "Área não pode ser cadastrada!";
            }
            else
            {
                if($areaDao->update($dados, "idarea = " . $idarea))
                    $this->view->resp = "Área editada com sucesso!";
                else
                    $this->view->resp = "Área não pode ser editada!";
            }
            
            $this->view->area = $dados["area"];
            
            unset($this->view->idarea);
        }
        $this->view->dados = $areaDao->listar();
    }

    public function editarAction()
    {
        $id = $this->getRequest()->getParam("params", 0);
        if($id == 0)
        { 
            $this->view->resp = "Não existe o id informado!";
            $this->indexAction();
            $this->render('Index');
        }
        else
        {
            $areaDao = new Application_Model_Areas();
            $area = $areaDao->buscarId($id);
            if($area != null){
                $this->view->area = $area["area"];
                $this->view->idarea = $area["idarea"];
            }else
                $this->view->resp = "Área não encontrada!";
            $this->indexAction();
            $this->render('Index');
        }
    }


}



