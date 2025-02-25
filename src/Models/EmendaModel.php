<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class EmendaModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }



    // CRIAR EMENDA

    public function criar($dados) {
        $query = "INSERT INTO emendas (emenda_id, emenda_numero, emenda_ano, emenda_valor, emenda_descricao, emenda_status, emenda_orgao, emenda_municipio, emenda_estado, emenda_objetivo, emenda_informacoes, emenda_tipo, emenda_gabinete, emenda_criado_por)
                  VALUES (UUID(), :emenda_numero, :emenda_ano, :emenda_valor, :emenda_descricao, :emenda_status, :emenda_orgao, :emenda_municipio, :emenda_estado, :emenda_objetivo, :emenda_informacoes, :emenda_tipo, :emenda_gabinete, :emenda_criado_por)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':emenda_numero', $dados['emenda_numero'], PDO::PARAM_INT);
        $stmt->bindParam(':emenda_ano', $dados['emenda_ano'], PDO::PARAM_INT);
        $stmt->bindParam(':emenda_valor', $dados['emenda_valor'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_descricao', $dados['emenda_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_status', $dados['emenda_status'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_orgao', $dados['emenda_orgao'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_municipio', $dados['emenda_municipio'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_estado', $dados['emenda_estado'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_objetivo', $dados['emenda_objetivo'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_informacoes', $dados['emenda_informacoes'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_tipo', $dados['emenda_tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_gabinete', $dados['emenda_gabinete'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_criado_por', $dados['emenda_criado_por'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function atualizar($emenda_id, $dados) {
        $query = "UPDATE emendas SET emenda_numero = :emenda_numero, emenda_estado = :emenda_estado, emenda_valor = :emenda_valor, emenda_descricao = :emenda_descricao, 
                  emenda_status = :emenda_status, emenda_orgao = :emenda_orgao, emenda_municipio = :emenda_municipio, 
                  emenda_objetivo = :emenda_objetivo, emenda_informacoes = :emenda_informacoes, emenda_tipo = :emenda_tipo
                  WHERE emenda_id = :emenda_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':emenda_numero', $dados['emenda_numero'], PDO::PARAM_INT);
        $stmt->bindParam(':emenda_valor', $dados['emenda_valor'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_descricao', $dados['emenda_descricao'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_status', $dados['emenda_status'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_orgao', $dados['emenda_orgao'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_estado', $dados['emenda_estado'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_municipio', $dados['emenda_municipio'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_objetivo', $dados['emenda_objetivo'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_informacoes', $dados['emenda_informacoes'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_tipo', $dados['emenda_tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':emenda_id', $emenda_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function listar($itens, $pagina, $ordem, $ordenarPor, $status, $tipo, $objetivo, $ano, $estado, $municipio,  $gabinete) {

        // Converte os parâmetros para inteiros
        $pagina = (int)$pagina;
        $itens = (int)$itens;
        $offset = ($pagina - 1) * $itens;

        // Inicializa a parte WHERE da query
        $where = "WHERE emenda_tipo = :tipo 
                  AND emenda_ano = :ano 
                  AND emenda_gabinete = :gabinete
                  AND emenda_estado = :estado";

        // Condicional para aplicar o filtro de 'status' ou 'objetivo'
        if ($status != 0) {
            $where .= " AND emenda_status = :status";
        }

        if ($objetivo != 0) {
            $where .= " AND emenda_objetivo = :objetivo";
        }

        if (!empty($municipio)) {
            $where .= " AND emenda_municipio = :municipio";
        }

        // Construção da query com total
        $query = "SELECT view_emendas.*, 
                         (SELECT COUNT(*) FROM view_emendas 
                          $where) as total, (SELECT SUM(emenda_valor) FROM view_emendas  $where ) as total_valor
                  FROM view_emendas 
                  $where 
                  ORDER BY $ordenarPor $ordem
                  LIMIT :offset, :itens";

        // Preparação da query
        $stmt = $this->conn->prepare($query);

        // Bind dos parâmetros obrigatórios
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindValue(':ano', $ano, PDO::PARAM_INT);
        $stmt->bindValue(':gabinete', $gabinete, PDO::PARAM_STR);
        $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);


        // Bind para 'status' se for diferente de zero
        if ($status != 0) {
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        }

        // Bind para 'objetivo' se for diferente de zero
        if ($objetivo != 0) {
            $stmt->bindParam(':objetivo', $objetivo, PDO::PARAM_STR);
        }

        if (!empty($municipio)) {
            $stmt->bindParam(':municipio', $municipio, PDO::PARAM_STR);
        }

        // Bind para offset e itens
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);

        // Executa a consulta
        $stmt->execute();

        // Retorna os resultados
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar($coluna, $valor) {
        $query = "SELECT * FROM view_emendas WHERE $coluna = :valor";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function apagar($emenda_id) {
        $query = "DELETE FROM emendas WHERE emenda_id = :emenda_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':emenda_id', $emenda_id, PDO::PARAM_STR);
        return $stmt->execute();
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
