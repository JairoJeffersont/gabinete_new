<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class UsuarioModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // USUÁRIO
    public function criarUsuario($dados) {
        $query = 'INSERT INTO usuario (usuario_id, usuario_cliente, usuario_nome, usuario_email, usuario_aniversario, usuario_telefone, usuario_senha, usuario_tipo, usuario_ativo) 
                  VALUES (UUID(), :usuario_cliente, :usuario_nome, :usuario_email, :usuario_aniversario, :usuario_telefone, :usuario_senha, :usuario_tipo, :usuario_ativo)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_cliente', $dados['usuario_cliente'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_nome', $dados['usuario_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_email', $dados['usuario_email'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_aniversario', $dados['usuario_aniversario'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_telefone', $dados['usuario_telefone'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_senha', password_hash($dados['usuario_senha'], PASSWORD_DEFAULT), PDO::PARAM_STR);
        $stmt->bindValue(':usuario_tipo', $dados['usuario_tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_ativo', $dados['usuario_ativo'], PDO::PARAM_BOOL);

        return $stmt->execute();
    }

    public function atualizarUsuario($dados) {
        $query = 'UPDATE usuario 
                  SET usuario_nome = :usuario_nome, usuario_email = :usuario_email, usuario_aniversario = :usuario_aniversario, 
                      usuario_telefone = :usuario_telefone, usuario_tipo = :usuario_tipo, usuario_ativo = :usuario_ativo
                  WHERE usuario_id = :usuario_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_id', $dados['usuario_id'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_nome', $dados['usuario_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_email', $dados['usuario_email'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_aniversario', $dados['usuario_aniversario'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_telefone', $dados['usuario_telefone'], PDO::PARAM_STR);        
        $stmt->bindValue(':usuario_tipo', $dados['usuario_tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_ativo', $dados['usuario_ativo'], PDO::PARAM_BOOL);

        return $stmt->execute();
    }

    public function listarUsuarios($usuario_gabinete) {
        $query = "SELECT * FROM view_usuario WHERE usuario_cliente = :cliente ORDER BY usuario_criado_em DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':cliente', $usuario_gabinete, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscaUsuario($coluna, $valor) {
        $query = "SELECT * FROM view_usuario WHERE $coluna = :valor";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function apagarUsuario($usuario_id) {
        $query = 'DELETE FROM usuario WHERE usuario_id = :usuario_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_STR);
        return $stmt->execute();
    }


    public function novoLog($usuario_id) {
        $query = 'INSERT INTO usuario_log (log_usuario) VALUES (:usuario_id)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_STR);

        return $stmt->execute();
    }


    // USUÁRIO TIPO
    public function criarUsuarioTipo($dados) {
        $query = 'INSERT INTO usuario_tipo (usuario_tipo_id, usuario_tipo_nome, usuario_tipo_descricao) 
                  VALUES (UUID(), :usuario_tipo_nome, :usuario_tipo_descricao)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_tipo_nome', $dados['usuario_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_tipo_descricao', $dados['usuario_tipo_descricao'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function atualizarUsuarioTipo($dados) {
        $query = 'UPDATE usuario_tipo 
                  SET usuario_tipo_nome = :usuario_tipo_nome, usuario_tipo_descricao = :usuario_tipo_descricao 
                  WHERE usuario_tipo_id = :usuario_tipo_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_tipo_id', $dados['usuario_tipo_id'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_tipo_nome', $dados['usuario_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_tipo_descricao', $dados['usuario_tipo_descricao'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function listarUsuarioTipos() {
        $query = "SELECT * FROM usuario_tipo ORDER BY usuario_tipo_nome ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscaUsuarioTipo($id) {
        $query = "SELECT * FROM usuario_tipo WHERE usuario_tipo_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function apagarUsuarioTipo($usuario_tipo_id) {
        $query = 'DELETE FROM usuario_tipo WHERE usuario_tipo_id = :usuario_tipo_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_tipo_id', $usuario_tipo_id, PDO::PARAM_STR);
        return $stmt->execute();
    }
}
