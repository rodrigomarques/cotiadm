<?php

class Application_Model_Promocoes extends Zend_Db_Table_Abstract
{

    protected $_name = "promocao";

    public function listar()
    {
        $select = $this->select()
                    ->order("dataInicio DESC")
                    ->order("status DESC")
                    ->order("dataFim DESC");
        return $this->fetchAll($select)->toArray();
    }
    
    public function buscarCodigo(array $data)
    {
        $select = $this->select()
                ->union(array(
                    $this->select()
                    ->where('datafim >= now()')
                    ->where('datainicio <= now()')
                    ->where('status = 1')
                    ->where("codigo = ?" , $data["codigo"])
                    ,
                    $this->select()->where('isNull(datafim)')
                    ->where('datainicio <= now()')
                    ->where('status = 1')
                    ->where("codigo = ?" , $data["codigo"])
                    )
                )
                ->order("dataInicio DESC")
                    ->order("status DESC")
                    ->order("dataFim DESC");
        
        return $this->fetchAll($select)->toArray();
    }
    
    public function buscarId($id)
    {   
        $select = $this->select()
                    ->where('idpromocao = ?', $id);
        return $this->fetchRow($select);
    }
    
}

