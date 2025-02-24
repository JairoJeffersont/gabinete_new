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


    public function inserirTiposPessoas($usuario, $gabinete) {
        $pessoasTipos = [
            ['pessoa_tipo_id' => 1, 'pessoa_tipo_nome' => 'Sem tipo definido', 'pessoa_tipo_descricao' => 'Sem tipo definido'],
            ['pessoa_tipo_id' => 2, 'pessoa_tipo_nome' => 'Familiares', 'pessoa_tipo_descricao' => 'Familiares do deputado'],
            ['pessoa_tipo_id' => 3, 'pessoa_tipo_nome' => 'Empresários', 'pessoa_tipo_descricao' => 'Donos de empresa'],
            ['pessoa_tipo_id' => 4, 'pessoa_tipo_nome' => 'Eleitores', 'pessoa_tipo_descricao' => 'Eleitores em geral'],
            ['pessoa_tipo_id' => 5, 'pessoa_tipo_nome' => 'Imprensa', 'pessoa_tipo_descricao' => 'Jornalistas, diretores de jornais, assessoria'],
            ['pessoa_tipo_id' => 6, 'pessoa_tipo_nome' => 'Site', 'pessoa_tipo_descricao' => 'Pessoas registradas no site'],
            ['pessoa_tipo_id' => 7, 'pessoa_tipo_nome' => 'Amigos', 'pessoa_tipo_descricao' => 'Amigos pessoais do deputado'],
            ['pessoa_tipo_id' => 8, 'pessoa_tipo_nome' => 'Autoridades', 'pessoa_tipo_descricao' => 'Autoridades públicas']
        ];


        try {
            foreach ($pessoasTipos as $tipo) {
                $tipo['pessoa_tipo_criado_por'] = $usuario;
                $tipo['pessoa_tipo_gabinete'] = $gabinete;
                $this->pessoaModel->criarTipoPessoa($tipo);
            }
            return ['status' => 'success', 'message' => 'Tipos padrões de pessoas inseridos com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'Alguns tipos de pessoas já estão cadastrados'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('orgao_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
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

    public function inserirTiposProfissoes($usuario, $gabinete) {
        $pessoasProfissoes = [
            ['pessoas_profissoes_id' => 2, 'pessoas_profissoes_nome' => 'Médico', 'pessoas_profissoes_descricao' => 'Profissional responsável por diagnosticar e tratar doenças'],
            ['pessoas_profissoes_id' => 3, 'pessoas_profissoes_nome' => 'Engenheiro de Software', 'pessoas_profissoes_descricao' => 'Profissional especializado em desenvolvimento e manutenção de sistemas de software'],
            ['pessoas_profissoes_id' => 4, 'pessoas_profissoes_nome' => 'Advogado', 'pessoas_profissoes_descricao' => 'Profissional que oferece consultoria e representação legal'],
            ['pessoas_profissoes_id' => 5, 'pessoas_profissoes_nome' => 'Professor', 'pessoas_profissoes_descricao' => 'Profissional responsável por ministrar aulas e orientar estudantes'],
            ['pessoas_profissoes_id' => 6, 'pessoas_profissoes_nome' => 'Enfermeiro', 'pessoas_profissoes_descricao' => 'Profissional da saúde que cuida e monitoriza pacientes'],
            ['pessoas_profissoes_id' => 7, 'pessoas_profissoes_nome' => 'Arquiteto', 'pessoas_profissoes_descricao' => 'Profissional que projeta e planeja edifícios e espaços urbanos'],
            ['pessoas_profissoes_id' => 8, 'pessoas_profissoes_nome' => 'Contador', 'pessoas_profissoes_descricao' => 'Profissional que gerencia contas e prepara relatórios financeiros'],
            ['pessoas_profissoes_id' => 9, 'pessoas_profissoes_nome' => 'Designer Gráfico', 'pessoas_profissoes_descricao' => 'Profissional especializado em criação visual e design'],
            ['pessoas_profissoes_id' => 10, 'pessoas_profissoes_nome' => 'Jornalista', 'pessoas_profissoes_descricao' => 'Profissional que coleta, escreve e distribui notícias'],
            ['pessoas_profissoes_id' => 11, 'pessoas_profissoes_nome' => 'Chef de Cozinha', 'pessoas_profissoes_descricao' => 'Profissional que planeja, dirige e prepara refeições em restaurantes'],
            ['pessoas_profissoes_id' => 12, 'pessoas_profissoes_nome' => 'Psicólogo', 'pessoas_profissoes_descricao' => 'Profissional que realiza avaliações psicológicas e oferece terapia'],
            ['pessoas_profissoes_id' => 13, 'pessoas_profissoes_nome' => 'Fisioterapeuta', 'pessoas_profissoes_descricao' => 'Profissional que ajuda na reabilitação física de pacientes'],
            ['pessoas_profissoes_id' => 14, 'pessoas_profissoes_nome' => 'Veterinário', 'pessoas_profissoes_descricao' => 'Profissional responsável pelo cuidado e tratamento de animais'],
            ['pessoas_profissoes_id' => 15, 'pessoas_profissoes_nome' => 'Fotógrafo', 'pessoas_profissoes_descricao' => 'Profissional que captura e edita imagens fotográficas'],
            ['pessoas_profissoes_id' => 16, 'pessoas_profissoes_nome' => 'Tradutor', 'pessoas_profissoes_descricao' => 'Profissional que converte textos de um idioma para outro'],
            ['pessoas_profissoes_id' => 17, 'pessoas_profissoes_nome' => 'Administrador', 'pessoas_profissoes_descricao' => 'Profissional que gerencia operações e processos em uma organização'],
            ['pessoas_profissoes_id' => 18, 'pessoas_profissoes_nome' => 'Biólogo', 'pessoas_profissoes_descricao' => 'Profissional que estuda organismos vivos e seus ecossistemas'],
            ['pessoas_profissoes_id' => 19, 'pessoas_profissoes_nome' => 'Economista', 'pessoas_profissoes_descricao' => 'Profissional que analisa dados econômicos e desenvolve modelos de previsão'],
            ['pessoas_profissoes_id' => 20, 'pessoas_profissoes_nome' => 'Programador', 'pessoas_profissoes_descricao' => 'Profissional que escreve e testa códigos de software'],
            ['pessoas_profissoes_id' => 21, 'pessoas_profissoes_nome' => 'Cientista de Dados', 'pessoas_profissoes_descricao' => 'Profissional que analisa e interpreta grandes volumes de dados'],
            ['pessoas_profissoes_id' => 22, 'pessoas_profissoes_nome' => 'Analista de Marketing', 'pessoas_profissoes_descricao' => 'Profissional que desenvolve e implementa estratégias de marketing'],
            ['pessoas_profissoes_id' => 23, 'pessoas_profissoes_nome' => 'Engenheiro Civil', 'pessoas_profissoes_descricao' => 'Profissional que projeta e constrói infraestrutura como pontes e edifícios'],
            ['pessoas_profissoes_id' => 24, 'pessoas_profissoes_nome' => 'Cozinheiro', 'pessoas_profissoes_descricao' => 'Profissional que prepara e cozinha alimentos em ambientes como restaurantes'],
            ['pessoas_profissoes_id' => 25, 'pessoas_profissoes_nome' => 'Social Media', 'pessoas_profissoes_descricao' => 'Profissional que gerencia e cria conteúdo para redes sociais'],
            ['pessoas_profissoes_id' => 26, 'pessoas_profissoes_nome' => 'Auditor', 'pessoas_profissoes_descricao' => 'Profissional que examina e avalia registros financeiros e operacionais'],
            ['pessoas_profissoes_id' => 27, 'pessoas_profissoes_nome' => 'Técnico em Informática', 'pessoas_profissoes_descricao' => 'Profissional que presta suporte técnico e manutenção de hardware e software'],
            ['pessoas_profissoes_id' => 28, 'pessoas_profissoes_nome' => 'Líder de Projeto', 'pessoas_profissoes_descricao' => 'Profissional que coordena e supervisiona projetos para garantir a conclusão bem-sucedida'],
            ['pessoas_profissoes_id' => 29, 'pessoas_profissoes_nome' => 'Químico', 'pessoas_profissoes_descricao' => 'Profissional que realiza pesquisas e experimentos químicos'],
            ['pessoas_profissoes_id' => 30, 'pessoas_profissoes_nome' => 'Gerente de Recursos Humanos', 'pessoas_profissoes_descricao' => 'Profissional responsável pela gestão de pessoal e políticas de recursos humanos'],
            ['pessoas_profissoes_id' => 31, 'pessoas_profissoes_nome' => 'Engenheiro Eletricista', 'pessoas_profissoes_descricao' => 'Profissional que projeta e implementa sistemas elétricos e eletrônicos'],
            ['pessoas_profissoes_id' => 32, 'pessoas_profissoes_nome' => 'Designer de Moda', 'pessoas_profissoes_descricao' => 'Profissional que cria e desenvolve roupas e acessórios'],
            ['pessoas_profissoes_id' => 33, 'pessoas_profissoes_nome' => 'Engenheiro Mecânico', 'pessoas_profissoes_descricao' => 'Profissional que projeta e desenvolve sistemas mecânicos e máquinas'],
            ['pessoas_profissoes_id' => 34, 'pessoas_profissoes_nome' => 'Web Designer', 'pessoas_profissoes_descricao' => 'Profissional que cria e mantém layouts e interfaces de sites'],
            ['pessoas_profissoes_id' => 35, 'pessoas_profissoes_nome' => 'Geólogo', 'pessoas_profissoes_descricao' => 'Profissional que estuda a composição e estrutura da Terra'],
            ['pessoas_profissoes_id' => 36, 'pessoas_profissoes_nome' => 'Segurança da Informação', 'pessoas_profissoes_descricao' => 'Profissional que protege sistemas e dados contra ameaças e ataques'],
            ['pessoas_profissoes_id' => 37, 'pessoas_profissoes_nome' => 'Consultor Financeiro', 'pessoas_profissoes_descricao' => 'Profissional que oferece orientação sobre gestão e planejamento financeiro'],
            ['pessoas_profissoes_id' => 38, 'pessoas_profissoes_nome' => 'Artista Plástico', 'pessoas_profissoes_descricao' => 'Profissional que cria obras de arte em diversos meios e materiais'],
            ['pessoas_profissoes_id' => 39, 'pessoas_profissoes_nome' => 'Logístico', 'pessoas_profissoes_descricao' => 'Profissional que coordena e gerencia operações de logística e cadeia de suprimentos'],
            ['pessoas_profissoes_id' => 40, 'pessoas_profissoes_nome' => 'Fonoaudiólogo', 'pessoas_profissoes_descricao' => 'Profissional que avalia e trata problemas de comunicação e linguagem'],
            ['pessoas_profissoes_id' => 41, 'pessoas_profissoes_nome' => 'Corretor de Imóveis', 'pessoas_profissoes_descricao' => 'Profissional que facilita a compra, venda e aluguel de propriedades']
        ];


        try {
            foreach ($pessoasProfissoes as $tipo) {
                $tipo['pessoas_profissoes_criado_por'] = $usuario;
                $tipo['pessoas_profissoes_gabinete'] = $gabinete;
                $this->pessoaModel->criarProfissaoPessoa($tipo);
            }
            return ['status' => 'success', 'message' => 'Tipos padrões de profissões inseridos com sucesso'];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['status' => 'duplicated', 'message' => 'Alguns tipos de profissões já estão cadastrados'];
            } else {
                $erro_id = uniqid();
                $this->logger->novoLog('orgao_tipo_log', $e->getMessage() . ' | ' . $erro_id, 'ERROR');
                return ['status' => 'error', 'message' => 'Erro interno do servidor', 'error_id' => $erro_id];
            }
        }
    }
}
