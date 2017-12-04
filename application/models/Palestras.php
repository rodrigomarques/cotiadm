<?php

class Application_Model_Palestras extends Zend_Db_Table_Abstract
{
    protected $_name = "palestra";

    public function buscar(array $data = array())
    {
        $select = $this->select()
                        ;
        if(isset($data["curso"]))
            $select->where ("curso_idcurso = ?", $data["curso"]);
        
        if(isset($data["status"]))
            $select->where ("status = ?", $data["status"]);
        
        $select->order("datacadastro DESC");
        
        return $this->fetchAll($select)->toArray();
    }
    
    public function buscarValidacao($curso, $email)
   {
       $select = $this->select()
                        ->where("curso_idcurso = ?", $curso)
                        ->where("email = ?", $email)
                        ->where("status = ?", 1)
                        ;
       return $this->fetchRow($select);
   }
    
    
}

