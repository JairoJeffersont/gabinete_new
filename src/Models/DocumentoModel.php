<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class DocumentoModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // CRIAR DOCUMENTO TIPO
    public function criarDocumentoTipo($dados) {
        $query = 'INSERT INTO documento_tipo (documento_tipo_id, documento_tipo_nome, documento_tipo_descricao, documento_tipo_criado_por, documento_tipo_gabinete) 
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
        $query = 'UPDATE documento_tipo 
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
        $query = "SELECT * FROM documento_tipo WHERE documento_tipo_gabinete = :documento_tipo_gabinete OR documento_tipo_gabinete = 1 ORDER BY documento_tipo_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':documento_tipo_gabinete', $gabinete, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // BUSCAR DOCUMENTO TIPO POR ID
    public function buscaDocumentoTipo($id) {
        $query = "SELECT * FROM documento_tipo WHERE documento_tipo_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // APAGAR DOCUMENTO TIPO
    public function apagarDocumentoTipo($documento_tipo_id) {
        $query = 'DELETE FROM documento_tipo WHERE documento_tipo_id = :documento_tipo_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':documento_tipo_id', $documento_tipo_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
