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

    // USUARIO TIPO CONTROLLER
    public function novoTipoUsuario($dados) {
        try {
            $this->usuarioModel->criarTipoUsuario($dados);
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

    public function atualizarTipoUsuario($dados) {
        try {
            $buscaTipoUsuario = $this->usuarioModel->buscaTipoUsuario($dados['usuario_tipo_id']);

            if (!$buscaTipoUsuario) {
                return ['status' => 'not_found', 'message' => 'Tipo de usuário não encontrado'];
            }

            $this->usuarioModel->atualizarTipoUsuario($dados);
            return ['status' => 'success', 'message' => 'Tipo de usuário atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarTiposUsuario() {
        try {
            $resultado = $this->usuarioModel->listarTipoUsuario();

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

    public function apagarTipoUsuario($id) {
        try {
            $buscaTipoUsuario = $this->usuarioModel->buscaTipoUsuario($id);

            if (!$buscaTipoUsuario) {
                return ['status' => 'not_found', 'message' => 'Tipo de usuário não encontrado'];
            }

            $this->usuarioModel->apagarTipoUsuario($id);
            return ['status' => 'success', 'message' => 'Tipo de usuário removido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o tipo de usuário. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // USUARIO CONTROLLER
    public function novoUsuario($dados) {
        try {

            if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 1) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para inserir novos usuários'];
            }

            $this->usuarioModel->criarUsuario($dados);
            return ['status' => 'success', 'message' => 'Usuário inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O usuário já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id);
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    public function atualizarUsuario($dados) {
        try {
            $buscaUsuario = $this->usuarioModel->buscaUsuario($dados['usuario_id'], 'usuario_id');
            if (!$buscaUsuario) {
                return ['status' => 'not_found'];
            }

            if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 1) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para editar usuários'];
            }

            $this->usuarioModel->atualizarUsuario($dados);
            return ['status' => 'success', 'message' => 'Usuário atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscarUsuario($coluna, $valor) {
        try {
            $resultado = $this->usuarioModel->buscaUsuario($valor, $coluna);
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

    public function apagarUsuario($id) {
        try {
            $buscaUsuario = $this->usuarioModel->buscaUsuario($id, 'usuario_id');

            if (!$buscaUsuario) {
                return ['status' => 'not_found', 'message' => 'Usuário não encontrado'];
            }

            $this->usuarioModel->apagarUsuario($id);
            return ['status' => 'success', 'message' => 'Usuário removido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o usuário. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
