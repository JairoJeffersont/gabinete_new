<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\PostagemModel;
use PDOException;

class PostagemController {

    private $postagemModel;
    private $logger;

    public function __construct() {
        $this->postagemModel = new PostagemModel();
        $this->logger = new Logger();
    }

    // CRIAR NOVO STATUS DE POSTAGEM
    public function novoPostagemStatus($dados) {
        try {
            $this->postagemModel->criarPostagemStatus($dados);
            return ['status' => 'success', 'message' => 'Status de postagem inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O status de postagem já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('postagem_status_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    // ATUALIZAR STATUS DE POSTAGEM
    public function atualizarPostagemStatus($dados) {
        try {
            $buscaStatus = $this->postagemModel->buscaPostagemStatus($dados['postagem_status_id']);

            if (!$buscaStatus) {
                return ['status' => 'not_found', 'message' => 'Status de postagem não encontrado'];
            }

            // Lógica de restrição para não permitir atualização de status padrão
            if ($buscaStatus['postagem_status_criado_por'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível atualizar um status de postagem padrão do sistema.'];
            }

            $this->postagemModel->atualizarPostagemStatus($dados);
            return ['status' => 'success', 'message' => 'Status de postagem atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('postagem_status_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // LISTAR STATUS DE POSTAGENS
    public function listarPostagemStatus() {
        try {
            $resultado = $this->postagemModel->listarPostagemStatus();
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum status de postagem encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('postagem_status_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // BUSCAR STATUS DE POSTAGEM PELO ID
    public function buscaPostagemStatus($id) {
        try {
            $resultado = $this->postagemModel->buscaPostagemStatus($id);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Status de postagem não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('postagem_status_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // APAGAR STATUS DE POSTAGEM
    public function apagarPostagemStatus($statusId) {
        try {
            $buscaStatus = $this->postagemModel->buscaPostagemStatus($statusId);

            if (!$buscaStatus) {
                return ['status' => 'not_found', 'message' => 'Status de postagem não encontrado'];
            }

            // Lógica de restrição para não permitir apagar status padrão
            if ($buscaStatus['postagem_status_criado_por'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar um status de postagem padrão do sistema.'];
            }

            $this->postagemModel->apagarPostagemStatus($statusId);
            return ['status' => 'success', 'message' => 'Status de postagem apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o status de postagem. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('postagem_status_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

}
