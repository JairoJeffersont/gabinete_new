<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class GabineteModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }


    //MODELO TIPO DE GABINETE
    public function criarTipoGabinete($dados) {
        $query = 'INSERT INTO tipo_gabinete(tipo_gabinete_id, tipo_gabinete_nome, tipo_gabinete_informacoes) VALUES (UUID(), :tipo_gabinete_nome, :tipo_gabinete_informacoes);';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':tipo_gabinete_nome', $dados['tipo_gabinete_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':tipo_gabinete_informacoes', $dados['tipo_gabinete_informacoes'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function atualizarTipoGabinete($dados) {
        $query = 'UPDATE tipo_gabinete SET tipo_gabinete_nome = :tipo_gabinete_nome, tipo_gabinete_informacoes = :tipo_gabinete_informacoes WHERE tipo_gabinete_id = :tipo_gabinete_id';

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':tipo_gabinete_id', $dados['tipo_gabinete_id'], PDO::PARAM_STR);
        $stmt->bindValue(':tipo_gabinete_nome', $dados['tipo_gabinete_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':tipo_gabinete_informacoes', $dados['tipo_gabinete_informacoes'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function listarTipoGabinete() {
        $query = "SELECT * FROM tipo_gabinete ORDER BY tipo_gabinete_nome ASC";
        $stmt = $this->conn->prepare($query);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscaTipoGabinete($id) {
        $query = "SELECT * FROM tipo_gabinete WHERE tipo_gabinete_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function apagarTipoGabinete($id) {
        $query = 'DELETE FROM tipo_gabinete WHERE tipo_gabinete_id = :id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);

        return $stmt->execute();
    }


    //MODELO GABINETE
    public function criarGabinete($dados) {
        $query = 'INSERT INTO gabinete(gabinete_id, gabinete_cliente, gabinete_tipo, gabinete_politico, gabinete_estado, gabinete_endereco, gabinete_municipio, gabinete_telefone, gabinete_funcionarios) VALUES (UUID(), :gabinete_cliente, :gabinete_tipo, :gabinete_politico, :gabinete_estado, :gabinete_endereco, :gabinete_municipio,  :gabinete_telefone, :gabinete_funcionarios);';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':gabinete_cliente', $dados['gabinete_cliente'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_tipo', $dados['gabinete_tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_politico', $dados['gabinete_politico'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_estado', $dados['gabinete_estado'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_endereco', $dados['gabinete_endereco'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_municipio', $dados['gabinete_municipio'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_telefone', $dados['gabinete_telefone'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_funcionarios', $dados['gabinete_funcionarios'], PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function atualizarGabinete($dados) {
        $query = 'UPDATE gabinete SET gabinete_cliente = :gabinete_cliente, gabinete_tipo = :gabinete_tipo, gabinete_politico = :gabinete_politico, gabinete_estado = :gabinete_estado, gabinete_endereco = :gabinete_endereco, gabinete_municipio = :gabinete_municipio,  gabinete_telefone = :gabinete_telefone WHERE gabinete_id = :gabinete_id';

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':gabinete_id', $dados['gabinete_id'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_cliente', $dados['gabinete_cliente'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_tipo', $dados['gabinete_tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_politico', $dados['gabinete_politico'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_estado', $dados['gabinete_estado'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_endereco', $dados['gabinete_endereco'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_municipio', $dados['gabinete_municipio'], PDO::PARAM_STR);
        $stmt->bindValue(':gabinete_telefone', $dados['gabinete_telefone'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function listarGabinete($itens, $pagina, $ordem, $ordenarPor) {
        $offset = ($pagina - 1) * $itens;

        $query = "SELECT gabinete.*, (SELECT COUNT(gabinete_id) FROM gabinete) as total_gabinete FROM gabinete ORDER BY $ordenarPor $ordem LIMIT :itens OFFSET :offset";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscaGabinete($id) {
        $query = "SELECT * FROM gabinete WHERE gabinete_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function apagarGabinete($id) {
        $query = 'DELETE FROM gabinete WHERE gabinete_id = :id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
