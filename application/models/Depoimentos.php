<?php

class Application_Model_Depoimentos extends Zend_Db_Table_Abstract
{

    protected $_name = "depoimento";
    
    public function listar()
    {
        return $this->fetchAll(null, "iddepoimento DESC")->toArray();
    }
    
    public function buscar($dados = array())
    {
        $select = $this->select();
        if(isset($dados['autor']) && $dados['autor'] != "")
            $select->where ('autor LIKE ?', $dados['autor']);
        $select->order("iddepoimento DESC");
        return $this->fetchAll($select)->toArray();
    }
    
    public function buscarPorId($id)
    {
        return $this->fetchRow("iddepoimento = " . $id)->toArray();
    }

}

