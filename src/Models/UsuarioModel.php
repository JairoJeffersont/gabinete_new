<?php

namespace GabineteMvc\Models;

use GabineteMvc\Database\Database;
use PDO;

class UsuarioModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // USUÁRIOS
    public function criarUsuario($dados) {
        $query = 'INSERT INTO usuario(usuario_id, usuario_tipo, usuario_gabinete, usuario_nome,  usuario_email, usuario_aniversario, usuario_telefone, usuario_senha, usuario_token, usuario_ativo, usuario_gestor) 
                  VALUES (UUID(), :usuario_tipo, :usuario_gabinete, :usuario_nome, :usuario_email, :usuario_aniversario, :usuario_telefone, :usuario_senha, :usuario_token, :usuario_ativo, :usuario_gestor);';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_tipo', $dados['usuario_tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_gabinete', $dados['usuario_gabinete'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_nome', $dados['usuario_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_email', $dados['usuario_email'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_aniversario', $dados['usuario_aniversario'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':usuario_telefone', $dados['usuario_telefone'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_senha', $dados['usuario_senha'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_token', $dados['usuario_token'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':usuario_ativo', $dados['usuario_ativo'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':usuario_gestor', $dados['usuario_gestor'] ?? 0, PDO::PARAM_BOOL);

        return $stmt->execute();
    }

    public function atualizarUsuario($dados) {
        $query = 'UPDATE usuario SET ';

        $campos = [];

        foreach ($dados as $campo => $valor) {
            if ($campo !== 'usuario_id') {
                $campos[] = "$campo = :$campo";
            }
        }

        $query .= implode(', ', $campos);
        $query .= ' WHERE usuario_id = :usuario_id';
        $stmt = $this->conn->prepare($query);

        foreach ($dados as $campo => $valor) {
            if ($campo === 'usuario_ativo') {
                $stmt->bindValue(":$campo", $valor ?? 1, PDO::PARAM_INT);
            } elseif ($campo === 'usuario_gestor') {
                $stmt->bindValue(":$campo", $valor ?? 0, PDO::PARAM_BOOL);
            } else {
                $stmt->bindValue(":$campo", $valor, PDO::PARAM_STR);
            }
        }

        return $stmt->execute();
    }



    public function listarUsuarios($itens, $pagina, $ordem, $ordenarPor, $gabinete) {
        // Listando usuários com paginação
        $offset = ($pagina - 1) * $itens;

        $query = "SELECT usuario.*, 
                         (SELECT COUNT(usuario_id) FROM usuario WHERE usuario_gabinete = :gabinete) as total_usuarios 
                  FROM usuario WHERE usuario_gabinete = :gabinete
                  ORDER BY $ordenarPor $ordem 
                  LIMIT :itens OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':itens', $itens, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':gabinete', $gabinete, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscaUsuario($coluna, $valor) {
        // Buscando usuário por coluna e valor
        $query = "SELECT * FROM usuario WHERE $coluna = :valor";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function apagarUsuario($usuario_id) {
        // Apagando usuário
        $query = 'DELETE FROM usuario WHERE usuario_id = :usuario_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function novoLog($id) {

        // Inserindo um novo usuário
        $query = 'INSERT INTO usuario_log(usuario_id) VALUES (:usuario_id);';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_id', $id, PDO::PARAM_STR);


        return $stmt->execute();
    }

    public function buscaLog($id) {

        // buscando logs de acesso
        $query = "SELECT * FROM usuario_log WHERE usuario_id = :id ORDER BY log_data DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;
    }


    // TIPOS DE USUÁRIO
    public function criarTipoUsuario($dados) {
        // Inserindo um novo tipo de usuário
        $query = 'INSERT INTO usuario_tipo(usuario_tipo_id, usuario_tipo_nome, usuario_tipo_descricao) 
                  VALUES (UUID(), :usuario_tipo_nome, :usuario_tipo_descricao)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_tipo_nome', $dados['usuario_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_tipo_descricao', $dados['usuario_tipo_descricao'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function atualizarTipoUsuario($dados) {
        // Atualizando tipo de usuário
        $query = 'UPDATE usuario_tipo 
                  SET usuario_tipo_nome = :usuario_tipo_nome, usuario_tipo_descricao = :usuario_tipo_descricao 
                  WHERE usuario_tipo_id = :usuario_tipo_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_tipo_id', $dados['usuario_tipo_id'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_tipo_nome', $dados['usuario_tipo_nome'], PDO::PARAM_STR);
        $stmt->bindValue(':usuario_tipo_descricao', $dados['usuario_tipo_descricao'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function listarTiposUsuario() {
        // Listando tipos de usuário
        $query = "SELECT * FROM usuario_tipo ORDER BY usuario_tipo_nome ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscaTipoUsuario($id) {
        // Buscando tipo de usuário pelo ID
        $query = "SELECT * FROM usuario_tipo WHERE usuario_tipo_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function apagarTipoUsuario($usuario_tipo_id) {
        // Apagando tipo de usuário
        $query = 'DELETE FROM usuario_tipo WHERE usuario_tipo_id = :usuario_tipo_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario_tipo_id', $usuario_tipo_id, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
