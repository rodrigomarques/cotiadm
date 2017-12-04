<?php

class RelatorioController extends Zend_Controller_Action {
    
    public function init() {
        
    }
    
    public function indexAction()
    {
        
    }
    
    public function turmaprofessorAction()
    {
        $professorDao = new Application_Model_Usuarios();
        $relatorioDao = new Application_Model_Relatorios();
        if($this->getRequest()->isPost())
        {
            $professor = $this->getRequest()->getParam("professor",0);
            $data = $this->getRequest()->getParam("data","");
            $util = new Util_Utilitarios();
            
            if($professor == 0)
                $professor = "";
            
            if($data != "")
                $data = $util->converterDataEntrada ($data);
            
            $this->_helper->viewRenderer->setNoRender(true);
            //$pdf = new Zend_Pdf();

            //$pdfPage = new Zend_Pdf_Page( Zend_Pdf_Page::SIZE_A4 );
            //$row = $pdfPage->getWidth();/*595*/
            //$alt = $pdfPage->getHeight();/*842*/
            
            
            
            $dados = $relatorioDao->listaTurmaProfessor(array(
                'data' => $data, "professor" => $professor));
            //$pdfPage->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES), 10);
            //$i = $alt - 20;
            
            $turmaAtual = 0;
            header ("Content-type: application/msword; charset=ISO-8859-1");
            
            $arquivo = "listaturmas.doc";
            $util = new Util_Utilitarios();
            
            $html = "<table width='100%'>";
            
            foreach($dados as $turmas)
            {
                /*if($i < 60)
                {
                    $pdf->pages[] = $pdfPage;
                    $pdfPage = new Zend_Pdf_Page( Zend_Pdf_Page::SIZE_A4 );
                    $pdfPage->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES), 10);
                    $i = $alt - 20;
                }
                */
                
                if($turmaAtual != $turmas["idturma"])
                {
                    if($turmaAtual != 0)
                    {
                        $html .= "</table>";
                        $html .= "<br /><br /><br /><br />";
                        $html .= "<table width='100%'>";
                    }
                    $html .= "<tr style='background:#EEE;'>";
                    $html .= "<td width='30%'>Professor: ". $turmas["professor"] ."</td>";
                    $html .= "<td colspan='4'>". utf8_decode($turmas["curso"]) ."</td>";
                    $html .= "</tr>";
                    
                    $html .= "<tr style='background:#EEE;'>";
                    $html .= "<td>". $turmas["horario"] ."</td>";
                    $html .= "<td>". $turmas["valor"] ."</td>";
                    $html .= "<td colspan='2'>". $util->converterDataSaida($turmas["inicio"]) ."</td>";
                    $html .= "<td>". $turmas["frequencia"] ."</td>";
                    $html .= "</tr>";
                    $turmaAtual = $turmas["idturma"];
                    
                    /*$pdfPage->drawLine(10, $i, 585, --$i);
                    $i -= 12;
                    $pdfPage->drawText("Professor: " . $turmas["professor"], 20, $i, 'UTF-8');
                    $pdfPage->drawText($turmas["frequencia"], 180, $i, 'UTF-8');
                    $pdfPage->drawText($turmas["curso"], 250, $i, 'UTF-8');
                    $i -= 12;
                    $pdfPage->drawText("Horario: " . $turmas["horario"], 20, $i, 'UTF-8');
                    $pdfPage->drawText("Valor: " . $turmas["valor"], 150, $i, 'UTF-8');
                    $pdfPage->drawText("Inicio: " . $util->converterDataSaida($turmas["inicio"]), 330, $i, 'UTF-8');
                    $i -= 7;
                    $pdfPage->drawLine(10, $i, 585, --$i);
                    $turmaAtual = $turmas["idturma"];
                    $i -= 10;*/
                }
                
                $html .= "<tr>";
                $html .= "<td>" . utf8_decode($turmas["aluno"])."</td>";
                $html .= "<td>" . (($turmas["refazendo"] == 0)?"Aluno Novo":"Refazendo")."</td>";
                $html .= "<td>" . $turmas["formapagamento"]."</td>";
                $html .= "<td>" . $turmas["statusturma"]."</td>";
                $html .= "<td> R$ " . number_format($turmas["total"],2,",",".")."</td>";
                $html .= "<td width='30%'>" . utf8_decode(nl2br($turmas["obs"]))."</td>";
                $html .= "</tr>";
                
                /*$pdfPage->drawText($turmas["aluno"], 20, $i, 'UTF-8');
                $pdfPage->drawText((($turmas["refazendo"] == 0)?"Aluno Novo":"Refazendo"), 250, $i, 'UTF-8');
                $pdfPage->drawText($turmas["formapagamento"], 350, $i, 'UTF-8');
                $pdfPage->drawText($turmas["total"], 500, $i, 'UTF-8');
                $i -= 15;*/
                
            }
            
            header ("Content-Disposition: attachment; filename=".$arquivo); //Gerando o arquivo
            echo $html; //Mostrando o arquivo

            exit;
            
            /*$pdf->pages[] = $pdfPage;*/

            //$pdf->save(APPLICATION_PATH . '/pdf/turmaprofessor.pdf');

            //$len = filesize(APPLICATION_PATH . '/pdf/turmaprofessor.pdf');
            //header("Expires: Mon, 26 Jul 1997 05:00:00 GMT\n");
            //header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            //header("Content-type: application/pdf;\n");
            //header("Content-Length: $len;\n");
            //header("Content-Disposition: attachment; filename=\"turmaprofessor.pdf\";\n\n");

            //readfile(APPLICATION_PATH . '/pdf/turmaprofessor.pdf');
            //exit;
            
            
        }
        $this->view->listaProfessor = $professorDao->listarProfessor();
    }
    
