<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class EmendaModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // CRIAR EMENDA STATUS
    public function criarEmendaStatus($dados) {
        $query = 'INSERT INTO emendas_status (emendas_status_id, emendas_status_nome, emendas_status_descricao, emendas_status_criado_por, emendas_status_gabinete) 
                  VALUES (UUID(), :emendas_status_nome, :emendas_status_descricao, :emendas_status_criado_por, :emendas_status_gabinete)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':emendas_status_nome', $dados['emendas_status_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':emendas_status_descricao', $dados['emendas_status_descricao'], PDO::PARAM_STR);
        $stmt->bindValue(':emendas_status_criado_por', $dados['emendas_status_criado_por'], PDO::PARAM_STR);
        $stmt->bindValue(':emendas_status_gabinete', $dados['emendas_status_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // ATUALIZAR EMENDA STATUS
    public function atualizarEmendaStatus($dados) {
        $query = 'UPDATE emendas_status 
                  SET emendas_status_nome = :emendas_status_nome, emendas_status_descricao = :emendas_status_descricao
                  WHERE emendas_status_id = :emendas_status_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':emendas_status_id', $dados['emendas_status_id'], PDO::PARAM_STR);
        $stmt->bindValue(':emendas_status_nome', $dados['emendas_status_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':emendas_status_descricao', $dados['emendas_status_descricao'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // LISTAR EMENDA STATUS
    public function listarEmendaStatus($gabinete) {
        $query = "SELECT * FROM emendas_status WHERE emendas_status_gabinete = :emendas_status_gabinete OR emendas_status_gabinete = 1 ORDER BY emendas_status_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':emendas_status_gabinete', $gabinete, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // BUSCAR EMENDA STATUS POR ID
    public function buscaEmendaStatus($id) {
        $query = "SELECT * FROM emendas_status WHERE emendas_status_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // APAGAR EMENDA STATUS
    public function apagarEmendaStatus($emendas_status_id) {
        $query = 'DELETE FROM emendas_status WHERE emendas_status_id = :emendas_status_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':emendas_status_id', $emendas_status_id, PDO::PARAM_STR);

        return $stmt->execute();
    }


    // CRIAR EMENDA OBJETIVO
    public function criarEmendaObjetivo($dados) {
        $query = 'INSERT INTO emendas_objetivos (emendas_objetivos_id, emendas_objetivos_nome, emendas_objetivos_descricao, emendas_objetivos_criado_por, emendas_objetivos_gabinete) 
                  VALUES (UUID(), :emendas_objetivos_nome, :emendas_objetivos_descricao, :emendas_objetivos_criado_por, :emendas_objetivos_gabinete)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':emendas_objetivos_nome', $dados['emendas_objetivos_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':emendas_objetivos_descricao', $dados['emendas_objetivos_descricao'], PDO::PARAM_STR);
        $stmt->bindValue(':emendas_objetivos_criado_por', $dados['emendas_objetivos_criado_por'], PDO::PARAM_STR);
        $stmt->bindValue(':emendas_objetivos_gabinete', $dados['emendas_objetivos_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // ATUALIZAR EMENDA OBJETIVO
    public function atualizarEmendaObjetivo($dados) {
        $query = 'UPDATE emendas_objetivos 
                  SET emendas_objetivos_nome = :emendas_objetivos_nome, emendas_objetivos_descricao = :emendas_objetivos_descricao
                  WHERE emendas_objetivos_id = :emendas_objetivos_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':emendas_objetivos_id', $dados['emendas_objetivos_id'], PDO::PARAM_STR);
        $stmt->bindValue(':emendas_objetivos_nome', $dados['emendas_objetivos_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':emendas_objetivos_descricao', $dados['emendas_objetivos_descricao'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // LISTAR EMENDA OBJETIVO
    public function listarEmendaObjetivo($gabinete) {
        $query = "SELECT * FROM emendas_objetivos WHERE emendas_objetivos_gabinete = :emendas_objetivos_gabinete OR emendas_objetivos_gabinete = 1 ORDER BY emendas_objetivos_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':emendas_objetivos_gabinete', $gabinete, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // BUSCAR EMENDA OBJETIVO POR ID
    public function buscaEmendaObjetivo($id) {
        $query = "SELECT * FROM emendas_objetivos WHERE emendas_objetivos_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // APAGAR EMENDA OBJETIVO
    public function apagarEmendaObjetivo($emendas_objetivos_id) {
        $query = 'DELETE FROM emendas_objetivos WHERE emendas_objetivos_id = :emendas_objetivos_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':emendas_objetivos_id', $emendas_objetivos_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
