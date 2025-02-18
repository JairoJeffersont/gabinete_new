<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class ClienteModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function criar($dados) {

        $query = 'INSERT INTO cliente(cliente_id, cliente_nome, cliente_email, cliente_telefone, cliente_ativo, cliente_endereco, cliente_cep, cliente_cpf) VALUES (UUID(), :cliente_nome, :cliente_email, :cliente_telefone, :cliente_ativo, :cliente_endereco, :cliente_cep, :cliente_cpf);';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':cliente_nome', $dados['cliente_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_email', $dados['cliente_email'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_telefone', $dados['cliente_telefone'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_ativo', $dados['cliente_ativo'], PDO::PARAM_BOOL);
        $stmt->bindValue(':cliente_endereco', $dados['cliente_endereco'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_cep', $dados['cliente_cep'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_cpf', $dados['cliente_cpf'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function atualizar($dados) {

        $query = 'UPDATE cliente SET cliente_nome = :cliente_nome, cliente_email = :cliente_email, cliente_telefone = :cliente_telefone, cliente_ativo = :cliente_ativo, cliente_endereco = :cliente_endereco, cliente_cep = :cliente_cep, cliente_cpf = :cliente_cpf WHERE cliente_id = :cliente_id';

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':cliente_id', $dados['cliente_id'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_nome', $dados['cliente_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_email', $dados['cliente_email'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_telefone', $dados['cliente_telefone'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_ativo', $dados['cliente_ativo'], PDO::PARAM_BOOL);
        $stmt->bindValue(':cliente_endereco', $dados['cliente_endereco'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_cep', $dados['cliente_cep'], PDO::PARAM_STR);
        $stmt->bindValue(':cliente_cpf', $dados['cliente_cpf'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function listar($itens, $pagina, $ordem, $ordenarPor) {

        $offset = ($pagina - 1) * $itens;

        $query = "SELECT cliente.*, (SELECT COUNT(cliente_id) FROM cliente) as total_cliente FROM cliente ORDER BY $ordenarPor $ordem LIMIT :itens OFFSET :offset";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function busca($coluna, $valor) {
        $query = "SELECT * FROM cliente WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function apagar($cliente_id) {
        $query = 'DELETE FROM cliente WHERE cliente_id = :cliente_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':cliente_id', $cliente_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
