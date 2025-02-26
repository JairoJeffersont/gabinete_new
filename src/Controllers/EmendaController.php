<?php

namespace GabineteMvc\Controllers;

use GabineteMvc\Middleware\Logger;
use GabineteMvc\Models\EmendaModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

use PDOException;

class EmendaController {

    private $emendaModel;
    private $logger;

    public function __construct() {
        $this->emendaModel = new EmendaModel();
        $this->logger = new Logger();
    }


    public function criarEmenda($dados) {

        if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 5) {
            return ['status' => 'forbidden', 'message' => 'Você não tem autorização para criar emendas.'];
        }

        try {
            $this->emendaModel->criar($dados);
            return ['status' => 'success', 'message' => 'Emenda inserida com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function atualizarEmenda($emenda_id, $dados) {

        $emenda = $this->buscarEmenda('emenda_id', $emenda_id);

        if ($emenda['status'] == 'not_found') {
            return $emenda;
        }

        if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 5) {
            return ['status' => 'forbidden', 'message' => 'Você não tem autorização para editar emendas.'];
        }

        try {
            $this->emendaModel->atualizar($emenda_id, $dados);
            return ['status' => 'success', 'message' => 'Emenda atualizada com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }


    public function listarEmendas($itens, $pagina, $ordem, $ordenarPor, $status, $tipo, $objetivo, $ano, $estado, $municipio,  $cliente) {
        try {
            $emendas = $this->emendaModel->listar($itens, $pagina, $ordem, $ordenarPor, $status, $tipo, $objetivo, $ano, $estado, $municipio,  $cliente);


            $total = (isset($emendas[0]['total'])) ? $emendas[0]['total'] : 0;
            $totalPaginas = ceil($total / $itens);

            if (empty($emendas)) {
                return ['status' => 'empty', 'message' => 'Nenhuma emenda registrada'];
            }

            return ['status' => 'success', 'message' => count($emendas) . ' emenda(s) encontrada(s)', 'dados' => $emendas, 'total_paginas' => $totalPaginas];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }


    public function buscarEmenda($coluna, $valor) {

        try {
            $emenda = $this->emendaModel->buscar($coluna, $valor);
            if ($emenda) {
                return ['status' => 'success', 'dados' => $emenda];
            } else {
                return ['status' => 'not_found', 'message' => 'Emenda não encontrada.'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    public function apagarEmenda($emenda_id) {
        try {
            $emenda = $this->buscarEmenda('emenda_id', $emenda_id);

            if ($emenda['status'] == 'not_found') {
                return $emenda;
            }

            if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 5) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para apagar emendas.'];
            }

            $this->emendaModel->apagar($emenda_id);
            return ['status' => 'success', 'message' => 'Emenda apagada com sucesso.'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }


    public function gerarCsv($itens, $pagina, $ordem, $ordenarPor, $statusGet, $tipoGet, $objetivoGet, $anoGet, $estadoGet, $municipioGet, $gabinete) {
        try {
            // Recupera as pessoas
            $result = $this->listarEmendas(intval(10000), 1,  $ordem, $ordenarPor, $statusGet, $tipoGet, $objetivoGet, $anoGet, $estadoGet, $municipioGet, $gabinete);

            

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
                $camposRemover = ['emenda_criado_por', 'emenda_gabinete',  'emenda_tipo', 'emenda_objetivo', 'emenda_status', 'emenda_id', 'total'];

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

    public function gerarXls($itens, $pagina, $ordem, $ordenarPor, $statusGet, $tipoGet, $objetivoGet, $anoGet, $estadoGet, $municipioGet, $gabinete) {
        try {
            // Recupera as pessoas
            $result = $this->listarEmendas(intval(10000), 1,  $ordem, $ordenarPor, $statusGet, $tipoGet, $objetivoGet, $anoGet, $estadoGet, $municipioGet, $gabinete);
            
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
                $camposRemover = ['emenda_criado_por', 'emenda_gabinete',  'emenda_tipo', 'emenda_objetivo', 'emenda_status', 'emenda_id', 'total'];

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



    // CRIAR EMENDA STATUS
    public function novoEmendaStatus($dados) {

        if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 5) {
            return ['status' => 'forbidden', 'message' => 'Você não tem autorização para criar status de emendas.'];
        }

        try {
            $this->emendaModel->criarEmendaStatus($dados);
            return ['status' => 'success', 'message' => 'Status de emenda inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O status de emenda já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('emenda_status_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    // ATUALIZAR EMENDA STATUS
    public function atualizarEmendaStatus($dados) {
        try {
            $buscaStatus = $this->emendaModel->buscaEmendaStatus($dados['emendas_status_id']);

            if (!$buscaStatus) {
                return ['status' => 'not_found', 'message' => 'Status de emenda não encontrado'];
            }

            if ($buscaStatus['emendas_status_gabinete'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível atualizar um status padrão do sistema.'];
            }

            if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 5) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para criar status de emendas.'];
            }

            $this->emendaModel->atualizarEmendaStatus($dados);
            return ['status' => 'success', 'message' => 'Status de emenda atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_status_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // LISTAR EMENDA STATUS
    public function listarEmendaStatus($gabinete) {
        try {
            $resultado = $this->emendaModel->listarEmendaStatus($gabinete);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum status de emenda encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_status_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // BUSCAR EMENDA STATUS POR ID
    public function buscaEmendaStatus($id) {
        try {
            $resultado = $this->emendaModel->buscaEmendaStatus($id);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Status de emenda não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_status_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // APAGAR EMENDA STATUS
    public function apagarEmendaStatus($statusId) {
        try {
            $buscaStatus = $this->emendaModel->buscaEmendaStatus($statusId);

            if (!$buscaStatus) {
                return ['status' => 'not_found', 'message' => 'Status de emenda não encontrado'];
            }

            if ($buscaStatus['emendas_status_gabinete'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar um status padrão do sistema.'];
            }

            if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 5) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para apagar status de emendas.'];
            }

            $this->emendaModel->apagarEmendaStatus($statusId);
            return ['status' => 'success', 'message' => 'Status de emenda apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o status de emenda. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_status_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // CRIAR EMENDA OBJETIVO
    public function novoEmendaObjetivo($dados) {

        if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 5) {
            return ['status' => 'forbidden', 'message' => 'Você não tem autorização para criar objetivos de emendas.'];
        }

        try {
            $this->emendaModel->criarEmendaObjetivo($dados);
            return ['status' => 'success', 'message' => 'Objetivo de emenda inserido com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'O objetivo de emenda já está cadastrado'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('emenda_objetivo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }

    // ATUALIZAR EMENDA OBJETIVO
    public function atualizarEmendaObjetivo($dados) {
        try {
            $buscaObjetivo = $this->emendaModel->buscaEmendaObjetivo($dados['emendas_objetivos_id']);

            if (!$buscaObjetivo) {
                return ['status' => 'not_found', 'message' => 'Objetivo de emenda não encontrado'];
            }

            if ($buscaObjetivo['emendas_objetivos_gabinete'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível atualizar um objetivo padrão do sistema.'];
            }

            if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 5) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para atualizar objetivos de emendas.'];
            }

            $this->emendaModel->atualizarEmendaObjetivo($dados);
            return ['status' => 'success', 'message' => 'Objetivo de emenda atualizado com sucesso'];
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_objetivo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // LISTAR EMENDA OBJETIVO
    public function listarEmendaObjetivo($gabinete) {
        try {
            $resultado = $this->emendaModel->listarEmendaObjetivo($gabinete);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Nenhum objetivo de emenda encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_objetivo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // BUSCAR EMENDA OBJETIVO POR ID
    public function buscaEmendaObjetivo($id) {
        try {
            $resultado = $this->emendaModel->buscaEmendaObjetivo($id);
            if ($resultado) {
                return ['status' => 'success', 'dados' => $resultado];
            } else {
                return ['status' => 'not_found', 'message' => 'Objetivo de emenda não encontrado'];
            }
        } catch (PDOException $e) {
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_objetivo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }

    // APAGAR EMENDA OBJETIVO
    public function apagarEmendaObjetivo($objetivoId) {
        try {
            $buscaObjetivo = $this->emendaModel->buscaEmendaObjetivo($objetivoId);

            if (!$buscaObjetivo) {
                return ['status' => 'not_found', 'message' => 'Objetivo de emenda não encontrado'];
            }

            if ($buscaObjetivo['emendas_objetivos_gabinete'] == 1) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar um objetivo padrão do sistema.'];
            }

            if ($_SESSION['usuario_tipo'] != 2 && $_SESSION['usuario_tipo'] != 5) {
                return ['status' => 'forbidden', 'message' => 'Você não tem autorização para apagar objetivos de emendas.'];
            }

            $this->emendaModel->apagarEmendaObjetivo($objetivoId);
            return ['status' => 'success', 'message' => 'Objetivo de emenda apagado com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
                return ['status' => 'forbidden', 'message' => 'Não é possível apagar o objetivo de emenda. Existem registros dependentes.'];
            }
            $erro_id = uniqid();
            $this->logger->novoLog('emenda_objetivo_log', $e->getMessage() . ' | ' . $erro_id);
            return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
        }
    }
}
