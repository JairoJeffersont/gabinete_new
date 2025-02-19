<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\GabineteModel;
use PDOException;

class GabineteController {

    private $gabineteModel;
    private $logger;

    public function __construct() {
        $this->gabineteModel = new GabineteModel();
        $this->logger = new Logger();
    }

    //TIPO GABINETE CONTROLLER
    public function novoTipoGabinete($dados) {
        try {
            $this->gabineteModel->criarTipoGabinete($dados);
            return ['status' => 'success', 'message' => 'Tipo de gabinete inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O tipo de gabinete já esta cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('tipo_gabinete_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    public function atualizarTipoGabinete($dados) {
        try {
            $buscaTipoGabinete = $this->gabineteModel->buscaTipoGabinete($dados['tipo_gabinete_id']);

            if (!$buscaTipoGabinete) {
                return ['status' => 'not_found', 'message' => 'Tipo de gabinete não encontrado',];
            }

            $this->gabineteModel->atualizarTipoGabinete($dados);
            return ['status' => 'success', 'message' => 'Tipo de gabinete atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('tipo_gabinete_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarTiposGabinete() {
        try {
            $resultado = $this->gabineteModel->listarTipoGabinete();

            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum tipo de gabinete encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('tipo_gabinete_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscarTipoGabinete($id) {
        try {
            $resultado = $this->gabineteModel->buscaTipoGabinete($id);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Tipo de gabinete não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function apagarTipoGabinete($id) {
        try {

            $buscaCliente = $this->gabineteModel->buscaTipoGabinete($id);

            if (!$buscaCliente) {
                return ['status' => 'not_found', 'message' => 'Tipo de gabinete não encontrado'];
            }

            $this->gabineteModel->apagarTipoGabinete($id);
            return ['status' => 'success', 'message' => 'Tipo de gabinete removido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o tipo de gabinete. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('tipo_gabinete_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    //GABINETE CONTROLLER
    public function novoGabinete($dados) {
        try {
            $this->gabineteModel->criarGabinete($dados);
            return ['status' => 'success', 'message' => 'Gabinete inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O gabinete já esta cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id);
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    public function atualizarGabinete($dados) {
        try {

            $buscaGabinete = $this->gabineteModel->buscaGabinete($dados['gabinete_id']);
            if (!$buscaGabinete) {
                return ['status' => 'not_found'];
            }

            $this->gabineteModel->atualizarGabinete($dados);
            return ['status' => 'success', 'message' => 'Gabinete atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscarGabinete($id) {
        try {
            $resultado = $this->gabineteModel->buscaGabinete($id);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Gabinete não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarGabinetes($itens, $pagina, $ordem, $ordenarPor) {
        try {
            $resultado = $this->gabineteModel->listarGabinete($itens, $pagina, $ordem, $ordenarPor);

            if ($resultado) {
                $total = (isset($resultado[0]['total_gabinete'])) ? $resultado[0]['total_gabinete'] : 0;
                $totalPaginas = ceil($total / $itens);
                return ['status' => 'success', 'total_paginas' => $totalPaginas, 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum gabinete encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function apagarGabinete($id) {
        try {

            $buscaGabinete = $this->gabineteModel->buscaGabinete($id);

            if (!$buscaGabinete) {
                return ['status' => 'not_found', 'message' => 'Gabinete não encontrado'];
            }

            $this->gabineteModel->apagarGabinete($id);
            return ['status' => 'success', 'message' => 'Gabinete removido com sucesso'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o cliente. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
