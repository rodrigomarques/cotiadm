<?php

class Application_Model_Areas extends Zend_Db_Table_Abstract
{
    protected $_name = "area";

    public function listar()
    {
        return $this->fetchAll(null, "area ASC")->toArray();
    }
    
    public function buscarId($id)
    {
        $select = $this->select()
                    ->where("idarea = ?", array($id));
        return $this->fetchRow($select)->toArray();
    }
    
}

