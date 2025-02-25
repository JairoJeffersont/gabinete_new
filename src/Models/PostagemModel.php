<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class PostagemModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // CRIAR STATUS DE POSTAGEM
    public function criarPostagemStatus($dados) {
        $query = 'INSERT INTO postagem_status(postagem_status_id, postagem_status_nome, postagem_status_descricao, postagem_status_criado_por, postagem_status_gabinete) 
                  VALUES (UUID(), :postagem_status_nome, :postagem_status_descricao, :postagem_status_criado_por, :postagem_status_gabinete)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':postagem_status_nome', $dados['postagem_status_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':postagem_status_descricao', $dados['postagem_status_descricao'], PDO::PARAM_STR);
        $stmt->bindValue(':postagem_status_criado_por', $dados['postagem_status_criado_por'], PDO::PARAM_STR);
        $stmt->bindValue(':postagem_status_gabinete', $dados['postagem_status_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // LISTAR STATUS DE POSTAGEM
    public function listarPostagemStatus() {
        $query = "SELECT * FROM postagem_status ORDER BY postagem_status_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // BUSCAR STATUS DE POSTAGEM POR ID
    public function buscaPostagemStatus($id) {
        $query = "SELECT * FROM postagem_status WHERE postagem_status_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // ATUALIZAR STATUS DE POSTAGEM
    public function atualizarPostagemStatus($dados) {
        $query = 'UPDATE postagem_status 
                  SET postagem_status_nome = :postagem_status_nome, 
                      postagem_status_descricao = :postagem_status_descricao,
                      postagem_status_gabinete = :postagem_status_gabinete
                  WHERE postagem_status_id = :postagem_status_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':postagem_status_id', $dados['postagem_status_id'], PDO::PARAM_STR);
        $stmt->bindValue(':postagem_status_nome', $dados['postagem_status_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':postagem_status_descricao', $dados['postagem_status_descricao'], PDO::PARAM_STR);
        $stmt->bindValue(':postagem_status_gabinete', $dados['postagem_status_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // APAGAR STATUS DE POSTAGEM
    public function apagarPostagemStatus($postagem_status_id) {
        $query = 'DELETE FROM postagem_status WHERE postagem_status_id = :postagem_status_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':postagem_status_id', $postagem_status_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
