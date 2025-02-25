<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class PessoaModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // PESSOAS
    public function criarPessoa($dados) {
        $query = "INSERT INTO pessoas (pessoa_id, pessoa_nome, pessoa_aniversario, pessoa_email, pessoa_telefone, pessoa_endereco, pessoa_bairro, pessoa_municipio, pessoa_estado, pessoa_cep, pessoa_sexo, pessoa_facebook, pessoa_instagram, pessoa_x, pessoa_informacoes, pessoa_profissao, pessoa_cargo, pessoa_tipo, pessoa_orgao, pessoa_foto, pessoa_criada_por, pessoa_gabinete)
                  VALUES (UUID(), :pessoa_nome, :pessoa_aniversario, :pessoa_email, :pessoa_telefone, :pessoa_endereco, :pessoa_bairro, :pessoa_municipio, :pessoa_estado, :pessoa_cep, :pessoa_sexo, :pessoa_facebook, :pessoa_instagram, :pessoa_x, :pessoa_informacoes, :pessoa_profissao, :pessoa_cargo, :pessoa_tipo, :pessoa_orgao, :pessoa_foto, :pessoa_criada_por, :pessoa_gabinete)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':pessoa_nome', $dados['pessoa_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_aniversario', $dados['pessoa_aniversario'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_email', $dados['pessoa_email'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_telefone', $dados['pessoa_telefone'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_endereco', $dados['pessoa_endereco'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_bairro', $dados['pessoa_bairro'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_municipio', $dados['pessoa_municipio'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_estado', $dados['pessoa_estado'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_cep', $dados['pessoa_cep'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_sexo', $dados['pessoa_sexo'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_facebook', $dados['pessoa_facebook'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_instagram', $dados['pessoa_instagram'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_x', $dados['pessoa_x'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_informacoes', $dados['pessoa_informacoes'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_profissao', $dados['pessoa_profissao'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_cargo', $dados['pessoa_cargo'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_tipo', $dados['pessoa_tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_orgao', $dados['pessoa_orgao'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_foto', $dados['pessoa_foto'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_criada_por', $dados['pessoa_criada_por'], PDO::PARAM_STR);
        $stmt->bindParam(':pessoa_gabinete', $dados['pessoa_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function atualizarPessoa($dados) {
        $query = 'UPDATE pessoas SET ';
        $campos = [];
        foreach ($dados as $campo => $valor) {
            if ($campo !== 'pessoa_id') {
                $campos[] = "$campo = :$campo";
            }
        }
        $query .= implode(', ', $campos);
        $query .= ' WHERE pessoa_id = :pessoa_id';

        $stmt = $this->conn->prepare($query);
        foreach ($dados as $campo => $valor) {
            $stmt->bindValue(":" . $campo, $valor ?? null, PDO::PARAM_STR);
        }

        return $stmt->execute();
    }

    public function listar($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $gabinete) {
        $pagina = (int)$pagina;
        $itens = (int)$itens;
        $offset = ($pagina - 1) * $itens;

        if ($termo === null) {
            if ($estado != null) {
                $query = "SELECT view_pessoas.*, 
                                 (SELECT COUNT(*) FROM pessoas WHERE pessoa_estado = '" . $estado . "' AND pessoa_gabinete = :gabinete) AS total
                          FROM view_pessoas
                          WHERE pessoa_estado = '" . $estado . "' AND pessoa_gabinete = :gabinete
                          ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
            } else {
                $query = "SELECT view_pessoas.*, 
                                 (SELECT COUNT(*) FROM pessoas WHERE pessoa_gabinete = :gabinete) AS total
                          FROM view_pessoas
                          WHERE pessoa_gabinete = :gabinete
                          ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
            }
        } else {
            if ($estado != null) {
                $query = "SELECT view_pessoas.*, 
                                 (SELECT COUNT(*) FROM pessoas WHERE pessoa_nome LIKE :termo AND pessoa_estado = '" . $estado . "' AND pessoa_gabinete = :gabinete) AS total
                          FROM view_pessoas
                          WHERE pessoa_nome LIKE :termo AND pessoa_estado = '" . $estado . "' AND pessoa_gabinete = :gabinete
                          ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
                $termo = '%' . $termo . '%';
            } else {
                $query = "SELECT view_pessoas.*, 
                                 (SELECT COUNT(*) FROM pessoas WHERE pessoa_nome LIKE :termo AND pessoa_gabinete = :gabinete) AS total
                          FROM view_pessoas
                          WHERE pessoa_nome LIKE :termo AND pessoa_gabinete = :gabinete
                          ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
                $termo = '%' . $termo . '%';
            }
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);
        $stmt->bindValue(':gabinete', $gabinete, PDO::PARAM_INT);

        if ($termo !== null) {
            $stmt->bindValue(':termo', $termo, PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscaPessoa($coluna, $valor) {
        $query = "SELECT * FROM view_pessoas WHERE $coluna = :valor";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function apagarPessoa($pessoa_id) {
        $query = 'DELETE FROM pessoas WHERE pessoa_id = :pessoa_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pessoa_id', $pessoa_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    // CRIAÇÃO DE TIPO DE PESSOA
    public function criarTipoPessoa($dados) {
        $query = 'INSERT INTO pessoas_tipos (pessoa_tipo_id, pessoa_tipo_nome, pessoa_tipo_descricao, pessoa_tipo_criado_por, pessoa_tipo_gabinete) 
                  VALUES (UUID(), :pessoa_tipo_nome, :pessoa_tipo_descricao, :pessoa_tipo_criado_por, :pessoa_tipo_gabinete)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pessoa_tipo_nome', $dados['pessoa_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_tipo_descricao', $dados['pessoa_tipo_descricao'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_tipo_criado_por', $dados['pessoa_tipo_criado_por'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_tipo_gabinete', $dados['pessoa_tipo_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // ATUALIZAÇÃO DE TIPO DE PESSOA
    public function atualizarTipoPessoa($dados) {
        $query = 'UPDATE pessoas_tipos 
                  SET pessoa_tipo_nome = :pessoa_tipo_nome, 
                      pessoa_tipo_descricao = :pessoa_tipo_descricao
                  WHERE pessoa_tipo_id = :pessoa_tipo_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pessoa_tipo_id', $dados['pessoa_tipo_id'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_tipo_nome', $dados['pessoa_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoa_tipo_descricao', $dados['pessoa_tipo_descricao'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // LISTAR TIPOS DE PESSOA
    public function listarTiposPessoa($pessoa_tipo_gabinete) {
        $query = 'SELECT * FROM pessoas_tipos WHERE pessoa_tipo_gabinete = :pessoa_tipo_gabinete OR pessoa_tipo_gabinete = 1 ORDER BY pessoa_tipo_nome ASC';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pessoa_tipo_gabinete', $pessoa_tipo_gabinete, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // BUSCAR TIPO DE PESSOA POR ID
    public function buscaTipoPessoa($id) {
        $query = 'SELECT * FROM pessoas_tipos WHERE pessoa_tipo_id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // APAGAR TIPO DE PESSOA
    public function apagarTipoPessoa($pessoa_tipo_id) {
        $query = 'DELETE FROM pessoas_tipos WHERE pessoa_tipo_id = :pessoa_tipo_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pessoa_tipo_id', $pessoa_tipo_id, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // CRIAÇÃO DE PROFISSÃO DE PESSOA
    public function criarProfissaoPessoa($dados) {
        $query = 'INSERT INTO pessoas_profissoes (pessoas_profissoes_id, pessoas_profissoes_nome, pessoas_profissoes_descricao, pessoas_profissoes_criado_por, pessoas_profissoes_gabinete) 
              VALUES (UUID(), :pessoas_profissoes_nome, :pessoas_profissoes_descricao, :pessoas_profissoes_criado_por, :pessoas_profissoes_gabinete)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pessoas_profissoes_nome', $dados['pessoas_profissoes_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoas_profissoes_descricao', $dados['pessoas_profissoes_descricao'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoas_profissoes_criado_por', $dados['pessoas_profissoes_criado_por'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoas_profissoes_gabinete', $dados['pessoas_profissoes_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // ATUALIZAÇÃO DE PROFISSÃO DE PESSOA
    public function atualizarProfissaoPessoa($dados) {
        $query = 'UPDATE pessoas_profissoes 
              SET pessoas_profissoes_nome = :pessoas_profissoes_nome, 
                  pessoas_profissoes_descricao = :pessoas_profissoes_descricao
              WHERE pessoas_profissoes_id = :pessoas_profissoes_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pessoas_profissoes_id', $dados['pessoas_profissoes_id'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoas_profissoes_nome', $dados['pessoas_profissoes_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':pessoas_profissoes_descricao', $dados['pessoas_profissoes_descricao'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // LISTAR PROFISSÕES DE PESSOA
    public function listarProfissoesPessoa($pessoas_profissoes_gabinete) {
        $query = 'SELECT * FROM pessoas_profissoes WHERE pessoas_profissoes_gabinete = :pessoas_profissoes_gabinete OR pessoas_profissoes_gabinete = 1 ORDER BY pessoas_profissoes_nome ASC';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pessoas_profissoes_gabinete', $pessoas_profissoes_gabinete, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // BUSCAR PROFISSÃO DE PESSOA POR ID
    public function buscaProfissaoPessoa($id) {
        $query = 'SELECT * FROM pessoas_profissoes WHERE pessoas_profissoes_id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // APAGAR PROFISSÃO DE PESSOA
    public function apagarProfissaoPessoa($pessoas_profissoes_id) {
        $query = 'DELETE FROM pessoas_profissoes WHERE pessoas_profissoes_id = :pessoas_profissoes_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pessoas_profissoes_id', $pessoas_profissoes_id, PDO::PARAM_STR);
        return $stmt->execute();
    }


    public function buscarAniversarianteMes($mes, $estado, $gabinete) {


        if (empty($estado)) {
            $query = "SELECT * FROM view_pessoas 
                      WHERE MONTH(pessoa_aniversario) = :mes 
                      AND pessoa_gabinete = :pessoa_gabinete 
                      ORDER BY DAY(pessoa_aniversario) ASC;";
        } else {
            $query = "SELECT * FROM view_pessoas 
                      WHERE MONTH(pessoa_aniversario) = :mes 
                      AND pessoa_gabinete = :pessoa_gabinete 
                      AND pessoa_estado = :estado 
                      ORDER BY DAY(pessoa_aniversario) ASC;";
        }


        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pessoa_gabinete', $gabinete, PDO::PARAM_STR);
        $stmt->bindParam(':mes', $mes, PDO::PARAM_INT);

        if (!empty($estado)) {
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        }


        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
