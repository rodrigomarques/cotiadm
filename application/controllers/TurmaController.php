<?php

class TurmaController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        /*if(Zend_Auth::getInstance()->hasIdentity() != 1){
            /*Se o usuario não estiver logado, mandar para index*/
           // $this->_redirect('/index/');
        //}*/
    }

    public function indexAction()
    {
        $professorDao = new Application_Model_Usuarios();
        $cursoDao = new Application_Model_Cursos();
        $data = array();
        $turmaDao = new Application_Model_Turmas();
        $util = new Util_Utilitarios();
        $salaDao = new Application_Model_Salas();
        $data['status'] = 1;
        if($this->getRequest()->isPost())
        {
            
            $status = $this->getRequest()->getParam("status", "");
            $inicio = $this->getRequest()->getParam("inicio", "");
            $curso = $this->getRequest()->getParam("curso", "");
            $professor = $this->getRequest()->getParam("professor", "");
            $sala = $this->getRequest()->getParam("sala", "");
            
            if($inicio != "")
                $inicio = $util->converterDataEntrada ($inicio);
            
            
                $data['inicio'] = $inicio;
                $data['professor'] = $professor;
                $data['curso'] = $curso;
                $data['status'] = $status;
                $data['sala'] = $sala;
            
            
            $this->view->inicio = $this->getRequest()->getParam("inicio", "");
            $this->view->professor = $this->getRequest()->getParam("professor", "");
            $this->view->curso = $this->getRequest()->getParam("curso", "");
            $this->view->status = $this->getRequest()->getParam("status", "");
            $this->view->sala = $this->getRequest()->getParam("sala", "");
        }
        $data["orderby"] = "inicio ASC";
        $dados = $turmaDao->consultarTurma($data);
        $listaSala = $salaDao->listar();
        $this->view->listaSala = $listaSala;
        $this->view->dados = $dados;
        $this->view->listaProfessor = $professorDao->listarProfessor();
        $this->view->listaCurso = $cursoDao->consultarCurso(array());
    }
    
    public function listamobileAction()
    {
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        //$professorDao = new Application_Model_Usuarios();
        //$cursoDao = new Application_Model_Cursos();
        $data = array();
        $turmaDao = new Application_Model_Turmas();
        //$util = new Util_Utilitarios();
        //$salaDao = new Application_Model_Salas();
        $data['status'] = 1;
        $data["orderby"] = "inicio ASC";
        $dados = $turmaDao->consultarTurmaWebService($data);
        //$listaSala = $salaDao->listar();
        //$this->view->listaSala = $listaSala;
        
        echo json_encode($dados);
        
        //$this->view->listaProfessor = $professorDao->listarProfessor();
        //$this->view->listaCurso = $cursoDao->consultarCurso(array());
    }

    public function cadastrarAction()
    {
        $cursoDao = new Application_Model_Cursos();
        $usuarioDao = new Application_Model_Usuarios();
        $salaDao = new Application_Model_Salas();
        
        if($this->getRequest()->isPost())
        {
            $util = new Util_Utilitarios();
            
            $frequencia = $this->getRequest()->getParam("dias", "");
            $horario = $this->getRequest()->getParam("horario", "");
            $incio = $this->getRequest()->getParam("inicio", "");
            $fim = $this->getRequest()->getParam("fim", "");
            $valor = $this->getRequest()->getParam("valor", "");
            $vagas = $this->getRequest()->getParam("vagas", "");
            $curso = $this->getRequest()->getParam("curso", "");
            $professor = $this->getRequest()->getParam("professor", "");
            $sala = $this->getRequest()->getParam("sala", "");
            $company = $this->getRequest()->getParam("incompany", 0);
            
            $dtinicio = "";
            $dtfim = "";
            
            if($frequencia != "")
                $frequencia = implode (";", $frequencia);
            
            if($incio != "")
                $dtinicio = $util->converterDataEntrada ($incio);
            
            if($fim != "")
                $dtfim = $util->converterDataEntrada ($fim);
            
            if($sala == "")
                $sala = null;
            
            $data = array(
                'curso_idcurso' => $curso,
                'usuario_idusuario' => ($professor != "")?$professor:null,
                'sala_idsala' => $sala,
                'frequencia' => $frequencia,
                'horario' => $horario,
                'inicio' => $dtinicio,
                'fim' => $dtfim,
                'valor' => $valor,
                'vagas' => $vagas,
                'status' => 1,
                'turmaincompany' => $company
            );
            
            $turmaDao = new Application_Model_Turmas();
            if($turmaDao->insert($data))
            {
                if($professor != "")
                {
                    $professorDao = new Application_Model_Usuarios();
                    $cursoSel = $cursoDao->buscarId($curso);
                    $prof = $professorDao->buscarId($professor);
                    
                    $pag = "http://www.cotiinformatica.com.br";
                        $envioEmail = new Util_EnvioEmail();
                        $envioEmail->setAssunto("Parabens, foi cadastrada uma turma para voce no sistema da coti!");
                        $envioEmail->setMensagem('
                                    <div style="width: 630px; font-family: verdana; ">
                    <img src="http://www.cotiinformatica.com.br/email/topo.jpg" />
                    <p><strong>' .$prof["nome"] . '</strong>,
                                    <br />
                Nesse momento começa sua jornada ao SUCESSO!

                Foi criada a turma: ' . $cursoSel["curso"] . ' 
                <br />
                Frequencia: ' .$frequencia. ' 
                <br />
                Horário: ' . $horario . '
                <br />
                Inicio: ' .$incio . '

                Att,

                <em>Coti Informática.</em>
                <div style="width: 340px; height: auto;background-color: #013A76; color: #FFF; padding: 10px 20px; margin-top: 50px">
                        <strong>Acesse nosso site e confira novidades.</strong>
                    <a href="http://www.cotiinformatica.com.br" style="color: #FFF">www.cotiinformatica.com.br</a>
                    <br />
                        <strong>Já curtiu nossa página no facebook ? </strong>
                    <a href="http://www.facebook.com/cotiinformatica" style="color: #FFF">www.facebook.com/cotiinformatica</a>

                </div>

            </div>
                                ');
                        $envioEmail->setRemtente("contato@cotiinformatica.com.br");

                        $envioEmail->setDestinatario($prof["email"]);
                        $envioEmail->enviarMSG();
                }
                
                $this->view->resp = "Turma cadastrada com sucesso!";
            }
            else
            {
                $this->view->resp = "Turma não pode ser cadastrada!";
            }
            
        }
        
        $listaCurso = $cursoDao->consultarCurso(array('status' => 1));
        $listaProfessor = $usuarioDao->listarProfessor();
        $listaSala = $salaDao->listar();
        $this->view->listaCurso = $listaCurso;
        $this->view->listaProfessor = $listaProfessor;
        $this->view->listaSala = $listaSala;
    }
    
    public function editarAction()
    {
        $util = new Util_Utilitarios();
        $id = $this->getRequest()->getParam("params", 0);
        $cursoDao = new Application_Model_Cursos();
        $usuarioDao = new Application_Model_Usuarios();
        $turmaDao = new Application_Model_Turmas();
        $turma = $turmaDao->buscarPorId($id);
        $salaDao = new Application_Model_Salas();
        if($id == 0 || $turma == null)
        {
            $this->indexAction();
            $this->view->resp = "Turma não encontrada";
            $this->render('index');
        }
        else
        {
            
            if($this->getRequest()->isPost())
            {
                $frequencia = $this->getRequest()->getParam("dias", "");
                $horario = $this->getRequest()->getParam("horario", "");
                $incio = $this->getRequest()->getParam("inicio", "");
                $fim = $this->getRequest()->getParam("fim", "");
                $valor = $this->getRequest()->getParam("valor", "");
                $vagas = $this->getRequest()->getParam("vagas", "");
                $curso = $this->getRequest()->getParam("curso", "");
                $professor = $this->getRequest()->getParam("professor", "");
                
                $sala = $this->getRequest()->getParam("sala", "");
                $company = $this->getRequest()->getParam("incompany", 0);
                if($sala == "")
                    $sala = null;
                
                $dtinicio = "";
                $dtfim = "";

                if($frequencia != "")
                    $frequencia = implode (";", $frequencia);

                if($incio != "")
                    $dtinicio = $util->converterDataEntrada ($incio);

                if($fim != "")
                    $dtfim = $util->converterDataEntrada ($fim);

                $data = array(
                    'curso_idcurso' => $curso,
                    'usuario_idusuario' => ($professor != "")?$professor:null,
                    'frequencia' => $frequencia,
                    'horario' => $horario,
                    'inicio' => $dtinicio,
                    'fim' => $dtfim,
                    'valor' => $valor,
                    'vagas' => $vagas,
                    'turmaincompany' => $company,
                    'sala_idsala' => $sala
                );

                $turmaDao = new Application_Model_Turmas();
                if($turmaDao->update($data, "idturma = " . $id))
                {
                    $this->view->resp = "Turma editada com sucesso!";
                }
                
                $turma = $turmaDao->buscarPorId($id);
            }
        
            $freq = array();
            if($turma["frequencia"] != "")
                $freq = explode(";", $turma["frequencia"]);
            $listaCurso = $cursoDao->consultarCurso(array('status' => 1));
            $listaProfessor = $usuarioDao->listarProfessor();
            $listaSala = $salaDao->listar();
            
            $this->view->listaCurso = $listaCurso;
            $this->view->listaProfessor = $listaProfessor;
            $this->view->turma = $turma;
            $this->view->dias = $freq;
            $this->view->listaSala = $listaSala;
        }
        
    }

    public function atualizarvagasAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getRequest()->getParam("params", 0);
        $idsala = $this->getRequest()->getParam("idsala", 0);
        $turmaDao = new Application_Model_Turmas();
        $turma = $turmaDao->buscarPorId($id);
        $salaDao = new Application_Model_Salas();
        $sala = $salaDao->buscarPorId($idsala);
        $salaatual = $salaDao->buscarPorId($turma["sala_idsala"]);
        
        //$salaatual["maximo"]; --> Maximo Atual
        
        $novoValor = $sala["maximo"] - $salaatual["maximo"] + $turma["vagas"];
        echo $novoValor;
    }
    
    public function excluirAction()
    {
        $id = $this->getRequest()->getParam("params", 0);
        $turmaDao = new Application_Model_Turmas();
        $turma = $turmaDao->buscarPorId($id);
        if($turma != null)
        {
            if($turma["status"] == 1)
                $dados = array('status' => 0);
            else
                $dados = array('status' => 1);
            $turmaDao->update($dados, "idturma = " . $id);
            $this->view->resp = "Turma alterada com sucesso!";
        }
        $this->indexAction();
        $this->render("index");
    }
    
    public function veralunosAction()
    {
        $id = $this->getRequest()->getParam("params", 0);
        $turmaDao = new Application_Model_Turmas();
        $alunoTurmaDao = new Application_Model_AlunosTurmas();
        $turma = $turmaDao->buscarIdTurma($id);
        
        if($turma != null)
        {
            
            $alunos = $alunoTurmaDao->buscarAlunosPorTurma(array("idturma" => $id));
            $this->view->alunos = $alunos;
            $this->view->turma = $turma;
        }
    }
    
    public function excluiralunoAction()
    {
        $idAluno = $this->getRequest()->getParam("param1", 0);
        $idTurma = $this->getRequest()->getParam("param2", 0);
        
        $alunoTurmaDao = new Application_Model_AlunosTurmas();
        $registro = $alunoTurmaDao->buscarChaves(array("aluno_idaluno" => $idAluno, "turma_idturma" => $idTurma));
        
        if($registro == null)
        {
            $this->view->resp = "Acesso invalido!";
        }
        else
        {
            
            $turmaDao = new Application_Model_Turmas();
            $chamadaDao = new Application_Model_Chamadas();
            $chamadaDao->delete("alunoturma_aluno_idaluno = " . $idAluno.  " AND alunoturma_turma_idturma = " . $idTurma);
            
            //if($registro["reserva"] == 0){
                $turma = $turmaDao->buscarPorId($idTurma);
                $turmaDao->update(array('vagas' => $turma["vagas"] + 1), 'idturma = ' . $idTurma) ;
            //}
            $alunoTurmaDao->delete("aluno_idaluno = " . $idAluno.  " AND turma_idturma = " . $idTurma);
            $this->view->resp = "Aluno excluído da turma com sucesso!";
        }
        $this->veralunosAction();
        $this->render("veralunos");
        
    }
 
    public function vervagasAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getRequest()->getParam("idsala", "");
        $salaDao = new Application_Model_Salas();
        if($id == "")
        {
            echo 0;
        }
        else 
        {
            $sala = $salaDao->buscarPorId($id);
            if($sala != null)
            {
                echo $sala["maximo"];
            }
            else 
            {
                echo 0;
            }
        }
    }
    
    public function excluirturmaAction()
    {
        $id = $this->getRequest()->getParam("params", "");
        $turmaDao = new Application_Model_Turmas();
        $turma = $turmaDao->buscarPorId($id);
        if($turma != null)
        {
            $alunoTurmaDao = new Application_Model_AlunosTurmas();
            $alunoTurma = $alunoTurmaDao->buscarAlunosPorTurma(array('idturma' => $id));
            if(count($alunoTurma) > 0)
            {
                $this->view->resp = "Existem alunos na turma, não é possível excluir!";
            }
            else
            {
                if($turmaDao->delete('idturma = ' . $id))
                    $this->view->resp = "Turma excluída com sucesso!";
                else
                    $this->view->resp = "Turma não pode ser excluída!";
            }
        }
        else
        {
            $this->view->resp = "Nao existe esta turma cadastrada!";
        }
        $this->indexAction();
        $this->render("index");
    }
    
    public function gerarpdfAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $pdf = new Zend_Pdf();
        
        $pdfPage = new Zend_Pdf_Page( Zend_Pdf_Page::SIZE_A4_LANDSCAPE );
        $row = $pdfPage->getWidth();/*842*/
        $alt = $pdfPage->getHeight();/*595*/
        
        $pdfImage = Zend_Pdf_Image::imageWithPath(APPLICATION_PATH . '/pdf/logo.png');
        $pdfPage->drawImage( $pdfImage, 10, 494, 419, 590);
        
        $idturma = $this->getRequest()->getParam("params");
        $turmaDao = new Application_Model_Turmas();
        $alunoTurmaDao = new Application_Model_AlunosTurmas();
        $util = new Util_Utilitarios();
        
        $turma = $turmaDao->buscarIdTurma($idturma);
        
        
        $listaAlunoTurma = $alunoTurmaDao->buscarAlunosPorTurma(array('idturma' => $turma["idturma"]));
        if(count($listaAlunoTurma) > 0)
        {
            $pdfPage->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD), 26);
            $pdfPage->drawText("ALUNOS", 30, 440, 'UTF-8');

            $pdfPage->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD), 18);
            $pdfPage->drawText($turma["curso"], 430, 560, 'UTF-8');

            $pdfPage->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD), 12);
            $pdfPage->drawText("Período: ". $util->definirSemana($turma["frequencia"]), 430, 540, 'UTF-8');
            $pdfPage->drawText("Início: " . $util->converterDataSaida($turma["inicio"]), 430, 520, 'UTF-8');
            $pdfPage->drawText("Carga Horária: " . $turma["cargahoraria"] . " Hrs", 430, 500, 'UTF-8');

            $pdfPage->drawText("Calendário de Turmas", 420, 476, 'UTF-8');
            $pdfPage->drawRectangle(10, 490, 830, 10, Zend_Pdf_Page::SHAPE_DRAW_STROKE  );
        
            $pdfPage->drawLine(180, 470, 830, 468);
            $pdfPage->drawLine(180, 450, 830, 448);
            $pdfPage->drawLine(180, 430, 830, 428);

            /*LINHAS*/
            $pdfPage->drawLine(10, 400, 830, 398);
            $pdfPage->drawLine(10, 370, 830, 368);
            $pdfPage->drawLine(10, 340, 830, 338);
            $pdfPage->drawLine(10, 310, 830, 308);
            $pdfPage->drawLine(10, 280, 830, 278);
            $pdfPage->drawLine(10, 250, 830, 248);
            $pdfPage->drawLine(10, 220, 830, 218);
            $pdfPage->drawLine(10, 190, 830, 188);
            $pdfPage->drawLine(10, 160, 830, 158);
            $pdfPage->drawLine(10, 130, 830, 128);
            $pdfPage->drawLine(10, 100, 830, 98);
            $pdfPage->drawLine(10, 70, 830, 68);
            $pdfPage->drawLine(10, 40, 830, 38);

            $pdfPage->drawLine(180, 490, 182, 10);

            for($i = 183; $i < 800; $i += 50 )
                $pdfPage->drawText("____/____", $i, 455, 'UTF-8');

            /*COLUNAS*/        
            for($i = 230; $i <= 780; $i += 50 )
                        $pdfPage->drawLine($i, 470, ($i + 2), 10);
            /*$pdfPage->drawLine(230, 470, 232, 10);
            $pdfPage->drawLine(280, 470, 282, 10);
            $pdfPage->drawLine(330, 470, 332, 10);
            $pdfPage->drawLine(380, 470, 382, 10);
            $pdfPage->drawLine(430, 470, 432, 10);
            $pdfPage->drawLine(480, 470, 482, 10);
            $pdfPage->drawLine(530, 470, 532, 10);
            $pdfPage->drawLine(580, 470, 582, 10);
            $pdfPage->drawLine(630, 470, 632, 10);
            $pdfPage->drawLine(680, 470, 682, 10);
            $pdfPage->drawLine(730, 470, 732, 10);
            $pdfPage->drawLine(780, 470, 782, 10);*/

            $pdfPage->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES), 10);
            $pdfPage->drawText("* Aluno Refazendo", 720, 500, 'UTF-8');
        
            $i = 385;
            $pdfPage->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES), 10);
            foreach($listaAlunoTurma as $at)
            {
                $pdfPage->drawText((($at["refazendo"] == 1)?"*":"").(mb_strtolower($at["nomealuno"],'UTF-8')), 12, $i, 'UTF-8');
                $i -= 30;
                
                if($i < 20)
                {
                    
                    $pdf->pages[] = $pdfPage;
                    $pdfPage = new Zend_Pdf_Page( Zend_Pdf_Page::SIZE_A4_LANDSCAPE );
                    
                    $pdfPage->drawImage( $pdfImage, 10, 494, 419, 590);
        
                    $pdfPage->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD), 26);
                    $pdfPage->drawText("ALUNOS", 30, 440, 'UTF-8');

                    $pdfPage->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD), 18);
                    $pdfPage->drawText($turma["curso"], 430, 560, 'UTF-8');

                    $pdfPage->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD), 12);
                    $pdfPage->drawText("Período: ". $util->definirSemana($turma["frequencia"]), 430, 540, 'UTF-8');
                    $pdfPage->drawText("Início: " . $util->converterDataSaida($turma["inicio"]), 430, 520, 'UTF-8');
                    $pdfPage->drawText("Carga Horária: " . $turma["cargahoraria"] . " Hrs", 430, 500, 'UTF-8');

                    $pdfPage->drawText("Calendário de Turmas", 420, 476, 'UTF-8');
                    $pdfPage->drawRectangle(10, 490, 830, 10, Zend_Pdf_Page::SHAPE_DRAW_STROKE  );

                    $pdfPage->drawLine(180, 470, 830, 468);
                    $pdfPage->drawLine(180, 450, 830, 448);
                    $pdfPage->drawLine(180, 430, 830, 428);

                    /*LINHAS*/
                    for($i = 400; $i >= 40; $i -= 50 )
                        $pdfPage->drawLine(10, $i, 830, $i - 2);

                    $pdfPage->drawLine(180, 490, 182, 10);

                    for($i = 183; $i < 800; $i += 50 )
                        $pdfPage->drawText("____/____", $i, 455, 'UTF-8');

                    /*COLUNAS*/        
                    for($i = 230; $i <= 780; $i += 50 )
                        $pdfPage->drawLine($i, 470, ($i + 2), 10);
                    

                    $pdfPage->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES), 10);
                    $pdfPage->drawText("* Aluno Refazendo", 720, 500, 'UTF-8');
                    
                    $i = 385;
                }
            }
        }
        
        $pdf->pages[] = $pdfPage;

        $pdf->save(APPLICATION_PATH . '/pdf/chamada.pdf');

        $len = filesize(APPLICATION_PATH . '/pdf/chamada.pdf');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT\n");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Content-type: application/pdf;\n");
        header("Content-Length: $len;\n");
        header("Content-Disposition: attachment; filename=\"chamada.pdf\";\n\n");

        readfile(APPLICATION_PATH . '/pdf/chamada.pdf');
        exit;
    }
}

