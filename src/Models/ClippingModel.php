<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class ClippingModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }



    public function criarClipping($dados) {
        $query = "INSERT INTO clipping (clipping_id, clipping_resumo, clipping_titulo, clipping_data, clipping_link, clipping_orgao, clipping_arquivo, clipping_tipo, clipping_criado_por, clipping_gabinete)
                  VALUES (UUID(), :clipping_resumo, :clipping_titulo, :clipping_data, :clipping_link, :clipping_orgao, :clipping_arquivo, :clipping_tipo, :clipping_criado_por, :clipping_gabinete)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':clipping_resumo', $dados['clipping_resumo'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_titulo', $dados['clipping_titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_data', $dados['clipping_data'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_link', $dados['clipping_link'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_orgao', $dados['clipping_orgao'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_arquivo', $dados['clipping_arquivo'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_tipo', $dados['clipping_tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_criado_por', $dados['clipping_criado_por'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_gabinete', $dados['clipping_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function atualizarClipping($clipping_id, $dados) {
        $query = "UPDATE clipping 
                  SET clipping_resumo = :clipping_resumo, 
                      clipping_titulo = :clipping_titulo, 
                      clipping_data = :clipping_data, 
                      clipping_link = :clipping_link, 
                      clipping_orgao = :clipping_orgao, 
                      clipping_arquivo = :clipping_arquivo, 
                      clipping_tipo = :clipping_tipo
                  WHERE clipping_id = :clipping_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':clipping_resumo', $dados['clipping_resumo'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_titulo', $dados['clipping_titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_data', $dados['clipping_data'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_link', $dados['clipping_link'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_orgao', $dados['clipping_orgao'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_arquivo', $dados['clipping_arquivo'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_tipo', $dados['clipping_tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':clipping_id', $clipping_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function listarClipping($busca, $ano, $gabinete) {
        if ($busca === '') {
            $query = 'SELECT * FROM view_clipping WHERE YEAR(clipping_data) = :ano AND clipping_gabinete = :clipping_gabinete ORDER BY clipping_data DESC';
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':clipping_gabinete', $gabinete, PDO::PARAM_STR);
            $stmt->bindValue(':ano', $ano, PDO::PARAM_STR);
        } else {
            $query = 'SELECT * FROM view_clipping WHERE (clipping_titulo LIKE :busca OR clipping_resumo LIKE :busca) AND clipping_gabinete = :clipping_gabinete ORDER BY clipping_data DESC';
            $stmt = $this->conn->prepare($query);
            $busca = '%' . $busca . '%';
            $stmt->bindValue(':busca', $busca, PDO::PARAM_STR);
            $stmt->bindValue(':clipping_gabinete', $gabinete, PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarClipping($coluna, $valor) {
        $query = "SELECT * FROM view_clipping  WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function apagarClipping($clipping_id) {
        $query = "DELETE FROM clipping WHERE clipping_id = :clipping_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clipping_id', $clipping_id, PDO::PARAM_STR);

        return $stmt->execute();
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
