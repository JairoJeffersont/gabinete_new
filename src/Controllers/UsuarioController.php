<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\UsuarioModel;
use PDOException;

class UsuarioController {
    private $usuarioModel;
    private $logger;

    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
        $this->logger = new Logger();
    }

    //USUARIO CONTROLLER
    public function novoUsuario($dados) {

        $nivel = isset($_SESSION['usuario_tipo']) ? $_SESSION['usuario_tipo'] : 2;

        if ($nivel != 2) {
            return ['status' => 'forbidden', 'message' => "Você não tem autorização para inserir novos usuários."];
        }

        $camposObrigatorios = ['usuario_cliente', 'usuario_nome', 'usuario_email', 'usuario_aniversario', 'usuario_telefone', 'usuario_senha', 'usuario_tipo', 'usuario_ativo'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        if (!filter_var($dados['usuario_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
        }

        try {
            $this->usuarioModel->criarUsuario($dados);
            return ['status' => 'success', 'message' => 'Usuário inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O usuário já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    public function atualizarUsuario($dados) {

        if ($_SESSION['usuario_tipo'] != 2) {
            return ['status' => 'forbidden', 'message' => "Você não tem autorização para atualizar um usuário."];
        }

        $camposObrigatorios = ['usuario_nome', 'usuario_email', 'usuario_aniversario', 'usuario_telefone', 'usuario_tipo', 'usuario_ativo'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        if (!filter_var($dados['usuario_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
        }


        try {
            $usuario = $this->usuarioModel->buscaUsuario('usuario_id', $dados['usuario_id']);

            if (!$usuario) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }

            $this->usuarioModel->atualizarUsuario($dados);
            return ['status' => 'success', 'message' => 'Usuário atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscaUsuario($coluna, $id) {
        try {
            $resultado = $this->usuarioModel->buscaUsuario($coluna, $id);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarUsuarios($usuario_gabinete) {
        try {
            $resultado = $this->usuarioModel->listarUsuarios($usuario_gabinete);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum usuário encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function apagarUsuario($usuario_id) {

        if ($_SESSION['usuario_tipo'] != 2) {
            return ['status' => 'forbidden', 'message' => "Você não tem autorização para apagar um usuário."];
        }

        try {

            $usuario = $this->usuarioModel->buscaUsuario('usuario_id', $usuario_id);

            if ($usuario['usuario_nome'] == $_SESSION['cliente_nome']) {
                return ['status' => 'forbidden', 'message' => "Você não pode apagar o gestor do gabinete."];
            }

            if (!$usuario) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }

            $this->usuarioModel->apagarUsuario($usuario_id);
            return ['status' => 'success', 'message' => 'Usuário apagado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }


    public function novoLog($usuario_id) {
        try {
            $this->usuarioModel->novoLog($usuario_id);
            return ['status' => 'success', 'message' => 'Log inserido com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }


    //TIPO USUARIO CONTROLLER
    public function novoUsuarioTipo($dados) {
        $camposObrigatorios = ['usuario_tipo_id', 'usuario_tipo_nome', 'usuario_tipo_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->usuarioModel->criarUsuarioTipo($dados);
            return ['status' => 'success', 'message' => 'Tipo de usuário inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O tipo de usuário já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('usuario_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    public function atualizarUsuarioTipo($dados) {
        try {
            $usuarioTipo = $this->usuarioModel->buscaUsuarioTipo($dados['usuario_tipo_id']);

            if (!$usuarioTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de usuário não encontrado'];
            }

            $this->usuarioModel->atualizarUsuarioTipo($dados);
            return ['status' => 'success', 'message' => 'Tipo de usuário atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscaUsuarioTipo($id) {
        try {
            $resultado = $this->usuarioModel->buscaUsuarioTipo($id);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Tipo de usuário não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarUsuariosTipos() {
        try {
            $resultado = $this->usuarioModel->listarUsuarioTipos();
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum tipo de usuário encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function apagarUsuarioTipo($usuario_tipo_id) {
        try {
            $usuarioTipo = $this->usuarioModel->buscaUsuarioTipo($usuario_tipo_id);

            if (!$usuarioTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de usuário não encontrado'];
            }

            $this->usuarioModel->apagarUsuarioTipo($usuario_tipo_id);
            return ['status' => 'success', 'message' => 'Tipo de usuário apagado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
