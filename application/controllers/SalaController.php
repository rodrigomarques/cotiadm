<?php

class SalaController extends Zend_Controller_Action
{

    public function init()
    {
        
    }
    
    public function indexAction()
    {
        $salaDao = new Application_Model_Salas();
        if($this->getRequest()->isPost())
        {
            $sala = $this->getRequest()->getParam("sala", "");
            $maximo = $this->getRequest()->getParam("maximo", "");
            $idsala = $this->getRequest()->getParam("id", 0);
            
            $data = array(
                'sala' => $sala,
                'maximo' => $maximo
            );
            if($idsala == 0)
            {
                if($salaDao->insert($data))
                {
                    $this->view->resp = "Sala cadastrada com sucesso!";
                }
                else
                {
                    $this->view->resp = "Sala não pode ser cadastrado!";
                }
            }
            else
            {
                if($salaDao->update($data, "idsala = " . $idsala))
                    $this->view->resp = "Sala editada com sucesso!";
                else
                    $this->view->resp = "Sala não pode ser editado!";
            }
            $this->view->sala = $sala;
            $this->view->maximo = $maximo;
            
            unset($this->view->iddepoimento);
        }
        
        $lista = $salaDao->listar();
        $this->view->dados = $lista;
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
            $salaDao = new Application_Model_Salas();
            $sala = $salaDao->buscarPorId($id);
            if($sala != null){
                $this->view->sala = $sala["sala"];
                $this->view->maximo = $sala["maximo"];
                $this->view->idsala = $sala["idsala"];
            }else
                $this->view->resp = "Sala não encontrada!";
            $this->indexAction();
            $this->render('Index');
        }
    }
    
}

