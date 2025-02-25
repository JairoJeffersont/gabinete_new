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

    public function criarPostagem($dados) {

        try {
            $dados['postagem_pasta'] = './public/arquivos/postagens/' . $dados['postagem_gabinete'] . '/' . uniqid();

            if (!is_dir($dados['postagem_pasta'])) {
                mkdir($dados['postagem_pasta'], 0777, true);
            }

            if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 2) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para criar postagens.'];
            }

            $this->postagemModel->criar($dados);
            return ['status' => 'success', 'message' => 'Postagem criada com sucesso.'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'Já existe uma postagem com este título.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('postagem_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function atualizarPostagem($postagem_id, $dados) {


        $postagem = $this->buscarPostagem('postagem_id', $postagem_id);

        if ($postagem['status'] == 'not_found') {
            return $postagem;
        }

        if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 2) {
            return ['status' => 'forbidden', 'message' => 'Você não tem autorização para editar postagens.'];
        }

        try {
            $this->postagemModel->atualizar($postagem_id, $dados);
            return ['status' => 'success', 'message' => 'Postagem atualizada com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('postagem_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarPostagens($itens, $pagina, $ordem, $ordenarPor, $situacao, $ano, $cliente) {
        try {
            $postagens = $this->postagemModel->listar($itens, $pagina, $ordem, $ordenarPor, $situacao, $ano, $cliente);

            $total = (isset($postagens[0]['total'])) ? $postagens[0]['total'] : 0;
            $totalPaginas = ceil($total / $itens);

            if (empty($postagens)) {
                return ['status' => 'not_found', 'message' => 'Nenhuma postagem registrada.'];
            }

            return ['status' => 'success', 'message' => count($postagens) . ' postagem(s) encontrada(s)', 'dados' => $postagens, 'total_paginas' => $totalPaginas,];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('postagem_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscarPostagem($coluna, $valor) {

        try {
            $postagem = $this->postagemModel->buscar($coluna, $valor);
            if ($postagem) {
                return ['status' => 'success', 'dados' => $postagem];
            } else {
                return ['status' => 'not_found', 'message' => 'Postagem não encontrada.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('postagem_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function apagarPostagem($postagem_id) {
        try {
            $postagem = $this->buscarPostagem('postagem_id', $postagem_id);

            if ($postagem['status'] == 'not_found') {
                return $postagem;
            }

            if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 2) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para apagar postagens.'];
            }

            $pasta = $postagem['dados']['postagem_pasta'];

            if (is_dir($pasta)) {
                $files = array_diff(scandir($pasta), ['.', '..']);
                foreach ($files as $file) {
                    unlink($pasta . DIRECTORY_SEPARATOR . $file);
                }
                rmdir($pasta);
            }

            $this->postagemModel->apagar($postagem_id);
            return ['status' => 'success', 'message' => 'Postagem apagada com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 400, 'message' => 'Não é possível apagar a postagem. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('postagem_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // CRIAR NOVO STATUS DE POSTAGEM
    public function novoPostagemStatus($dados) {

        if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 2) {
            return ['status' => 'forbidden', 'message' => 'Você não tem autorização para criar status de postagens.'];
        }

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

            if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 2) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para atualizar status de postagens.'];
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

            return ['status' => 'forbidden', 'message' => 'Você não tem autorização para apagar status de postagens.'];


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
