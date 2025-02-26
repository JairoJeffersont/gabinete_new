<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\OrgaoModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PDOException;

class OrgaoController {

    private $orgaoModel;
    private $logger;

    public function __construct() {
        $this->orgaoModel = new OrgaoModel();
        $this->logger = new Logger();
    }

    // CRIAR ÓRGÃO
    public function novoOrgao($dados) {
        try {
            $this->orgaoModel->criarOrgao($dados);
            return ['status' => 'success', 'message' => 'Órgão inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O órgão já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('orgao_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    // ATUALIZAR ÓRGÃO
    public function atualizarOrgao($dados) {
        try {
            $buscaOrgao = $this->orgaoModel->buscaOrgao('orgao_id', $dados['orgao_id']);

            if (!$buscaOrgao) {
                return ['status' => 'not_found', 'message' => 'Órgão não encontrado'];
            }

            $this->orgaoModel->atualizarOrgao($dados);
            return ['status' => 'success', 'message' => 'Órgão atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // BUSCAR ÓRGÃO
    public function buscaOrgao($coluna, $valor) {
        try {
            $resultado = $this->orgaoModel->buscaOrgao($coluna, $valor);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Órgão não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // LISTAR ÓRGÃOS
    public function listarOrgaos($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $gabinete) {
        try {
            $resultado = $this->orgaoModel->listar($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $gabinete);

            if ($resultado) {
                $total = (isset($resultado[0]['total'])) ? $resultado[0]['total'] : 0;
                $totalPaginas = ceil($total / $itens);
                return ['status' => 'success', 'total_paginas' => $totalPaginas, 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum órgão encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // APAGAR ÓRGÃO
    public function apagarOrgao($orgaoId) {
        try {
            $buscaOrgao = $this->orgaoModel->buscaOrgao('orgao_id', $orgaoId);

            if (!$buscaOrgao) {
                return ['status' => 'not_found', 'message' => 'Órgão não encontrado'];
            }

            $this->orgaoModel->apagarOrgao($orgaoId);
            return ['status' => 'success', 'message' => 'Órgão apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o órgão. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function gerarCsv($gabinete) {
        try {
            // Recupera as pessoas
            $result = $this->listarOrgaos(10000000, 1, 'asc', 'orgao_nome', null, null, $gabinete);
            if ($result['status'] == 'success') {
                $buscaPessoas = $result['dados'];

                // Verifica se a busca retornou resultados
                if (empty($buscaPessoas)) {
                    return ['status' => 'error', 'message' => 'Nenhum órgão encontrado'];
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
                $camposRemover = ['orgao_criado_por', 'orgao_gabinete',  'orgao_tipo', 'total', 'orgao_id'];

                // Pega as chaves do primeiro item do array (cabeçalho)
                $cabecalho = array_keys($buscaPessoas[0]);

                // Filtra os campos a serem removidos do cabeçalho
                $cabecalhoFiltrado = array_diff($cabecalho, $camposRemover);

                // Adiciona o cabeçalho ao CSV
                fputcsv($arquivo, $cabecalhoFiltrado);

                // Adiciona os dados das pessoas ao CSV
                foreach ($buscaPessoas as $pessoa) {
                    // Filtra os dados da pessoa removendo as chaves desnecessárias
                    $pessoaFiltrada = array_diff_key($pessoa, array_flip($camposRemover));
                    fputcsv($arquivo, $pessoaFiltrada);
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
            $result = $this->listarOrgaos(10000000, 1, 'asc', 'orgao_nome', null, null, $gabinete);

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
                $camposRemover = ['orgao_criado_por', 'orgao_gabinete',  'orgao_tipo', 'total', 'orgao_id'];

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
            } else {
                return ['status' => 'not_found'];
            }
        } catch (\Exception $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('pessoa_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }


    // CRIAR NOVO TIPO DE ÓRGÃO
    public function novoOrgaoTipo($dados) {
        try {
            $this->orgaoModel->criarOrgaoTipo($dados);
            return ['status' => 'success', 'message' => 'Tipo de órgão inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O tipo de órgão já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('orgao_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    // ATUALIZAR TIPO DE ÓRGÃO
    public function atualizarOrgaoTipo($dados) {
        try {
            $buscaTipo = $this->orgaoModel->buscaOrgaoTipo($dados['orgao_tipo_id']);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de órgão não encontrado'];
            }

            if ($buscaTipo['orgao_tipo_criado_por'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível atualizar um tipo de órgão padrão dos sistema.'];
            }

            $this->orgaoModel->atualizarOrgaoTipo($dados);
            return ['status' => 'success', 'message' => 'Tipo de órgão atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // LISTAR TIPOS DE ÓRGÃOS
    public function listarOrgaosTipos($orgao_tipo_gabinete) {
        try {
            $resultado = $this->orgaoModel->listarOrgaosTipos($orgao_tipo_gabinete);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum tipo de órgão encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // BUSCAR TIPO DE ÓRGÃO PELO ID
    public function buscaOrgaoTipo($id) {
        try {
            $resultado = $this->orgaoModel->buscaOrgaoTipo($id);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Tipo de órgão não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // APAGAR TIPO DE ÓRGÃO
    public function apagarOrgaoTipo($tipoId) {
        try {
            $buscaTipo = $this->orgaoModel->buscaOrgaoTipo($tipoId);

            if (!$buscaTipo) {
                return ['status' => 'not_found', 'message' => 'Tipo de órgão não encontrado'];
            }

            if ($buscaTipo['orgao_tipo_criado_por'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar um tipo de órgão padrão dos sistema.'];
            }

            $this->orgaoModel->apagarOrgaoTipo($tipoId);
            return ['status' => 'success', 'message' => 'Tipo de órgão apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o tipo de órgão. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('orgao_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
