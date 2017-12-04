<?php

class AlunoController extends Zend_Controller_Action
{

    private $estado = array(
        'RJ', 'AC', 'AL', 'AM', 'AP', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MG', 
        'MS', 'MT', 'PA', 'PB', 'PE', 'PI', 'PR', 'RN', 'RO', 'RR', 'RS', 'SC',
        'SE', 'SP', 'TO'
    );
    
    private $pagamento = array(
        'A VISTA','BOLETO E CHEQUE', 'BOLETO', 'CHEQUE','DINHEIRO E BOLETO', 'EMPENHO', 'CARTÃO', 
        'CARTÃO E BOLETO', 'CORTESIA'
    );
    
     private $statusAluno = array(
        'ATIVO','TRANCADO','CANCELADO'
    );
    
    public function init()
    {
        if(Zend_Auth::getInstance()->hasIdentity() != 1){
            /*Se o usuario não estiver logado, mandar para index*/
            $this->_redirect('/index/');
        }
        /* Initialize action controller here */
        $this->view->estados = $this->estado;
        $this->view->formaPagamento = $this->pagamento;
        $this->view->statusAluno = $this->statusAluno;
    }

    public function indexAction()
    {
        
        $alunoDao = new Application_Model_Alunos();
        $cursoDao = new Application_Model_Cursos();
        $nome = $this->getRequest()->getParam("nome", "");
        $matricula = $this->getRequest()->getParam("matricula", "");
        $email = $this->getRequest()->getParam("email", "");
        $cpf = $this->getRequest()->getParam("cpf", "");
        $curso = $this->getRequest()->getParam("curso", "");
        $order = $this->getRequest()->getParam("order", "idaluno DESC");
        $dados = $alunoDao->buscar(array("nome" => $nome."%", 'matricula' => $matricula , 
            'email' => "%". $email . "%", 'cpf' => $cpf."%", 'order' => $order, 'limit' => 500,
            'curso' => $curso));
        
        $exp = $this->getRequest()->getParam("exportar", "");
        
        if($exp != ""){
            $util = new Util_Utilitarios();
            $listEmails = '';
            foreach ($dados as $al):
                $listEmails .= $al["email"] . ",";
            endforeach;
            $arquivo = APPLICATION_PATH . "/data/alunos.txt";
            $util->exportarTXT($arquivo, $listEmails);
            header("Content-Type: application/text"); // informa o tipo do arquivo ao navegador
            header("Content-Length: ".filesize($arquivo)); // informa o tamanho do arquivo ao navegador
            header("Content-Disposition: attachment; filename=".basename($arquivo)); // informa ao navegador que é tipo anexo e faz abrir a janela de download, tambem informa o nome do arquivo
            readfile($arquivo); // lê o arquivo
            exit;
        }
        
        $this->view->dados = $dados;
        $this->view->nome = $this->getRequest()->getParam("nome", "");
        $this->view->cpf = $this->getRequest()->getParam("cpf", "");
        $this->view->email = $this->getRequest()->getParam("email", "");
        $this->view->curso = $this->getRequest()->getParam("curso", "");
        $this->view->matricula = $matricula;
        $this->view->idaluno = $this->getRequest()->getParam("idaluno", 0);
        $this->view->listaCurso = $cursoDao->consultarCurso(array('status' => 1, 'order' => 1));
    }

    public function cadastrarAction()
    {
        $turmaDao = new Application_Model_Turmas();
        $alunoTurmaDao = new Application_Model_AlunosTurmas();
        $cursoDao = new Application_Model_Cursos();
        if($this->getRequest()->isPost())
        {
            $util = new Util_Utilitarios();
            $alunoDao = new Application_Model_Alunos();
            
            $nome = $this->getRequest()->getParam("nome", "");
            $email = $this->getRequest()->getParam("email", "");
            $sexo = $this->getRequest()->getParam("sexo", "");
            $nascimento = $this->getRequest()->getParam("dtnascimento", "");
            $telefone = $this->getRequest()->getParam("telefone", "");
            $celular = $this->getRequest()->getParam("celular", "");
            $cpf = $this->getRequest()->getParam("cpf", "");
            $rg = $this->getRequest()->getParam("rg", "");
            $respaluno = $this->getRequest()->getParam("respaluno", "");
            $observacaoaluno = $this->getRequest()->getParam("obsaluno", "");
            
            $logradouro = $this->getRequest()->getParam("logradouro", "");
            $cidade = $this->getRequest()->getParam("cidade", "");
            $numero = ($this->getRequest()->getParam("numero", "") == "" || !is_numeric($this->getRequest()->getParam("numero", "")))?0:$this->getRequest()->getParam("numero", 0);
            $cep = $this->getRequest()->getParam("cep", "");
            $estado = $this->getRequest()->getParam("estado", "");
            $complemento = $this->getRequest()->getParam("complemento", "");
            $bairro = $this->getRequest()->getParam("bairro", "");
            $pagamento = "";
            $turma = $this->getRequest()->getParam("turmas", "");
            
            $responsavel = "";
            $obs = "";
                            
            if(!$turma == ""){
                
            
            if($nascimento != "")
                $nascimento = $util->converterDataEntrada ($nascimento);
            
            $data = array(
                'nome' => $nome,
                'email' => $email,
                'sexo' => $sexo,
                'nascimento' => $nascimento,
                'cpf' => $cpf,
                'rg' => $rg,
                'telefone' => $telefone,
                'celular' => $celular,
                'responsavelaluno' => $respaluno,
                'observacaoaluno' => $observacaoaluno,
            );
            if($alunoDao->buscarCpf($cpf) == null)
            {
                $idaluno = $alunoDao->insert($data);
                if($idaluno)
                {
                    $dataEnd = array(
                        'aluno_idaluno' => $idaluno,
                        'logradouro' => $logradouro,
                        'numero' => $numero,
                        'complemento' => $complemento,
                        'cep' => $cep,
                        'bairro' => $bairro,
                        'cidade' => $cidade,
                        'estado' => $estado
                    );

                    $enderecoDao = new Application_Model_Enderecos();
                    if($enderecoDao->insert($dataEnd))
                    {
                        
                        if($turma != "")
                        {
                            $pagamento = $this->getRequest()->getParam("pagamento", "");
                            $responsavel = $this->getRequest()->getParam("responsavel", "");
                            $obs = $this->getRequest()->getParam("obs", "");
                            $valortotal = $this->getRequest()->getParam("valortotal", 0);
                            if($valortotal == "" || $valortotal == 0)
                                $valortotal = 0;
                            
                            $alunoreserva = $this->getRequest()->getParam("alunoreserva", 0);
                            $pendente = $this->getRequest()->getParam("pendente", 0);
                            
                            $dataTurma = array(
                                'aluno_idaluno' => $idaluno,
                                'turma_idturma' => $turma,
                                'refazendo' => 0,
                                'formapagamento' => $pagamento,
                                'responsavel' => $responsavel,
                                'obs' => $obs,
                                'total' => $valortotal,
                                'reserva' => $alunoreserva,
                                'pendencia' => $pendente
                            );

                            $idTurma = $alunoTurmaDao->insert($dataTurma);

                            if($alunoreserva != 1)
                            {
                                $turma = $turmaDao->buscarPorId($dataTurma['turma_idturma']);
                                $turmaDao->update(array('vagas' => $turma["vagas"] - 1), 'idturma = ' . $dataTurma['turma_idturma']) ;
                            }

                            $this->view->opt = 1;
                            $this->view->idaluno = $idaluno;
                            $this->view->idturma = $dataTurma['turma_idturma'];
                        }

                        $this->view->resp = "Aluno cadastrado com sucesso!";
                        $alunoantigo = $this->getRequest()->getParam("alunoantigo", 0);
                        if($alunoantigo != 1)
                        {
                        $pag = "http://www.cotiinformatica.com.br";
                        $envioEmail = new Util_EnvioEmail();
                        $envioEmail->setAssunto("Parabens, você acabe de iniciar a sua caminhada de sucesso!");
                        $envioEmail->setMensagem('
                                    <div style="width: 630px; font-family: verdana; ">
                    <img src="http://www.cotiinformatica.com.br/email/topo.jpg" />
                    <p><strong>' .$data["nome"] . '</strong>,
                                    <br />
                Nesse momento começa sua jornada ao SUCESSO!

                Com dedicação e muito estudo temos certeza que você chegará longe.

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

                        $envioEmail->setDestinatario($data["email"]);
                        $envioEmail->enviarMSG();
                        }
                        $nome = "";
                        $email = "";
                        $nascimento = "";
                        $cpf = "";
                        $telefone = "";
                        $celular = "";
                        $logradouro = "";
                        $cidade = "";
                        $bairro = "";
                        $estado = "";
                        $complemento = "";
                        $cep = "";
                        $numero = "";
                        $responsavel = "";
                        $obs = "";
                    }
                    else
                    {
                        $enderecoDao->delete('aluno_idaluno = ' . $idaluno);
                        $this->view->resp = "Não pode cadastrar aluno!";
                    }
                }
                else
                {
                    $this->view->resp = "Não pode cadastrar aluno!";
                }
            }
            else
            {
                $this->view->resp = "Aluno ja cadastrado - cpf não pode ser igual!";
            }
            }
            else{
                $this->view->resp = "Escolha uma turma para o aluno!";
            }
            
            $this->view->nome = $nome;
            $this->view->email = $email;
            $this->view->nasc = $nascimento;
            $this->view->cpf = $cpf;
            $this->view->telefone = $telefone;
            $this->view->celular = $celular;
            $this->view->logradouro = $logradouro;
            $this->view->cidade = $cidade;
            $this->view->bairro = $bairro;
            $this->view->estado = $estado;
            $this->view->complemento = $complemento;
            $this->view->cep = $cep;
            $this->view->numero = $numero;
            $this->view->pagamento = $pagamento;
            $this->view->responsavel = $responsavel;
            $this->view->obs = $obs;
            $this->view->obsaluno = $observacaoaluno;
        }
        
        $this->view->listaCurso = $cursoDao->consultarCurso(array('status' => 1));
        $this->view->listaTurma = $turmaDao->consultarTurma(array('status' => 1));
    }
    
    public function listarcursosAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $util = new Util_Utilitarios();
        $id = $this->getRequest()->getParam("id", 0);
        
        $turmaDao = new Application_Model_Turmas();
        $listaTurma = $turmaDao->consultarTurma(array('curso' => $id, 'status' => 1));
        if(count($listaTurma) > 0):
            echo "<select name='turmas' id='turmas'>";
                echo "<option value='0'>--Escolha a turma</option>";
                foreach($listaTurma as $turma):
                    echo "<option value='".$turma["idturma"]."'>".$util->converterDataSaida($turma["inicio"])." / ".$turma["horario"]."</option>";
                endforeach;
            echo "</select>";
        else:
            echo "Não existem turmas ativas!";
        endif;
    }
    
