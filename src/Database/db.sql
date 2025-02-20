-- Tabela de Tipos de Gabinetes
CREATE TABLE
  gabinete_tipo (
    gabinete_tipo_id varchar(36) NOT NULL,
    gabinete_tipo_nome varchar(255) NOT NULL UNIQUE,
    gabinete_tipo_informacoes TEXT NULL,
    gabinete_tipo_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (gabinete_tipo_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- Tabela de Gabinetes
CREATE TABLE
  gabinete (
    gabinete_id varchar(36) NOT NULL,
    gabinete_tipo varchar(36) NOT NULL,
    gabinete_nome varchar(50) NOT NULL,
    gabinete_endereco varchar(255) NOT NULL,
    gabinete_municipio varchar(50) NOT NULL,
    gabinete_estado varchar(50) NOT NULL,
    gabinete_email varchar(255) NOT NULL,
    gabinete_telefone varchar(20) NOT NULL,
    gabinete_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    gabinete_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (gabinete_id),
    CONSTRAINT fk_gabinete_tipo FOREIGN KEY (gabinete_tipo) REFERENCES gabinete_tipo (gabinete_tipo_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- Tabela de Tipos de Usuários
CREATE TABLE
  usuario_tipo (
    usuario_tipo_id varchar(36) NOT NULL,
    usuario_tipo_nome varchar(255) NOT NULL,
    usuario_tipo_descricao varchar(255) NOT NULL,
    PRIMARY KEY (usuario_tipo_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- Tabela de Usuários
CREATE TABLE
  usuario (
    usuario_id varchar(36) NOT NULL,
    usuario_tipo varchar(36) NOT NULL,
    usuario_gabinete varchar(36) NOT NULL,
    usuario_nome varchar(255) NOT NULL,
    usuario_cpf varchar(14) DEFAULT NULL,
    usuario_email varchar(255) NOT NULL,
    usuario_aniversario DATE DEFAULT NULL,
    usuario_telefone varchar(20) NOT NULL,
    usuario_senha varchar(255) NOT NULL,
    usuario_token varchar(36) DEFAULT NULL,
    usuario_ativo tinyint NOT NULL,
    usuario_gestor BOOLEAN NOT NULL DEFAULT FALSE,
    usuario_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id),
    CONSTRAINT fk_usuario_tipo FOREIGN KEY (usuario_tipo) REFERENCES usuario_tipo (usuario_tipo_id),
    CONSTRAINT fk_usuario_gabinete FOREIGN KEY (usuario_gabinete) REFERENCES gabinete (gabinete_id) --ON DELETE CASCADE,
    CONSTRAINT unico_gestor_por_gabinete UNIQUE (usuario_gabinete, usuario_gestor)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO gabinete_tipo (gabinete_tipo_id, gabinete_tipo_nome, gabinete_tipo_informacoes)
  VALUES
    (1, 'Outro', 'Gabinete administrativo'),
    (2, 'Deputado Federal', 'Gabinete destinado a um deputado federal no Congresso Nacional'),
    (3, 'Deputado Estadual', 'Gabinete destinado a um deputado estadual nas assembleias estaduais'),
    (4, 'Vereador', 'Gabinete destinado a um vereador nas câmaras municipais'),
    (5, 'Prefeito', 'Gabinete destinado ao prefeito de um município'),
    (6, 'Governador', 'Gabinete destinado ao governador de um estado'),
    (7, 'Senador', 'Gabinete destinado a um senador no Senado Federal');  

INSERT INTO usuario_tipo (usuario_tipo_id, usuario_tipo_nome, usuario_tipo_descricao) 
  VALUES 
    (1, 'Administrador', 'Usuario root do sistema'),
    (2, 'Administrativo', 'Usuario administrativo'),
    (3, 'Comunicação', 'Usuario da assessoria de comunicação'),
    (4, 'Legislativo', 'Usuario da assessoria legislativa'),
    (5, 'Orçamento', 'Usuario da assessoria orçamentária'),
    (6, 'Secretaria', 'Usuario da secretaria do gabinete');  