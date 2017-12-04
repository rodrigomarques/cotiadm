<?php

class Application_Model_Alunos extends Zend_Db_Table_Abstract
{

    protected $_name = "aluno";

    public function buscar(array $dados)
    {
        $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from("aluno", array('aluno.*'))
                    ->joinLeft("alunoturma", 'aluno_idaluno = idaluno')
                    ->joinLeft("turma", 'turma_idturma = idturma')
                    ;
        if(isset($dados['nome']) && $dados['nome'] != "%")
            $select->where("nome LIKE ?" , $dados["nome"]);
        
        if(isset($dados['curso']) && $dados['curso'] != "")
            $select->where("curso_idcurso = ?" , $dados["curso"]);
        
        if(isset($dados['matricula']) && $dados['matricula'] != "")
            $select->where("idaluno = ?" , $dados["matricula"]);
        
        if(isset($dados['email']) && $dados['email'] != "%")
            $select->where("email LIKE ?" , $dados["email"]);
        
        if(isset($dados['cpf']) && $dados['cpf'] != "%")
            $select->where("cpf LIKE ?" , $dados["cpf"]);
        
        if(isset($dados['order']) && $dados['order'] != "")
            $select->order($dados['order']);
        
        if(isset($dados['limit']) && $dados['limit'] != "")
            $select->limit ($dados["limit"]);
        
        $select->group("idaluno");
        
        return $this->fetchAll($select)->toArray();
    }
    
    public function buscarId($id)
    {   
        $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('a' => 'aluno'))
                    ->joinLeft(array('e' => 'endereco'), 'e.aluno_idaluno = a.idaluno')
                    ->where('idaluno = ?', $id);
        return $this->fetchRow($select);
    }
    
    public function buscarCpf($cpf)
    {
        $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('a' => 'aluno'))
                    ->where('cpf = ?', $cpf);
        return $this->fetchRow($select);
    }
}

