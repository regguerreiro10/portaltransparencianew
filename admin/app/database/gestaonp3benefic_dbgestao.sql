-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 24/02/2026 às 15:26
-- Versão do servidor: 10.6.24-MariaDB
-- Versão do PHP: 8.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `gestaonp3benefic_dbgestao`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `administradora`
--

CREATE TABLE `administradora` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) DEFAULT NULL,
  `cnpj` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `rua` varchar(500) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `bairro` varchar(500) DEFAULT NULL,
  `complemento` varchar(500) DEFAULT NULL,
  `cidade_id` int(11) DEFAULT NULL,
  `telefone01` varchar(255) DEFAULT NULL,
  `telefone02` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `cd_grupo` int(11) DEFAULT NULL,
  `de_login_usu` varchar(50) DEFAULT NULL,
  `de_senha_usu` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `alerta_program`
--

CREATE TABLE `alerta_program` (
  `id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `system_program_id` int(11) DEFAULT NULL,
  `mensagem` text DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `entidade_id` int(11) DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `anexos_seguros`
--

CREATE TABLE `anexos_seguros` (
  `id` int(11) NOT NULL,
  `seguros_id` int(11) DEFAULT NULL,
  `caminho` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `anexos_veiculo`
--

CREATE TABLE `anexos_veiculo` (
  `id` int(11) NOT NULL,
  `descricao` varchar(500) DEFAULT NULL,
  `veiculos_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `api_error`
--

CREATE TABLE `api_error` (
  `id` int(11) NOT NULL,
  `classe` varchar(255) DEFAULT NULL,
  `metodo` varchar(255) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `dados` varchar(3000) DEFAULT NULL,
  `error_message` varchar(3000) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `aprovador`
--

CREATE TABLE `aprovador` (
  `id` int(11) NOT NULL,
  `system_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `aprovador_frotas`
--

CREATE TABLE `aprovador_frotas` (
  `id` int(11) NOT NULL,
  `system_users_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `autorizacao_pedido`
--

CREATE TABLE `autorizacao_pedido` (
  `id` int(11) NOT NULL,
  `pedido_frotas_id` int(11) DEFAULT NULL,
  `veiculos_id` int(11) DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `data_autorizacao` date DEFAULT NULL,
  `historico` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `backup_itens_pedido_frotas_diff`
--

CREATE TABLE `backup_itens_pedido_frotas_diff` (
  `id` int(11) NOT NULL DEFAULT 0,
  `pedido_frotas_id` int(11) DEFAULT NULL,
  `tipo` int(11) DEFAULT NULL,
  `qtde` double DEFAULT NULL,
  `descricao` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `valor_unitario` double DEFAULT NULL,
  `valor_desconto` double DEFAULT NULL,
  `valor_total` double DEFAULT NULL,
  `marca_modelo` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fabricante` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `codigo` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `qtdekmgarantia` int(11) DEFAULT NULL,
  `diasdegarantia` int(11) DEFAULT NULL,
  `qtdehoras` int(11) DEFAULT NULL,
  `perc_desconto` double DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `produto_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cartao`
--

CREATE TABLE `cartao` (
  `id` int(11) NOT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `numero_cartao` varchar(200) DEFAULT NULL,
  `data_validade` date DEFAULT NULL,
  `data_emissao` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `tipo_conta_id` int(11) NOT NULL,
  `nome` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria_cliente`
--

CREATE TABLE `categoria_cliente` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria_cnh`
--

CREATE TABLE `categoria_cnh` (
  `id` int(11) NOT NULL,
  `descricao` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `centrocusto`
--

CREATE TABLE `centrocusto` (
  `id` int(11) NOT NULL,
  `nome` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cep_cache`
--

CREATE TABLE `cep_cache` (
  `id` int(11) NOT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `rua` varchar(255) DEFAULT NULL,
  `cidade` varchar(500) DEFAULT NULL,
  `bairro` varchar(500) DEFAULT NULL,
  `codigo_ibge` varchar(20) DEFAULT NULL,
  `uf` varchar(2) DEFAULT NULL,
  `cidade_id` int(11) DEFAULT NULL,
  `estado_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cidade`
--

CREATE TABLE `cidade` (
  `id` int(11) NOT NULL,
  `estado_id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `codigo_ibge` varchar(10) NOT NULL,
  `idold` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cidade_pedido`
--

CREATE TABLE `cidade_pedido` (
  `id` int(11) NOT NULL,
  `cidade_id` int(11) DEFAULT NULL,
  `pedido_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cidade_pedido_frotas`
--

CREATE TABLE `cidade_pedido_frotas` (
  `id` int(11) NOT NULL,
  `pedido_frotas_id` int(11) DEFAULT NULL,
  `cidade_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `comentario_proposta`
--

CREATE TABLE `comentario_proposta` (
  `id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `propostas_id` int(11) DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `leitura_dt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `condicao_pagamento`
--

CREATE TABLE `condicao_pagamento` (
  `id` int(11) NOT NULL,
  `nome` text NOT NULL,
  `numero_parcelas` int(11) DEFAULT NULL,
  `inicio` int(11) DEFAULT NULL,
  `intervalo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `condutor`
--

CREATE TABLE `condutor` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `cpf` varchar(50) DEFAULT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `cnh` varchar(50) DEFAULT NULL,
  `categoria` char(2) DEFAULT NULL,
  `numero_dispositivo` varchar(20) DEFAULT NULL,
  `codigo_patrimonio` varchar(20) DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `condutores_daeb`
--

CREATE TABLE `condutores_daeb` (
  `Nome` varchar(34) DEFAULT NULL,
  `Registro` int(6) DEFAULT NULL,
  `Unidade` varchar(4) DEFAULT NULL,
  `CNH` varchar(11) DEFAULT NULL,
  `Categoria` varchar(2) DEFAULT NULL,
  `CPF` varchar(14) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `conta`
--

CREATE TABLE `conta` (
  `id` int(11) NOT NULL,
  `pessoa_id` int(11) DEFAULT NULL,
  `tipo_conta_id` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `forma_pagamento_id` int(11) NOT NULL,
  `pedido_venda_id` int(11) DEFAULT NULL,
  `dt_vencimento` date DEFAULT NULL,
  `dt_emissao` date DEFAULT NULL,
  `dt_pagamento` date DEFAULT NULL,
  `valor` double DEFAULT NULL,
  `valor_txcontrato` double DEFAULT NULL,
  `txadm` double DEFAULT NULL,
  `valor_txadm` double DEFAULT NULL,
  `valor_txbancaria` double DEFAULT NULL,
  `txantecipacao` double DEFAULT NULL,
  `valor_txantecipacao` double DEFAULT NULL,
  `valor_produto_s_desc_txc` double DEFAULT NULL,
  `valor_servico_s_desc_txc` double DEFAULT NULL,
  `valor_liquido` double DEFAULT NULL,
  `valor_produto_c_desc_txc` double DEFAULT NULL,
  `valor_servico_c_desc_txc` double DEFAULT NULL,
  `ir` double DEFAULT NULL,
  `cofins` double DEFAULT NULL,
  `csll` double DEFAULT NULL,
  `pis` double DEFAULT NULL,
  `ir_servico` double DEFAULT NULL,
  `cofins_servico` double DEFAULT NULL,
  `csll_servico` double DEFAULT NULL,
  `pis_servico` double DEFAULT NULL,
  `iss_servico` double DEFAULT NULL,
  `valor_liqbase_prod_posimp` double DEFAULT NULL,
  `valor_liqbase_serv_posimp` double DEFAULT NULL,
  `valor_txc_imp_produto_servico` double DEFAULT NULL,
  `valor_total_liq_tx_conta` double DEFAULT NULL,
  `parcela` int(11) DEFAULT NULL,
  `obs` text DEFAULT NULL,
  `mes_vencimento` int(11) DEFAULT NULL,
  `ano_vencimento` int(11) DEFAULT NULL,
  `ano_mes_vencimento` int(11) DEFAULT NULL,
  `mes_emissao` int(11) DEFAULT NULL,
  `ano_emissao` int(11) DEFAULT NULL,
  `ano_mes_emissao` int(11) DEFAULT NULL,
  `mes_pagamento` int(11) DEFAULT NULL,
  `ano_pagamento` int(11) DEFAULT NULL,
  `ano_mes_pagamento` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `descricao` varchar(200) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `pedido_frotas_id` int(11) DEFAULT NULL,
  `entidade_id` int(11) DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `fatura_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `conta_anexo`
--

CREATE TABLE `conta_anexo` (
  `id` int(11) NOT NULL,
  `conta_id` int(11) NOT NULL,
  `tipo_anexo_id` int(11) NOT NULL,
  `descricao` text DEFAULT NULL,
  `arquivo` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `conta_thallita`
--

CREATE TABLE `conta_thallita` (
  `id` int(11) NOT NULL,
  `pessoa_id` int(11) DEFAULT NULL,
  `tipo_conta_id` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `forma_pagamento_id` int(11) NOT NULL,
  `pedido_venda_id` int(11) DEFAULT NULL,
  `dt_vencimento` date DEFAULT NULL,
  `dt_emissao` date DEFAULT NULL,
  `dt_pagamento` date DEFAULT NULL,
  `valor` double DEFAULT NULL,
  `valor_txcontrato` double DEFAULT NULL,
  `valor_txadm` double DEFAULT NULL,
  `valor_txbancaria` double DEFAULT NULL,
  `valor_txantecipacao` double DEFAULT NULL,
  `valor_liquido` double DEFAULT NULL,
  `parcela` int(11) DEFAULT NULL,
  `obs` text DEFAULT NULL,
  `mes_vencimento` int(11) DEFAULT NULL,
  `ano_vencimento` int(11) DEFAULT NULL,
  `ano_mes_vencimento` int(11) DEFAULT NULL,
  `mes_emissao` int(11) DEFAULT NULL,
  `ano_emissao` int(11) DEFAULT NULL,
  `ano_mes_emissao` int(11) DEFAULT NULL,
  `mes_pagamento` int(11) DEFAULT NULL,
  `ano_pagamento` int(11) DEFAULT NULL,
  `ano_mes_pagamento` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `descricao` varchar(200) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `pedido_frotas_id` int(11) DEFAULT NULL,
  `entidade_id` int(11) DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `fatura_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `corveiculo`
--

CREATE TABLE `corveiculo` (
  `id` int(11) NOT NULL,
  `descricao` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cotacao`
--

CREATE TABLE `cotacao` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `pessoa_id` int(11) DEFAULT NULL,
  `estado_pedido_id` int(11) DEFAULT NULL,
  `data_cotacao` date DEFAULT NULL,
  `obs` varchar(500) DEFAULT NULL,
  `valor_total` double DEFAULT NULL,
  `valor_desconto` double DEFAULT NULL,
  `valor_liquido` double DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `cidade_id` int(11) DEFAULT NULL,
  `txadm` double DEFAULT NULL,
  `txbancaria` double DEFAULT NULL,
  `txantecipacao` double DEFAULT NULL,
  `txcontrato` double DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `entidade_id` int(11) DEFAULT NULL,
  `estado_pedido1_id` int(11) DEFAULT NULL,
  `data_limite_resposta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cotacao_historico`
--

CREATE TABLE `cotacao_historico` (
  `id` int(11) NOT NULL,
  `estado_pedido_id` int(11) DEFAULT NULL,
  `data_historico` date DEFAULT NULL,
  `obs` varchar(500) DEFAULT NULL,
  `aprovador_id` int(11) DEFAULT NULL,
  `cotacao_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `departamento_unit`
--

CREATE TABLE `departamento_unit` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `name_old` varchar(255) DEFAULT NULL,
  `email` varchar(500) DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `valor_empenho` double DEFAULT NULL,
  `rua` varchar(500) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `bairro` varchar(500) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `cidade_id` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `idold` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `dispositivos`
--

CREATE TABLE `dispositivos` (
  `id` int(11) NOT NULL,
  `descricao` varchar(200) DEFAULT NULL,
  `imagem` varchar(500) DEFAULT NULL,
  `tipo_finalidade_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `dispositivos_solicitados`
--

CREATE TABLE `dispositivos_solicitados` (
  `id` int(11) NOT NULL,
  `numerocartao` int(11) DEFAULT NULL,
  `datasolicitacao` date DEFAULT NULL,
  `horasolicitacao` datetime DEFAULT NULL,
  `dispositivos_id` int(11) DEFAULT NULL,
  `veiculos_id` int(11) DEFAULT NULL,
  `status_dispositivos_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `via` int(11) DEFAULT NULL,
  `rastreio` varchar(100) DEFAULT NULL,
  `coringa` char(1) DEFAULT NULL,
  `pessoa_id` int(11) DEFAULT NULL,
  `saldo_atual` double DEFAULT NULL,
  `saldo_limite` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos_cotacao`
--

CREATE TABLE `documentos_cotacao` (
  `id` int(11) NOT NULL,
  `cotacao_id` int(11) DEFAULT NULL,
  `caminho` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos_pedido`
--

CREATE TABLE `documentos_pedido` (
  `id` int(11) NOT NULL,
  `caminho` varchar(500) DEFAULT NULL,
  `pedido_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos_pedido_frotas`
--

CREATE TABLE `documentos_pedido_frotas` (
  `id` int(11) NOT NULL,
  `caminho` varchar(500) DEFAULT NULL,
  `pedido_frotas_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos_pessoa`
--

CREATE TABLE `documentos_pessoa` (
  `id` int(11) NOT NULL,
  `caminho` text DEFAULT NULL,
  `pessoa_id` int(11) DEFAULT NULL,
  `tipo_documento_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos_propostas`
--

CREATE TABLE `documentos_propostas` (
  `id` int(11) NOT NULL,
  `propostas_id` int(11) DEFAULT NULL,
  `caminho` varchar(500) DEFAULT NULL,
  `numero` varchar(30) DEFAULT NULL,
  `tipo_documentos_propostas_id` int(11) DEFAULT NULL,
  `valor` double DEFAULT NULL,
  `estabelecimento_idold` int(11) DEFAULT NULL,
  `cliente_idold` int(11) DEFAULT NULL,
  `proposta_idold` int(11) DEFAULT NULL,
  `pedido_idold` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documento_autorizacao_pedido`
--

CREATE TABLE `documento_autorizacao_pedido` (
  `id` int(11) NOT NULL,
  `caminho` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `autorizacao_pedido_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `dotacao_pedido_frotas`
--

CREATE TABLE `dotacao_pedido_frotas` (
  `id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `pedido_frotas_id` int(11) DEFAULT NULL,
  `saldo_departamento_id` int(11) DEFAULT NULL,
  `valor` double DEFAULT NULL,
  `saldo_atual` double DEFAULT NULL,
  `propostas_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `email_template`
--

CREATE TABLE `email_template` (
  `id` int(11) NOT NULL,
  `titulo` text DEFAULT NULL,
  `mensagem` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `entidade`
--

CREATE TABLE `entidade` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) DEFAULT NULL,
  `cnpj` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `rua` varchar(500) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `bairro` varchar(500) DEFAULT NULL,
  `complemento` varchar(500) DEFAULT NULL,
  `cidade_id` int(11) DEFAULT NULL,
  `telefone01` varchar(255) DEFAULT NULL,
  `telefone02` varchar(255) DEFAULT NULL,
  `longitude` double(10,6) DEFAULT NULL,
  `latitude` double(10,6) DEFAULT NULL,
  `administradora_id` int(11) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `frotas` tinyint(1) DEFAULT NULL,
  `compras` tinyint(1) DEFAULT NULL,
  `taxacontrato` double DEFAULT NULL,
  `tipo_frota` char(1) DEFAULT NULL,
  `numero_documento` varchar(500) DEFAULT NULL,
  `numero_processo` varchar(500) DEFAULT NULL,
  `abastecimento` tinyint(4) DEFAULT NULL,
  `idold` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `error_log_crontab`
--

CREATE TABLE `error_log_crontab` (
  `id` int(11) NOT NULL,
  `classe` text DEFAULT NULL,
  `metodo` text DEFAULT NULL,
  `mensagem` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `especie`
--

CREATE TABLE `especie` (
  `id` int(11) NOT NULL,
  `descricao` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estado`
--

CREATE TABLE `estado` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `sigla` char(2) NOT NULL,
  `codigo_ibge` varchar(10) DEFAULT NULL,
  `idold` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estado_pedido`
--

CREATE TABLE `estado_pedido` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `cor` varchar(100) DEFAULT NULL,
  `kanban` char(1) DEFAULT NULL,
  `ordem` int(11) DEFAULT NULL,
  `estado_final` char(1) DEFAULT NULL,
  `estado_inicial` char(1) DEFAULT NULL,
  `permite_edicao` char(1) DEFAULT NULL,
  `permite_exclusao` char(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estado_pedido_aprovador`
--

CREATE TABLE `estado_pedido_aprovador` (
  `id` int(11) NOT NULL,
  `estado_pedido_venda_id` int(11) NOT NULL,
  `aprovador_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estado_pedido_frotas`
--

CREATE TABLE `estado_pedido_frotas` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `cor` varchar(255) DEFAULT NULL,
  `kanban` char(1) DEFAULT NULL,
  `ordem` int(11) DEFAULT NULL,
  `estado_final` char(1) DEFAULT NULL,
  `estado_inicial` char(1) DEFAULT NULL,
  `permite_edicao` char(1) DEFAULT NULL,
  `permite_exclusao` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estado_pedido_frotas_aprovador`
--

CREATE TABLE `estado_pedido_frotas_aprovador` (
  `id` int(11) NOT NULL,
  `aprovador_frotas_id` int(11) DEFAULT NULL,
  `estado_pedido_frotas_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estado_pedido_venda`
--

CREATE TABLE `estado_pedido_venda` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `cor` varchar(100) DEFAULT NULL,
  `kanban` char(1) DEFAULT NULL,
  `ordem` int(11) DEFAULT NULL,
  `estado_final` char(1) DEFAULT NULL,
  `estado_inicial` char(1) DEFAULT NULL,
  `permite_edicao` char(1) DEFAULT NULL,
  `permite_exclusao` char(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `etapa_negociacao`
--

CREATE TABLE `etapa_negociacao` (
  `id` int(11) NOT NULL,
  `nome` text DEFAULT NULL,
  `cor` text DEFAULT NULL,
  `ordem` int(11) DEFAULT NULL,
  `roteiro` text DEFAULT NULL,
  `kanban` char(1) DEFAULT NULL,
  `permite_edicao` char(1) DEFAULT NULL,
  `permite_exclusao` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fabricante`
--

CREATE TABLE `fabricante` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `familia`
--

CREATE TABLE `familia` (
  `id` int(11) NOT NULL,
  `descricao` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `familia_produto`
--

CREATE TABLE `familia_produto` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `suiv_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fatura`
--

CREATE TABLE `fatura` (
  `id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `periodo_apuracao_inicial` date DEFAULT NULL,
  `periodo_apuracao_final` date DEFAULT NULL,
  `data_vencimento` date DEFAULT NULL,
  `totalgeral` double DEFAULT NULL,
  `totalservico` double DEFAULT NULL,
  `totalproduto` double DEFAULT NULL,
  `desconto` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `forma_pagamento_id` int(11) DEFAULT NULL,
  `numero_fatura` varchar(10) DEFAULT NULL,
  `obs` int(11) DEFAULT NULL,
  `data_emissao` datetime DEFAULT NULL,
  `data_pagamento` date DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `forma_pagamento`
--

CREATE TABLE `forma_pagamento` (
  `id` int(11) NOT NULL,
  `nome` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fotos_veiculos`
--

CREATE TABLE `fotos_veiculos` (
  `id` int(11) NOT NULL,
  `veiculos_id` int(11) DEFAULT NULL,
  `caminho` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `grupo_pessoa`
--

CREATE TABLE `grupo_pessoa` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_cotacao`
--

CREATE TABLE `itens_cotacao` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `qtde` int(11) DEFAULT NULL,
  `valor` double DEFAULT NULL,
  `valor_total` double DEFAULT NULL,
  `valor_sinapi` double DEFAULT NULL,
  `cotacao_id` int(11) DEFAULT NULL,
  `estado_pedido_id` int(11) DEFAULT NULL,
  `itens_pedido_id` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `unidade_medida_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_pedido`
--

CREATE TABLE `itens_pedido` (
  `id` int(11) NOT NULL,
  `pedido_venda_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` double DEFAULT NULL,
  `valor` double DEFAULT NULL,
  `desconto` double DEFAULT NULL,
  `valor_total` double DEFAULT NULL,
  `obs` varchar(500) DEFAULT NULL,
  `valor_cotacao` double DEFAULT NULL,
  `valor_cotacao_total` double DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `unidade_medida_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_pedido_frotas`
--

CREATE TABLE `itens_pedido_frotas` (
  `id` int(11) NOT NULL,
  `pedido_frotas_id` int(11) DEFAULT NULL,
  `tipo` int(11) DEFAULT NULL,
  `qtde` double DEFAULT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `valor_unitario` double DEFAULT NULL,
  `valor_desconto` double DEFAULT NULL,
  `valor_total` double DEFAULT NULL,
  `marca_modelo` varchar(255) DEFAULT NULL,
  `fabricante` varchar(255) DEFAULT NULL,
  `codigo` varchar(255) DEFAULT NULL,
  `qtdekmgarantia` int(11) DEFAULT NULL,
  `diasdegarantia` int(11) DEFAULT NULL,
  `qtdehoras` int(11) DEFAULT NULL,
  `perc_desconto` double DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `tbo_horas` decimal(18,3) DEFAULT NULL,
  `tbo_ciclos` decimal(18,3) DEFAULT NULL,
  `tsn_horas` decimal(18,3) DEFAULT NULL,
  `tso_horas` decimal(18,3) DEFAULT NULL,
  `csn_ciclos` decimal(18,3) DEFAULT NULL,
  `cso_ciclos` decimal(18,3) DEFAULT NULL,
  `familia_produto_id` int(11) DEFAULT NULL,
  `uso` text DEFAULT NULL,
  `finalidade` text DEFAULT NULL,
  `aplicacao` text DEFAULT NULL,
  `idold` int(11) DEFAULT NULL,
  `tipo_old` int(11) DEFAULT NULL,
  `pedido_idold` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_propostas`
--

CREATE TABLE `itens_propostas` (
  `id` int(11) NOT NULL,
  `propostas_id` int(11) NOT NULL,
  `tipo` int(11) DEFAULT NULL,
  `descricao` varchar(200) DEFAULT NULL,
  `qtdekmgarantia` int(11) DEFAULT NULL,
  `diasdegarantia` int(11) DEFAULT NULL,
  `qtdehoras` int(11) DEFAULT NULL,
  `valor` decimal(18,2) DEFAULT NULL,
  `perc_desconto` decimal(18,2) DEFAULT NULL,
  `valor_total` decimal(18,2) DEFAULT NULL,
  `marca_modelo` varchar(255) DEFAULT NULL,
  `fabricante` varchar(255) DEFAULT NULL,
  `codigo` varchar(255) DEFAULT NULL,
  `itens_pedido_frotas_id` int(11) DEFAULT NULL,
  `estado_pedido_frotas_id` int(11) DEFAULT NULL,
  `tipo_pecas_id` int(11) DEFAULT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `tbo_horas` decimal(18,3) DEFAULT NULL,
  `tbo_ciclos` decimal(18,3) DEFAULT NULL,
  `tsn_horas` decimal(18,3) DEFAULT NULL,
  `tso_horas` decimal(18,3) DEFAULT NULL,
  `csn_ciclos` decimal(18,3) DEFAULT NULL,
  `cso_ciclos` decimal(18,3) DEFAULT NULL,
  `familia_produto_id` int(11) DEFAULT NULL,
  `valor_old` double DEFAULT NULL,
  `perc_desconto_old` double DEFAULT NULL,
  `valor_total_old` double DEFAULT NULL,
  `uso` text DEFAULT NULL,
  `finalidade` text DEFAULT NULL,
  `aplicacao` text DEFAULT NULL,
  `qtde` double DEFAULT NULL,
  `idold` int(11) DEFAULT NULL,
  `pedido_idold` int(11) DEFAULT NULL,
  `propostas_idold` int(11) DEFAULT NULL,
  `tipo_old` int(11) DEFAULT NULL,
  `item_idold` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `manutencao_garantia`
--

CREATE TABLE `manutencao_garantia` (
  `id` int(11) NOT NULL,
  `itens_propostas_id` int(11) DEFAULT NULL,
  `veiculos_id` int(11) DEFAULT NULL,
  `pedido_frotas_id` int(11) DEFAULT NULL,
  `propostas_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `tipo` int(11) DEFAULT NULL,
  `km_manutencao` int(11) DEFAULT NULL,
  `dias_garantia` int(11) DEFAULT NULL,
  `datagarantia` date DEFAULT NULL,
  `descricao` varchar(160) DEFAULT NULL,
  `obs` text DEFAULT NULL,
  `ativo` char(1) DEFAULT NULL,
  `qtde` double DEFAULT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `ciclos_manutencao` decimal(18,3) DEFAULT NULL,
  `tbo_horas` decimal(18,3) DEFAULT NULL,
  `tbo_ciclos` decimal(18,3) DEFAULT NULL,
  `tsn_horas` decimal(18,3) DEFAULT NULL,
  `tso_horas` decimal(18,3) DEFAULT NULL,
  `csn_ciclos` decimal(18,3) DEFAULT NULL,
  `cso_ciclos` decimal(18,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `marca`
--

CREATE TABLE `marca` (
  `id` int(11) NOT NULL,
  `descricao` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `matriz_estado_pedido`
--

CREATE TABLE `matriz_estado_pedido` (
  `id` int(11) NOT NULL,
  `estado_pedido_venda_origem_id` int(11) NOT NULL,
  `estado_pedido_venda_destino_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `matriz_estado_pedido_frotas`
--

CREATE TABLE `matriz_estado_pedido_frotas` (
  `id` int(11) NOT NULL,
  `estado_pedido_frotas_origem_id` int(11) DEFAULT NULL,
  `estado_pedido_frotas_destino_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `modelo`
--

CREATE TABLE `modelo` (
  `id` int(11) NOT NULL,
  `marca_id` int(11) DEFAULT NULL,
  `descricao` varchar(500) DEFAULT NULL,
  `ano_fabricacao` int(11) DEFAULT NULL,
  `tipo_veiculo_id` int(11) DEFAULT NULL,
  `tipo_combustivel_id` int(11) DEFAULT NULL,
  `especie_id` int(11) DEFAULT NULL,
  `propriedade_id` int(11) DEFAULT NULL,
  `familia_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `modelos_completo`
--

CREATE TABLE `modelos_completo` (
  `id` int(4) DEFAULT NULL,
  `marca_id` int(3) DEFAULT NULL,
  `descricao` varchar(44) DEFAULT NULL,
  `marca` varchar(16) DEFAULT NULL,
  `tipo_veiculo` varchar(8) DEFAULT NULL,
  `combustivel` varchar(10) DEFAULT NULL,
  `especie` varchar(10) DEFAULT NULL,
  `familia` varchar(10) DEFAULT NULL,
  `classificacao` varchar(10) DEFAULT NULL,
  `codigo_fipe` varchar(10) DEFAULT NULL,
  `valor_fipe` varchar(4) DEFAULT NULL,
  `ano_fabricacao` int(4) DEFAULT NULL,
  `classificacao_id` int(1) DEFAULT NULL,
  `familia_id` varchar(1) DEFAULT NULL,
  `especie_id` varchar(17) DEFAULT NULL,
  `combustivel_corrigido` varchar(17) DEFAULT NULL,
  `tipo_veiculo_corrigido` varchar(11) DEFAULT NULL,
  `combustivel_id` int(1) DEFAULT NULL,
  `tipo_veiculo_id` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `modelo_ano`
--

CREATE TABLE `modelo_ano` (
  `id` int(11) NOT NULL,
  `ano` int(11) DEFAULT NULL,
  `modelo_id` int(11) DEFAULT NULL,
  `preco` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `motorista_santana_paraiso`
--

CREATE TABLE `motorista_santana_paraiso` (
  `matricula` varchar(255) DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `unidade` varchar(255) DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `subunidade` varchar(255) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `sexo` varchar(255) DEFAULT NULL,
  `data_nascimento` varchar(255) DEFAULT NULL,
  `cpf` varchar(255) DEFAULT NULL,
  `telefone` varchar(255) DEFAULT NULL,
  `celular` varchar(255) DEFAULT NULL,
  `rg` varchar(255) DEFAULT NULL,
  `orgao_emissor` varchar(255) DEFAULT NULL,
  `cargo` varchar(255) DEFAULT NULL,
  `data_admissao` varchar(255) DEFAULT NULL,
  `numero_cnh` varchar(255) DEFAULT NULL,
  `categoria_cnh` varchar(255) DEFAULT NULL,
  `data_vencimento_cnh` varchar(255) DEFAULT NULL,
  `data_primeira_habilitacao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `movimento_dispositivos`
--

CREATE TABLE `movimento_dispositivos` (
  `id` int(11) NOT NULL,
  `dispositivos_solicitados_id` int(11) DEFAULT NULL,
  `datahora` datetime DEFAULT NULL,
  `valor` double DEFAULT NULL,
  `obs` varchar(255) DEFAULT NULL,
  `localizacao` varchar(255) DEFAULT NULL,
  `sentidodapassagem` varchar(255) DEFAULT NULL,
  `operadorapedagio` varchar(255) DEFAULT NULL,
  `tipodavia` varchar(255) DEFAULT NULL,
  `idtransacao` int(11) DEFAULT NULL,
  `veiculos_id` int(11) DEFAULT NULL,
  `estabelecimento_id` int(11) DEFAULT NULL,
  `condutor_id` int(11) DEFAULT NULL,
  `qtde` int(11) DEFAULT NULL,
  `valor_unitario` double DEFAULT NULL,
  `valor_total` double DEFAULT NULL,
  `valor_desconto` double DEFAULT NULL,
  `valor_liquido` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `multas`
--

CREATE TABLE `multas` (
  `id` int(11) NOT NULL,
  `veiculos_id` int(11) DEFAULT NULL,
  `condutor_id` int(11) DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `status_multas_id` int(11) DEFAULT NULL,
  `numero_alt` varchar(50) DEFAULT NULL,
  `enquadramento` varchar(100) DEFAULT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `data_infracao` datetime DEFAULT NULL,
  `local_infracao` varchar(200) DEFAULT NULL,
  `orgao_autuador` varchar(120) DEFAULT NULL,
  `pontos_cnh` int(11) DEFAULT NULL,
  `valor_original` double DEFAULT NULL,
  `valor_desconto` double DEFAULT NULL,
  `parcela` int(11) DEFAULT NULL,
  `data_vencimento` date DEFAULT NULL,
  `data_pagamento` datetime DEFAULT NULL,
  `valor_pago` double DEFAULT NULL,
  `motivo_cancelamento` varchar(255) DEFAULT NULL,
  `obs` text DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `multas_anexos`
--

CREATE TABLE `multas_anexos` (
  `id` int(11) NOT NULL,
  `multas_id` int(11) DEFAULT NULL,
  `arquivo` varchar(255) DEFAULT NULL,
  `obs` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `negociacao`
--

CREATE TABLE `negociacao` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `vendedor_id` int(11) NOT NULL,
  `origem_contato_id` int(11) NOT NULL,
  `etapa_negociacao_id` int(11) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fechamento` date DEFAULT NULL,
  `data_fechamento_esperada` date DEFAULT NULL,
  `valor_total` double DEFAULT NULL,
  `ordem` int(11) DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `ano` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `email_novo_pedido_enviado` char(1) DEFAULT 'F',
  `departamento_unit_id` int(11) NOT NULL,
  `system_users_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `negociacao_arquivo`
--

CREATE TABLE `negociacao_arquivo` (
  `id` int(11) NOT NULL,
  `negociacao_id` int(11) NOT NULL,
  `nome_arquivo` text DEFAULT NULL,
  `conteudo_arquivo` text DEFAULT NULL,
  `dt_arquivo` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `negociacao_atividade`
--

CREATE TABLE `negociacao_atividade` (
  `id` int(11) NOT NULL,
  `tipo_atividade_id` int(11) NOT NULL,
  `negociacao_id` int(11) NOT NULL,
  `descricao` text DEFAULT NULL,
  `horario_inicial` datetime DEFAULT NULL,
  `horario_final` datetime DEFAULT NULL,
  `observacao` text DEFAULT NULL,
  `dt_atividade` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `negociacao_historico_etapa`
--

CREATE TABLE `negociacao_historico_etapa` (
  `id` int(11) NOT NULL,
  `negociacao_id` int(11) NOT NULL,
  `etapa_negociacao_id` int(11) NOT NULL,
  `dt_etapa` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `negociacao_item`
--

CREATE TABLE `negociacao_item` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `negociacao_id` int(11) NOT NULL,
  `quantidade` double DEFAULT NULL,
  `valor` double DEFAULT NULL,
  `valor_total` double DEFAULT NULL,
  `dt_item` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `negociacao_observacao`
--

CREATE TABLE `negociacao_observacao` (
  `id` int(11) NOT NULL,
  `negociacao_id` int(11) NOT NULL,
  `observacao` text DEFAULT NULL,
  `dt_observacao` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `notas_system_unit`
--

CREATE TABLE `notas_system_unit` (
  `id` int(11) NOT NULL,
  `caminho` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `mes_ano` varchar(6) DEFAULT NULL,
  `valor` double DEFAULT NULL,
  `notificar` tinyint(4) DEFAULT NULL,
  `numero` varchar(50) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `nota_fiscal`
--

CREATE TABLE `nota_fiscal` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `pedido_venda_id` int(11) NOT NULL,
  `condicao_pagamento_id` int(11) NOT NULL,
  `obs` text DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `ano` int(11) DEFAULT NULL,
  `valor_total` double DEFAULT NULL,
  `data_emissao` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `estabelecimento_idold` int(11) DEFAULT NULL,
  `cliente_idold` int(11) DEFAULT NULL,
  `proposta_idold` int(11) DEFAULT NULL,
  `pedido_idold` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `nota_fiscal_item`
--

CREATE TABLE `nota_fiscal_item` (
  `id` int(11) NOT NULL,
  `pedido_venda_item_id` int(11) DEFAULT NULL,
  `nota_fiscal_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` double DEFAULT NULL,
  `valor` double DEFAULT NULL,
  `desconto` double DEFAULT NULL,
  `valor_total` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `origem_contato`
--

CREATE TABLE `origem_contato` (
  `id` int(11) NOT NULL,
  `nome` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `os_itens_marechal`
--

CREATE TABLE `os_itens_marechal` (
  `codos` int(11) DEFAULT NULL,
  `categoriasos` varchar(255) DEFAULT NULL,
  `cnpjestabelecimento` varchar(255) DEFAULT NULL,
  `codunidadeveiculo` int(11) DEFAULT NULL,
  `codveiculo` int(11) DEFAULT NULL,
  `dataaprovacao` datetime DEFAULT NULL,
  `datacancelamento` datetime DEFAULT NULL,
  `datacriacao` datetime DEFAULT NULL,
  `datafinalizacao` datetime DEFAULT NULL,
  `dataveiculoentregue` datetime DEFAULT NULL,
  `dataorcamento` datetime DEFAULT NULL,
  `dataaberturaos` datetime DEFAULT NULL,
  `dataprevisaoentrega` datetime DEFAULT NULL,
  `dataosrejeitada` datetime DEFAULT NULL,
  `diasentrega` int(11) DEFAULT NULL,
  `kmveiculo` int(11) DEFAULT NULL,
  `manutencaorevisada` int(11) DEFAULT NULL,
  `numcartaoveiculo` varchar(255) DEFAULT NULL,
  `observacao` text DEFAULT NULL,
  `statusos` int(11) DEFAULT NULL,
  `usuarioabertura` int(11) DEFAULT NULL,
  `usuarioaprovoufinalizou` int(11) DEFAULT NULL,
  `niveltanqueveiculo` varchar(255) DEFAULT NULL,
  `codcategoria` int(11) DEFAULT NULL,
  `garantiaitem` varchar(255) DEFAULT NULL,
  `nomeitem` text DEFAULT NULL,
  `qtde` double DEFAULT NULL,
  `resumoitem` text DEFAULT NULL,
  `statusitem` int(11) DEFAULT NULL,
  `tempomdo_dias` int(11) DEFAULT NULL,
  `tempomdo_horas` int(11) DEFAULT NULL,
  `tempomdo_minutos` int(11) DEFAULT NULL,
  `tipo` varchar(255) DEFAULT NULL,
  `valorunitariopeca` double DEFAULT NULL,
  `valortotalpeca` double DEFAULT NULL,
  `valorunitariohoramdo` double DEFAULT NULL,
  `valortotalmdo` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ouvidoria`
--

CREATE TABLE `ouvidoria` (
  `id` int(11) NOT NULL,
  `tipo_ouvidoria_id` int(11) NOT NULL,
  `nome` text DEFAULT NULL,
  `telefone` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `mensagem` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `pessoa_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido`
--

CREATE TABLE `pedido` (
  `id` int(11) NOT NULL,
  `dtpedido` date DEFAULT NULL,
  `tipo_pedido_id` int(11) DEFAULT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `descricaopedido` varchar(60) DEFAULT NULL,
  `vendedor_id` int(11) DEFAULT NULL,
  `estado_pedido_venda_id` int(11) DEFAULT NULL,
  `condicao_pagamento_id` int(11) DEFAULT NULL,
  `transportadora_id` int(11) DEFAULT NULL,
  `negociacao_id` int(11) DEFAULT NULL,
  `dt_pedido` date DEFAULT NULL,
  `obs` text DEFAULT NULL,
  `situacao_pedido_id` int(11) DEFAULT NULL,
  `frete` double DEFAULT NULL,
  `mes` char(2) DEFAULT NULL,
  `ano` char(4) DEFAULT NULL,
  `valor_total` double DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `obs_comercial` text DEFAULT NULL,
  `obs_financeiro` text DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `centrocusto_id` int(11) DEFAULT NULL,
  `valor_total_cotacao` double NOT NULL,
  `valor_desconto_cotacao` double DEFAULT NULL,
  `valor_liquido_cotacao` double DEFAULT NULL,
  `cidade_id` int(11) DEFAULT NULL,
  `cartao_id` int(11) DEFAULT NULL,
  `dt_finalizacao` datetime DEFAULT NULL,
  `veiculos_id` int(11) DEFAULT NULL,
  `entidade_id` int(11) DEFAULT NULL,
  `estado_pedido1_id` int(11) DEFAULT NULL,
  `saldo_departamento_id` int(11) DEFAULT NULL,
  `data_limite_resposta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_as_cliente`
--

CREATE TABLE `pedido_as_cliente` (
  `id` int(11) NOT NULL,
  `pedido_frotas_id` int(11) DEFAULT NULL,
  `pessoa_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_frotas`
--

CREATE TABLE `pedido_frotas` (
  `id` int(11) NOT NULL,
  `dt_pedido` datetime DEFAULT NULL,
  `descricaopedido` varchar(60) DEFAULT NULL,
  `dtprevisaoentrega` date DEFAULT NULL,
  `estado_pedido_frotas_id` int(11) DEFAULT NULL,
  `estabelecimento_id` int(11) DEFAULT NULL,
  `veiculos_id` int(11) DEFAULT NULL,
  `placa` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `km` decimal(18,3) DEFAULT NULL,
  `condutor_entrada_id` int(11) DEFAULT NULL,
  `condutor_retirada_id` int(11) DEFAULT NULL,
  `dataretirada` datetime DEFAULT NULL,
  `obs` varchar(255) DEFAULT NULL,
  `mes` char(2) DEFAULT NULL,
  `ano` char(4) DEFAULT NULL,
  `dt_finalizacao` date DEFAULT NULL,
  `cidade_id` int(11) DEFAULT NULL,
  `tipo_manutencao_id` int(11) DEFAULT NULL,
  `negociacao_id` int(11) DEFAULT NULL,
  `condicao_pagamento_id` int(11) DEFAULT NULL,
  `valor_total` double DEFAULT NULL,
  `valor_total_proposta` double DEFAULT NULL,
  `valor_desconto_proposta` double DEFAULT NULL,
  `valor_liquido_proposta` double DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `dataentrada` datetime DEFAULT NULL,
  `entidade_id` int(11) DEFAULT NULL,
  `data_limite_resposta` datetime DEFAULT NULL,
  `estado_pedido_frotas1_id` int(11) DEFAULT NULL,
  `saldo_departamento_id` int(11) DEFAULT NULL,
  `data_aprovacao` datetime DEFAULT NULL,
  `orcamento_base_id` int(11) DEFAULT NULL,
  `orcamento_base` tinyint(1) DEFAULT NULL,
  `valor_base_aprovacao` double DEFAULT NULL,
  `km_old` int(11) DEFAULT NULL,
  `ciclos` decimal(18,3) DEFAULT NULL,
  `abastecimento` tinyint(4) DEFAULT NULL,
  `idold` int(11) DEFAULT NULL,
  `motorista_idold` int(11) DEFAULT NULL,
  `system_users_idold` int(11) DEFAULT NULL,
  `status_old` varchar(20) DEFAULT NULL,
  `enviar_old` varchar(20) DEFAULT NULL,
  `motorista_old` varchar(50) DEFAULT NULL,
  `usuariocliente_idold` int(11) DEFAULT NULL,
  `usuarioaprovou_old` int(11) DEFAULT NULL,
  `ip_old` varchar(20) DEFAULT NULL,
  `usuario_old` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_frotas_historico`
--

CREATE TABLE `pedido_frotas_historico` (
  `id` int(11) NOT NULL,
  `pedido_frotas_id` int(11) DEFAULT NULL,
  `data_operacao` datetime DEFAULT NULL,
  `aprovador_frotas_id` int(11) DEFAULT NULL,
  `obs` varchar(255) DEFAULT NULL,
  `estado_pedido_frotas_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_historico`
--

CREATE TABLE `pedido_historico` (
  `id` int(11) NOT NULL,
  `pedido_venda_id` int(11) NOT NULL,
  `estado_pedido_venda_id` int(11) NOT NULL,
  `aprovador_id` int(11) DEFAULT NULL,
  `data_operacao` datetime DEFAULT NULL,
  `obs` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_seguimento`
--

CREATE TABLE `pedido_seguimento` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `seguimento_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_venda`
--

CREATE TABLE `pedido_venda` (
  `id` int(11) NOT NULL,
  `tipo_pedido_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `vendedor_id` int(11) NOT NULL,
  `estado_pedido_venda_id` int(11) NOT NULL,
  `condicao_pagamento_id` int(11) NOT NULL,
  `transportadora_id` int(11) NOT NULL,
  `negociacao_id` int(11) DEFAULT NULL,
  `dt_pedido` date DEFAULT NULL,
  `obs` varchar(255) DEFAULT NULL,
  `frete` double DEFAULT NULL,
  `mes` char(2) DEFAULT NULL,
  `ano` char(4) DEFAULT NULL,
  `valor_total` double DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `obs_comercial` text DEFAULT NULL,
  `obs_financeiro` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_venda_item`
--

CREATE TABLE `pedido_venda_item` (
  `id` int(11) NOT NULL,
  `pedido_venda_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` double DEFAULT NULL,
  `valor` double DEFAULT NULL,
  `desconto` double DEFAULT NULL,
  `valor_total` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pessoa`
--

CREATE TABLE `pessoa` (
  `id` int(11) NOT NULL,
  `tipo_cliente_id` int(11) NOT NULL,
  `categoria_cliente_id` int(11) DEFAULT NULL,
  `system_user_id` int(11) DEFAULT NULL,
  `nome` varchar(500) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `obs` varchar(1000) DEFAULT NULL,
  `fone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `login` varchar(255) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `banco` varchar(100) DEFAULT NULL,
  `agencia` varchar(20) DEFAULT NULL,
  `conta` varchar(100) DEFAULT NULL,
  `operacao` varchar(40) DEFAULT NULL,
  `favorecido` varchar(100) DEFAULT NULL,
  `chavepix` varchar(100) DEFAULT NULL,
  `tipochavepix` varchar(40) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `taxaadm` double DEFAULT NULL,
  `taxabancaria` double DEFAULT NULL,
  `taxaantecipacao` double DEFAULT NULL,
  `taxacontrato` double DEFAULT NULL,
  `abrirpedido` char(3) DEFAULT NULL,
  `cidade_id` int(11) DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `taxadesconto` double DEFAULT NULL,
  `ir` double DEFAULT NULL,
  `csll` double DEFAULT NULL,
  `confins` double DEFAULT NULL,
  `pis` double DEFAULT NULL,
  `iss` double DEFAULT NULL,
  `ir_servico` double DEFAULT NULL,
  `csll_servico` double DEFAULT NULL,
  `confins_servico` double DEFAULT NULL,
  `pis_servico` double DEFAULT NULL,
  `iss_servico` double DEFAULT NULL,
  `optante` tinyint(1) DEFAULT NULL,
  `data_emissao_cnh` date DEFAULT NULL,
  `data_validade_cnh` date DEFAULT NULL,
  `numero_registro_cnh` varchar(255) DEFAULT NULL,
  `categoria_cnh_id` int(11) DEFAULT NULL,
  `rg` varchar(255) DEFAULT NULL,
  `cpf` varchar(255) DEFAULT NULL,
  `status` varchar(6) DEFAULT NULL,
  `codestabelecimento_logpay` int(11) DEFAULT NULL,
  `apuracao` int(11) DEFAULT NULL,
  `pagamento` int(11) DEFAULT NULL,
  `nomeresponsavel` varchar(37) DEFAULT NULL,
  `cargo` varchar(31) DEFAULT NULL,
  `setor` varchar(60) DEFAULT NULL,
  `razaosocial` varchar(255) DEFAULT NULL,
  `sexo` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `ativo` char(1) DEFAULT NULL,
  `selo` tinyint(4) DEFAULT NULL,
  `data_desativacao` datetime DEFAULT NULL,
  `horariofuncionamento` varchar(255) DEFAULT NULL,
  `numero_registro` varchar(255) DEFAULT NULL,
  `vinculo_funcional` varchar(20) DEFAULT NULL,
  `idold` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pessoaold`
--

CREATE TABLE `pessoaold` (
  `id` int(11) NOT NULL,
  `tipo_cliente_id` int(11) NOT NULL,
  `categoria_cliente_id` int(11) DEFAULT NULL,
  `system_user_id` int(11) DEFAULT NULL,
  `nome` varchar(500) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `obs` varchar(1000) DEFAULT NULL,
  `fone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `login` varchar(255) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `banco` varchar(100) DEFAULT NULL,
  `agencia` varchar(20) DEFAULT NULL,
  `conta` varchar(100) DEFAULT NULL,
  `operacao` varchar(40) DEFAULT NULL,
  `favorecido` varchar(100) DEFAULT NULL,
  `chavepix` varchar(100) DEFAULT NULL,
  `tipochavepix` varchar(40) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `taxaadm` double DEFAULT NULL,
  `taxabancaria` double DEFAULT NULL,
  `taxaantecipacao` double DEFAULT NULL,
  `taxacontrato` double DEFAULT NULL,
  `abrirpedido` char(3) DEFAULT NULL,
  `cidade_id` int(11) DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `taxadesconto` double DEFAULT NULL,
  `ir` double DEFAULT NULL,
  `csll` double DEFAULT NULL,
  `confins` double DEFAULT NULL,
  `pis` double DEFAULT NULL,
  `iss` double DEFAULT NULL,
  `ir_servico` double DEFAULT NULL,
  `csll_servico` double DEFAULT NULL,
  `confins_servico` double DEFAULT NULL,
  `pis_servico` double DEFAULT NULL,
  `iss_servico` double DEFAULT NULL,
  `optante` tinyint(1) DEFAULT NULL,
  `data_emissao_cnh` date DEFAULT NULL,
  `data_validade_cnh` date DEFAULT NULL,
  `numero_registro_cnh` varchar(255) DEFAULT NULL,
  `categoria_cnh_id` int(11) DEFAULT NULL,
  `rg` varchar(255) DEFAULT NULL,
  `cpf` varchar(255) DEFAULT NULL,
  `status` varchar(6) DEFAULT NULL,
  `codestabelecimento_logpay` int(11) DEFAULT NULL,
  `apuracao` int(11) DEFAULT NULL,
  `pagamento` int(11) DEFAULT NULL,
  `nomeresponsavel` varchar(37) DEFAULT NULL,
  `cargo` varchar(31) DEFAULT NULL,
  `setor` varchar(60) DEFAULT NULL,
  `razaosocial` varchar(255) DEFAULT NULL,
  `sexo` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `ativo` char(1) DEFAULT NULL,
  `selo` tinyint(4) DEFAULT NULL,
  `data_desativacao` datetime DEFAULT NULL,
  `horariofuncionamento` varchar(255) DEFAULT NULL,
  `numero_registro` varchar(255) DEFAULT NULL,
  `vinculo_funcional` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pessoa_contato`
--

CREATE TABLE `pessoa_contato` (
  `id` int(11) NOT NULL,
  `pessoa_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `telefone` varchar(255) DEFAULT NULL,
  `obs` varchar(500) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pessoa_departamento`
--

CREATE TABLE `pessoa_departamento` (
  `id` int(11) NOT NULL,
  `pessoa_id` int(11) NOT NULL,
  `departamento_unit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pessoa_endereco`
--

CREATE TABLE `pessoa_endereco` (
  `id` int(11) NOT NULL,
  `pessoa_id` int(11) NOT NULL,
  `cidade_id` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `principal` char(1) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `rua` varchar(500) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `bairro` varchar(500) DEFAULT NULL,
  `complemento` varchar(500) DEFAULT NULL,
  `data_desativacao` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `longitude` varchar(19) DEFAULT NULL,
  `latitude` varchar(19) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pessoa_grupo`
--

CREATE TABLE `pessoa_grupo` (
  `id` int(11) NOT NULL,
  `pessoa_id` int(11) NOT NULL,
  `grupo_pessoa_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto`
--

CREATE TABLE `produto` (
  `id` int(11) NOT NULL,
  `tipo_produto_id` int(11) NOT NULL,
  `familia_produto_id` int(11) DEFAULT NULL,
  `fornecedor_id` int(11) DEFAULT NULL,
  `unidade_medida_id` int(11) NOT NULL,
  `fabricante_id` int(11) DEFAULT NULL,
  `nome` text NOT NULL,
  `cod_barras` varchar(255) DEFAULT NULL,
  `preco_venda` double NOT NULL,
  `preco_custo` double DEFAULT NULL,
  `peso_liquido` double DEFAULT NULL,
  `peso_bruto` double DEFAULT NULL,
  `largura` double DEFAULT NULL,
  `altura` double DEFAULT NULL,
  `volume` double DEFAULT NULL,
  `estoque_minimo` double DEFAULT NULL,
  `qtde_estoque` double DEFAULT NULL,
  `estoque_maximo` double DEFAULT NULL,
  `obs` varchar(500) DEFAULT NULL,
  `ativo` char(1) DEFAULT NULL,
  `foto` varchar(500) DEFAULT NULL,
  `data_ultimo_reajuste_preco` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `codigo_sinapi` int(11) NOT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `codigo_orse` int(11) DEFAULT NULL,
  `unidade_orse` varchar(30) DEFAULT NULL,
  `valor_orse` varchar(50) DEFAULT NULL,
  `suiv_grupo_id` int(11) DEFAULT NULL,
  `suiv_nickname_id` int(11) DEFAULT NULL,
  `suiv_preco_peca` decimal(18,2) DEFAULT NULL,
  `suiv_peca_id` int(11) DEFAULT NULL,
  `suiv_partnumber` varchar(255) DEFAULT NULL,
  `suiv_tempo_mao_obra_id` decimal(14,2) DEFAULT NULL,
  `suiv_tempo_servico` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto_system_unit`
--

CREATE TABLE `produto_system_unit` (
  `id` int(11) NOT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `produto_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `propostas`
--

CREATE TABLE `propostas` (
  `id` int(11) NOT NULL,
  `pedido_frotas_id` int(11) DEFAULT NULL,
  `pessoa_id` int(11) DEFAULT NULL,
  `estado_pedido_frotas_id` int(11) DEFAULT NULL,
  `estado_pedido_frotas_idold` int(11) DEFAULT NULL,
  `motorista_entrada_id` int(11) DEFAULT NULL,
  `veiculos_id` int(11) DEFAULT NULL,
  `placa` varchar(200) DEFAULT NULL,
  `modelo` varchar(200) DEFAULT NULL,
  `data_cotacao` date DEFAULT NULL,
  `obs` varchar(500) DEFAULT NULL,
  `valor_total` decimal(18,2) DEFAULT NULL,
  `valor_desconto` decimal(18,2) DEFAULT NULL,
  `valor_liquido` decimal(18,2) DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `data_entrada_veiculo` datetime DEFAULT NULL,
  `data_retirada_veiculo` datetime DEFAULT NULL,
  `data_previsao_entrega` date DEFAULT NULL,
  `motorista_retirada_id` int(11) DEFAULT NULL,
  `km` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `cidade_id` int(11) NOT NULL,
  `total_produtos_sem_desconto` decimal(18,2) DEFAULT NULL,
  `total_servicos_sem_desconto` decimal(18,2) DEFAULT NULL,
  `total_geral_sem_desconto` decimal(18,2) DEFAULT NULL,
  `total_produtos_com_desconto` decimal(18,2) DEFAULT NULL,
  `desconto_contratual` decimal(18,2) DEFAULT NULL,
  `total_servicos_com_desconto` decimal(18,2) DEFAULT NULL,
  `total_geral_com_desconto` decimal(18,2) DEFAULT NULL,
  `entidade_id` int(11) DEFAULT NULL,
  `responsavel_tecnico` varchar(255) DEFAULT NULL,
  `datahora_inicioservico` datetime DEFAULT NULL,
  `datahora_fimservico` datetime DEFAULT NULL,
  `data_limite_resposta` date DEFAULT NULL,
  `estado_pedido_frotas1_id` int(11) DEFAULT NULL,
  `ciclos` decimal(18,3) DEFAULT NULL,
  `horimetro_entrada_aeronave` decimal(18,3) DEFAULT NULL,
  `ciclos_entrada_aeronave` decimal(18,3) DEFAULT NULL,
  `horimetro_retirada_aeronave` decimal(18,3) DEFAULT NULL,
  `ciclos_retirada_aeronave` decimal(18,3) DEFAULT NULL,
  `horimetro_inicioservico` decimal(18,3) DEFAULT NULL,
  `ciclos_inicioservico` decimal(18,3) DEFAULT NULL,
  `horimetro_fimservico` decimal(18,3) DEFAULT NULL,
  `ciclos_fimservico` decimal(18,3) DEFAULT NULL,
  `valor_total_old` double DEFAULT NULL,
  `valor_desconto_old` double DEFAULT NULL,
  `valor_liquido_old` double DEFAULT NULL,
  `total_produtos_sem_desconto_old` double DEFAULT NULL,
  `total_produtos_com_desconto_old` double DEFAULT NULL,
  `total_geral_sem_desconto_old` double DEFAULT NULL,
  `total_geral_com_desconto_old` double DEFAULT NULL,
  `total_servicos_sem_desconto_old` double DEFAULT NULL,
  `total_servicos_com_desconto_old` double DEFAULT NULL,
  `desconto_contratual_old` double DEFAULT NULL,
  `abastecimento` tinyint(4) DEFAULT NULL,
  `idold` int(11) DEFAULT NULL,
  `pedido_idold` int(11) DEFAULT NULL,
  `status_old` varchar(20) DEFAULT NULL,
  `cliente_idold` int(11) DEFAULT NULL,
  `data_pag_autorizadoold` datetime DEFAULT NULL,
  `pagamento_autorizadoold` varchar(3) DEFAULT NULL,
  `estabelecimento_idold` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `propostas_historico`
--

CREATE TABLE `propostas_historico` (
  `id` int(11) NOT NULL,
  `propostas_id` int(11) NOT NULL,
  `estado_pedido_frotas_id` int(11) DEFAULT NULL,
  `aprovador_frotas_id` int(11) DEFAULT NULL,
  `data_historico` datetime DEFAULT NULL,
  `obs` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `propriedade`
--

CREATE TABLE `propriedade` (
  `id` int(11) NOT NULL,
  `descricao` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `redecredenciada`
--

CREATE TABLE `redecredenciada` (
  `CodEstabelecimento` int(7) DEFAULT NULL,
  `RazaoSocial` varchar(78) DEFAULT NULL,
  `NomeFantasia` varchar(56) DEFAULT NULL,
  `CNPJ` varchar(18) DEFAULT NULL,
  `Logradouro` varchar(5) DEFAULT NULL,
  `Endereco` varchar(45) DEFAULT NULL,
  `Numero` varchar(8) DEFAULT NULL,
  `Bairro` varchar(33) DEFAULT NULL,
  `Cidade` varchar(26) DEFAULT NULL,
  `Estado` varchar(2) DEFAULT NULL,
  `CEP` varchar(10) DEFAULT NULL,
  `Categoria` varchar(14) DEFAULT NULL,
  `Descricao` varchar(116) DEFAULT NULL,
  `Status` varchar(7) DEFAULT NULL,
  `Apuracao` int(2) DEFAULT NULL,
  `Pagamento` int(2) DEFAULT NULL,
  `TaxaAdm` decimal(4,2) DEFAULT NULL,
  `Banco` varchar(3) DEFAULT NULL,
  `Agencia` varchar(11) DEFAULT NULL,
  `ContaCorrente` varchar(20) DEFAULT NULL,
  `Telefone` varchar(14) DEFAULT NULL,
  `Celular` varchar(16) DEFAULT NULL,
  `e-mail` varchar(57) DEFAULT NULL,
  `NomeResponsavel` varchar(37) DEFAULT NULL,
  `RG` varchar(16) DEFAULT NULL,
  `CPF` varchar(14) DEFAULT NULL,
  `Setor` varchar(16) DEFAULT NULL,
  `Cargo` varchar(31) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `saldo_departamento`
--

CREATE TABLE `saldo_departamento` (
  `id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `tipotransacao` char(1) DEFAULT NULL,
  `datatransacao` date DEFAULT NULL,
  `historico` varchar(100) DEFAULT NULL,
  `valor` double DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL,
  `saldo_produto` double DEFAULT NULL,
  `saldo_servico` double DEFAULT NULL,
  `documento_empenho` varchar(255) DEFAULT NULL,
  `numero_documento_empenho` varchar(255) DEFAULT NULL,
  `saldo_entidade_contrato_id` int(11) DEFAULT NULL,
  `saldo_total` double DEFAULT NULL,
  `data_processo` date DEFAULT NULL,
  `data_documento_empenho` date DEFAULT NULL,
  `numero_processo` varchar(255) NOT NULL,
  `idold` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `saldo_entidade_contrato`
--

CREATE TABLE `saldo_entidade_contrato` (
  `id` int(11) NOT NULL,
  `entidade_id` int(11) DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `tipotransacao` char(1) DEFAULT NULL,
  `datatransacao` date DEFAULT NULL,
  `historico` text DEFAULT NULL,
  `valor_saldo` double DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `dtinicio` date DEFAULT NULL,
  `dtfinal` date DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT NULL,
  `idold` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `saldo_veiculo`
--

CREATE TABLE `saldo_veiculo` (
  `id` int(11) NOT NULL,
  `tipo_transacao` char(1) DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `motivo_transacao` varchar(255) DEFAULT NULL,
  `data_transacao` datetime DEFAULT NULL,
  `valor_transacao` double DEFAULT NULL,
  `veiculos_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `seguimento`
--

CREATE TABLE `seguimento` (
  `id` int(11) NOT NULL,
  `descricao` text DEFAULT NULL,
  `idold` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `seguimento_pedido_frotas`
--

CREATE TABLE `seguimento_pedido_frotas` (
  `id` int(11) NOT NULL,
  `pedido_frotas_id` int(11) DEFAULT NULL,
  `seguimento_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `seguimento_pessoa`
--

CREATE TABLE `seguimento_pessoa` (
  `id` int(11) NOT NULL,
  `seguimento_id` int(11) DEFAULT NULL,
  `pessoa_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `seguros`
--

CREATE TABLE `seguros` (
  `id` int(11) NOT NULL,
  `saldo_entidade_contrato_id` int(11) DEFAULT NULL,
  `tipo_seguro_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_final` date DEFAULT NULL,
  `numero_apolice` varchar(100) DEFAULT NULL,
  `valor_cobertura` double DEFAULT NULL,
  `obs` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `status_dispositivos`
--

CREATE TABLE `status_dispositivos` (
  `id` int(11) NOT NULL,
  `descricao` varchar(50) DEFAULT NULL,
  `cor` varchar(50) DEFAULT NULL,
  `ordem` int(11) DEFAULT NULL,
  `mensagem` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `status_multas`
--

CREATE TABLE `status_multas` (
  `id` int(11) NOT NULL,
  `descricao` varchar(100) DEFAULT NULL,
  `cor` varchar(7) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `status_veiculo`
--

CREATE TABLE `status_veiculo` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `cor` varchar(10) DEFAULT NULL,
  `ordem` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_access_log`
--

CREATE TABLE `system_access_log` (
  `id` int(11) NOT NULL,
  `sessionid` text DEFAULT NULL,
  `login` text DEFAULT NULL,
  `login_time` timestamp NULL DEFAULT NULL,
  `login_year` varchar(4) DEFAULT NULL,
  `login_month` varchar(2) DEFAULT NULL,
  `login_day` varchar(2) DEFAULT NULL,
  `logout_time` timestamp NULL DEFAULT NULL,
  `impersonated` char(1) DEFAULT NULL,
  `access_ip` varchar(45) DEFAULT NULL,
  `impersonated_by` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_access_notification_log`
--

CREATE TABLE `system_access_notification_log` (
  `id` int(11) NOT NULL,
  `login` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `ip_address` text DEFAULT NULL,
  `login_time` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_change_log`
--

CREATE TABLE `system_change_log` (
  `id` int(11) NOT NULL,
  `logdate` timestamp NULL DEFAULT NULL,
  `login` text DEFAULT NULL,
  `tablename` text DEFAULT NULL,
  `primarykey` text DEFAULT NULL,
  `pkvalue` text DEFAULT NULL,
  `operation` text DEFAULT NULL,
  `columnname` text DEFAULT NULL,
  `oldvalue` text DEFAULT NULL,
  `newvalue` text DEFAULT NULL,
  `access_ip` text DEFAULT NULL,
  `transaction_id` text DEFAULT NULL,
  `log_trace` text DEFAULT NULL,
  `session_id` text DEFAULT NULL,
  `class_name` text DEFAULT NULL,
  `php_sapi` text DEFAULT NULL,
  `log_year` varchar(4) DEFAULT NULL,
  `log_month` varchar(2) DEFAULT NULL,
  `log_day` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_document`
--

CREATE TABLE `system_document` (
  `id` int(11) NOT NULL,
  `system_user_id` int(11) DEFAULT NULL,
  `title` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `submission_date` date DEFAULT NULL,
  `archive_date` date DEFAULT NULL,
  `filename` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_document_category`
--

CREATE TABLE `system_document_category` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_document_group`
--

CREATE TABLE `system_document_group` (
  `id` int(11) NOT NULL,
  `document_id` int(11) DEFAULT NULL,
  `system_group_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_document_user`
--

CREATE TABLE `system_document_user` (
  `id` int(11) NOT NULL,
  `document_id` int(11) DEFAULT NULL,
  `system_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_group`
--

CREATE TABLE `system_group` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `uuid` varchar(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_group_program`
--

CREATE TABLE `system_group_program` (
  `id` int(11) NOT NULL,
  `system_group_id` int(11) NOT NULL,
  `system_program_id` int(11) NOT NULL,
  `actions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_message`
--

CREATE TABLE `system_message` (
  `id` int(11) NOT NULL,
  `system_user_id` int(11) DEFAULT NULL,
  `system_user_to_id` int(11) DEFAULT NULL,
  `subject` text DEFAULT NULL,
  `message` text DEFAULT NULL,
  `dt_message` text DEFAULT NULL,
  `checked` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_notification`
--

CREATE TABLE `system_notification` (
  `id` int(11) NOT NULL,
  `system_user_id` int(11) DEFAULT NULL,
  `system_user_to_id` int(11) DEFAULT NULL,
  `subject` text DEFAULT NULL,
  `message` text DEFAULT NULL,
  `dt_message` text DEFAULT NULL,
  `action_url` text DEFAULT NULL,
  `action_label` text DEFAULT NULL,
  `icon` text DEFAULT NULL,
  `checked` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_preference`
--

CREATE TABLE `system_preference` (
  `id` varchar(255) NOT NULL,
  `preference` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_program`
--

CREATE TABLE `system_program` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `controller` text NOT NULL,
  `actions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_request_log`
--

CREATE TABLE `system_request_log` (
  `id` int(11) NOT NULL,
  `endpoint` text DEFAULT NULL,
  `logdate` text DEFAULT NULL,
  `log_year` varchar(4) DEFAULT NULL,
  `log_month` varchar(2) DEFAULT NULL,
  `log_day` varchar(2) DEFAULT NULL,
  `session_id` text DEFAULT NULL,
  `login` text DEFAULT NULL,
  `access_ip` text DEFAULT NULL,
  `class_name` text DEFAULT NULL,
  `http_host` text DEFAULT NULL,
  `server_port` text DEFAULT NULL,
  `request_uri` text DEFAULT NULL,
  `request_method` text DEFAULT NULL,
  `query_string` text DEFAULT NULL,
  `request_headers` text DEFAULT NULL,
  `request_body` text DEFAULT NULL,
  `request_duration` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_sql_log`
--

CREATE TABLE `system_sql_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `logdate` timestamp NULL DEFAULT NULL,
  `login` text DEFAULT NULL,
  `database_name` text DEFAULT NULL,
  `sql_command` text DEFAULT NULL,
  `statement_type` text DEFAULT NULL,
  `access_ip` varchar(45) DEFAULT NULL,
  `transaction_id` text DEFAULT NULL,
  `log_trace` text DEFAULT NULL,
  `session_id` text DEFAULT NULL,
  `class_name` text DEFAULT NULL,
  `php_sapi` text DEFAULT NULL,
  `request_id` text DEFAULT NULL,
  `log_year` varchar(4) DEFAULT NULL,
  `log_month` varchar(2) DEFAULT NULL,
  `log_day` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_unit`
--

CREATE TABLE `system_unit` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `connection_name` text DEFAULT NULL,
  `email` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rua` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complemento` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnpj` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone03` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone01` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone02` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade_id` int(11) DEFAULT NULL,
  `utilizasinapi` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recaptcha` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entidade_id` int(11) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `testar_valor_venal` tinyint(4) DEFAULT NULL,
  `aprovacao_por_item` tinyint(4) DEFAULT NULL,
  `selecao_redes_aleatoria` tinyint(4) DEFAULT NULL,
  `pedido_base` tinyint(4) DEFAULT NULL,
  `testar_revisao` tinyint(4) DEFAULT NULL,
  `longitude` varchar(19) DEFAULT NULL,
  `latitude` varchar(19) DEFAULT NULL,
  `valor_base_aprovacao` double DEFAULT NULL,
  `enviar_email_auto_relatorio` tinyint(1) DEFAULT NULL,
  `garantia_dias` int(11) DEFAULT NULL,
  `garantia_km` decimal(18,3) DEFAULT NULL,
  `percentual_produto_similar` double DEFAULT NULL,
  `utiliza_temparia` tinyint(4) DEFAULT NULL,
  `idold` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_users`
--

CREATE TABLE `system_users` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `login` text NOT NULL,
  `password` text NOT NULL,
  `email` text DEFAULT NULL,
  `frontpage_id` int(11) DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `active` char(1) DEFAULT NULL,
  `accepted_term_policy_at` text DEFAULT NULL,
  `accepted_term_policy` char(1) DEFAULT NULL,
  `two_factor_enabled` char(1) DEFAULT 'N',
  `two_factor_type` varchar(100) DEFAULT NULL,
  `two_factor_secret` varchar(255) DEFAULT NULL,
  `cpf` varchar(255) DEFAULT NULL,
  `notificarusuario` tinyint(4) DEFAULT NULL,
  `idold` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_user_departamento_unit`
--

CREATE TABLE `system_user_departamento_unit` (
  `id` int(11) NOT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_user_group`
--

CREATE TABLE `system_user_group` (
  `id` int(11) NOT NULL,
  `system_user_id` int(11) NOT NULL,
  `system_group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_user_program`
--

CREATE TABLE `system_user_program` (
  `id` int(11) NOT NULL,
  `system_user_id` int(11) NOT NULL,
  `system_program_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_user_unit`
--

CREATE TABLE `system_user_unit` (
  `id` int(11) NOT NULL,
  `system_user_id` int(11) NOT NULL,
  `system_unit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tabela_fipe_final_corrigida`
--

CREATE TABLE `tabela_fipe_final_corrigida` (
  `id` int(11) NOT NULL,
  `Type` varchar(255) DEFAULT NULL,
  `Brand_Code` int(11) DEFAULT NULL,
  `Brand_Value` varchar(255) DEFAULT NULL,
  `Model_Code` int(11) DEFAULT NULL,
  `Model_Value` varchar(255) DEFAULT NULL,
  `Year_Code` varchar(20) DEFAULT NULL,
  `Year_Value` varchar(255) DEFAULT NULL,
  `Fipe_Code` varchar(255) DEFAULT NULL,
  `Fuel_Letter` varchar(255) DEFAULT NULL,
  `Fuel_Type` varchar(255) DEFAULT NULL,
  `Price` varchar(255) DEFAULT NULL,
  `Month` varchar(255) DEFAULT NULL,
  `Especie` varchar(255) DEFAULT NULL,
  `Familia` varchar(255) DEFAULT NULL,
  `Propriedade` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf16 COLLATE=utf16_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tabela_orse`
--

CREATE TABLE `tabela_orse` (
  `id` int(11) NOT NULL,
  `codigo` varchar(5) DEFAULT NULL,
  `descricao` varchar(253) DEFAULT NULL,
  `unidade` varchar(6) DEFAULT NULL,
  `valor` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `taxas_pessoa`
--

CREATE TABLE `taxas_pessoa` (
  `id` int(11) NOT NULL,
  `pessoa_id` int(11) DEFAULT NULL,
  `entidade_id` int(11) DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `taxaadm` double DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `taxabancaria` double DEFAULT NULL,
  `taxaantecipacao` double DEFAULT NULL,
  `taxacontrato` double DEFAULT NULL,
  `taxadesconto` double DEFAULT NULL,
  `optante` tinyint(1) DEFAULT NULL,
  `ir` double DEFAULT NULL,
  `csll` double DEFAULT NULL,
  `cofins` double DEFAULT NULL,
  `pis` double DEFAULT NULL,
  `ir_servico` double DEFAULT NULL,
  `csll_servico` double DEFAULT NULL,
  `cofins_servico` double DEFAULT NULL,
  `pis_servico` double DEFAULT NULL,
  `iss_servico` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_anexo`
--

CREATE TABLE `tipo_anexo` (
  `id` int(11) NOT NULL,
  `nome` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_atividade`
--

CREATE TABLE `tipo_atividade` (
  `id` int(11) NOT NULL,
  `nome` text DEFAULT NULL,
  `cor` text DEFAULT NULL,
  `icone` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_cliente`
--

CREATE TABLE `tipo_cliente` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `sigla` char(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_combustivel`
--

CREATE TABLE `tipo_combustivel` (
  `id` int(11) NOT NULL,
  `descricao` varchar(30) DEFAULT NULL,
  `idold` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_conta`
--

CREATE TABLE `tipo_conta` (
  `id` int(11) NOT NULL,
  `nome` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_documento`
--

CREATE TABLE `tipo_documento` (
  `id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `descricao` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_documentos_propostas`
--

CREATE TABLE `tipo_documentos_propostas` (
  `id` int(11) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_finalidade`
--

CREATE TABLE `tipo_finalidade` (
  `id` int(11) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_manutencao`
--

CREATE TABLE `tipo_manutencao` (
  `id` int(11) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `idold` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_ouvidoria`
--

CREATE TABLE `tipo_ouvidoria` (
  `id` int(11) NOT NULL,
  `nome` text NOT NULL,
  `cor` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_pecas`
--

CREATE TABLE `tipo_pecas` (
  `id` int(11) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_pedido`
--

CREATE TABLE `tipo_pedido` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `nome` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_produto`
--

CREATE TABLE `tipo_produto` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_seguro`
--

CREATE TABLE `tipo_seguro` (
  `id` int(11) NOT NULL,
  `descricao` varchar(30) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_veiculo`
--

CREATE TABLE `tipo_veiculo` (
  `id` int(11) NOT NULL,
  `descricao` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `unidade_medida`
--

CREATE TABLE `unidade_medida` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `sigla` char(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `vehicletoken`
--

CREATE TABLE `vehicletoken` (
  `id` int(11) NOT NULL,
  `token` text DEFAULT NULL,
  `veiculos_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `veiculos`
--

CREATE TABLE `veiculos` (
  `id` int(11) NOT NULL,
  `dispositivos_id` int(11) DEFAULT NULL,
  `prefixo` varchar(100) DEFAULT NULL,
  `placa` varchar(100) DEFAULT NULL,
  `marca_id` int(11) DEFAULT NULL,
  `modelo_id` int(11) DEFAULT NULL,
  `anof` int(11) DEFAULT NULL,
  `anom` int(11) DEFAULT NULL,
  `chassi` varchar(100) DEFAULT NULL,
  `renavam` varchar(100) DEFAULT NULL,
  `capacidade_tanque` int(11) DEFAULT NULL,
  `hodometroatual` decimal(18,3) DEFAULT NULL,
  `tipo_veiculo_id` int(11) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `valor_tabela_fipe` double DEFAULT NULL,
  `identificacao` varchar(20) DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `system_users_id` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `saldo_veiculo` double DEFAULT NULL,
  `tipo_combustivel_id` int(11) DEFAULT NULL,
  `propriedade_id` int(11) DEFAULT NULL,
  `corveiculo_id` int(11) DEFAULT NULL,
  `status_veiculo_id` int(11) DEFAULT NULL,
  `especie_id` int(11) DEFAULT NULL,
  `familia_id` int(11) DEFAULT NULL,
  `codigo_fipe` varchar(30) DEFAULT NULL,
  `responsavel_id` int(11) DEFAULT NULL,
  `ciclos` decimal(18,3) DEFAULT NULL,
  `hodometroatual_old` int(11) DEFAULT NULL,
  `codigo_patrimonio` varchar(20) DEFAULT NULL,
  `numero_dispositivo` varchar(20) DEFAULT NULL,
  `idold` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `veiculos_jiparana`
--

CREATE TABLE `veiculos_jiparana` (
  `placa` varchar(7) DEFAULT NULL,
  `novaplaca` varchar(10) DEFAULT NULL,
  `unidade` varchar(60) DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `cnpj_faturamento` varchar(60) DEFAULT NULL,
  `marca` varchar(30) DEFAULT NULL,
  `marca_id` int(11) DEFAULT NULL,
  `modelo` varchar(200) DEFAULT NULL,
  `modelo_id` int(11) DEFAULT NULL,
  `combustivel` varchar(30) DEFAULT NULL,
  `tipo_combustivel_id` int(11) DEFAULT NULL,
  `capacidade_tanque` varchar(10) DEFAULT NULL,
  `ano_fabricacao` varchar(4) DEFAULT NULL,
  `ano_modelo` varchar(4) DEFAULT NULL,
  `tipo_veiculo` varchar(30) DEFAULT NULL,
  `tipo_veiculo_id` int(11) DEFAULT NULL,
  `cor_veiculo` varchar(30) DEFAULT NULL,
  `cor_veiculo_id` int(11) DEFAULT NULL,
  `numero_chassi` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `veiculos_marechal`
--

CREATE TABLE `veiculos_marechal` (
  `marca` varchar(30) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `ano` varchar(4) DEFAULT NULL,
  `combustivel` varchar(20) DEFAULT NULL,
  `placa` varchar(10) DEFAULT NULL,
  `prefixo` varchar(20) DEFAULT NULL,
  `locadoproprio` varchar(30) DEFAULT NULL,
  `subunidade` varchar(200) DEFAULT NULL,
  `unidade` varchar(200) DEFAULT NULL,
  `km` int(11) DEFAULT NULL,
  `tanque` int(11) DEFAULT NULL,
  `chassi` varchar(100) DEFAULT NULL,
  `tipoveiculo` varchar(50) DEFAULT NULL,
  `situacao` varchar(20) DEFAULT NULL,
  `marca_id` int(11) DEFAULT NULL,
  `modelo_id` int(11) DEFAULT NULL,
  `tipo_combustivel_id` int(11) DEFAULT NULL,
  `tipo_veiculo_id` int(11) DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `veiculos_saae`
--

CREATE TABLE `veiculos_saae` (
  `marca` varchar(30) DEFAULT NULL,
  `marca_id` int(11) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `modelo_id` int(11) DEFAULT NULL,
  `ano` varchar(4) DEFAULT NULL,
  `combustivel` varchar(20) DEFAULT NULL,
  `tipo_combustivel_id` int(11) DEFAULT NULL,
  `placa` varchar(10) DEFAULT NULL,
  `prefixo` varchar(20) DEFAULT NULL,
  `locadoproprio` varchar(30) DEFAULT NULL,
  `subunidade` varchar(200) DEFAULT NULL,
  `unidade` varchar(200) DEFAULT NULL,
  `km` int(11) DEFAULT NULL,
  `tanque` int(11) DEFAULT NULL,
  `chassi` varchar(100) DEFAULT NULL,
  `tipoveiculo` varchar(50) DEFAULT NULL,
  `tipo_veiculo_id` int(11) DEFAULT NULL,
  `situacao` varchar(20) DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `veiculos_santana_paraiso`
--

CREATE TABLE `veiculos_santana_paraiso` (
  `marca` varchar(255) DEFAULT NULL,
  `marca_id` int(11) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `modelo_id` int(11) DEFAULT NULL,
  `ano` varchar(255) DEFAULT NULL,
  `ano_f` varchar(4) DEFAULT NULL,
  `ano_m` varchar(4) DEFAULT NULL,
  `system_unit_id` int(11) DEFAULT NULL,
  `departamento_unit_id` int(11) DEFAULT NULL,
  `unidade` varchar(255) DEFAULT NULL,
  `subunidade` varchar(255) DEFAULT NULL,
  `chassi` varchar(255) DEFAULT NULL,
  `placa` varchar(255) DEFAULT NULL,
  `placa_nova` varchar(8) DEFAULT NULL,
  `combustivel` varchar(255) DEFAULT NULL,
  `tipo_combustivel_id` int(11) DEFAULT NULL,
  `capacidade` varchar(255) DEFAULT NULL,
  `tipo_de_frota` varchar(255) DEFAULT NULL,
  `propriedade_id` int(11) DEFAULT NULL,
  `tipo` varchar(255) DEFAULT NULL,
  `tipo_veiculo_id` int(11) DEFAULT NULL,
  `renavam` varchar(255) DEFAULT NULL,
  `cor` varchar(255) DEFAULT NULL,
  `corveiculo_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `km` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `viewsaldoempenho`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `viewsaldoempenho` (
`entidade_id` int(11)
,`system_unit_id` int(11)
,`departamento_unit_id` int(11)
,`dt_pedido` datetime
,`total_servicos` double
,`total_produtos` double
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_aprovadores_unidade`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_aprovadores_unidade` (
`id` int(11)
,`email` text
,`name` text
,`login` text
,`aprovador_frotas_id` int(11)
,`system_group_id` int(11)
,`system_unit_id` int(11)
,`active` char(1)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_cidadeestado`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_cidadeestado` (
`idcidade` int(11)
,`nomecidade` varchar(255)
,`idestado` int(11)
,`nomeestado` varchar(255)
,`sigla` char(2)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_comparacaoprodutos`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_comparacaoprodutos` (
`estabelecimento_id` int(11)
,`nome_estabelecimento` varchar(500)
,`cidade_id` int(11)
,`nomecidade` varchar(255)
,`estado_id` int(11)
,`uf` char(2)
,`produto_id` int(11)
,`produto_id_real` int(11)
,`nome_produto` text
,`tipo_produto_id` int(11)
,`item_proposta_id` int(11)
,`valor` decimal(18,2)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_consumosrealizados`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_consumosrealizados` (
`pedido_frotas_id` int(11)
,`propostas_id` int(11)
,`veiculos_id` int(11)
,`placa` varchar(100)
,`marca` varchar(500)
,`modelo` varchar(500)
,`anof` int(11)
,`km` decimal(18,3)
,`fornecedor` varchar(500)
,`total_geral_com_desconto` decimal(18,2)
,`cidade_id` int(11)
,`nome_cidade` varchar(255)
,`estado_id` int(11)
,`sigla_estado` char(2)
,`mes` char(2)
,`ano` char(4)
,`system_unit_id` int(11)
,`valor_transacao_credito` double
,`valor_transacao_debito` double
,`saldo_atual` double
,`km_anterior` decimal(18,3)
,`km_rodado` decimal(19,3)
,`custo_por_km` decimal(22,2)
,`qtd_pedidos_mes` bigint(21)
,`total_mensal_manutencao` decimal(40,2)
,`custo_medio_mensal` decimal(41,2)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_cotacao`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_cotacao` (
`cotacao_id` int(11)
,`data_cotacao` date
,`unidade` text
,`departamento` text
,`pedido_id` int(11)
,`descricaopedido` varchar(60)
,`nomeautorizado` mediumtext
,`pessoa_id` int(11)
,`nomefornecedor` varchar(500)
,`cnpj` varchar(20)
,`nomecidade` varchar(255)
,`uf` char(2)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_departamento_unit_system_unit`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_departamento_unit_system_unit` (
`system_unit_id` int(11)
,`name_system_unit` text
,`name_departamento_unit` text
,`id` int(11)
,`departamento_unit_id` int(11)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_dotacao_pedido_frotas`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_dotacao_pedido_frotas` (
`id` int(11)
,`saldo_departamento_id` int(11)
,`pedido_frotas_id` int(11)
,`descricaopedido` varchar(60)
,`estabelecimento_id` int(11)
,`veiculos_id` int(11)
,`km` decimal(18,3)
,`valor` double
,`saldo_atual` double
,`valor_liquido_proposta` double
,`dt_pedido` datetime
,`dt_finalizacao` date
,`estado_pedido_frotas_id` int(11)
,`system_users_id` int(11)
,`system_unit_id` int(11)
,`cidade_id` int(11)
,`propostas_id` int(11)
,`departamento_unit_id` int(11)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_email_usuarios`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_email_usuarios` (
`system_users_id` int(11)
,`name` text
,`email` text
,`system_unit_id` int(11)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_enviarcotacao`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_enviarcotacao` (
`id` int(11)
,`cidade_id` int(11)
,`seguimento_id` int(11)
,`nome` varchar(500)
,`pessoa_id` int(11)
,`deleted_at` datetime
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_enviarcotacao_seguradora`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_enviarcotacao_seguradora` (
`id` int(11)
,`cidade_id` int(11)
,`seguimento_id` int(11)
,`nome` varchar(500)
,`pessoa_id` int(11)
,`deleted_at` datetime
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_indicadoresstatuspedidofrotas`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_indicadoresstatuspedidofrotas` (
`entidade_id` int(11)
,`system_unit_id` int(11)
,`departamento_unit_id` int(11)
,`estado_pedido_frotas_id` int(11)
,`ano` char(4)
,`mes` char(2)
,`qtde` bigint(21)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_itenscotacao_produto`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_itenscotacao_produto` (
`id` int(11)
,`produto_id` int(11)
,`qtde` int(11)
,`valor` double
,`valor_total` double
,`cotacao_id` int(11)
,`id_produto` int(11)
,`nome` text
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_listagem_novos_produtos`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_listagem_novos_produtos` (
`nome` text
,`familia_produto_id` int(11)
,`qtde` bigint(21)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_negociacao_timeline`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_negociacao_timeline` (
`chave` int(11)
,`negociacao_id` int(11)
,`dt_historico` datetime /* mariadb-5.3 */
,`tipo` varchar(10)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_pedidos_as_cliente`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_pedidos_as_cliente` (
`id` int(11)
,`pedido_frotas_id` int(11)
,`pessoa_id` int(11)
,`id_pessoa` int(11)
,`nome` varchar(500)
,`fone` varchar(255)
,`email` varchar(255)
,`id_pessoa_endereco` int(11)
,`cidade_id` int(11)
,`id_cidade` int(11)
,`estado_id` int(11)
,`nome_cidade` varchar(255)
,`id_estado` int(11)
,`sigla` char(2)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_pedido_frotas_propostas`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_pedido_frotas_propostas` (
`id` int(11)
,`descricaopedido` varchar(60)
,`dt_pedido` datetime
,`valor_total` double
,`valor_desconto_proposta` double
,`valor_liquido_proposta` double
,`estado_pedido_frotas_id` int(11)
,`departamento_unit_id` int(11)
,`estabelecimento_id` int(11)
,`cidade_id` int(11)
,`dt_finalizacao` date
,`system_unit_id` int(11)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_pessoa`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_pessoa` (
`id` int(11)
,`nome` varchar(500)
,`id_pessoa_grupo` int(11)
,`pessoa_id` int(11)
,`grupo_pessoa_id` int(11)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_pessoa_cnh`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_pessoa_cnh` (
`id` int(11)
,`system_unit_id` int(11)
,`nome` varchar(500)
,`numero_registro_cnh` varchar(255)
,`data_validade_cnh` date
,`system_unit_name` text
,`status_cnh` varchar(14)
,`ordem_status` int(1)
,`dias_para_vencer` int(8)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_produtocompras`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_produtocompras` (
`id` int(11)
,`pedido_id` int(11)
,`estado_pedido_venda_id` int(11)
,`estado_pedido_id` int(11)
,`data_cotacao` date
,`pessoa_id` int(11)
,`system_unit_id` int(11)
,`departamento_unit_id` int(11)
,`entidade_id` int(11)
,`qtde` int(11)
,`valor_unitario` double
,`valor_total` double
,`nomeproduto` text
,`nomeestabelecimento` varchar(500)
,`dt_pedido` date
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_produtos_servicos_aprovados`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_produtos_servicos_aprovados` (
`id` int(11)
,`pedido_frotas_id` int(11)
,`pessoa_id` int(11)
,`estado_pedido_frotas_id` int(11)
,`veiculos_id` int(11)
,`system_unit_id` int(11)
,`departamento_unit_id` int(11)
,`data_cotacao` date
,`dt_pedido` datetime
,`qtde` double
,`valor` decimal(18,2)
,`perc_desconto` decimal(18,2)
,`valor_total` decimal(18,2)
,`nomeproduto` text
,`produto_id` int(11)
,`nomeestabelecimento` varchar(500)
,`taxacontrato` double
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_propostaaprovadaporrede`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_propostaaprovadaporrede` (
`id` int(11)
,`pessoa_id` int(11)
,`nome` varchar(500)
,`pedido_frotas_id` int(11)
,`descricaopedido` varchar(60)
,`estado_pedido_frotas_id` int(11)
,`motorista_entrada_id` int(11)
,`veiculos_id` int(11)
,`data_cotacao` date
,`obs` varchar(500)
,`valor_total` decimal(18,2)
,`valor_desconto` decimal(18,2)
,`valor_liquido` decimal(18,2)
,`system_unit_id` int(11)
,`departamento_unit_id` int(11)
,`system_users_id` int(11)
,`data_entrada_veiculo` datetime
,`data_retirada_veiculo` datetime
,`data_previsao_entrega` date
,`motorista_retirada_id` int(11)
,`km` int(11)
,`created_at` datetime
,`deleted_at` datetime
,`updated_at` datetime
,`cidade_id` int(11)
,`total_produtos_sem_desconto` decimal(18,2)
,`total_servicos_sem_desconto` decimal(18,2)
,`total_geral_sem_desconto` decimal(18,2)
,`total_produtos_com_desconto` decimal(18,2)
,`total_servicos_com_desconto` decimal(18,2)
,`desconto_contratual` decimal(18,2)
,`total_geral_com_desconto` decimal(18,2)
,`responsavel_tecnico` varchar(255)
,`datahora_inicioservico` datetime
,`datahora_fimservico` datetime
,`dt_pedido` datetime
,`data_aprovacao` datetime
,`nomeaprovador` text
,`data_autorizacao_pagamento` datetime
,`nomeaprovadorpagamento` text
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_propostas`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_propostas` (
`id` int(11)
,`pedido_frotas_id` int(11)
,`estabelecimento_id` int(11)
,`estado_pedido_frotas_id` int(11)
,`veiculos_id` int(11)
,`placa` varchar(200)
,`modelo` varchar(200)
,`data_cotacao` date
,`obs` varchar(500)
,`valor_total` decimal(18,2)
,`valor_desconto` decimal(18,2)
,`valor_liquido` decimal(18,2)
,`system_unit_id` int(11)
,`departamento_unit_id` int(11)
,`system_users_id` int(11)
,`data_entrada_veiculo` datetime
,`horimetro_entrada_aeronave` decimal(18,3)
,`ciclos_entrada_aeronave` decimal(18,3)
,`data_retirada_veiculo` datetime
,`horimetro_retirada_aeronave` decimal(18,3)
,`ciclos_retirada_aeronave` decimal(18,3)
,`data_previsao_entrega` date
,`km` int(11)
,`ciclos` decimal(18,3)
,`created_at` datetime
,`updated_at` datetime
,`deleted_at` datetime
,`responsavel_tecnico` varchar(255)
,`datahora_inicioservico` datetime
,`horimetro_inicioservico` decimal(18,3)
,`ciclos_inicioservico` decimal(18,3)
,`datahora_fimservico` datetime
,`horimetro_fimservico` decimal(18,3)
,`ciclos_fimservico` decimal(18,3)
,`total_produtos_sem_desconto` decimal(18,2)
,`total_servicos_sem_desconto` decimal(18,2)
,`total_geral_sem_desconto` decimal(18,2)
,`total_produtos_com_desconto` decimal(18,2)
,`total_servicos_com_desconto` decimal(18,2)
,`desconto_contratual` decimal(18,2)
,`motorista_entrada_id` int(11)
,`total_geral_com_desconto` decimal(18,2)
,`entidade_id` int(11)
,`cidade_id` int(11)
,`motorista_retirada_id` int(11)
,`data_limite_resposta` date
,`estado_pedido_frotas1_id` int(11)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_redescredenciadas`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_redescredenciadas` (
`id` int(11)
,`nome` varchar(500)
,`rua` varchar(500)
,`cidade_id` int(11)
,`nomecidade` varchar(255)
,`sigla` char(2)
,`email` varchar(255)
,`horariofuncionamento` varchar(255)
,`responsavel` char(0)
,`proprietario` char(0)
,`data_desativacao` datetime
,`fone` varchar(255)
,`NFProduto` int(1)
,`NFServico` int(1)
,`QTDEOS` int(1)
,`QTDEOSAndamento` int(1)
,`MediaAvaliacao` int(1)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_redesdisponiveis`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_redesdisponiveis` (
`id` int(11)
,`nome` varchar(500)
,`documento` varchar(20)
,`fone` varchar(255)
,`email` varchar(255)
,`selo` tinyint(4)
,`cidade_id` int(11)
,`estado_id` int(11)
,`ativo` char(1)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_relatoriomanutencao`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_relatoriomanutencao` (
`propostas_id` int(11)
,`pedido_frotas_id` int(11)
,`dt_pedido` datetime
,`estado_pedido_id` int(11)
,`veiculos_id` int(11)
,`km` decimal(18,3)
,`valor_total_pedido` double
,`valor_total_proposta` double
,`valor_desconto_proposta` double
,`valor_liquido_proposta` double
,`system_user_unit_id` int(11)
,`system_users_id` int(11)
,`departamento_unit_id` int(11)
,`entidade_id` int(11)
,`pessoa_id` int(11)
,`estado_proposta_id` int(11)
,`motorista_entrada_id` int(11)
,`motorista_retirada_id` int(11)
,`valor_totalp` decimal(18,2)
,`valor_descontop` decimal(18,2)
,`valor_liquidop` decimal(18,2)
,`data_entrada_veiculo` datetime
,`data_retirada_veiculo` datetime
,`data_previsao_entrega` date
,`datahora_inicioservico` datetime
,`datahora_fimservico` datetime
,`qtd_servico` double
,`valor_servico` decimal(62,2)
,`perc_desc_servico` decimal(62,2)
,`valor_total_servico` decimal(62,2)
,`qtd_produto` double
,`valor_produto` decimal(62,2)
,`perc_desc_produto` decimal(62,2)
,`valor_total_produto` decimal(62,2)
,`unidade` text
,`placa` varchar(100)
,`marca_id` int(11)
,`marca` varchar(500)
,`modelo_id` int(11)
,`modelo` varchar(500)
,`anof` int(11)
,`anom` int(11)
,`nomepessoa` varchar(500)
,`cidade_id` int(11)
,`cidade` varchar(255)
,`estado_id` int(11)
,`estado` char(2)
,`cnpj` varchar(20)
,`descricaopedido` varchar(60)
,`tipo_manutencao_id` int(11)
,`tipomanutencao` varchar(255)
,`nomeaprovador` mediumtext
,`system_unit_id` int(11)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_relatoriopecasveiculos`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_relatoriopecasveiculos` (
`propostas_id` int(11)
,`pedido_frotas_id` int(11)
,`estado_pedido_frotas_id` int(11)
,`dt_pedido` datetime
,`dt_finalizacao` date
,`system_unit_id` int(11)
,`departamento_unit_id` int(11)
,`data_historico` datetime /* mariadb-5.3 */
,`nome_usuario` mediumtext
,`pessoa_id` int(11)
,`nome_estabelecimento` varchar(500)
,`veiculos_id` int(11)
,`placa` varchar(100)
,`marca` varchar(500)
,`modelo` varchar(500)
,`km` decimal(18,3)
,`desconto_contratual` decimal(18,2)
,`tipo` int(11)
,`nome_produto_servico` text
,`valor_unitario_produto` decimal(18,2)
,`valor_unitario_servico` decimal(18,2)
,`qtde_produto` double
,`qtde_servico` double
,`perc_desconto_produto` decimal(18,2)
,`perc_desconto_servico` decimal(18,2)
,`valor_total_produto` decimal(18,2)
,`valor_total_servico` decimal(18,2)
,`km_garantia_produto` int(11)
,`km_garantia_servico` int(11)
,`dias_garantia_produto` int(11)
,`dias_garantia_servico` int(11)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_relatorioporrede_sintetico`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_relatorioporrede_sintetico` (
`proposta_id` int(11)
,`pedido_id` int(11)
,`pessoa_id` int(11)
,`system_unit_id` int(11)
,`departamento_unit_id` int(11)
,`qtd_proposta_recebida` decimal(22,0)
,`dt_abertura` datetime /* mariadb-5.3 */
,`qtd_proposta_finalizado` decimal(22,0)
,`dt_finalizado` datetime /* mariadb-5.3 */
,`dt_aprovado` datetime /* mariadb-5.3 */
,`qtd_proposta_entregue` decimal(22,0)
,`qtd_proposta_aguardando` decimal(22,0)
,`vl_produto` decimal(40,2)
,`vl_servico` decimal(40,2)
,`vl_total` decimal(40,2)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `view_saldoempenhocompras`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `view_saldoempenhocompras` (
`saldo_departamento_id` int(11)
,`numero_documento_empenho` varchar(255)
,`entidade_id` int(11)
,`system_unit_id` int(11)
,`departamento_unit_id` int(11)
,`datatransacao` date
,`mes` char(2)
,`ano` char(4)
,`estado_pedido_venda_id` int(11)
,`documento_empenho` varchar(255)
,`total_produtos` double
,`saldo_empenho` double
,`saldoatual` double
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_propostas_duplicadas_grupo`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_propostas_duplicadas_grupo` (
`pedido_frotas_id` int(11)
,`pessoa_id` int(11)
,`qtd` bigint(21)
);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `administradora`
--
ALTER TABLE `administradora`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `alerta_program`
--
ALTER TABLE `alerta_program`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_alerta_program_1` (`system_program_id`),
  ADD KEY `fk_alerta_program_2` (`system_unit_id`),
  ADD KEY `fk_alerta_program_3` (`entidade_id`),
  ADD KEY `fk_alerta_program_4` (`system_users_id`);

--
-- Índices de tabela `anexos_seguros`
--
ALTER TABLE `anexos_seguros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_anexos_seguros_1` (`seguros_id`);

--
-- Índices de tabela `anexos_veiculo`
--
ALTER TABLE `anexos_veiculo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_anexos_veiculo_1` (`veiculos_id`);

--
-- Índices de tabela `aprovador`
--
ALTER TABLE `aprovador`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `aprovador_frotas`
--
ALTER TABLE `aprovador_frotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_aprovador_frotas_1` (`system_users_id`);

--
-- Índices de tabela `autorizacao_pedido`
--
ALTER TABLE `autorizacao_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_autorizar_pedido_1` (`pedido_frotas_id`),
  ADD KEY `fk_autorizar_pedido_3` (`veiculos_id`),
  ADD KEY `fk_autorizar_pedido_5` (`system_users_id`);

--
-- Índices de tabela `cartao`
--
ALTER TABLE `cartao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `categoria_cliente`
--
ALTER TABLE `categoria_cliente`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `categoria_cnh`
--
ALTER TABLE `categoria_cnh`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `centrocusto`
--
ALTER TABLE `centrocusto`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cep_cache`
--
ALTER TABLE `cep_cache`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cidade`
--
ALTER TABLE `cidade`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nome_estado` (`nome`,`estado_id`),
  ADD KEY `idx_nome` (`nome`);

--
-- Índices de tabela `cidade_pedido`
--
ALTER TABLE `cidade_pedido`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cidade_pedido_frotas`
--
ALTER TABLE `cidade_pedido_frotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cidade_pedido_frotas_1` (`cidade_id`),
  ADD KEY `fk_cidade_pedido_frotas_2` (`pedido_frotas_id`);

--
-- Índices de tabela `comentario_proposta`
--
ALTER TABLE `comentario_proposta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comentario_proposta_1` (`propostas_id`),
  ADD KEY `fk_comentario_proposta_2` (`system_users_id`);

--
-- Índices de tabela `condicao_pagamento`
--
ALTER TABLE `condicao_pagamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `condutor`
--
ALTER TABLE `condutor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_condutor_1` (`system_unit_id`),
  ADD KEY `fk_condutor_2` (`departamento_unit_id`),
  ADD KEY `fk_condutor_3` (`system_users_id`);

--
-- Índices de tabela `conta`
--
ALTER TABLE `conta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_conta_8` (`pedido_frotas_id`);

--
-- Índices de tabela `conta_anexo`
--
ALTER TABLE `conta_anexo`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `corveiculo`
--
ALTER TABLE `corveiculo`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cotacao`
--
ALTER TABLE `cotacao`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `cotacao_pedido` (`pedido_id`,`pessoa_id`);

--
-- Índices de tabela `cotacao_historico`
--
ALTER TABLE `cotacao_historico`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `departamento_unit`
--
ALTER TABLE `departamento_unit`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `dispositivos`
--
ALTER TABLE `dispositivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_dispositivos_1` (`tipo_finalidade_id`);

--
-- Índices de tabela `dispositivos_solicitados`
--
ALTER TABLE `dispositivos_solicitados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_dispositivos_solicitados_3` (`system_unit_id`),
  ADD KEY `fk_dispositivos_solicitados_6` (`dispositivos_id`),
  ADD KEY `fk_dispositivos_solicitados_4` (`departamento_unit_id`),
  ADD KEY `fk_dispositivos_solicitados_1` (`veiculos_id`),
  ADD KEY `fk_dispositivos_solicitados_5` (`system_users_id`),
  ADD KEY `fk_dispositivos_solicitados_2` (`status_dispositivos_id`);

--
-- Índices de tabela `documentos_cotacao`
--
ALTER TABLE `documentos_cotacao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `documentos_pedido`
--
ALTER TABLE `documentos_pedido`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `documentos_pedido_frotas`
--
ALTER TABLE `documentos_pedido_frotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_documentos_pedido_frotas_1` (`pedido_frotas_id`);

--
-- Índices de tabela `documentos_pessoa`
--
ALTER TABLE `documentos_pessoa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_documentos_pessoa_1` (`tipo_documento_id`),
  ADD KEY `fk_documentos_pessoa_2` (`pessoa_id`);

--
-- Índices de tabela `documentos_propostas`
--
ALTER TABLE `documentos_propostas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_documentos_propostas_1` (`propostas_id`),
  ADD KEY `fk_documentos_propostas_2` (`tipo_documentos_propostas_id`);

--
-- Índices de tabela `documento_autorizacao_pedido`
--
ALTER TABLE `documento_autorizacao_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_documento_autorizacao_pedido_1` (`autorizacao_pedido_id`);

--
-- Índices de tabela `dotacao_pedido_frotas`
--
ALTER TABLE `dotacao_pedido_frotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_dotacao_pedido_frotas_3` (`propostas_id`),
  ADD KEY `fk_dotacao_pedido_frotas_1` (`pedido_frotas_id`),
  ADD KEY `fk_dotacao_pedido_frotas_2` (`saldo_departamento_id`);

--
-- Índices de tabela `email_template`
--
ALTER TABLE `email_template`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `entidade`
--
ALTER TABLE `entidade`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_entidade_1` (`administradora_id`);

--
-- Índices de tabela `error_log_crontab`
--
ALTER TABLE `error_log_crontab`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `especie`
--
ALTER TABLE `especie`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `estado`
--
ALTER TABLE `estado`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `estado_pedido`
--
ALTER TABLE `estado_pedido`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `estado_pedido_aprovador`
--
ALTER TABLE `estado_pedido_aprovador`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `estado_pedido_frotas`
--
ALTER TABLE `estado_pedido_frotas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `estado_pedido_frotas_aprovador`
--
ALTER TABLE `estado_pedido_frotas_aprovador`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_estado_pedido_frotas_aprovador_2` (`estado_pedido_frotas_id`),
  ADD KEY `fk_estado_pedido_frotas_aprovador_1` (`aprovador_frotas_id`);

--
-- Índices de tabela `estado_pedido_venda`
--
ALTER TABLE `estado_pedido_venda`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `etapa_negociacao`
--
ALTER TABLE `etapa_negociacao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `fabricante`
--
ALTER TABLE `fabricante`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `familia`
--
ALTER TABLE `familia`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `familia_produto`
--
ALTER TABLE `familia_produto`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `fatura`
--
ALTER TABLE `fatura`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `forma_pagamento`
--
ALTER TABLE `forma_pagamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `fotos_veiculos`
--
ALTER TABLE `fotos_veiculos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_fotos_veiculos_1` (`veiculos_id`);

--
-- Índices de tabela `grupo_pessoa`
--
ALTER TABLE `grupo_pessoa`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `itens_cotacao`
--
ALTER TABLE `itens_cotacao`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `CHK_UNIQUE` (`pedido_venda_id`,`produto_id`);

--
-- Índices de tabela `itens_pedido_frotas`
--
ALTER TABLE `itens_pedido_frotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_itens_pedido_frotas_2` (`produto_id`) USING BTREE,
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_idold` (`idold`);

--
-- Índices de tabela `itens_propostas`
--
ALTER TABLE `itens_propostas`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `fk_itens_propostas_1` (`propostas_id`) USING BTREE,
  ADD KEY `fk_itens_propostas_4` (`tipo_pecas_id`) USING BTREE,
  ADD KEY `fk_itens_propostas_3` (`estado_pedido_frotas_id`) USING BTREE;

--
-- Índices de tabela `manutencao_garantia`
--
ALTER TABLE `manutencao_garantia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_manutencao_garantia_3` (`pedido_frotas_id`),
  ADD KEY `fk_manutencao_garantia_4` (`propostas_id`),
  ADD KEY `fk_manutencao_1` (`itens_propostas_id`),
  ADD KEY `fk_manutencao_2` (`veiculos_id`);

--
-- Índices de tabela `marca`
--
ALTER TABLE `marca`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `matriz_estado_pedido`
--
ALTER TABLE `matriz_estado_pedido`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `matriz_estado_pedido_frotas`
--
ALTER TABLE `matriz_estado_pedido_frotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_matriz_estado_pedido_frotas_1` (`estado_pedido_frotas_origem_id`),
  ADD KEY `fk_matriz_estado_pedido_frotas_2` (`estado_pedido_frotas_destino_id`);

--
-- Índices de tabela `modelo`
--
ALTER TABLE `modelo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_modelo_10` (`especie_id`),
  ADD KEY `fk_modelo_20` (`propriedade_id`),
  ADD KEY `fk_modelo_30` (`familia_id`),
  ADD KEY `fk_modelo_40` (`tipo_veiculo_id`),
  ADD KEY `fk_modelo_50` (`tipo_combustivel_id`),
  ADD KEY `fk_modelo_60` (`marca_id`);

--
-- Índices de tabela `modelo_ano`
--
ALTER TABLE `modelo_ano`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_modelo_ano_1` (`modelo_id`);

--
-- Índices de tabela `movimento_dispositivos`
--
ALTER TABLE `movimento_dispositivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_movimento_dispositivos_1` (`dispositivos_solicitados_id`),
  ADD KEY `fk_movimento_dispositivos_2` (`veiculos_id`),
  ADD KEY `fk_movimento_dispositivos_3` (`estabelecimento_id`),
  ADD KEY `fk_movimento_dispositivos_4` (`condutor_id`);

--
-- Índices de tabela `multas`
--
ALTER TABLE `multas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_multas_1` (`veiculos_id`),
  ADD KEY `fk_multas_2` (`condutor_id`),
  ADD KEY `fk_multas_3` (`system_unit_id`),
  ADD KEY `fk_multas_4` (`departamento_unit_id`),
  ADD KEY `fk_multas_5` (`system_users_id`);

--
-- Índices de tabela `multas_anexos`
--
ALTER TABLE `multas_anexos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_multas_anexos_1` (`multas_id`);

--
-- Índices de tabela `negociacao`
--
ALTER TABLE `negociacao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `negociacao_arquivo`
--
ALTER TABLE `negociacao_arquivo`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `negociacao_atividade`
--
ALTER TABLE `negociacao_atividade`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `negociacao_historico_etapa`
--
ALTER TABLE `negociacao_historico_etapa`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `negociacao_item`
--
ALTER TABLE `negociacao_item`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `negociacao_observacao`
--
ALTER TABLE `negociacao_observacao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `notas_system_unit`
--
ALTER TABLE `notas_system_unit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notas_1` (`system_unit_id`);

--
-- Índices de tabela `nota_fiscal`
--
ALTER TABLE `nota_fiscal`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `nota_fiscal_item`
--
ALTER TABLE `nota_fiscal_item`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `origem_contato`
--
ALTER TABLE `origem_contato`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `ouvidoria`
--
ALTER TABLE `ouvidoria`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `pedido_as_cliente`
--
ALTER TABLE `pedido_as_cliente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedido_as_cliente_1` (`pedido_frotas_id`),
  ADD KEY `fk_pedido_as_cliente_2` (`pessoa_id`);

--
-- Índices de tabela `pedido_frotas`
--
ALTER TABLE `pedido_frotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedidomanutencao_1` (`estado_pedido_frotas_id`),
  ADD KEY `fk_pedidomanutencao_2` (`estabelecimento_id`),
  ADD KEY `fk_pedidofrotas_5` (`condutor_entrada_id`),
  ADD KEY `fk_pedidofrotas_6` (`tipo_manutencao_id`),
  ADD KEY `fk_pedidofrotas_7` (`negociacao_id`),
  ADD KEY `fk_pedidofrotas_8` (`condicao_pagamento_id`),
  ADD KEY `fk_pedidofrotas_9` (`system_unit_id`),
  ADD KEY `fk_pedidofrotas_10` (`departamento_unit_id`),
  ADD KEY `fk_pedidofrotas_11` (`system_users_id`),
  ADD KEY `fk_pedidofrotas_12` (`condutor_retirada_id`);

--
-- Índices de tabela `pedido_frotas_historico`
--
ALTER TABLE `pedido_frotas_historico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedido_frotas_historico_3` (`estado_pedido_frotas_id`);

--
-- Índices de tabela `pedido_historico`
--
ALTER TABLE `pedido_historico`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pedido_seguimento`
--
ALTER TABLE `pedido_seguimento`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pedido_venda`
--
ALTER TABLE `pedido_venda`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pedido_venda_item`
--
ALTER TABLE `pedido_venda_item`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pessoa`
--
ALTER TABLE `pessoa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cidade` (`cidade_id`),
  ADD KEY `idx_nome` (`id`),
  ADD KEY `idx_pessoa_idold` (`idold`);

--
-- Índices de tabela `pessoa_contato`
--
ALTER TABLE `pessoa_contato`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pessoa_departamento`
--
ALTER TABLE `pessoa_departamento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pessoa_departamento_1` (`pessoa_id`),
  ADD KEY `fk_pessoa_departamento_2` (`departamento_unit_id`);

--
-- Índices de tabela `pessoa_endereco`
--
ALTER TABLE `pessoa_endereco`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pessoa_grupo`
--
ALTER TABLE `pessoa_grupo`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `produto`
--
ALTER TABLE `produto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_produto_10` (`nome`(1024)),
  ADD KEY `idx_familia` (`familia_produto_id`),
  ADD KEY `idx_tipo_produto` (`tipo_produto_id`),
  ADD KEY `idx_system_unit` (`system_unit_id`);

--
-- Índices de tabela `produto_system_unit`
--
ALTER TABLE `produto_system_unit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_produto_system_unit_1` (`system_unit_id`),
  ADD KEY `fk_produto_system_unit_2` (`produto_id`);

--
-- Índices de tabela `propostas`
--
ALTER TABLE `propostas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_propostas_1` (`pedido_frotas_id`),
  ADD KEY `fk_propostas_2` (`pessoa_id`),
  ADD KEY `fk_propostas_3` (`estado_pedido_frotas_id`),
  ADD KEY `fk_propostas_5` (`veiculos_id`),
  ADD KEY `fk_propostas_7` (`system_unit_id`),
  ADD KEY `fk_propostas_8` (`departamento_unit_id`),
  ADD KEY `fk_propostas_9` (`system_users_id`),
  ADD KEY `fk_propostas_81` (`entidade_id`),
  ADD KEY `fk_propostas_12` (`motorista_entrada_id`),
  ADD KEY `fk_propostas_10` (`motorista_retirada_id`),
  ADD KEY `IDINDEX` (`pedido_frotas_id`,`pessoa_id`);

--
-- Índices de tabela `propostas_historico`
--
ALTER TABLE `propostas_historico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_propostas_historico_1` (`propostas_id`),
  ADD KEY `fk_propostas_historico_2` (`estado_pedido_frotas_id`),
  ADD KEY `fk_propostas_historico_4` (`aprovador_frotas_id`);

--
-- Índices de tabela `propriedade`
--
ALTER TABLE `propriedade`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `saldo_departamento`
--
ALTER TABLE `saldo_departamento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_saldo_departamento_unit_1` (`departamento_unit_id`);

--
-- Índices de tabela `saldo_entidade_contrato`
--
ALTER TABLE `saldo_entidade_contrato`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_saldo_entidade_contrato_1` (`entidade_id`),
  ADD KEY `fk_saldo_entidade_contrato_2` (`system_users_id`);

--
-- Índices de tabela `saldo_veiculo`
--
ALTER TABLE `saldo_veiculo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_saldo_veiculo_1` (`veiculos_id`);

--
-- Índices de tabela `seguimento`
--
ALTER TABLE `seguimento`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `seguimento_pedido_frotas`
--
ALTER TABLE `seguimento_pedido_frotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_seguimento_pedido_frotas_1` (`pedido_frotas_id`),
  ADD KEY `fk_seguimento_pedido_frotas_2` (`seguimento_id`);

--
-- Índices de tabela `seguimento_pessoa`
--
ALTER TABLE `seguimento_pessoa`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `seguros`
--
ALTER TABLE `seguros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_seguros_1` (`saldo_entidade_contrato_id`);

--
-- Índices de tabela `status_dispositivos`
--
ALTER TABLE `status_dispositivos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `status_multas`
--
ALTER TABLE `status_multas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `status_veiculo`
--
ALTER TABLE `status_veiculo`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `system_access_log`
--
ALTER TABLE `system_access_log`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `system_access_notification_log`
--
ALTER TABLE `system_access_notification_log`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `system_change_log`
--
ALTER TABLE `system_change_log`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `system_document`
--
ALTER TABLE `system_document`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_system_document_2` (`category_id`),
  ADD KEY `fk_system_document_1` (`system_user_id`);

--
-- Índices de tabela `system_document_category`
--
ALTER TABLE `system_document_category`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `system_document_group`
--
ALTER TABLE `system_document_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_system_document_group_2` (`document_id`),
  ADD KEY `fk_system_document_group_1` (`system_group_id`);

--
-- Índices de tabela `system_document_user`
--
ALTER TABLE `system_document_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_system_document_user_2` (`document_id`),
  ADD KEY `fk_system_document_user_1` (`system_user_id`);

--
-- Índices de tabela `system_group`
--
ALTER TABLE `system_group`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `system_group_program`
--
ALTER TABLE `system_group_program`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_system_group_program_1` (`system_program_id`),
  ADD KEY `fk_system_group_program_2` (`system_group_id`);

--
-- Índices de tabela `system_message`
--
ALTER TABLE `system_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_system_message_1` (`system_user_id`),
  ADD KEY `fk_system_message_2` (`system_user_to_id`);

--
-- Índices de tabela `system_notification`
--
ALTER TABLE `system_notification`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `system_preference`
--
ALTER TABLE `system_preference`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `system_program`
--
ALTER TABLE `system_program`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `system_request_log`
--
ALTER TABLE `system_request_log`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `system_sql_log`
--
ALTER TABLE `system_sql_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_login` (`login`(768));

--
-- Índices de tabela `system_unit`
--
ALTER TABLE `system_unit`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `system_users`
--
ALTER TABLE `system_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_system_user_1` (`system_unit_id`),
  ADD KEY `fk_system_user_2` (`frontpage_id`);

--
-- Índices de tabela `system_user_departamento_unit`
--
ALTER TABLE `system_user_departamento_unit`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `system_user_group`
--
ALTER TABLE `system_user_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_system_user_group_1` (`system_group_id`),
  ADD KEY `fk_system_user_group_2` (`system_user_id`);

--
-- Índices de tabela `system_user_program`
--
ALTER TABLE `system_user_program`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_system_user_program_1` (`system_program_id`),
  ADD KEY `fk_system_user_program_2` (`system_user_id`);

--
-- Índices de tabela `system_user_unit`
--
ALTER TABLE `system_user_unit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_system_user_unit_1` (`system_user_id`),
  ADD KEY `fk_system_user_unit_2` (`system_unit_id`);

--
-- Índices de tabela `tabela_fipe_final_corrigida`
--
ALTER TABLE `tabela_fipe_final_corrigida`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tabela_orse`
--
ALTER TABLE `tabela_orse`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tabela_orse_10` (`codigo`);

--
-- Índices de tabela `taxas_pessoa`
--
ALTER TABLE `taxas_pessoa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_taxas_pessoa_1` (`system_unit_id`),
  ADD KEY `fk_taxas_pessoa_3` (`system_users_id`),
  ADD KEY `fk_taxas_pessoa_4` (`entidade_id`),
  ADD KEY `fk_taxas_pessoa_5` (`pessoa_id`);

--
-- Índices de tabela `tipo_anexo`
--
ALTER TABLE `tipo_anexo`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipo_atividade`
--
ALTER TABLE `tipo_atividade`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipo_cliente`
--
ALTER TABLE `tipo_cliente`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipo_combustivel`
--
ALTER TABLE `tipo_combustivel`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipo_conta`
--
ALTER TABLE `tipo_conta`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipo_documento`
--
ALTER TABLE `tipo_documento`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipo_documentos_propostas`
--
ALTER TABLE `tipo_documentos_propostas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipo_finalidade`
--
ALTER TABLE `tipo_finalidade`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipo_manutencao`
--
ALTER TABLE `tipo_manutencao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipo_ouvidoria`
--
ALTER TABLE `tipo_ouvidoria`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipo_pecas`
--
ALTER TABLE `tipo_pecas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_desscricao` (`descricao`);

--
-- Índices de tabela `tipo_pedido`
--
ALTER TABLE `tipo_pedido`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipo_produto`
--
ALTER TABLE `tipo_produto`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipo_seguro`
--
ALTER TABLE `tipo_seguro`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipo_veiculo`
--
ALTER TABLE `tipo_veiculo`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `unidade_medida`
--
ALTER TABLE `unidade_medida`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `vehicletoken`
--
ALTER TABLE `vehicletoken`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vehicletoken_1` (`veiculos_id`);

--
-- Índices de tabela `veiculos`
--
ALTER TABLE `veiculos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_veiculos_4` (`modelo_id`),
  ADD KEY `fk_veiculos_7` (`tipo_veiculo_id`),
  ADD KEY `fk_veiculos_1` (`tipo_combustivel_id`),
  ADD KEY `fk_veiculos_2` (`dispositivos_id`),
  ADD KEY `fk_veiculos_5` (`propriedade_id`),
  ADD KEY `fk_veiculos_8` (`system_unit_id`),
  ADD KEY `fk_veiculos_3` (`marca_id`),
  ADD KEY `fk_veiculos_6` (`corveiculo_id`),
  ADD KEY `fk_veiculos_9` (`departamento_unit_id`),
  ADD KEY `fk_veiculos_10` (`system_users_id`),
  ADD KEY `fk_veiculos_120` (`especie_id`),
  ADD KEY `fk_veiculos_130` (`familia_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `administradora`
--
ALTER TABLE `administradora`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `alerta_program`
--
ALTER TABLE `alerta_program`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `anexos_seguros`
--
ALTER TABLE `anexos_seguros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `anexos_veiculo`
--
ALTER TABLE `anexos_veiculo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `aprovador`
--
ALTER TABLE `aprovador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `aprovador_frotas`
--
ALTER TABLE `aprovador_frotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `autorizacao_pedido`
--
ALTER TABLE `autorizacao_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cartao`
--
ALTER TABLE `cartao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `categoria_cliente`
--
ALTER TABLE `categoria_cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `categoria_cnh`
--
ALTER TABLE `categoria_cnh`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `centrocusto`
--
ALTER TABLE `centrocusto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cep_cache`
--
ALTER TABLE `cep_cache`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cidade`
--
ALTER TABLE `cidade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cidade_pedido`
--
ALTER TABLE `cidade_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cidade_pedido_frotas`
--
ALTER TABLE `cidade_pedido_frotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `comentario_proposta`
--
ALTER TABLE `comentario_proposta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `condicao_pagamento`
--
ALTER TABLE `condicao_pagamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `condutor`
--
ALTER TABLE `condutor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `conta`
--
ALTER TABLE `conta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `conta_anexo`
--
ALTER TABLE `conta_anexo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `corveiculo`
--
ALTER TABLE `corveiculo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cotacao`
--
ALTER TABLE `cotacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cotacao_historico`
--
ALTER TABLE `cotacao_historico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `departamento_unit`
--
ALTER TABLE `departamento_unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `dispositivos`
--
ALTER TABLE `dispositivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `dispositivos_solicitados`
--
ALTER TABLE `dispositivos_solicitados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `documentos_cotacao`
--
ALTER TABLE `documentos_cotacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `documentos_pedido`
--
ALTER TABLE `documentos_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `documentos_pedido_frotas`
--
ALTER TABLE `documentos_pedido_frotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `documentos_pessoa`
--
ALTER TABLE `documentos_pessoa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `documentos_propostas`
--
ALTER TABLE `documentos_propostas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `documento_autorizacao_pedido`
--
ALTER TABLE `documento_autorizacao_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `dotacao_pedido_frotas`
--
ALTER TABLE `dotacao_pedido_frotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `email_template`
--
ALTER TABLE `email_template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `entidade`
--
ALTER TABLE `entidade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `error_log_crontab`
--
ALTER TABLE `error_log_crontab`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `especie`
--
ALTER TABLE `especie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estado`
--
ALTER TABLE `estado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estado_pedido`
--
ALTER TABLE `estado_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estado_pedido_aprovador`
--
ALTER TABLE `estado_pedido_aprovador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estado_pedido_frotas`
--
ALTER TABLE `estado_pedido_frotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estado_pedido_frotas_aprovador`
--
ALTER TABLE `estado_pedido_frotas_aprovador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estado_pedido_venda`
--
ALTER TABLE `estado_pedido_venda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `etapa_negociacao`
--
ALTER TABLE `etapa_negociacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fabricante`
--
ALTER TABLE `fabricante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `familia`
--
ALTER TABLE `familia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `familia_produto`
--
ALTER TABLE `familia_produto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fatura`
--
ALTER TABLE `fatura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `forma_pagamento`
--
ALTER TABLE `forma_pagamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fotos_veiculos`
--
ALTER TABLE `fotos_veiculos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `grupo_pessoa`
--
ALTER TABLE `grupo_pessoa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `itens_cotacao`
--
ALTER TABLE `itens_cotacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `itens_pedido_frotas`
--
ALTER TABLE `itens_pedido_frotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `itens_propostas`
--
ALTER TABLE `itens_propostas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `manutencao_garantia`
--
ALTER TABLE `manutencao_garantia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `marca`
--
ALTER TABLE `marca`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `matriz_estado_pedido`
--
ALTER TABLE `matriz_estado_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `matriz_estado_pedido_frotas`
--
ALTER TABLE `matriz_estado_pedido_frotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `modelo`
--
ALTER TABLE `modelo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `modelo_ano`
--
ALTER TABLE `modelo_ano`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `movimento_dispositivos`
--
ALTER TABLE `movimento_dispositivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `multas`
--
ALTER TABLE `multas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `multas_anexos`
--
ALTER TABLE `multas_anexos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `negociacao`
--
ALTER TABLE `negociacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `negociacao_arquivo`
--
ALTER TABLE `negociacao_arquivo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `negociacao_atividade`
--
ALTER TABLE `negociacao_atividade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `negociacao_historico_etapa`
--
ALTER TABLE `negociacao_historico_etapa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `negociacao_item`
--
ALTER TABLE `negociacao_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `negociacao_observacao`
--
ALTER TABLE `negociacao_observacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `notas_system_unit`
--
ALTER TABLE `notas_system_unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `nota_fiscal`
--
ALTER TABLE `nota_fiscal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `nota_fiscal_item`
--
ALTER TABLE `nota_fiscal_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `origem_contato`
--
ALTER TABLE `origem_contato`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ouvidoria`
--
ALTER TABLE `ouvidoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedido_as_cliente`
--
ALTER TABLE `pedido_as_cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedido_frotas`
--
ALTER TABLE `pedido_frotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedido_frotas_historico`
--
ALTER TABLE `pedido_frotas_historico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedido_historico`
--
ALTER TABLE `pedido_historico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedido_seguimento`
--
ALTER TABLE `pedido_seguimento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedido_venda`
--
ALTER TABLE `pedido_venda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedido_venda_item`
--
ALTER TABLE `pedido_venda_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pessoa`
--
ALTER TABLE `pessoa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pessoa_contato`
--
ALTER TABLE `pessoa_contato`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pessoa_departamento`
--
ALTER TABLE `pessoa_departamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pessoa_endereco`
--
ALTER TABLE `pessoa_endereco`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pessoa_grupo`
--
ALTER TABLE `pessoa_grupo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produto`
--
ALTER TABLE `produto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produto_system_unit`
--
ALTER TABLE `produto_system_unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `propostas`
--
ALTER TABLE `propostas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `propostas_historico`
--
ALTER TABLE `propostas_historico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `propriedade`
--
ALTER TABLE `propriedade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `saldo_departamento`
--
ALTER TABLE `saldo_departamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `saldo_entidade_contrato`
--
ALTER TABLE `saldo_entidade_contrato`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `saldo_veiculo`
--
ALTER TABLE `saldo_veiculo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `seguimento`
--
ALTER TABLE `seguimento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `seguimento_pedido_frotas`
--
ALTER TABLE `seguimento_pedido_frotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `seguimento_pessoa`
--
ALTER TABLE `seguimento_pessoa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `seguros`
--
ALTER TABLE `seguros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `status_dispositivos`
--
ALTER TABLE `status_dispositivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `status_multas`
--
ALTER TABLE `status_multas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `status_veiculo`
--
ALTER TABLE `status_veiculo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `system_access_log`
--
ALTER TABLE `system_access_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `system_access_notification_log`
--
ALTER TABLE `system_access_notification_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `system_group`
--
ALTER TABLE `system_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `system_program`
--
ALTER TABLE `system_program`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `system_sql_log`
--
ALTER TABLE `system_sql_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `system_unit`
--
ALTER TABLE `system_unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `system_users`
--
ALTER TABLE `system_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `system_user_departamento_unit`
--
ALTER TABLE `system_user_departamento_unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `system_user_group`
--
ALTER TABLE `system_user_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `system_user_unit`
--
ALTER TABLE `system_user_unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tabela_fipe_final_corrigida`
--
ALTER TABLE `tabela_fipe_final_corrigida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tabela_orse`
--
ALTER TABLE `tabela_orse`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `taxas_pessoa`
--
ALTER TABLE `taxas_pessoa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipo_anexo`
--
ALTER TABLE `tipo_anexo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipo_atividade`
--
ALTER TABLE `tipo_atividade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipo_cliente`
--
ALTER TABLE `tipo_cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipo_combustivel`
--
ALTER TABLE `tipo_combustivel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipo_conta`
--
ALTER TABLE `tipo_conta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipo_documento`
--
ALTER TABLE `tipo_documento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipo_documentos_propostas`
--
ALTER TABLE `tipo_documentos_propostas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipo_finalidade`
--
ALTER TABLE `tipo_finalidade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipo_manutencao`
--
ALTER TABLE `tipo_manutencao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipo_ouvidoria`
--
ALTER TABLE `tipo_ouvidoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipo_pecas`
--
ALTER TABLE `tipo_pecas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipo_pedido`
--
ALTER TABLE `tipo_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipo_produto`
--
ALTER TABLE `tipo_produto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipo_seguro`
--
ALTER TABLE `tipo_seguro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipo_veiculo`
--
ALTER TABLE `tipo_veiculo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `unidade_medida`
--
ALTER TABLE `unidade_medida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `vehicletoken`
--
ALTER TABLE `vehicletoken`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `veiculos`
--
ALTER TABLE `veiculos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Estrutura para view `viewsaldoempenho`
--
DROP TABLE IF EXISTS `viewsaldoempenho`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `viewsaldoempenho`  AS SELECT `pf`.`entidade_id` AS `entidade_id`, `pf`.`system_unit_id` AS `system_unit_id`, `pf`.`departamento_unit_id` AS `departamento_unit_id`, `pf`.`dt_pedido` AS `dt_pedido`, sum((select sum(`itens_pedido_frotas`.`valor_total`) from `itens_pedido_frotas` where `itens_pedido_frotas`.`pedido_frotas_id` = `pf`.`id` and `itens_pedido_frotas`.`tipo` = 2)) AS `total_servicos`, sum((select sum(`itens_pedido_frotas`.`valor_total`) from `itens_pedido_frotas` where `itens_pedido_frotas`.`pedido_frotas_id` = `pf`.`id` and `itens_pedido_frotas`.`tipo` = 1)) AS `total_produtos` FROM `pedido_frotas` AS `pf` WHERE `pf`.`estado_pedido_frotas_id` in (8,13,20) GROUP BY `pf`.`entidade_id`, `pf`.`system_unit_id`, `pf`.`departamento_unit_id`, `pf`.`dt_pedido` ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_aprovadores_unidade`
--
DROP TABLE IF EXISTS `view_aprovadores_unidade`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_aprovadores_unidade`  AS SELECT DISTINCT `su`.`id` AS `id`, `su`.`email` AS `email`, `su`.`name` AS `name`, `su`.`login` AS `login`, `epfa`.`aprovador_frotas_id` AS `aprovador_frotas_id`, `sug`.`system_group_id` AS `system_group_id`, `sus`.`system_unit_id` AS `system_unit_id`, `su`.`active` AS `active` FROM ((((`system_user_unit` `sus` join `system_users` `su` on(`su`.`id` = `sus`.`system_user_id`)) join `system_user_group` `sug` on(`sug`.`system_user_id` = `sus`.`system_user_id` and `sug`.`system_group_id` = 2)) join `aprovador_frotas` `af` on(`af`.`system_users_id` = `sug`.`system_user_id`)) join `estado_pedido_frotas_aprovador` `epfa` on(`epfa`.`aprovador_frotas_id` = `af`.`id` and `epfa`.`estado_pedido_frotas_id` = 13)) WHERE `su`.`active` = 'Y' ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_cidadeestado`
--
DROP TABLE IF EXISTS `view_cidadeestado`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_cidadeestado`  AS SELECT `c`.`id` AS `idcidade`, `c`.`nome` AS `nomecidade`, `e`.`id` AS `idestado`, `e`.`nome` AS `nomeestado`, `e`.`sigla` AS `sigla` FROM (`cidade` `c` join `estado` `e` on(`e`.`id` = `c`.`estado_id`)) ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_comparacaoprodutos`
--
DROP TABLE IF EXISTS `view_comparacaoprodutos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_comparacaoprodutos`  AS SELECT `pes`.`id` AS `estabelecimento_id`, `pes`.`nome` AS `nome_estabelecimento`, `pe`.`cidade_id` AS `cidade_id`, `c`.`nome` AS `nomecidade`, `c`.`estado_id` AS `estado_id`, `e`.`sigla` AS `uf`, `ip`.`produto_id` AS `produto_id`, `prod`.`id` AS `produto_id_real`, `prod`.`nome` AS `nome_produto`, `prod`.`tipo_produto_id` AS `tipo_produto_id`, `ip`.`id` AS `item_proposta_id`, `ip`.`valor` AS `valor` FROM (((((((`itens_propostas` `ip` left join `produto` `prod` on(`prod`.`id` = `ip`.`produto_id`)) left join `propostas` `p` on(`p`.`id` = `ip`.`propostas_id`)) left join `pessoa` `pes` on(`pes`.`id` = `p`.`pessoa_id`)) left join `pessoa_endereco` `pe` on(`pe`.`pessoa_id` = `pes`.`id`)) left join `cidade` `c` on(`c`.`id` = `pe`.`cidade_id`)) left join `estado` `e` on(`e`.`id` = `c`.`estado_id`)) join (select max(`ip2`.`id`) AS `max_id` from (`itens_propostas` `ip2` left join `propostas` `p2` on(`p2`.`id` = `ip2`.`propostas_id`)) where `ip2`.`produto_id` is not null group by `p2`.`pessoa_id`,`ip2`.`produto_id`) `ultimos` on(`ultimos`.`max_id` = `ip`.`id`)) ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_consumosrealizados`
--
DROP TABLE IF EXISTS `view_consumosrealizados`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_consumosrealizados`  AS SELECT DISTINCT `ped`.`id` AS `pedido_frotas_id`, `p`.`id` AS `propostas_id`, `ped`.`veiculos_id` AS `veiculos_id`, `v`.`placa` AS `placa`, `ma`.`descricao` AS `marca`, `m`.`descricao` AS `modelo`, `v`.`anof` AS `anof`, `ped`.`km` AS `km`, `pes`.`nome` AS `fornecedor`, `p`.`total_geral_com_desconto` AS `total_geral_com_desconto`, `pe`.`cidade_id` AS `cidade_id`, `c`.`nome` AS `nome_cidade`, `e`.`id` AS `estado_id`, `e`.`sigla` AS `sigla_estado`, `ped`.`mes` AS `mes`, `ped`.`ano` AS `ano`, `v`.`system_unit_id` AS `system_unit_id`, coalesce((select sum(`saldo_veiculo`.`valor_transacao`) from `saldo_veiculo` where `saldo_veiculo`.`veiculos_id` = `p`.`veiculos_id` and `saldo_veiculo`.`tipo_transacao` = 'C'),0) AS `valor_transacao_credito`, coalesce((select sum(`saldo_veiculo`.`valor_transacao`) from `saldo_veiculo` where `saldo_veiculo`.`veiculos_id` = `p`.`veiculos_id` and `saldo_veiculo`.`tipo_transacao` = 'D'),0) AS `valor_transacao_debito`, coalesce((select sum(`saldo_veiculo`.`valor_transacao`) from `saldo_veiculo` where `saldo_veiculo`.`veiculos_id` = `p`.`veiculos_id` and `saldo_veiculo`.`tipo_transacao` = 'C'),0) - coalesce((select sum(`saldo_veiculo`.`valor_transacao`) from `saldo_veiculo` where `saldo_veiculo`.`veiculos_id` = `p`.`veiculos_id` and `saldo_veiculo`.`tipo_transacao` = 'D'),0) AS `saldo_atual`, (select max(`ped2`.`km`) from (`propostas` `p2` join `pedido_frotas` `ped2` on(`ped2`.`id` = `p2`.`pedido_frotas_id`)) where `p2`.`veiculos_id` = `p`.`veiculos_id` and `ped2`.`km` < `ped`.`km` and `p2`.`estado_pedido_frotas_id` = 8) AS `km_anterior`, `ped`.`km`- coalesce((select max(`ped2`.`km`) from (`propostas` `p2` join `pedido_frotas` `ped2` on(`ped2`.`id` = `p2`.`pedido_frotas_id`)) where `p2`.`veiculos_id` = `p`.`veiculos_id` and `ped2`.`km` < `ped`.`km` and `p2`.`estado_pedido_frotas_id` = 8),0) AS `km_rodado`, round(`p`.`total_geral_com_desconto` / nullif(`ped`.`km` - coalesce((select max(`ped2`.`km`) from (`propostas` `p2` join `pedido_frotas` `ped2` on(`ped2`.`id` = `p2`.`pedido_frotas_id`)) where `p2`.`veiculos_id` = `p`.`veiculos_id` and `ped2`.`km` < `ped`.`km` and `p2`.`estado_pedido_frotas_id` = 8),0),0),2) AS `custo_por_km`, (select count(`p3`.`id`) from (`propostas` `p3` join `pedido_frotas` `ped3` on(`ped3`.`id` = `p3`.`pedido_frotas_id`)) where `p3`.`veiculos_id` = `p`.`veiculos_id` and `p3`.`estado_pedido_frotas_id` = 8 and `ped3`.`ano` = `ped`.`ano` and `ped3`.`mes` = `ped`.`mes`) AS `qtd_pedidos_mes`, (select sum(`p3`.`total_geral_com_desconto`) from (`propostas` `p3` join `pedido_frotas` `ped3` on(`ped3`.`id` = `p3`.`pedido_frotas_id`)) where `p3`.`veiculos_id` = `p`.`veiculos_id` and `p3`.`estado_pedido_frotas_id` = 8 and `ped3`.`ano` = `ped`.`ano` and `ped3`.`mes` = `ped`.`mes`) AS `total_mensal_manutencao`, round((select sum(`p3`.`total_geral_com_desconto`) from (`propostas` `p3` join `pedido_frotas` `ped3` on(`ped3`.`id` = `p3`.`pedido_frotas_id`)) where `p3`.`veiculos_id` = `p`.`veiculos_id` and `p3`.`estado_pedido_frotas_id` = 8 and `ped3`.`ano` = `ped`.`ano` and `ped3`.`mes` = `ped`.`mes`) / nullif((select count(`p3`.`id`) from (`propostas` `p3` join `pedido_frotas` `ped3` on(`ped3`.`id` = `p3`.`pedido_frotas_id`)) where `p3`.`veiculos_id` = `p`.`veiculos_id` and `p3`.`estado_pedido_frotas_id` = 8 and `ped3`.`ano` = `ped`.`ano` and `ped3`.`mes` = `ped`.`mes`),0),2) AS `custo_medio_mensal` FROM ((((((((`propostas` `p` left join `pessoa` `pes` on(`pes`.`id` = `p`.`pessoa_id`)) left join `pedido_frotas` `ped` on(`ped`.`id` = `p`.`pedido_frotas_id`)) left join `veiculos` `v` on(`v`.`id` = `ped`.`veiculos_id`)) left join `modelo` `m` on(`m`.`id` = `v`.`modelo_id`)) left join `marca` `ma` on(`ma`.`id` = `v`.`marca_id`)) left join `pessoa_endereco` `pe` on(`pe`.`cidade_id` = `pes`.`cidade_id`)) left join `cidade` `c` on(`c`.`id` = `pe`.`cidade_id`)) left join `estado` `e` on(`e`.`id` = `c`.`estado_id`)) WHERE `p`.`estado_pedido_frotas_id` = 8 ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_cotacao`
--
DROP TABLE IF EXISTS `view_cotacao`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_cotacao`  AS SELECT `c`.`id` AS `cotacao_id`, `c`.`data_cotacao` AS `data_cotacao`, `su`.`name` AS `unidade`, `dp`.`name` AS `departamento`, `pd`.`id` AS `pedido_id`, `pd`.`descricaopedido` AS `descricaopedido`, (select `sus`.`name` from (`pedido_historico` `ph` join `system_users` `sus` on(`sus`.`id` = `ph`.`aprovador_id`)) where `ph`.`pedido_venda_id` = `pd`.`id` and `ph`.`estado_pedido_venda_id` = 13 order by `ph`.`data_operacao` desc limit 1) AS `nomeautorizado`, `p`.`id` AS `pessoa_id`, `p`.`nome` AS `nomefornecedor`, `p`.`documento` AS `cnpj`, `cid`.`nome` AS `nomecidade`, `est`.`sigla` AS `uf` FROM (((((((`cotacao` `c` join `pessoa` `p` on(`p`.`id` = `c`.`pessoa_id`)) join `pessoa_endereco` `pe` on(`pe`.`pessoa_id` = `p`.`id` and `pe`.`principal` = 'T')) join `pedido` `pd` on(`pd`.`id` = `c`.`pedido_id` and `pd`.`cliente_id` = `c`.`pessoa_id`)) join `departamento_unit` `dp` on(`dp`.`id` = `pd`.`departamento_unit_id`)) join `system_unit` `su` on(`su`.`id` = `dp`.`system_unit_id`)) join `cidade` `cid` on(`cid`.`id` = `pd`.`cidade_id`)) join `estado` `est` on(`est`.`id` = `cid`.`estado_id`)) ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_departamento_unit_system_unit`
--
DROP TABLE IF EXISTS `view_departamento_unit_system_unit`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gestaonp3benefic`@`localhost` SQL SECURITY DEFINER VIEW `view_departamento_unit_system_unit`  AS SELECT `su`.`id` AS `system_unit_id`, `su`.`name` AS `name_system_unit`, `dp`.`name` AS `name_departamento_unit`, `dp`.`id` AS `id`, `dp`.`id` AS `departamento_unit_id` FROM (`system_unit` `su` join `departamento_unit` `dp` on(`dp`.`system_unit_id` = `su`.`id`)) ORDER BY `su`.`name` ASC, `dp`.`name` ASC ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_dotacao_pedido_frotas`
--
DROP TABLE IF EXISTS `view_dotacao_pedido_frotas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_dotacao_pedido_frotas`  AS SELECT DISTINCT `dpf`.`id` AS `id`, `dpf`.`saldo_departamento_id` AS `saldo_departamento_id`, `pf`.`id` AS `pedido_frotas_id`, `pf`.`descricaopedido` AS `descricaopedido`, `pf`.`estabelecimento_id` AS `estabelecimento_id`, `pf`.`veiculos_id` AS `veiculos_id`, `pf`.`km` AS `km`, `dpf`.`valor` AS `valor`, `dpf`.`saldo_atual` AS `saldo_atual`, `pf`.`valor_liquido_proposta` AS `valor_liquido_proposta`, `pf`.`dt_pedido` AS `dt_pedido`, `pf`.`dt_finalizacao` AS `dt_finalizacao`, `pf`.`estado_pedido_frotas_id` AS `estado_pedido_frotas_id`, `pf`.`system_users_id` AS `system_users_id`, `pf`.`system_unit_id` AS `system_unit_id`, `pe`.`cidade_id` AS `cidade_id`, `dpf`.`id` AS `propostas_id`, `pf`.`departamento_unit_id` AS `departamento_unit_id` FROM (((`dotacao_pedido_frotas` `dpf` join `pedido_frotas` `pf` on(`pf`.`id` = `dpf`.`pedido_frotas_id` and `pf`.`deleted_at` is null)) left join `propostas` `p` on(`p`.`pedido_frotas_id` = `pf`.`id` and `p`.`estado_pedido_frotas_id` in (8,13,20,24) and `p`.`deleted_at` is null)) left join `pessoa_endereco` `pe` on(`pe`.`pessoa_id` = `p`.`pessoa_id` and `pe`.`principal` = 'T')) WHERE `dpf`.`deleted_at` is null AND `pf`.`estado_pedido_frotas_id` in (8,13,20,24) ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_email_usuarios`
--
DROP TABLE IF EXISTS `view_email_usuarios`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_email_usuarios`  AS SELECT DISTINCT `sudu`.`system_users_id` AS `system_users_id`, `susers`.`name` AS `name`, `susers`.`email` AS `email`, `su`.`id` AS `system_unit_id` FROM (((`system_user_departamento_unit` `sudu` join `departamento_unit` `du` on(`du`.`id` = `sudu`.`departamento_unit_id`)) join `system_unit` `su` on(`su`.`id` = `du`.`system_unit_id`)) join `system_users` `susers` on(`susers`.`id` = `sudu`.`system_users_id`)) ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_enviarcotacao`
--
DROP TABLE IF EXISTS `view_enviarcotacao`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_enviarcotacao`  AS SELECT `p`.`id` AS `id`, `pe`.`cidade_id` AS `cidade_id`, `sp`.`seguimento_id` AS `seguimento_id`, `p`.`nome` AS `nome`, `p`.`id` AS `pessoa_id`, `p`.`deleted_at` AS `deleted_at` FROM (((`pessoa` `p` join `pessoa_endereco` `pe` on(`pe`.`pessoa_id` = `p`.`id`)) join `pessoa_grupo` `pg` on(`pg`.`pessoa_id` = `p`.`id` and `pg`.`grupo_pessoa_id` = 4)) left join `seguimento_pessoa` `sp` on(`sp`.`pessoa_id` = `p`.`id`)) ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_enviarcotacao_seguradora`
--
DROP TABLE IF EXISTS `view_enviarcotacao_seguradora`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_enviarcotacao_seguradora`  AS SELECT `p`.`id` AS `id`, `pe`.`cidade_id` AS `cidade_id`, `sp`.`seguimento_id` AS `seguimento_id`, `p`.`nome` AS `nome`, `p`.`id` AS `pessoa_id`, `p`.`deleted_at` AS `deleted_at` FROM (((`pessoa` `p` join `pessoa_endereco` `pe` on(`pe`.`pessoa_id` = `p`.`id`)) join `pessoa_grupo` `pg` on(`pg`.`pessoa_id` = `p`.`id` and `pg`.`grupo_pessoa_id` = 6)) left join `seguimento_pessoa` `sp` on(`sp`.`pessoa_id` = `p`.`id`)) ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_indicadoresstatuspedidofrotas`
--
DROP TABLE IF EXISTS `view_indicadoresstatuspedidofrotas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_indicadoresstatuspedidofrotas`  AS SELECT DISTINCT `pf`.`entidade_id` AS `entidade_id`, `pf`.`system_unit_id` AS `system_unit_id`, `pf`.`departamento_unit_id` AS `departamento_unit_id`, `pf`.`estado_pedido_frotas_id` AS `estado_pedido_frotas_id`, `pf`.`ano` AS `ano`, `pf`.`mes` AS `mes`, count(0) AS `qtde` FROM `pedido_frotas` AS `pf` GROUP BY `pf`.`entidade_id`, `pf`.`system_unit_id`, `pf`.`departamento_unit_id`, `pf`.`ano`, `pf`.`mes`, `pf`.`estado_pedido_frotas_id` ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_itenscotacao_produto`
--
DROP TABLE IF EXISTS `view_itenscotacao_produto`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_itenscotacao_produto`  AS SELECT `itens_cotacao`.`id` AS `id`, `itens_cotacao`.`produto_id` AS `produto_id`, `itens_cotacao`.`qtde` AS `qtde`, `itens_cotacao`.`valor` AS `valor`, `itens_cotacao`.`valor_total` AS `valor_total`, `itens_cotacao`.`cotacao_id` AS `cotacao_id`, `produto`.`id` AS `id_produto`, `produto`.`nome` AS `nome` FROM (`itens_cotacao` join `produto`) WHERE `itens_cotacao`.`produto_id` = `produto`.`id` ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_listagem_novos_produtos`
--
DROP TABLE IF EXISTS `view_listagem_novos_produtos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_listagem_novos_produtos`  AS SELECT `produto`.`nome` AS `nome`, `produto`.`familia_produto_id` AS `familia_produto_id`, count(0) AS `qtde` FROM `produto` WHERE `produto`.`system_unit_id` = 85 GROUP BY `produto`.`nome`, `produto`.`familia_produto_id` ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_negociacao_timeline`
--
DROP TABLE IF EXISTS `view_negociacao_timeline`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_negociacao_timeline`  AS SELECT `negociacao_observacao`.`id` AS `chave`, `negociacao_observacao`.`negociacao_id` AS `negociacao_id`, `negociacao_observacao`.`dt_observacao` AS `dt_historico`, 'observacao' AS `tipo` FROM `negociacao_observacao`union all select `negociacao_arquivo`.`id` AS `chave`,`negociacao_arquivo`.`negociacao_id` AS `negociacao_id`,`negociacao_arquivo`.`dt_arquivo` AS `dt_historico`,'arquivo' AS `tipo` from `negociacao_arquivo` union all select `negociacao_atividade`.`id` AS `chave`,`negociacao_atividade`.`negociacao_id` AS `negociacao_id`,`negociacao_atividade`.`dt_atividade` AS `dt_historico`,'atividade' AS `tipo` from `negociacao_atividade` union all select `negociacao_item`.`id` AS `chave`,`negociacao_item`.`negociacao_id` AS `negociacao_id`,`negociacao_item`.`dt_item` AS `dt_historico`,'produto' AS `tipo` from `negociacao_item` union all select `negociacao_historico_etapa`.`id` AS `chave`,`negociacao_historico_etapa`.`negociacao_id` AS `negociacao_id`,`negociacao_historico_etapa`.`dt_etapa` AS `dt_historico`,'etapa' AS `tipo` from `negociacao_historico_etapa`  ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_pedidos_as_cliente`
--
DROP TABLE IF EXISTS `view_pedidos_as_cliente`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_pedidos_as_cliente`  AS SELECT `pedido_as_cliente`.`id` AS `id`, `pedido_as_cliente`.`pedido_frotas_id` AS `pedido_frotas_id`, `pedido_as_cliente`.`pessoa_id` AS `pessoa_id`, `pessoa`.`id` AS `id_pessoa`, `pessoa`.`nome` AS `nome`, `pessoa`.`fone` AS `fone`, `pessoa`.`email` AS `email`, `pessoa_endereco`.`id` AS `id_pessoa_endereco`, `pessoa_endereco`.`cidade_id` AS `cidade_id`, `cidade`.`id` AS `id_cidade`, `cidade`.`estado_id` AS `estado_id`, `cidade`.`nome` AS `nome_cidade`, `estado`.`id` AS `id_estado`, `estado`.`sigla` AS `sigla` FROM ((((`pedido_as_cliente` join `pessoa` on(`pedido_as_cliente`.`pessoa_id` = `pessoa`.`id`)) join `pessoa_endereco` on(`pessoa_endereco`.`pessoa_id` = `pessoa`.`id`)) join `cidade` on(`pessoa_endereco`.`cidade_id` = `cidade`.`id`)) join `estado` on(`cidade`.`estado_id` = `estado`.`id`)) ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_pedido_frotas_propostas`
--
DROP TABLE IF EXISTS `view_pedido_frotas_propostas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_pedido_frotas_propostas`  AS SELECT `pf`.`id` AS `id`, `pf`.`descricaopedido` AS `descricaopedido`, `pf`.`dt_pedido` AS `dt_pedido`, `pf`.`valor_total` AS `valor_total`, `pf`.`valor_desconto_proposta` AS `valor_desconto_proposta`, `pf`.`valor_liquido_proposta` AS `valor_liquido_proposta`, `pf`.`estado_pedido_frotas_id` AS `estado_pedido_frotas_id`, `pf`.`departamento_unit_id` AS `departamento_unit_id`, `p`.`pessoa_id` AS `estabelecimento_id`, `pe`.`cidade_id` AS `cidade_id`, `pf`.`dt_finalizacao` AS `dt_finalizacao`, `pf`.`system_unit_id` AS `system_unit_id` FROM ((`pedido_frotas` `pf` join `propostas` `p` on(`p`.`pedido_frotas_id` = `pf`.`id` and `p`.`estado_pedido_frotas_id` = `pf`.`estado_pedido_frotas_id`)) left join `pessoa_endereco` `pe` on(`pe`.`pessoa_id` = `p`.`pessoa_id` and `pe`.`principal` = 'T')) ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_pessoa`
--
DROP TABLE IF EXISTS `view_pessoa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_pessoa`  AS SELECT `pessoa`.`id` AS `id`, `pessoa`.`nome` AS `nome`, `pessoa_grupo`.`id` AS `id_pessoa_grupo`, `pessoa_grupo`.`pessoa_id` AS `pessoa_id`, `pessoa_grupo`.`grupo_pessoa_id` AS `grupo_pessoa_id` FROM (`pessoa` join `pessoa_grupo`) WHERE `pessoa_grupo`.`pessoa_id` = `pessoa`.`id` ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_pessoa_cnh`
--
DROP TABLE IF EXISTS `view_pessoa_cnh`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_pessoa_cnh`  AS SELECT `p`.`id` AS `id`, `p`.`system_unit_id` AS `system_unit_id`, `p`.`nome` AS `nome`, `p`.`numero_registro_cnh` AS `numero_registro_cnh`, `p`.`data_validade_cnh` AS `data_validade_cnh`, `su`.`name` AS `system_unit_name`, CASE WHEN `p`.`data_validade_cnh` is null THEN 'NAO_CADASTRADA' WHEN `p`.`data_validade_cnh` < curdate() THEN 'VENCIDA' WHEN `p`.`data_validade_cnh` <= curdate() + interval 30 day THEN 'AVENCER' ELSE 'EM_DIA' END AS `status_cnh`, CASE WHEN `p`.`data_validade_cnh` is null THEN 2 WHEN `p`.`data_validade_cnh` < curdate() THEN 0 WHEN `p`.`data_validade_cnh` <= curdate() + interval 30 day THEN 1 ELSE 3 END AS `ordem_status`, CASE WHEN `p`.`data_validade_cnh` is null THEN NULL ELSE to_days(`p`.`data_validade_cnh`) - to_days(curdate()) END AS `dias_para_vencer` FROM ((`pessoa` `p` join `pessoa_grupo` `pg` on(`pg`.`pessoa_id` = `p`.`id`)) left join `system_unit` `su` on(`su`.`id` = `p`.`system_unit_id`)) WHERE `pg`.`grupo_pessoa_id` = 5 ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_produtocompras`
--
DROP TABLE IF EXISTS `view_produtocompras`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_produtocompras`  AS SELECT `c`.`id` AS `id`, `c`.`pedido_id` AS `pedido_id`, `ped`.`estado_pedido_venda_id` AS `estado_pedido_venda_id`, `c`.`estado_pedido_id` AS `estado_pedido_id`, `c`.`data_cotacao` AS `data_cotacao`, `c`.`pessoa_id` AS `pessoa_id`, `ped`.`system_unit_id` AS `system_unit_id`, `ped`.`departamento_unit_id` AS `departamento_unit_id`, `ped`.`entidade_id` AS `entidade_id`, `ic`.`qtde` AS `qtde`, `ic`.`valor` AS `valor_unitario`, `ic`.`valor_total` AS `valor_total`, `prod`.`nome` AS `nomeproduto`, `p`.`nome` AS `nomeestabelecimento`, `ped`.`dt_pedido` AS `dt_pedido` FROM ((((`cotacao` `c` join `itens_cotacao` `ic` on(`ic`.`cotacao_id` = `c`.`id`)) left join `pessoa` `p` on(`p`.`id` = `c`.`pessoa_id`)) left join `produto` `prod` on(`prod`.`id` = `ic`.`produto_id`)) left join `pedido` `ped` on(`ped`.`id` = `c`.`pedido_id`)) WHERE `ped`.`estado_pedido_venda_id` in (8,13,18,20) AND `c`.`estado_pedido_id` in (8,13,18,20) ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_produtos_servicos_aprovados`
--
DROP TABLE IF EXISTS `view_produtos_servicos_aprovados`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_produtos_servicos_aprovados`  AS SELECT `p`.`id` AS `id`, `p`.`pedido_frotas_id` AS `pedido_frotas_id`, `p`.`pessoa_id` AS `pessoa_id`, `p`.`estado_pedido_frotas_id` AS `estado_pedido_frotas_id`, `p`.`veiculos_id` AS `veiculos_id`, `p`.`system_unit_id` AS `system_unit_id`, `p`.`departamento_unit_id` AS `departamento_unit_id`, `p`.`data_cotacao` AS `data_cotacao`, `pf`.`dt_pedido` AS `dt_pedido`, `ip`.`qtde` AS `qtde`, `ip`.`valor` AS `valor`, `ip`.`perc_desconto` AS `perc_desconto`, `ip`.`valor_total` AS `valor_total`, `prod`.`nome` AS `nomeproduto`, `ip`.`produto_id` AS `produto_id`, `pes`.`nome` AS `nomeestabelecimento`, `e`.`taxacontrato` AS `taxacontrato` FROM ((((((`propostas` `p` join `itens_propostas` `ip` on(`ip`.`propostas_id` = `p`.`id`)) join `produto` `prod` on(`prod`.`id` = `ip`.`produto_id`)) join `pessoa` `pes` on(`pes`.`id` = `p`.`pessoa_id`)) join `pedido_frotas` `pf` on(`pf`.`id` = `p`.`pedido_frotas_id`)) join `system_unit` `su` on(`su`.`id` = `pf`.`system_unit_id`)) join `entidade` `e` on(`e`.`id` = `su`.`entidade_id`)) WHERE `p`.`estado_pedido_frotas_id` in (8,13,18,20,24) ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_propostaaprovadaporrede`
--
DROP TABLE IF EXISTS `view_propostaaprovadaporrede`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_propostaaprovadaporrede`  AS SELECT `p`.`id` AS `id`, `p`.`pessoa_id` AS `pessoa_id`, `pes`.`nome` AS `nome`, `p`.`pedido_frotas_id` AS `pedido_frotas_id`, `pf`.`descricaopedido` AS `descricaopedido`, `p`.`estado_pedido_frotas_id` AS `estado_pedido_frotas_id`, `p`.`motorista_entrada_id` AS `motorista_entrada_id`, `p`.`veiculos_id` AS `veiculos_id`, `p`.`data_cotacao` AS `data_cotacao`, `p`.`obs` AS `obs`, `p`.`valor_total` AS `valor_total`, `p`.`valor_desconto` AS `valor_desconto`, `p`.`valor_liquido` AS `valor_liquido`, `p`.`system_unit_id` AS `system_unit_id`, `p`.`departamento_unit_id` AS `departamento_unit_id`, `p`.`system_users_id` AS `system_users_id`, `p`.`data_entrada_veiculo` AS `data_entrada_veiculo`, `p`.`data_retirada_veiculo` AS `data_retirada_veiculo`, `p`.`data_previsao_entrega` AS `data_previsao_entrega`, `p`.`motorista_retirada_id` AS `motorista_retirada_id`, `p`.`km` AS `km`, `p`.`created_at` AS `created_at`, `p`.`deleted_at` AS `deleted_at`, `p`.`updated_at` AS `updated_at`, `p`.`cidade_id` AS `cidade_id`, `p`.`total_produtos_sem_desconto` AS `total_produtos_sem_desconto`, `p`.`total_servicos_sem_desconto` AS `total_servicos_sem_desconto`, `p`.`total_geral_sem_desconto` AS `total_geral_sem_desconto`, `p`.`total_produtos_com_desconto` AS `total_produtos_com_desconto`, `p`.`total_servicos_com_desconto` AS `total_servicos_com_desconto`, `p`.`desconto_contratual` AS `desconto_contratual`, `p`.`total_geral_com_desconto` AS `total_geral_com_desconto`, `p`.`responsavel_tecnico` AS `responsavel_tecnico`, `p`.`datahora_inicioservico` AS `datahora_inicioservico`, `p`.`datahora_fimservico` AS `datahora_fimservico`, `pf`.`dt_pedido` AS `dt_pedido`, `ph`.`data_historico` AS `data_aprovacao`, `su`.`name` AS `nomeaprovador`, `ph1`.`data_historico` AS `data_autorizacao_pagamento`, `su1`.`name` AS `nomeaprovadorpagamento` FROM ((((((((`propostas` `p` join `pedido_frotas` `pf` on(`pf`.`id` = `p`.`pedido_frotas_id`)) left join `propostas_historico` `ph` on(`ph`.`propostas_id` = `p`.`id` and `ph`.`estado_pedido_frotas_id` = 8)) left join `aprovador_frotas` `af` on(`af`.`id` = `ph`.`aprovador_frotas_id`)) left join `system_users` `su` on(`su`.`id` = `af`.`system_users_id`)) left join `propostas_historico` `ph1` on(`ph1`.`propostas_id` = `p`.`id` and `ph1`.`estado_pedido_frotas_id` = 18)) left join `aprovador_frotas` `af1` on(`af1`.`id` = `ph1`.`aprovador_frotas_id`)) left join `system_users` `su1` on(`su1`.`id` = `af1`.`system_users_id`)) left join `pessoa` `pes` on(`pes`.`id` = `p`.`pessoa_id`)) WHERE `p`.`estado_pedido_frotas_id` in (8,13,18,20,24) ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_propostas`
--
DROP TABLE IF EXISTS `view_propostas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_propostas`  AS SELECT `propostas`.`id` AS `id`, `propostas`.`pedido_frotas_id` AS `pedido_frotas_id`, `propostas`.`pessoa_id` AS `estabelecimento_id`, `propostas`.`estado_pedido_frotas_id` AS `estado_pedido_frotas_id`, `propostas`.`veiculos_id` AS `veiculos_id`, `propostas`.`placa` AS `placa`, `propostas`.`modelo` AS `modelo`, `propostas`.`data_cotacao` AS `data_cotacao`, `propostas`.`obs` AS `obs`, `propostas`.`valor_total` AS `valor_total`, `propostas`.`valor_desconto` AS `valor_desconto`, `propostas`.`valor_liquido` AS `valor_liquido`, `propostas`.`system_unit_id` AS `system_unit_id`, `propostas`.`departamento_unit_id` AS `departamento_unit_id`, `propostas`.`system_users_id` AS `system_users_id`, `propostas`.`data_entrada_veiculo` AS `data_entrada_veiculo`, `propostas`.`horimetro_entrada_aeronave` AS `horimetro_entrada_aeronave`, `propostas`.`ciclos_entrada_aeronave` AS `ciclos_entrada_aeronave`, `propostas`.`data_retirada_veiculo` AS `data_retirada_veiculo`, `propostas`.`horimetro_retirada_aeronave` AS `horimetro_retirada_aeronave`, `propostas`.`ciclos_retirada_aeronave` AS `ciclos_retirada_aeronave`, `propostas`.`data_previsao_entrega` AS `data_previsao_entrega`, `propostas`.`km` AS `km`, `propostas`.`ciclos` AS `ciclos`, `propostas`.`created_at` AS `created_at`, `propostas`.`updated_at` AS `updated_at`, `propostas`.`deleted_at` AS `deleted_at`, `propostas`.`responsavel_tecnico` AS `responsavel_tecnico`, `propostas`.`datahora_inicioservico` AS `datahora_inicioservico`, `propostas`.`horimetro_inicioservico` AS `horimetro_inicioservico`, `propostas`.`ciclos_inicioservico` AS `ciclos_inicioservico`, `propostas`.`datahora_fimservico` AS `datahora_fimservico`, `propostas`.`horimetro_fimservico` AS `horimetro_fimservico`, `propostas`.`ciclos_fimservico` AS `ciclos_fimservico`, `propostas`.`total_produtos_sem_desconto` AS `total_produtos_sem_desconto`, `propostas`.`total_servicos_sem_desconto` AS `total_servicos_sem_desconto`, `propostas`.`total_geral_sem_desconto` AS `total_geral_sem_desconto`, `propostas`.`total_produtos_com_desconto` AS `total_produtos_com_desconto`, `propostas`.`total_servicos_com_desconto` AS `total_servicos_com_desconto`, `propostas`.`desconto_contratual` AS `desconto_contratual`, `propostas`.`motorista_entrada_id` AS `motorista_entrada_id`, `propostas`.`total_geral_com_desconto` AS `total_geral_com_desconto`, `propostas`.`entidade_id` AS `entidade_id`, `propostas`.`cidade_id` AS `cidade_id`, `propostas`.`motorista_retirada_id` AS `motorista_retirada_id`, `propostas`.`data_limite_resposta` AS `data_limite_resposta`, `propostas`.`estado_pedido_frotas1_id` AS `estado_pedido_frotas1_id` FROM `propostas` ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_redescredenciadas`
--
DROP TABLE IF EXISTS `view_redescredenciadas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_redescredenciadas`  AS SELECT `p`.`id` AS `id`, `p`.`nome` AS `nome`, `pe`.`rua` AS `rua`, `pe`.`cidade_id` AS `cidade_id`, `c`.`nome` AS `nomecidade`, `e`.`sigla` AS `sigla`, `p`.`email` AS `email`, `p`.`horariofuncionamento` AS `horariofuncionamento`, '' AS `responsavel`, '' AS `proprietario`, `p`.`data_desativacao` AS `data_desativacao`, `p`.`fone` AS `fone`, 0 AS `NFProduto`, 0 AS `NFServico`, 0 AS `QTDEOS`, 0 AS `QTDEOSAndamento`, 0 AS `MediaAvaliacao` FROM (((`pessoa` `p` join `pessoa_endereco` `pe` on(`pe`.`pessoa_id` = `p`.`id`)) left join `cidade` `c` on(`c`.`id` = `pe`.`cidade_id`)) left join `estado` `e` on(`e`.`id` = `c`.`estado_id`)) ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_redesdisponiveis`
--
DROP TABLE IF EXISTS `view_redesdisponiveis`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_redesdisponiveis`  AS SELECT `pessoa`.`id` AS `id`, `pessoa`.`nome` AS `nome`, `pessoa`.`documento` AS `documento`, `pessoa`.`fone` AS `fone`, `pessoa`.`email` AS `email`, `pessoa`.`selo` AS `selo`, `pessoa_endereco`.`cidade_id` AS `cidade_id`, `cidade`.`estado_id` AS `estado_id`, `pessoa`.`ativo` AS `ativo` FROM (((`pessoa` join `pessoa_endereco` on(`pessoa_endereco`.`pessoa_id` = `pessoa`.`id` and `pessoa_endereco`.`principal` = 'T')) join `cidade` on(`pessoa_endereco`.`cidade_id` = `cidade`.`id`)) join `estado` on(`cidade`.`estado_id` = `estado`.`id`)) WHERE `pessoa`.`deleted_at` is null AND !exists(select 1 from `pessoa_grupo` `pg` where `pg`.`pessoa_id` = `pessoa`.`id` AND `pg`.`grupo_pessoa_id` = 5 limit 1) AND `pessoa`.`ativo` = 'T' ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_relatoriomanutencao`
--
DROP TABLE IF EXISTS `view_relatoriomanutencao`;

CREATE ALGORITHM=UNDEFINED DEFINER=`gestaonp3benefic`@`localhost` SQL SECURITY DEFINER VIEW `view_relatoriomanutencao`  AS SELECT `p`.`id` AS `propostas_id`, `pf`.`id` AS `pedido_frotas_id`, `pf`.`dt_pedido` AS `dt_pedido`, `pf`.`estado_pedido_frotas_id` AS `estado_pedido_id`, `pf`.`veiculos_id` AS `veiculos_id`, `pf`.`km` AS `km`, `pf`.`valor_total` AS `valor_total_pedido`, `pf`.`valor_total_proposta` AS `valor_total_proposta`, `pf`.`valor_desconto_proposta` AS `valor_desconto_proposta`, `pf`.`valor_liquido_proposta` AS `valor_liquido_proposta`, `pf`.`system_unit_id` AS `system_user_unit_id`, `pf`.`system_users_id` AS `system_users_id`, `pf`.`departamento_unit_id` AS `departamento_unit_id`, `pf`.`entidade_id` AS `entidade_id`, `p`.`pessoa_id` AS `pessoa_id`, `p`.`estado_pedido_frotas_id` AS `estado_proposta_id`, `p`.`motorista_entrada_id` AS `motorista_entrada_id`, `p`.`motorista_retirada_id` AS `motorista_retirada_id`, `p`.`valor_total` AS `valor_totalp`, `p`.`valor_desconto` AS `valor_descontop`, `p`.`valor_liquido` AS `valor_liquidop`, `p`.`data_entrada_veiculo` AS `data_entrada_veiculo`, `p`.`data_retirada_veiculo` AS `data_retirada_veiculo`, `p`.`data_previsao_entrega` AS `data_previsao_entrega`, `p`.`datahora_inicioservico` AS `datahora_inicioservico`, `p`.`datahora_fimservico` AS `datahora_fimservico`, sum(case when `ip`.`tipo` = 2 then `ip`.`qtde` else 0 end) AS `qtd_servico`, sum(case when `ip`.`tipo` = 2 then `ip`.`valor` else 0 end) AS `valor_servico`, sum(case when `ip`.`tipo` = 2 then `ip`.`perc_desconto` else NULL end) AS `perc_desc_servico`, sum(case when `ip`.`tipo` = 2 then `ip`.`valor_total` else 0 end) AS `valor_total_servico`, sum(case when `ip`.`tipo` = 1 then `ip`.`qtde` else 0 end) AS `qtd_produto`, sum(case when `ip`.`tipo` = 1 then `ip`.`valor` else 0 end) AS `valor_produto`, sum(case when `ip`.`tipo` = 1 then `ip`.`perc_desconto` else NULL end) AS `perc_desc_produto`, sum(case when `ip`.`tipo` = 1 then `ip`.`valor_total` else 0 end) AS `valor_total_produto`, `su`.`name` AS `unidade`, `v`.`placa` AS `placa`, `m`.`id` AS `marca_id`, `m`.`descricao` AS `marca`, `mo`.`id` AS `modelo_id`, `mo`.`descricao` AS `modelo`, `v`.`anof` AS `anof`, `v`.`anom` AS `anom`, `pes`.`nome` AS `nomepessoa`, `pend`.`cidade_id` AS `cidade_id`, `cid`.`nome` AS `cidade`, `est`.`id` AS `estado_id`, `est`.`sigla` AS `estado`, `pes`.`documento` AS `cnpj`, `pf`.`descricaopedido` AS `descricaopedido`, `pf`.`tipo_manutencao_id` AS `tipo_manutencao_id`, `tm`.`descricao` AS `tipomanutencao`, (select `suser`.`name` from ((`pedido_frotas_historico` `pfh` left join `aprovador_frotas` `af` on(`af`.`id` = `pfh`.`aprovador_frotas_id`)) left join `system_users` `suser` on(`suser`.`id` = `af`.`system_users_id`)) where `pfh`.`pedido_frotas_id` = `pf`.`id` order by `pfh`.`id` desc limit 1) AS `nomeaprovador`, `pf`.`system_unit_id` AS `system_unit_id` FROM (((((((((((`propostas` `p` join `pedido_frotas` `pf` on(`pf`.`id` = `p`.`pedido_frotas_id`)) left join (select `itens_propostas`.`propostas_id` AS `propostas_id`,`itens_propostas`.`tipo` AS `tipo`,sum(`itens_propostas`.`qtde`) AS `qtde`,sum(`itens_propostas`.`valor`) AS `valor`,sum(`itens_propostas`.`valor_total`) AS `valor_total`,sum(`itens_propostas`.`perc_desconto`) AS `perc_desconto` from `itens_propostas` group by `itens_propostas`.`propostas_id`,`itens_propostas`.`tipo`) `ip` on(`ip`.`propostas_id` = `p`.`id`)) left join `system_unit` `su` on(`su`.`id` = `pf`.`system_unit_id`)) left join `veiculos` `v` on(`v`.`id` = `pf`.`veiculos_id`)) left join `marca` `m` on(`m`.`id` = `v`.`marca_id`)) left join `modelo` `mo` on(`mo`.`id` = `v`.`modelo_id`)) left join `pessoa` `pes` on(`pes`.`id` = `p`.`pessoa_id`)) left join `pessoa_endereco` `pend` on(`pend`.`pessoa_id` = `pes`.`id` and `pend`.`principal` = 'T')) left join `cidade` `cid` on(`cid`.`id` = `pend`.`cidade_id`)) left join `estado` `est` on(`est`.`id` = `cid`.`estado_id`)) left join `tipo_manutencao` `tm` on(`tm`.`id` = `pf`.`tipo_manutencao_id`)) WHERE `pf`.`estado_pedido_frotas_id` in (8,13,18,20) AND `p`.`estado_pedido_frotas_id` in (8,13,18,20) GROUP BY `p`.`id`, `pf`.`id`, `pf`.`dt_pedido`, `pf`.`estado_pedido_frotas_id`, `pf`.`veiculos_id`, `pf`.`km`, `pf`.`valor_total`, `pf`.`valor_total_proposta`, `pf`.`valor_desconto_proposta`, `pf`.`valor_liquido_proposta`, `pf`.`system_unit_id`, `pf`.`system_users_id`, `pf`.`departamento_unit_id`, `pf`.`entidade_id`, `p`.`pessoa_id`, `p`.`estado_pedido_frotas_id`, `p`.`motorista_entrada_id`, `p`.`motorista_retirada_id`, `p`.`valor_total`, `p`.`valor_desconto`, `p`.`valor_liquido`, `p`.`data_entrada_veiculo`, `p`.`data_retirada_veiculo`, `p`.`data_previsao_entrega`, `p`.`datahora_inicioservico`, `p`.`datahora_fimservico`, `su`.`name`, `v`.`placa`, `m`.`id`, `m`.`descricao`, `mo`.`id`, `mo`.`descricao`, `v`.`anof`, `v`.`anom`, `pes`.`nome`, `pend`.`cidade_id`, `cid`.`nome`, `est`.`id`, `est`.`sigla`, `pes`.`documento`, `pf`.`descricaopedido`, `pf`.`tipo_manutencao_id`, `tm`.`descricao` ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_relatoriopecasveiculos`
--
DROP TABLE IF EXISTS `view_relatoriopecasveiculos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_relatoriopecasveiculos`  AS SELECT DISTINCT `ip`.`propostas_id` AS `propostas_id`, `p`.`pedido_frotas_id` AS `pedido_frotas_id`, `pf`.`estado_pedido_frotas_id` AS `estado_pedido_frotas_id`, `pf`.`dt_pedido` AS `dt_pedido`, `pf`.`dt_finalizacao` AS `dt_finalizacao`, `pf`.`system_unit_id` AS `system_unit_id`, `pf`.`departamento_unit_id` AS `departamento_unit_id`, (select `ph`.`data_historico` from `propostas_historico` `ph` where `ph`.`propostas_id` = `p`.`id` and `ph`.`estado_pedido_frotas_id` = 18 limit 1) AS `data_historico`, (select `su`.`name` from (`propostas_historico` `ph` left join `system_users` `su` on(`ph`.`aprovador_frotas_id` = `su`.`id`)) where `ph`.`propostas_id` = `p`.`id` and `ph`.`estado_pedido_frotas_id` = 18 limit 1) AS `nome_usuario`, `p`.`pessoa_id` AS `pessoa_id`, `pes`.`nome` AS `nome_estabelecimento`, `p`.`veiculos_id` AS `veiculos_id`, `v`.`placa` AS `placa`, `m`.`descricao` AS `marca`, `mm`.`descricao` AS `modelo`, `pf`.`km` AS `km`, `p`.`desconto_contratual` AS `desconto_contratual`, `ip`.`tipo` AS `tipo`, `prod`.`nome` AS `nome_produto_servico`, CASE WHEN `ip`.`tipo` = 1 THEN `ip`.`valor` ELSE NULL END AS `valor_unitario_produto`, CASE WHEN `ip`.`tipo` = 2 THEN `ip`.`valor` ELSE NULL END AS `valor_unitario_servico`, CASE WHEN `ip`.`tipo` = 1 THEN `ip`.`qtde` ELSE NULL END AS `qtde_produto`, CASE WHEN `ip`.`tipo` = 2 THEN `ip`.`qtde` ELSE NULL END AS `qtde_servico`, CASE WHEN `ip`.`tipo` = 1 THEN `ip`.`perc_desconto` ELSE NULL END AS `perc_desconto_produto`, CASE WHEN `ip`.`tipo` = 2 THEN `ip`.`perc_desconto` ELSE NULL END AS `perc_desconto_servico`, CASE WHEN `ip`.`tipo` = 1 THEN `ip`.`valor_total` ELSE NULL END AS `valor_total_produto`, CASE WHEN `ip`.`tipo` = 2 THEN `ip`.`valor_total` ELSE NULL END AS `valor_total_servico`, CASE WHEN `ip`.`tipo` = 1 THEN `ip`.`qtdekmgarantia` ELSE NULL END AS `km_garantia_produto`, CASE WHEN `ip`.`tipo` = 2 THEN `ip`.`qtdekmgarantia` ELSE NULL END AS `km_garantia_servico`, CASE WHEN `ip`.`tipo` = 1 THEN `ip`.`diasdegarantia` ELSE NULL END AS `dias_garantia_produto`, CASE WHEN `ip`.`tipo` = 2 THEN `ip`.`diasdegarantia` ELSE NULL END AS `dias_garantia_servico` FROM (((((((`itens_propostas` `ip` left join `produto` `prod` on(`prod`.`id` = `ip`.`produto_id`)) left join `propostas` `p` on(`p`.`id` = `ip`.`propostas_id`)) left join `pedido_frotas` `pf` on(`pf`.`id` = `p`.`pedido_frotas_id`)) left join `pessoa` `pes` on(`pes`.`id` = `p`.`pessoa_id`)) left join `veiculos` `v` on(`v`.`id` = `pf`.`veiculos_id`)) left join `marca` `m` on(`m`.`id` = `v`.`marca_id`)) left join `modelo` `mm` on(`mm`.`id` = `v`.`modelo_id`)) WHERE `pf`.`estado_pedido_frotas_id` in (8,13,18,20) AND `p`.`estado_pedido_frotas_id` in (8,13,18,20) ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_relatorioporrede_sintetico`
--
DROP TABLE IF EXISTS `view_relatorioporrede_sintetico`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_relatorioporrede_sintetico`  AS SELECT `p`.`id` AS `proposta_id`, `p`.`pedido_frotas_id` AS `pedido_id`, `p`.`pessoa_id` AS `pessoa_id`, `p`.`system_unit_id` AS `system_unit_id`, `p`.`departamento_unit_id` AS `departamento_unit_id`, coalesce(`ph`.`qtd_recebida`,0) AS `qtd_proposta_recebida`, `ph`.`dt_abertura` AS `dt_abertura`, coalesce(`ph`.`qtd_finalizado`,0) AS `qtd_proposta_finalizado`, `ph`.`dt_finalizado` AS `dt_finalizado`, `ph`.`dt_aprovado` AS `dt_aprovado`, coalesce(`ph`.`qtd_entregue`,0) AS `qtd_proposta_entregue`, coalesce(`ph`.`qtd_aguardando`,0) AS `qtd_proposta_aguardando`, coalesce(`ips`.`vl_produto`,0) AS `vl_produto`, coalesce(`ips`.`vl_servico`,0) AS `vl_servico`, coalesce(`ips`.`vl_total`,0) AS `vl_total` FROM ((`propostas` `p` left join (select `ph`.`propostas_id` AS `propostas_id`,sum(case when `ph`.`estado_pedido_frotas_id` = 11 then 1 else 0 end) AS `qtd_recebida`,max(case when `ph`.`estado_pedido_frotas_id` = 11 then `ph`.`data_historico` end) AS `dt_abertura`,sum(case when `ph`.`estado_pedido_frotas_id` = 8 then 1 else 0 end) AS `qtd_finalizado`,max(case when `ph`.`estado_pedido_frotas_id` = 8 then `ph`.`data_historico` end) AS `dt_finalizado`,max(case when `ph`.`estado_pedido_frotas_id` = 13 then `ph`.`data_historico` end) AS `dt_aprovado`,sum(case when `ph`.`estado_pedido_frotas_id` = 20 then 1 else 0 end) AS `qtd_entregue`,sum(case when `ph`.`estado_pedido_frotas_id` = 12 then 1 else 0 end) AS `qtd_aguardando` from `propostas_historico` `ph` group by `ph`.`propostas_id`) `ph` on(`ph`.`propostas_id` = `p`.`id`)) left join (select `ip`.`propostas_id` AS `propostas_id`,sum(case when `ip`.`tipo` = 1 then `ip`.`valor_total` else 0 end) AS `vl_produto`,sum(case when `ip`.`tipo` = 2 then `ip`.`valor_total` else 0 end) AS `vl_servico`,sum(`ip`.`valor_total`) AS `vl_total` from `itens_propostas` `ip` group by `ip`.`propostas_id`) `ips` on(`ips`.`propostas_id` = `p`.`id`)) ;

-- --------------------------------------------------------

--
-- Estrutura para view `view_saldoempenhocompras`
--
DROP TABLE IF EXISTS `view_saldoempenhocompras`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_saldoempenhocompras`  AS SELECT DISTINCT `pf`.`saldo_departamento_id` AS `saldo_departamento_id`, `sd`.`numero_documento_empenho` AS `numero_documento_empenho`, `pf`.`entidade_id` AS `entidade_id`, `pf`.`system_unit_id` AS `system_unit_id`, `pf`.`departamento_unit_id` AS `departamento_unit_id`, `sd`.`datatransacao` AS `datatransacao`, `pf`.`mes` AS `mes`, `pf`.`ano` AS `ano`, `pf`.`estado_pedido_venda_id` AS `estado_pedido_venda_id`, `sd`.`documento_empenho` AS `documento_empenho`, coalesce(`itens`.`total_produtos`,0) AS `total_produtos`, coalesce(`sd`.`saldo_produto`,0) AS `saldo_empenho`, coalesce(`sd`.`saldo_produto`,0) - coalesce(`itens`.`total_produtos`,0) AS `saldoatual` FROM ((`pedido` `pf` left join (select `itens_pedido`.`pedido_venda_id` AS `pedido_venda_id`,sum(`itens_pedido`.`valor_total`) AS `total_produtos` from `itens_pedido` group by `itens_pedido`.`pedido_venda_id`) `itens` on(`itens`.`pedido_venda_id` = `pf`.`id`)) left join `saldo_departamento` `sd` on(`sd`.`id` = `pf`.`saldo_departamento_id`)) WHERE `pf`.`saldo_departamento_id` is not null ;

-- --------------------------------------------------------

--
-- Estrutura para view `vw_propostas_duplicadas_grupo`
--
DROP TABLE IF EXISTS `vw_propostas_duplicadas_grupo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_propostas_duplicadas_grupo`  AS SELECT `propostas`.`pedido_frotas_id` AS `pedido_frotas_id`, `propostas`.`pessoa_id` AS `pessoa_id`, count(0) AS `qtd` FROM `propostas` GROUP BY `propostas`.`pedido_frotas_id`, `propostas`.`pessoa_id` HAVING count(0) > 1 ;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `alerta_program`
--
ALTER TABLE `alerta_program`
  ADD CONSTRAINT `fk_alerta_program_1` FOREIGN KEY (`system_program_id`) REFERENCES `system_program` (`id`),
  ADD CONSTRAINT `fk_alerta_program_2` FOREIGN KEY (`system_unit_id`) REFERENCES `system_unit` (`id`),
  ADD CONSTRAINT `fk_alerta_program_3` FOREIGN KEY (`entidade_id`) REFERENCES `entidade` (`id`),
  ADD CONSTRAINT `fk_alerta_program_4` FOREIGN KEY (`system_users_id`) REFERENCES `system_users` (`id`);

--
-- Restrições para tabelas `anexos_seguros`
--
ALTER TABLE `anexos_seguros`
  ADD CONSTRAINT `fk_anexos_seguros_1` FOREIGN KEY (`seguros_id`) REFERENCES `seguros` (`id`);

--
-- Restrições para tabelas `aprovador_frotas`
--
ALTER TABLE `aprovador_frotas`
  ADD CONSTRAINT `fk_aprovador_frotas_1` FOREIGN KEY (`system_users_id`) REFERENCES `system_users` (`id`);

--
-- Restrições para tabelas `autorizacao_pedido`
--
ALTER TABLE `autorizacao_pedido`
  ADD CONSTRAINT `fk_autorizar_pedido_3` FOREIGN KEY (`veiculos_id`) REFERENCES `veiculos` (`id`),
  ADD CONSTRAINT `fk_autorizar_pedido_5` FOREIGN KEY (`system_users_id`) REFERENCES `system_users` (`id`);

--
-- Restrições para tabelas `cidade_pedido_frotas`
--
ALTER TABLE `cidade_pedido_frotas`
  ADD CONSTRAINT `fk_cidade_pedido_frotas_1` FOREIGN KEY (`cidade_id`) REFERENCES `cidade` (`id`),
  ADD CONSTRAINT `fk_cidade_pedido_frotas_2` FOREIGN KEY (`pedido_frotas_id`) REFERENCES `pedido_frotas` (`id`);

--
-- Restrições para tabelas `comentario_proposta`
--
ALTER TABLE `comentario_proposta`
  ADD CONSTRAINT `fk_comentario_proposta_2` FOREIGN KEY (`system_users_id`) REFERENCES `system_users` (`id`);

--
-- Restrições para tabelas `condutor`
--
ALTER TABLE `condutor`
  ADD CONSTRAINT `fk_condutor_1` FOREIGN KEY (`system_unit_id`) REFERENCES `system_unit` (`id`),
  ADD CONSTRAINT `fk_condutor_2` FOREIGN KEY (`departamento_unit_id`) REFERENCES `departamento_unit` (`id`),
  ADD CONSTRAINT `fk_condutor_3` FOREIGN KEY (`system_users_id`) REFERENCES `system_users` (`id`);

--
-- Restrições para tabelas `dispositivos`
--
ALTER TABLE `dispositivos`
  ADD CONSTRAINT `fk_dispositivos_1` FOREIGN KEY (`tipo_finalidade_id`) REFERENCES `tipo_finalidade` (`id`);

--
-- Restrições para tabelas `dispositivos_solicitados`
--
ALTER TABLE `dispositivos_solicitados`
  ADD CONSTRAINT `fk_dispositivos_solicitados_1` FOREIGN KEY (`veiculos_id`) REFERENCES `veiculos` (`id`),
  ADD CONSTRAINT `fk_dispositivos_solicitados_2` FOREIGN KEY (`status_dispositivos_id`) REFERENCES `status_dispositivos` (`id`),
  ADD CONSTRAINT `fk_dispositivos_solicitados_3` FOREIGN KEY (`system_unit_id`) REFERENCES `system_unit` (`id`),
  ADD CONSTRAINT `fk_dispositivos_solicitados_4` FOREIGN KEY (`departamento_unit_id`) REFERENCES `departamento_unit` (`id`),
  ADD CONSTRAINT `fk_dispositivos_solicitados_5` FOREIGN KEY (`system_users_id`) REFERENCES `system_users` (`id`),
  ADD CONSTRAINT `fk_dispositivos_solicitados_6` FOREIGN KEY (`dispositivos_id`) REFERENCES `dispositivos` (`id`);

--
-- Restrições para tabelas `documentos_pedido_frotas`
--
ALTER TABLE `documentos_pedido_frotas`
  ADD CONSTRAINT `fk_documentos_pedido_frotas_1` FOREIGN KEY (`pedido_frotas_id`) REFERENCES `pedido_frotas` (`id`);

--
-- Restrições para tabelas `documentos_pessoa`
--
ALTER TABLE `documentos_pessoa`
  ADD CONSTRAINT `fk_documentos_pessoa_1` FOREIGN KEY (`tipo_documento_id`) REFERENCES `tipo_documento` (`id`),
  ADD CONSTRAINT `fk_documentos_pessoa_2` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoa` (`id`);

--
-- Restrições para tabelas `documentos_propostas`
--
ALTER TABLE `documentos_propostas`
  ADD CONSTRAINT `fk_documentos_propostas_1` FOREIGN KEY (`propostas_id`) REFERENCES `propostas` (`id`),
  ADD CONSTRAINT `fk_documentos_propostas_2` FOREIGN KEY (`tipo_documentos_propostas_id`) REFERENCES `tipo_documentos_propostas` (`id`);

--
-- Restrições para tabelas `documento_autorizacao_pedido`
--
ALTER TABLE `documento_autorizacao_pedido`
  ADD CONSTRAINT `fk_documento_autorizacao_pedido_1` FOREIGN KEY (`autorizacao_pedido_id`) REFERENCES `autorizacao_pedido` (`id`);

--
-- Restrições para tabelas `dotacao_pedido_frotas`
--
ALTER TABLE `dotacao_pedido_frotas`
  ADD CONSTRAINT `fk_dotacao_pedido_frotas_1` FOREIGN KEY (`pedido_frotas_id`) REFERENCES `pedido_frotas` (`id`),
  ADD CONSTRAINT `fk_dotacao_pedido_frotas_2` FOREIGN KEY (`saldo_departamento_id`) REFERENCES `saldo_departamento` (`id`),
  ADD CONSTRAINT `fk_dotacao_pedido_frotas_3` FOREIGN KEY (`propostas_id`) REFERENCES `propostas` (`id`);

--
-- Restrições para tabelas `estado_pedido_frotas_aprovador`
--
ALTER TABLE `estado_pedido_frotas_aprovador`
  ADD CONSTRAINT `fk_estado_pedido_frotas_aprovador_1` FOREIGN KEY (`aprovador_frotas_id`) REFERENCES `aprovador_frotas` (`id`),
  ADD CONSTRAINT `fk_estado_pedido_frotas_aprovador_2` FOREIGN KEY (`estado_pedido_frotas_id`) REFERENCES `estado_pedido_frotas` (`id`);

--
-- Restrições para tabelas `fotos_veiculos`
--
ALTER TABLE `fotos_veiculos`
  ADD CONSTRAINT `fk_fotos_veiculos_1` FOREIGN KEY (`veiculos_id`) REFERENCES `veiculos` (`id`);

--
-- Restrições para tabelas `itens_pedido_frotas`
--
ALTER TABLE `itens_pedido_frotas`
  ADD CONSTRAINT `fk_itens_pedido_frotas_2` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`);

--
-- Restrições para tabelas `itens_propostas`
--
ALTER TABLE `itens_propostas`
  ADD CONSTRAINT `fk_itens_propostas_1` FOREIGN KEY (`propostas_id`) REFERENCES `propostas` (`id`),
  ADD CONSTRAINT `fk_itens_propostas_3` FOREIGN KEY (`estado_pedido_frotas_id`) REFERENCES `estado_pedido_frotas` (`id`),
  ADD CONSTRAINT `fk_itens_propostas_4` FOREIGN KEY (`tipo_pecas_id`) REFERENCES `tipo_pecas` (`id`);

--
-- Restrições para tabelas `manutencao_garantia`
--
ALTER TABLE `manutencao_garantia`
  ADD CONSTRAINT `fk_manutencao_1` FOREIGN KEY (`itens_propostas_id`) REFERENCES `itens_propostas` (`id`),
  ADD CONSTRAINT `fk_manutencao_2` FOREIGN KEY (`veiculos_id`) REFERENCES `veiculos` (`id`),
  ADD CONSTRAINT `fk_manutencao_garantia_3` FOREIGN KEY (`pedido_frotas_id`) REFERENCES `pedido_frotas` (`id`),
  ADD CONSTRAINT `fk_manutencao_garantia_4` FOREIGN KEY (`propostas_id`) REFERENCES `propostas` (`id`);

--
-- Restrições para tabelas `matriz_estado_pedido_frotas`
--
ALTER TABLE `matriz_estado_pedido_frotas`
  ADD CONSTRAINT `fk_matriz_estado_pedido_frotas_1` FOREIGN KEY (`estado_pedido_frotas_origem_id`) REFERENCES `estado_pedido_frotas` (`id`),
  ADD CONSTRAINT `fk_matriz_estado_pedido_frotas_2` FOREIGN KEY (`estado_pedido_frotas_destino_id`) REFERENCES `estado_pedido_frotas` (`id`);

--
-- Restrições para tabelas `modelo`
--
ALTER TABLE `modelo`
  ADD CONSTRAINT `fk_modelo_10` FOREIGN KEY (`especie_id`) REFERENCES `especie` (`id`),
  ADD CONSTRAINT `fk_modelo_20` FOREIGN KEY (`propriedade_id`) REFERENCES `propriedade` (`id`),
  ADD CONSTRAINT `fk_modelo_30` FOREIGN KEY (`familia_id`) REFERENCES `familia` (`id`),
  ADD CONSTRAINT `fk_modelo_40` FOREIGN KEY (`tipo_veiculo_id`) REFERENCES `tipo_veiculo` (`id`),
  ADD CONSTRAINT `fk_modelo_50` FOREIGN KEY (`tipo_combustivel_id`) REFERENCES `tipo_combustivel` (`id`);

--
-- Restrições para tabelas `modelo_ano`
--
ALTER TABLE `modelo_ano`
  ADD CONSTRAINT `fk_modelo_ano_1` FOREIGN KEY (`modelo_id`) REFERENCES `modelo` (`id`);

--
-- Restrições para tabelas `movimento_dispositivos`
--
ALTER TABLE `movimento_dispositivos`
  ADD CONSTRAINT `fk_movimento_dispositivos_1` FOREIGN KEY (`dispositivos_solicitados_id`) REFERENCES `dispositivos_solicitados` (`id`),
  ADD CONSTRAINT `fk_movimento_dispositivos_2` FOREIGN KEY (`veiculos_id`) REFERENCES `veiculos` (`id`),
  ADD CONSTRAINT `fk_movimento_dispositivos_3` FOREIGN KEY (`estabelecimento_id`) REFERENCES `pessoa` (`id`),
  ADD CONSTRAINT `fk_movimento_dispositivos_4` FOREIGN KEY (`condutor_id`) REFERENCES `pessoa` (`id`);

--
-- Restrições para tabelas `multas`
--
ALTER TABLE `multas`
  ADD CONSTRAINT `fk_multas_1` FOREIGN KEY (`veiculos_id`) REFERENCES `veiculos` (`id`),
  ADD CONSTRAINT `fk_multas_2` FOREIGN KEY (`condutor_id`) REFERENCES `pessoa` (`id`),
  ADD CONSTRAINT `fk_multas_3` FOREIGN KEY (`system_unit_id`) REFERENCES `system_unit` (`id`),
  ADD CONSTRAINT `fk_multas_4` FOREIGN KEY (`departamento_unit_id`) REFERENCES `departamento_unit` (`id`),
  ADD CONSTRAINT `fk_multas_5` FOREIGN KEY (`system_users_id`) REFERENCES `system_users` (`id`);

--
-- Restrições para tabelas `multas_anexos`
--
ALTER TABLE `multas_anexos`
  ADD CONSTRAINT `fk_multas_anexos_1` FOREIGN KEY (`multas_id`) REFERENCES `multas` (`id`);

--
-- Restrições para tabelas `notas_system_unit`
--
ALTER TABLE `notas_system_unit`
  ADD CONSTRAINT `fk_notas_1` FOREIGN KEY (`system_unit_id`) REFERENCES `system_unit` (`id`);

--
-- Restrições para tabelas `pedido_as_cliente`
--
ALTER TABLE `pedido_as_cliente`
  ADD CONSTRAINT `fk_pedido_as_cliente_1` FOREIGN KEY (`pedido_frotas_id`) REFERENCES `pedido_frotas` (`id`),
  ADD CONSTRAINT `fk_pedido_as_cliente_2` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoa` (`id`);

--
-- Restrições para tabelas `pedido_frotas`
--
ALTER TABLE `pedido_frotas`
  ADD CONSTRAINT `fk_pedidofrotas_10` FOREIGN KEY (`departamento_unit_id`) REFERENCES `departamento_unit` (`id`),
  ADD CONSTRAINT `fk_pedidofrotas_11` FOREIGN KEY (`system_users_id`) REFERENCES `system_users` (`id`),
  ADD CONSTRAINT `fk_pedidofrotas_12` FOREIGN KEY (`condutor_retirada_id`) REFERENCES `condutor` (`id`),
  ADD CONSTRAINT `fk_pedidofrotas_5` FOREIGN KEY (`condutor_entrada_id`) REFERENCES `condutor` (`id`),
  ADD CONSTRAINT `fk_pedidofrotas_6` FOREIGN KEY (`tipo_manutencao_id`) REFERENCES `tipo_manutencao` (`id`),
  ADD CONSTRAINT `fk_pedidofrotas_7` FOREIGN KEY (`negociacao_id`) REFERENCES `negociacao` (`id`),
  ADD CONSTRAINT `fk_pedidofrotas_8` FOREIGN KEY (`condicao_pagamento_id`) REFERENCES `condicao_pagamento` (`id`),
  ADD CONSTRAINT `fk_pedidofrotas_9` FOREIGN KEY (`system_unit_id`) REFERENCES `system_unit` (`id`),
  ADD CONSTRAINT `fk_pedidomanutencao_1` FOREIGN KEY (`estado_pedido_frotas_id`) REFERENCES `estado_pedido_frotas` (`id`),
  ADD CONSTRAINT `fk_pedidomanutencao_2` FOREIGN KEY (`estabelecimento_id`) REFERENCES `pessoa` (`id`);

--
-- Restrições para tabelas `pedido_frotas_historico`
--
ALTER TABLE `pedido_frotas_historico`
  ADD CONSTRAINT `fk_pedido_frotas_historico_3` FOREIGN KEY (`estado_pedido_frotas_id`) REFERENCES `estado_pedido_frotas` (`id`);

--
-- Restrições para tabelas `produto_system_unit`
--
ALTER TABLE `produto_system_unit`
  ADD CONSTRAINT `fk_produto_system_unit_1` FOREIGN KEY (`system_unit_id`) REFERENCES `system_unit` (`id`),
  ADD CONSTRAINT `fk_produto_system_unit_2` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`);

--
-- Restrições para tabelas `propostas`
--
ALTER TABLE `propostas`
  ADD CONSTRAINT `fk_propostas_10` FOREIGN KEY (`motorista_retirada_id`) REFERENCES `pessoa` (`id`),
  ADD CONSTRAINT `fk_propostas_12` FOREIGN KEY (`motorista_entrada_id`) REFERENCES `pessoa` (`id`),
  ADD CONSTRAINT `fk_propostas_2` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoa` (`id`),
  ADD CONSTRAINT `fk_propostas_3` FOREIGN KEY (`estado_pedido_frotas_id`) REFERENCES `estado_pedido_frotas` (`id`),
  ADD CONSTRAINT `fk_propostas_5` FOREIGN KEY (`veiculos_id`) REFERENCES `veiculos` (`id`),
  ADD CONSTRAINT `fk_propostas_7` FOREIGN KEY (`system_unit_id`) REFERENCES `system_unit` (`id`),
  ADD CONSTRAINT `fk_propostas_8` FOREIGN KEY (`departamento_unit_id`) REFERENCES `departamento_unit` (`id`),
  ADD CONSTRAINT `fk_propostas_81` FOREIGN KEY (`entidade_id`) REFERENCES `entidade` (`id`),
  ADD CONSTRAINT `fk_propostas_9` FOREIGN KEY (`system_users_id`) REFERENCES `system_users` (`id`);

--
-- Restrições para tabelas `propostas_historico`
--
ALTER TABLE `propostas_historico`
  ADD CONSTRAINT `fk_propostas_historico_2` FOREIGN KEY (`estado_pedido_frotas_id`) REFERENCES `estado_pedido_frotas` (`id`);

--
-- Restrições para tabelas `saldo_departamento`
--
ALTER TABLE `saldo_departamento`
  ADD CONSTRAINT `fk_saldo_departamento_unit_1` FOREIGN KEY (`departamento_unit_id`) REFERENCES `departamento_unit` (`id`);

--
-- Restrições para tabelas `saldo_entidade_contrato`
--
ALTER TABLE `saldo_entidade_contrato`
  ADD CONSTRAINT `fk_saldo_entidade_contrato_1` FOREIGN KEY (`entidade_id`) REFERENCES `entidade` (`id`),
  ADD CONSTRAINT `fk_saldo_entidade_contrato_2` FOREIGN KEY (`system_users_id`) REFERENCES `system_users` (`id`);

--
-- Restrições para tabelas `saldo_veiculo`
--
ALTER TABLE `saldo_veiculo`
  ADD CONSTRAINT `fk_saldo_veiculo_1` FOREIGN KEY (`veiculos_id`) REFERENCES `veiculos` (`id`);

--
-- Restrições para tabelas `seguimento_pedido_frotas`
--
ALTER TABLE `seguimento_pedido_frotas`
  ADD CONSTRAINT `fk_seguimento_pedido_frotas_2` FOREIGN KEY (`seguimento_id`) REFERENCES `seguimento` (`id`);

--
-- Restrições para tabelas `seguros`
--
ALTER TABLE `seguros`
  ADD CONSTRAINT `fk_seguros_1` FOREIGN KEY (`saldo_entidade_contrato_id`) REFERENCES `saldo_entidade_contrato` (`id`);

--
-- Restrições para tabelas `system_document`
--
ALTER TABLE `system_document`
  ADD CONSTRAINT `fk_system_document_1` FOREIGN KEY (`system_user_id`) REFERENCES `system_users` (`id`),
  ADD CONSTRAINT `fk_system_document_2` FOREIGN KEY (`category_id`) REFERENCES `system_document_category` (`id`),
  ADD CONSTRAINT `system_document_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `system_document_category` (`id`);

--
-- Restrições para tabelas `system_document_group`
--
ALTER TABLE `system_document_group`
  ADD CONSTRAINT `fk_system_document_group_1` FOREIGN KEY (`system_group_id`) REFERENCES `system_group` (`id`),
  ADD CONSTRAINT `fk_system_document_group_2` FOREIGN KEY (`document_id`) REFERENCES `system_document` (`id`),
  ADD CONSTRAINT `system_document_group_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `system_document` (`id`);

--
-- Restrições para tabelas `system_document_user`
--
ALTER TABLE `system_document_user`
  ADD CONSTRAINT `fk_system_document_user_1` FOREIGN KEY (`system_user_id`) REFERENCES `system_users` (`id`),
  ADD CONSTRAINT `fk_system_document_user_2` FOREIGN KEY (`document_id`) REFERENCES `system_document` (`id`),
  ADD CONSTRAINT `system_document_user_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `system_document` (`id`);

--
-- Restrições para tabelas `system_group_program`
--
ALTER TABLE `system_group_program`
  ADD CONSTRAINT `fk_system_group_program_1` FOREIGN KEY (`system_program_id`) REFERENCES `system_program` (`id`),
  ADD CONSTRAINT `fk_system_group_program_2` FOREIGN KEY (`system_group_id`) REFERENCES `system_group` (`id`);

--
-- Restrições para tabelas `system_message`
--
ALTER TABLE `system_message`
  ADD CONSTRAINT `fk_system_message_1` FOREIGN KEY (`system_user_id`) REFERENCES `system_users` (`id`),
  ADD CONSTRAINT `fk_system_message_2` FOREIGN KEY (`system_user_to_id`) REFERENCES `system_users` (`id`);

--
-- Restrições para tabelas `system_users`
--
ALTER TABLE `system_users`
  ADD CONSTRAINT `fk_system_user_1` FOREIGN KEY (`system_unit_id`) REFERENCES `system_unit` (`id`),
  ADD CONSTRAINT `fk_system_user_2` FOREIGN KEY (`frontpage_id`) REFERENCES `system_program` (`id`);

--
-- Restrições para tabelas `system_user_group`
--
ALTER TABLE `system_user_group`
  ADD CONSTRAINT `fk_system_user_group_1` FOREIGN KEY (`system_group_id`) REFERENCES `system_group` (`id`),
  ADD CONSTRAINT `fk_system_user_group_2` FOREIGN KEY (`system_user_id`) REFERENCES `system_users` (`id`);

--
-- Restrições para tabelas `system_user_program`
--
ALTER TABLE `system_user_program`
  ADD CONSTRAINT `fk_system_user_program_1` FOREIGN KEY (`system_program_id`) REFERENCES `system_program` (`id`),
  ADD CONSTRAINT `fk_system_user_program_2` FOREIGN KEY (`system_user_id`) REFERENCES `system_users` (`id`);

--
-- Restrições para tabelas `system_user_unit`
--
ALTER TABLE `system_user_unit`
  ADD CONSTRAINT `fk_system_user_unit_1` FOREIGN KEY (`system_user_id`) REFERENCES `system_users` (`id`),
  ADD CONSTRAINT `fk_system_user_unit_2` FOREIGN KEY (`system_unit_id`) REFERENCES `system_unit` (`id`);

--
-- Restrições para tabelas `taxas_pessoa`
--
ALTER TABLE `taxas_pessoa`
  ADD CONSTRAINT `fk_taxas_pessoa_1` FOREIGN KEY (`system_unit_id`) REFERENCES `system_unit` (`id`),
  ADD CONSTRAINT `fk_taxas_pessoa_3` FOREIGN KEY (`system_users_id`) REFERENCES `system_users` (`id`),
  ADD CONSTRAINT `fk_taxas_pessoa_4` FOREIGN KEY (`entidade_id`) REFERENCES `entidade` (`id`),
  ADD CONSTRAINT `fk_taxas_pessoa_5` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoa` (`id`);

--
-- Restrições para tabelas `vehicletoken`
--
ALTER TABLE `vehicletoken`
  ADD CONSTRAINT `fk_vehicletoken_1` FOREIGN KEY (`veiculos_id`) REFERENCES `veiculos` (`id`);

--
-- Restrições para tabelas `veiculos`
--
ALTER TABLE `veiculos`
  ADD CONSTRAINT `fk_veiculos_1` FOREIGN KEY (`tipo_combustivel_id`) REFERENCES `tipo_combustivel` (`id`),
  ADD CONSTRAINT `fk_veiculos_10` FOREIGN KEY (`system_users_id`) REFERENCES `system_users` (`id`),
  ADD CONSTRAINT `fk_veiculos_120` FOREIGN KEY (`especie_id`) REFERENCES `especie` (`id`),
  ADD CONSTRAINT `fk_veiculos_130` FOREIGN KEY (`familia_id`) REFERENCES `familia` (`id`),
  ADD CONSTRAINT `fk_veiculos_2` FOREIGN KEY (`dispositivos_id`) REFERENCES `dispositivos` (`id`),
  ADD CONSTRAINT `fk_veiculos_3` FOREIGN KEY (`marca_id`) REFERENCES `marca` (`id`),
  ADD CONSTRAINT `fk_veiculos_4` FOREIGN KEY (`modelo_id`) REFERENCES `modelo` (`id`),
  ADD CONSTRAINT `fk_veiculos_5` FOREIGN KEY (`propriedade_id`) REFERENCES `propriedade` (`id`),
  ADD CONSTRAINT `fk_veiculos_6` FOREIGN KEY (`corveiculo_id`) REFERENCES `corveiculo` (`id`),
  ADD CONSTRAINT `fk_veiculos_7` FOREIGN KEY (`tipo_veiculo_id`) REFERENCES `tipo_veiculo` (`id`),
  ADD CONSTRAINT `fk_veiculos_8` FOREIGN KEY (`system_unit_id`) REFERENCES `system_unit` (`id`),
  ADD CONSTRAINT `fk_veiculos_80` FOREIGN KEY (`system_unit_id`) REFERENCES `system_unit` (`id`),
  ADD CONSTRAINT `fk_veiculos_9` FOREIGN KEY (`departamento_unit_id`) REFERENCES `departamento_unit` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
