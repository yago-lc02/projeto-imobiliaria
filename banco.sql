-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 04/06/2026 às 05:31
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `imobiliaria_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `administradores`
--

INSERT INTO `administradores` (`id`, `nome`, `email`, `senha`) VALUES
(1, 'Yago Admin', 'admin@imobiliaria.com', '$2y$10$vvYudgvslqYY8FSv9TYZVeInBBcZhRcqQxrD2Pzh5OEzAwi6eNNri');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `tipo_interesse` enum('venda','aluguel','ambos') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id`, `nome`, `cpf`, `email`, `telefone`, `tipo_interesse`) VALUES
(4, 'Neymar da Silva Santos Júnior', '333.333.333-33', 'neymar@web.com', '64 93333-3333', 'venda');

-- --------------------------------------------------------

--
-- Estrutura para tabela `corretores`
--

CREATE TABLE `corretores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `creci` varchar(20) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `especialidade` varchar(50) NOT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `corretores`
--

INSERT INTO `corretores` (`id`, `nome`, `creci`, `telefone`, `especialidade`, `foto`) VALUES
(1, 'Lionel Andrés Messi Cuccittini', '11111-F', '64 91111-1111', 'Lançamentos', 'uploads/imagens/corretor_6a1f658ab48b4.jpg'),
(3, 'Cristiano Ronaldo dos Santos Aveiro', '22222-F', '64 92222-2222', 'Geral', 'uploads/imagens/corretor_6a1f6a3741250.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `imoveis`
--

CREATE TABLE `imoveis` (
  `id` int(11) NOT NULL,
  `tipo` enum('venda','aluguel') NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `metragem` decimal(10,2) NOT NULL,
  `valor` decimal(15,2) NOT NULL,
  `status` enum('disponivel','vendido','alugado') DEFAULT 'disponivel',
  `fotos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fotos`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `imoveis`
--

INSERT INTO `imoveis` (`id`, `tipo`, `endereco`, `metragem`, `valor`, `status`, `fotos`) VALUES
(2, 'aluguel', 'Estrada Santos Dumont 3879 Itaim Duque de Caxias RJ 36579-911', 250.00, 40000.00, 'alugado', '[\"uploads\\/imagens\\/imovel_6a1f6f2a13baf_0.jpg\",\"uploads\\/imagens\\/imovel_6a1f6f2a140df_1.jpg\"]'),
(3, 'venda', 'Travessa Barão do Rio Branco	3099 Bela Vista Aracaju SE 58832-121', 150.00, 500000.00, 'vendido', '[\"uploads\\/imagens\\/imovel_6a1f75c635761_0.jpg\",\"uploads\\/imagens\\/imovel_6a1f75c635e8d_1.jpg\"]'),
(4, 'aluguel', 'Alameda Lauro de Freitas 1507 Boa Vista Rio Largo AL	84131-177', 400.00, 2500000.00, 'disponivel', '[\"uploads\\/imagens\\/imovel_6a1f910a77502_0.jpg\",\"uploads\\/imagens\\/imovel_6a1f910a77f72_1.jpg\"]'),
(5, 'venda', 'Estrada Sete de Setembro 4175 Apt 540 Jardim do Sol	Parintins AM 67755-737', 500.00, 50000.00, 'vendido', '[\"uploads\\/imagens\\/imovel_6a1f9175eb7ea_0.jpg\",\"uploads\\/imagens\\/imovel_6a1f9175ebdba_1.jpg\"]'),
(6, 'venda', 'Alameda Conselheiro Furtado 6538 Apt 247 Cidade Nova Bayeux PB 63792-479', 600.00, 250000.00, 'disponivel', '[\"uploads\\/imagens\\/imovel_6a1f920e349ac_0.jpg\",\"uploads\\/imagens\\/imovel_6a1f920e3504d_1.jpg\"]'),
(7, 'aluguel', 'Avenida Marechal Deodoro 8691 Moema Picos PI 02097-572', 500.00, 70000.00, 'disponivel', '[\"uploads\\/imagens\\/imovel_6a1f929507ecf_0.jpg\",\"uploads\\/imagens\\/imovel_6a1f929508333_1.jpg\"]');

-- --------------------------------------------------------

--
-- Estrutura para tabela `visitas`
--

CREATE TABLE `visitas` (
  `id` int(11) NOT NULL,
  `data_horario` datetime NOT NULL,
  `imovel_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `corretor_id` int(11) NOT NULL,
  `observacoes` text DEFAULT NULL,
  `resultado` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `visitas`
--

INSERT INTO `visitas` (`id`, `data_horario`, `imovel_id`, `cliente_id`, `corretor_id`, `observacoes`, `resultado`) VALUES
(3, '2026-06-03 07:00:00', 7, 4, 1, 'Cliente possui disponibilidade de horários', 'Cliente irá revisitar em breve');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Índices de tabela `corretores`
--
ALTER TABLE `corretores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `creci` (`creci`);

--
-- Índices de tabela `imoveis`
--
ALTER TABLE `imoveis`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `visitas`
--
ALTER TABLE `visitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imovel_id` (`imovel_id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `corretor_id` (`corretor_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `corretores`
--
ALTER TABLE `corretores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `imoveis`
--
ALTER TABLE `imoveis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `visitas`
--
ALTER TABLE `visitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `visitas`
--
ALTER TABLE `visitas`
  ADD CONSTRAINT `visitas_ibfk_1` FOREIGN KEY (`imovel_id`) REFERENCES `imoveis` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `visitas_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `visitas_ibfk_3` FOREIGN KEY (`corretor_id`) REFERENCES `corretores` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
