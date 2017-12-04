<?php

$dias_de_prazo_para_pagamento = 1;
$taxa_boleto = 20;
//$data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006"; 
$valor_cobrado = str_replace(",", ".",$boleto["valorparcela"]);
$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

$dadosboleto["nosso_numero"] = $boleto["pagamento_idpagamento"];  // Nosso numero sem o DV - REGRA: M�ximo de 7 caracteres!
$dadosboleto["numero_documento"] = $boleto["nparcela"].$boleto["pagamento_idpagamento"];	// Num do pedido ou nosso numero
//$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = date("d/m/Y"); // Data de emiss�o do Boleto
$dadosboleto["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com v�rgula e sempre com duas casas depois da virgula

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = isset($pagamento["nome"])?$pagamento["nome"]:"Aluno";
$dadosboleto["endereco1"] = isset($pagamento["logradouro"])?$pagamento["logradouro"].", " 
            .$pagamento["numero"]." ".$pagamento["complemento"] :"Endereço";
$dadosboleto["endereco2"] = isset($pagamento["cidade"])?$pagamento["cidade"]." - " 
            .$pagamento["estado"]." - CEP: ".$pagamento["cep"] :"Endereço";

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = "COTI INFORMÁTICA";
$dadosboleto["demonstrativo2"] = "Mensalidade referente ao curso na COTI INFORMÁTICA";
$dadosboleto["demonstrativo3"] = "";
$dadosboleto["instrucoes1"] = "Após o vencimento cobrar multa 2% ao + 0,33% ao dia.";
$dadosboleto["instrucoes2"] = "Desconto de R$ 20,00 para pagamento dentro da data e na secretaria da COTI";
$dadosboleto["instrucoes3"] = "Cursos: " . (isset($pagamento["curso"])?$pagamento["curso"]:"CURSO COTI");
$dadosboleto["instrucoes4"] = "";

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = "";
$dadosboleto["aceite"] = "";		
$dadosboleto["especie"] = "R$";
$dadosboleto["especie_doc"] = "";


// ---------------------- DADOS FIXOS DE CONFIGURA��O DO SEU BOLETO --------------- //


// DADOS PERSONALIZADOS - SANTANDER BANESPA
$dadosboleto["codigo_cliente"] = "130001956"; // C�digo do Cliente (PSK) (Somente 7 digitos)
$dadosboleto["ponto_venda"] = "3017"; // Ponto de Venda = Agencia
$dadosboleto["carteira"] = "102";  // Cobran�a Simples - SEM Registro
$dadosboleto["carteira_descricao"] = "COBRANÇA SIMPLES - CSR";  // Descri��o da Carteira

// SEUS DADOS
$dadosboleto["identificacao"] = "Pagamento referente a mensalidade da coti informática - Curso: " 
    .(isset($pagamento["curso"])?$pagamento["curso"]:"CURSO COTI");
$dadosboleto["cpf_cnpj"] = "";
$dadosboleto["endereco"] = "Av. Rio Branco, 185";
$dadosboleto["cidade_uf"] = "Rio de Janeiro / RJ";
$dadosboleto["cedente"] = "COTI INFORMÁTICA LTDA";



$data = $boleto["datacobranca"];
$data = explode("-",$data);
$data = array_reverse($data);
$data = implode("/" , $data);

$data_venc = $data;
$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA

include_once("include/funcoes_santander_banespa.php"); 
include("include/layout_santander_banespa.php");

?>
