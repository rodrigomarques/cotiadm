<?php

class Application_Model_AlunosTurmasReserva extends Zend_Db_Table_Abstract
{

    protected $_name = "alunoturmareservasite";

    public function buscarChaves(array $data){
        return $this->fetchRow("aluno_idaluno = " . $data['aluno_idaluno'] . " AND turma_idturma = " 
                . $data['turma_idturma']);
    }
}

