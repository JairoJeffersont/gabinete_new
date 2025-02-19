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
    PRIMARY KEY (usuario_tipo_id, usuario_tipo_nome)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

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

CREATE TABLE
  orgao_tipo(
    orgao_tipo_id varchar(36) NOT NULL,
    orgao_tipo_nome varchar(255) NOT NULL UNIQUE,
    orgao_tipo_descricao varchar(255) NOT NULL,
    orgao_tipo_criado_por varchar(36),
    orgao_tipo_gabinete varchar(36),
    orgao_tipo_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    orgao_tipo_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY(orgao_tipo_id, orgao_tipo_nome),
    CONSTRAINT fk_orgao_tipo_gabinete FOREIGN KEY (orgao_tipo_gabinete) REFERENCES gabinete (gabinete_id),
    CONSTRAINT fk_orgao_tipo_criado_por FOREIGN KEY (orgao_tipo_criado_por) REFERENCES usuario (usuario_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci; 

CREATE TABLE
  orgao(
    orgao_id varchar(36) NOT NULL,
    orgao_nome varchar(255) NOT NULL,
    orgao_email varchar(255) NOT NULL UNIQUE,
    orgao_tipo varchar(36) NOT NULL,
    orgao_gabinete varchar(36) NOT NULL,
    orgao_estado varchar(2) NOT NULL,
    orgao_municipio varchar(255) NOT NULL,
    orgao_bairro varchar(255) NULL,
    orgao_endereco varchar(255) NULL,
    orgao_telefone varchar(11) NULL,
    orgao_site varchar(255) NULL,
    orgao_instagram varchar(255) NULL,
    orgao_twitter varchar(255) NULL,
    orgao_facebook varchar(255) NULL,
    orgao_informacoes TEXT NULL,
    orgao_criado_por varchar(36),
    orgao_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    orgao_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY(orgao_id),
    CONSTRAINT fk_orgao_gabinete FOREIGN KEY (orgao_gabinete) REFERENCES gabinete (gabinete_id),
    CONSTRAINT fk_orgao_criado_por FOREIGN KEY (orgao_criado_por) REFERENCES usuario (usuario_id),
    CONSTRAINT fk_orgao_tipo FOREIGN KEY (orgao_tipo) REFERENCES orgao_tipo (orgao_tipo_id)
  )ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;   

CREATE TABLE
    pessoa_tipo(
      pessoa_tipo_id varchar(36) NOT NULL,
      pessoa_tipo_nome varchar(255) NOT NULL UNIQUE,
      pessoa_tipo_descricao varchar(255) NOT NULL,
      pessoa_tipo_criado_por varchar(36),
      pessoa_tipo_gabinete varchar(36),
      pessoa_tipo_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      pessoa_tipo_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY(pessoa_tipo_id, pessoa_tipo_nome),
      CONSTRAINT fk_pessoa_tipo_gabinete FOREIGN KEY (pessoa_tipo_gabinete) REFERENCES gabinete (gabinete_id),
      CONSTRAINT fk_pessoa_tipo_criado_por FOREIGN KEY (pessoa_tipo_criado_por) REFERENCES usuario (usuario_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci; 

CREATE TABLE
    pessoa_genero(
      pessoa_genero_id varchar(36) NOT NULL,
      pessoa_genero_nome varchar(255) NOT NULL UNIQUE,      
      pessoa_genero_criado_por varchar(36),
      pessoa_genero_gabinete varchar(36),
      pessoa_genero_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      pessoa_genero_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY(pessoa_genero_id, pessoa_genero_nome),
      CONSTRAINT fk_pessoa_genero_gabinete FOREIGN KEY (pessoa_genero_gabinete) REFERENCES gabinete (gabinete_id),
      CONSTRAINT fk_pessoa_genero_criado_por FOREIGN KEY (pessoa_genero_criado_por) REFERENCES usuario (usuario_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci; 

CREATE TABLE
    pessoa_profissao(
      pessoa_profissao_id varchar(36) NOT NULL,
      pessoa_profissao_nome varchar(255) NOT NULL UNIQUE,      
      pessoa_profissao_criado_por varchar(36),
      pessoa_profissao_gabinete varchar(36),
      pessoa_profissao_criada_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      pessoa_profissao_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY(pessoa_profissao_id, pessoa_profissao_nome),
      CONSTRAINT fk_pessoa_profissao_gabinete FOREIGN KEY (pessoa_profissao_gabinete) REFERENCES gabinete (gabinete_id),
      CONSTRAINT fk_pessoa_profissao_criada_por FOREIGN KEY (pessoa_profissao_criado_por) REFERENCES usuario (usuario_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci; 

CREATE TABLE
  pessoa(
    pessoa_id varchar(36) NOT NULL,
    pessoa_nome varchar(255) NOT NULL,
    pessoa_email varchar(255) NOT NULL UNIQUE,
    pessoa_tipo varchar(36) NOT NULL,
    pessoa_profissao varchar(36) NOT NULL,
    pessoa_genero varchar(36) NOT NULL,
    pessoa_orgao varchar(36) NOT NULL,
    pessoa_cargo varchar(255) NULL,
    pessoa_gabinete varchar(36) NOT NULL,
    pessoa_estado varchar(2) NOT NULL,
    pessoa_municipio varchar(255) NOT NULL,
    pessoa_bairro varchar(255) NULL,
    pessoa_endereco varchar(255) NULL,
    pessoa_telefone varchar(11) NULL,
    pessoa_instagram varchar(255) NULL,
    pessoa_twitter varchar(255) NULL,
    pessoa_facebook varchar(255) NULL,
    pessoa_foto varchar(255) NULL,
    pessoa_informacoes TEXT NULL,
    pessoa_criada_por varchar(36),
    pessoa_criada_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    pessoa_atualizada_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY(pessoa_id),
    CONSTRAINT fk_pessoa_gabinete FOREIGN KEY (pessoa_gabinete) REFERENCES gabinete (gabinete_id),
    CONSTRAINT fk_pessoa_criada_por FOREIGN KEY (pessoa_criada_por) REFERENCES usuario (usuario_id),
    CONSTRAINT fk_pessoa_tipo FOREIGN KEY (pessoa_tipo) REFERENCES pessoa_tipo (pessoa_tipo_id),
    CONSTRAINT fk_pessoa_genero FOREIGN KEY (pessoa_genero) REFERENCES pessoa_genero (pessoa_genero_id),
    CONSTRAINT fk_pessoa_profissao FOREIGN KEY (pessoa_profissao) REFERENCES pessoa_profissao (pessoa_profissao_id),
    CONSTRAINT fk_pessoa_orgao FOREIGN KEY (pessoa_orgao) REFERENCES orgao (orgao_id)
  )ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;   
  
INSERT INTO tipo_gabinete (tipo_gabinete_id, tipo_gabinete_nome, tipo_gabinete_informacoes)
  VALUES
    (1, 'Deputado Federal', 'Gabinete destinado a um deputado federal no Congresso Nacional'),
    (2, 'Deputado Estadual', 'Gabinete destinado a um deputado estadual nas assembleias estaduais'),
    (3, 'Vereador', 'Gabinete destinado a um vereador nas câmaras municipais'),
    (4, 'Prefeito', 'Gabinete destinado ao prefeito de um município'),
    (5, 'Governador', 'Gabinete destinado ao governador de um estado'),
    (6, 'Senador', 'Gabinete destinado a um senador no Senado Federal');

INSERT INTO usuario_tipo (usuario_tipo_id, usuario_tipo_nome, usuario_tipo_descricao) 
  VALUES 
    (1, 'Administrador', 'Usuario root do sistema'),
    (2, 'Administrativo', 'Usuario administrativo'),
    (3, 'Comunicação', 'Usuario da assessoria de comunicação'),
    (4, 'Legislativo', 'Usuario da assessoria legislativa'),
    (5, 'Orçamento', 'Usuario da assessoria orçamentária'),
    (6, 'Secretaria', 'Usuario da secretaria do gabinete');

INSERT INTO cliente (cliente_id, cliente_nome, cliente_email, cliente_telefone, cliente_ativo, cliente_endereco, cliente_cep, cliente_cpf) 
  VALUES (1,'CLIENTE SISTEMA', 'CLIENTE@CLIENTE.COM', '000000', 1, 'ENDERECO', '000000', '00000000000000');

INSERT INTO gabinete (gabinete_id, gabinete_cliente, gabinete_tipo, gabinete_politico, gabinete_estado) 
  VALUES (1, 1, 1, 'POLITICO SISTEMA', 'DF');

INSERT INTO usuario (usuario_id, usuario_gabinete, usuario_nome, usuario_email, usuario_aniversario, usuario_telefone, usuario_senha, usuario_tipo, usuario_ativo) 
  VALUES (1, 1, 'USUÁRIO SISTEMA', 'USUARIO@SISTEMA.COM', '2000-01-01', '55555555', '123456789', 1, 1);

INSERT INTO orgao_tipo (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) 
  VALUES 
    ('1', 'Sem tipo', 'Sem tipo definido', '1', '1'),
    ('2', 'Prefeitura', 'Administração municipal', '1', '1'),
    ('3', 'Secretaria de Estado', 'Órgão do governo estadual', '1', '1'),
    ('4', 'Secretaria Municipal', 'Órgão do governo municipal', '1', '1'),
    ('5', 'Câmara Municipal', 'Órgão legislativo do município', '1', '1'),
    ('6', 'Assembleia Legislativa', 'Órgão legislativo do estado', '1', '1'),
    ('7', 'Tribunal de Contas', 'Órgão fiscalizador das contas públicas', '1', '1'),
    ('8', 'Defensoria Pública', 'Órgão que presta assistência jurídica gratuita', '1', '1'),
    ('9', 'Ministério Público', 'Órgão de defesa da ordem jurídica e interesses sociais', '1', '1'),
    ('10', 'Autarquia Municipal', 'Órgão autônomo da administração pública municipal', '1', '1'),
    ('11', 'Autarquia Estadual', 'Órgão autônomo da administração pública estadual', '1', '1'),
    ('12', 'Empresa Pública', 'Empresa controlada pelo governo para serviços públicos', '1', '1'),
    ('13', 'Fundação Pública', 'Entidade criada para fins de interesse público', '1', '1');

INSERT INTO orgao (orgao_id, orgao_nome, orgao_email, orgao_tipo, orgao_gabinete, orgao_estado, orgao_municipio, orgao_criado_por) 
  VALUES (1, 'ÓRGAO SISTEMA', 'ORGAO@SISTEA.COM', 1, 1 ,'DF', 'BRASILIA', 1);

INSERT INTO pessoa_tipo (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_gabinete) 
  VALUES 
    ('1', 'Sem tipo', 'Sem tipo definido', '1', '1'), 
    ('2', 'Autoridade Pública', 'Pessoa que ocupa cargo público de destaque', '1', '1'),    
    ('3', 'Assessor', 'Pessoa que presta assessoria a uma autoridade ou órgão', '1', '1'),
    ('4', 'Político', 'Pessoa eleita para cargo público', '1', '1'),
    ('5', 'Cidadão', 'Pessoa comum sem vínculo governamental', '1', '1'),
    ('6', 'Empresário', 'Pessoa que possui ou administra um negócio', '1', '1'),
    ('7', 'Imprensa', 'Pessoa relacionada a imprensa', '1', '1'),
    ('8', 'Família', 'Parente ou membro da família de uma autoridade', '1', '1'),
    ('9', 'Amigo', 'Pessoa com relação de amizade com uma autoridade ou servidor', '1', '1'),
    ('10', 'Líder Comunitário', 'Pessoa que representa interesses de uma comunidade', '1', '1'),    
    ('11', 'Eleitor', 'Pessoa que participa do processo eleitoral', '1', '1');

INSERT INTO pessoa_profissao (pessoa_profissao_id, pessoa_profissao_nome, pessoa_profissao_criado_por, pessoa_profissao_gabinete) 
  VALUES 
    ('2', 'Médico', '1', '1'),
    ('3', 'Advogado', '1', '1'),
    ('4', 'Engenheiro', '1', '1'),
    ('5', 'Professor', '1', '1'),
    ('6', 'Jornalista', '1', '1'),
    ('7', 'Empresário', '1', '1'),
    ('8', 'Arquiteto', '1', '1'),
    ('9', 'Contador', '1', '1'),
    ('10', 'Servidor Público', '1', '1'),
    ('11', 'Policial', '1', '1'),
    ('12', 'Bombeiro', '1', '1'),
    ('13', 'Motorista', '1', '1'),
    ('14', 'Agricultor', '1', '1'),
    ('15', 'Artista', '1', '1'),
    ('16', 'Desenvolvedor de Software', '1', '1'),
    ('17', 'Eletricista', '1', '1'),
    ('18', 'Mecânico', '1', '1'),
    ('19', 'Veterinário', '1', '1'),
    ('20', 'Psicólogo', '1', '1'),
    ('21', 'Enfermeiro', '1', '1'),
    ('22', 'Cozinheiro', '1', '1'),
    ('23', 'Atendente', '1', '1'),
    ('24', 'Militar', '1', '1'),
    ('25', 'Gestor Público', '1', '1');

INSERT INTO pessoa_genero (pessoa_genero_id, pessoa_genero_nome, pessoa_genero_criado_por, pessoa_genero_gabinete) 
  VALUES 
    ('1', 'Prefere não informar', '1', '1'),
    ('2', 'Masculino', '1', '1'),
    ('3', 'Feminino', '1', '1'),
    ('4', 'Não-Binário', '1', '1'),
    ('5', 'Agênero', '1', '1'),
    ('6', 'Gênero Fluido', '1', '1'),
    ('7', 'Transgênero Masculino', '1', '1'),
    ('8', 'Transgênero Feminino', '1', '1'),
    ('9', 'Outro', '1', '1');

CREATE VIEW view_orgao_tipo AS
  SELECT 
      orgao_tipo.*, usuario.usuario_nome 
    FROM orgao_tipo INNER JOIN usuario ON orgao_tipo.orgao_tipo_criado_por = usuario.usuario_id;

CREATE VIEW view_orgao AS
  SELECT 
      orgao.*,usuario.usuario_nome
    FROM orgao INNER JOIN usuario ON orgao.orgao_criado_por = usuario.usuario_id;

CREATE VIEW view_pessoa_tipo AS
  SELECT 
      pessoa_tipo.*, usuario.usuario_nome     
    FROM pessoa_tipo INNER JOIN usuario ON pessoa_tipo.pessoa_tipo_criado_por = usuario.usuario_id;

CREATE VIEW view_pessoa_genero AS
  SELECT 
      pessoa_genero.*, usuario.usuario_nome       
    FROM pessoa_genero INNER JOIN usuario ON pessoa_genero.pessoa_genero_criado_por = usuario.usuario_id;

CREATE VIEW view_pessoa_profissao AS
  SELECT 
     pessoa_profissao.*, usuario.usuario_nome
    FROM pessoa_profissao INNER JOIN usuario ON pessoa_profissao.pessoa_profissao_criado_por = usuario.usuario_id;

CREATE VIEW view_pessoa AS
  SELECT 
     pessoa.*, usuario.usuario_nome
    FROM pessoa INNER JOIN usuario ON pessoa.pessoa_criada_por = usuario.usuario_id;