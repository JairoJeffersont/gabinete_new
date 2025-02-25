<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\DocumentoModel;
use GabineteMvc\Middleware\FileUploader;

use PDOException;

class DocumentoController {

    private $documentoModel;
    private $logger;
    private $fileUpload;
    private $pasta_arquivo;

    public function __construct() {
        $this->documentoModel = new DocumentoModel();
        $this->logger = new Logger();
        $this->fileUpload = new FileUploader();
        $this->pasta_arquivo = 'public/arquivos/documentos';
    }


    // CRIAR DOCUMENTO
    public function novoDocumento($dados) {
        try {

            if (!empty($dados['arquivo']['tmp_name'])) {
                $uploadResult = $this->fileUpload->uploadFile($this->pasta_arquivo . '/' . $dados['documento_gabinete'], $dados['arquivo'], ['application/pdf', 'application/docx', 'application/doc', 'application/xls', 'application/xlsx'], 15);

                if ($uploadResult['status'] !== 'success') {
                    return $uploadResult;
                }

                $dados['documento_arquivo'] = $uploadResult['file_path'];
            }

            $this->documentoModel->criarDocumento($dados);
            return ['status' => 'success', 'message' => 'Documento inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'Documento já cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('documento_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    // ATUALIZAR DOCUMENTO
    public function atualizarDocumento($dados) {
        try {
            $buscaDocumento = $this->documentoModel->buscaDocumento('documento_id', $dados['documento_id']);

            if (!$buscaDocumento) {
                return ['status' => 'not_found', 'message' => 'Documento não encontrado'];
            }

            $this->documentoModel->atualizarDocumento($dados);
            return ['status' => 'success', 'message' => 'Documento atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('documento_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // BUSCAR DOCUMENTO
    public function buscaDocumento($coluna, $valor) {
        try {
            $resultado = $this->documentoModel->buscaDocumento($coluna, $valor);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Documento não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('documento_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // LISTAR DOCUMENTOS
    public function listarDocumentos($ano, $tipo, $busca, $gabinete) {
        try {
            $resultado = $this->documentoModel->listarDocumentos($ano, $tipo, $busca, $gabinete);

            if ($resultado) {
                
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum documento encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('documento_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // APAGAR DOCUMENTO
    public function apagarDocumento($documentoId) {
        try {
            $buscaDocumento = $this->documentoModel->buscaDocumento('documento_id', $documentoId);

            if (!$buscaDocumento) {
                return ['status' => 'not_found', 'message' => 'Documento não encontrado'];
            }

            $this->documentoModel->apagarDocumento($documentoId);
            return ['status' => 'success', 'message' => 'Documento apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o documento. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('documento_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }



    // CRIAR DOCUMENTO TIPO
    public function novoDocumentoTipo($dados) {
        try {
            $this->documentoModel->criarDocumentoTipo($dados);
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
            $buscaTipo = $this->documentoModel->buscaDocumentoTipo($dados['documento_tipo_id']);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de documento não encontrado'];
            }

            if ($buscaTipo['documento_tipo_gabinete'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível atualizar um tipo padrão dos sistema.'];
            }

            $this->documentoModel->atualizarDocumentoTipo($dados);
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
            $resultado = $this->documentoModel->listarDocumentoTipo($gabinete);
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
            $resultado = $this->documentoModel->buscaDocumentoTipo($id);
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
            $buscaTipo = $this->documentoModel->buscaDocumentoTipo($tipoId);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de documento não encontrado'];
            }

            if ($buscaTipo['documento_tipo_gabinete'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar um tipo padrão dos sistema.'];
            }


            $this->documentoModel->apagarDocumentoTipo($tipoId);
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
