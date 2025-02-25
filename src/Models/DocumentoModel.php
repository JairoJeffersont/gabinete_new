<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class DocumentoModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // CRIAR DOCUMENTO
    public function criarDocumento($dados) {
        $query = 'INSERT INTO documentos(documento_id, documento_titulo, documento_resumo, documento_arquivo, documento_ano, documento_tipo, documento_orgao, documento_criado_por, documento_gabinete) 
              VALUES (UUID(), :documento_titulo, :documento_resumo, :documento_arquivo, :documento_ano, :documento_tipo, :documento_orgao, :documento_criado_por, :documento_gabinete);';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':documento_titulo', $dados['documento_titulo'], PDO::PARAM_STR);
        $stmt->bindValue(':documento_resumo', $dados['documento_resumo'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':documento_arquivo', $dados['documento_arquivo'], PDO::PARAM_STR);
        $stmt->bindValue(':documento_ano', $dados['documento_ano'], PDO::PARAM_INT);
        $stmt->bindValue(':documento_tipo', $dados['documento_tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':documento_orgao', $dados['documento_orgao'], PDO::PARAM_STR);
        $stmt->bindValue(':documento_criado_por', $dados['documento_criado_por'], PDO::PARAM_STR);
        $stmt->bindValue(':documento_gabinete', $dados['documento_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // ATUALIZAR DOCUMENTO
    public function atualizarDocumento($dados) {
        $query = 'UPDATE documentos SET ';

        $campos = [];

        foreach ($dados as $campo => $valor) {
            if ($campo !== 'documento_id') {
                $campos[] = "$campo = :$campo";
            }
        }

        $query .= implode(', ', $campos);
        $query .= ' WHERE documento_id = :documento_id';
        $stmt = $this->conn->prepare($query);

        foreach ($dados as $campo => $valor) {
            if ($campo === 'documento_ano') {
                $stmt->bindValue(":$campo", $valor, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(":$campo", $valor, PDO::PARAM_STR);
            }
        }

        return $stmt->execute();
    }

    // LISTAR DOCUMENTOS
    public function listarDocumentos($ano, $tipo, $busca, $gabinete) {


        if (empty($busca) && empty($tipo)) {
            $query = 'SELECT * FROM view_documentos WHERE documento_ano = :ano AND documento_gabinete = :gabinete ORDER BY documento_titulo DESC';
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':ano', $ano, PDO::PARAM_STR);

            $stmt->bindValue(':gabinete', $gabinete, PDO::PARAM_STR);
        } else if (empty($busca) && !empty($tipo)) {
            $query = 'SELECT * FROM view_documentos WHERE documento_ano = :ano AND documento_tipo = :tipo AND documento_gabinete = :gabinete ORDER BY documento_titulo DESC';
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':ano', $ano, PDO::PARAM_STR);
            $stmt->bindValue(':gabinete', $gabinete, PDO::PARAM_STR);
            $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        } else if (!empty($busca)) {
            $query = 'SELECT * FROM view_documentos WHERE documento_titulo LIKE :busca OR documento_resumo LIKE :busca AND documento_gabinete = :gabinete ORDER BY documento_titulo DESC';
            $stmt = $this->conn->prepare($query);
            $busca = '%' . $busca . '%';
            $stmt->bindValue(':busca', $busca, PDO::PARAM_STR);
            $stmt->bindValue(':gabinete', $gabinete, PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // BUSCAR DOCUMENTO POR COLUNA E VALOR
    public function buscaDocumento($coluna, $valor) {
        $query = "SELECT * FROM view_documentos WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // APAGAR DOCUMENTO
    public function apagarDocumento($documento_id) {
        $query = 'DELETE FROM documentos WHERE documento_id = :documento_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':documento_id', $documento_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    // BUSCAR LOGS DE DOCUMENTO
    public function buscaLog($id) {
        $query = "SELECT * FROM documento_log WHERE documento_id = :id ORDER BY log_data DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }



    // CRIAR DOCUMENTO TIPO
    public function criarDocumentoTipo($dados) {
        $query = 'INSERT INTO documentos_tipos (documento_tipo_id, documento_tipo_nome, documento_tipo_descricao, documento_tipo_criado_por, documento_tipo_gabinete) 
                  VALUES (UUID(), :documento_tipo_nome, :documento_tipo_descricao, :documento_tipo_criado_por, :documento_tipo_gabinete)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':documento_tipo_nome', $dados['documento_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':documento_tipo_descricao', $dados['documento_tipo_descricao'], PDO::PARAM_STR);
        $stmt->bindValue(':documento_tipo_criado_por', $dados['documento_tipo_criado_por'], PDO::PARAM_STR);
        $stmt->bindValue(':documento_tipo_gabinete', $dados['documento_tipo_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // ATUALIZAR DOCUMENTO TIPO
    public function atualizarDocumentoTipo($dados) {
        $query = 'UPDATE documentos_tipos 
                  SET documento_tipo_nome = :documento_tipo_nome, documento_tipo_descricao = :documento_tipo_descricao
                  WHERE documento_tipo_id = :documento_tipo_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':documento_tipo_id', $dados['documento_tipo_id'], PDO::PARAM_STR);
        $stmt->bindValue(':documento_tipo_nome', $dados['documento_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':documento_tipo_descricao', $dados['documento_tipo_descricao'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // LISTAR DOCUMENTOS TIPO
    public function listarDocumentoTipo($gabinete) {
        $query = "SELECT * FROM documentos_tipos WHERE documento_tipo_gabinete = :documento_tipo_gabinete OR documento_tipo_gabinete = 1 ORDER BY documento_tipo_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':documento_tipo_gabinete', $gabinete, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // BUSCAR DOCUMENTO TIPO POR ID
    public function buscaDocumentoTipo($id) {
        $query = "SELECT * FROM documentos_tipos WHERE documento_tipo_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // APAGAR DOCUMENTO TIPO
    public function apagarDocumentoTipo($documento_tipo_id) {
        $query = 'DELETE FROM documentos_tipos WHERE documento_tipo_id = :documento_tipo_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':documento_tipo_id', $documento_tipo_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