    public function conferirAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $idturma = $this->getRequest()->getParam("id", 0);
        if($idturma != 0)
        {
            $turmaDao = new Application_Model_Turmas();
            $turma = $turmaDao->buscarPorId($idturma);
            if($turma != null)
            {
                if($turma["vagas"] <= 0)
                {
                    echo "A turma escolhida não possui vaga, vai continuar mesmo? Olha lá hein!
                        \n\nDepois não vem dizer que não avisei e colocar a culpa no programador!";
                }
            }
        }
    }
    
    public function turmasAction()
    {
        $session = new Zend_Session_Namespace();
        //$session ->setExpirationSeconds( 86400 );
        $turmaDao = new Application_Model_Turmas();
        $cursoDao = new Application_Model_Cursos();
        $alunoDao = new Application_Model_Alunos();
        $id = $this->getRequest()->getParam("params", 0);
        $aluno = $alunoDao->buscarId($id);
                        
        if($aluno == null)
        {
            $this->indexAction();
            $this->render('index');
        }
        else
        {
            $this->view->aluno = $aluno;
            if($this->getRequest()->isPost())
            {
                $alunoTurmaDao = new Application_Model_AlunosTurmas();
                $pagamento = $this->getRequest()->getParam("pagamento", "");
                $responsavel = $this->getRequest()->getParam("responsavel", "");
                $obs = $this->getRequest()->getParam("obs", "");
                $turma = $this->getRequest()->getParam("turmas", "");
                
                if($turma == "")
                {
                    $this->view->resp = "Selecione uma turma para o aluno!";
                }
                else
                {
                    $data = array(
                        'aluno_idaluno' => $id,
                        'turma_idturma' => $turma,
                        'refazendo' => $this->getRequest()->getParam("refazendo", 0),
                        'pendencia' => $this->getRequest()->getParam("pendente", 0),
                    );

                    $alunoTurma = $alunoTurmaDao->buscarChaves($data);
                    if($alunoTurma != null)
                    {
                        $this->view->resp = "Turma ja cadastrada para este aluno!";
                    }
                    else
                    {
                        $alunoreserva = $this->getRequest()->getParam("alunoreserva", 0);
                        $turma = $turmaDao->buscarPorId($data['turma_idturma']);
                        $turmaDao->update(array('vagas' => $turma["vagas"] - 1), 'idturma = ' . $data['turma_idturma']) ;
                        $data['formapagamento'] = $pagamento;
                        $data['responsavel'] = $responsavel;
                        $data['obs'] = $obs;
                        $data['reserva'] = $alunoreserva;
                        $total = $this->getRequest()->getParam("valortotal", 0);
                        if($total == "" || $total == 0)
                            $total = 0;
                        $data['total'] = $total;
                        if($alunoTurmaDao->insert($data))
                       {
                           $this->view->resp = "Cadastrado com sucesso!";
                       }   
                    }
                }
            }
            $this->view->turmasAluno = $turmaDao->listarTurmaPorAluno($id);
        }
        
        
        $this->view->listaCurso = $cursoDao->consultarCurso(array('status' => 1));
        $this->view->listaTurma = $turmaDao->consultarTurma(array('status' => 1, 'orderby' => 'curso ASC'));
    }
    
     public function excluirAction()
    {
        $alunoDao = new Application_Model_Alunos();
        $id = $this->getRequest()->getParam("params", 0);
        $aluno = $alunoDao->buscarId($id);
                        
        if($aluno == null)
        {
            $this->view->resp = "Aluno não encontrado!";
            $this->indexAction();
            $this->render('index');
        }
        else
        {
            $enderecoDao = new Application_Model_Enderecos();
            $enderecoDao->delete('aluno_idaluno = ' . $id);
            $alunoTurmaDao = new Application_Model_AlunosTurmas();
            if(count($alunoTurmaDao->buscarTurmas(array('aluno_idaluno' => $id))) > 0){
                $parcelamentoDao = new Application_Model_Parcelamentos();
                $pagamentoDao = new Application_Model_Pagamentos();
                
                $pagamentos = $pagamentoDao->pagamentosPorAluno(array('idaluno' => $id));
                if(count($pagamentos) > 0):
                    foreach($pagamentos as $pag):
                        $parcelamentoDao->delete("pagamento_idpagamento = " . $pag["idpagamento"]);
                        $pagamentoDao->delete('idpagamento = ' . $pag["idpagamento"]);
                    endforeach;
                endif;
                
                $chamadaDao = new Application_Model_Chamadas();
                $chamadaDao->delete('alunoturma_aluno_idaluno = ' . $id);
                $alunoTurmaDao->delete('aluno_idaluno = ' . $id);
            }
            $alunoDao->delete('idaluno = ' . $id);
            $this->view->resp = "Aluno excluído com sucesso!";
            //$this->indexAction();
            //$this->render('index');
            $this->_forward('index', 'Aluno');
        }
    }
    
    public function excluirturmasAction()
    {
        /*
         * Tem que excluir a chamada dessse cara
         */
        $chamadaDao = new Application_Model_Chamadas();
        $alunoTurmaDao = new Application_Model_AlunosTurmas();
        $id = $this->getRequest()->getParam("params", 0);
        $idTurma = $this->getRequest()->getParam("paramsturma", 0);
        $this->view->resp = "Excluído com sucesso!";
        $chamadaDao->delete('alunoturma_aluno_idaluno = ' . $id . ' AND alunoturma_turma_idturma = ' . $idTurma);
        $alunoTurmaDao->delete('aluno_idaluno = ' . $id . ' AND turma_idturma = ' . $idTurma);
        $turmaDao = new Application_Model_Turmas();
        $turma = $turmaDao->buscarPorId($idTurma);
        $turmaDao->update(array('vagas' => $turma["vagas"] + 1), 'idturma = ' . $idTurma) ;
                    
        $this->turmasAction();
        $this->render('turmas');
    }
    
    public function editAction()
    {
        $idaluno = $this->getRequest()->getParam("params", 0);
        $alunoDao = new Application_Model_Alunos();
        
        if($this->getRequest()->isPost())
        {
            $util = new Util_Utilitarios();
            $alunoDao = new Application_Model_Alunos();
            
            $nome = $this->getRequest()->getParam("nome", "");
            $email = $this->getRequest()->getParam("email", "");
            $sexo = $this->getRequest()->getParam("sexo", "");
            $nascimento = $this->getRequest()->getParam("dtnascimento", "");
            $telefone = $this->getRequest()->getParam("telefone", "");
            $celular = $this->getRequest()->getParam("celular", "");
            $cpf = $this->getRequest()->getParam("cpf", "");
            $rg = $this->getRequest()->getParam("rg", "");
            $respaluno = $this->getRequest()->getParam("respaluno", "");
            $observacaoaluno = $this->getRequest()->getParam("obsaluno", "");
            
            $logradouro = $this->getRequest()->getParam("logradouro", "");
            $cidade = $this->getRequest()->getParam("cidade", "");
            $numero = ($this->getRequest()->getParam("numero", "") == "" || !is_numeric($this->getRequest()->getParam("numero", "")))?0:$this->getRequest()->getParam("numero", 0);
            $cep = $this->getRequest()->getParam("cep", "");
            $estado = $this->getRequest()->getParam("estado", "");
            $complemento = $this->getRequest()->getParam("complemento", "");
            $bairro = $this->getRequest()->getParam("bairro", "");

            if($nascimento != "")
                $nascimento = $util->converterDataEntrada ($nascimento);
            
            $data = array(
                'nome' => $nome,
                'email' => $email,
                'sexo' => $sexo,
                'nascimento' => $nascimento,
                'cpf' => $cpf,
                'rg' => $rg,
                'telefone' => $telefone,
                'celular' => $celular,
                'responsavelaluno' => $respaluno,
                'observacaoaluno' => $observacaoaluno,
            );
            $alunoDao->update($data, "idaluno = " . $idaluno);
            
            $dataEnd = array(
                'logradouro' => $logradouro,
                'numero' => $numero,
                'complemento' => $complemento,
                'cep' => $cep,
                'bairro' => $bairro,
                'cidade' => $cidade,
                'estado' => $estado
            );
                
            $enderecoDao = new Application_Model_Enderecos();
            $enderecoDao->update($dataEnd, 'aluno_idaluno = ' . $idaluno);
                
                        
            $this->view->resp = "Aluno editado com sucesso!";
        }
        
        $this->view->aluno = $alunoDao->buscarId($idaluno);
    }

    public function boletoAction()
    {
        $this->_helper->layout()->disableLayout();
        $session = new Zend_Session_Namespace();
        $nome = isset($session->nome)?$session->nome:"ATENDENTE";
        $view = new Zend_View_Helper_BaseUrl();
        $url = 'http://www.cotiinformatica.com.br' . $view->baseUrl();
        $turma = $this->getRequest()->getParam("turma", 0);
        $aluno = $this->getRequest()->getParam("aluno", 0);
        $util = new Util_Utilitarios();
        
        $alunoTurmaDao = new Application_Model_AlunosTurmas();
        $listaAluno = $alunoTurmaDao->buscarAluno(array('idturma' => $turma, 'idaluno' => $aluno));
        
        if(count($listaAluno) > 0)
        {
            $contrato = "<div class='contrato' id='contrato' style='margin: 10px;text-align: justify;'>";
            
            $data = $util->converterDataSaida($listaAluno["inicio"]);
            $dataExtenso = $util->mesExtenso($data);
            
            switch ($listaAluno["formapagamento"])
            {
               
                case 'CARTÃO E BOLETO' :
                    
                    $contrato .= "
                        <p>A COTI INFORMATICA no Estado do Rio de Janeiro, com sede na Av. Rio Branco 185, 
                        Sala 307 - Centro - Rio de Janeiro - RJ - CEP 20040-007 , a seguir designado 
                        abreviadamente 'COTI INFORMATICA', neste ato representado pelo Sr.(a) Fernanda Nunes 
                        Lopes de Souza Diretora Identidade nº 10356974-5 emitida pelo DETRAN RJ e o(a) 
                        Sr.(a) " .(($listaAluno["responsavelaluno"] != "")?$listaAluno["responsavelaluno"]:$listaAluno["nomealuno"]). ", CPF: " .$listaAluno["cpf"]." inscrito(a) na Identidade: 
                        " .$listaAluno["rg"]." - IFP 
                        ".(($listaAluno["responsavelaluno"] != "")?", responsável pelo aluno " . $listaAluno["nomealuno"].", ":"")."    
                        residente e domiciliado(a) ".$listaAluno["logradouro"] . " " . $listaAluno["numero"]. ", " . $listaAluno["complemento"]." 
                        Bairro: " .$listaAluno["bairro"]. " - " .$listaAluno["cidade"]." - " .$listaAluno["estado"]."
                        CEP: " . $listaAluno["cep"].", seguir designado(a) simplesmente 'Cliente', tem entre si justo e 
                        acordado o presente Contrato de Prestação de Serviços Educacionais, que se regerá pelas 
                        cláusulas e condições seguintes:</p>

                        <p><b>Cláusula 1ª </b>– A COTI INFORMATICA obriga-se a prestar serviços educacionais 
                        para o Cliente, inscrito na programação ".$listaAluno["curso"]." – Duração Total: " .$listaAluno["cargahoraria"]." horas, 
                        que tem seu início previsto para ".$dataExtenso.", ocorrendo às aulas sempre as 
                        ".$util->definirSemana($listaAluno["frequencia"])." de ".$listaAluno["horario"].", em conformidade com a legislação pertinente e 
                        de acordo com as normas do Regimento da COTI INFORMATICA, o qual o Cliente declara 
                        conhecer e que se encontra na Unidade.</p>

                        <p><b>Cláusula 2ª</b> – É atribuição e responsabilidade da COTI INFORMATICA o 
                        planejamento e a prestação dos serviços educacionais, a definição das formas, 
                        datas e locais para realização e avaliação da aprendizagem, a designação de 
                        docentes qualificados, a orientação didático-pedagógica e educacional, bem 
                        como outros procedimentos necessários ao bom desenvolvimento dos programas, nos 
                        termos das normas e legislação vigente.</p>

                        <p><b>§ Único – O Cliente reconhece que é prerrogativa da Unidade utilizar outros 
                        espaços físicos para a prestação dos serviços aqui descritos.</b></p>

                        <p><b>Cláusula 3ª </b>– O Cliente pagará a COTI INFORMATICA, pelas obrigações assumidas 
                        no presente contrato, a importância total de R$ <span class='valorcontrato'></span> <input type='text' name='valor' class='campovalor' value='".$listaAluno["total"]."' /> . 
                        Nas formas disponibilizadas pela COTI INFORMATICA, sendo: <span class='dadoscontrato'></span> <input type='text' name='dados' class='campodados' />.</p>

                        <p><b>Cláusula 4ª </b>–  Caso o Cliente opte pelo parcelamento, poderá fazê-lo 
                        exclusivamente nas modalidades aceitas pela COTI INFORMATICA. Sendo oferecida e escolhida 
                        a forma de pagamento através de boletos bancários, onde os mesmos serão pagos 
                        observando-se os prazos e condições acordadas abaixo:</p>

                        <p><b>§ 1º - Até a data do vencimento, os pagamentos poderão ser feitos em qualquer agência bancária, nas casas lotéricas 
                        ou via internet.</b></p>

                        <p><b>§	2º - Até 30 (trinta) dias após o vencimento, os pagamentos poderão ser feitos somente nas agências do Banco ITAÚ, com os acréscimos previstos no boleto, Multa de 2%, mais juros de 0,33% ao dia.</b></p>

                        <p><b>§ 3º - Ultrapassados 30 (trinta) dias do vencimento, os pagamentos somente poderão ser feitos solicitando um novo boleto no site do itaú.</b></p>

                        <p><b>Cláusula 5ª </b>– Quando o número mínimo de Clientes desejado não for atingido, 
                        a COTI INFORMATICA, mediante aviso prévio, poderá cancelar a programação ou marcar nova 
                        data de início da mesma, ficando a critério do Cliente a permanência ou não na nova data 
                        estipulada. Esta cláusula é válida inclusive para os módulos subseqüentes ao primeiro, 
                        caso existam.</p>

                        <p><b>§ Único - Caso o Cliente opte por não permanecer na programação, os valores 
                        pagos serão devolvidos.</b></p>

                        <p><b>Cláusula 6ª </b>– Ao efetuar a inscrição em uma determinada programação ou em 
                        módulos que a compõem, o Cliente motiva uma série de custos, tais como aquisição 
                        de recursos didáticos, alocação de ambientes, contratação de docentes, bem como a 
                        falta de tempo hábil para o preenchimento da vaga por outro Cliente. Desta forma, 
                        as partes concordam que:</p>
 
                        <p>§ 1º - Até 7 dias antes do início da programação será restituído o valor total pago. 
                        Para tanto, o Cliente deverá devolver a 1ª via do Recibo, na COTI INFORMATICA.</p>

                        <p>§ 2º - Do 6º dia até o 1º dia antes do início da programação será descontada a 
                        taxa administrativa de 10% (dez por cento) sobre o valor da programação. </p>

                        <p>§ 3º - Após o início da programação serão cobradas todas as atividades realizadas 
                        até a data do cancelamento da inscrição, bem como a taxa administrativa de 25% 
                        (vinte e cinco por cento) sobre o valor do restante da programação.</p>
 
                        <p>§ 4º - O cancelamento se dará nas seguintes situações:</p>

                        <p>
                        <ol type='a'>
                        <li> por solicitação do cliente, através de documento escrito, entregue na COTI INFORMATICA;</li>
                        </ol>
                        </p>

                        <p>§ 5º - Caso a programação seja realizada entre ausentes, será observado o disposto no 
                        art. 49 do Código de Defesa do Consumidor (Lei 8.078, de 11.09.90).</p>

                        <p><b>Cláusula 7ª </b>- As mudanças de horários ou dias da semana, dentro de uma mesma 
                        programação ou em programações diversas, será realizada desde que haja disponibilidade 
                        de vagas no turno solicitado, mediante as seguintes condições:</p>

                        <p>§ 1º - Com até 7 dias de antecedência à data de início da programação ou do módulo 
                        que compõe a mesma, a transferência será feita sem ônus para o Cliente.</p>

                        <p>§ 2º - Nos demais casos a transferência estará sujeita a avaliação da COTI INFORMATICA, 
                        bem como ao pagamento de taxa de transferência (se pertinente) cujo valor encontra-se 
                        na mesma.</p>

                        <p>§ 3º - Para efetuar a transferência, o Cliente deverá manifestar sua intenção por 
                        escrito e entregá-la na COTI INFORMATICA.</p>

                        <p><b>Cláusula 8ª </b>– Quaisquer declarações, atestados, certificados ou segunda via de documentos, 
                        serão cobrados no ato de sua solicitação.</p>
                        
                        <p>§ Único - As tabelas de preço de serviços, produtos e taxas estão à disposição para 
                        consulta na COTI INFORMATICA.</p>


                        <p><b>Cláusula 9ª </b>- O limite legal de faltas é de no máximo 25% (vinte e cinco 
                        por cento) da duração da programação, salvo os casos previstos no Decreto Lei 1044/69, 
                        Lei Federal 6202/75, Lei Federal 4375/64 e parecer CFE nº 47/76 (por exemplo, 
                        gestantes, serviço militar, doenças infecto-contagiosas). De acordo com a legislação 
                        relacionada anteriormente, as faltas serão justificadas para fins de aprovação, mas não 
                        existem reposições.</p>


                        <p><b>Cláusula 10ª </b>- Para obter a certificação ao final da programação, o Cliente 
                        deverá ter participado de no mínimo 75% (setenta e cinco por cento) das atividades 
                        propostas e ter sido aprovado por processo de avaliação.</p>

                        <p><b>Cláusula 11ª </b>- O presente contrato vigorará pelo tempo de duração da 
                        programação, constituindo motivos para sua rescisão:</p>

                        <p>§ 1º - Superveniência de caso fortuito ou força maior, nos termos da legislação 
                        civil; e</p>

                        <p>§ 2º - Inadimplência do Cliente.</p>

                        <p><b>Cláusula 12ª </b>- Qualquer reclamação que o Cliente queira apresentar a 
                        COTI INFORMATICA, relacionada à atividade, objeto deste contrato, só será aceita 
                        se formulada por escrito, mediante correspondência firmada pelo Cliente e protocolada 
                        junto à COTI INFORMATICA onde a atividade tenha sido ou estiver sendo prestada.</p>

                        <p><b>Cláusula 13ª </b>- Fica eleito o Foro da Comarca da Capital do Estado do 
                        Rio de Janeiro, para a solução de dúvidas ou litígios porventura decorrentes deste 
                        contrato, com expressa renúncia de qualquer outro, por mais privilegiado que seja.</p>

                        <p>E, por estarem as partes justas e contratadas, assinam o presente em duas vias, 
                        de igual teor, na presença das testemunhas abaixo nomeadas</p>

                        <p>Rio de Janeiro, " . $util->mesExtenso() . ".</p>

                        <p>
                        <br />
                        _______________________________________________________________<br />
                          Cliente:  ". (($listaAluno["responsavelaluno"] != "")?$listaAluno["responsavelaluno"]:$listaAluno["nomealuno"])."</p>

 
                        <p>
                        <img src='http://villafacil.com/rodrigoimagem/assfernanda.jpg' />
                        <br />_______________________________________________________________<br />
                        Prestador: COTI SERVIÇOS DE INFORMATICA</p>
 
                         <p>
                         <img src='http://villafacil.com/rodrigoimagem/imagem/assbelem.jpg' />
                         <br />_______________________________________________________________<br />
                         Testemunha 1: EDSON BELEM DE SOUZA JUNIOR</p>

                         <p>
                         <br /><br /><br />_______________________________________________________________<br />
                        Testemunha 2: ".$nome."</p>
                    ";
                    
                    break;
                
                case 'CARTÃO' :
                    
                    $contrato .= "
                        <p>A COTI INFORMATICA no Estado do Rio de Janeiro, com sede na Av. Rio Branco 185, 
                        Sala 307 - Centro - Rio de Janeiro - RJ - CEP 20040-007 , a seguir designado 
                        abreviadamente 'COTI INFORMATICA', neste ato representado pelo Sr.(a) Fernanda 
                        Nunes Lopes de Souza Diretora Identidade nº 10356974-5  emitida pelo DETRAN RJ e o(a) 
                        Sr.(a) ".(($listaAluno["responsavelaluno"] != "")?$listaAluno["responsavelaluno"]:$listaAluno["nomealuno"]).", CPF: ".$listaAluno["cpf"]." inscrito(a) na Identidade: 
                        ".$listaAluno["rg"]." 
                        ".(($listaAluno["responsavelaluno"] != "")?", responsável pelo aluno " . $listaAluno["nomealuno"].", ":"")."
                        residente e domiciliado(a) ".$listaAluno["logradouro"]." " . $listaAluno["numero"]. "
                        , " . $listaAluno["complemento"]." Bairro ".$listaAluno["bairro"]." ".$listaAluno["cidade"]." - " .$listaAluno["estado"]."
                        CEP: ".$listaAluno["cep"].", seguir designado(a) simplesmente 'Cliente', tem entre si justo e acordado 
                        o presente Contrato de Prestação de Serviços Educacionais, que se regerá pelas cláusulas 
                        e condições seguintes:</p>

                        <p><b>Cláusula 1ª </b>– A COTI INFORMATICA obriga-se a prestar serviços educacionais 
                        para o Cliente, inscrito na programação ".$listaAluno["curso"]." – Duração Total: 
                        " .$listaAluno["cargahoraria"]. " horas, que tem seu início previsto para ".$dataExtenso.", ocorrendo às aulas 
                        sempre as ".$util->definirSemana($listaAluno["frequencia"])." de " .$listaAluno["horario"]. ", em conformidade 
                        com a legislação pertinente e de acordo com as normas do Regimento da COTI INFORMATICA, 
                        o qual o Cliente declara conhecer e que se encontra na Unidade.</p>

                        <p><b>Cláusula 2ª </b>– É atribuição e responsabilidade da COTI INFORMATICA o 
                        planejamento e a prestação dos serviços educacionais, a definição das formas, datas e 
                        locais para realização e avaliação da aprendizagem, a designação de docentes qualificados, 
                        a orientação didático-pedagógica e educacional, bem como outros procedimentos necessários 
                        ao bom desenvolvimento dos programas, nos termos das normas e legislação vigente.</p>

                        <p>§ Único – O Cliente reconhece que é prerrogativa da Unidade utilizar outros espaços 
                        físicos para a prestação dos serviços aqui descritos.</p>

                        <p><b>Cláusula 3ª </b>– O Cliente pagará a COTI INFORMATICA, pelas obrigações assumidas 
                        no presente contrato, a importância total de R$ <span class='valorcontrato'></span> <input type='text' name='valor' class='campovalor' value='".$listaAluno["total"]."' />. 
                        Nas formas disponibilizadas pela COTI INFORMATICA, sendo: <span class='dadoscontrato'></span> <input type='text' name='dados' class='campodados' />.</p>

                        <p><b>Cláusula 4ª </b>– Quando o número mínimo de Clientes desejado não for atingido, 
                        a COTI INFORMATICA, mediante aviso prévio, poderá cancelar a programação ou marcar 
                        nova data de início da mesma, ficando a critério do Cliente a permanência ou não na 
                        nova data estipulada. Esta cláusula é válida inclusive para os módulos subseqüentes 
                        ao primeiro, caso existam.</p>

                        <p>§ Único - Caso o Cliente opte por não permanecer na programação, os valores pagos 
                        serão devolvidos.</p>

                        <p><b>Cláusula 5ª </b>– Ao efetuar a inscrição em uma determinada programação ou em módulos que a 
                        compõem, o Cliente motiva uma série de custos, tais como: impostos ao governo, tarifa a 
                        administradora de cartão de crédito,  aquisição de recursos didáticos, alocação de ambientes, 
                        contratação de docentes, bem como a falta de tempo hábil para o preenchimento da vaga por outro Cliente. 
                        Desta forma, as partes concordam que:</p>
 
                        <p>§ 1º - Até 7 dias antes do início da programação será restituído o valor total pago, subtraído os encargos 
                        financeiros referente a emissão de Nota fiscal (15%) + Taxa administrativa do valor da venda, 
                        da empresa de cartão de crédito (5%). Para tanto, o Cliente deverá devolver a 1ª via do Recibo, 
                        na COTI INFORMATICA.</p>

                        <p>§ 2º - Do 6º dia até o 1º dia antes do início da programação, além dos encargos do parágrafo superior, 
                        será descontada a taxa administrativa de 2% (dois por cento) sobre o valor da programação, totalizando 22% 
                        do valor pago.</p>

                        <p>§ 3º - Após o início da programação serão cobradas todas as atividades realizadas até a data do 
                        cancelamento da inscrição, bem como a taxa administrativa de 25% (vinte e cinco por cento) sobre o valor 
                        do restante da programação, que inclui 15% da emissão da nota fiscal da programação + 5% de tarifa da 
                        operadora do cartão + 5% do valor da programação.</p>
 
                        <p>§ 4º - O cancelamento se dará nas seguintes situações:</p>

                        <p>
                        <ol type='a'>
                            <li> por solicitação do cliente, através de documento escrito, entregue na 
                            COTI INFORMATICA;</li>
                        </ol></p>

                        <p>§ 5º - Caso a programação seja realizada entre ausentes, será observado o disposto 
                        no art. 49 do Código de Defesa do Consumidor (Lei 8.078, de 11.09.90).</p>

                        <p>Cláusula 6ª - As mudanças de horários ou dias da semana, dentro de uma mesma 
                        programação ou em programações diversas, será realizada desde que haja disponibilidade 
                        de vagas no turno solicitado, mediante as seguintes condições:</p>

                        <p>§ 1º - Com até 7 dias de antecedência à data de início da programação ou do módulo 
                        que compõe a mesma, a transferência será feita sem ônus para o Cliente.</p>

                        <p>§ 2º - Nos demais casos a transferência estará sujeita a avaliação da COTI INFORMATICA,
                        bem como ao pagamento de taxa de transferência (se pertinente) cujo valor encontra-se 
                        na mesma.</p>

                        <p>§ 3º - Para efetuar a transferência, o Cliente deverá manifestar sua intenção por 
                        escrito e entregá-la na COTI INFORMATICA.</p>

                        <p><b>Cláusula 7ª </b>– Quaisquer declarações, atestados, certificados ou segunda via de documentos, 
                        serão cobrados no ato de sua solicitação.</p>

                        <p>§ Único - As tabelas de preço de serviços, produtos e taxas estão à disposição para 
                        consulta na COTI INFORMATICA.</p>

                        <p><b>Cláusula 8ª </b>- O limite legal de faltas é de no máximo 25% (vinte e cinco 
                        por cento) da duração da programação, salvo os casos previstos no Decreto Lei 1044/69, 
                        Lei Federal 6202/75, Lei Federal 4375/64 e parecer CFE nº 47/76 (por exemplo, 
                        gestantes, serviço militar, doenças infecto-contagiosas). De acordo com a legislação 
                        relacionada anteriormente, as faltas serão justificadas para fins de aprovação, mas 
                        não existem reposições.</p>

                        <p><b>Cláusula 9ª </b>- Para obter a certificação ao final da programação, o Cliente 
                        deverá ter participado de no mínimo 75% (setenta e cinco por cento) das atividades 
                        propostas e ter sido aprovado por processo de avaliação.</p>

                        <p><b>Cláusula 10ª </b>- O presente contrato vigorará pelo tempo de duração da 
                        programação, constituindo motivos para sua rescisão:</p>

                        <p>§ 1º - Superveniência de caso fortuito ou força maior, nos termos da legislação 
                        civil; e</p>

                        <p>§ 2º - Inadimplência do Cliente.</p>

                        <p><b>Cláusula 11ª </b>- Qualquer reclamação que o Cliente queira apresentar a COTI 
                        INFORMATICA, relacionada à atividade, objeto deste contrato, só será aceita se formulada 
                        por escrito, mediante correspondência firmada pelo Cliente e protocolada junto à 
                        COTI INFORMATICA onde a atividade tenha sido ou estiver sendo prestada.</p>

                        <p><b>Cláusula 12ª </b>- Fica eleito o Foro da Comarca da Capital do Estado do 
                        Rio de Janeiro, para a solução de dúvidas ou litígios porventura decorrentes deste 
                        contrato, com expressa renúncia de qualquer outro, por mais privilegiado que seja.</p>

                        <p>E, por estarem as partes justas e contratadas, assinam o presente em duas vias, 
                        de igual teor, na presença das testemunhas abaixo nomeadas</p>

                        <p>Rio de Janeiro, " . $util->mesExtenso() . ".</p>

                        <p>
                        <br /><br /><br />
                        _______________________________________________________________<br />
                          Cliente:  ". (($listaAluno["responsavelaluno"] != "")?$listaAluno["responsavelaluno"]:$listaAluno["nomealuno"]) ."</p>

 
                        <p>
                        <img src='http://villafacil.com/rodrigoimagem/imagem/assfernanda.jpg' />
                        <br />_______________________________________________________________<br />
                        Prestador: COTI SERVIÇOS DE INFORMATICA</p>
 
                         <p>
                         <img src='http://villafacil.com/rodrigoimagem/imagem/assbelem.jpg' />
                         <br />_______________________________________________________________<br />
                         Testemunha 1: EDSON BELEM DE SOUZA JUNIOR</p>

                         <p>
                         <br /><br /><br />_______________________________________________________________<br />
                        Testemunha 2: ".$nome."</p>
                    ";
                    
                    
                    break;
                
                case 'A VISTA' :
                    $contrato .= "
                        <p>A COTI INFORMATICA no Estado do Rio de Janeiro, com sede na Av. Rio Branco 185, Sala 307 - Centro - 
                        Rio de Janeiro - RJ - CEP 20040-007 , a seguir designado abreviadamente 'COTI INFORMATICA', neste ato 
                        representado pelo Sr.(a) Fernanda Nunes Lopes de Souza Diretora Identidade nº 10356974-5 emitida pelo 
                        DETRAN RJ e o(a) Sr.(a) " .(($listaAluno["responsavelaluno"] != "")?$listaAluno["responsavelaluno"]:$listaAluno["nomealuno"]) . ", CPF: " . $listaAluno["cpf"] . " inscrito(a) na Identidade: " .$listaAluno["rg"]. " 
                        ".(($listaAluno["responsavelaluno"] != "")?", responsável pelo aluno " . $listaAluno["nomealuno"]:"")."
                        residente e domiciliado(a) " .$listaAluno["logradouro"] . " " . $listaAluno["numero"]. ", " . $listaAluno["complemento"] . " – " . 
                        $listaAluno["bairro"] . " - " .$listaAluno["cidade"] ." - " .$listaAluno["estado"].  " CEP: " . $listaAluno["cep"] . ", 
                        seguir designado(a) simplesmente 'Cliente', tem entre si justo e acordado o presente Contrato de Prestação de 
                        Serviços Educacionais, que se regerá pelas cláusulas e condições seguintes:</p>

                        <p><b>Cláusula 1ª </b>– A COTI INFORMATICA obriga-se a prestar serviços educacionais para o Cliente, inscrito na programação 
                        " .$listaAluno["curso"]. " – Duração Total: " .$listaAluno["cargahoraria"]. " Horas, que tem seu início previsto para " .$dataExtenso. ", ocorrendo às aulas 
                        sempre a(os) " . $util->definirSemana($listaAluno["frequencia"]) . " de " .$listaAluno["horario"]. ", em conformidade com a legislação pertinente e de acordo com as normas do Regimento 
                        da COTI INFORMATICA, o qual o Cliente declara conhecer e que se encontra na Unidade.</p>
                        
                        <p><b>Cláusula 2ª </b>– É atribuição e responsabilidade da COTI INFORMATICA  o planejamento e a prestação dos serviços 
                        educacionais, a definição das formas, datas e locais para realização e avaliação da aprendizagem, a designação de docentes 
                        qualificados, a orientação didático-pedagógica e educacional, bem como outros procedimentos necessários ao bom desenvolvimento 
                        dos programas, nos termos das normas e legislação vigente.</p>

                        <p><blockout><b>§ Único – O Cliente reconhece que é prerrogativa da Unidade utilizar outros espaços físicos para a prestação dos 
                        serviços aqui descritos.</b></blockout></p>

                        <p><b>Cláusula 3ª </b>– O Cliente pagará a COTI INFORMATICA, pelas obrigações assumidas no presente contrato, a importância total 
                        de R$ <span class='valorcontrato'></span> <input type='text' value='".$listaAluno["total"]."' name='valor' class='campovalor' />, à vista nas formas disponibilizadas pela COTI INFORMATICA, sendo:</p>


                        <p><b>Cláusula 4ª </b>– Caso algum cheque do Cliente deixe de compensar nas datas previstas, o mesmo deve comparecer a COTI 
                        INFORMATICA para resgatá-los em um período de 20 dias corridos, posterior a essa data o cheque será reapresentado ao banco.  
                        Caso o cheque volte pela segunda vez a COTI INFORMATICA enviará o débito para empresa de cobrança, além de poder promover o 
                        protesto da dívida, sem prejuízo da inclusão do nome do Cliente nos cadastros restritivos de crédito, de acordo com a 
                        legislação vigente, bem como poderá promover a cobrança judicial da dívida.</p>

                        <p><b>Cláusula 5ª </b>– Quando o número mínimo de Clientes desejado não for atingido, a COTI INFORMATICA, mediante aviso prévio, 
                        poderá cancelar a programação ou marcar nova data de início da mesma, ficando a critério do Cliente a permanência ou não na 
                        nova data estipulada. Esta cláusula é válida inclusive para os módulos subseqüentes ao primeiro, caso existam.</p>

                        <p><blockout><b>§ Único - Caso o Cliente opte por não permanecer na programação, os valores pagos serão devolvidos.
                        </b></blockout></p>


                        <p><b>Cláusula 6ª </b>– Ao efetuar a inscrição em uma determinada programação ou em módulos que a compõem, o Cliente motiva 
                        uma série de custos, tais como aquisição de recursos didáticos, alocação de ambientes, contratação de docentes, bem como a falta 
                        de tempo hábil para o preenchimento da vaga por outro Cliente. Desta forma, as partes concordam que:</p>
 
                        <p><blockout>§ 1º - Até 7 dias antes do início da programação será restituído o valor total pago. Para tanto, o Cliente deverá 
                        devolver a 1ª via do Recibo, na COTI INFORMATICA.</blockout></p>

                        <p><blockout>§ 2º - Do 6º dia até o 1º dia antes do início da programação será descontada a taxa administrativa de 10% 
                        (dez por cento) sobre o valor da programação.</blockout></p>

                        <p><blockout>§ 3º - Após o início da programação serão cobradas todas as atividades realizadas até a data do cancelamento da 
                        inscrição, bem como a taxa administrativa de 25% (vinte e cinco por cento) sobre o valor do restante da programação.</blockout></p>
 
                        <p><blockout>§ 4º - O cancelamento se dará nas seguintes situações:</blockout></p>

                        <p><blockout><ol type='a'><li>por solicitação do cliente, através de documento escrito, entregue na COTI INFORMATICA;
                        </li></ol></blockout></p>

                        <p><blockout>§ 5º - Caso a programação seja realizada entre ausentes, será observado o disposto no art. 49 do Código de 
                        Defesa do Consumidor (Lei 8.078, de 11.09.90).</blockout></p>


                        <p><b>Cláusula 7ª </b>- As mudanças de horários ou dias da semana, dentro de uma mesma programação ou em programações 
                        diversas, será realizada desde que haja disponibilidade de vagas no turno solicitado, mediante as seguintes condições:</p>

                        <p><blockout>§ 1º - Com até 7 dias de antecedência à data de início da programação ou do módulo que compõe a mesma, a 
                        transferência será feita sem ônus para o Cliente.<blockout></p>

                        <p><blockout>§ 2º - Nos demais casos a transferência estará sujeita a avaliação da COTI INFORMATICA, bem como ao pagamento 
                        de taxa de transferência (se pertinente) cujo valor encontra-se na mesma.</blockout></p>

                        <p><blockout>§ 3º - Para efetuar a transferência, o Cliente deverá manifestar sua intenção por escrito e entregá-la na 
                        COTI INFORMATICA.</blockout></p>


                        <p><b>Cláusula 8ª – Quaisquer declarações, atestados, certificados  ou segunda via de documentos, serão cobrados no ato de sua solicitação.</b></p>

                        <p><blockout>§ Único - As tabelas de preço de serviços, produtos e taxas estão à disposição para consulta na COTI 
                        INFORMATICA.</blockout></p>

                        <p><b>Cláusula 9ª - O limite legal de faltas é de no máximo 25% (vinte e cinco por cento) da duração da programação, salvo 
                        os casos previstos no Decreto Lei 1044/69, Lei Federal 6202/75, Lei Federal 4375/64 e parecer CFE nº 47/76 (por exemplo, gestantes, 
                        serviço militar, doenças infecto-contagiosas). De acordo com a legislação relacionada anteriormente, as faltas serão justificadas 
                        para fins de aprovação, mas não existem reposições.</b></p>


                        <p><b>Cláusula 10ª - Para obter a certificação ao final da programação, o Cliente deverá ter participado de no mínimo 75% 
                        (setenta e cinco por cento) das atividades propostas e ter sido aprovado por processo de avaliação.</b></p>


                        <p><b>Cláusula 11ª - O presente contrato vigorará pelo tempo de duração da programação, constituindo motivos para 
                        sua rescisão:</b></p>

                        <p><blockout>§ 1º - Superveniência de caso fortuito ou força maior, nos termos da legislação civil; e</blockout></p>

                        <p><blockout>§ 2º - Inadimplência do Cliente.</blockout></p>


                        <p><b>Cláusula 12ª - Qualquer reclamação que o Cliente queira apresentar a COTI INFORMATICA, relacionada à atividade, 
                        objeto deste contrato, só será aceita se formulada por escrito, mediante correspondência firmada pelo Cliente e protocolada 
                        junto à COTI INFORMATICA onde a atividade tenha sido ou estiver sendo prestada.</b></p>


                        <p><b>Cláusula 13ª - Fica eleito o Foro da Comarca da Capital do Estado do Rio de Janeiro, para a solução de dúvidas ou 
                        litígios porventura decorrentes deste contrato, com expressa renúncia de qualquer outro, por mais privilegiado que seja.</b></p>


                        <p>E, por estarem as partes justas e contratadas, assinam o presente em duas vias, de igual teor, na presença das 
                        testemunhas abaixo nomeadas</p>

                        <p>Rio de Janeiro, " . $util->mesExtenso() . ".</p>

                        <p>
                        <br /><br /><br />
                        _______________________________________________________________<br />
                          Cliente:  ". (($listaAluno["responsavelaluno"] != "")?$listaAluno["responsavelaluno"]:$listaAluno["nomealuno"]) ."</p>

 
                        <p>
                        <img src='http://villafacil.com/rodrigoimagem/imagem/assfernanda.jpg' />
                        <br />_______________________________________________________________<br />
                        Prestador: COTI SERVIÇOS DE INFORMATICA</p>
 
                         <p>
                         <img src='http://villafacil.com/rodrigoimagem/imagem/assbelem.jpg' />
                         <br />_______________________________________________________________<br />
                         Testemunha 1: EDSON BELEM DE SOUZA JUNIOR</p>

                         <p>
                         <br /><br /><br />_______________________________________________________________<br />
                        Testemunha 2: ".$nome."</p>

                    ";
                    
                    

                    break;
                
                case 'BOLETO E CHEQUE' : 
                    
                    $contrato .= "
                        <p>A COTI INFORMATICA no Estado do Rio de Janeiro, com sede na Av. Rio Branco 185, 
                        Sala 307 - Centro - Rio de Janeiro - RJ - CEP 20040-007 , a seguir designado abreviadamente 
                        'COTI INFORMATICA', neste ato representado pelo Sr.(a) Fernanda Nunes Lopes de Souza 
                        Diretora Identidade nº 10356974-5 emitida pelo DETRAN RJ e o(a) Sr.(a) " .(($listaAluno["responsavelaluno"] != "")?$listaAluno["responsavelaluno"]:$listaAluno["nomealuno"]) . 
                        ", ID: " .$listaAluno["rg"] . " inscrito(a) no CPF: " .$listaAluno["cpf"] .", " . (($listaAluno["responsavelaluno"] != "")?", responsável pelo aluno " . $listaAluno["nomealuno"].", " :"")."residente e domiciliado(a) 
                        " .$listaAluno["logradouro"] . " " . $listaAluno["numero"]. ", " . $listaAluno["complemento"] . " – " . 
                        $listaAluno["bairro"] . " - " .$listaAluno["cidade"] ." - " .$listaAluno["estado"] ." CEP: ".$listaAluno["cep"]."
                        , seguir designado(a) simplesmente 
                        'Cliente', tem entre si justo e acordado o presente Contrato de Prestação de Serviços 
                        Educacionais, que se regerá pelas cláusulas e condições seguintes:</p>
  
                        <p><b>Cláusula 1ª </b>– A COTI INFORMATICA obriga-se a prestar serviços educacionais para 
                        o Cliente, inscrito na programação " . $listaAluno["curso"] ." – Duração Total: ".$listaAluno["cargahoraria"]." Horas, 
                        que tem seu início 
                        previsto para ".$dataExtenso.",    ocorrendo às aulas sempre a(os) " .$util->definirSemana($listaAluno["frequencia"]) ."
                        de ".$listaAluno["horario"].",em conformidade com a legislação pertinente e de acordo com as normas 
                        do Regimento da COTI INFORMATICA, o qual o Cliente declara conhecer e que se encontra 
                        na Unidade.</p>

                        <p><b>Cláusula 2ª </b>– É atribuição e responsabilidade da COTI INFORMATICA o 
                        planejamento e a prestação dos serviços educacionais, a definição das formas, datas 
                        e locais para realização e avaliação da aprendizagem, a designação de docentes 
                        qualificados, a orientação didático-pedagógica e educacional, bem como outros 
                        procedimentos necessários ao bom desenvolvimento dos programas, nos termos das 
                        normas e legislação vigente.</p>

                        <p>§ Único – O Cliente reconhece que é prerrogativa da Unidade utilizar outros espaços 
                        físicos para a prestação dos serviços aqui descritos.</p>

                        <p><b>Cláusula 3ª </b>– O Cliente pagará a COTI INFORMATICA, pelas obrigações assumidas no 
                        presente contrato, a importância total de R$ <span class='valorcontrato'></span> <input type='text' name='valor' class='campovalor' value='".$listaAluno["total"]."' /> 
                        Nas formas disponibilizadas pela COTI INFORMATICA, sendo: <span class='dadoscontrato'></span> <input type='text' name='dados' class='campodados' />.</p>

                        <p><b>Cláusula 4ª </b>– Caso algum cheque do Cliente deixe de compensar nas datas previstas, 
                        o mesmo deve comparecer a COTI INFORMATICA para resgatá-los em um período de 20 dias 
                        corridos, posterior a essa data o cheque será reapresentado ao banco. Caso o cheque 
                        volte pela segunda vez a COTI INFORMATICA enviará o débito para empresa de cobrança, 
                        além de poder promover o protesto da dívida, sem prejuízo da inclusão do nome do 
                        Cliente nos cadastros restritivos de crédito, de acordo com a legislação vigente, bem 
                        como poderá promover a cobrança judicial da dívida.</p>

                        <p><b>Cláusula 5ª </b>– Quando o número mínimo de Clientes desejado não for atingido, 
                        a COTI INFORMATICA, mediante aviso prévio, poderá cancelar a programação ou marcar 
                        nova data de início da mesma, ficando a critério do Cliente a permanência ou não na 
                        nova data estipulada. Esta cláusula é válida inclusive para os módulos subseqüentes 
                        ao primeiro, caso existam.</p>

                        <p>§ Único - Caso o Cliente opte por não permanecer na programação, os valores pagos 
                        serão devolvidos.</p>

                        <p><b>Cláusula 6ª </b>– Ao efetuar a inscrição em uma determinada programação ou em 
                        módulos que a compõem, o Cliente motiva uma série de custos, tais como aquisição de 
                        recursos didáticos, alocação de ambientes, contratação de docentes, bem como a falta 
                        de tempo hábil para o preenchimento da vaga por outro Cliente. Desta forma, as partes 
                        concordam que:</p>
 
                        <p><blockout>§ 1º - Até 7 dias antes do início da programação será restituído o valor 
                        total pago. Para tanto, o Cliente deverá devolver a 1ª via do Recibo, na 
                        COTI INFORMATICA.</blockout></p>

                        <p><blockout>§ 2º - Do 6º dia até o 1º dia antes do início da programação será descontada 
                        a taxa administrativa de 10% (dez por cento) sobre o valor da programação.</blockout></p>

                        <p><blockout>§ 3º - Após o início da programação serão cobradas todas as atividades 
                        realizadas até a data do cancelamento da inscrição, bem como a taxa administrativa de 
                        25% (vinte e cinco por cento) sobre o valor do restante da programação.</blockout></p>
 
                        <p><blockout>§ 4º - O cancelamento se dará nas seguintes situações:</blockout></p>

                        <p><blockout>
                        <ol type='a'>
                            <li> por solicitação do cliente, através de documento escrito, entregue na COTI INFORMATICA;</li>
                        </li></ol></blockout></p>
                        
                        <p><blockout>§ 5º - Caso a programação seja realizada entre ausentes, será observado 
                        o disposto no art. 49 do Código de Defesa do Consumidor (Lei 8.078, de 11.09.90).</blockout></p>


                        <p><b>Cláusula 7ª </b>- As mudanças de horários ou dias da semana, dentro de uma mesma 
                        programação ou em programações diversas, será realizada desde que haja disponibilidade 
                        de vagas no turno solicitado, mediante as seguintes condições:</p>

                        <p><blockout>§ 1º - Com até 7 dias de antecedência à data de início da programação 
                        ou do módulo que compõe a mesma, a transferência será feita sem ônus para o Cliente.
                        </blockout></p>

                        <p><blockout>§ 2º - Nos demais casos a transferência estará sujeita a avaliação da 
                        COTI INFORMATICA, bem como ao pagamento de taxa de transferência (se pertinente) 
                        cujo valor encontra-se na mesma.</blockout></p>

                        <p><blockout>§ 3º - Para efetuar a transferência, o Cliente deverá manifestar sua 
                        intenção por escrito e entregá-la na COTI INFORMATICA.
                        </blockout></p>


                        <p><b>Cláusula 8ª </b>– Quaisquer declarações, atestados, certificados  ou segunda via de 
                        documentos, serão cobrados no ato de sua solicitação.</p>

                        <p><blockout>§ Único - As tabelas de preço de serviços, produtos e taxas estão à 
                        disposição para consulta na COTI INFORMATICA.</blockout></p>

                        <p><b>Cláusula 9ª </b>- O limite legal de faltas é de no máximo 25% (vinte e cinco por 
                        cento) da duração da programação, salvo os casos previstos no Decreto Lei 1044/69, 
                        Lei Federal 6202/75, Lei Federal 4375/64 e parecer CFE nº 47/76 (por exemplo, 
                        gestantes, serviço militar, doenças infecto-contagiosas). De acordo com a legislação 
                        relacionada anteriormente, as faltas serão justificadas para fins de aprovação, mas 
                        não existem reposições.</p>

                        <p><b>Cláusula 10ª </b>- Para obter a certificação ao final da programação, o 
                        Cliente deverá ter participado de no mínimo 75% (setenta e cinco por cento) das 
                        atividades propostas e ter sido aprovado por processo de avaliação.</p>

                        <p><b>Cláusula 11ª </b>- O presente contrato vigorará pelo tempo de duração da 
                        programação, constituindo motivos para sua rescisão:</p>

                        <p><blockout>§ 1º - Superveniência de caso fortuito ou força maior, nos termos da 
                        legislação civil; e</blockout></p>

                        <p><blockout>§ 2º - Inadimplência do Cliente.</blockout></p>


                        <p><b>Cláusula 12ª </b>- Qualquer reclamação que o Cliente queira apresentar a 
                        COTI INFORMATICA, relacionada à atividade, objeto deste contrato, só será aceita 
                        se formulada por escrito, mediante correspondência firmada pelo Cliente e 
                        protocolada junto à COTI INFORMATICA onde a atividade tenha sido ou estiver sendo 
                        prestada.</p>

                        <p><b>Cláusula 13ª </b>- Fica eleito o Foro da Comarca da Capital do Estado do Rio 
                        de Janeiro, para a solução de dúvidas ou litígios porventura decorrentes deste contrato, 
                        com expressa renúncia de qualquer outro, por mais privilegiado que seja.</p>


                        <p>E, por estarem as partes justas e contratadas, assinam o presente em duas vias, 
                        de igual teor, na presença das testemunhas abaixo nomeadas</p>

                        <p>Rio de Janeiro, " . $util->mesExtenso() . ".</p>

                        <p>
                        <br /><br /><br />
                        _______________________________________________________________<br />
                          Cliente:  ". (($listaAluno["responsavelaluno"] != "")?$listaAluno["responsavelaluno"]:$listaAluno["nomealuno"]) ."</p>

 
                        <p>
                        <img src='http://villafacil.com/rodrigoimagem/imagem/assfernanda.jpg' />
                        <br />_______________________________________________________________<br />
                        Prestador: COTI SERVIÇOS DE INFORMATICA</p>
 
                         <p>
                         <img src='http://villafacil.com/rodrigoimagem/imagem/assbelem.jpg' />
                         <br />_______________________________________________________________<br />
                         Testemunha 1: EDSON BELEM DE SOUZA JUNIOR</p>

                         <p>
                         <br /><br /><br />_______________________________________________________________<br />
                        Testemunha 2: ".$nome."</p>

";
                    
                    break;
                
                case 'BOLETO' : 
                    
                    $contrato .= "
                        <p>A COTI INFORMATICA no Estado do Rio de Janeiro, com sede na Av. Rio Branco 185, 
                        Sala 307 - Centro - Rio de Janeiro - RJ - CEP 20040-007 , a seguir designado 
                        abreviadamente 'COTI INFORMATICA', neste ato representado pelo Sr.(a) Fernanda Nunes 
                        Lopes de Souza Diretora Identidade nº 10356974-5 emitida pelo DETRAN RJ e o(a) 
                        Sr.(a) " .(($listaAluno["responsavelaluno"] != "")?$listaAluno["responsavelaluno"]:$listaAluno["nomealuno"]). ", CPF: " .$listaAluno["cpf"]." inscrito(a) na Identidade: 
                        " .$listaAluno["rg"]." - IFP 
                        ".(($listaAluno["responsavelaluno"] != "")?", responsável pelo aluno " . $listaAluno["nomealuno"].", ":"")."    
                        residente e domiciliado(a) ".$listaAluno["logradouro"] . " " . $listaAluno["numero"]. ", " . $listaAluno["complemento"]." 
                        Bairro: " .$listaAluno["bairro"]. " - " .$listaAluno["cidade"]." - " .$listaAluno["estado"]."
                        CEP: " . $listaAluno["cep"].", seguir designado(a) simplesmente 'Cliente', tem entre si justo e 
                        acordado o presente Contrato de Prestação de Serviços Educacionais, que se regerá pelas 
                        cláusulas e condições seguintes:</p>

                        <p><b>Cláusula 1ª </b>– A COTI INFORMATICA obriga-se a prestar serviços educacionais 
                        para o Cliente, inscrito na programação ".$listaAluno["curso"]." – Duração Total: " .$listaAluno["cargahoraria"]." horas, 
                        que tem seu início previsto para ".$dataExtenso.", ocorrendo às aulas sempre as 
                        ".$util->definirSemana($listaAluno["frequencia"])." de ".$listaAluno["horario"].", em conformidade com a legislação pertinente e 
                        de acordo com as normas do Regimento da COTI INFORMATICA, o qual o Cliente declara 
                        conhecer e que se encontra na Unidade.</p>

                        <p><b>Cláusula 2ª</b> – É atribuição e responsabilidade da COTI INFORMATICA o 
                        planejamento e a prestação dos serviços educacionais, a definição das formas, 
                        datas e locais para realização e avaliação da aprendizagem, a designação de 
                        docentes qualificados, a orientação didático-pedagógica e educacional, bem 
                        como outros procedimentos necessários ao bom desenvolvimento dos programas, nos 
                        termos das normas e legislação vigente.</p>

                        <p><b>§ Único – O Cliente reconhece que é prerrogativa da Unidade utilizar outros 
                        espaços físicos para a prestação dos serviços aqui descritos.</b></p>

                        <p><b>Cláusula 3ª </b>– O Cliente pagará a COTI INFORMATICA, pelas obrigações assumidas 
                        no presente contrato, a importância total de R$ <span class='valorcontrato'></span> <input type='text' name='valor' class='campovalor' value='".$listaAluno["total"]."' /> . 
                        Nas formas disponibilizadas pela COTI INFORMATICA, sendo: <span class='dadoscontrato'></span> <input type='text' name='dados' class='campodados' />.</p>

                        <p><b>Cláusula 4ª </b>–  Caso o Cliente opte pelo parcelamento, poderá fazê-lo 
                        exclusivamente nas modalidades aceitas pela COTI INFORMATICA. Sendo oferecida e escolhida 
                        a forma de pagamento através de boletos bancários, onde os mesmos serão pagos 
                        observando-se os prazos e condições acordadas abaixo:</p>

                        <p><b>§ 1º - Até a data do vencimento, os pagamentos poderão ser feitos em qualquer agência bancária, nas casas lotéricas 
                        ou via internet.</b></p>

                        <p><b>§ 2º - Até 30 (trinta) dias após o vencimento, os pagamentos poderão ser feitos somente nas agências do Banco ITAÚ, com os acréscimos previstos no boleto, Multa de 2%, mais juros de 0,33% ao dia.</b></p>

                        <p><b>§ 3º - Ultrapassados 30 (trinta) dias do vencimento, os pagamentos somente poderão ser feitos solicitando um novo boleto no site do itaú.</b></p>

                        <p><b>Cláusula 5ª </b>– Quando o número mínimo de Clientes desejado não for atingido, 
                        a COTI INFORMATICA, mediante aviso prévio, poderá cancelar a programação ou marcar nova 
                        data de início da mesma, ficando a critério do Cliente a permanência ou não na nova data 
                        estipulada. Esta cláusula é válida inclusive para os módulos subseqüentes ao primeiro, 
                        caso existam.</p>

                        <p><b>§ Único - Caso o Cliente opte por não permanecer na programação, os valores 
                        pagos serão devolvidos.</b></p>

                        <p><b>Cláusula 6ª </b>– Ao efetuar a inscrição em uma determinada programação ou em 
                        módulos que a compõem, o Cliente motiva uma série de custos, tais como aquisição 
                        de recursos didáticos, alocação de ambientes, contratação de docentes, bem como a 
                        falta de tempo hábil para o preenchimento da vaga por outro Cliente. Desta forma, 
                        as partes concordam que:</p>
 
                        <p>§ 1º - Até 7 dias antes do início da programação será restituído o valor total pago. 
                        Para tanto, o Cliente deverá devolver a 1ª via do Recibo, na COTI INFORMATICA.</p>

                        <p>§ 2º - Do 6º dia até o 1º dia antes do início da programação será descontada a 
                        taxa administrativa de 10% (dez por cento) sobre o valor da programação. </p>

                        <p>§ 3º - Após o início da programação serão cobradas todas as atividades realizadas 
                        até a data do cancelamento da inscrição, bem como a taxa administrativa de 25% 
                        (vinte e cinco por cento) sobre o valor do restante da programação.</p>
 
                        <p>§ 4º - O cancelamento se dará nas seguintes situações:</p>

                        <p>
                        <ol type='a'>
                        <li> por solicitação do cliente, através de documento escrito, entregue na COTI INFORMATICA;</li>
                        </ol>
                        </p>

                        <p>§ 5º - Caso a programação seja realizada entre ausentes, será observado o disposto no 
                        art. 49 do Código de Defesa do Consumidor (Lei 8.078, de 11.09.90).</p>

                        <p><b>Cláusula 7ª </b>- As mudanças de horários ou dias da semana, dentro de uma mesma 
                        programação ou em programações diversas, será realizada desde que haja disponibilidade 
                        de vagas no turno solicitado, mediante as seguintes condições:</p>

                        <p>§ 1º - Com até 7 dias de antecedência à data de início da programação ou do módulo 
                        que compõe a mesma, a transferência será feita sem ônus para o Cliente.</p>

                        <p>§ 2º - Nos demais casos a transferência estará sujeita a avaliação da COTI INFORMATICA, 
                        bem como ao pagamento de taxa de transferência (se pertinente) cujo valor encontra-se 
                        na mesma.</p>

                        <p>§ 3º - Para efetuar a transferência, o Cliente deverá manifestar sua intenção por 
                        escrito e entregá-la na COTI INFORMATICA.</p>

                        <p><b>Cláusula 8ª </b>– Quaisquer declarações, atestados, certificados  ou segunda via de documentos, 
                        serão cobrados no ato de sua solicitação.</p>
                        
                        <p>§ Único - As tabelas de preço de serviços, produtos e taxas estão à disposição para 
                        consulta na COTI INFORMATICA.</p>


                        <p><b>Cláusula 9ª </b>- O limite legal de faltas é de no máximo 25% (vinte e cinco 
                        por cento) da duração da programação, salvo os casos previstos no Decreto Lei 1044/69, 
                        Lei Federal 6202/75, Lei Federal 4375/64 e parecer CFE nº 47/76 (por exemplo, 
                        gestantes, serviço militar, doenças infecto-contagiosas). De acordo com a legislação 
                        relacionada anteriormente, as faltas serão justificadas para fins de aprovação, mas não 
                        existem reposições.</p>


                        <p><b>Cláusula 10ª </b>- Para obter a certificação ao final da programação, o Cliente 
                        deverá ter participado de no mínimo 75% (setenta e cinco por cento) das atividades 
                        propostas e ter sido aprovado por processo de avaliação.</p>

                        <p><b>Cláusula 11ª </b>- O presente contrato vigorará pelo tempo de duração da 
                        programação, constituindo motivos para sua rescisão:</p>

                        <p>§ 1º - Superveniência de caso fortuito ou força maior, nos termos da legislação 
                        civil; e</p>

                        <p>§ 2º - Inadimplência do Cliente.</p>

                        <p><b>Cláusula 12ª </b>- Qualquer reclamação que o Cliente queira apresentar a 
                        COTI INFORMATICA, relacionada à atividade, objeto deste contrato, só será aceita 
                        se formulada por escrito, mediante correspondência firmada pelo Cliente e protocolada 
                        junto à COTI INFORMATICA onde a atividade tenha sido ou estiver sendo prestada.</p>

                        <p><b>Cláusula 13ª </b>- Fica eleito o Foro da Comarca da Capital do Estado do 
                        Rio de Janeiro, para a solução de dúvidas ou litígios porventura decorrentes deste 
                        contrato, com expressa renúncia de qualquer outro, por mais privilegiado que seja.</p>

                        <p>E, por estarem as partes justas e contratadas, assinam o presente em duas vias, 
                        de igual teor, na presença das testemunhas abaixo nomeadas</p>

                        <p>Rio de Janeiro, " . $util->mesExtenso() . ".</p>

                        <p>
                        <br />
                        _______________________________________________________________<br />
                          Cliente:  ". (($listaAluno["responsavelaluno"] != "")?$listaAluno["responsavelaluno"]:$listaAluno["nomealuno"])."</p>

 
                        <p>
                        <img src='http://villafacil.com/rodrigoimagem/imagem/assfernanda.jpg' />
                        <br />_______________________________________________________________<br />
                        Prestador: COTI SERVIÇOS DE INFORMATICA</p>
 
                         <p>
                         <img src='http://villafacil.com/rodrigoimagem/imagem/assbelem.jpg' />
                         <br />_______________________________________________________________<br />
                         Testemunha 1: EDSON BELEM DE SOUZA JUNIOR</p>

                         <p>
                         <br /><br /><br />_______________________________________________________________<br />
                        Testemunha 2: ".$nome."</p>
                    ";
                    
                    break;
                
                case 'CHEQUE' :
                    
                    $contrato .= "
                        <p>A COTI INFORMATICA no Estado do Rio de Janeiro, com sede na Av. Rio Branco 185, 
                        Sala 307 - Centro - Rio de Janeiro - RJ - CEP 20040-007 , a seguir designado 
                        abreviadamente 'COTI INFORMATICA', neste ato representado pelo Sr.(a) Fernanda 
                        Nunes Lopes de Souza Diretora Identidade nº 10356974-5  emitida pelo DETRAN RJ e o(a) 
                        Sr.(a) ".(($listaAluno["responsavelaluno"] != "")?$listaAluno["responsavelaluno"]:$listaAluno["nomealuno"]).", CPF: ".$listaAluno["cpf"]." inscrito(a) na Identidade: 
                        ".$listaAluno["rg"]." 
                        ".(($listaAluno["responsavelaluno"] != "")?", responsável pelo aluno " . $listaAluno["nomealuno"].", ":"")."
                        residente e domiciliado(a) ".$listaAluno["logradouro"]." " . $listaAluno["numero"]. "
                        , " . $listaAluno["complemento"]." Bairro ".$listaAluno["bairro"]." ".$listaAluno["cidade"]." - " .$listaAluno["estado"]."
                        CEP: ".$listaAluno["cep"].", seguir designado(a) simplesmente 'Cliente', tem entre si justo e acordado 
                        o presente Contrato de Prestação de Serviços Educacionais, que se regerá pelas cláusulas 
                        e condições seguintes:</p>

                        <p><b>Cláusula 1ª </b>– A COTI INFORMATICA obriga-se a prestar serviços educacionais 
                        para o Cliente, inscrito na programação ".$listaAluno["curso"]." – Duração Total: 
                        " .$listaAluno["cargahoraria"]. " horas, que tem seu início previsto para ".$dataExtenso.", ocorrendo às aulas 
                        sempre as ".$util->definirSemana($listaAluno["frequencia"])." de " .$listaAluno["horario"]. ", em conformidade 
                        com a legislação pertinente e de acordo com as normas do Regimento da COTI INFORMATICA, 
                        o qual o Cliente declara conhecer e que se encontra na Unidade.</p>

                        <p><b>Cláusula 2ª </b>– É atribuição e responsabilidade da COTI INFORMATICA o 
                        planejamento e a prestação dos serviços educacionais, a definição das formas, datas e 
                        locais para realização e avaliação da aprendizagem, a designação de docentes qualificados, 
                        a orientação didático-pedagógica e educacional, bem como outros procedimentos necessários 
                        ao bom desenvolvimento dos programas, nos termos das normas e legislação vigente.</p>

                        <p>§ Único – O Cliente reconhece que é prerrogativa da Unidade utilizar outros espaços 
                        físicos para a prestação dos serviços aqui descritos.</p>

                        <p><b>Cláusula 3ª </b>– O Cliente pagará a COTI INFORMATICA, pelas obrigações assumidas 
                        no presente contrato, a importância total de R$ <span class='valorcontrato'></span> <input type='text' name='valor' class='campovalor' value='".$listaAluno["total"]."' />. 
                        Nas formas disponibilizadas pela COTI INFORMATICA, sendo: <span class='dadoscontrato'></span> <input type='text' name='dados' class='campodados' />.</p>

                        <p><b>Cláusula 4ª </b>– Caso algum cheque do Cliente deixe de compensar nas datas 
                        previstas, o mesmo deve comparecer a COTI INFORMATICA para resgatá-los em um período 
                        de 20 dias corridos, posterior a essa data o cheque será reapresentado ao banco.  
                        Caso o cheque volte pela segunda vez a COTI INFORMATICA enviará o débito para empresa 
                        de cobrança, além de poder promover o protesto da dívida, sem prejuízo da inclusão do 
                        nome do Cliente nos cadastros restritivos de crédito, de acordo com a legislação vigente, 
                        bem como poderá promover a cobrança judicial da dívida.</p>


                        <p><b>Cláusula 5ª </b>– Quando o número mínimo de Clientes desejado não for atingido, 
                        a COTI INFORMATICA, mediante aviso prévio, poderá cancelar a programação ou marcar 
                        nova data de início da mesma, ficando a critério do Cliente a permanência ou não na 
                        nova data estipulada. Esta cláusula é válida inclusive para os módulos subseqüentes 
                        ao primeiro, caso existam.</p>

                        <p>§ Único - Caso o Cliente opte por não permanecer na programação, os valores pagos 
                        serão devolvidos.</p>

                        <p><b>Cláusula 6ª </b>– Ao efetuar a inscrição em uma determinada programação ou em 
                        módulos que a compõem, o Cliente motiva uma série de custos, tais como aquisição de 
                        recursos didáticos, alocação de ambientes, contratação de docentes, bem como a falta 
                        de tempo hábil para o preenchimento da vaga por outro Cliente. Desta forma, as partes 
                        concordam que:</p>
 
                        <p>§ 1º - Até 7 dias antes do início da programação será restituído o valor total pago. 
                        Para tanto, o Cliente deverá devolver a 1ª via do Recibo, na COTI INFORMATICA.</p>

                        <p>§ 2º - Do 6º dia até o 1º dia antes do início da programação será descontada a taxa 
                        administrativa de 10% (dez por cento) sobre o valor da programação. </p>

                        <p>§ 3º - Após o início da programação serão cobradas todas as atividades realizadas 
                        até a data do cancelamento da inscrição, bem como a taxa administrativa de 25% (vinte e 
                        cinco por cento) sobre o valor do restante da programação.</p>
 
                        <p>§ 4º - O cancelamento se dará nas seguintes situações:</p>

                        <p>
                        <ol type='a'>
                            <li> por solicitação do cliente, através de documento escrito, entregue na 
                            COTI INFORMATICA;</li>
                        </ol></p>

                        <p>§ 5º - Caso a programação seja realizada entre ausentes, será observado o disposto 
                        no art. 49 do Código de Defesa do Consumidor (Lei 8.078, de 11.09.90).</p>

                        <p>Cláusula 7ª - As mudanças de horários ou dias da semana, dentro de uma mesma 
                        programação ou em programações diversas, será realizada desde que haja disponibilidade 
                        de vagas no turno solicitado, mediante as seguintes condições:</p>

                        <p>§ 1º - Com até 7 dias de antecedência à data de início da programação ou do módulo 
                        que compõe a mesma, a transferência será feita sem ônus para o Cliente.</p>

                        <p>§ 2º - Nos demais casos a transferência estará sujeita a avaliação da COTI INFORMATICA,
                        bem como ao pagamento de taxa de transferência (se pertinente) cujo valor encontra-se 
                        na mesma.</p>

                        <p>§ 3º - Para efetuar a transferência, o Cliente deverá manifestar sua intenção por 
                        escrito e entregá-la na COTI INFORMATICA.</p>

                        <p><b>Cláusula 8ª </b>– Quaisquer declarações, atestados, certificados  ou segunda via de documentos, 
                        serão cobrados no ato de sua solicitação.</p>

                        <p>§ Único - As tabelas de preço de serviços, produtos e taxas estão à disposição para 
                        consulta na COTI INFORMATICA.</p>

                        <p><b>Cláusula 9ª </b>- O limite legal de faltas é de no máximo 25% (vinte e cinco 
                        por cento) da duração da programação, salvo os casos previstos no Decreto Lei 1044/69, 
                        Lei Federal 6202/75, Lei Federal 4375/64 e parecer CFE nº 47/76 (por exemplo, 
                        gestantes, serviço militar, doenças infecto-contagiosas). De acordo com a legislação 
                        relacionada anteriormente, as faltas serão justificadas para fins de aprovação, mas 
                        não existem reposições.</p>

                        <p><b>Cláusula 10ª </b>- Para obter a certificação ao final da programação, o Cliente 
                        deverá ter participado de no mínimo 75% (setenta e cinco por cento) das atividades 
                        propostas e ter sido aprovado por processo de avaliação.</p>

                        <p><b>Cláusula 11ª </b>- O presente contrato vigorará pelo tempo de duração da 
                        programação, constituindo motivos para sua rescisão:</p>

                        <p>§ 1º - Superveniência de caso fortuito ou força maior, nos termos da legislação 
                        civil; e</p>

                        <p>§ 2º - Inadimplência do Cliente.</p>

                        <p><b>Cláusula 12ª </b>- Qualquer reclamação que o Cliente queira apresentar a COTI 
                        INFORMATICA, relacionada à atividade, objeto deste contrato, só será aceita se formulada 
                        por escrito, mediante correspondência firmada pelo Cliente e protocolada junto à 
                        COTI INFORMATICA onde a atividade tenha sido ou estiver sendo prestada.</p>

                        <p><b>Cláusula 13ª </b>- Fica eleito o Foro da Comarca da Capital do Estado do 
                        Rio de Janeiro, para a solução de dúvidas ou litígios porventura decorrentes deste 
                        contrato, com expressa renúncia de qualquer outro, por mais privilegiado que seja.</p>

                        <p>E, por estarem as partes justas e contratadas, assinam o presente em duas vias, 
                        de igual teor, na presença das testemunhas abaixo nomeadas</p>

                        <p>Rio de Janeiro, " . $util->mesExtenso() . ".</p>

                        <p>
                        <br /><br /><br />
                        _______________________________________________________________<br />
                          Cliente:  ". (($listaAluno["responsavelaluno"] != "")?$listaAluno["responsavelaluno"]:$listaAluno["nomealuno"]) ."</p>

 
                        <p>
                        <img src='http://villafacil.com/rodrigoimagem/imagem/assfernanda.jpg' />
                        <br />_______________________________________________________________<br />
                        Prestador: COTI SERVIÇOS DE INFORMATICA</p>
 
                         <p>
                         <img src='http://villafacil.com/rodrigoimagem/imagem/assbelem.jpg' />
                         <br />_______________________________________________________________<br />
                         Testemunha 1: EDSON BELEM DE SOUZA JUNIOR</p>

                         <p>
                         <br /><br /><br />_______________________________________________________________<br />
                        Testemunha 2: ".$nome."</p>
                    ";
                    
                    break;
                
                case 'DINHEIRO E BOLETO' :
                    
                    $contrato .= "
                        <p>A COTI INFORMATICA no Estado do Rio de Janeiro, com sede na Av. Rio Branco 185, 
                        Sala 307 - Centro - Rio de Janeiro - RJ - CEP 20040-007 , a seguir designado 
                        abreviadamente 'COTI INFORMATICA', neste ato representado pelo Sr.(a) Fernanda Nunes 
                        Lopes de Souza Diretora Identidade nº 10356974-5  emitida pelo DETRAN RJ e o(a) 
                        Sr.(a) " .(($listaAluno["responsavelaluno"] != "")?$listaAluno["responsavelaluno"]:$listaAluno["nomealuno"]).", Identidade: ".$listaAluno["rg"]." e 
                        CPF: ".$listaAluno["cpf"]." 
                        " .(($listaAluno["responsavelaluno"] != "")?", responsável pelo aluno " . $listaAluno["nomealuno"].", ":"")."
                        residente e domiciliado(a)  ".$listaAluno["logradouro"]." " . $listaAluno["numero"]." " .$listaAluno["complemento"] ." - 
                        Bairro: ".$listaAluno["bairro"]." ".$listaAluno["cidade"]." - " .$listaAluno["estado"]." - 
                        CEP: ".$listaAluno["cep"].", seguir designado(a) simplesmente 'Cliente', 
                        tem entre si justo e acordado o presente Contrato de Prestação de Serviços Educacionais, 
                        que se regerá pelas cláusulas e condições seguintes:</p>
                        
                        <p><b>Cláusula 1ª </b>– A COTI INFORMATICA obriga-se a prestar serviços educacionais 
                        para o Cliente, inscrito na programação ".$listaAluno["curso"]."  – Duração Total: ".$listaAluno["cargahoraria"]." Horas, 
                        que tem seu início previsto para ".$dataExtenso.",  ocorrendo às aulas sempre as 
                        ".$util->definirSemana($listaAluno["frequencia"])." das ".$listaAluno["horario"].", em conformidade com a legislação pertinente e 
                        de acordo com as normas do Regimento da COTI INFORMATICA, o qual o Cliente declara 
                        conhecer e que se encontra na Unidade.</p>

                        <p><b>Cláusula 2ª </b>– É atribuição e responsabilidade da COTI INFORMATICA o planejamento 
                        e a prestação dos serviços educacionais, a definição das formas, datas e locais para 
                        realização e avaliação da aprendizagem, a designação de docentes qualificados, a 
                        orientação didático-pedagógica e educacional, bem como outros procedimentos 
                        necessários ao bom desenvolvimento dos programas, nos termos das normas e legislação 
                        vigente.</p>

                        <p><b>§ Único – </b>O Cliente reconhece que é prerrogativa da Unidade utilizar outros 
                        espaços físicos para a prestação dos serviços aqui descritos.</p>

                        <p><b>Cláusula 3ª <b>– O Cliente pagará a COTI INFORMATICA, pelas obrigações assumidas 
                        no presente contrato, a importância total de R$ <span class='valorcontrato'></span> <input type='text' name='valor' class='campovalor' value='".$listaAluno["total"]."' />. Nas formas 
                        disponibilizadas pela COTI INFORMATICA, sendo: <span class='dadoscontrato'></span> <input type='text' name='dados' class='campodados' />.</p>

                        <p><b>Cláusula 4ª </b>– Caso o Cliente opte pelo parcelamento, poderá fazê-lo exclusivamente nas modalidades aceitas pela COTI INFORMATICA. Sendo oferecida e escolhida a forma de pagamento através de boletos bancários, onde os mesmos serão pagos observando-se os prazos e condições acordadas abaixo:</p>

                        <p><b>§ 1º - Até a data do vencimento, os pagamentos poderão ser feitos em qualquer agência bancária, nas casas lotéricas 
                        ou via internet.</b></p>

                        <p><b>§ 2º - Até 30 (trinta) dias após o vencimento, os pagamentos poderão ser feitos somente nas agências do Banco ITAÚ, com os acréscimos previstos no boleto, Multa de 2%, mais juros de 0,33% ao dia.</b></p>

                        <p><b>§ 3º - Ultrapassados 30 (trinta) dias do vencimento, os pagamentos somente poderão ser feitos solicitando um novo boleto no site do itaú.</b></p>

                        <p><b>Cláusula 5ª – Quando o número mínimo de Clientes desejado não for atingido, 
                        a COTI INFORMATICA, mediante aviso prévio, poderá cancelar a programação ou marcar 
                        nova data de início da mesma, ficando a critério do Cliente a permanência ou não na 
                        nova data estipulada. Esta cláusula é válida inclusive para os módulos subseqüentes 
                        ao primeiro, caso existam.</p>

                        <p>§ Único - Caso o Cliente opte por não permanecer na programação, os valores pagos 
                        serão devolvidos.</p>

                        <p><b>Cláusula 6ª </b>– Ao efetuar a inscrição em uma determinada programação ou em módulos 
                        que a compõem, o Cliente motiva uma série de custos, tais como aquisição de recursos 
                        didáticos, alocação de ambientes, contratação de docentes, bem como a falta de tempo 
                        hábil para o preenchimento da vaga por outro Cliente. Desta forma, as partes concordam 
                        que:</p>

                        <p>§ 1º - Até 7 dias antes do início da programação será restituído o valor total pago. 
                        Para tanto, o Cliente deverá devolver a 1ª via do Recibo, na COTI INFORMATICA.</p>

                        <p>§ 2º - Do 6º dia até o 1º dia antes do início da programação será descontada a taxa 
                        administrativa de 10% (dez por cento) sobre o valor da programação. </p>

                        <p>§ 3º - Após o início da programação serão cobradas todas as atividades realizadas até 
                        a data do cancelamento da inscrição, bem como a taxa administrativa de 25% (vinte e cinco 
                        por cento) sobre o valor do restante da programação.</p>

                        <p>§ 4º - O cancelamento se dará nas seguintes situações:</p>

                        <p>
                        <ol type='a'>
                        <li> por solicitação do cliente, através de documento escrito, entregue na COTI 
                        INFORMATICA;
                        </ol></p>

                        <p>§ 5º - Caso a programação seja realizada entre ausentes, será observado o 
                        disposto no art. 49 do Código de Defesa do Consumidor (Lei 8.078, de 11.09.90).</p>

                        <p><b>Cláusula 7ª </b>- As mudanças de horários ou dias da semana, dentro de uma mesma 
                        programação ou em programações diversas, será realizada desde que haja disponibilidade 
                        de vagas no turno solicitado, mediante as seguintes condições:</p>

                        <p>§ 1º - Com até 7 dias de antecedência à data de início da programação ou do módulo 
                        que compõe a mesma, a transferência será feita sem ônus para o Cliente.</p>

                        <p>§ 2º - Nos demais casos a transferência estará sujeita a avaliação da COTI INFORMATICA, 
                        bem como ao pagamento de taxa de transferência (se pertinente) cujo valor encontra-se na 
                        mesma.</p>

                        <p>§ 3º - Para efetuar a transferência, o Cliente deverá manifestar sua intenção por 
                        escrito e entregá-la na COTI INFORMATICA.</p>

                        <p><b>Cláusula 8ª </b>– Quaisquer declarações, atestados, certificados  ou segunda via de documentos, 
                        serão cobrados no ato de sua solicitação.</p>

                        <p>§ Único - As tabelas de preço de serviços, produtos e taxas estão à disposição para 
                        consulta na COTI INFORMATICA.</p>

                        <p><b>Cláusula 9ª </b>- O limite legal de faltas é de no máximo 25% (vinte e cinco por 
                        cento) da duração da programação, salvo os casos previstos no Decreto Lei 1044/69, Lei 
                        Federal 6202/75, Lei Federal 4375/64 e parecer CFE nº 47/76 (por exemplo, gestantes, 
                        serviço militar, doenças infecto-contagiosas). De acordo com a legislação relacionada 
                        anteriormente, as faltas serão justificadas para fins de aprovação, mas não existem 
                        reposições.</p>

                        <p><b>Cláusula 10ª </b>- Para obter a certificação ao final da programação, o Cliente 
                        deverá ter participado de no mínimo 75% (setenta e cinco por cento) das atividades 
                        propostas e ter sido aprovado por processo de avaliação.</p>

                        <p><b>Cláusula 11ª </b>- O presente contrato vigorará pelo tempo de duração da 
                        programação, constituindo motivos para sua rescisão:</p>

                        <p>§ 1º - Superveniência de caso fortuito ou força maior, nos termos da legislação 
                        civil; e</p>

                        <p>§ 2º - Inadimplência do Cliente.</p>

                        <p><b>Cláusula 12ª </b>- Qualquer reclamação que o Cliente queira apresentar a 
                        COTI INFORMATICA, relacionada à atividade, objeto deste contrato, só será aceita se 
                        formulada por escrito, mediante correspondência firmada pelo Cliente e protocolada 
                        junto à COTI INFORMATICA onde a atividade tenha sido ou estiver sendo prestada.</p>

                        <p><b>Cláusula 13ª </b>- Fica eleito o Foro da Comarca da Capital do Estado do Rio 
                        de Janeiro, para a solução de dúvidas ou litígios porventura decorrentes deste 
                        contrato, com expressa renúncia de qualquer outro, por mais privilegiado que seja.</p>

                        <p>E, por estarem as partes justas e contratadas, assinam o presente em duas vias, 
                        de igual teor, na presença das testemunhas abaixo nomeadas</p>

                        <p>Rio de Janeiro, " . $util->mesExtenso() . ".</p>

                        <p>
                        <br /><br /><br />
                        _______________________________________________________________<br />
                          Cliente:  ".(($listaAluno["responsavelaluno"] != "")?$listaAluno["responsavelaluno"]:$listaAluno["nomealuno"])."</p>

 
                        <p>
                        <img src='http://villafacil.com/rodrigoimagem/imagem/assfernanda.jpg' />
                        <br />_______________________________________________________________<br />
                        Prestador: COTI SERVIÇOS DE INFORMATICA</p>
 
                         <p>
                         <img src='http://villafacil.com/rodrigoimagem/imagem/assbelem.jpg' />
                         <br />_______________________________________________________________<br />
                         Testemunha 1: EDSON BELEM DE SOUZA JUNIOR</p>

                         <p>
                         <br /><br /><br />_______________________________________________________________<br />
                        Testemunha 2: ".$nome."</p>

";
                    
                    break;
                
                default : 
                    
                    break;
            }
            $contrato .= "</div>";
            echo $contrato;
            echo "<hr />";
            echo "<div id='refazer' style='margin: 8px;text-align: justify;'>";
            echo "<h2>Regras para refazer os cursos:</h2>
            <p>A Coti Informática entende que o aluno que já pagou por um curso não deve pagar 
            novamente caso queira refazê-lo. Portando em qualquer um de nossos cursos o aluno tem 
            o direito de se rematricular para reforçar o que foi aprendido.</p>
            
            <p>A Coti Informática disponibiliza 2 computadores por turma para alunos que queiram refazer o curso. 
            Caso a carga horária do curso seja alaterada o Aluno pagará a diferença.</p>
            
            <p><b>No entanto há algumas regras para refazer os cursos, veja quais são:</b>
            <ul>
                <li>O aluno só poderá se rematricular no mesmo curso que já fez.</li>
                <li>O aluno tem 1 ano a partir do início da turma para refazer seu curso.</li>
                <li>O aluno deverá ter no mínimo 85% de presença em sua primeira matrícula.</li>
                <li>Já rematriculado o aluno não poderá faltar a primeira aula do curso.</li>
                <li>Não poderá ter frequência inferior a 85% ou duas faltas consecutivas.</li>
                <li>Caso o curso sofra alteração de carga horária o aluno pagará apenas a diferença</li>
                <li>O aluno somente poderá refazer o curso se estiver em dia com os pagamentos de seu(s) curso(s) junto a COTI Informática.</li>
            </ul>
                
            <h2>Regras para transferencia de turma:</h2>
            
            <p>Ao efetuar a inscrição em uma determinada programação ou em módulos que a compõem, 
            o Cliente motiva uma série de custos, tais como aquisição de recursos didáticos, alocação de 
            ambientes, contratação de docentes, bem como a falta de tempo hábil para o preenchimento da vaga 
            por outro Cliente. Desta forma, as partes concordam que para as mudanças de horários ou dias da semana, 
            dentro de uma mesma programação ou em programações diversas, será realizada desde que haja disponibilidade 
            de vagas no turno solicitado, mediante as seguintes condições:</p>

            <p>
            1º - Com até 7 dias de antecedência à data de início da programação ou do módulo que 
            compõe a mesma, a transferência será feita sem ônus para o Cliente.<br />
            2º - Nos demais casos a transferência estará sujeita a avaliação da COTI INFORMATICA, 
            bem como ao pagamento de taxa de transferência cujo valor encontra-se abaixo:<br />
            <blockout>- 50% do valor do curso se ainda não tiver atingindo 50% da carga horária em sua turma e 
            pedir tranferência para uma turma que ainda irá começar.</blockout><br />
            <blockout>- R$ 160,00 se se transferir para uma mesma turma em seu nivel que tenha vaga.</blockout><br />
            3º - Para efetuar a transferência, o Cliente deverá manifestar sua intenção por escrito e entregá-la 
            na COTI INFORMATICA.<br /><br /><br />
            
            Data: ".date('d')."/".date('m')."/".date('Y')."  Assinatura do Atendente: _______________________________
            <br /><br />
            <p>Av. Rio Branco 185 – 9º Andar – Sala 307 – Centro – Rio de Janeiro – RJ<br />
            Tel: 2262-9043 / 78152807  - e-mail: contato@cotiinformatica.com.br  / http://www.cotiinformatica.com.br </p>
";
            echo "<br />";
            echo "<h2>Regras para refazer os cursos:</h2>
            <p>A Coti Informática entende que o aluno que já pagou por um curso não deve pagar 
            novamente caso queira refazê-lo. Portando em qualquer um de nossos cursos o aluno tem 
            o direito de se rematricular para reforçar o que foi aprendido.</p>
            
            <p>A Coti Informática disponibiliza 2 computadores por turma para alunos que queiram refazer o curso. 
            Caso a carga horária do curso seja alaterada o Aluno pagará a diferença.</p>
            
            <p><b>No entanto há algumas regras para refazer os cursos, veja quais são:</b>
            <ul>
                <li>O aluno só poderá se rematricular no mesmo curso que já fez.</li>
                <li>O aluno tem 1 ano a partir do início da turma para refazer seu curso.</li>
                <li>O aluno deverá ter no mínimo 85% de presença em sua primeira matrícula.</li>
                <li>Já rematriculado o aluno não poderá faltar a primeira aula do curso.</li>
                <li>Não poderá ter frequência inferior a 85% ou duas faltas consecutivas.</li>
                <li>Caso o curso sofra alteração de carga horária o aluno pagará apenas a diferença</li>
                <li>O aluno somente poderá refazer o curso se estiver em dia com os pagamentos de seu(s) curso(s) junto a COTI Informática.</li>
            </ul>
                
            <h2>Regras para transferencia de turma:</h2>
            
            <p>Ao efetuar a inscrição em uma determinada programação ou em módulos que a compõem, 
            o Cliente motiva uma série de custos, tais como aquisição de recursos didáticos, alocação de 
            ambientes, contratação de docentes, bem como a falta de tempo hábil para o preenchimento da vaga 
            por outro Cliente. Desta forma, as partes concordam que para as mudanças de horários ou dias da semana, 
            dentro de uma mesma programação ou em programações diversas, será realizada desde que haja disponibilidade 
            de vagas no turno solicitado, mediante as seguintes condições:</p>

            <p>
            1º - Com até 7 dias de antecedência à data de início da programação ou do módulo que 
            compõe a mesma, a transferência será feita sem ônus para o Cliente.<br />
            2º - Nos demais casos a transferência estará sujeita a avaliação da COTI INFORMATICA, 
            bem como ao pagamento de taxa de transferência cujo valor encontra-se abaixo:<br />
            <blockout>- 50% do valor do curso se ainda não tiver atingindo 50% da carga horária em sua turma e 
            pedir tranferência para uma turma que ainda irá começar.</blockout><br />
            <blockout>- R$ 160,00 se se transferir para uma mesma turma em seu nivel que tenha vaga.</blockout><br />
            3º - Para efetuar a transferência, o Cliente deverá manifestar sua intenção por escrito e entregá-la 
            na COTI INFORMATICA.<br /><br /><br />
            
            Data: ".date('d')."/".date('m')."/".date('Y')."  Assinatura do Aluno: _______________________________
            <br />
            <p>Av. Rio Branco 185 – 9º Andar – Sala 307 – Centro – Rio de Janeiro – RJ<br />
            Tel: 2262-9043 / 78152807  - e-mail: contato@cotiinformatica.com.br  / http://www.cotiinformatica.com.br </p>
";
            echo "</div>";
            $this->view->contrato = $contrato;
        }
        
    }
    
    public function pagamentoAction()
    {
        $idaluno = $this->getRequest()->getParam("param1", 0);
        $idturma = $this->getRequest()->getParam("param2", 0);
        $alunoTurmaDao = new Application_Model_AlunosTurmas();
        
        $alunoTurma = $alunoTurmaDao->buscarChaves(array('aluno_idaluno' => $idaluno,
            'turma_idturma' => $idturma));
        if(!is_numeric($idaluno) || !is_numeric($idturma) || $idaluno == 0 || $idturma == 0)
        {
            $this->indexAction();
            $this->view->resp = "Parcelamento não pode ser efetuado!";
            $this->render('index');
        }
        elseif($alunoTurma == null)
        {
            $this->indexAction();
            $this->view->resp = "Aluno não vinculado a esta turma!";
            $this->render('index');
        }
        else 
        {
            $pagamentoDao = new Application_Model_Pagamentos();
            $boletos = $pagamentoDao->verificarParcelamento(array('idaluno' => $idaluno, 'idturma' => $idturma));
            if(count($boletos) > 0){
                $this->view->isBoleto = 1;
                $this->view->idpagamento = $boletos[0]["idpagamento"];
                $this->view->dadosParcelamento = $boletos;
            }
            if($this->getRequest()->isPost())
            {
                $parcelas = $this->getRequest()->getParam("parcela", 0);
                $valortotal = $this->getRequest()->getParam("valortotal", 0);
                $obs = $this->getRequest()->getParam("obs", "");
                
                if(is_numeric($parcelas) && is_numeric($valortotal))
                {
                    $data = array(
                        'alunoturma_aluno_idaluno' => $idaluno,
                        'alunoturma_turma_idturma' => $idturma,
                        'parcelas' => $parcelas,
                        'data' => date('Y-m-d'),
                        'hora' => date('H:m:i'),
                        'obs' => $obs,
                        'valortotal' => $valortotal
                    );
                    
                    $idpagamento = $pagamentoDao->insert($data);
                    if($idpagamento > 0)
                    {
                        $parcelamentoDao = new Application_Model_Parcelamentos();
                        $valorParcelado = $valortotal / $parcelas;
                        $dataParcelamento = array(
                            'valorparcela' => $valorParcelado,
                            'pagamento_idpagamento' => $idpagamento["idpagamento"]
                        );
                        $mes = date('m');
                        $dia = date('d');
                        $ano = date('Y');
                        for($i = 1; $i <= $parcelas; $i++)
                        {
                            /*
                             * Gerar os pagamentos das parcelas e imprimir de maneira editavel na 
                             * .phtml
                             * Gerar os boletos
                             */
                            $data = mktime(0, 0, 0, $mes + $i - 1, 7, $ano);
                            $dataParcelamento["datacobranca"] = date('Y-m-d', $data);
                            $dataParcelamento["nparcela"] = $i;
                            $parcelamentoDao->insert($dataParcelamento);
                        }
                        
                        $this->view->dadosParcelamento = $parcelamentoDao->listarPorPagamento($idpagamento["idpagamento"]);
                        $this->view->idpagamento = $idpagamento["idpagamento"];
                    }
                }
                else
                {
                    $this->view->resp = "Informe numeros nos campos parcelas e valor total<br />";
                }
                
                $this->view->parcela = $parcelas;
                $this->view->obs = $obs;
            }
            
            $this->view->total = isset($valortotal)?$valortotal:$alunoTurma["total"];
        }
    }
    
   public function pagamentoalunoAction()
   {
       $id = $this->getRequest()->getParam("params", "");
       if($id == "")
       {
           $this->indexAction();
           $this->render("index");
       }
       else
       {
           $pagamentoDao = new Application_Model_Pagamentos();
           $listaPagamento = $pagamentoDao->listaPagamentoAluno(array('idaluno' => $id));
           $this->view->dadosParcelamento = $listaPagamento;
       }
   }
   
   public function buscarpagamentoAction()
   {
       $this->_helper->layout->disableLayout();
       $this->_helper->viewRenderer->setNoRender(true);
       $util = new Util_Utilitarios();
       
       $id = $this->getRequest()->getParam("idparcelamento", "0;0");
       $ids = explode(";", $id);
       
       $nparcela = $ids[0];
       $idpagamento = $ids[1];
       
       $parcelamentoDao = new Application_Model_Parcelamentos();
       $dadosPagamento = $parcelamentoDao->buscarPorChave($nparcela, $idpagamento);
       if(count($dadosPagamento) > 0)
       {
           echo $dadosPagamento["valorparcela"].";"; //0
           echo $dadosPagamento["valorpago"].";"; //1
           echo $util->converterDataSaida($dadosPagamento["datacobranca"]).";"; //2
           echo $dadosPagamento["status"].";"; //3
           echo $nparcela.";";//4
           echo $idpagamento.";";//5
       }
       
   }
   
   public function realizarpagamentoAction()
   {
       $this->_helper->viewRenderer->setNoRender(true);
       
       $nparcela = $this->getRequest()->getParam("nparcela", 0);
       $idpagamento = $this->getRequest()->getParam("idpagamento", 0);
       $valorparcela = $this->getRequest()->getParam("valorparcela", 0);
       $valorpago = $this->getRequest()->getParam("valorpago", 0);
       $datacobranca = $this->getRequest()->getParam("datacobranca", "");
       $status = $this->getRequest()->getParam("status", 0);
       $obs = $this->getRequest()->getParam("obs", "");
       
       $parcelamentoDao = new Application_Model_Parcelamentos();
       
       if($this->getRequest()->isPost())
       {
           $dados = array(
               'valorparcela' => $valorparcela,
               'valorpago' => $valorpago,
               'datacobranca' => $datacobranca,
               'status' => $status,
               'obs' => $obs
           );
           
           if($valorparcela == "")
               unset($dados["valorparcela"]);
           
           if($datacobranca == "")
               unset($dados["datacobranca"]);
           else
           {
               $util = new Util_Utilitarios();
               $dados['datacobranca'] = $util->converterDataEntrada($datacobranca);
           }
               
           if(is_numeric($valorpago))
           {
                if($parcelamentoDao->update($dados, 'nparcela = ' . $nparcela . " AND pagamento_idpagamento = " . $idpagamento))
                {
                    $this->view->resp = "Pagamento realizado!";
                }
                else
                {
                    $this->view->resp = "Pagamento não pode ser realizado!";
                }
           }
       }
       
       $this->pagamentoalunoAction();
       $this->render("pagamentoaluno");
   }
   
   public function editarpagamentoAction()
   {
       $this->_helper->viewRenderer->setNoRender(true);
       
       if($this->getRequest()->isGet())
       {
           $nparcela = $this->getRequest()->getParam("nparcela", 0);
           $idpagamento = $this->getRequest()->getParam("idpagamento", 0);
           
           $cobranca = $this->getRequest()->getParam("cobranca","");
           if($cobranca != "")
           {
               $util = new Util_Utilitarios();
               $dados = array(
                   'datacobranca' => $util->converterDataEntrada($cobranca)
               );
               $parcelamentoDao = new Application_Model_Parcelamentos();
               
               if($parcelamentoDao->update($dados, 'nparcela = ' . $nparcela . " AND pagamento_idpagamento = " . $idpagamento))
                       $this->view->resp = "Alterado com sucesso!";
               else
                   $this->view->resp = "Não pode alterar!";
           }
           
       }
       $this->pagamentoAction();
       $this->render("pagamento");
   }
   
   public function detalhesturmaAction()
   {
       $alunoTurmaDao = new Application_Model_AlunosTurmas();
       $idaluno = $this->getRequest()->getParam("params", "");
       $idaturma = $this->getRequest()->getParam("paramsturma", "");
       
       if(!is_numeric($idaturma) || !is_numeric($idaluno))
       {
           $this->turmasAction();
           $this->render("turmas");
       }
       else
       {
           $aluno = $alunoTurmaDao->buscarAluno(array("idturma" => $idaturma, "idaluno" => $idaluno));
           if($aluno == null)
           {
               $this->turmasAction();
               $this->render("turmas");
           }
           else
           {
               if($this->getRequest()->isPost())
               {
                   $turmaDao = new Application_Model_Turmas();
                   $turma = $turmaDao->buscarPorId($idaturma);
                   
                   $reserva = $this->getRequest()->getParam("reserva", 0);
                   $statusAluno = $this->getRequest()->getParam("statusAluno", "");
                   $pendencia = $this->getRequest()->getParam("pendente", 0);
                   $data = array(
                       'obs' => $this->getRequest()->getParam("obs", ""),
                       'pendencia' => $pendencia
                   );
                   
                   
                       $pagamento = $this->getRequest()->getParam("pagamento", "");
                       $responsavel = $this->getRequest()->getParam("responsavel", "");
                       $valortotal = $this->getRequest()->getParam("valortotal", "");
                       
                       if(!is_numeric($valortotal) || $valortotal < 0)
                           $valortotal = 0;
                       $data["total"] = $valortotal;
                       $data["formapagamento"] = $pagamento;
                       $data["responsavel"] = $responsavel;
                       $data["reserva"] = 0;
                       $data["statusturma"] = $statusAluno;
                       
                       
                       if($reserva == 1){
                            //$turmaDao->update(array('vagas' => $turma["vagas"] - 1), 'idturma = ' . $idaturma) ;
                            $data["cadastro"] = date('Y-m-d H:i:s');
                       }else if($aluno["statusturma"] == "ATIVO" && $statusAluno == "TRANCADO" ){
                           $turmaDao->update(array('vagas' => $turma["vagas"] + 1), 'idturma = ' . $idaturma) ;
                       }else if($aluno["statusturma"] == "ATIVO" && $statusAluno == "CANCELADO" ){
                           $turmaDao->update(array('vagas' => $turma["vagas"] + 1), 'idturma = ' . $idaturma) ;
                       }else if(($aluno["statusturma"] == "TRANCADO" || $aluno["statusturma"] == "CANCELADO") 
                               &&  $statusAluno == "ATIVO"){
                           $data["cadastro"] = date('Y-m-d H:i:s');
                           $turmaDao->update(array('vagas' => $turma["vagas"] - 1), 'idturma = ' . $idaturma) ;
                       }
                   
                   $alunoTurmaDao->update($data, "turma_idturma = " . $idaturma . " AND aluno_idaluno = " . $idaluno);
                   $aluno = $alunoTurmaDao->buscarAluno(array("idturma" => $idaturma, "idaluno" => $idaluno));
                   $this->view->resp = "Dados da turma editado com sucesso!";
               }
               $this->view->aluno = $aluno;
           }
       }
   }
   
   public function relatoriopendenteAction()
   {
       error_reporting(!E_ALL);
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $pdf = new Zend_Pdf();
        
        $pdfPage = new Zend_Pdf_Page( Zend_Pdf_Page::SIZE_A4 );
        $row = $pdfPage->getWidth();/*595*/
        $alt = $pdfPage->getHeight();/*842*/
        
        $pdfImage = Zend_Pdf_Image::imageWithPath(APPLICATION_PATH . '/pdf/logo.png');
        $pdfPage->drawImage( $pdfImage, 10,  747, 419, 843 );
        $pdfPage->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 8);
        
        $alunoTurmaDao = new Application_Model_AlunosTurmas();
        $dados = $alunoTurmaDao->buscarPendentes();
        
        if(count($dados) > 0)
        {
            $i = 740;
            foreach($dados as $alunos)
            {
                unset($alunos["resumo"]);
                unset($alunos["ementa"]);
                
                $pdfPage->drawText("Aluno: ". $alunos["nomealuno"], 10, $i, 'UTF-8');
                $pdfPage->drawText("Curso: ". $alunos["curso"], 300, $i, 'UTF-8');
                $i -= 15;
                $soma = 0;
                $tam = strlen($alunos["obs"]);
                
                for($j = $soma; $j < $tam; $j = $soma)
                {
                    
                    $alunos["obs"] = str_replace("iconv","", $alunos["obs"]);
                    $linha = substr(str_replace("\n","", ($alunos["obs"])), $j, 130);
                    
                    $soma += 130;
                    $pdfPage->drawText($linha, 10, $i, 'UTF-8');
                    $i -= 15;
                    if($i < 30){
                        $i = 740;
                        $pdf->pages[] = $pdfPage;
                        $pdfPage = new Zend_Pdf_Page( Zend_Pdf_Page::SIZE_A4 );
                        $pdfImage = Zend_Pdf_Image::imageWithPath(APPLICATION_PATH . '/pdf/logo.png');
                        $pdfPage->drawImage( $pdfImage, 10,  747, 419, 843 );
                        $pdfPage->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 8);
                    }
                }
                $pdfPage->drawLine(10, $i, 585, $i - 2);
                $i -= 20;
                if($i < 30){
                    $i = 740;
                    $pdf->pages[] = $pdfPage;
                    $pdfPage = new Zend_Pdf_Page( Zend_Pdf_Page::SIZE_A4 );
                    $pdfImage = Zend_Pdf_Image::imageWithPath(APPLICATION_PATH . '/pdf/logo.png');
                    $pdfPage->drawImage( $pdfImage, 10,  747, 419, 843 );
                    $pdfPage->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 8);
                    
                }
            }
        }
        
        
        
        $pdf->pages[] = $pdfPage;

        $pdf->save(APPLICATION_PATH . '/pdf/relatorio.pdf');

        $len = filesize(APPLICATION_PATH . '/pdf/relatorio.pdf');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT\n");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Content-type: application/pdf;\n");
        header("Content-Length: $len;\n");
        header("Content-Disposition: attachment; filename=\"relatorio.pdf\";\n\n");

        readfile(APPLICATION_PATH . '/pdf/relatorio.pdf');
        exit;
   }
   
}


