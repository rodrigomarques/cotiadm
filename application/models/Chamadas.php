<?php

class Application_Model_Chamadas extends Zend_Db_Table_Abstract
{

    protected $_name = "chamada";

    public function listarPresenca(array $params)
    {
        $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('c' => 'chamada'), array('c.data', 'c.status',
                        'c.alunoturma_turma_idturma', 'c.alunoturma_aluno_idaluno'));
        
        if(isset($params['idturma']) && $params['idturma'] != "")
            $select->where ('alunoturma_turma_idturma = ?', $params['idturma']);
        
        $select = $select
                        ->order("data ASC");
        return $this->fetchAll($select)->toArray();
    }
    
    public function buscarChaves(array $data){
        return $this->fetchRow("alunoturma_aluno_idaluno = " . $data['alunoturma_aluno_idaluno'] . 
                " AND alunoturma_turma_idturma = " 
                . $data['alunoturma_turma_idturma']. " AND data = '".$data["data"]."'");
    }
    
    
    public function listarPresencaData(array $params)
    {
        $select = $this->select()
                    ->distinct()
                    ->setIntegrityCheck(false)
                    ->from(array('c' => 'chamada'), array('c.data'));
        
        if(isset($params['idturma']) && $params['idturma'] != "")
            $select->where ('alunoturma_turma_idturma = ?', $params['idturma']);
        
        $select = $select
                        ->order("data DESC");
        return $this->fetchAll($select)->toArray();
    }
}

