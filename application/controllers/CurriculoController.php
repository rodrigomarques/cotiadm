<?php

class CurriculoController extends Zend_Controller_Action
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
        
        $curriculoDao = new Application_Model_Curriculos();
        $cursoDao = new Application_Model_Cursos();
        
        $nome = $this->getRequest()->getParam("nome", "");
        $matricula = $this->getRequest()->getParam("matricula", "");
        $email = $this->getRequest()->getParam("email", "");
        $curso = $this->getRequest()->getParam("curso", "");
        $order = $this->getRequest()->getParam("order", "idaluno DESC");
        
        
        
        $dados = $curriculoDao->buscar(array("nome" => $nome."%", 'matricula' => $matricula , 
            'email' => "%". $email . "%", 'curso' => $curso, 'order' => $order, 'limit' => 500));
        
        $this->view->dados = $dados;
        $this->view->listaCurso = $cursoDao->consultarCurso(array());
        $this->view->nome = $this->getRequest()->getParam("nome", "");
        $this->view->curso = $this->getRequest()->getParam("curso", "");
        $this->view->email = $this->getRequest()->getParam("email", "");
        $this->view->matricula = $matricula;
        $this->view->idaluno = $this->getRequest()->getParam("idaluno", 0);
    }

    public function empregarAction()
    {
        $id = $this->getRequest()->getParam("id");
        $curriculoDao = new Application_Model_Curriculos();
        $curriculo = $curriculoDao->buscarId($id);
        
        $alocado = ($curriculo["alocado"] == 1)?0:1;
        
        $curriculoDao->update(array("alocado" => $alocado), "idcurriculo = " . $id);
        $this->render("index");
        $this->indexAction();
    }
    
    public function buscaralunoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $nome = $this->getRequest()->getParam("nome","");
        $alunoDao = new Application_Model_Alunos();
        $dados = $alunoDao->buscar(array('nome' => $nome . "%", 'limit' => 15, 'order' => 'idaluno desc'));
        if(count($dados) > 0):
            echo "<table width='100%'>";
            echo "<tr>";
            echo "<th></th>";
            echo "<th>NOME</th>";
            echo "<th>E-MAIL</th>";
            echo "<th>CPF</th>";
            echo "<th>TELEFONE</th>";
            echo "<th>CELULAR</th>";
            echo "</tr>";
            foreach($dados as $al):
                echo "<tr>";
                echo "<td><input type='radio' name='aluno' value='".$al["idaluno"]."'></td>";
                echo "<td>".$al["nome"]."</td>";
                echo "<td>".$al["email"]."</td>";
                echo "<td>".$al["cpf"]."</td>";
                echo "<td>".$al["telefone"]."</td>";
                echo "<td>".$al["celular"]."</td>";
                echo "</tr>";
            endforeach;
            echo "</table>";
        endif;
    }
    
    public function cadastrarAction()
    {
        $this->view->nome = $this->getRequest()->getParam("nome", "");
        if($this->getRequest()->isPost())
        {
            $util = new Util_Utilitarios();
            $aluno = $this->getRequest()->getParam("aluno", 0);
            
            if($aluno == 0):
                $this->view->resp = "Informe um aluno!";
                return;
            endif;
            
            $arquivo = $_FILES["arquivo"];
            $alunoDao = new Application_Model_Alunos();
            $curriculoDao = new Application_Model_Curriculos();
            $al = $curriculoDao->buscarPorId($aluno);
            $alNew = $alunoDao->buscarId($aluno);
            
            $arq1exist = false;
            $ext1 = "";
            $validacao = true;
            $mensagem = "";
            $nomeArquivo1 = "";

            if ($arquivo["name"] != "") {
                $arq1exist = true;
                $ext1 = $util->pegarExtensao($arquivo["name"]);
                //$nomeArquivo1 = $util->gerarNome($ext1);
                $nomeArquivo1 = $alNew["nome"] . "." . $ext1;
            }

            if ($arq1exist == true && !preg_match("/^(doc|docx|pdf)$/", $ext1)) {
                $mensagem .= "<div class=\"alert alert-error\">Currículo é inválido.</div>";
                $validacao = false;
            }

            if ($arq1exist == true && $arquivo["size"] > 1024 * 1024) {
                $mensagem .= "<div class=\"alert alert-error\">Currículo deve ter no máximo 1MB.</div>";
                $validacao = false;
            }
            
            if ($validacao == true) {

                    $upload = true;

                    if ($arquivo["name"] != "") {
						@unlink("bancocurriculo/" . $al["curriculo"]);
                        if (!move_uploaded_file($arquivo["tmp_name"], "bancocurriculo/" . $nomeArquivo1)) {
                            $upload = false;
                        }
                    }
                    
                    if($upload){
                        $data = array(
                            'aluno_idaluno' => $aluno,
                            'curriculo' => $nomeArquivo1,
                            'datacadastro' => date('Y-m-d H:i:s'),
                            'alocado' => 0
                        );
                        
                        if($al == null){
                            $curriculoDao->insert($data);
                        }else{
                            $curriculoDao->update($data, 'idcurriculo = ' . $al["idcurriculo"]);
                        }
                        $this->view->resp = "Currículo adicionado com sucesso";
                    }
            }else{
                $this->view->resp = $mensagem;
            }
        }
    }
    
   
}


