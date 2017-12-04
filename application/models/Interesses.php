<?php

class Application_Model_Interesses extends Zend_Db_Table_Abstract
{

    protected $_name = "interesse";
    
    public function listarPorId($id){
        return $this->fetchRow("idinteresse = " . $id);
    }
    
    public function countInteressesAluno($idcontatosite){
        return $this->_db->query("SELECT idinteresse FROM interesse 
                            WHERE contatosite_idcontatosite = " . $idcontatosite)->rowCount();            
    }

}

