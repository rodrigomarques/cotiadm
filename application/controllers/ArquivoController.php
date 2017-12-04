<?php

class ArquivoController extends Zend_Controller_Action
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
        $arquivoDao = new Application_Model_Arquivos();
        $this->view->lista = $arquivoDao->listar();
        
    }
    
   public function cadastrarAction()
   {
       $cursoDao = new Application_Model_Cursos();
       if($this->getRequest()->isPost()){
            $arq = $_FILES["arquivo"];
            if($arq["name"] == ""){
                $this->view->resp = "<span id='validate'>Informe um arquivo para o upload!</span>";
            }else{
                
                if($arq["size"] > 1024 * 1024){
                    $msg = "<span id='validate'>Arquivo não pode ter mais que 1MB!</span>";
                }else{
                    $util = new Util_Utilitarios();
                    $arquivoDao = new Application_Model_Arquivos();

                    $extensao = $util->pegarExtensao($arq["name"]);
                    $nomeArquivo = $util->gerarNome($extensao);
                    $session = new Zend_Session_Namespace();

                    $dados = array(
                        "curso_idcurso" => ($this->getRequest()->getParam("curso","") == "")?null:$this->getRequest()->getParam("curso"),
                        "titulo" => $this->getRequest()->getParam("titulo"),
                        "caminho" => $nomeArquivo,
                    );

                    $msg = "<span id='validate'>Arquivo não pode ser adicionado!</span>";

                    if(move_uploaded_file($arq["tmp_name"], "temparquivos/" . $nomeArquivo)){
                        $idarq = $arquivoDao->insert($dados);
                        if($idarq){
                            $msg = "<span id='validate'>Arquivo adicionado!</span>";
                        }else
                            @unlink ("temparquivos/" . $nomeArquivo);
                    }
                }
            
                $this->view->titulo = $this->getRequest()->getParam("titulo","");
                $this->view->resp = $msg;
            }
        }
        $lista = $cursoDao->consultarCurso(array('status' => 1));
        $this->view->listaCurso =$lista;
        
   }
   
}


