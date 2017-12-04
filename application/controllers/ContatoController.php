<?php

class ContatoController extends Zend_Controller_Action
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
        $cursoDao = new Application_Model_Cursos();
        $this->view->realizado = -1;
        $this->view->listaCurso = $cursoDao->consultarCurso();
        if($this->getRequest()->isPost())
        {
            $aluno = $this->getRequest()->getParam("aluno", "");
            $email = $this->getRequest()->getParam("email", "");
            $turno = $this->getRequest()->getParam("turno", "");
            $curso = $this->getRequest()->getParam("curso", "");
            $dias = $this->getRequest()->getParam("dias", "");
            $realizado = $this->getRequest()->getParam("realizado", "");
            $exportar = $this->getRequest()->getParam("exportar", 0);
            
            $data = array(
                'aluno' => $aluno . "%",
                'email' => "%" . $email . "%",
                'curso' => $curso,
                'turno' => $turno,
                'dias' => $dias,
                'status' => $realizado
            );
            
            if($data["status"]==-1)
                unset ($data["status"]);
            
            $contatosDao = new Application_Model_Contatos();
            $dados = $contatosDao->buscarInteresse($data);
            
            $this->view->lista = $dados;
            $this->view->aluno = $aluno;
            $this->view->email = $email;
            $this->view->turno = $turno;
            $this->view->curso = $curso;
            $this->view->dias = $dias;
            $this->view->realizado = $realizado;
            
            if($exportar == 1)
            {
                $util = new Util_Utilitarios();
                $listEmails = '';
                foreach ($dados as $al):
                    $listEmails .= $al["email"] . ",";
                endforeach;
                $arquivo = APPLICATION_PATH . "/data/contatosite.txt";
                $util->exportarTXT($arquivo, $listEmails);
                header("Content-Type: application/text"); // informa o tipo do arquivo ao navegador
            header("Content-Length: ".filesize($arquivo)); // informa o tamanho do arquivo ao navegador
            header("Content-Disposition: attachment; filename=".basename($arquivo)); // informa ao navegador que é tipo anexo e faz abrir a janela de download, tambem informa o nome do arquivo
            readfile($arquivo); // lê o arquivo
            exit;
            }
        }
        
    }
    
    public function excluirAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $id = $this->getRequest()->getParam("id", 0);
        $interesseDao = new Application_Model_Interesses();
        $interesse = $interesseDao->listarPorId($id);
        if($interesse != null)
        {
            $idcontatosite = $interesse["contatosite_idcontatosite"];
            $interesseDao->delete("idinteresse = " . $id);
            $total = $interesseDao->countInteressesAluno($idcontatosite);
            if($total == 0){
                $contatoSite = new Application_Model_Contatos();
                $contatoSite->delete("idcontatosite = " . $idcontatosite);
            }
        
            echo "Interesse do site excluído com sucesso!";
        }
        else
        {
            echo "Nenhum contato existente!";
        }
    }

    public function atualizarcontatoAction()
    {
        $id = $this->getRequest()->getParam("param1", "");
        if($id != "")
        {
            $contatoInteresse = new Application_Model_Interesses();
            $contato = $contatoInteresse->listarPorId($id);
            
            if($contato != null)
            {
                $data = array();
                if($contato["status"] == 0)
                {
                    $data["status"] = 1;
                }
                else
                {
                    $data["status"] = 0;
                }
                $contatoInteresse->update($data, "idinteresse = " . $id);
                $this->indexAction();
                $contatosDao = new Application_Model_Contatos();
                $dados = $contatosDao->buscarInteresse(array());
                $this->view->lista = $dados;
                $this->view->resp = "Contato modificado com sucesso!";
                $this->render("index");
            }
            else
            {
                $this->indexAction();
                $this->render("index");
            }
        }
    }
    
    public function excluirallAction()
    {
        $ids = $this->getRequest()->getParam("ckidinteresse", array());
        $interesse = new Application_Model_Interesses();
        $contatoSiteDao = new Application_Model_Contatos();
        if(count($ids) > 0)
        {
            foreach($ids as $id)
            {
                $inter = $interesse->listarPorId($id);
                $tam = $interesse->countInteressesAluno($inter["contatosite_idcontatosite"]);
                $interesse->delete('idinteresse = ' . $id);
                if($tam == 1)
                {
                    $contatoSiteDao->delete('idcontatosite = ' . $inter["contatosite_idcontatosite"]);
                }
            }
            $this->view->resp = "Interesse excluido com sucesso!";
        }
        $this->indexAction();
        $this->render("index");
    }

}

