<?php

class Application_Model_AdminAcl extends Zend_Acl
{

    public $resouces = array("Condomino", "Festa", "Funcionario", "Gasto", "Relatorio", "Usuario");
    
    public function  __construct() {
        $this->addRole(new Zend_Acl_Role('VISIT'));
        $this->addRole(new Zend_Acl_Role('USER'), 'VISIT');
        $this->addRole(new Zend_Acl_Role('professor'), 'USER');
        $this->addRole(new Zend_Acl_Role('secretaria'), 'USER');
        $this->addRole(new Zend_Acl_Role('adm'), 'USER');

        $this->addResource(new Zend_Acl_Resource('Aluno'));
        $this->addResource(new Zend_Acl_Resource('Error'));
        $this->addResource(new Zend_Acl_Resource('Area'));
        $this->addResource(new Zend_Acl_Resource('Chamada'));
        $this->addResource(new Zend_Acl_Resource('Contato'));
        $this->addResource(new Zend_Acl_Resource('Index'));
        $this->addResource(new Zend_Acl_Resource('Curso'));
        $this->addResource(new Zend_Acl_Resource('Depoimento'));
        $this->addResource(new Zend_Acl_Resource('Newsletter'));
        $this->addResource(new Zend_Acl_Resource('Turma'));
        $this->addResource(new Zend_Acl_Resource('Usuario'));
        $this->addResource(new Zend_Acl_Resource('Arquivo'));
        $this->addResource(new Zend_Acl_Resource('Sala'));
        
        $this->addResource(new Zend_Acl_Resource('Relatorio'));
        $this->addResource(new Zend_Acl_Resource('Promocao'));
        $this->addResource(new Zend_Acl_Resource('Curriculo'));
        $this->addResource(new Zend_Acl_Resource('Campanha'));
        $this->addResource(new Zend_Acl_Resource('Palestra'));
        
        $this->addResource(new Zend_Acl_Resource('Pagamento'));
        $this->addResource(new Zend_Acl_Resource('Reserva'));
        
        $this->allow('VISIT', 'Usuario', array('logar', 'sair', 'esqueceusenha'));
        $this->allow('VISIT', 'Index', array('index'));
        $this->allow('VISIT', 'Error', array('index','error'));
        
        $this->allow('USER', 'Index', array('painel', 'alterarsenha'));
        $this->allow('USER', 'Aluno', array('index', 'listarcursos', 'conferir'));
        //$this->allow('USER', 'Chamada', array('index', 'gerarchamada', 'confirmar'));
        $this->allow('USER', 'Curso', array('index'));
        $this->allow('USER', 'Arquivo', array('index'));
        
        $this->allow('USER', 'Curriculo', array('index','buscaraluno','empregar'));
        $this->allow('USER', 'Turma', array('listamobile'));
        
        $this->allow('professor', 'Arquivo', array('cadastrar'));
        $this->allow('professor', 'Chamada', array('index', 'gerarchamada', 'confirmar'));
        $this->allow('professor', 'Curriculo', array('cadastrar'));
        
        $this->allow('secretaria', 'Aluno', array('cadastrar','excluir','excluirturmas','edit','boleto',
                            'turmas', 'pagamento', 'pagamentoaluno', 'buscarpagamento', 'realizarpagamento', 'editarpagamento', 'detalhesturma', 'relatoriopendente'));
        $this->allow('secretaria', 'Area', array('index', 'editar'));
        $this->allow('secretaria', 'Contato', array('index', 'excluir', 'atualizarcontato', 'excluirall'));
        $this->allow('secretaria', 'Curso', array('cadastrar','editar','excluir'));
        $this->allow('secretaria', 'Depoimento', array('index','editar','excluir'));
        $this->allow('secretaria', 'Newsletter', array('index', 'excluir'));
        $this->allow('secretaria', 'Turma', array('index', 'cadastrar', 'editar', 'excluir', 'veralunos', 'excluiraluno','vervagas','atualizarvagas', 'excluirturma', 'gerarpdf'));
        $this->allow('secretaria', 'Sala', array('index', 'editar'));
        
        $this->allow('adm', 'Usuario', array('index', 'modificar', 'editar'));
        $this->allow('adm', 'Aluno', array('index', 'cadastrar','excluir','excluirturmas','edit','boleto','turmas', 'pagamento', 'devedor',
            'pagamentoaluno', 'buscarpagamento', 'realizarpagamento', 'editarpagamento','detalhesturma', 'relatoriopendente'));
        $this->allow('adm', 'Area', array('index', 'editar'));
        $this->allow('adm', 'Contato', array('index', 'excluir', 'atualizarcontato', 'excluirall'));
        $this->allow('adm', 'Curso', array('cadastrar','editar','excluir'));
        $this->allow('adm', 'Depoimento', array('index','editar','excluir'));
        $this->allow('adm', 'Newsletter', array('index', 'excluir'));
        $this->allow('adm', 'Turma', array('index', 'cadastrar', 'editar', 'excluir', 'veralunos', 'excluiraluno','vervagas', 'atualizarvagas', 'excluirturma', 'gerarpdf'));
        $this->allow('adm', 'Arquivo', array('cadastrar'));
        $this->allow('adm', 'Sala', array('index', 'editar'));
        
        $this->allow('adm', 'Promocao', array('index', 'novo', 'status', 'verificar', ''));
        $this->allow('adm', 'Relatorio', array('index', 'turmaprofessor', 'alunonovo', 'statusaluno', 'emailalunos'));
        $this->allow('adm', 'Curriculo', array('cadastrar'));
        
        $this->allow('adm', 'Campanha', array('index', 'cadastrar'));
        $this->allow('adm', 'Palestra', array('index','excluir','edit'));
        
        $this->allow('adm', 'Pagamento', array('index','buscar'));
        $this->allow('adm', 'Reserva', array('index'));

            
    }

     /*
     * Regras:
      *     USER
      *     CONDOMINO
      *     FUNCIONARIO
      *     FUNCIONARIOADM
      *     ADMIN
     */
}

