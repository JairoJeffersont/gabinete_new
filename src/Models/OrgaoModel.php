<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class OrgaoModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // CRIAR TIPO DE ÓRGÃO
    public function criarOrgaoTipo($dados) {
        $query = 'INSERT INTO orgaos_tipos (
                      orgao_tipo_id, 
                      orgao_tipo_nome, 
                      orgao_tipo_descricao, 
                      orgao_tipo_criado_por, 
                      orgao_tipo_gabinete
                  ) VALUES (
                      UUID(), 
                      :orgao_tipo_nome, 
                      :orgao_tipo_descricao, 
                      :orgao_tipo_criado_por, 
                      :orgao_tipo_gabinete
                  )';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':orgao_tipo_nome', $dados['orgao_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_tipo_descricao', $dados['orgao_tipo_descricao'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_tipo_criado_por', $dados['orgao_tipo_criado_por'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_tipo_gabinete', $dados['orgao_tipo_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // ATUALIZAR TIPO DE ÓRGÃO
    public function atualizarOrgaoTipo($dados) {
        $query = 'UPDATE orgaos_tipos 
                  SET 
                      orgao_tipo_nome = :orgao_tipo_nome, 
                      orgao_tipo_descricao = :orgao_tipo_descricao, 
                      orgao_tipo_gabinete = :orgao_tipo_gabinete 
                  WHERE orgao_tipo_id = :orgao_tipo_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':orgao_tipo_id', $dados['orgao_tipo_id'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_tipo_nome', $dados['orgao_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_tipo_descricao', $dados['orgao_tipo_descricao'], PDO::PARAM_STR);
        $stmt->bindValue(':orgao_tipo_gabinete', $dados['orgao_tipo_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // LISTAR TIPOS DE ÓRGÃOS
    public function listarOrgaosTipos($orgao_tipo_gabinete) {
        $query = "SELECT * FROM view_orgaos_tipos WHERE orgao_tipo_gabinete = :orgao_tipo_gabinete ORDER BY orgao_tipo_nome ASC";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':orgao_tipo_gabinete', $orgao_tipo_gabinete, PDO::PARAM_STR);
    
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    // BUSCAR TIPO DE ÓRGÃO PELO ID
    public function buscaOrgaoTipo($id) {
        $query = "SELECT * FROM view_orgaos_tipos WHERE orgao_tipo_id = :id";

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
