<?php

class Zend_View_Helper_AlunoDetalhes extends Zend_View_Helper_Abstract{

    public $view;

    public function alunoDetalhes($id) {
        $alunoTurmaDao = new Application_Model_AlunosTurmas();
        $alunoDao = new Application_Model_Alunos();
            
            $dadosAluno = $alunoDao->buscarId($id);
            $dados = $alunoTurmaDao->buscarUltimasObsPorAluno($id);
            
            $conteudo = "";
            
            if($dadosAluno != null && $dadosAluno["observacaoaluno"] != ""):
                $conteudo .= "Observação do aluno: ";
                $conteudo .= $dadosAluno["observacaoaluno"];
                $conteudo .= "<br><hr><br>";
            endif;
            
            if(count($dados) > 0):
                $conteudo .= "Observações das turmas: <br>";
                foreach($dados as $al):
                    $conteudo .= $al["obs"];
                    $conteudo .= "<br><hr><br>";
                endforeach;
            endif;
            
            return $conteudo; 
        
    }

    public function setView(Zend_View_Interface $view) {

        $this->view = $view;

    }

}

?>
