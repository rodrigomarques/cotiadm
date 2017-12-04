<?php 
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(dirname(__FILE__).'/../..' . '/library'),
    get_include_path(),
)));
 
require_once('Zend/Loader/Autoloader.php');
Zend_Loader_Autoloader::getInstance();

require_once('../application/models/Parcelamentos.php');
$db = Zend_Db::factory('PDO_MYSQL', array(
                                'host'     => 'localhost',
                                'username' => 'root',
                                'password' => 'coti',
                                'dbname'   => 'syscoti'
                ));
 

$parcelamentoDao = new Application_Model_Parcelamentos(array('db' => $db));
$idpagamento = isset($_GET["pagamento"])?$_GET["pagamento"]:0;

$listaBoletos = $parcelamentoDao->listarPorPagamento($idpagamento);


require 'boleto_santander_banespa.php';

?>