<?php

class DepoimentoController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        if(Zend_Auth::getInstance()->hasIdentity() != 1){
            /*Se o usuario não estiver logado, mandar para index*/
            $this->_redirect('/index/');
        }
    }

    public function indexAction()
    {
        $depoimentoDao = new Application_Model_Depoimentos();
        if($this->getRequest()->isPost())
        {
            $autor = $this->getRequest()->getParam("autor", "");
            $depoimento = $this->getRequest()->getParam("depoimento", "");
            $iddepoimento = $this->getRequest()->getParam("id", 0);
            
            $data = array(
                'autor' => $autor,
                'depoimento' => $depoimento
            );
            if($iddepoimento == 0)
            {
                if($depoimentoDao->insert($data))
                {
                    $this->view->resp = "Depoimento cadastrado com sucesso!";
                }
                else
                {
                    $this->view->resp = "Depoimento não pode ser cadastrado!";
                }
            }
            else
            {
                if($depoimentoDao->update($data, "iddepoimento = " . $iddepoimento))
                    $this->view->resp = "Depoimento editado com sucesso!";
                else
                    $this->view->resp = "Depoimento não pode ser editado!";
            }
            $this->view->autor = $autor;
            $this->view->depoimento = $depoimento;
            
            unset($this->view->iddepoimento);
        }
        
        $pagina = intval($this->getRequest()->getParam("pag", 1));
        $lista = $depoimentoDao->listar();
        
        $paginator = Zend_Paginator::factory($lista);
        $paginator->setItemCountPerPage(10);
        $paginator->setPageRange(5);
        $paginator->setCurrentPageNumber($pagina);
        
        $this->view->pag = $pagina;
        $this->view->dados = $paginator;
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
            $depoimentoDao = new Application_Model_Depoimentos();
            $depoimento = $depoimentoDao->buscarPorId($id);
            if($depoimento != null){
                $this->view->autor = $depoimento["autor"];
                $this->view->depoimento = $depoimento["depoimento"];
                $this->view->iddepoimento = $depoimento["iddepoimento"];
            }else
                $this->view->resp = "Depoimento não encontrado!";
            $this->indexAction();
            $this->render('Index');
        }
    }
    
    public function excluirAction()
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
            $depoimentoDao = new Application_Model_Depoimentos();
            $depoimento = $depoimentoDao->buscarPorId($id);
            if($depoimento != null){
                $depoimentoDao->delete("iddepoimento = " . $id);
                $this->view->resp = "Depoimento excluído com sucesso!";
            }else
                $this->view->resp = "Depoimento não encontrado!";
            $this->indexAction();
            $this->render('Index');
        }
    }


}

