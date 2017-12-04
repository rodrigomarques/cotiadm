<?php

class Application_Model_AlunosTurmas extends Zend_Db_Table_Abstract
{

    protected $_name = "alunoturma";

    public function buscarChaves(array $data){
        return $this->fetchRow("aluno_idaluno = " . $data['aluno_idaluno'] . " AND turma_idturma = " 
                . $data['turma_idturma']);
    }
    
    public function buscarTurmas(array $data){
        return $this->fetchAll("aluno_idaluno = " . $data['aluno_idaluno'])->toArray();
    }
    
    public function buscarUltimasObsPorAluno($idaluno){
        $select = $this
                    ->select()
                     ->where("aluno_idaluno = ?", array($idaluno))
                     ->where("obs != ''")
                     ->order("cadastro desc");
        return $this->fetchAll($select)->toArray();
    }
    
    public function buscarAlunosPorTurma(array $data){
        $select = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('t' => 'turma'), array('t.idturma', 't.frequencia', 't.horario', 't.inicio',
                            't.fim', 't.valor', 't.vagas', 't.status', 'c.curso', 'c.cargahoraria', 
                            'c.posicao', 'u.usuario', 'u.idusuario','nomealuno' => 'a.nome', 'emailaluno' => 'a.email', 'a.idaluno', 
                            'a.celular','a.telefone', 'at.refazendo','nomeusuario' => 'u.nome'))
                        ->join(array('c' => 'curso'), 't.curso_idcurso = c.idcurso')
                        ->join(array('at' => 'alunoturma'), 't.idturma = at.turma_idturma')
                        ->join(array('a' => 'aluno'), 'a.idaluno = at.aluno_idaluno')
                        ->joinLeft(array('u' =>'usuario'), 't.usuario_idusuario = u.idusuario');
        
        $select->where("idturma = ?", $data["idturma"]);
        //echo $select->assemble();
        return $this->fetchAll($select)->toArray();
    }
    
    public function buscarAlunosPorTurmaRelatorio(array $data){
        $select = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('t' => 'turma'), array('t.idturma'
                            ,'nomealuno' => 'a.nome', 'emailaluno' => 'a.email', 
                            'a.idaluno', 'at.refazendo'))
                        ->join(array('at' => 'alunoturma'), 't.idturma = at.turma_idturma')
                        ->join(array('a' => 'aluno'), 'a.idaluno = at.aluno_idaluno');
        $select->where("at.refazendo = 0");
        $select->where("curso_idcurso = ?", $data["idcurso"]);
        //echo $select->assemble();
        return $this->fetchAll($select)->toArray();
    }
    
    public function buscarAluno(array $data){
        $select = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('t' => 'turma'), array('t.idturma', 't.frequencia', 't.horario', 't.inicio',
                            't.fim', 't.valor', 't.vagas', 't.status', 'c.curso', 'c.cargahoraria', 
                            'c.posicao', 'u.usuario', 'u.idusuario','nomealuno' => 'a.nome', 'emailaluno' => 'a.email', 'a.idaluno', 
                            'a.celular','a.telefone', 'at.refazendo','nomeusuario' => 'u.nome',
                            'at.formapagamento', 'at.obs', 'at.responsavel'))
                        ->join(array('c' => 'curso'), 't.curso_idcurso = c.idcurso')
                        ->join(array('at' => 'alunoturma'), 't.idturma = at.turma_idturma')
                        ->join(array('a' => 'aluno'), 'a.idaluno = at.aluno_idaluno')
                        ->joinLeft(array('e' => 'endereco'), 'a.idaluno = e.aluno_idaluno')
                        ->joinLeft(array('u' =>'usuario'), 't.usuario_idusuario = u.idusuario');
        
        $select->where("idturma = ?", $data["idturma"]);
        $select->where("idaluno = ?", $data["idaluno"]);
        //echo $select->assemble();
        return $this->fetchRow($select);
    }
    
    public function buscarPendentes(){
        $select = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('t' => 'turma'), array('t.idturma', 't.frequencia', 't.horario', 't.inicio',
                            't.fim', 't.valor', 't.vagas', 't.status', 'c.curso', 'c.cargahoraria', 
                            'c.posicao', 'u.usuario', 'u.idusuario','nomealuno' => 'a.nome', 'emailaluno' => 'a.email', 'a.idaluno', 
                            'a.celular','a.telefone', 'at.refazendo'))
                        ->join(array('c' => 'curso'), 't.curso_idcurso = c.idcurso')
                        ->join(array('at' => 'alunoturma'), 't.idturma = at.turma_idturma')
                        ->join(array('a' => 'aluno'), 'a.idaluno = at.aluno_idaluno')
                        ->joinLeft(array('u' =>'usuario'), 't.usuario_idusuario = u.idusuario');
        
        $select->where("pendencia = 1");
        return $this->fetchAll($select)->toArray();
    }
}

