
CREATE TABLE `activity` (
  `id` varchar(50) NOT NULL,
  `owner_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `antecedent` varchar(250) NOT NULL,
  `behavior` varchar(250) NOT NULL,
  `consequence` varchar(250) NOT NULL,
  `category` varchar(200) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `auto` tinyint(1) NOT NULL DEFAULT '0',
  `auto_guide` tinyint(1) NOT NULL DEFAULT '0',
  `difficulty` varchar(15) NOT NULL DEFAULT 'NOT_RATED'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `curriculum` (
  `id` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `category` varchar(150) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `order_type` varchar(50) NOT NULL DEFAULT 'followOrder',
  `reinforcement_type` varchar(50) NOT NULL DEFAULT 'none',
  `reinforcement_value` varchar(50) NOT NULL,
  `frequency_type` varchar(50) NOT NULL DEFAULT 'atEveryCorrect',
  `frequency_value` varchar(50) NOT NULL,
  `error_type` varchar(50) NOT NULL DEFAULT 'none',
  `error_value` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'curriculum'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `curriculum_program` (
  `id` varchar(100) NOT NULL,
  `sessionProgram_id` varchar(100) NOT NULL,
  `curriculum_id` varchar(100) NOT NULL,
  `student_id` varchar(100) NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `error` (
  `id` int(11) NOT NULL,
  `description` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `guide_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `label` (
  `id` varchar(50) NOT NULL,
  `stimuli_id` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `mts_evaluation` (
  `id` int(11) NOT NULL,
  `model_stimuli_type` varchar(30) NOT NULL,
  `model_stimuli` varchar(100) NOT NULL,
  `compare_stimuli` varchar(300) NOT NULL,
  `correct_stimuli` varchar(50) NOT NULL,
  `difficulty` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `professional` (
  `id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `program` (
  `id` varchar(50) NOT NULL,
  `curriculumId` varchar(50) NOT NULL,
  `owner_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `antecedent` varchar(200) NOT NULL,
  `behavior` varchar(200) NOT NULL,
  `consequence` varchar(200) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `order_type` varchar(50) NOT NULL DEFAULT 'none',
  `reinforcement_type` varchar(50) NOT NULL DEFAULT 'none',
  `reinforcement_value` varchar(50) NOT NULL,
  `frequency_type` varchar(50) NOT NULL DEFAULT 'none',
  `frequency_value` varchar(50) NOT NULL,
  `error_type` varchar(50) NOT NULL DEFAULT 'none',
  `error_value` varchar(50) NOT NULL,
  `position` int(11) NOT NULL,
  `auto` tinyint(1) NOT NULL DEFAULT '0',
  `guide_id` varchar(50) NOT NULL,
  `category` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `program_trial` (
  `id` varchar(50) NOT NULL,
  `program_id` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `professional_id` varchar(50) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `session_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `reinforcement` (
  `id` varchar(50) NOT NULL,
  `owner_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `selectd_image` (
  `id` varchar(50) NOT NULL,
  `tags` varchar(500) NOT NULL,
  `emotion` varchar(100) NOT NULL,
  `search_query` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


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


CREATE TABLE `sessionprogram_activity_trial` (
  `id` varchar(50) NOT NULL,
  `sessionprogramTrial_id` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `professional_id` varchar(50) NOT NULL,
  `sessionactivity_id` varchar(50) NOT NULL,
  `result` varchar(50) NOT NULL,
  `result_data` text NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sessionprogram_trial` (
  `id` varchar(50) NOT NULL,
  `session_program_id` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `professional_id` varchar(50) NOT NULL,
  `last_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `session_program` (
  `id` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `owner_id` varchar(50) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `follow_activity_order` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--

CREATE TABLE `session_program_activity` (
  `id` varchar(50) NOT NULL,
  `activity_id` varchar(50) NOT NULL,
  `sessionProgram_id` varchar(50) NOT NULL,
  `correction_type` varchar(50) NOT NULL DEFAULT 'none',
  `correction_value` varchar(50) NOT NULL,
  `reinforcer_type` varchar(50) NOT NULL DEFAULT 'none',
  `reinforcer_value` varchar(50) NOT NULL,
  `next_on_correct` varchar(150) NOT NULL DEFAULT 'none',
  `next_on_wrong` varchar(150) NOT NULL DEFAULT 'none',
  `position` int(11) NOT NULL,
  `next_on_correct_id` varchar(50) NOT NULL,
  `next_on_wrong_id` varchar(50) NOT NULL,
  `next_after_correction` varchar(100) NOT NULL,
  `next_after_correction_id` varchar(50) NOT NULL,
  `next_after_correction_wrong` varchar(100) NOT NULL,
  `next_after_correction_wrong_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `stimuli` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `owner_id` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `type` varchar(50) NOT NULL,
  `url` varchar(300) NOT NULL,
  `version` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


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
  `evaluation_id` varchar(50) NOT NULL,
  `active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `student_tutorship` (
  `id` varchar(50) NOT NULL,
  `professional_id` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `trial` (
  `id` varchar(50) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
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


CREATE TABLE `user` (
  `username` varchar(30) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `pass` varchar(300) NOT NULL,
  `city` varchar(100) NOT NULL,
  `comment` varchar(400) NOT NULL,
  `role` varchar(40) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `avatar` varchar(100) NOT NULL DEFAULT 'avatar.png',
  `tuto_finished` tinyint(1) NOT NULL DEFAULT '1',
  `athena` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `user_login` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `activity`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

ALTER TABLE `curriculum`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

ALTER TABLE `error`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `group_activity`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

ALTER TABLE `label`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `mts_evaluation`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `professional`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `program`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

ALTER TABLE `program_trial`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `reinforcement`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `selectd_image`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `session`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sessionprogram_activity_trial`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sessionprogram_trial`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `session_program`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `session_program_activity`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stimuli`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

ALTER TABLE `student`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `student_tutorship`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `trial`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `user`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `user_login`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `error`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `mts_evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

ALTER TABLE `user_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4339;



INSERT INTO `activity` (`id`, `owner_id`, `name`, `antecedent`, `behavior`, `consequence`, `category`, `active`, `auto`, `auto_guide`, `difficulty`) VALUES
('pubAgruparComBotaoFinalizar', 'pub', 'Agrupar Com Botao Finalizar', '', '', '', 'template, agrupar, botao', 1, 0, 0, 'NOT_RATED'),
('pubAgruparimagens', 'pub', 'Agrupar imagens', 'SÃƒÂ£o exibidos estÃƒÂ­mulos (imagens) que devem ser agrupados', 'O estudante deve agrupar estÃƒÂ­mulos de acordo com a configuraÃƒÂ§ÃƒÂ£o da atividade.', 'Configurada no plano de ensino', 'Template, dragging, drag', 1, 0, 0, ''),
('pubapenasavalia', 'pub', 'apenas avalia', '', '', '', 'template', 1, 0, 0, 'NOT_RATED'),
('pubapresentainstrucaoeavalia', 'pub', 'apresenta instrucao e avalia', '', '', '', 'template', 1, 0, 0, 'NOT_RATED'),
('pubApresentarimagemtextoudioouvdeo', 'pub', 'Apresentar imagem, texto, ÃƒÂ¡udio ou vÃƒÂ­deo', 'Definido na atividade', 'O estudante fez o comportamento esperado', 'Eh apresentado um item de preferencia ao estudante', 'reinforcement,template', 1, 0, 0, ''),
('pubApresentarinstrucao', 'pub', 'Apresentar instrucao', '', '', '', 'template', 1, 0, 0, ''),
('pubDesenharnaTela', 'pub', 'Desenhar na Tela', '', '', '', 'template', 1, 0, 0, 'NOT_RATED'),
('pubExibirInstrucaoSemRegistro', 'pub', 'Exibir Instrucao Sem Registro', '', '', '', 'template', 1, 0, 0, 'NOT_RATED'),
('pubInserirTexto', 'pub', 'Inserir Texto', 'SÃƒÂ£o apresentados estÃƒÂ­mulos diferentes (imagens, textos ou ÃƒÂ¡udios)', 'O estudante escreve algo sobre os estÃƒÂ­mulos', 'Configurada no programa de ensino', 'Template', 1, 0, 0, ''),
('pubJogodeCorridaEmoes', 'pub', 'Jogo de Corrida - EmoÃƒÂ§ÃƒÂµes', '', '', '', 'corrida, game, template', 1, 0, 0, 'NOT_RATED'),
('pubMatchingtoSample', 'pub', 'Matching to Sample', 'Ãƒâ€° apresentado um estÃƒÂ­mulo modelo (imagem, texto ou ÃƒÂ¡udio) e estÃƒÂ­mulos de comparaÃƒÂ§ÃƒÂ£o (imagem, texto)', 'O estudante deve selecionar o estÃƒÂ­mulo de comparaÃƒÂ§ÃƒÂ£o esperado', 'Configurada no programa de ensino', 'MTS, template', 1, 0, 0, ''),
('pubSelecionarPreferencias', 'pub', 'Selecionar Preferencias', '', '', '', 'template, preferencia', 1, 0, 0, 'NOT_RATED');


