<?php

class CursoController extends Zend_Controller_Action
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
        $areaDao = new Application_Model_Areas();
        $cursoDao = new Application_Model_Cursos();
        $dados = array(
                "curso" => "",
                "status" => "",
                "area" => ""
            );
        if($this->getRequest()->isPost())
        {
            $curso = $this->getRequest()->getParam("curso");
            $status = $this->getRequest()->getParam("status", "");
            $area = $this->getRequest()->getParam("area", "");
            
            $dados = array(
                "curso" => $curso,
                "status" => $status,
                "area" => $area,
                'order' => 1
            );

            $this->view->curso = $curso;
            if($status != "")
                $this->view->status = $status;
            $this->view->area = $area;
        }

        $listaCursos = $cursoDao->consultarCurso($dados);
        $this->view->listaCursos = $listaCursos;
        $this->view->listaAreas = $areaDao->listar();
    }

    public function cadastrarAction()
    {
        $areaDao = new Application_Model_Areas();
        
        if($this->getRequest()->isPost())
        {
            $cursoDao = new Application_Model_Cursos();   
            
            $idarea = $this->getRequest()->getParam("area", 0);
            $curso = $this->getRequest()->getParam("nome", "");
            $cargahoraria = $this->getRequest()->getParam("cargahoraria", "");
            $posicao = $this->getRequest()->getParam("posicao", "");
            if($posicao == "" || !is_numeric($posicao))
                $posicao = 10;
            $resumo = $this->getRequest()->getParam("resumo");
            $ementa = $this->getRequest()->getParam("ementa");
            $company = $this->getRequest()->getParam("incompany", 0);
            $tag = $this->getRequest()->getParam("tag", "");
            $title = $this->getRequest()->getParam("title", "");
            $txtseo = $this->getRequest()->getParam("txtseo", "");
            $url = $this->getRequest()->getParam("urlpagseguro", "");
            
            $isTag = $cursoDao->buscarTag($tag);
            if(count($isTag) == 0 || $tag == ""){
                $status = 1;
                $dados = array(
                    'area_idarea' => $idarea,
                    "curso" => $curso ,
                    "status" => $status ,
                    "cargahoraria" => $cargahoraria ,
                    "posicao" => $posicao ,
                    "resumo" => $resumo ,
                    'incompany' => $company,
                    "ementa" => $ementa,
                    "tag" => $tag,
                    "txtseo" => $txtseo,
                    "title" => $title,
                    "urlpagseguro" => $url,
                );
            
                if($cursoDao->insert($dados))
                    $this->view->resp = "Curso cadastrado com sucesso!";
                else
                    $this->view->resp = "Curso não pode ser cadastrado!";
            }else{
                $this->view->resp = "A TAG ja existe!";
            }
            $this->view->area = $idarea;
            $this->view->curso = $curso;
            $this->view->cargahoraria = $cargahoraria;
            $this->view->posicao = $posicao;
            $this->view->resumo = $resumo;
            $this->view->ementa = $ementa;
            $this->view->txtseo = $txtseo;
            $this->view->tag = $tag;
            $this->view->title = $title;
            $this->view->urlpagseguro = $url;
        }
        
        $this->view->listaAreas = $areaDao->listar();
    }
    
    public function editarAction()
    {
        $id = $this->getRequest()->getParam("params", 0);
        $areaDao = new Application_Model_Areas();
        $cursoDao = new Application_Model_Cursos();
        $curso = $cursoDao->buscarId($id);
        if($id == 0 || $curso == null)
        {
            $this->indexAction();
            $this->view->resp = "Curso não encontrado";
            $this->render('index');
        }
        else
        {
            if($this->getRequest()->isPost())
            {
                $idarea = $this->getRequest()->getParam("area", 0);
                $nome = $this->getRequest()->getParam("nome", "");
                $cargahoraria = $this->getRequest()->getParam("cargahoraria", "");
                $posicao = $this->getRequest()->getParam("posicao", "");
                if($posicao == "" || !is_numeric($posicao))
                    $posicao = 10;
                $resumo = $this->getRequest()->getParam("resumo");
                $ementa = $this->getRequest()->getParam("ementa");
                $company = $this->getRequest()->getParam("incompany", 0);
                $tag = $this->getRequest()->getParam("tag", "");
                $title = $this->getRequest()->getParam("title", "");
                $txtseo = $this->getRequest()->getParam("txtseo", "");
                $urlpagseguro = $this->getRequest()->getParam("urlpagseguro", "");
                $isTag = array();
                if($curso["tag"] != $tag):
                    $isTag = $cursoDao->buscarTag($tag);
                endif;
                
                if(count($isTag) == 0){
                    $dados = array(
                        'area_idarea' => $idarea,
                        "curso" => $nome ,
                        "cargahoraria" => $cargahoraria ,
                        "posicao" => $posicao ,
                        "resumo" => $resumo ,
                        "ementa" => $ementa,
                        'incompany' => $company,
                        'txtseo' => $txtseo,
                        'title' => $title,
                        'urlpagseguro' => $urlpagseguro,
                    );
                    
                    if($curso["tag"] != $tag):
                        $dados["tag"] = $tag;
                    endif;
                
                    if($cursoDao->update($dados, 'idcurso = ' . $id))
                        $this->view->resp = "Curso editado com sucesso!";
                    else
                        $this->view->resp = "Modifique algum valor para edição do curso!";
                }else{
                    $this->view->resp = "Tag ja existente!";
                }
                $curso = $cursoDao->buscarId($id);
            }
            $this->view->listaAreas = $areaDao->listar();
            $this->view->curso = $curso;
        }
            
    }
    
    public function excluirAction()
    {
        $id = $this->getRequest()->getParam("params", 0);
        $cursoDao = new Application_Model_Cursos();
        $curso = $cursoDao->buscarId($id);
        if($id == 0 || $curso == null)
        {
            $this->view->resp = "Curso não encontrado";
        }
        else
        {
            if($curso["status"] == 1)
                $dados = array('status' => 0);
            else
                $dados = array('status' => 1);
            $cursoDao->update($dados, 'idcurso = ' . $id);
            $this->view->resp = "Status modificado com sucesso!";
        }
        $this->indexAction();
        $this->render('index');
    }


}



