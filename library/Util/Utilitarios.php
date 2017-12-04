<?php
class Util_Utilitarios{
    
    private $meses = array(
        "01" => 'Janeiro', 
        "02" => 'Fevereiro', 
        "03" => 'Março', 
        "04" => 'Abril', 
        "05" => 'Maio', 
        "06" => 'Junho', 
        "07" => 'Julho', 
        "08" => 'Agosto', 
        "09" => 'Setembro', 
        "10" => 'Outubro', 
        "11" => 'Novembro', 
        "12" => 'Dezembro'
    );
    
    private $semana = array(
        "seg" => "Segunda","ter" => "Terça","qua" => "Quarta",
        "qui" => "Quinta","sex" => "Sexta","sab" => "Sábado",
        "dom" => "Domingo"
    );
    
    public function definirSemana($semanas = '')
    {
        $diasSemana = explode(";", $semanas);
        if(count($diasSemana) > 0)
        {
            $tam = count($diasSemana);
            $extenso = "";
            if($tam == 4)
            {
                return "Segunda à Quinta";
            }
            else if($tam == 5)
            {
                return "Segunda à Sexta";
            }
            for($i = 0; $i < $tam; $i++)
            {
                $extenso .= $this->semana[$diasSemana[$i]];
                if($i < $tam - 2)
                    $extenso .= ", ";
                else if($i < $tam - 1)
                    $extenso .= " e ";
            }
            return $extenso;
        }
        else
        {
            return "n/d";
        }
    }
    
    public function mesExtenso($data = '')
    {
        if($data == "")
            $data = date('d/m/Y');
        
        $dataPart = explode('/', $data);
        if(count($dataPart) == 3)
        {
            return $dataPart[0]." de ".$this->meses[$dataPart[1]]." de " . $dataPart[2];
        }
        else
        {
            return date('d')." de ".$this->meses[date('m')]." de " . date('Y');
        }
    }
    
    public function gerarSenha($nDigits = 6){
        $senhaCompleta = rand(100000000, 999999999);
        $senhaCript = md5($senhaCompleta);
        $senhaPart = substr($senhaCript, 0, $nDigits);
        return $senhaPart;
    }
    
    public function converterDataEntrada($data){
        $data = explode("/", $data);
        $data = array_reverse($data);
        return implode("-", $data);
    }
    
    public function converterDataSaida($data){
        $data = explode("-", $data);
        $data = array_reverse($data);
        return implode("/", $data);
    }
    
    public function quebrarData($data){
        if(strpos($data, "-")){
            return explode("-", $data);
        }else{
            return explode("/", $data);
        }
    }
    
    public function validarPermissao($perfil, $resource, $action = "index"){
        $acl = new Application_Model_AdminAcl();
        return $acl->isAllowed($perfil, $resource, $action);
        
    }
    
    public function formatNumber($number){
        return number_format($number, 2,",",".");
    }
    
    public function pegarExtensao($arquivo){
        $linha = explode(".", $arquivo);
        $linha = array_reverse($linha);
        return strtolower($linha[0]);
    }
    
    public function gerarNome($extensao){
        $nome = date('YmdHis').rand(100000,999999).".".$extensao;
        return $nome;
    }
    
    public function tamanhos($imagem){
        return getimagesize($imagem);
    }
    
    public function gerarChave($codigo)
    {
        return sha1($codigo);
    }
    
    public function exportarTXT($caminho, $conteudo)
    {
        $arq = @fopen($caminho, "w+");
        @fwrite($arq, $conteudo);
        @fclose($arq);
    }
}

?>