-- MySQL dump 10.13  Distrib 5.5.12, for Win32 (x86)
--
-- Host: localhost    Database: villageouropreto
-- ------------------------------------------------------
-- Server version	5.5.12

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;



--
-- Table structure for table `arquivos`
--

DROP TABLE IF EXISTS `arquivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arquivos` (
  `idarquivos` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(45) NOT NULL,
  `descricao` text,
  `arquivo` varchar(255) NOT NULL,
  `data` date NOT NULL,
  `status` int(11) NOT NULL,
  `usuario_idusuario` int(11) NOT NULL,
  PRIMARY KEY (`idarquivos`),
  KEY `fk_arquivos_usuario1` (`usuario_idusuario`),
  CONSTRAINT `fk_arquivos_usuario1` FOREIGN KEY (`usuario_idusuario`) REFERENCES `usuario` (`idusuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arquivos`
--



--
-- Table structure for table `cargo`
--

DROP TABLE IF EXISTS `cargo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cargo` (
  `idcargo` int(11) NOT NULL AUTO_INCREMENT,
  `nomecargo` varchar(45) NOT NULL,
  PRIMARY KEY (`idcargo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cargo`
--

LOCK TABLES `cargo` WRITE;
/*!40000 ALTER TABLE `cargo` DISABLE KEYS */;
/*!40000 ALTER TABLE `cargo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `condomino`
--

DROP TABLE IF EXISTS `condomino`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `condomino` (
  `idcondomino` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `sexo` char(1) NOT NULL,
  `datanascimento` date NOT NULL,
  `cpf` varchar(15) NOT NULL,
  `rg` varchar(45) NOT NULL,
  `bloco` int(11) NOT NULL,
  `apartamento` int(11) NOT NULL,
  `sindico` int(11) NOT NULL,
  `usuario_idusuario` int(11) NOT NULL,
  PRIMARY KEY (`idcondomino`),
  KEY `fk_condomino_usuario1` (`usuario_idusuario`),
  CONSTRAINT `fk_condomino_usuario1` FOREIGN KEY (`usuario_idusuario`) REFERENCES `usuario` (`idusuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `condomino`
--


--
-- Table structure for table `correspondencia`
--

DROP TABLE IF EXISTS `correspondencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `correspondencia` (
  `idcorrespondencia` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) NOT NULL,
  `descricao` text,
  `status` varchar(45) NOT NULL,
  `datachegada` datetime NOT NULL,
  `dataentregue` datetime DEFAULT NULL,
  `condomino_idcondomino` int(11) DEFAULT NULL,
  PRIMARY KEY (`idcorrespondencia`),
  KEY `fk_correspondencia_condomino1` (`condomino_idcondomino`),
  CONSTRAINT `fk_correspondencia_condomino1` FOREIGN KEY (`condomino_idcondomino`) REFERENCES `condomino` (`idcondomino`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `correspondencia`
--

--
-- Table structure for table `enderecofuncionario`
--

DROP TABLE IF EXISTS `enderecofuncionario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enderecofuncionario` (
  `idenderecofuncionario` int(11) NOT NULL AUTO_INCREMENT,
  `logradouro` varchar(50) NOT NULL,
  `numero` int(11) NOT NULL,
  `complemento` varchar(50) DEFAULT NULL,
  `bairro` varchar(40) NOT NULL,
  `cidade` varchar(50) NOT NULL,
  `estado` char(2) NOT NULL,
  `cep` varchar(15) NOT NULL,
  `funcionario_idfuncionario` int(11) NOT NULL,
  PRIMARY KEY (`idenderecofuncionario`),
  KEY `fk_enderecofuncionario_funcionario1` (`funcionario_idfuncionario`),
  CONSTRAINT `fk_enderecofuncionario_funcionario1` FOREIGN KEY (`funcionario_idfuncionario`) REFERENCES `funcionario` (`idfuncionario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enderecofuncionario`
--

LOCK TABLES `enderecofuncionario` WRITE;
/*!40000 ALTER TABLE `enderecofuncionario` DISABLE KEYS */;
/*!40000 ALTER TABLE `enderecofuncionario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enderecoproprietario`
--

DROP TABLE IF EXISTS `enderecoproprietario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enderecoproprietario` (
  `idenderecoproprietario` int(11) NOT NULL AUTO_INCREMENT,
  `logradouro` varchar(50) NOT NULL,
  `numero` int(11) NOT NULL,
  `complemento` varchar(50) DEFAULT NULL,
  `bairro` varchar(40) NOT NULL,
  `cidade` varchar(50) NOT NULL,
  `estado` char(2) NOT NULL,
  `cep` varchar(15) NOT NULL,
  `proprietario_idproprietario` int(11) NOT NULL,
  PRIMARY KEY (`idenderecoproprietario`),
  KEY `fk_enderecoproprietario_proprietario1` (`proprietario_idproprietario`),
  CONSTRAINT `fk_enderecoproprietario_proprietario1` FOREIGN KEY (`proprietario_idproprietario`) REFERENCES `proprietario` (`idproprietario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enderecoproprietario`
--

LOCK TABLES `enderecoproprietario` WRITE;
/*!40000 ALTER TABLE `enderecoproprietario` DISABLE KEYS */;
/*!40000 ALTER TABLE `enderecoproprietario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estoque`
--

DROP TABLE IF EXISTS `estoque`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estoque` (
  `idestoque` int(11) NOT NULL AUTO_INCREMENT,
  `produto_idproduto` int(11) NOT NULL,
  `dataentrada` date NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor` double(10,2) NOT NULL,
  `codigo` varchar(70) DEFAULT NULL,
  `descricao` text,
  PRIMARY KEY (`idestoque`),
  KEY `fk_estoque_produto1` (`produto_idproduto`),
  CONSTRAINT `fk_estoque_produto1` FOREIGN KEY (`produto_idproduto`) REFERENCES `produto` (`idproduto`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estoque`
--


--
-- Table structure for table `fornecedor`
--

DROP TABLE IF EXISTS `fornecedor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fornecedor` (
  `idfornecedor` int(11) NOT NULL AUTO_INCREMENT,
  `nomefantasia` varchar(45) DEFAULT NULL,
  `razaosocial` varchar(45) DEFAULT NULL,
  `telefone` varchar(45) DEFAULT NULL,
  `celular` varchar(45) DEFAULT NULL,
  `responsavel` varchar(45) DEFAULT NULL,
  `logradouro` varchar(45) DEFAULT NULL,
  `numero` varchar(45) DEFAULT NULL,
  `complemento` varchar(45) DEFAULT NULL,
  `bairro` varchar(45) DEFAULT NULL,
  `cep` varchar(45) DEFAULT NULL,
  `cidade` varchar(45) DEFAULT NULL,
  `estado` varchar(45) DEFAULT NULL,
  `descricao` varchar(45) DEFAULT NULL,
  `tipofornecedor_idtipofornecedor` int(11) NOT NULL,
  PRIMARY KEY (`idfornecedor`),
  KEY `fk_fornecedor_tipofornecedor1` (`tipofornecedor_idtipofornecedor`),
  CONSTRAINT `fk_fornecedor_tipofornecedor1` FOREIGN KEY (`tipofornecedor_idtipofornecedor`) REFERENCES `tipofornecedor` (`idtipofornecedor`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fornecedor`
--


--
-- Table structure for table `funcionario`
--

DROP TABLE IF EXISTS `funcionario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `funcionario` (
  `idfuncionario` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) NOT NULL,
  `email` varchar(50) NOT NULL,
  `sexo` char(1) NOT NULL,
  `datanascimento` date NOT NULL,
  `cpf` varchar(15) NOT NULL,
  `rg` varchar(45) NOT NULL,
  `dataadmissao` date NOT NULL,
  `salarioBruto` double(10,2) NOT NULL,
  `descvt` double(10,2) DEFAULT NULL,
  `vt` double(10,2) DEFAULT NULL,
  `descvr` double(10,2) DEFAULT NULL,
  `vr` double(10,2) DEFAULT NULL,
  `INSS` double(10,2) DEFAULT NULL,
  `outrosdescontos` double(10,2) DEFAULT NULL,
  `valoradicional` double(10,2) DEFAULT NULL,
  `isterceirizado` int(11) DEFAULT '0',
  `cargo_idcargo` int(11) NOT NULL,
  `usuario_idusuario` int(11) NOT NULL,
  PRIMARY KEY (`idfuncionario`),
  KEY `fk_funcionario_cargo1` (`cargo_idcargo`),
  KEY `fk_funcionario_usuario1` (`usuario_idusuario`),
  CONSTRAINT `fk_funcionario_cargo1` FOREIGN KEY (`cargo_idcargo`) REFERENCES `cargo` (`idcargo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_funcionario_usuario1` FOREIGN KEY (`usuario_idusuario`) REFERENCES `usuario` (`idusuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funcionario`
--

LOCK TABLES `funcionario` WRITE;
/*!40000 ALTER TABLE `funcionario` DISABLE KEYS */;
/*!40000 ALTER TABLE `funcionario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gasto`
--

DROP TABLE IF EXISTS `gasto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gasto` (
  `idgasto` int(11) NOT NULL AUTO_INCREMENT,
  `gasto` varchar(100) NOT NULL,
  `valor` double(10,2) NOT NULL,
  `obs` text,
  `datagasto` datetime NOT NULL,
  `funcionario_idfuncionario` int(11) NOT NULL,
  PRIMARY KEY (`idgasto`),
  KEY `fk_gasto_funcionario1` (`funcionario_idfuncionario`),
  CONSTRAINT `fk_gasto_funcionario1` FOREIGN KEY (`funcionario_idfuncionario`) REFERENCES `funcionario` (`idfuncionario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gasto`
--

LOCK TABLES `gasto` WRITE;
/*!40000 ALTER TABLE `gasto` DISABLE KEYS */;
/*!40000 ALTER TABLE `gasto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historicoentrada`
--

DROP TABLE IF EXISTS `historicoentrada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historicoentrada` (
  `idhistoricoentrada` int(11) NOT NULL AUTO_INCREMENT,
  `dataentrada` datetime NOT NULL,
  `visitante_idvisitante` int(11) DEFAULT NULL,
  PRIMARY KEY (`idhistoricoentrada`),
  KEY `visitante_idvisitante` (`visitante_idvisitante`),
  CONSTRAINT `historicoentrada_ibfk_1` FOREIGN KEY (`visitante_idvisitante`) REFERENCES `visitante` (`idvisitante`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historicoentrada`
--


--
-- Table structure for table `historicosindico`
--

DROP TABLE IF EXISTS `historicosindico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historicosindico` (
  `idhistoricosindico` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `inicio` datetime DEFAULT NULL,
  `fim` datetime DEFAULT NULL,
  `atual` int(11) NOT NULL,
  PRIMARY KEY (`idhistoricosindico`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historicosindico`
--



--
-- Table structure for table `itenslocacao`
--

DROP TABLE IF EXISTS `itenslocacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itenslocacao` (
  `iditenslocacao` int(11) NOT NULL AUTO_INCREMENT,
  `item` varchar(50) NOT NULL,
  `valor` double(10,2) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`iditenslocacao`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itenslocacao`
--

LOCK TABLES `itenslocacao` WRITE;
/*!40000 ALTER TABLE `itenslocacao` DISABLE KEYS */;
/*!40000 ALTER TABLE `itenslocacao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mensagem`
--

DROP TABLE IF EXISTS `mensagem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mensagem` (
  `idmensagem` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(45) NOT NULL,
  `texto` text,
  `data` date NOT NULL,
  `status` int(11) NOT NULL,
  `usuario_idusuario` int(11) NOT NULL,
  PRIMARY KEY (`idmensagem`),
  KEY `fk_mensagem_usuario1` (`usuario_idusuario`),
  CONSTRAINT `fk_mensagem_usuario1` FOREIGN KEY (`usuario_idusuario`) REFERENCES `usuario` (`idusuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensagem`
--

LOCK TABLES `mensagem` WRITE;
/*!40000 ALTER TABLE `mensagem` DISABLE KEYS */;
/*!40000 ALTER TABLE `mensagem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mensagemcondomino`
--

DROP TABLE IF EXISTS `mensagemcondomino`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mensagemcondomino` (
  `mensagem_idmensagem` int(11) NOT NULL,
  `condomino_idcondomino` int(11) NOT NULL,
  PRIMARY KEY (`mensagem_idmensagem`,`condomino_idcondomino`),
  KEY `fk_mensagemcondomino_mensagem1` (`mensagem_idmensagem`),
  KEY `fk_mensagemcondomino_condomino1` (`condomino_idcondomino`),
  CONSTRAINT `fk_mensagemcondomino_condomino1` FOREIGN KEY (`condomino_idcondomino`) REFERENCES `condomino` (`idcondomino`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_mensagemcondomino_mensagem1` FOREIGN KEY (`mensagem_idmensagem`) REFERENCES `mensagem` (`idmensagem`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensagemcondomino`
--

LOCK TABLES `mensagemcondomino` WRITE;
/*!40000 ALTER TABLE `mensagemcondomino` DISABLE KEYS */;
/*!40000 ALTER TABLE `mensagemcondomino` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mobile`
--

DROP TABLE IF EXISTS `mobile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mobile` (
  `idmobile` int(11) NOT NULL AUTO_INCREMENT,
  `chave` varchar(200) NOT NULL,
  `data` datetime NOT NULL,
  `usuario_idusuario` int(11) NOT NULL,
  PRIMARY KEY (`idmobile`),
  KEY `fk_mobile_usuario1` (`usuario_idusuario`),
  CONSTRAINT `fk_mobile_usuario1` FOREIGN KEY (`usuario_idusuario`) REFERENCES `usuario` (`idusuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mobile`
--

LOCK TABLES `mobile` WRITE;
/*!40000 ALTER TABLE `mobile` DISABLE KEYS */;
/*!40000 ALTER TABLE `mobile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `noticia`
--

DROP TABLE IF EXISTS `noticia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `noticia` (
  `idnoticia` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(60) NOT NULL,
  `descricao` text,
  `imagem1` varchar(100) DEFAULT NULL,
  `imagem2` varchar(100) DEFAULT NULL,
  `imagem3` varchar(100) DEFAULT NULL,
  `imagem4` varchar(100) DEFAULT NULL,
  `usuario_idusuario` int(11) NOT NULL,
  PRIMARY KEY (`idnoticia`),
  KEY `fk_noticia_usuario1` (`usuario_idusuario`),
  CONSTRAINT `fk_noticia_usuario1` FOREIGN KEY (`usuario_idusuario`) REFERENCES `usuario` (`idusuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `noticia`
--

LOCK TABLES `noticia` WRITE;
/*!40000 ALTER TABLE `noticia` DISABLE KEYS */;
/*!40000 ALTER TABLE `noticia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ocorrencia`
--

DROP TABLE IF EXISTS `ocorrencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ocorrencia` (
  `idocorrencia` int(11) NOT NULL AUTO_INCREMENT,
  `ocorrencia` varchar(50) NOT NULL,
  `tipo` varchar(45) NOT NULL,
  `descricao` text,
  `dataocorrencia` date NOT NULL,
  `usuario_reclamante` int(11) NOT NULL,
  PRIMARY KEY (`idocorrencia`),
  KEY `fk_ocorrencia_usuario1` (`usuario_reclamante`),
  CONSTRAINT `fk_ocorrencia_usuario1` FOREIGN KEY (`usuario_reclamante`) REFERENCES `usuario` (`idusuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ocorrencia`
--


--
-- Table structure for table `os`
--

DROP TABLE IF EXISTS `os`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `os` (
  `idos` int(11) NOT NULL AUTO_INCREMENT,
  `os` varchar(100) NOT NULL,
  `codigoos` varchar(50) NOT NULL,
  `data` date NOT NULL,
  `usuario_idusuario` int(11) NOT NULL,
  PRIMARY KEY (`idos`),
  KEY `fk_retirada_usuario1` (`usuario_idusuario`),
  CONSTRAINT `fk_retirada_usuario1` FOREIGN KEY (`usuario_idusuario`) REFERENCES `usuario` (`idusuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `os`
--


--
-- Table structure for table `pagamentocondominio`
--

DROP TABLE IF EXISTS `pagamentocondominio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pagamentocondominio` (
  `idpagamentocondominio` int(11) NOT NULL AUTO_INCREMENT,
  `mes` int(11) DEFAULT NULL,
  `ano` int(11) DEFAULT NULL,
  `pagamento` int(11) DEFAULT NULL,
  `valor` double NOT NULL,
  `desconto` double DEFAULT NULL,
  `observacao` text NOT NULL,
  `condomino_idcondomino` int(11) NOT NULL,
  `funcionario_idfuncionario` int(11) NOT NULL,
  PRIMARY KEY (`idpagamentocondominio`),
  KEY `fk_pagamentocondominio_condomino1` (`condomino_idcondomino`),
  KEY `fk_pagamentocondominio_funcionario1` (`funcionario_idfuncionario`),
  CONSTRAINT `fk_pagamentocondominio_condomino1` FOREIGN KEY (`condomino_idcondomino`) REFERENCES `condomino` (`idcondomino`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_pagamentocondominio_funcionario1` FOREIGN KEY (`funcionario_idfuncionario`) REFERENCES `funcionario` (`idfuncionario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagamentocondominio`
--

LOCK TABLES `pagamentocondominio` WRITE;
/*!40000 ALTER TABLE `pagamentocondominio` DISABLE KEYS */;
/*!40000 ALTER TABLE `pagamentocondominio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagamentofuncionario`
--

DROP TABLE IF EXISTS `pagamentofuncionario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pagamentofuncionario` (
  `idpagamentofuncionario` int(11) NOT NULL AUTO_INCREMENT,
  `valorPagamento` double(10,2) NOT NULL,
  `desconto` double(10,2) DEFAULT NULL,
  `horasextra` int(11) DEFAULT NULL,
  `descontodias` int(11) DEFAULT NULL,
  `datapagamento` date NOT NULL,
  `observacao` text,
  `funcionario_idfuncionario` int(11) NOT NULL,
  PRIMARY KEY (`idpagamentofuncionario`),
  KEY `fk_pagamentofuncionario_funcionario1` (`funcionario_idfuncionario`),
  CONSTRAINT `fk_pagamentofuncionario_funcionario1` FOREIGN KEY (`funcionario_idfuncionario`) REFERENCES `funcionario` (`idfuncionario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagamentofuncionario`
--

LOCK TABLES `pagamentofuncionario` WRITE;
/*!40000 ALTER TABLE `pagamentofuncionario` DISABLE KEYS */;
/*!40000 ALTER TABLE `pagamentofuncionario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perfil`
--

DROP TABLE IF EXISTS `perfil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perfil` (
  `idperfil` int(11) NOT NULL AUTO_INCREMENT,
  `perfil` varchar(45) NOT NULL,
  PRIMARY KEY (`idperfil`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perfil`
--

LOCK TABLES `perfil` WRITE;
/*!40000 ALTER TABLE `perfil` DISABLE KEYS */;
INSERT INTO `perfil` VALUES (1,'condomino'),(2,'administrador'),(3,'funcionario'),(4,'secretaria'),(5,'funcionarioadmin');
/*!40000 ALTER TABLE `perfil` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto`
--

DROP TABLE IF EXISTS `produto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produto` (
  `idproduto` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) NOT NULL,
  PRIMARY KEY (`idproduto`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto`
--

--
-- Table structure for table `proprietario`
--

DROP TABLE IF EXISTS `proprietario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proprietario` (
  `idproprietario` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) NOT NULL,
  `email` varchar(50) NOT NULL,
  `sexo` char(1) NOT NULL,
  `datanascimento` date DEFAULT NULL,
  `cpf` varchar(15) NOT NULL,
  `rg` varchar(20) DEFAULT NULL,
  `condomino_idcondomino` int(11) NOT NULL,
  PRIMARY KEY (`idproprietario`),
  KEY `fk_proprietario_condomino1` (`condomino_idcondomino`),
  CONSTRAINT `fk_proprietario_condomino1` FOREIGN KEY (`condomino_idcondomino`) REFERENCES `condomino` (`idcondomino`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proprietario`
--

LOCK TABLES `proprietario` WRITE;
/*!40000 ALTER TABLE `proprietario` DISABLE KEYS */;
/*!40000 ALTER TABLE `proprietario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `receita`
--

DROP TABLE IF EXISTS `receita`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `receita` (
  `idreceita` int(11) NOT NULL AUTO_INCREMENT,
  `receita` varchar(50) NOT NULL,
  `valor` double(10,2) NOT NULL,
  `obs` text,
  `datareceita` date NOT NULL,
  `funcionario_idfuncionario` int(11) NOT NULL,
  PRIMARY KEY (`idreceita`),
  KEY `fk_receita_funcionario1` (`funcionario_idfuncionario`),
  CONSTRAINT `fk_receita_funcionario1` FOREIGN KEY (`funcionario_idfuncionario`) REFERENCES `funcionario` (`idfuncionario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receita`
--

LOCK TABLES `receita` WRITE;
/*!40000 ALTER TABLE `receita` DISABLE KEYS */;
/*!40000 ALTER TABLE `receita` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reserva`
--

DROP TABLE IF EXISTS `reserva`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reserva` (
  `idreserva` int(11) NOT NULL AUTO_INCREMENT,
  `datafesta` date NOT NULL,
  `valor` double DEFAULT NULL,
  `pago` int(11) NOT NULL,
  `turno` varchar(45) NOT NULL,
  `realizada` int(11) DEFAULT NULL,
  `obs` text,
  `condomino_idcondomino` int(11) NOT NULL,
  `tipofesta_idtipofesta` int(11) NOT NULL,
  PRIMARY KEY (`idreserva`),
  KEY `fk_salaofestas_condomino` (`condomino_idcondomino`),
  KEY `fk_reserva_tipofesta1` (`tipofesta_idtipofesta`),
  CONSTRAINT `fk_reserva_tipofesta1` FOREIGN KEY (`tipofesta_idtipofesta`) REFERENCES `tipofesta` (`idtipofesta`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_salaofestas_condomino` FOREIGN KEY (`condomino_idcondomino`) REFERENCES `condomino` (`idcondomino`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reserva`
--


--
-- Table structure for table `reservaitenslocacao`
--

DROP TABLE IF EXISTS `reservaitenslocacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservaitenslocacao` (
  `reserva_idreserva` int(11) NOT NULL,
  `itenslocacao_iditenslocacao` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`reserva_idreserva`,`itenslocacao_iditenslocacao`),
  KEY `fk_reserva_has_itenslocacao_itenslocacao1` (`itenslocacao_iditenslocacao`),
  KEY `fk_reserva_has_itenslocacao_reserva1` (`reserva_idreserva`),
  CONSTRAINT `fk_reserva_has_itenslocacao_itenslocacao1` FOREIGN KEY (`itenslocacao_iditenslocacao`) REFERENCES `itenslocacao` (`iditenslocacao`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_reserva_has_itenslocacao_reserva1` FOREIGN KEY (`reserva_idreserva`) REFERENCES `reserva` (`idreserva`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservaitenslocacao`
--

LOCK TABLES `reservaitenslocacao` WRITE;
/*!40000 ALTER TABLE `reservaitenslocacao` DISABLE KEYS */;
/*!40000 ALTER TABLE `reservaitenslocacao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `respostas`
--

DROP TABLE IF EXISTS `respostas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `respostas` (
  `idrespostas` int(11) NOT NULL AUTO_INCREMENT,
  `resposta` varchar(50) NOT NULL,
  `votacao_idvotacao` int(11) NOT NULL,
  PRIMARY KEY (`idrespostas`),
  KEY `fk_respostas_votacao1` (`votacao_idvotacao`),
  CONSTRAINT `fk_respostas_votacao1` FOREIGN KEY (`votacao_idvotacao`) REFERENCES `votacao` (`idvotacao`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `respostas`
--

LOCK TABLES `respostas` WRITE;
/*!40000 ALTER TABLE `respostas` DISABLE KEYS */;
/*!40000 ALTER TABLE `respostas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `retirada`
--

DROP TABLE IF EXISTS `retirada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `retirada` (
  `idretirada` int(11) NOT NULL AUTO_INCREMENT,
  `dataretirada` date NOT NULL,
  `quantidade` int(11) NOT NULL,
  `estoque_idestoque` int(11) NOT NULL,
  `os_idos` int(11) NOT NULL,
  PRIMARY KEY (`idretirada`),
  KEY `fk_retirada_estoque1` (`estoque_idestoque`),
  KEY `fk_retirada_os1` (`os_idos`),
  CONSTRAINT `fk_retirada_estoque1` FOREIGN KEY (`estoque_idestoque`) REFERENCES `estoque` (`idestoque`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_retirada_os1` FOREIGN KEY (`os_idos`) REFERENCES `os` (`idos`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `retirada`
--

LOCK TABLES `retirada` WRITE;
/*!40000 ALTER TABLE `retirada` DISABLE KEYS */;
/*!40000 ALTER TABLE `retirada` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `telefone`
--

DROP TABLE IF EXISTS `telefone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `telefone` (
  `idtelefone` int(11) NOT NULL AUTO_INCREMENT,
  `ddd` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `condomino_idcondomino` int(11) NOT NULL,
  PRIMARY KEY (`idtelefone`),
  KEY `fk_telefone_condomino1` (`condomino_idcondomino`),
  CONSTRAINT `fk_telefone_condomino1` FOREIGN KEY (`condomino_idcondomino`) REFERENCES `condomino` (`idcondomino`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telefone`
--

LOCK TABLES `telefone` WRITE;
/*!40000 ALTER TABLE `telefone` DISABLE KEYS */;
/*!40000 ALTER TABLE `telefone` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `telefonefuncionario`
--

DROP TABLE IF EXISTS `telefonefuncionario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `telefonefuncionario` (
  `idtelefonefuncionario` int(11) NOT NULL AUTO_INCREMENT,
  `ddd` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `funcionario_idfuncionario` int(11) NOT NULL,
  PRIMARY KEY (`idtelefonefuncionario`),
  KEY `fk_telefonefuncionario_funcionario1` (`funcionario_idfuncionario`),
  CONSTRAINT `fk_telefonefuncionario_funcionario1` FOREIGN KEY (`funcionario_idfuncionario`) REFERENCES `funcionario` (`idfuncionario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telefonefuncionario`
--

LOCK TABLES `telefonefuncionario` WRITE;
/*!40000 ALTER TABLE `telefonefuncionario` DISABLE KEYS */;
/*!40000 ALTER TABLE `telefonefuncionario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `telefoneproprietario`
--

DROP TABLE IF EXISTS `telefoneproprietario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `telefoneproprietario` (
  `idtelefoneproprietario` int(11) NOT NULL AUTO_INCREMENT,
  `ddd` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `proprietario_idproprietario` int(11) NOT NULL,
  PRIMARY KEY (`idtelefoneproprietario`),
  KEY `fk_telefoneproprietario_proprietario1` (`proprietario_idproprietario`),
  CONSTRAINT `fk_telefoneproprietario_proprietario1` FOREIGN KEY (`proprietario_idproprietario`) REFERENCES `proprietario` (`idproprietario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telefoneproprietario`
--

LOCK TABLES `telefoneproprietario` WRITE;
/*!40000 ALTER TABLE `telefoneproprietario` DISABLE KEYS */;
/*!40000 ALTER TABLE `telefoneproprietario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipofesta`
--

DROP TABLE IF EXISTS `tipofesta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipofesta` (
  `idtipofesta` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(50) NOT NULL,
  `valor` double(10,2) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idtipofesta`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipofesta`
--


--
-- Table structure for table `tipofornecedor`
--

DROP TABLE IF EXISTS `tipofornecedor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipofornecedor` (
  `idtipofornecedor` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(45) NOT NULL,
  PRIMARY KEY (`idtipofornecedor`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipofornecedor`
--


--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario` (
  `idusuario` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `senha` varchar(120) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `perfil_idperfil` int(11) NOT NULL,
  PRIMARY KEY (`idusuario`),
  UNIQUE KEY `login_UNIQUE` (`login`),
  KEY `fk_usuario_perfil1` (`perfil_idperfil`),
  CONSTRAINT `fk_usuario_perfil1` FOREIGN KEY (`perfil_idperfil`) REFERENCES `perfil` (`idperfil`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--


--
-- Table structure for table `usuarioarquivos`
--

DROP TABLE IF EXISTS `usuarioarquivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarioarquivos` (
  `usuario_idusuario` int(11) NOT NULL DEFAULT '0',
  `arquivos_idarquivos` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`usuario_idusuario`,`arquivos_idarquivos`),
  KEY `arquivos_idarquivos` (`arquivos_idarquivos`),
  CONSTRAINT `usuarioarquivos_ibfk_1` FOREIGN KEY (`usuario_idusuario`) REFERENCES `usuario` (`idusuario`),
  CONSTRAINT `usuarioarquivos_ibfk_2` FOREIGN KEY (`arquivos_idarquivos`) REFERENCES `arquivos` (`idarquivos`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarioarquivos`


--
-- Table structure for table `usuariovotacao`
--

DROP TABLE IF EXISTS `usuariovotacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuariovotacao` (
  `idusuariovotacao` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_idusuario` int(11) NOT NULL,
  `respostas_idrespostas` int(11) NOT NULL,
  PRIMARY KEY (`idusuariovotacao`),
  KEY `fk_usuariovotacao_usuario1` (`usuario_idusuario`),
  KEY `fk_usuariovotacao_respostas1` (`respostas_idrespostas`),
  CONSTRAINT `fk_usuariovotacao_respostas1` FOREIGN KEY (`respostas_idrespostas`) REFERENCES `respostas` (`idrespostas`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_usuariovotacao_usuario1` FOREIGN KEY (`usuario_idusuario`) REFERENCES `usuario` (`idusuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuariovotacao`
--

LOCK TABLES `usuariovotacao` WRITE;
/*!40000 ALTER TABLE `usuariovotacao` DISABLE KEYS */;
/*!40000 ALTER TABLE `usuariovotacao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vagacondomino`
--

DROP TABLE IF EXISTS `vagacondomino`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vagacondomino` (
  `condomino_idcondomino` int(11) NOT NULL,
  `vagas_idvagas` int(11) NOT NULL,
  `placa` varchar(45) NOT NULL,
  `descricao` text,
  PRIMARY KEY (`condomino_idcondomino`,`vagas_idvagas`),
  UNIQUE KEY `vagas_idvagas_UNIQUE` (`vagas_idvagas`),
  KEY `fk_vagacondomino_condomino1` (`condomino_idcondomino`),
  KEY `fk_vagacondomino_vagas1` (`vagas_idvagas`),
  CONSTRAINT `fk_vagacondomino_condomino1` FOREIGN KEY (`condomino_idcondomino`) REFERENCES `condomino` (`idcondomino`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_vagacondomino_vagas1` FOREIGN KEY (`vagas_idvagas`) REFERENCES `vagas` (`idvagas`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vagacondomino`
--

LOCK TABLES `vagacondomino` WRITE;
/*!40000 ALTER TABLE `vagacondomino` DISABLE KEYS */;
/*!40000 ALTER TABLE `vagacondomino` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vagas`
--

DROP TABLE IF EXISTS `vagas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vagas` (
  `idvagas` int(11) NOT NULL AUTO_INCREMENT,
  `vaga` varchar(45) NOT NULL,
  `descricao` text,
  PRIMARY KEY (`idvagas`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vagas`
--

LOCK TABLES `vagas` WRITE;
/*!40000 ALTER TABLE `vagas` DISABLE KEYS */;
/*!40000 ALTER TABLE `vagas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visitante`
--

DROP TABLE IF EXISTS `visitante`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visitante` (
  `idvisitante` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `cpf` varchar(45) NOT NULL,
  `rg` varchar(45) DEFAULT NULL,
  `telefone` varchar(50) DEFAULT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `usuario_idusuario` int(11) NOT NULL,
  `foto` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`idvisitante`),
  KEY `fk_visitante_usuario1` (`usuario_idusuario`),
  CONSTRAINT `fk_visitante_usuario1` FOREIGN KEY (`usuario_idusuario`) REFERENCES `usuario` (`idusuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visitante`
--


--
-- Table structure for table `votacao`
--

DROP TABLE IF EXISTS `votacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `votacao` (
  `idvotacao` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(60) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`idvotacao`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `votacao`
--

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-01-16 19:35:16
