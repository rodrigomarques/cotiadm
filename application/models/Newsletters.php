<?php

class Application_Model_Newsletters extends Zend_Db_Table_Abstract
{

    protected $_name = "newsletter";
    
    public function buscar(array $dados)
    {
        $select = $this->select();
        if(isset($dados["nome"]))
                        $select = $select->where("nome LIKE ?", $dados["nome"]);
        if(isset($dados["email"]))
                        $select = $select->where("email LIKE ?", $dados["email"]);
        
        return $this->fetchAll($select, "nome ASC")->toArray();
    }
    
    public function findEmail($email = "")
    {
        $select = $this->select()
                        ->where("email = ?", $email);
        
        return $this->fetchAll($select)->toArray();
    }

    public function listarPorId($id)
    {
        $select = $this->select()
                        ->where("idnewsletter = ?", $id);
        
        return $this->fetchRow($select);
    }
}

