<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\ClienteModel;
use PDOException;

class ClienteController {

    private $clienteModel;
    private $logger;

    public function __construct() {
        $this->clienteModel = new ClienteModel();
        $this->logger = new Logger();
    }

    public function novoCliente($dados) {

        $camposObrigatorios = ['cliente_id', 'cliente_nome', 'cliente_email', 'cliente_telefone', 'cliente_ativo', 'cliente_usuarios', 'cliente_gabinete_nome', 'cliente_gabinete_estado', 'cliente_gabinete_tipo'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        if (!filter_var($dados['cliente_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
        }

        try {
            $this->clienteModel->criarCliente($dados);
            return ['status' => 'success', 'message' => 'Cliente inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O cliente já esta cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    public function atualizarCliente($dados) {
        try {

            $buscaCliente = $this->clienteModel->buscaCliente('cliente_id', $dados['cliente_id']);

            if (!$buscaCliente) {
                return ['status' => 'not_found', 'message' => 'Cliente não encontrado'];
            }

            $camposObrigatorios = ['cliente_id', 'cliente_nome', 'cliente_email', 'cliente_telefone', 'cliente_ativo', 'cliente_usuarios', 'cliente_gabinete_nome', 'cliente_gabinete_estado', 'cliente_gabinete_tipo'];

            foreach ($camposObrigatorios as $campo) {
                if (!isset($dados[$campo])) {
                    return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
                }
            }

            if (!filter_var($dados['cliente_email'], FILTER_VALIDATE_EMAIL)) {
                return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
            }

            $this->clienteModel->atualizarCliente($dados);
            return ['status' => 'success', 'message' => 'Cliente atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscaCliente($id) {
        try {
            $resultado = $this->clienteModel->buscaCliente('cliente_id', $id);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Cliente não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarClientes($itens, $pagina, $ordem, $ordenarPor) {
        try {
            $resultado = $this->clienteModel->listarCliente($itens, $pagina, $ordem, $ordenarPor);

            if ($resultado) {
                $total = (isset($resultado[0]['total_cliente'])) ? $resultado[0]['total_cliente'] : 0;
                $totalPaginas = ceil($total / $itens);
                return ['status' => 'success', 'total_paginas' => $totalPaginas, 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum cliente encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function apagarCliente($clienteId) {
        try {
            $buscaCliente = $this->clienteModel->buscaCliente('cliente_id', $clienteId);

            if (!$buscaCliente) {
                return ['status' => 'not_found', 'message' => 'Cliente não encontrado'];
            }

            $this->clienteModel->apagarCliente($clienteId);
            return ['status' => 'success', 'message' => 'Cliente apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o cliente. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}