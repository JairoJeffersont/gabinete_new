<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\TipoUsuarioModel;
use PDOException;

/**
 * Classe TipoUsuarioController
 * Controlador de tipos de usuários.
 *
 * Gerencia operações como criação, atualização, busca, listagem e remoção de tipos de usuários.
 */
class TipoUsuarioController {

    private $usuarioTipoModel;
    private $logger;

    public function __construct() {
        $this->usuarioTipoModel = new TipoUsuarioModel();
        $this->logger = new Logger();
    }

    public function novoUsuarioTipo($dados) {
        if (!isset($dados['usuario_tipo_nome'])) {
            return ['status' => 'bad_request', 'message' => 'Campo usuario_tipo_nome é obrigatório'];
        }

        $dados['usuario_tipo_nome'] = htmlspecialchars(trim($dados['usuario_tipo_nome']));

        try {
            $this->usuarioTipoModel->criar($dados);
            return ['status' => 'success', 'message' => 'Tipo de usuário inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O tipo de usuário já está cadastrado'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function atualizarUsuarioTipo($dados) {
        if (!isset($dados['usuario_tipo_id']) || !isset($dados['usuario_tipo_nome'])) {
            return ['status' => 'bad_request', 'message' => 'Campos obrigatórios faltando'];
        }

        $dados['usuario_tipo_nome'] = htmlspecialchars(trim($dados['usuario_tipo_nome']));

        try {
            $buscaUsuarioTipo = $this->usuarioTipoModel->busca('usuario_tipo_id', $dados['usuario_tipo_id']);
            if (!$buscaUsuarioTipo) {
                return ['status' => 'not_found'];
            }

            $this->usuarioTipoModel->atualizar($dados);
            return ['status' => 'success', 'message' => 'Tipo de usuário atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscarUsuarioTipo($valor, $coluna = 'usuario_tipo_id') {
        $colunasPermitidas = ['usuario_tipo_id', 'usuario_tipo_nome'];

        if (!in_array($coluna, $colunasPermitidas)) {
            return ['status' => 'bad_request'];
        }

        try {
            $resultado = $this->usuarioTipoModel->busca($coluna, $valor);
            return $resultado ? ['status' => 'success', 'dados' => $resultado] : ['status' => 'not_found'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarUsuarioTipos($itens, $pagina, $ordem, $ordenarPor) {
        try {
            $resultado = $this->usuarioTipoModel->listar($itens, $pagina, $ordem, $ordenarPor);
            if ($resultado) {
                $total = isset($resultado[0]['total_usuario_tipo']) ? $resultado[0]['total_usuario_tipo'] : 0;
                $totalPaginas = ceil($total / $itens);
                return ['status' => 'success', 'total_paginas' => $totalPaginas, 'dados' => $resultado];
            }
            return ['status' => 'not_found', 'message' => 'Nenhum tipo de usuário encontrado'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function apagarUsuarioTipo($usuarioTipoId) {
        if (!isset($usuarioTipoId)) {
            return ['status' => 'bad_request'];
        }

        try {
            $buscaUsuarioTipo = $this->usuarioTipoModel->busca('usuario_tipo_id', $usuarioTipoId);
            if (!$buscaUsuarioTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de usuário não encontrado'];
            }

            $this->usuarioTipoModel->apagar($usuarioTipoId);
            return ['status' => 'success', 'message' => 'Tipo de usuário removido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o tipo de usuário. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('usuario_tipo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
