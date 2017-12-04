<?php

class ChamadaController extends Zend_Controller_Action
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
        $turmaDao = new Application_Model_Turmas();
        $alunoTurmaDao = new Application_Model_AlunosTurmas();
        
        $idprofessor = "";
        $session = new Zend_Session_Namespace();
        $session ->setExpirationSeconds( 86400 );
        $idprofessor = $session->idusuario;
        if($this->getRequest()->isPost())
        {
            $idturma = $this->getRequest()->getParam("turma", 0);
            $turma = $turmaDao->buscarPorId($idturma);
            
            if($turma != null)
            {
                $alunoTurma = $alunoTurmaDao->buscarAlunosPorTurma($turma->toArray());
                $chamadaDao = new Application_Model_Chamadas();
                $listaPresenca = $chamadaDao->listarPresenca(array('idturma' => $idturma));
                $listaDatas = $chamadaDao->listarPresencaData(array('idturma' => $idturma));
                
                $this->view->turma = $turma;
                $this->view->turmas = $alunoTurma;
                $this->view->id = $idturma;
                $this->view->listaPresenca = $listaPresenca;
                $this->view->listaDatas = $listaDatas;
            }
        }
        if($session->perfil == "professor")
        $this->view->listaTurma = $turmaDao->consultarTurma(array( 
            'professor' => $idprofessor,'fim' => 'yes', 'orderby' => 'status DESC' , 'orderby2' => 'curso ASC'));
    }
    
    public function gerarchamadaAction()
    {
        if($this->getRequest()->isPost())
        {
            $alunoTurma = new Application_Model_AlunosTurmas();
            $turmaDao = new Application_Model_Turmas();
            $idturma = $this->getRequest()->getParam("turma",0);
            /*
             * id dos alunos presentes
             */
            $presentes = $this->getRequest()->getParam("presentes");
            $alunos = $alunoTurma->buscarAlunosPorTurma(array("idturma" => $idturma));
            $turma = $turmaDao->buscarPorId($idturma);
            
            $this->view->alunos = $alunos;
            $this->view->presentes = $presentes;
            $this->view->turma = $idturma;
            $this->view->dadosturma = $turma;
        }
    }
    
    public function confirmarAction()
    {
        if($this->getRequest()->isPost())
        {
            $util = new Util_Utilitarios();
            $idturma = $this->getRequest()->getParam("turma", 0);
            $presentes = $this->getRequest()->getParam("presentes", 0);
            $data = $this->getRequest()->getParam("dtpresenca", "");
            if($data != "")
                $data = $util->converterDataEntrada ($data);
            
            $chamadaDao = new Application_Model_Chamadas();
            $alunoTurmaDao = new Application_Model_AlunosTurmas();
            $alunos = $alunoTurmaDao->buscarAlunosPorTurma(array("idturma" => $idturma));
            foreach($alunos as $aluno):
                $dados = array(
                        "data" => $data,
                        "alunoturma_aluno_idaluno" => $aluno["idaluno"],
                        "alunoturma_turma_idturma" => $idturma
                    ); 
                $existPresenca = $chamadaDao->buscarChaves($dados);
                if($existPresenca == null):
                    if(in_array($aluno["idaluno"], $presentes)):
                        $dados["status"] = 1;
                    else:
                        $dados["status"] = 0;
                    endif;
                    $chamadaDao->insert($dados);
                endif; 
            endforeach;
            $this->view->resp = "Chamada gerada com sucesso!";
            $this->indexAction();
            $this->render("index");
        }
    }

}

