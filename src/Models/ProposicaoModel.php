<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class ProposicaoModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function novoTemaProposicao($dados) {
        $query = "INSERT INTO proposicao_tema (proposicao_tema_id, proposicao_tema_nome, proposicao_tema_criado_por, proposicao_tema_gabinete)
                  VALUES (UUID(), :proposicao_tema_nome, :proposicao_tema_criado_por, :proposicao_tema_gabinete)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':proposicao_tema_nome', $dados['proposicao_tema_nome'], PDO::PARAM_STR);
        $stmt->bindParam(':proposicao_tema_criado_por', $dados['proposicao_tema_criado_por'], PDO::PARAM_STR);
        $stmt->bindParam(':proposicao_tema_gabinete', $dados['proposicao_tema_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function listarTemaProposicao($cliente) {
        $query = "SELECT * FROM view_proposicao_tema WHERE proposicao_tema_gabinete = :cliente OR proposicao_tema_gabinete = 1 ORDER BY proposicao_tema_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar($coluna, $valor)
    {
        $query = "SELECT * FROM view_proposicao_tema WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
