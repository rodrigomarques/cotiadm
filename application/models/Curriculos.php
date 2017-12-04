<?php

class Application_Model_Curriculos extends Zend_Db_Table_Abstract
{

    protected $_name = "curriculo";

    public function buscarPorId($idaluno)
    {
        $select = $this->select()
                    ->where("aluno_idaluno = ?", $idaluno);
        return $this->fetchRow($select);
    }
    
    public function buscarId($idcurriculo)
    {
        $select = $this->select()
                    ->where("idcurriculo = ?", $idcurriculo);
        return $this->fetchRow($select);
    }
    
     public function buscar(array $dados)
    {
        $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('a' => 'aluno'), array('a.*', 'c.*'))
                    ->joinInner(array('c' => 'curriculo'), 'a.idaluno = c.aluno_idaluno')
                    ->joinInner(array('at' => 'alunoturma'), 'a.idaluno = at.aluno_idaluno')
                    ->joinInner(array('t' => 'turma'), 'at.turma_idturma = t.idturma')
                    ->joinInner(array('cur' => 'curso'), 'cur.idcurso = t.curso_idcurso')
                    ;
        if(isset($dados['nome']) && $dados['nome'] != "%")
            $select->where("nome LIKE ?" , $dados["nome"]);
        
        if(isset($dados['matricula']) && $dados['matricula'] != "")
            $select->where("idaluno = ?" , $dados["matricula"]);
        
        if(isset($dados['email']) && $dados['email'] != "%")
            $select->where("email LIKE ?" , $dados["email"]);
        
        if(isset($dados['cpf']) && $dados['cpf'] != "%")
            $select->where("cpf LIKE ?" , $dados["cpf"]);
        
        if(isset($dados['curso']) && $dados['curso'] != "")
            $select->where("idcurso = ?" , $dados["curso"]);
        
        if(isset($dados['alocado']) && $dados['alocado'] != "")
            $select->where("alocado = ?" , $dados["alocado"]);
        
        if(isset($dados['order']) && $dados['order'] != "")
            $select->order($dados['order']);
        
        if(isset($dados['limit']) && $dados['limit'] != "")
            $select->limit ($dados["limit"]);
        
        $select->group("a.idaluno");
        return $this->fetchAll($select)->toArray();
    }
    
}

