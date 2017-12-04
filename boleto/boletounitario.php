<?php 
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(dirname(__FILE__).'/../..' . '/library'),
    get_include_path(),
)));
 
require_once('Zend/Loader/Autoloader.php');
Zend_Loader_Autoloader::getInstance();

require_once('../application/models/Parcelamentos.php');
require_once('../application/models/Pagamentos.php');
$db = Zend_Db::factory('PDO_MYSQL', array(
                                'host'     => 'localhost',
                                'username' => 'root',
                                'password' => 'coti',
                                'dbname'   => 'syscoti'
                ));
 

$parcelamentoDao = new Application_Model_Parcelamentos(array('db' => $db));
$dados = isset($_GET["pagamento"])?$_GET["pagamento"]:0;
$dados = explode(";", $dados);

$idpagamento = isset($dados[1])?$dados[1]:0;
$parcela = isset($dados[0])?$dados[0]:0;

if($idpagamento != 0 && $parcela != 0)
{
 
    $boleto = $parcelamentoDao->buscarPorChave($parcela,$idpagamento);
    if($boleto != null)
    {
        $pagamentoDao = new Application_Model_Pagamentos(array('db' => $db));
        $pagamento = $pagamentoDao->buscarPagamento(array('idpagamento' => $idpagamento));
        require 'santanderunitario.php';
    }
    else
    {
        echo "Não foi encontrado o boleto solicitado!";
    }
}
else
{
    echo "Não foi encontrado o boleto solicitado!";
}

?>