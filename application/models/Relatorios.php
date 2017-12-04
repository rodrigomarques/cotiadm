<?php

class Application_Model_Relatorios extends Zend_Db_Table_Abstract
{
    
    public function listaTurmaProfessor(array $dados){
        
        $query = "
        SELECT 
	t.frequencia, t.horario, t.inicio, t.valor, t.vagas, t.status,t.idturma, 
	IF(u.nome IS NULL,'SEM NOME', u.nome) as professor , u.email,
	at.refazendo, at.total, at.formapagamento, at.obs, at.reserva, at.pendencia,at.statusturma,
	a.nome as aluno, a.celular,
        c.curso 
        FROM 
                turma t
        INNER JOIN curso c 
        ON c.idcurso = t.curso_idcurso 
        INNER JOIN 
                alunoturma at 
        ON t.idturma = at.turma_idturma 
        INNER JOIN 
                aluno a 
        ON a.idaluno = at.aluno_idaluno 
        LEFT JOIN 	
                usuario u 
        ON u.idusuario = t.usuario_idusuario 
        WHERE 1 = 1 ";
        
        if(isset($dados["professor"]) && $dados["professor"] != "")
            $query .= " AND u.idusuario =  " . $dados["professor"] ;
        
        if(isset($dados["data"]) && $dados["data"] != "")
            $query .= " AND t.inicio >= '" . $dados["data"]. "' " ;
        
        $query .= "
        ORDER BY t.inicio ASC, 
        t.idturma ASC
        ;
	";

        $select = $this->_db->query($query)->fetchAll();

        return $select;
        
    }
    
    public function listaNovosAlunos(array $dados){
            
        $query = "
            SELECT 
                nome, cadastro, idaluno, curso, valor, formapagamento, obs 
                FROM aluno a 
                INNER JOIN alunoturma at 
                ON aluno_idaluno = idaluno 
                INNER JOIN turma t 
                ON turma_idturma = idturma 
                INNER JOIN curso c 
                ON idcurso  = curso_idcurso
        ";
        
        $query .= "
            WHERE 1 = 1 
            AND reserva = 0 ";
        
        $query .= " AND cadastro BETWEEN '".$dados['dataincio']."' AND '".$dados['datafim']."' ";
        $query .= " ORDER BY cadastro DESC";
        
        $select = $this->_db->query($query)->fetchAll();

        return $select;
    }
    
    public function statusaluno(array $dados){
            
        $query = "
            SELECT 
                nome, cadastro, idaluno, curso, valor, formapagamento, obs 
                FROM aluno a 
                INNER JOIN alunoturma at 
                ON aluno_idaluno = idaluno 
                INNER JOIN turma t 
                ON turma_idturma = idturma 
                INNER JOIN curso c 
                ON idcurso  = curso_idcurso
        ";
        
        $query .= "
            WHERE 1 = 1 
            ";
        if(isset($dados["situacao"])){
            if($dados["situacao"] == "RESERVA"){
                $query .= " AND reserva = 1 ";
            }else{
                $query .= " AND statusturma = '" . $dados["situacao"]. "' ";
            }
        }
        $query .= " AND cadastro BETWEEN '".$dados['dataincio']."' AND '".$dados['datafim']."' ";
        $query .= " ORDER BY cadastro DESC";
        
        $select = $this->_db->query($query)->fetchAll();

        return $select;
    }
    
}

