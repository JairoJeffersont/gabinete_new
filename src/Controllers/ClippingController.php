<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\ClippingModel;
use PDOException;

class ClippingController {

    private $clippingTipoModel;
    private $logger;

    public function __construct() {
        $this->clippingTipoModel = new ClippingModel();
        $this->logger = new Logger();
    }

    // CRIAR NOVO TIPO DE CLIPPING
    public function novoClippingTipo($dados) {
        try {

            if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 2) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para criar tipos de clipping.'];
            }

            $this->clippingTipoModel->criarClippingTipo($dados);
            return ['status' => 'success', 'message' => 'Tipo de clipping inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O tipo de clipping já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('clipping_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    // ATUALIZAR TIPO DE CLIPPING
    public function atualizarClippingTipo($dados) {
        try {
            $buscaTipo = $this->clippingTipoModel->buscaClippingTipo($dados['clipping_tipo_id']);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de clipping não encontrado'];
            }

            if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 2) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para atualizar tipos de clipping.'];
            }

            if ($buscaTipo['clipping_tipo_criado_por'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível atualizar um tipos de clipping padrão do sistema.'];
            }

            $this->clippingTipoModel->atualizarClippingTipo($dados);
            return ['status' => 'success', 'message' => 'Tipo de clipping atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('clipping_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // LISTAR TIPOS DE CLIPPING
    public function listarClippingTipos($clipping_tipo_gabinete) {
        try {
            $resultado = $this->clippingTipoModel->listarClippingTipos($clipping_tipo_gabinete);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum tipo de clipping encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('clipping_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // BUSCAR TIPO DE CLIPPING PELO ID
    public function buscaClippingTipo($id) {
        try {
            $resultado = $this->clippingTipoModel->buscaClippingTipo($id);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Tipo de clipping não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('clipping_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // APAGAR TIPO DE CLIPPING
    public function apagarClippingTipo($tipoId) {
        try {
            $buscaTipo = $this->clippingTipoModel->buscaClippingTipo($tipoId);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de clipping não encontrado'];
            }

            if ($buscaTipo['clipping_tipo_criado_por'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar um tipos de clipping padrão do sistema.'];
            }

            if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 2) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para apagar tipos de clipping.'];
            }

            $this->clippingTipoModel->apagarClippingTipo($tipoId);
            return ['status' => 'success', 'message' => 'Tipo de clipping apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o tipo de clipping. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('clipping_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
