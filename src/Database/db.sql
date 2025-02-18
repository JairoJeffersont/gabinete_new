CREATE TABLE
  cliente (
    cliente_id varchar(36) NOT NULL,
    cliente_nome varchar(100) NOT NULL,
    cliente_email varchar(50) NOT NULL UNIQUE,
    cliente_telefone varchar(14) NOT NULL,
    cliente_ativo tinyint (1) NOT NULL,
    cliente_endereco varchar(255) DEFAULT NULL,
    cliente_cep varchar(8) DEFAULT NULL,
    cliente_cpf varchar(14) NOT NULL UNIQUE,
    cliente_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cliente_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (cliente_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  tipo_gabinete (
    tipo_gabinete_id varchar(36) NOT NULL,
    tipo_gabinete_nome varchar(255) NOT NULL UNIQUE,
    tipo_gabinete_informacoes TEXT NULL,
    tipo_gabinete_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (tipo_gabinete_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO tipo_gabinete (tipo_gabinete_id, tipo_gabinete_nome, tipo_gabinete_informacoes)
  VALUES
    (UUID(), 'Deputado Federal', 'Gabinete destinado a um deputado federal no Congresso Nacional'),
    (UUID(), 'Deputado Estadual', 'Gabinete destinado a um deputado estadual nas assembleias estaduais'),
    (UUID(), 'Vereador', 'Gabinete destinado a um vereador nas câmaras municipais'),
    (UUID(), 'Prefeito', 'Gabinete destinado ao prefeito de um município'),
    (UUID(), 'Governador', 'Gabinete destinado ao governador de um estado'),
    (UUID(), 'Senador', 'Gabinete destinado a um senador no Senado Federal');


CREATE TABLE
  gabinete (
    gabinete_id varchar(36) NOT NULL,
    gabinete_cliente varchar(36) NOT NULL,
    gabinete_tipo varchar(36) NOT NULL,
    gabinete_politico varchar(36) NOT NULL UNIQUE,
    gabinete_estado varchar(2) NOT NULL,
    gabinete_endereco varchar(255) NULL,
    gabinete_municipio varchar(255) NULL,
    gabinete_telefone varchar(15) NULL,
    gabinete_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    gabinete_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (gabinete_id),
    CONSTRAINT fk_gabinete_cliente FOREIGN KEY (gabinete_cliente) REFERENCES cliente (cliente_id),
    CONSTRAINT fk_gabinete_tipo FOREIGN KEY (gabinete_tipo) REFERENCES tipo_gabinete (tipo_gabinete_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  usuario_tipo (
    usuario_tipo_id varchar(36) NOT NULL,
    usuario_tipo_nome varchar(255) NOT NULL,
    usuario_tipo_descricao varchar(255) NOT NULL,
    PRIMARY KEY (usuario_tipo_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO usuario_tipo (usuario_tipo_id, usuario_tipo_nome, usuario_tipo_descricao) 
  VALUES 
    (UUID(), 'Administrador', 'Usuario administrativo'),
    (UUID(), 'Comunicação', 'Usuario da assessoria de comunicação'),
    (UUID(), 'Legislativo', 'Usuario da assessoria legislativa'),
    (UUID(), 'Orçamento', 'Usuario da assessoria orçamentária'),
    (UUID(), 'Secretaria', 'Usuario da secretaria do gabinete');

CREATE TABLE
  usuario (
    usuario_id varchar(36) NOT NULL,
    usuario_gabinete varchar(36) NOT NULL,
    usuario_nome varchar(255) NOT NULL,
    usuario_email varchar(255) NOT NULL,
    usuario_aniversario DATE NOT NULL,
    usuario_telefone varchar(15) NOT NULL,
    usuario_senha varchar(255) NOT NULL,
    usuario_tipo varchar(36) NOT NULL,
    usuario_ativo tinyint NOT NULL,
    usuario_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id),
    CONSTRAINT fk_usuario_gabinete FOREIGN KEY (usuario_gabinete) REFERENCES gabinete (gabinete_id),
    CONSTRAINT fk_usuario_tipo FOREIGN KEY (usuario_tipo) REFERENCES usuario_tipo (usuario_tipo_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;