CREATE TABLE
  tipo_gabinete (
    tipo_gabinete_id varchar(36) NOT NULL,
    tipo_gabinete_nome varchar(255) NOT NULL UNIQUE,
    tipo_gabinete_informacoes TEXT NULL,
    tipo_gabinete_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (tipo_gabinete_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  cliente (
    cliente_id varchar(36) NOT NULL,
    cliente_nome varchar(100) NOT NULL,
    cliente_email varchar(50) NOT NULL UNIQUE,
    cliente_telefone varchar(20) NOT NULL,
    cliente_ativo tinyint (1) NOT NULL,
    cliente_usuarios int NOT NULL DEFAULT 1,  
    cliente_gabinete_nome varchar(36) NOT NULL UNIQUE,
    cliente_gabinete_estado varchar(2) NOT NULL,
    cliente_gabinete_tipo varchar(36) NOT NULL,
    cliente_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cliente_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (cliente_id),
    CONSTRAINT fk_gabinete_tipo FOREIGN KEY (cliente_gabinete_tipo) REFERENCES tipo_gabinete (tipo_gabinete_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  usuario_tipo (
    usuario_tipo_id varchar(36) NOT NULL,
    usuario_tipo_nome varchar(255) NOT NULL,
    usuario_tipo_descricao varchar(255) NOT NULL,
    PRIMARY KEY (usuario_tipo_id, usuario_tipo_nome)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  usuario (
    usuario_id varchar(36) NOT NULL,
    usuario_cliente varchar(36) NOT NULL,
    usuario_nome varchar(255) NOT NULL,
    usuario_email varchar(255) NOT NULL,
    usuario_aniversario DATE DEFAULT NULL,
    usuario_telefone varchar(20) NOT NULL,
    usuario_senha varchar(255) NOT NULL,
    usuario_tipo varchar(36) NOT NULL,
    usuario_ativo tinyint NOT NULL,
    usuario_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id),
    CONSTRAINT fk_usuario_cliente FOREIGN KEY (usuario_cliente) REFERENCES cliente (cliente_id),
    CONSTRAINT fk_usuario_tipo FOREIGN KEY (usuario_tipo) REFERENCES usuario_tipo (usuario_tipo_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE 
  usuario_log (
    log_id INT AUTO_INCREMENT,
    log_usuario VARCHAR(36),
    log_data TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (log_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO tipo_gabinete (tipo_gabinete_id, tipo_gabinete_nome, tipo_gabinete_informacoes)
  VALUES
    (1, 'Outro', 'Gabinete administrativo'),
    (2, 'Deputado Federal', 'Gabinete destinado a um deputado federal no Congresso Nacional'),
    (3, 'Deputado Estadual', 'Gabinete destinado a um deputado estadual nas assembleias estaduais'),
    (4, 'Vereador', 'Gabinete destinado a um vereador nas câmaras municipais'),
    (5, 'Prefeito', 'Gabinete destinado ao prefeito de um município'),
    (6, 'Governador', 'Gabinete destinado ao governador de um estado'),
    (7, 'Senador', 'Gabinete destinado a um senador no Senado Federal');  

INSERT INTO cliente (cliente_id, cliente_nome, cliente_email, cliente_telefone, cliente_ativo, cliente_usuarios, cliente_gabinete_nome, cliente_gabinete_estado, cliente_gabinete_tipo) 
  VALUES (1,'CLIENTE SISTEMA', 'CLIENTE@CLIENTE.COM', '000000', 1, 1, 'GABINETE SISTEMA', 'DF', 1);

INSERT INTO usuario_tipo (usuario_tipo_id, usuario_tipo_nome, usuario_tipo_descricao) 
  VALUES 
    (1, 'Administrador', 'Usuario root do sistema'),
    (2, 'Administrativo', 'Usuario administrativo'),
    (3, 'Comunicação', 'Usuario da assessoria de comunicação'),
    (4, 'Legislativo', 'Usuario da assessoria legislativa'),
    (5, 'Orçamento', 'Usuario da assessoria orçamentária'),
    (6, 'Secretaria', 'Usuario da secretaria do gabinete');  

INSERT INTO usuario (usuario_id, usuario_cliente, usuario_nome, usuario_email, usuario_aniversario, usuario_telefone, usuario_senha, usuario_tipo, usuario_ativo) 
  VALUES (1, 1, 'USUÁRIO SISTEMA', 'USUARIO@SISTEMA.COM', '2000-01-01', '55555555', '123456789', 1, 1);

CREATE VIEW view_cliente AS SELECT cliente.*, tipo_gabinete.tipo_gabinete_nome FROM cliente INNER JOIN tipo_gabinete ON cliente.cliente_gabinete_tipo = tipo_gabinete.tipo_gabinete_id;
CREATE VIEW view_usuario AS SELECT usuario.*, cliente.cliente_nome, cliente.cliente_gabinete_estado, usuario_tipo.usuario_tipo_nome FROM usuario INNER JOIN cliente ON usuario.usuario_cliente = cliente.cliente_id INNER JOIN usuario_tipo ON usuario.usuario_tipo = usuario_tipo.usuario_tipo_id;   

