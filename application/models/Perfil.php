<?php

class Application_Model_Perfil extends Zend_Db_Table_Abstract
{

    protected $_name = "perfil";
    
    public function listar()
    {
        return $this->fetchAll(null, "perfil ASC")->toArray();
    }

    public function findById($id)
    {
        $select = $this->select()
                    ->where("idperfil = ?", $id);
        return $this->fetchRow($select);
    }
}

