<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\PessoaModel;
use PDOException;

class PessoaController {

    private $pessoaModel;
    private $logger;

    public function __construct() {
        $this->pessoaModel = new PessoaModel();
        $this->logger = new Logger();
    }

    // CRIAR PESSOA
    public function novaPessoa($dados) {
        try {
            $this->pessoaModel->criarPessoa($dados);
            return ['status' => 'success', 'message' => 'Pessoa inserida com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'A pessoa já está cadastrada'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    // ATUALIZAR PESSOA
    public function atualizarPessoa($dados) {
        try {
            $buscaPessoa = $this->pessoaModel->buscaPessoa('pessoa_id', $dados['pessoa_id']);

            if (!$buscaPessoa) {
                return ['status' => 'not_found', 'message' => 'Pessoa não encontrada'];
            }

            $this->pessoaModel->atualizarPessoa($dados);
            return ['status' => 'success', 'message' => 'Pessoa atualizada com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // BUSCAR PESSOA
    public function buscaPessoa($coluna, $valor) {
        try {
            $resultado = $this->pessoaModel->buscaPessoa($coluna, $valor);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Pessoa não encontrada'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // LISTAR PESSOAS
    public function listarPessoas($itens, $pagina, $ordem, $ordenarPor, $termo = null, $estado = null, $cliente = null) {
        try {
            $resultado = $this->pessoaModel->listar($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $cliente);

            if ($resultado) {
                $total = (isset($resultado[0]['total'])) ? $resultado[0]['total'] : 0;
                $totalPaginas = ceil($total / $itens);
                return ['status' => 'success', 'total_paginas' => $totalPaginas, 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhuma pessoa encontrada'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // APAGAR PESSOA
    public function apagarPessoa($pessoaId) {
        try {
            $buscaPessoa = $this->pessoaModel->buscaPessoa('pessoa_id', $pessoaId);

            if (!$buscaPessoa) {
                return ['status' => 'not_found', 'message' => 'Pessoa não encontrada'];
            }

            $this->pessoaModel->apagarPessoa($pessoaId);
            return ['status' => 'success', 'message' => 'Pessoa apagada com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar a pessoa. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // CRIAR TIPO DE PESSOA
    public function novoTipoPessoa($dados) {
        try {
            $this->pessoaModel->criarTipoPessoa($dados);
            return ['status' => 'success', 'message' => 'Tipo de pessoa inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O tipo de pessoa já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('pessoa_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    // ATUALIZAR TIPO DE PESSOA
    public function atualizarTipoPessoa($dados) {
        try {
            $buscaTipo = $this->pessoaModel->buscaTipoPessoa($dados['pessoa_tipo_id']);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de pessoa não encontrado'];
            }

            $this->pessoaModel->atualizarTipoPessoa($dados);
            return ['status' => 'success', 'message' => 'Tipo de pessoa atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // LISTAR TIPOS DE PESSOA
    public function listarTiposPessoa($pessoa_tipo_gabinete) {
        try {
            $resultado = $this->pessoaModel->listarTiposPessoa($pessoa_tipo_gabinete);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum tipo de pessoa encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // BUSCAR TIPO DE PESSOA POR ID
    public function buscaTipoPessoa($id) {
        try {
            $resultado = $this->pessoaModel->buscaTipoPessoa($id);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Tipo de pessoa não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // APAGAR TIPO DE PESSOA
    public function apagarTipoPessoa($tipoId) {
        try {
            $buscaTipo = $this->pessoaModel->buscaTipoPessoa($tipoId);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de pessoa não encontrado'];
            }

            $this->pessoaModel->apagarTipoPessoa($tipoId);
            return ['status' => 'success', 'message' => 'Tipo de pessoa apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o tipo de pessoa. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // CRIAR PROFISSÃO DE PESSOA
    public function novaProfissaoPessoa($dados) {
        try {
            $this->pessoaModel->criarProfissaoPessoa($dados);
            return ['status' => 'success', 'message' => 'Profissão de pessoa inserida com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'A profissão de pessoa já está cadastrada'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('pessoa_profissao_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    // ATUALIZAR PROFISSÃO DE PESSOA
    public function atualizarProfissaoPessoa($dados) {
        try {
            $buscaProfissao = $this->pessoaModel->buscaProfissaoPessoa($dados['pessoas_profissoes_id']);

            if (!$buscaProfissao) {
                return ['status' => 'not_found', 'message' => 'Profissão de pessoa não encontrada'];
            }

            $this->pessoaModel->atualizarProfissaoPessoa($dados);
            return ['status' => 'success', 'message' => 'Profissão de pessoa atualizada com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_profissao_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // LISTAR PROFISSÕES DE PESSOA
    public function listarProfissoesPessoa($pessoas_profissoes_gabinete) {
        try {
            $resultado = $this->pessoaModel->listarProfissoesPessoa($pessoas_profissoes_gabinete);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhuma profissão de pessoa encontrada'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_profissao_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // BUSCAR PROFISSÃO DE PESSOA POR ID
    public function buscaProfissaoPessoa($id) {
        try {
            $resultado = $this->pessoaModel->buscaProfissaoPessoa($id);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Profissão de pessoa não encontrada'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_profissao_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // APAGAR PROFISSÃO DE PESSOA
    public function apagarProfissaoPessoa($profissaoId) {
        try {
            $buscaProfissao = $this->pessoaModel->buscaProfissaoPessoa($profissaoId);

            if (!$buscaProfissao) {
                return ['status' => 'not_found', 'message' => 'Profissão de pessoa não encontrada'];
            }

            $this->pessoaModel->apagarProfissaoPessoa($profissaoId);
            return ['status' => 'success', 'message' => 'Profissão de pessoa apagada com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar a profissão de pessoa. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_profissao_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}