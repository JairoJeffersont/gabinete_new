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

    // GABINETE CONTROLLER
    public function novoGabinete($dados) {

        $camposObrigatorios = ['gabinete_usuarios', 'gabinete_usuarios', 'gabinete_nome', 'gabinete_nome_sistema', 'gabinete_estado', 'gabinete_tipo'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }        

        try {
            $this->gabineteModel->criarGabinete($dados);
            return ['status' => 'success', 'message' => 'Gabinete inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O gabinete já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    public function atualizarGabinete($dados) {
        try {

            $buscaGabinete = $this->gabineteModel->buscaGabinete('gabinete_id', $dados['gabinete_id']);

            if (!$buscaGabinete) {
                return ['status' => 'not_found', 'message' => 'Gabinete não encontrado'];
            }

            $camposObrigatorios = ['gabinete_nome', 'gabinete_email', 'gabinete_telefone', 'gabinete_usuarios', 'gabinete_estado', 'gabinete_tipo'];

            foreach ($camposObrigatorios as $campo) {
                if (!isset($dados[$campo])) {
                    return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
                }
            }

            if (!filter_var($dados['gabinete_email'], FILTER_VALIDATE_EMAIL)) {
                return ['status' => 'invalid_email', 'message' => 'Email inválido.'];
            }

            $this->gabineteModel->atualizarGabinete($dados);
            return ['status' => 'success', 'message' => 'Gabinete atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscaGabinete($id) {
        try {
            $resultado = $this->gabineteModel->buscaGabinete('gabinete_id', $id);
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

    public function apagarGabinete($gabineteId) {
        try {
            $buscaGabinete = $this->gabineteModel->buscaGabinete('gabinete_id', $gabineteId);

            if (!$buscaGabinete) {
                return ['status' => 'not_found', 'message' => 'Gabinete não encontrado'];
            }

            $this->gabineteModel->apagarGabinete($gabineteId);
            return ['status' => 'success', 'message' => 'Gabinete apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o gabinete. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // TIPO GABINETE CONTROLLER
    public function novoTipoGabinete($dados) {

        $camposObrigatorios = ['gabinete_tipo_nome', 'gabinete_tipo_informacoes'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($dados[$campo])) {
                return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
            }
        }

        try {
            $this->gabineteModel->criarTipoGabinete($dados);
            return ['status' => 'success', 'message' => 'Tipo de gabinete inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O tipo de gabinete já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    public function atualizarTipoGabinete($dados) {
        try {

            $buscaTipo = $this->gabineteModel->buscaTipoGabinete($dados['gabinete_tipo_id']);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de gabinete não encontrado'];
            }

            $camposObrigatorios = ['gabinete_tipo_nome', 'gabinete_tipo_informacoes'];

            foreach ($camposObrigatorios as $campo) {
                if (!isset($dados[$campo])) {
                    return ['status' => 'bad_request', 'message' => "O campo '$campo' é obrigatório."];
                }
            }

            $this->gabineteModel->atualizarTipoGabinete($dados);
            return ['status' => 'success', 'message' => 'Tipo de gabinete atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarTipoGabinete() {
        try {
            $resultado = $this->gabineteModel->listarTipoGabinete();
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum tipo de gabinete encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscaTipoGabinete($id) {
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

    public function apagarTipoGabinete($tipoId) {
        try {
            $buscaTipo = $this->gabineteModel->buscaTipoGabinete($tipoId);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de gabinete não encontrado'];
            }

            $this->gabineteModel->apagarTipoGabinete($tipoId);
            return ['status' => 'success', 'message' => 'Tipo de gabinete apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o tipo de gabinete. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('gabinete_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
