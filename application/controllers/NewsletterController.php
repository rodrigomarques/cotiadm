<?php

class NewsletterController extends Zend_Controller_Action
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
        $newsletterDao = new Application_Model_Newsletters();
        if($this->getRequest()->isPost())
        {
            $nome = $this->getRequest()->getParam("nome", "");
            $email = $this->getRequest()->getParam("email", "");
            $dados = array(
                'nome' => $nome . "%",
                'email' => $email . "%"
            );
            $lista = $newsletterDao->buscar($dados);
            
            $this->view->nome = $nome;
            $this->view->email = $email;
        }
        else
            $lista = $newsletterDao->buscar(array());
        
        $this->view->lista = $lista;
    }
    
    public function excluirAction()
    {
        
        
        $id = $this->getRequest()->getParam("params", 0);
        $newsDao = new Application_Model_Newsletters();
        $news = $newsDao->listarPorId($id);
        if($news != null)
        {
            $newsDao->delete("idnewsletter = " . $id);
            $this->view->resp = "Aluno excluído da newsletter!";
        }
        else
        {
            $this->view->resp = "Aluno não pode ser excluído da newsletter!";
        }
        
        $this->indexAction();
        $this->render("index");
    }

}

