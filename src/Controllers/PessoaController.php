<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\FileUploader;
use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\PessoaModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
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

    public function gerarCsv($gabinete) {
        try {
            // Recupera as pessoas
            $result = $this->listarPessoas(10000000, 1, 'asc', 'pessoa_nome', null, null, $gabinete);
            if ($result['status'] == 'success') {
                $buscaPessoas = $result['dados'];

                // Verifica se a busca retornou resultados
                if (empty($buscaPessoas)) {
                    return ['status' => 'error', 'message' => 'Nenhuma pessoa encontrada'];
                }

                // Caminho da pasta onde os arquivos CSV serão salvos
                $pasta = './public/arquivos/csv/' . $gabinete . '/';

                // Cria o diretório se não existir
                if (!is_dir($pasta)) {
                    mkdir($pasta, 0777, true);  // Cria a pasta e garante permissões
                }

                // Nome do arquivo CSV
                $nomeArquivo = 'pessoas_' . date('d_m_y') . '.csv';
                $caminhoArquivo = $pasta . $nomeArquivo;

                // Abre o arquivo para escrita
                $arquivo = fopen($caminhoArquivo, 'w');

                // Campos a serem removidos
                $camposRemover = ['gabinete_nome', 'pessoa_foto', 'pessoa_criada_por', 'pessoa_gabinete', 'pessoa_orgao', 'pessoa_tipo', 'total', 'pessoa_id'];

                // Pega as chaves do primeiro item do array (cabeçalho)
                $cabecalho = array_keys($buscaPessoas[0]);

                // Filtra os campos a serem removidos
                $cabecalho = array_diff($cabecalho, $camposRemover);

                // Adiciona o cabeçalho ao CSV
                fputcsv($arquivo, $cabecalho);

                // Adiciona os dados das pessoas ao CSV
                foreach ($buscaPessoas as $pessoa) {
                    // Formata o campo pessoa_aniversario para d/m
                    if (isset($pessoa['pessoa_aniversario']) && !empty($pessoa['pessoa_aniversario'])) {
                        // Converte para data e formata
                        $dataAniversario = \DateTime::createFromFormat('Y-m-d', $pessoa['pessoa_aniversario']);
                        if ($dataAniversario !== false) {
                            $pessoa['pessoa_aniversario'] = $dataAniversario->format('d/m');
                        }
                    }

                    // Filtra os campos a serem removidos de cada pessoa
                    $pessoa = array_diff_key($pessoa, array_flip($camposRemover));

                    // Adiciona os valores de cada pessoa no CSV
                    fputcsv($arquivo, $pessoa);
                }

                // Fecha o arquivo
                fclose($arquivo);

                // Retorna sucesso com o nome do arquivo
                return ['status' => 'success', 'message' => 'CSV gerado com sucesso', 'file' => $pasta . $nomeArquivo];
            } else {
                return ['status' => 'not_found'];
            }
        } catch (PDOException $e) {
            // Geração do erro
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }


    public function gerarXls($gabinete) {
        try {
            // Recupera as pessoas
            $result = $this->listarPessoas(10000000, 1, 'asc', 'pessoa_nome', null, null, $gabinete);           

            if ($result['status'] == 'success') {

                $buscaPessoas = $result['dados'];
                // Verifica se a busca retornou resultados
                if (empty($buscaPessoas)) {
                    return ['status' => 'error', 'message' => 'Nenhuma pessoa encontrada'];
                }

                // Caminho da pasta onde os arquivos XLS serão salvos
                $pasta = './public/arquivos/xls/' . $gabinete . '/';

                // Cria o diretório se não existir
                if (!is_dir($pasta)) {
                    mkdir($pasta, 0777, true);
                }

                // Nome do arquivo XLS
                $nomeArquivo = 'pessoas_' . date('d_m_y') . '.xls';
                $caminhoArquivo = $pasta . $nomeArquivo;

                // Cria uma nova planilha
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // Campos a serem removidos
                $camposRemover = ['gabinete_nome', 'pessoa_foto', 'pessoa_criada_por', 'pessoa_gabinete', 'pessoa_orgao', 'pessoa_tipo', 'total', 'pessoa_id'];

                // Pega as chaves do primeiro item do array (cabeçalho)
                $cabecalho = array_keys($buscaPessoas[0]);
                $cabecalho = array_diff($cabecalho, $camposRemover);

                // Adiciona o cabeçalho à planilha
                $coluna = 'A';
                foreach ($cabecalho as $titulo) {
                    $sheet->setCellValue($coluna . '1', $titulo);
                    $coluna++;
                }

                // Adiciona os dados das pessoas à planilha
                $linha = 2;
                foreach ($buscaPessoas as $pessoa) {
                    // Formata o campo pessoa_aniversario para d/m
                    if (isset($pessoa['pessoa_aniversario']) && !empty($pessoa['pessoa_aniversario'])) {
                        $dataAniversario = \DateTime::createFromFormat('Y-m-d', $pessoa['pessoa_aniversario']);
                        if ($dataAniversario !== false) {
                            $pessoa['pessoa_aniversario'] = $dataAniversario->format('d/m');
                        }
                    }

                    // Filtra os campos a serem removidos de cada pessoa
                    $pessoa = array_diff_key($pessoa, array_flip($camposRemover));

                    // Preenche os dados na planilha
                    $coluna = 'A';
                    foreach ($pessoa as $valor) {
                        $sheet->setCellValue($coluna . $linha, $valor);
                        $coluna++;
                    }
                    $linha++;
                }

                // Salva o arquivo XLS
                $writer = new Xls($spreadsheet);
                $writer->save($caminhoArquivo);

                return ['status' => 'success', 'message' => 'Excel gerado com sucesso', 'file' => $caminhoArquivo];
            }else{
                return ['status' => 'not_found'];

            }
        } catch (\Exception $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
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
