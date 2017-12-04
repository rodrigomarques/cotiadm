<?php

class Application_Model_Salas extends Zend_Db_Table_Abstract
{

    protected $_name = "sala";

    public function listar()
    {
        $select = $this->select()
                    ->order("sala ASC");
        return $this->fetchAll($select)->toArray();
    }
    
    public function buscarPorId($id)
    {
        $select = $this->select()
                    ->where("idsala = ?", $id);
        return $this->fetchRow($select);
    }
    
}

