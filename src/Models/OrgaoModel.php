<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class OrgaoModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }


    // CRIAR ÓRGÃO
    public function criarOrgao($dados) {
        $query = 'INSERT INTO orgaos(orgao_id, orgao_nome, orgao_email, orgao_telefone, orgao_endereco, orgao_bairro, orgao_municipio, orgao_estado, orgao_cep, orgao_tipo, orgao_informacoes, orgao_site, orgao_criado_por, orgao_gabinete) VALUES (UUID(), :orgao_nome, :orgao_email, :orgao_telefone, :orgao_endereco, :orgao_bairro, :orgao_municipio, :orgao_estado, :orgao_cep, :orgao_tipo, :orgao_informacoes, :orgao_site, :orgao_criado_por, :orgao_gabinete);';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':orgao_nome', $dados['orgao_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_email', $dados['orgao_email'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_telefone', $dados['orgao_telefone'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_endereco', $dados['orgao_endereco'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_bairro', $dados['orgao_bairro'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_municipio', $dados['orgao_municipio'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_estado', $dados['orgao_estado'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_cep', $dados['orgao_cep'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':orgao_tipo', $dados['orgao_tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_informacoes', $dados['orgao_informacoes'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':orgao_site', $dados['orgao_site'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':orgao_criado_por', $dados['orgao_criado_por'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_gabinete', $dados['orgao_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // ATUALIZAR ÓRGÃO
    public function atualizarOrgao($dados) {
        $query = 'UPDATE orgaos SET ';

        $campos = [];

        foreach ($dados as $campo => $valor) {
            if ($campo !== 'orgao_id') {
                $campos[] = "$campo = :$campo";
            }
        }

        $query .= implode(', ', $campos);
        $query .= ' WHERE orgao_id = :orgao_id';
        $stmt = $this->conn->prepare($query);

        foreach ($dados as $campo => $valor) {
            $stmt->bindValue(":$campo", $valor, PDO::PARAM_STR);
        }

        return $stmt->execute();
    }

    // LISTAR ÓRGÃOS
    public function listar($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $gabinete) {
        $pagina = (int)$pagina;
        $itens = (int)$itens;
        $offset = ($pagina - 1) * $itens;

        if ($termo === null) {
            if ($estado != null) {
                $query = "SELECT view_orgaos.*, (SELECT COUNT(*) FROM orgaos WHERE orgao_id <> 1 AND orgao_estado = '" . $estado . "' AND orgao_gabinete = :gabinete) AS total FROM view_orgaos WHERE orgao_id <> 1 AND orgao_estado = '" . $estado . "'  AND orgao_gabinete = :gabinete ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
            } else {
                $query = "SELECT view_orgaos.*, (SELECT COUNT(*) FROM orgaos WHERE orgao_id <> 1) AS total FROM view_orgaos WHERE orgao_id <> 1  AND orgao_gabinete = :gabinete ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
            }
        } else {
            if ($estado != null) {
                $query = "SELECT view_orgaos.*, (SELECT COUNT(*) FROM orgaos WHERE orgao_id <> 1 AND orgao_nome LIKE :termo AND orgao_estado = '" . $estado . "'  AND orgao_gabinete = :gabinete) AS total FROM view_orgaos WHERE orgao_id <> 1 AND orgao_nome LIKE :termo AND orgao_estado = '" . $estado . "'  AND orgao_gabinete = :gabinete ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
                $termo = '%' . $termo . '%';
            } else {
                $query = "SELECT view_orgaos.*, (SELECT COUNT(*) FROM orgaos WHERE orgao_id <> 1 AND orgao_nome LIKE :termo  AND orgao_gabinete= :gabinete) AS total FROM view_orgaos WHERE orgao_id <> 1 AND orgao_nome LIKE :termo AND orgao_gabinete = :gabinete ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
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

    // BUSCAR ÓRGÃO
    public function buscaOrgao($coluna, $valor) {
        $query = "SELECT * FROM orgaos WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // APAGAR ÓRGÃO
    public function apagarOrgao($orgao_id) {
        $query = 'DELETE FROM orgaos WHERE orgao_id = :orgao_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':orgao_id', $orgao_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    // CRIAR TIPO DE ÓRGÃO
    public function criarTipoOrgao($dados) {
        $query = 'INSERT INTO orgaos_tipos(orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao) 
                  VALUES (UUID(), :orgao_tipo_nome, :orgao_tipo_descricao)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':orgao_tipo_nome', $dados['orgao_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_tipo_descricao', $dados['orgao_tipo_descricao'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // LISTAR TIPOS DE ÓRGÃO
    public function listarTiposOrgao() {
        $query = "SELECT * FROM orgaos_tipos ORDER BY orgao_tipo_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // BUSCAR TIPO DE ÓRGÃO
    public function buscaTipoOrgao($id) {
        $query = "SELECT * FROM orgaos_tipos WHERE orgao_tipo_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // APAGAR TIPO DE ÓRGÃO
    public function apagarTipoOrgao($orgao_tipo_id) {
        $query = 'DELETE FROM orgaos_tipos WHERE orgao_tipo_id = :orgao_tipo_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':orgao_tipo_id', $orgao_tipo_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    // CRIAR TIPO DE ÓRGÃO
    public function criarOrgaoTipo($dados) {
        $query = 'INSERT INTO orgaos_tipos (orgao_tipo_id, orgao_tipo_nome, orgao_tipo_descricao, orgao_tipo_criado_por, orgao_tipo_gabinete) VALUES (UUID(), :orgao_tipo_nome, :orgao_tipo_descricao, :orgao_tipo_criado_por, :orgao_tipo_gabinete)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':orgao_tipo_nome', $dados['orgao_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_tipo_descricao', $dados['orgao_tipo_descricao'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_tipo_criado_por', $dados['orgao_tipo_criado_por'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_tipo_gabinete', $dados['orgao_tipo_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // ATUALIZAR TIPO DE ÓRGÃO
    public function atualizarOrgaoTipo($dados) {
        $query = 'UPDATE orgaos_tipos SET orgao_tipo_nome = :orgao_tipo_nome, orgao_tipo_descricao = :orgao_tipo_descricao, orgao_tipo_gabinete = :orgao_tipo_gabinete WHERE orgao_tipo_id = :orgao_tipo_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':orgao_tipo_id', $dados['orgao_tipo_id'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_tipo_nome', $dados['orgao_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_tipo_descricao', $dados['orgao_tipo_descricao'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_tipo_gabinete', $dados['orgao_tipo_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // LISTAR TIPOS DE ÓRGÃOS
    public function listarOrgaosTipos($orgao_tipo_gabinete) {
        $query = "SELECT * FROM orgaos_tipos WHERE orgao_tipo_gabinete = :orgao_tipo_gabinete OR orgao_tipo_gabinete = 1 ORDER BY orgao_tipo_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':orgao_tipo_gabinete', $orgao_tipo_gabinete, PDO::PARAM_STR);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // BUSCAR TIPO DE ÓRGÃO PELO ID
    public function buscaOrgaoTipo($id) {
        $query = "SELECT * FROM orgaos_tipos WHERE orgao_tipo_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // APAGAR TIPO DE ÓRGÃO
    public function apagarOrgaoTipo($orgao_tipo_id) {
        $query = 'DELETE FROM orgaos_tipos WHERE orgao_tipo_id = :orgao_tipo_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':orgao_tipo_id', $orgao_tipo_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
