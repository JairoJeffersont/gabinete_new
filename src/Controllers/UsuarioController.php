<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Models\UsuarioModel;
use GabineteMvc\Middleware\Logger;
use PDOException;

class UsuarioController {

    private $usuarioModel;
    private $logger;

    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
        $this->logger = new Logger();
    }

    // USUÁRIO CONTROLLER
    public function novoUsuario($dados) {
        $camposObrigatorios = ['usuario_nome', 'usuario_email', 'usuario_telefone', 'usuario_senha', 'usuario_tipo', 'usuario_gabinete'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        if (!filter_var($dados['usuario_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
        }

        // Criptografar a senha
        $dados['usuario_senha'] = password_hash($dados['usuario_senha'], PASSWORD_BCRYPT);

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
        try {
            $buscaUsuario = $this->usuarioModel->buscaUsuario('usuario_id', $dados['usuario_id']);

            if (!$buscaUsuario) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }

            $camposObrigatorios = ['usuario_nome', 'usuario_email', 'usuario_telefone', 'usuario_senha', 'usuario_tipo', 'usuario_gabinete'];

            foreach ($camposObrigatorios as $campo) {
                if (!isset($dados[$campo])) {
                    return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
                }
            }

            if (!filter_var($dados['usuario_email'], FILTER_VALIDATE_EMAIL)) {
                return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
            }

            // Criptografar a senha
            $dados['usuario_senha'] = password_hash($dados['usuario_senha'], PASSWORD_BCRYPT);

            $this->usuarioModel->atualizarUsuario($dados);
            return ['status' => 'success', 'message' => 'Usuário atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscaUsuario($id) {
        try {
            $resultado = $this->usuarioModel->buscaUsuario('usuario_id', $id);
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

    public function listarUsuarios($itens, $pagina, $ordem, $ordenarPor, $gabinete) {
        try {
            $resultado = $this->usuarioModel->listarUsuarios($itens, $pagina, $ordem, $ordenarPor, $gabinete);

            if ($resultado) {
                $total = (isset($resultado[0]['total_usuario'])) ? $resultado[0]['total_usuario'] : 0;
                $totalPaginas = ceil($total / $itens);
                return ['status' => 'success', 'total_paginas' => $totalPaginas, 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum usuário encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function apagarUsuario($usuarioId) {
        try {
            $buscaUsuario = $this->usuarioModel->buscaUsuario('usuario_id', $usuarioId);

            if (!$buscaUsuario) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }

            $this->usuarioModel->apagarUsuario($usuarioId);
            return ['status' => 'success', 'message' => 'Usuário apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o usuário. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // TIPO USUÁRIO CONTROLLER
    public function novoTipoUsuario($dados) {
        $camposObrigatorios = ['usuario_tipo_nome', 'usuario_tipo_descricao'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->usuarioModel->criarTipoUsuario($dados);
            return ['status' => 'success', 'message' => 'Tipo de usuário inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O tipo de usuário já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    public function atualizarTipoUsuario($dados) {
        try {

            $buscaTipo = $this->usuarioModel->buscaTipoUsuario($dados['usuario_tipo_id']);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de usuário não encontrado'];
            }

            $camposObrigatorios = ['usuario_tipo_nome', 'usuario_tipo_descricao'];

            foreach ($camposObrigatorios as $campo) {
                if (!isset($dados[$campo])) {
                    return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
                }
            }

            $this->usuarioModel->atualizarTipoUsuario($dados);
            return ['status' => 'success', 'message' => 'Tipo de usuário atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarTipoUsuario() {
        try {
            $resultado = $this->usuarioModel->listarTiposUsuario();
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum tipo de usuário encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscaTipoUsuario($id) {
        try {
            $resultado = $this->usuarioModel->buscaTipoUsuario($id);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Tipo de usuário não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function apagarTipoUsuario($tipoId) {
        try {
            $buscaTipo = $this->usuarioModel->buscaTipoUsuario($tipoId);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de usuário não encontrado'];
            }

            $this->usuarioModel->apagarTipoUsuario($tipoId);
            return ['status' => 'success', 'message' => 'Tipo de usuário apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o tipo de usuário. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
