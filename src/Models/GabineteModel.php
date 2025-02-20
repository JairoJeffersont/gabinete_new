<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class GabineteModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // GABINETE
    public function criarGabinete($dados) {
        // Inserindo um novo gabinete
        $query = 'INSERT INTO gabinete(gabinete_id, gabinete_tipo, gabinete_nome, gabinete_endereco, gabinete_municipio, gabinete_estado, gabinete_email, gabinete_telefone) 
                  VALUES (UUID(), :gabinete_tipo, :gabinete_nome, :gabinete_endereco, :gabinete_municipio, :gabinete_estado, :gabinete_email, :gabinete_telefone);';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':gabinete_tipo', $dados['gabinete_tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_nome', $dados['gabinete_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_endereco', $dados['gabinete_endereco'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_municipio', $dados['gabinete_municipio'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_estado', $dados['gabinete_estado'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_email', $dados['gabinete_email'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_telefone', $dados['gabinete_telefone'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function atualizarGabinete($dados) {
        // Atualizando as informações do gabinete
        $query = 'UPDATE gabinete 
                  SET gabinete_tipo = :gabinete_tipo, gabinete_nome = :gabinete_nome, gabinete_endereco = :gabinete_endereco, 
                      gabinete_municipio = :gabinete_municipio, gabinete_estado = :gabinete_estado, gabinete_email = :gabinete_email, 
                      gabinete_telefone = :gabinete_telefone 
                  WHERE gabinete_id = :gabinete_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':gabinete_id', $dados['gabinete_id'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_tipo', $dados['gabinete_tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_nome', $dados['gabinete_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_endereco', $dados['gabinete_endereco'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_municipio', $dados['gabinete_municipio'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_estado', $dados['gabinete_estado'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_email', $dados['gabinete_email'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_telefone', $dados['gabinete_telefone'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function listarGabinete($itens, $pagina, $ordem, $ordenarPor) {
        // Listando gabinetes com paginação
        $offset = ($pagina - 1) * $itens;

        $query = "SELECT gabinete.*, 
                         (SELECT COUNT(gabinete_id) FROM gabinete) as total_gabinete 
                  FROM gabinete 
                  ORDER BY $ordenarPor $ordem 
                  LIMIT :itens OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscaGabinete($coluna, $valor) {
        // Buscando gabinete por coluna e valor
        $query = "SELECT * FROM gabinete WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function apagarGabinete($gabinete_id) {
        // Apagando gabinete
        $query = 'DELETE FROM gabinete WHERE gabinete_id = :gabinete_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':gabinete_id', $gabinete_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    // TIPO DE GABINETE
    public function criarTipoGabinete($dados) {
        // Inserindo um novo tipo de gabinete
        $query = 'INSERT INTO gabinete_tipo(gabinete_tipo_id, gabinete_tipo_nome, gabinete_tipo_informacoes) 
                  VALUES (UUID(), :gabinete_tipo_nome, :gabinete_tipo_informacoes)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':gabinete_tipo_nome', $dados['gabinete_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_tipo_informacoes', $dados['gabinete_tipo_informacoes'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function atualizarTipoGabinete($dados) {
        // Atualizando tipo de gabinete
        $query = 'UPDATE gabinete_tipo 
                  SET gabinete_tipo_nome = :gabinete_tipo_nome, gabinete_tipo_informacoes = :gabinete_tipo_informacoes 
                  WHERE gabinete_tipo_id = :gabinete_tipo_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':gabinete_tipo_id', $dados['gabinete_tipo_id'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_tipo_nome', $dados['gabinete_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_tipo_informacoes', $dados['gabinete_tipo_informacoes'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function listarTipoGabinete() {
        // Listando tipos de gabinetes
        $query = "SELECT * FROM gabinete_tipo ORDER BY gabinete_tipo_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscaTipoGabinete($id) {
        // Buscando tipo de gabinete pelo ID
        $query = "SELECT * FROM gabinete_tipo WHERE gabinete_tipo_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function apagarTipoGabinete($gabinete_tipo_id) {
        // Apagando tipo de gabinete
        $query = 'DELETE FROM gabinete_tipo WHERE gabinete_tipo_id = :gabinete_tipo_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':gabinete_tipo_id', $gabinete_tipo_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
