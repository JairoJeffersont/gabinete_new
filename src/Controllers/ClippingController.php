<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\FileUploader;
use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\ClippingModel;
use PDOException;

class ClippingController {

    private $clippingModel;
    private $logger;
    private $fileUploader;
    private $pasta_arquivo;

    public function __construct() {
        $this->clippingModel = new ClippingModel();
        $this->logger = new Logger();
        $this->fileUploader = new FileUploader();
        $this->pasta_arquivo = 'public/arquivos/clippings';
    }


    public function criarClipping($dados)
    {
        
        if (!empty($dados['arquivo']['tmp_name'])) {
            $uploadResult = $this->fileUploader->uploadFile($this->pasta_arquivo . '/' . $dados['clipping_gabinete'], $dados['arquivo'], ['image/pdf', 'image/png', 'image/jpg', 'image/jpeg'], 20);

            if ($uploadResult['status'] !== 'success') {
                return $uploadResult;
            }

            $dados['clipping_arquivo'] = $uploadResult['file_path'];
        }

        try {
            $this->clippingModel->criarClipping($dados);
            return ['status' => 'success', 'message' => 'Clipping criado com sucesso.'];
        } catch (PDOException $e) {
            if (!empty($dados['arquivo']['tmp_name'])) {
                $this->fileUploader->deleteFile($dados['clipping_arquivo'] ?? null);
            }

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'Esse clipping já está cadastrado.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('clipping_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarClippings($busca, $ano, $cliente)
    {
        try {
            $result = $this->clippingModel->listarClipping($busca, $ano, $cliente);

            if (empty($result)) {
                return ['status' => 'empty', 'message' => 'Nenhum clipping encontrado.'];
            }

            return ['status' => 'success', 'dados' => $result];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('clipping_error', 'ID do erro: ' . $erro_id . ' | ' . $e->getMessage());
            return ['status' => 'error', 'status_code' => 500, 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscarClipping($coluna, $valor)
    {
        try {
            $clipping = $this->clippingModel->buscarClipping($coluna, $valor);
            if ($clipping) {
                return ['status' => 'success', 'dados' => $clipping];
            } else {
                return ['status' => 'not_found', 'message' => 'Clipping não encontrado.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('clipping_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }


    public function atualizarClipping($clipping_id, $dados)
    {
       

        $clipping = $this->buscarClipping('clipping_id', $clipping_id);

        if ($clipping['status'] == 'not_found') {
            return $clipping;
        }

        if (!empty($dados['arquivo']['tmp_name'])) {
            $uploadResult = $this->fileUploader->uploadFile($this->pasta_arquivo . '/' . $dados['clipping_cliente'], $dados['arquivo'], ['pdf', 'png', 'jpg', 'jpeg'], 5);

            if ($uploadResult['status'] !== 'success') {
                return $uploadResult;
            }

            if (!empty($clipping['dados']['clipping_arquivo'])) {
                $this->fileUploader->deleteFile($clipping['dados']['clipping_arquivo']);
            }

            $dados['clipping_arquivo'] = $uploadResult['file_path'];
        } else {
            $dados['clipping_arquivo'] = $clipping['dados']['clipping_arquivo'] ?? null;
        }

        try {
            $this->clippingModel->atualizarClipping($clipping_id, $dados);
            return ['status' => 'success', 'message' => 'Clipping atualizado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('clipping_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }


    public function apagarClipping($clipping_id)
    {
        try {
            $clipping = $this->buscarClipping('clipping_id', $clipping_id);

            if ($clipping['status'] == 'not_found') {
                return $clipping;
            }

            if (isset($clipping['dados']['clipping_arquivo'])) {
                $this->fileUploader->deleteFile($clipping['dados']['clipping_arquivo']);
            }

            $this->clippingModel->apagarClipping($clipping_id);
            return ['status' => 'success', 'message' => 'Clipping apagado com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('clipping_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // CRIAR NOVO TIPO DE CLIPPING
    public function novoClippingTipo($dados) {
        try {

            if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 2) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para criar tipos de clipping.'];
            }

            $this->clippingModel->criarClippingTipo($dados);
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
            $buscaTipo = $this->clippingModel->buscaClippingTipo($dados['clipping_tipo_id']);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de clipping não encontrado'];
            }

            if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 2) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para atualizar tipos de clipping.'];
            }

            if ($buscaTipo['clipping_tipo_criado_por'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível atualizar um tipos de clipping padrão do sistema.'];
            }

            $this->clippingModel->atualizarClippingTipo($dados);
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
            $resultado = $this->clippingModel->listarClippingTipos($clipping_tipo_gabinete);
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
            $resultado = $this->clippingModel->buscaClippingTipo($id);
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
            $buscaTipo = $this->clippingModel->buscaClippingTipo($tipoId);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de clipping não encontrado'];
            }

            if ($buscaTipo['clipping_tipo_criado_por'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar um tipos de clipping padrão do sistema.'];
            }

            if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 2) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para apagar tipos de clipping.'];
            }

            $this->clippingModel->apagarClippingTipo($tipoId);
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
