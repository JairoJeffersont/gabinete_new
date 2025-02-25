<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\DocumentoModel;
use PDOException;

class DocumentoController {

    private $documentoTipoModel;
    private $logger;

    public function __construct() {
        $this->documentoTipoModel = new DocumentoModel();
        $this->logger = new Logger();
    }

    // CRIAR DOCUMENTO TIPO
    public function novoDocumentoTipo($dados) {
        try {
            $this->documentoTipoModel->criarDocumentoTipo($dados);
            return ['status' => 'success', 'message' => 'Tipo de documento inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O tipo de documento já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('documento_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    // ATUALIZAR DOCUMENTO TIPO
    public function atualizarDocumentoTipo($dados) {
        try {
            $buscaTipo = $this->documentoTipoModel->buscaDocumentoTipo($dados['documento_tipo_id']);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de documento não encontrado'];
            }

            $this->documentoTipoModel->atualizarDocumentoTipo($dados);
            return ['status' => 'success', 'message' => 'Tipo de documento atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('documento_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // LISTAR DOCUMENTOS TIPO
    public function listarDocumentoTipo($gabinete) {
        try {
            $resultado = $this->documentoTipoModel->listarDocumentoTipo($gabinete);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum tipo de documento encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('documento_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // BUSCAR DOCUMENTO TIPO POR ID
    public function buscaDocumentoTipo($id) {
        try {
            $resultado = $this->documentoTipoModel->buscaDocumentoTipo($id);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Tipo de documento não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('documento_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // APAGAR DOCUMENTO TIPO
    public function apagarDocumentoTipo($tipoId) {
        try {
            $buscaTipo = $this->documentoTipoModel->buscaDocumentoTipo($tipoId);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de documento não encontrado'];
            }

            $this->documentoTipoModel->apagarDocumentoTipo($tipoId);
            return ['status' => 'success', 'message' => 'Tipo de documento apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o tipo de documento. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('documento_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
