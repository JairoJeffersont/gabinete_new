<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Models\MensagemModel;
use GabineteMvc\Middleware\Logger;
use PDOException;

class MensagemController {

    private $mensagemModel;
    private $logger;

    public function __construct() {
        $this->mensagemModel = new MensagemModel();
        $this->logger = new Logger();
    }

    // CRIAR NOVA MENSAGEM
    public function novaMensagem($dados) {
        try {
            $this->mensagemModel->criarMensagem($dados);
            return ['status' => 'success', 'message' => 'Mensagem inserida com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
                $this->logger->novoLog('mensagem_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // BUSCAR MENSAGEM
    public function buscaMensagem($coluna, $valor) {
        try {
            $resultado = $this->mensagemModel->buscaMensagem($coluna, $valor);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Mensagem não encontrada'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('mensagem_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // LISTAR MENSAGENS
    public function listarMensagens($itens, $pagina, $ordem, $ordenarPor, $usuario) {
        try {
            $resultado = $this->mensagemModel->listarMensagem($itens, $pagina, $ordem, $ordenarPor, $usuario);

            if ($resultado) {
                $total = (isset($resultado[0]['total_mensagem'])) ? $resultado[0]['total_mensagem'] : 0;
                $totalPaginas = ceil($total / $itens);
                return ['status' => 'success', 'total_paginas' => $totalPaginas, 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhuma mensagem encontrada'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('mensagem_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // APAGAR MENSAGEM
    public function apagarMensagem($mensagemId) {
        try {
            $buscaMensagem = $this->mensagemModel->buscaMensagem('mensagem_id', $mensagemId);

            if (!$buscaMensagem) {
                return ['status' => 'not_found', 'message' => 'Mensagem não encontrada'];
            }

            $this->mensagemModel->apagarMensagem($mensagemId);
            return ['status' => 'success', 'message' => 'Mensagem apagada com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar a mensagem. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('mensagem_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }



}
