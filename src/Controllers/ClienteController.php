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

    //CLIENTE CONTROLLER
    public function novoCliente($dados) {

        $camposObrigatorios = ['cliente_nome', 'cliente_email', 'cliente_telefone', 'cliente_usuarios', 'cliente_gabinete_nome', 'cliente_gabinete_estado', 'cliente_gabinete_tipo'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        if (!filter_var($dados['cliente_email'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
        }

        try {
            $dados['cliente_ativo'] = 1;
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

    //TIPO GABINETE CONTROLLER
    public function novoTipoGabinete($dados) {

        $camposObrigatorios = ['tipo_gabinete_nome', 'tipo_gabinete_informacoes'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->clienteModel->criarTipoGabinete($dados);
            return ['status' => 'success', 'message' => 'Tipo de gabinete inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O tipo de gabinete já esta cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    public function atualizarTipoGabinete($dados) {
        try {

            $buscaTipo = $this->clienteModel->buscaTipoGabinete($dados['tipo_gabinete_id']);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de gabiente não encontrado'];
            }

            $camposObrigatorios = ['tipo_gabinete_nome', 'tipo_gabinete_informacoes'];

            foreach ($camposObrigatorios as $campo) {
                if (!isset($dados[$campo])) {
                    return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
                }
            }

            if (!filter_var($dados['cliente_email'], FILTER_VALIDATE_EMAIL)) {
                return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
            }

            $this->clienteModel->atualizarCliente($dados);
            return ['status' => 'success', 'message' => 'Tipo de gabinete atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarTipoGabinete() {
        try {
            $resultado = $this->clienteModel->listarTipoGabinete();
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum tipo encontrado encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscaTipoGabinete($id) {
        try {
            $resultado = $this->clienteModel->buscaTipoGabinete($id);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Tipo gabinete não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function apagarTipoGabinete($tipoId) {
        try {
            $buscaTipo = $this->clienteModel->buscaTipoGabinete($tipoId);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo gabinete não encontrado'];
            }

            $this->clienteModel->apagarTipoGabinete($tipoId);
            return ['status' => 'success', 'message' => 'Tipo gabinete apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o tipo. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('cliente_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
