<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class PostagemModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function criar($dados) {
        $query = "INSERT INTO postagens (postagem_id, postagem_titulo, postagem_data, postagem_pasta, postagem_informacoes, postagem_midias, postagem_status, postagem_criada_por, postagem_gabinete)
                  VALUES (UUID(), :postagem_titulo, :postagem_data, :postagem_pasta, :postagem_informacoes, :postagem_midias, :postagem_status, :postagem_criada_por, :postagem_gabinete)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':postagem_titulo', $dados['postagem_titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_data', $dados['postagem_data'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_pasta', $dados['postagem_pasta'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_informacoes', $dados['postagem_informacoes'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_midias', $dados['postagem_midias'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_status', $dados['postagem_status'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_criada_por', $dados['postagem_criada_por'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_gabinete', $dados['postagem_gabinete'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function atualizar($postagem_id, $dados) {
        $query = "UPDATE postagens SET postagem_titulo = :postagem_titulo, postagem_data = :postagem_data, 
                  postagem_informacoes = :postagem_informacoes, postagem_midias = :postagem_midias, postagem_status = :postagem_status
                  WHERE postagem_id = :postagem_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':postagem_titulo', $dados['postagem_titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_data', $dados['postagem_data'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_informacoes', $dados['postagem_informacoes'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_midias', $dados['postagem_midias'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_status', $dados['postagem_status'], PDO::PARAM_STR);
        $stmt->bindParam(':postagem_id', $postagem_id, PDO::PARAM_STR);

        return $stmt->execute();
    }


    public function listar($itens, $pagina, $ordem, $ordenarPor, $situacao, $ano, $postagem_gabinete) {
        $pagina = (int)$pagina;
        $itens = (int)$itens;
        $offset = ($pagina - 1) * $itens;

        if ($situacao == 'all') {
            $query = "SELECT *, (SELECT COUNT(*) FROM view_postagens WHERE postagem_gabinete = :postagem_gabinete) AS total FROM view_postagens WHERE postagem_gabinete = :postagem_gabinete AND YEAR(postagem_data) = :ano ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
        } else {
            $query = "SELECT *, (SELECT COUNT(*) FROM view_postagens WHERE postagem_gabinete = :postagem_gabinete) AS total FROM view_postagens WHERE postagem_gabinete = :postagem_gabinete AND postagem_status = :situacao AND YEAR(postagem_data) = :ano ORDER BY $ordenarPor $ordem LIMIT :offset, :itens";
        }


        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);
        $stmt->bindParam(':postagem_gabinete', $postagem_gabinete, PDO::PARAM_STR);
        $stmt->bindParam(':ano', $ano, PDO::PARAM_STR);

        if ($situacao == 'all') {
        } else {
            $stmt->bindParam(':situacao', $situacao, PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar($coluna, $valor) {
        $query = "SELECT * FROM view_postagens WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function apagar($postagem_id) {
        $query = "DELETE FROM postagens WHERE postagem_id = :postagem_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':postagem_id', $postagem_id, PDO::PARAM_STR);

        return $stmt->execute();
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
