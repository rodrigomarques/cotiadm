<?php

class Application_Model_Turmas extends Zend_Db_Table_Abstract
{
    protected $_name = "turma";
    
    public function consultarTurma(array $dados)
    {
        $select = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('t' => 'turma'), array('t.idturma', 't.frequencia', 't.horario', 't.inicio',
                            't.fim', 't.valor', 't.vagas','statusturma' => 't.status', 'statuscurso' => 'c.status', 'c.curso', 'c.cargahoraria',
                            't.turmaincompany',
                            'c.posicao', 'u.usuario', 'u.idusuario'))
                        ->join(array('c' => 'curso'), 't.curso_idcurso = c.idcurso')
                        ->joinLeft(array('u' =>'usuario'), 't.usuario_idusuario = u.idusuario')
                        ->joinLeft(array('s' =>'sala'), 's.idsala = t.sala_idsala');
        if(isset($dados["inicio"]) && $dados["inicio"] != "")
            $select->where ('inicio >= ? ', $dados["inicio"]);
        
        
        if(isset($dados["fim"]) && $dados["fim"] != "")
            $select->where ('DATE_ADD(fim, INTERVAL 8 DAY) >=  CURDATE()');
        
        if(isset($dados["professor"]) && $dados["professor"] != "")
            $select->where ('idusuario = ? ', $dados["professor"]);
        
        if(isset($dados["curso"]) && $dados["curso"] != "")
            $select->where ('idcurso = ? ', $dados["curso"]);
        
        if(isset($dados["status"]) && $dados["status"] != "")
            $select->where ('t.status = ? ', $dados["status"]);
        
        if(isset($dados["sala"]) && $dados["sala"] != "")
            $select->where ('s.idsala = ? ', $dados["sala"]);
        
        if(isset($dados["orderby"]))
            $select->order ($dados["orderby"]);
        
        if(isset($dados["orderby2"]))
            $select->order ($dados["orderby2"]);
        
