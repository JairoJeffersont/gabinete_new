<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class UsuarioModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // MODELO TIPO DE USUÁRIO
    public function criarTipoUsuario($dados) {
        $query = 'INSERT INTO usuario_tipo(usuario_tipo_id, usuario_tipo_nome, usuario_tipo_descricao) VALUES (UUID(), :usuario_tipo_nome, :usuario_tipo_descricao);';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_tipo_nome', $dados['usuario_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_tipo_descricao', $dados['usuario_tipo_descricao'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function atualizarTipoUsuario($dados) {
        $query = 'UPDATE usuario_tipo SET usuario_tipo_nome = :usuario_tipo_nome, usuario_tipo_descricao = :usuario_tipo_descricao WHERE usuario_tipo_id = :usuario_tipo_id';

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':usuario_tipo_id', $dados['usuario_tipo_id'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_tipo_nome', $dados['usuario_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_tipo_descricao', $dados['usuario_tipo_descricao'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function listarTipoUsuario() {
        $query = "SELECT * FROM usuario_tipo ORDER BY usuario_tipo_nome";
        $stmt = $this->conn->prepare($query);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscaTipoUsuario($id) {
        $query = "SELECT * FROM usuario_tipo WHERE usuario_tipo_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }

    public function apagarTipoUsuario($id) {
        $query = 'DELETE FROM usuario_tipo WHERE usuario_tipo_id = :id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    // MODELO USUÁRIO
    public function criarUsuario($dados) {
        $query = 'INSERT INTO usuario(usuario_id, usuario_gabinete, usuario_nome, usuario_email, usuario_aniversario, usuario_telefone, usuario_senha, usuario_tipo, usuario_ativo) VALUES (UUID(), :usuario_gabinete, :usuario_nome, :usuario_email, :usuario_aniversario, :usuario_telefone, :usuario_senha, :usuario_tipo, :usuario_ativo);';

        $senha = password_hash($dados['usuario_senha'], PASSWORD_DEFAULT);


        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_gabinete', $dados['usuario_gabinete'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_nome', $dados['usuario_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_email', $dados['usuario_email'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_aniversario', $dados['usuario_aniversario'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_telefone', $dados['usuario_telefone'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_senha', $senha, PDO::PARAM_STR);
        $stmt->bindValue(':usuario_tipo', $dados['usuario_tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_ativo', $dados['usuario_ativo'], PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function atualizarUsuario($dados) {
        $query = 'UPDATE usuario SET usuario_gabinete = :usuario_gabinete, usuario_nome = :usuario_nome, usuario_email = :usuario_email, usuario_aniversario = :usuario_aniversario, usuario_telefone = :usuario_telefone, usuario_senha = :usuario_senha, usuario_tipo = :usuario_tipo, usuario_ativo = :usuario_ativo WHERE usuario_id = :usuario_id';

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':usuario_id', $dados['usuario_id'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_gabinete', $dados['usuario_gabinete'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_nome', $dados['usuario_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_email', $dados['usuario_email'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_aniversario', $dados['usuario_aniversario'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_telefone', $dados['usuario_telefone'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_senha', $dados['usuario_senha'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_tipo', $dados['usuario_tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_ativo', $dados['usuario_ativo'], PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function listarUsuarios($itens, $pagina, $ordem, $ordenarPor) {
        $offset = ($pagina - 1) * $itens;

        $query = "SELECT usuario.*, (SELECT COUNT(usuario_id) FROM usuario) as total_usuarios FROM usuario ORDER BY $ordenarPor $ordem LIMIT :itens OFFSET :offset";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscaUsuario($valor, $coluna) {
        $query = "SELECT * FROM usuario WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function apagarUsuario($id) {
        $query = 'DELETE FROM usuario WHERE usuario_id = :id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
