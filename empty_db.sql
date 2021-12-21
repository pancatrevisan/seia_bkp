-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2020 at 10:47 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `swaba`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

CREATE TABLE `activity` (
  `id` varchar(50) NOT NULL,
  `owner_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `antecedent` varchar(250) NOT NULL,
  `behavior` varchar(250) NOT NULL,
  `consequence` varchar(250) NOT NULL,
  `category` varchar(200) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `auto` tinyint(1) NOT NULL DEFAULT 0,
  `auto_guide` tinyint(1) NOT NULL DEFAULT 0,
  `difficulty` varchar(15) NOT NULL DEFAULT 'NOT_RATED'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `activity`
--

INSERT INTO `activity` (`id`, `owner_id`, `name`, `antecedent`, `behavior`, `consequence`, `category`, `active`, `auto`, `auto_guide`, `difficulty`) VALUES
('pubAgruparimagens', 'pub', 'Agrupar imagens', 'SÃ£o exibidos estÃ­mulos (imagens) que devem ser agrupados', 'O estudante deve agrupar estÃ­mulos de acordo com a configuraÃ§Ã£o da atividade.', 'Configurada no plano de ensino', 'Template, dragging, drag', 1, 0, 0, ''),
('pubApresentarimagemtextoudioouvdeo', 'pub', 'Apresentar imagem, texto, Ã¡udio ou vÃ­deo', 'Definido na atividade', 'O estudante fez o comportamento esperado', 'Eh apresentado um item de preferencia ao estudante', 'reinforcement,template', 1, 0, 0, ''),
('pubApresentarinstrucao', 'pub', 'Apresentar instrucao', '', '', '', 'template', 1, 0, 0, ''),
('pubInserirTexto', 'pub', 'Inserir Texto', 'SÃ£o apresentados estÃ­mulos diferentes (imagens, textos ou Ã¡udios)', 'O estudante escreve algo sobre os estÃ­mulos', 'Configurada no programa de ensino', 'Template', 1, 0, 0, ''),
('pubMatchingtoSample', 'pub', 'Matching to Sample', 'Ã‰ apresentado um estÃ­mulo modelo (imagem, texto ou Ã¡udio) e estÃ­mulos de comparaÃ§Ã£o (imagem, texto)', 'O estudante deve selecionar o estÃ­mulo de comparaÃ§Ã£o esperado', 'Configurada no programa de ensino', 'MTS, template', 1, 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `curriculum`
--

CREATE TABLE `curriculum` (
  `id` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `category` varchar(150) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `order_type` varchar(50) NOT NULL DEFAULT 'followOrder',
  `reinforcement_type` varchar(50) NOT NULL DEFAULT 'none',
  `reinforcement_value` varchar(50) NOT NULL,
  `frequency_type` varchar(50) NOT NULL DEFAULT 'atEveryCorrect',
  `frequency_value` varchar(50) NOT NULL,
  `error_type` varchar(50) NOT NULL DEFAULT 'none',
  `error_value` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'curriculum'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `curriculum`
--

INSERT INTO `curriculum` (`id`, `student_id`, `name`, `description`, `category`, `active`, `order_type`, `reinforcement_type`, `reinforcement_value`, `frequency_type`, `frequency_value`, `error_type`, `error_value`, `type`) VALUES
('student060120125957060120125957', 'student060120125957', '', '', '', 1, 'followOrder', 'none', '', 'atEveryCorrect', '', 'none', '', 'curriculum'),
('student060120125957060120125957aval', 'student060120125957', '', '', '', 1, 'followOrder', 'none', '', 'atEveryCorrect', '', 'none', '', 'aval'),
('student140220101700140220101700', 'student140220101700', '', '', '', 1, 'followOrder', 'none', '', 'atEveryCorrect', '', 'none', '', 'curriculum'),
('student140220101700140220101700aval', 'student140220101700', '', '', '', 1, 'followOrder', 'none', '', 'atEveryCorrect', '', 'none', '', 'aval'),
('student170120123717170120123717', 'student170120123717', '', '', '', 1, 'followOrder', 'none', '', 'atEveryCorrect', '', 'none', '', 'curriculum'),
('student170120123717170120123717aval', 'student170120123717', '', '', '', 1, 'followOrder', 'none', '', 'atEveryCorrect', '', 'none', '', 'aval');

-- --------------------------------------------------------

--
-- Table structure for table `error`
--

CREATE TABLE `error` (
  `id` int(11) NOT NULL,
  `description` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `group_activity`
--

CREATE TABLE `group_activity` (
  `id` varchar(50) NOT NULL,
  `group_id` varchar(50) NOT NULL,
  `owner_id` varchar(50) NOT NULL,
  `activity_id` varchar(50) NOT NULL,
  `position` int(11) NOT NULL,
  `reinforcement_type` varchar(50) NOT NULL DEFAULT 'definedByGroup',
  `reinforcement_value` varchar(100) NOT NULL,
  `correction_type` varchar(50) DEFAULT 'definedByGroup',
  `correction_value` varchar(100) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `guide_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `label`
--

CREATE TABLE `label` (
  `id` varchar(50) NOT NULL,
  `stimuli_id` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `professional`
--

CREATE TABLE `professional` (
  `id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `professional`
--

INSERT INTO `professional` (`id`) VALUES
('mu');

-- --------------------------------------------------------

--
-- Table structure for table `program`
--

CREATE TABLE `program` (
  `id` varchar(50) NOT NULL,
  `curriculumId` varchar(50) NOT NULL,
  `owner_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `antecedent` varchar(200) NOT NULL,
  `behavior` varchar(200) NOT NULL,
  `consequence` varchar(200) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `order_type` varchar(50) NOT NULL DEFAULT 'none',
  `reinforcement_type` varchar(50) NOT NULL DEFAULT 'none',
  `reinforcement_value` varchar(50) NOT NULL,
  `frequency_type` varchar(50) NOT NULL DEFAULT 'none',
  `frequency_value` varchar(50) NOT NULL,
  `error_type` varchar(50) NOT NULL DEFAULT 'none',
  `error_value` varchar(50) NOT NULL,
  `position` int(11) NOT NULL,
  `auto` tinyint(1) NOT NULL DEFAULT 0,
  `guide_id` varchar(50) NOT NULL,
  `category` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `program_trial`
--

CREATE TABLE `program_trial` (
  `id` varchar(50) NOT NULL,
  `program_id` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `professional_id` varchar(50) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `session_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reinforcement`
--

CREATE TABLE `reinforcement` (
  `id` varchar(50) NOT NULL,
  `owner_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `id` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `professional_id` varchar(50) NOT NULL,
  `last_date` datetime NOT NULL,
  `complete` tinyint(1) NOT NULL,
  `curriculum_id` varchar(50) NOT NULL,
  `last_trial` varchar(60) NOT NULL,
  `last_program_id` varchar(60) NOT NULL,
  `last_groupactivity_id` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stimuli`
--

CREATE TABLE `stimuli` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `owner_id` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `type` varchar(50) NOT NULL,
  `url` varchar(300) NOT NULL,
  `version` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `birthday` date NOT NULL,
  `sex` varchar(10) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(5) NOT NULL,
  `medication` varchar(100) NOT NULL,
  `avatar` varchar(50) NOT NULL DEFAULT 'avatar.png',
  `curriculum_id` varchar(50) NOT NULL,
  `evaluation_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `name`, `birthday`, `sex`, `city`, `state`, `medication`, `avatar`, `curriculum_id`, `evaluation_id`) VALUES
('student140220101700', 'Estudante 1', '1988-06-08', 'male', 'Marechal Rondon', 'PR', 'Nenhuma', 'avatar.png', 'student140220101700140220101700', 'student140220101700140220101700aval');

-- --------------------------------------------------------

--
-- Table structure for table `student_tutorship`
--

CREATE TABLE `student_tutorship` (
  `id` varchar(50) NOT NULL,
  `professional_id` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `student_tutorship`
--

INSERT INTO `student_tutorship` (`id`, `professional_id`, `student_id`) VALUES
('tutorship140220101700', 'mu', 'student140220101700');

-- --------------------------------------------------------

--
-- Table structure for table `trial`
--

CREATE TABLE `trial` (
  `id` varchar(50) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `program_trial_id` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `professional_id` varchar(50) NOT NULL,
  `activity_id` varchar(50) NOT NULL,
  `result` varchar(50) NOT NULL,
  `result_data` text NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `session_id` varchar(60) NOT NULL,
  `groupactivity_id` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `username` varchar(30) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `pass` varchar(300) NOT NULL,
  `city` varchar(100) NOT NULL,
  `comment` varchar(400) NOT NULL,
  `role` varchar(40) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `avatar` varchar(100) NOT NULL DEFAULT 'avatar.png'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`username`, `name`, `email`, `pass`, `city`, `comment`, `role`, `active`, `avatar`) VALUES
('epimentel', 'Edson Pimentel', 'epimentel@email.com', '$2y$10$DMnvv39372hWaWPSYIpI..sQYs/OZMgU76YRPXl9HFrL.K4.jdBUW', 'Santo AndrÃ©', 'Professor da UFABC.', 'professional', 0, 'avatar.png'),
('jpgois', 'JoÃ£o Paulo Gois', 'jpgois@gmail.com', '$2y$10$1eyyTzyz9UmRQt.4nxgJouXNtZrJwhQYJQMZ0Qgcbbo28SbD.AW8q', 'Santo AndrÃ©', 'Professor da UFABC.', 'professional', 0, 'avatar.png'),
('mu', 'Mu', 'mu@santuary.com', '$2y$10$/9BJlQ5wKnWsepk61oAqGe5z1qahQ2408.0Sp7ULl004A5jAaCgmm', 'Athenas', 'bla', 'professional', 0, 'avatar.png'),
('pbenitez', 'Priscila Benitez', 'pbenitez@email.com', '$2y$10$GKyXtrQcf66fUOeaZgLQpOScMZK0vuEQmsHxEufpXOGJBvp8lSoEm', 'Santo AndrÃ©', 'Professora da UFABC.', 'professional', 0, 'avatar.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `curriculum`
--
ALTER TABLE `curriculum`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `error`
--
ALTER TABLE `error`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_activity`
--
ALTER TABLE `group_activity`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `professional`
--
ALTER TABLE `professional`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `program`
--
ALTER TABLE `program`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `program_trial`
--
ALTER TABLE `program_trial`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reinforcement`
--
ALTER TABLE `reinforcement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stimuli`
--
ALTER TABLE `stimuli`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_tutorship`
--
ALTER TABLE `student_tutorship`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trial`
--
ALTER TABLE `trial`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `error`
--
ALTER TABLE `error`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