        return $this->fetchAll($select)->toArray();
    }
    
    public function consultarTurmaWebService(array $dados)
    {
        $select = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('t' => 'turma'), array('t.idturma', 't.frequencia', 't.horario', 't.inicio',
                            't.fim', 't.valor', 't.vagas','statusturma' => 't.status', 'statuscurso' => 'c.status', 'c.curso',
                            ))
                        ->join(array('c' => 'curso'), 't.curso_idcurso = c.idcurso', array())
                        ->joinLeft(array('u' =>'usuario'), 't.usuario_idusuario = u.idusuario',array())
                        ->joinLeft(array('s' =>'sala'), 's.idsala = t.sala_idsala', array());
        
        if(isset($dados["inicio"]) && $dados["inicio"] != "")
            $select->where ('inicio >= ? ', $dados["inicio"]);
        
        if(isset($dados["fim"]) && $dados["fim"] != "")
            $select->where ('DATE_ADD(fim, INTERVAL 8 DAY) >=  CURDATE()');
        
        if(isset($dados["professor"]) && $dados["professor"] != "")
            $select->where ('idusuario = ? ', $dados["professor"]);
        
        if(isset($dados["curso"]) && $dados["curso"] != "")
            $select->where ('idcurso = ? ', $dados["curso"]);
        
        if(isset($dados["status"]) && $dados["status"] != "")
            $select->where ('t.status = ? ', $dados["status"]);
        
        if(isset($dados["sala"]) && $dados["sala"] != "")
            $select->where ('s.idsala = ? ', $dados["sala"]);
        
        if(isset($dados["orderby"]))
            $select->order ($dados["orderby"]);
        
        if(isset($dados["orderby2"]))
            $select->order ($dados["orderby2"]);
        
        return $this->fetchAll($select)->toArray();
    }
    
    public function consultarTurmaSite(array $dados)
    {
        $select = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('t' => 'turma'), array('t.idturma', 't.frequencia', 't.horario', 't.inicio',
                            't.fim', 't.valor', 't.vagas','statusturma' => 't.status', 'statuscurso' => 'c.status', 'c.curso', 'c.cargahoraria', 
                            't.turmaincompany',
                            'c.posicao'))
                        ->join(array('c' => 'curso'), 't.curso_idcurso = c.idcurso')
                        ->join(array('a' => 'area'), 'a.idarea = c.area_idarea');
        
        if(isset($dados["area"]) && $dados["area"] != "")
            $select->where ('idarea = ? ', $dados["area"]);
        
        if(isset($dados["idcurso"]) && $dados["idcurso"] != "")
            $select->where ('idcurso = ? ', $dados["idcurso"]);
        
        if(isset($dados["inicio"]) && $dados["inicio"] != ""){
            
            $util = new Util_Utilitarios();
            $dados["inicio"] = $util->converterDataEntrada($dados["inicio"]);
            
            $select->where ('inicio >= ? ', $dados["inicio"]);
        }
        
        if(isset($dados["fim"]) && $dados["fim"] != ""){
            $util = new Util_Utilitarios();
            $dados["fim"] = $util->converterDataEntrada($dados["fim"]);
            
            $select->where ('inicio <= ? ', $dados["fim"]);
        }
        
        $select->where ("turmaincompany = 0");
        $select->where ("incompany = 0");
        //$select->where ("vagas > 0");
        $select->where ('t.status = 1 ');
        $select->where ('c.status = 1 ');
        
        $select->order ('posicao ASC');
        $select->order ('curso ASC');
        $select->order ('inicio ASC');
        
        return $this->fetchAll($select)->toArray();
    }

    public function listarTurmaPorAluno($id)
    {
        $select = $this->select()
                ->setIntegrityCheck(false)
                        ->from(array('t' => 'turma'), array('t.idturma', 't.frequencia', 't.horario', 't.inicio',
                            't.fim', 't.valor', 't.vagas', 't.status', 'c.curso', 'c.cargahoraria', 
                            'c.posicao', 'u.usuario', 'u.idusuario', 'at.refazendo', 'at.aluno_idaluno', 
                            'at.formapagamento', 'at.responsavel', 'at.obs', 'u.nome'))
                        ->join(array('at' => 'alunoturma'), "t.idturma = at.turma_idturma")
                        ->join(array('c' => 'curso'), "c.idcurso = t.curso_idcurso")
                        ->joinLeft(array('u' =>'usuario'), 't.usuario_idusuario = u.idusuario')
                ->where('aluno_idaluno = ?', array($id));
        return $this->fetchAll($select)->toArray();
        
    }
    
    public function buscarPorId($id)
    {
        return $this->fetchRow("idturma = " . $id);
        
    }
    
    public function buscarIdTurma($id)
    {
        $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('t' => 'turma'))
                    ->join(array("c" => "curso"),'t.curso_idcurso = c.idcurso')
                    ->where("idturma = ?", $id);
        
        return $this->fetchRow($select);
    }
    
    public function proximasTurmas()
    {
        $select = $this->select()
                        ->setIntegrityCheck(false)
                        ->from(array('t' => 'turma'), array('t.idturma', 't.frequencia', 't.horario', 't.inicio',
                            't.turmaincompany', 'c.curso', 'c.cargahoraria'))
                        ->join(array('c' => 'curso'), 't.curso_idcurso = c.idcurso');
        
        $select->where ('t.turmaincompany = 0 ');
        $select->where ('t.status = 1 ');
        $select->where ('c.status = 1 ');
        //DATE_ADD(fim, INTERVAL 8 DAY)
        $select->where ('t.inicio >= CURRENT_DATE');
        $select->where ("c.incompany = 0");
        
        
        $select->order ('inicio ASC');
        $select->limit(4);
        
        return $this->fetchAll($select)->toArray();
    }
}

