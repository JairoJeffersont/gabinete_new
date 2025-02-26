<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\FileUploader;
use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\PessoaModel;
use PDOException;

class PessoaController {

    private $pessoaModel;
    private $logger;
    private $fileUpload;
    private $pasta_foto;

    public function __construct() {
        $this->pessoaModel = new PessoaModel();
        $this->logger = new Logger();
        $this->fileUpload = new FileUploader();
        $this->pasta_foto = 'public/arquivos/fotos_pessoas';
    }

    // CRIAR PESSOA
    public function novaPessoa($dados) {
        if (!empty($dados['foto']['tmp_name'])) {
            $uploadResult = $this->fileUpload->uploadFile($this->pasta_foto . '/' . $dados['pessoa_gabinete'], $dados['foto'], ['image/jpg', 'image/jpeg', 'image/png'], 20);

            if ($uploadResult['status'] !== 'success') {
                return $uploadResult;
            }

            $dados['pessoa_foto'] = $uploadResult['file_path'];
        }

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

            if (!empty($dados['foto']['tmp_name'])) {
                $uploadResult = $this->fileUpload->uploadFile($this->pasta_foto . '/' . $dados['pessoa_gabinete'], $dados['foto'], ['image/jpg', 'image/jpeg', 'image/png'], 20);

                if ($uploadResult['status'] !== 'success') {
                    return $uploadResult;
                }
                

                if (!empty($buscaPessoa['pessoa_foto'])) {
                    $this->fileUpload->deleteFile($buscaPessoa['pessoa_foto']);
                }


                $dados['pessoa_foto'] = $uploadResult['file_path'];
            } else {
                $dados['pessoa_foto'] = $buscaPessoa['pessoa_foto'] ?? null;
            }

            unset($dados['foto']);
            unset($dados['pessoa_gabinete']);
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
    public function listarPessoas($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $cliente) {
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
    public function apagarPessoa($pessoa_id) {
        try {
            $pessoa = $this->buscaPessoa('pessoa_id', $pessoa_id);

            if ($pessoa['status'] == 'not_found') {
                return $pessoa;
            }

            if (isset($pessoa['dados']['pessoa_foto'])) {
                $this->fileUpload->deleteFile($pessoa['dados']['pessoa_foto']);
            }

            $this->pessoaModel->apagarPessoa($pessoa_id);
            return ['status' => 'success', 'message' => 'Pessoa apagada com sucesso.'];
        } catch (PDOException $e) {

            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'error', 'status_code' => 400, 'message' => 'Não é possível apagar a pessoa. Existem registros dependentes.'];
            }

            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_log', $e->getMessage() . ' | ' . $erro_id);
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

            if ($buscaTipo['pessoa_tipo_gabinete'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível atualizar um tipo de pessoa padrão dos sistema.'];
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

            if ($buscaTipo['pessoa_tipo_gabinete'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar um tipo de pessoa padrão dos sistema.'];
            }

            $this->pessoaModel->apagarTipoPessoa($tipoId);
            return ['status' => 'successs', 'message' => 'Tipo de pessoa apagado com sucesso'];
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

            if ($buscaProfissao['pessoas_profissoes_gabinete'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível atualizar uma profissão padrão dos sistema.'];
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

            if ($buscaProfissao['pessoas_profissoes_gabinete'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar uma profissão padrão dos sistema.'];
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

    public function buscarAniversarianteMes($mes, $estado, $cliente) {
        try {
            $pessoas = $this->pessoaModel->buscarAniversarianteMes($mes, $estado, $cliente);
            if ($pessoas) {
                return ['status' => 'success', 'dados' => $pessoas];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum aniversáriante para o mês.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
