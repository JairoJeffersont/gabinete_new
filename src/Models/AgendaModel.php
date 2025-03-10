<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class AgendaModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // Criar um novo evento na agenda
    public function criarAgenda($dados) {
        $query = 'INSERT INTO agenda (agenda_id, agenda_titulo, agenda_situacao, agenda_tipo, agenda_data, agenda_local, agenda_estado, agenda_informacoes, agenda_criada_por, agenda_gabinete) 
                  VALUES (UUID(), :agenda_titulo, :agenda_situacao, :agenda_tipo, :agenda_data, :agenda_local, :agenda_estado, :agenda_informacoes, :agenda_criada_por, :agenda_gabinete)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':agenda_titulo', $dados['agenda_titulo'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_situacao', $dados['agenda_situacao'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_tipo', $dados['agenda_tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_data', $dados['agenda_data'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_local', $dados['agenda_local'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_estado', $dados['agenda_estado'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_informacoes', $dados['agenda_informacoes'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_criada_por', $dados['agenda_criada_por'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_gabinete', $dados['agenda_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // Atualizar um evento da agenda
    public function atualizarAgenda($dados) {
        $query = 'UPDATE agenda SET agenda_titulo = :agenda_titulo, agenda_situacao = :agenda_situacao, agenda_tipo = :agenda_tipo, agenda_data = :agenda_data, agenda_local = :agenda_local, agenda_estado = :agenda_estado, agenda_informacoes = :agenda_informacoes WHERE agenda_id = :agenda_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':agenda_id', $dados['agenda_id'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_titulo', $dados['agenda_titulo'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_situacao', $dados['agenda_situacao'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_tipo', $dados['agenda_tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_data', $dados['agenda_data'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_local', $dados['agenda_local'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_estado', $dados['agenda_estado'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_informacoes', $dados['agenda_informacoes'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // Listar eventos da agenda
    public function listarAgendas($data, $tipo, $situacao, $gabinete) {
        // Inicia a consulta básica com filtro pela data e cliente
        $query = "SELECT * FROM view_agenda WHERE agenda_gabinete = :gabinete AND DATE(agenda_data) = :data";

        // Adiciona a condição para tipo, se fornecido
        if (!empty($tipo)) {
            $query .= " AND agenda_tipo = :tipo";
        }

        // Adiciona a condição para situacao, se fornecido
        if (!empty($situacao)) {
            $query .= " AND agenda_situacao = :situacao";
        }

        // Ordena os resultados
        $query .= " ORDER BY agenda_data ASC";

        // Prepara a consulta
        $stmt = $this->conn->prepare($query);

        // Vincula os parâmetros obrigatórios
        $stmt->bindParam(':gabinete', $gabinete, PDO::PARAM_STR);
        $stmt->bindParam(':data', $data, PDO::PARAM_STR);

        // Vincula os parâmetros opcionais (tipo e situacao) se forem fornecidos
        if (!empty($tipo)) {
            $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        }
        if (!empty($situacao)) {
            $stmt->bindParam(':situacao', $situacao, PDO::PARAM_STR);
        }

        // Executa a consulta
        $stmt->execute();

        // Retorna o resultado
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar evento da agenda por ID
    public function buscaAgenda($id) {
        $query = 'SELECT * FROM agenda WHERE agenda_id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Apagar evento da agenda
    public function apagarAgenda($id) {
        $query = 'DELETE FROM agenda WHERE agenda_id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Criar Tipo de Agenda
    public function criarTipoAgenda($dados) {
        $query = 'INSERT INTO agenda_tipo (agenda_tipo_id, agenda_tipo_nome, agenda_tipo_descricao, agenda_tipo_criado_por, agenda_tipo_gabinete) 
                  VALUES (UUID(), :agenda_tipo_nome, :agenda_tipo_descricao, :agenda_tipo_criado_por, :agenda_tipo_gabinete)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':agenda_tipo_nome', $dados['agenda_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_tipo_descricao', $dados['agenda_tipo_descricao'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_tipo_criado_por', $dados['agenda_tipo_criado_por'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_tipo_gabinete', $dados['agenda_tipo_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // Atualizar Tipo de Agenda
    public function atualizarTipoAgenda($dados) {
        $query = 'UPDATE agenda_tipo 
                  SET agenda_tipo_nome = :agenda_tipo_nome, 
                      agenda_tipo_descricao = :agenda_tipo_descricao
                  WHERE agenda_tipo_id = :agenda_tipo_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':agenda_tipo_id', $dados['agenda_tipo_id'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_tipo_nome', $dados['agenda_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_tipo_descricao', $dados['agenda_tipo_descricao'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // Listar Tipos de Agenda
    public function listarTiposAgenda($gabinete) {
        $query = 'SELECT * FROM agenda_tipo WHERE agenda_tipo_gabinete = :gabinete OR agenda_tipo_gabinete = 1 ORDER BY agenda_tipo_nome ASC';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':gabinete', $gabinete, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar Tipo de Agenda por ID
    public function buscaTipoAgenda($id) {
        $query = 'SELECT * FROM agenda_tipo WHERE agenda_tipo_id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Apagar Tipo de Agenda
    public function apagarTipoAgenda($id) {
        $query = 'DELETE FROM agenda_tipo WHERE agenda_tipo_id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Criar Situação da Agenda
    public function criarSituacaoAgenda($dados) {
        $query = 'INSERT INTO agenda_situacao (agenda_situacao_id, agenda_situacao_nome, agenda_situacao_descricao, agenda_situacao_criado_por, agenda_situacao_gabinete) 
                  VALUES (UUID(), :agenda_situacao_nome, :agenda_situacao_descricao, :agenda_situacao_criado_por, :agenda_situacao_gabinete)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':agenda_situacao_nome', $dados['agenda_situacao_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_situacao_descricao', $dados['agenda_situacao_descricao'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_situacao_criado_por', $dados['agenda_situacao_criado_por'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_situacao_gabinete', $dados['agenda_situacao_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // Atualizar Situação da Agenda
    public function atualizarSituacaoAgenda($dados) {
        $query = 'UPDATE agenda_situacao 
                  SET agenda_situacao_nome = :agenda_situacao_nome, 
                      agenda_situacao_descricao = :agenda_situacao_descricao
                  WHERE agenda_situacao_id = :agenda_situacao_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':agenda_situacao_id', $dados['agenda_situacao_id'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_situacao_nome', $dados['agenda_situacao_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':agenda_situacao_descricao', $dados['agenda_situacao_descricao'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // Listar Situações da Agenda
    public function listarSituacoesAgenda($gabinete) {
        $query = 'SELECT * FROM agenda_situacao WHERE agenda_situacao_gabinete = :gabinete OR agenda_situacao_gabinete = 1 ORDER BY agenda_situacao_nome ASC';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':gabinete', $gabinete, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar Situação da Agenda por ID
    public function buscaSituacaoAgenda($id) {
        $query = 'SELECT * FROM agenda_situacao WHERE agenda_situacao_id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Apagar Situação da Agenda
    public function apagarSituacaoAgenda($id) {
        $query = 'DELETE FROM agenda_situacao WHERE agenda_situacao_id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        return $stmt->execute();
    }
}
