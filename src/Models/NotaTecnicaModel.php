<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class NotaTecnicaModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // Criar uma nova nota técnica
    public function criarNotaTecnica($dados) {
        $query = 'INSERT INTO nota_tecnica (nota_id, nota_proposicao, nota_proposicao_apelido, nota_proposicao_resumo, nota_proposicao_tema, nota_texto, nota_criada_por, nota_gabinete) 
                  VALUES (UUID(), :nota_proposicao, :nota_proposicao_apelido, :nota_proposicao_resumo, :nota_proposicao_tema, :nota_texto, :nota_criada_por, :nota_gabinete)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':nota_proposicao', $dados['nota_proposicao'], PDO::PARAM_INT);
        $stmt->bindValue(':nota_proposicao_apelido', $dados['nota_proposicao_apelido'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':nota_proposicao_resumo', $dados['nota_proposicao_resumo'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':nota_proposicao_tema', $dados['nota_proposicao_tema'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':nota_texto', $dados['nota_texto'], PDO::PARAM_STR);
        $stmt->bindValue(':nota_criada_por', $dados['nota_criada_por'], PDO::PARAM_STR);
        $stmt->bindValue(':nota_gabinete', $dados['nota_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // Atualizar uma nota técnica existente
    public function atualizarNotaTecnica($dados) {
        $query = 'UPDATE nota_tecnica SET ';
        $campos = [];

        foreach ($dados as $campo => $valor) {
            if ($campo !== 'nota_id') {
                $campos[] = "$campo = :$campo";
            }
        }

        $query .= implode(', ', $campos);
        $query .= ' WHERE nota_id = :nota_id';
        $stmt = $this->conn->prepare($query);

        foreach ($dados as $campo => $valor) {
            $stmt->bindValue(":$campo", $valor, PDO::PARAM_STR);
        }

        return $stmt->execute();
    }

    // Listar todas as notas técnicas com paginação
    public function listarNotasTecnicas($itens, $pagina, $ordem, $ordenarPor, $gabinete) {
        $offset = ($pagina - 1) * $itens;

        $query = "SELECT nota_tecnica.*, 
                         (SELECT COUNT(nota_id) FROM nota_tecnica WHERE nota_gabinete = :gabinete) as total_notas 
                  FROM nota_tecnica WHERE nota_gabinete = :gabinete
                  ORDER BY $ordenarPor $ordem 
                  LIMIT :itens OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':gabinete', $gabinete, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar uma nota técnica por coluna e valor
    public function buscaNotaTecnica($coluna, $valor) {
        $query = "SELECT * FROM nota_tecnica WHERE $coluna = :valor";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Apagar uma nota técnica
    public function apagarNotaTecnica($nota_id) {
        $query = 'DELETE FROM nota_tecnica WHERE nota_id = :nota_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':nota_id', $nota_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
