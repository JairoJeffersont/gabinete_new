<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class ClienteModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    //CLIENTE
    public function criarCliente($dados) {

        $query = 'INSERT INTO cliente(cliente_id, cliente_nome, cliente_email, cliente_telefone, cliente_ativo, cliente_usuarios, cliente_gabinete_nome, cliente_gabinete_estado, cliente_gabinete_tipo) VALUES (UUID(), :cliente_nome, :cliente_email, :cliente_telefone, :cliente_ativo, :cliente_usuarios, :cliente_gabinete_nome, :cliente_gabinete_estado, :cliente_gabinete_tipo);';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':cliente_nome', $dados['cliente_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_email', $dados['cliente_email'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_telefone', $dados['cliente_telefone'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_ativo', $dados['cliente_ativo'], PDO::PARAM_BOOL);
        $stmt->bindValue(':cliente_usuarios', $dados['cliente_usuarios'], PDO::PARAM_INT);
        $stmt->bindValue(':cliente_gabinete_nome', $dados['cliente_gabinete_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_gabinete_estado', $dados['cliente_gabinete_estado'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_gabinete_tipo', $dados['cliente_gabinete_tipo'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function atualizarCliente($dados) {

        $query = 'UPDATE cliente SET cliente_nome = :cliente_nome, cliente_email = :cliente_email, cliente_telefone = :cliente_telefone, cliente_ativo = :cliente_ativo, cliente_usuarios = :cliente_usuarios, cliente_gabinete_nome = :cliente_gabinete_nome, cliente_gabinete_estado = :cliente_gabinete_estado, cliente_gabinete_tipo = :cliente_gabinete_tipo WHERE cliente_id = :cliente_id';

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':cliente_nome', $dados['cliente_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_email', $dados['cliente_email'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_telefone', $dados['cliente_telefone'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_ativo', $dados['cliente_ativo'], PDO::PARAM_BOOL);
        $stmt->bindValue(':cliente_usuarios', $dados['cliente_usuarios'], PDO::PARAM_INT);
        $stmt->bindValue(':cliente_gabinete_nome', $dados['cliente_gabinete_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_gabinete_estado', $dados['cliente_gabinete_estado'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_gabinete_tipo', $dados['cliente_gabinete_tipo'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function listarCliente($itens, $pagina, $ordem, $ordenarPor) {

        $offset = ($pagina - 1) * $itens;

        $query = "SELECT view_cliente.*, (SELECT COUNT(cliente_id) FROM cliente) as total_cliente FROM view_cliente ORDER BY $ordenarPor $ordem LIMIT :itens OFFSET :offset";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscaCliente($coluna, $valor) {
        $query = "SELECT * FROM view_cliente WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function apagarCliente($cliente_id) {
        $query = 'DELETE FROM cliente WHERE cliente_id = :cliente_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':cliente_id', $cliente_id, PDO::PARAM_STR);

        return $stmt->execute();
    }


    //TIPO GABINETE
    public function criarTipoGabinete($dados) {
        $query = 'INSERT INTO tipo_gabinete (tipo_gabinete_id, tipo_gabinete_nome, tipo_gabinete_informacoes) 
                  VALUES (UUID(), :tipo_gabinete_nome, :tipo_gabinete_informacoes)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':tipo_gabinete_nome', $dados['tipo_gabinete_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':tipo_gabinete_informacoes', $dados['tipo_gabinete_informacoes'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function atualizarTipoGabinete($dados) {
        $query = 'UPDATE tipo_gabinete 
                  SET tipo_gabinete_nome = :tipo_gabinete_nome, tipo_gabinete_informacoes = :tipo_gabinete_informacoes 
                  WHERE tipo_gabinete_id = :tipo_gabinete_id';

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

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function apagarTipoGabinete($tipo_gabinete_id) {
        $query = 'DELETE FROM tipo_gabinete WHERE tipo_gabinete_id = :tipo_gabinete_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':tipo_gabinete_id', $tipo_gabinete_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
