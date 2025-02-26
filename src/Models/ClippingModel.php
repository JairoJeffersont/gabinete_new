<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class ClippingModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }


    

    // CRIAR TIPO DE CLIPPING
    public function criarClippingTipo($dados) {
        $query = 'INSERT INTO clipping_tipos (clipping_tipo_id, clipping_tipo_nome, clipping_tipo_descricao, clipping_tipo_criado_por, clipping_tipo_gabinete) 
                  VALUES (UUID(), :clipping_tipo_nome, :clipping_tipo_descricao, :clipping_tipo_criado_por, :clipping_tipo_gabinete)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':clipping_tipo_nome', $dados['clipping_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':clipping_tipo_descricao', $dados['clipping_tipo_descricao'], PDO::PARAM_STR);
        $stmt->bindValue(':clipping_tipo_criado_por', $dados['clipping_tipo_criado_por'], PDO::PARAM_STR);
        $stmt->bindValue(':clipping_tipo_gabinete', $dados['clipping_tipo_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // LISTAR TIPOS DE CLIPPING
    public function listarClippingTipos($clipping_tipo_gabinete) {
        $query = "SELECT * FROM clipping_tipos WHERE clipping_tipo_gabinete = :clipping_tipo_gabinete OR clipping_tipo_gabinete = 1 ORDER BY clipping_tipo_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':clipping_tipo_gabinete', $clipping_tipo_gabinete, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // BUSCAR TIPO DE CLIPPING PELO ID
    public function buscaClippingTipo($id) {
        $query = "SELECT * FROM clipping_tipos WHERE clipping_tipo_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // ATUALIZAR TIPO DE CLIPPING
    public function atualizarClippingTipo($dados) {
        $query = 'UPDATE clipping_tipos SET clipping_tipo_nome = :clipping_tipo_nome, clipping_tipo_descricao = :clipping_tipo_descricao WHERE clipping_tipo_id = :clipping_tipo_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':clipping_tipo_id', $dados['clipping_tipo_id'], PDO::PARAM_STR);
        $stmt->bindValue(':clipping_tipo_nome', $dados['clipping_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':clipping_tipo_descricao', $dados['clipping_tipo_descricao'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // APAGAR TIPO DE CLIPPING
    public function apagarClippingTipo($clipping_tipo_id) {
        $query = 'DELETE FROM clipping_tipos WHERE clipping_tipo_id = :clipping_tipo_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':clipping_tipo_id', $clipping_tipo_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