    public function alunonovoAction()
    {
        if($this->getRequest()->isPost())
        {
            $dataInicio = $this->getRequest()->getParam("anoinicio")."-".$this->getRequest()->getParam("mesinicio")."-01";
            $dataFim = $this->getRequest()->getParam("anofim")."-".$this->getRequest()->getParam("mesfim")."-30";
            
            $dados = array(
                'dataincio' => $dataInicio, 
                'datafim' => $dataFim
            );
            
            $relatorioDao = new Application_Model_Relatorios();
            $data = $relatorioDao->listaNovosAlunos($dados);
            
            $this->view->dados = $data;
        }
    }
    
    public function statusalunoAction()
    {
        if($this->getRequest()->isPost())
        {
            $dataInicio = $this->getRequest()->getParam("anoinicio")."-".$this->getRequest()->getParam("mesinicio")."-01";
            $dataFim = $this->getRequest()->getParam("anofim")."-".$this->getRequest()->getParam("mesfim")."-30";
            
            $dados = array(
                'dataincio' => $dataInicio, 
                'datafim' => $dataFim,
                'situacao' => $this->getRequest()->getParam("situacao")
            );
            
            $relatorioDao = new Application_Model_Relatorios();
            $data = $relatorioDao->statusaluno($dados);
            
            $this->view->dados = $data;
            $this->view->situacao = $dados["situacao"];
        }
    }
    
    public function emailalunosAction()
    {
        $cursoDao = new Application_Model_Cursos();
        $alunoTurmaDao = new Application_Model_AlunosTurmas();
        if($this->getRequest()->isPost())
        {
            $cursos = $this->getRequest()->getParam("cursos");
            $data = array();
            if(isset($cursos) && $cursos != ""):
                $filename = APPLICATION_PATH . "/data/relatorioalunos.csv";
                $realPath = realpath( $filename );

                if ( false === $realPath )
                {
                    touch( $filename );
                    chmod( $filename, 0777 );
                }

                $filename = realpath( $filename );
                $handle = fopen( $filename, "w" );
                foreach($cursos as $curso):
                    $valor = $alunoTurmaDao->buscarAlunosPorTurmaRelatorio(array('idcurso' =>$curso));
                    if(count($valor) > 0):

                        foreach($valor as $alunos):
                            $excel = array();
                            $excel[] = $alunos["nomealuno"];
                            $excel[] = $alunos["emailaluno"];
                            fputcsv( $handle, $excel, ";");
                        endforeach;
                    endif;
                endforeach;

                fclose( $handle );

                $this->getResponse()->setRawHeader( "Content-Type: application/vnd.ms-excel; charset=UTF-8" )
                    ->setRawHeader( "Content-Disposition: attachment; filename=relatorioalunos.csv" )
                    ->setRawHeader( "Content-Transfer-Encoding: binary" )
                    ->setRawHeader( "Expires: 0" )
                    ->setRawHeader( "Cache-Control: must-revalidate, post-check=0, pre-check=0" )
                    ->setRawHeader( "Pragma: public" )
                    ->setRawHeader( "Content-Length: " . filesize( $filename ) )
                    ->sendResponse();

                readfile( $filename ); 
                
                exit();
            endif;
        }
        
        $dadosCurso = $cursoDao->consultarCursoSite(array());  
        $this->view->dadosCurso = $dadosCurso;
    }
}

?>
