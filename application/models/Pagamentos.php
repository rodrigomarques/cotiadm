<?php

class Application_Model_Pagamentos extends Zend_Db_Table_Abstract
{

    protected $_name = "pagamento";
    
    public function verificarParcelamento(array $data)
    {
        $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('pag' => 'pagamento'))
                            ->join(array('par' => 'parcelamento'), 
                                    'pag.idpagamento = par.pagamento_idpagamento')
                            ->where('alunoturma_aluno_idaluno = ?', $data['idaluno'])
                            ->where('alunoturma_turma_idturma = ?', $data['idturma']);
        return $this->fetchAll($select)->toArray();
    }
   
    public function pagamentosPorAluno(array $data)
    {
        $select = $this->select()
                            ->where('alunoturma_aluno_idaluno = ?', $data['idaluno']);

        return $this->fetchAll($select)->toArray();
    }
    
    public function listaPagamentoAluno(array $data)
    {
        $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('pag' => 'pagamento'), array('pag.idpagamento','statusparcela' => 'par.status'))
                            ->join(array('par' => 'parcelamento'), 
                                    'pag.idpagamento = par.pagamento_idpagamento')
                            ->join(array('t' => 'turma'), 
                                     't.idturma = pag.alunoturma_turma_idturma')
                            ->join(array('c' => 'curso'), 
                                     'c.idcurso = t.curso_idcurso')
                            ->where('alunoturma_aluno_idaluno = ?', $data['idaluno'])
                            ->order("datacobranca ASC")
                            ->order("nparcela ASC")
                            ->group("idpagamento")
                            ->group("nparcela")
                            ;
        
        
        return $this->fetchAll($select)->toArray();
    }
    
    public function buscarPagamento(array $data)
    {
        $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('pag' => 'pagamento'))
                            ->join(array('at' => 'alunoturma'), 
                                    'at.aluno_idaluno = pag.alunoturma_aluno_idaluno')
                            ->join(array('t' => 'turma'), 
                                     't.idturma = at.turma_idturma')
                            ->join(array('c' => 'curso'), 
                                     'c.idcurso = t.curso_idcurso')
                            ->join(array('a' => 'aluno'), 
                                    'a.idaluno = at.aluno_idaluno')
                            ->join(array('e' => 'endereco'), 
                                    'a.idaluno = e.aluno_idaluno')
                            ->where('idpagamento = ?', $data['idpagamento']);
        return $this->fetchRow($select);
    }
    
}

