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
        try {
            $this->clienteModel->criar($dados);
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
            $this->clienteModel->atualizar($dados);
            return ['status' => 'success', 'message' => 'Cliente atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarClientes($itens, $pagina, $ordem, $ordenarPor) {
        try {
            $resultado = $this->clienteModel->listar($itens, $pagina, $ordem, $ordenarPor);

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
            $buscaCliente = $this->clienteModel->busca('cliente_id', $clienteId);

            if (!$buscaCliente) {
                return ['status' => 'not_found', 'message' => 'Cliente não encontrado'];
            }

            $this->clienteModel->apagar($clienteId);
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
