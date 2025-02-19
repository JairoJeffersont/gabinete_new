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

    //TIPO PESSOA CONTROLLER
    public function novoTipoPessoa($dados) {
        try {
            $this->pessoaModel->criarPessoaTipo($dados);
            return ['status' => 'success', 'message' => 'Tipo de pessoa inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O tipo de pessoa já esta cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('tipo_pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    public function atualizarTipoPessoa($dados) {
        try {
            $buscaTipoPessoa = $this->pessoaModel->buscaPessoaTipo($dados['pessoa_tipo_id']);

            if (!$buscaTipoPessoa) {
                return ['status' => 'not_found', 'message' => 'Tipo de pessoa não encontrado',];
            }

            $this->pessoaModel->atualizarPessoaTipo($dados);
            return ['status' => 'success', 'message' => 'Tipo de pessoa atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('tipo_pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarTiposPessoa() {
        try {
            $resultado = $this->pessoaModel->listarPessoaTipo();

            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum tipo de pessoa encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('tipo_pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function apagarTipoPessoa($id) {
        try {

            $buscaTipoPessoa = $this->pessoaModel->buscaPessoaTipo($id);

            if (!$buscaTipoPessoa) {
                return ['status' => 'not_found', 'message' => 'Tipo de pessoa não encontrado'];
            }

            $this->pessoaModel->apagarPessoaTipo($id);
            return ['status' => 'success', 'message' => 'Tipo de pessoa removido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o tipo de pessoa. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('tipo_pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    //GENERO PESSOA CONTROLLER
    public function novoGeneroPessoa($dados) {
        try {
            $this->pessoaModel->criarPessoaGenero($dados);
            return ['status' => 'success', 'message' => 'Genero de pessoa inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O genero de pessoa já esta cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('genero_pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    public function atualizarGeneroPessoa($dados) {
        try {
            $buscaGeneroPessoa = $this->pessoaModel->buscaPessoaGenero($dados['pessoa_genero_id']);

            if (!$buscaGeneroPessoa) {
                return ['status' => 'not_found', 'message' => 'Genero de pessoa não encontrado',];
            }

            $this->pessoaModel->atualizarPessoaGenero($dados);
            return ['status' => 'success', 'message' => 'Genero de pessoa atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('genero_pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarGenerosPessoa() {
        try {
            $resultado = $this->pessoaModel->listarPessoaGenero();

            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum genero de pessoa encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('genero_pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function apagarGeneroPessoa($id) {
        try {

            $buscaGeneroPessoa = $this->pessoaModel->buscaPessoaGenero($id);

            if (!$buscaGeneroPessoa) {
                return ['status' => 'not_found', 'message' => 'Genero de pessoa não encontrado'];
            }

            $this->pessoaModel->apagarPessoaGenero($id);
            return ['status' => 'success', 'message' => 'Genero de pessoa removido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o genero de pessoa. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('genero_pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    //PROFISSAO PESSOA CONTROLLER
    public function novaProfissaoPessoa($dados) {
        try {
            $this->pessoaModel->criarPessoaProfissao($dados);
            return ['status' => 'success', 'message' => 'Profissão de pessoa inserida com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'A profissão de pessoa já esta cadastrada'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('profissao_pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    public function atualizarProfissaoPessoa($dados) {
        try {
            $buscaProfissaoPessoa = $this->pessoaModel->buscaPessoaProfissao($dados['pessoa_profissao_id']);

            if (!$buscaProfissaoPessoa) {
                return ['status' => 'not_found', 'message' => 'Profissão de pessoa não encontrada',];
            }

            $this->pessoaModel->atualizarPessoaProfissao($dados);
            return ['status' => 'success', 'message' => 'Profissão de pessoa atualizada com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('profissao_pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function listarProfissoesPessoa() {
        try {
            $resultado = $this->pessoaModel->listarPessoaProfissao();

            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhuma profissão de pessoa encontrada'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('profissao_pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function apagarProfissaoPessoa($id) {
        try {

            $buscaProfissaoPessoa = $this->pessoaModel->buscaPessoaProfissao($id);

            if (!$buscaProfissaoPessoa) {
                return ['status' => 'not_found', 'message' => 'Profissão de pessoa não encontrada'];
            }

            $this->pessoaModel->apagarPessoaProfissao($id);
            return ['status' => 'success', 'message' => 'Profissão de pessoa removida com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar a profissão de pessoa. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('profissao_pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    //PESSOA CONTROLLER
    public function novaPessoa($dados) {
        try {
            $this->pessoaModel->criarPessoa($dados);
            return ['status' => 'success', 'message' => 'Pessoa inserida com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'A pessoa já esta cadastrada'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    public function atualizarPessoa($dados) {
        try {

            $buscaPessoa = $this->pessoaModel->buscaPessoa($dados['pessoa_id']);
            if (!$buscaPessoa) {
                return ['status' => 'not_found'];
            }

            $this->pessoaModel->atualizarPessoa($dados);
            return ['status' => 'success', 'message' => 'Pessoa atualizada com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function buscarPessoa($id) {
        try {
            $resultado = $this->pessoaModel->buscaPessoa($id);
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

    public function listarPessoas($itens, $pagina, $ordem, $ordenarPor) {
        try {
            $resultado = $this->pessoaModel->listarPessoa($itens, $pagina, $ordem, $ordenarPor);

            if ($resultado) {
                $total = (isset($resultado[0]['total_pessoa'])) ? $resultado[0]['total_pessoa'] : 0;
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

    public function apagarPessoa($id) {
        try {

            $buscaPessoa = $this->pessoaModel->buscaPessoa($id);

            if (!$buscaPessoa) {
                return ['status' => 'not_found', 'message' => 'Pessoa não encontrada'];
            }

            $this->pessoaModel->apagarPessoa($id);
            return ['status' => 'success', 'message' => 'Pessoa removida com sucesso'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar a pessoa. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
