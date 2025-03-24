<?php

ob_start();

use GabineteMvc\Controllers\GabineteController;
use GabineteMvc\Controllers\ProposicaoController;
use GabineteMvc\Controllers\NotaTecnicaController;

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

$proposicaoController = new ProposicaoController();
$notaController = new NotaTecnicaController();
$gabineteController = new GabineteController();

$buscaGabinete = $gabineteController->buscaGabinete('gabinete_id', $_SESSION['usuario_gabinete']);

$proposicaoIdGet = $_GET['id'];

$buscaProposicao = $proposicaoController->buscarDetalheSenado($proposicaoIdGet);
$buscaNota = $notaController->buscarNotaTecnica('nota_proposicao', $proposicaoIdGet);
$buscaTema = $proposicaoController->listarProposicoesTemas($_SESSION['usuario_gabinete']);

if ($buscaProposicao['status'] == 'error' || empty($buscaProposicao['dados'])) {
    header('location: ?secao=proposicoes');
}

?>

<div class="d-flex" id="wrapper">
    <?php include './src/Views/menus/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/menus/top_menu.php'; ?>
        <div class="container-fluid p-2">

            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav barra_navegacao" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm custom-nav barra_navegacao" href="?secao=proposicoes" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>

                </div>
            </div>

            <div class="card mb-2">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-newspaper"></i> Proposição</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-2">Nesta seção, você pode consultar informações de uma proposição.</p>
                    <p class="card-text mb-0">As informações são de responsabilidade da Câmara dos Deputados, podendo sofrer alterações a qualquer momento ou com algum atraso.</p>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body card_descricao_body p-3">
                    <h5 class="card-title mb-3">
                        <?php echo $buscaProposicao['dados']['DetalheMateria']['Materia']['IdentificacaoMateria']['DescricaoIdentificacaoMateria']; ?>
                        <small><?php echo isset($buscaNota['dados'][0]['nota_proposicao_apelido']) ? ' | ' . $buscaNota['dados'][0]['nota_proposicao_apelido'] : ''; ?></small>
                    </h5>
                    <p class="card-text mb-2"><?php echo $buscaProposicao['dados']['DetalheMateria']['Materia']['DadosBasicosMateria']['EmentaMateria'] ?></p>
                    <hr class="mb-2 mt-0">
                    <p class="card-text mb-1"><i class="bi bi-calendar2-week"></i> Data de apresentação: <?php echo date('d/m/Y', strtotime($buscaProposicao['dados']['DetalheMateria']['Materia']['DadosBasicosMateria']['DataApresentacao'])) ?></p>
                    <?php

                    if ($buscaProposicao['dados']['DetalheMateria']['Materia']['IdentificacaoMateria']['IndicadorTramitando'] == 'Sim') {
                        echo '<p class="card-text mb-2"><i class="bi bi-archive"></i> Situação: Tramitando</p>';
                    } else {
                        echo '<p class="card-text mb-2"><i class="bi bi-archive"></i> Situação: Arquivada</p>';
                    }

                    ?>
                </div>
            </div>
            <div class="col-12">
                <div class="card mb-2 ">
                    <div class="card-header bg-success text-white px-2 py-1 card_descricao_body"> Nota técnica</div>
                    <div class="card-body p-2">
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {

                            $dados = [
                                'nota_proposicao' => $proposicaoIdGet,
                                'nota_proposicao_apelido' => htmlspecialchars($_POST['nota_proposicao_apelido'], ENT_QUOTES, 'UTF-8'),
                                'nota_proposicao_resumo' => htmlspecialchars($_POST['nota_proposicao_resumo'], ENT_QUOTES, 'UTF-8'),
                                'nota_proposicao_tema' => htmlspecialchars($_POST['nota_proposicao_tema'], ENT_QUOTES, 'UTF-8'),
                                'nota_texto' => $_POST['nota_texto'],
                                'nota_criada_por' => $_SESSION['usuario_id'],
                                'nota_gabinete' => $_SESSION['usuario_gabinete']
                            ];

                            $result = $notaController->novaNotaTecnica($dados);

                            if ($result['status'] == 'success') {
                                echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                                echo '<script>
                                    setTimeout(() => {
                                        window.location.href = "?secao=proposicaoSF&id=' . $proposicaoIdGet . '";
                                    }, 1000);
                                </script>
                                ';
                            } else if ($result['status'] == 'duplicated' ||  $result['status'] == 'bad_request') {
                                echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            } else if ($result['status'] == 'error') {
                                echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                            }
                        }

                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar'])) {


                            $dados = [
                                'nota_id' => $buscaNota['dados']['nota_id'],
                                'nota_proposicao' => $proposicaoIdGet,
                                'nota_proposicao_apelido' => htmlspecialchars($_POST['nota_proposicao_apelido'], ENT_QUOTES, 'UTF-8'),
                                'nota_proposicao_resumo' => htmlspecialchars($_POST['nota_proposicao_resumo'], ENT_QUOTES, 'UTF-8'),
                                'nota_proposicao_tema' => htmlspecialchars($_POST['nota_proposicao_tema'], ENT_QUOTES, 'UTF-8'),
                                'nota_texto' => $_POST['nota_texto'],
                            ];

                            $result = $notaController->atualizarNotaTecnica($dados);

                            if ($result['status'] == 'success') {
                                echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                                echo '<script>
                                        setTimeout(() => {
                                            window.location.href = "?secao=proposicaoSF&id=' . $proposicaoIdGet . '";
                                            }, 1000);
                                      </script>';
                            } else if ($result['status'] == 'duplicated' ||  $result['status'] == 'bad_request') {
                                echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            } else if ($result['status'] == 'error') {
                                echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                            }
                        }

                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {

                            $result = $notaController->apagarNotaTecnica($buscaNota['dados']['nota_id']);

                            if ($result['status'] == 'success') {
                                echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                                echo '<script>
                                        setTimeout(() => {
                                            window.location.href = "?secao=proposicaoSF&id=' . $proposicaoIdGet . '";
                                            }, 1000);
                                      </script>';
                            } else if ($result['status'] == 'error') {
                                echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                            }
                        }
                        ?>

                        <form class="row g-2 form_custom" method="POST">
                            <div class="col-md-3 col-12">
                                <input type="text" class="form-control form-control-sm" name="nota_proposicao_apelido" value="<?php echo $buscaNota['status'] == 'success' ? $buscaNota['dados']['nota_proposicao_apelido'] : '' ?>" placeholder="Título" required>
                            </div>
                            <div class="col-md-5 col-12">
                                <input type="text" class="form-control form-control-sm" name="nota_proposicao_resumo" placeholder="Resumo" value="<?php echo $buscaNota['status'] == 'success' ? $buscaNota['dados']['nota_proposicao_resumo'] : '' ?>" required>
                            </div>
                            <div class="col-md-2 col-12">
                                <select class="form-control form-control-sm" name="nota_proposicao_tema" id="nota_proposicao_tema" required>

                                    <?php
                                    if ($buscaTema['status'] == 'success') {
                                        foreach ($buscaTema['dados'] as $tema) {

                                            if (empty($buscaNota['dados']['nota_proposicao_tema'])) {
                                                if ($tema['proposicao_tema_id'] == 21) {
                                                    echo '<option value="' . $tema['proposicao_tema_id'] . '" selected>' . $tema['proposicao_tema_nome'] . '</option>';
                                                } else {
                                                    echo '<option value="' . $tema['proposicao_tema_id'] . '">' . $tema['proposicao_tema_nome'] . '</option>';
                                                }
                                            } else {
                                                if ($buscaNota['dados']['nota_proposicao_tema'] == $tema['proposicao_tema_id']) {
                                                    echo '<option value="' . $tema['proposicao_tema_id'] . '" selected>' . $tema['proposicao_tema_nome'] . '</option>';
                                                } else {
                                                    echo '<option value="' . $tema['proposicao_tema_id'] . '">' . $tema['proposicao_tema_nome'] . '</option>';
                                                }
                                            }
                                        }
                                    }

                                    ?>
                                    <option value="+">+ Novo Tema</option>

                                </select>
                            </div>

                            <div class="col-md-2 col-12">
                                <input type="text" class="form-control form-control-sm" disabled value="<?php echo $_SESSION['usuario_nome'] ?>" required>
                            </div>

                            <div class="col-md-12 col-12">
                                <textarea class="form-control form-control-sm" name="nota_texto" placeholder="Texto" rows="10"><?php echo $buscaNota['status'] == 'success' ? $buscaNota['dados']['nota_texto'] : '' ?></textarea>
                            </div>
                            <div class="col-md-6 col-12">
                                <?php

                                if ($buscaNota['status'] == 'success') {
                                    echo '<button type="submit" class="btn btn-primary btn-sm" name="btn_atualizar"><i class="bi bi-floppy-fill"></i> Atualizar</button>&nbsp;';
                                    echo '<button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="bi bi-trash-fill"></i> Apagar</button>&nbsp;';
                                    echo '<a href="?secao=imprimir-proposicaoSF&id=' . $proposicaoIdGet . '" id="btn_imprimir"  target="_blank" type="button" class="btn btn-secondary btn-sm"><i class="bi bi-printer"></i> Imprimir</a>';
                                } else {
                                    echo '<button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>&nbsp;';
                                    echo '<a href="?secao=imprimir-proposicaoSF&id=' . $proposicaoIdGet . '"  id="btn_imprimir"  target="_blank" type="button" class="btn btn-secondary btn-sm"><i class="bi bi-printer"></i> Imprimir</a>';
                                }

                                ?>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body card_descricao_body p-2">
                    <div class="accordion" id="accordionPanelsStayOpenExample">

                        <?php
                        $buscaTexto = $proposicaoController->buscarTextoSenado($proposicaoIdGet);

                        if (isset($buscaTexto['dados']['TextoMateria']['Materia']['Textos']['Texto'])) {
                            foreach ($buscaTexto['dados']['TextoMateria']['Materia']['Textos']['Texto'] as $texto) {

                                echo ' <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" style="font-size: 0.5em" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse' . $texto['CodigoTexto'] . '" aria-expanded="false" aria-controls="panelsStayOpen-collapse' . $texto['CodigoTexto'] . '">
                                                    <i class="bi bi-file-text"></i> &nbsp; &nbsp;' . $texto['DescricaoTipoTexto'] . '
                                                </button>
                                            </h2>
                                            <div id="panelsStayOpen-collapse' . $texto['CodigoTexto'] . '" class="accordion-collapse collapse">
                                                <div class="accordion-body">
                                                    <iframe src="https://docs.google.com/gview?url=' . urlencode($texto['UrlTexto']) . '&embedded=true" width="100%" height="1000px"></iframe>
                                                </div>
                                            </div>
                                        </div>';
                            }
                        } else if ($buscaTexto['status'] == 'success' && empty($buscaTexto['dados'])) {
                            echo '<tr><td colspan=3>Nenhum texto encontrado</td></tr>';
                        } else {
                            echo '<tr><td colspan=3>' . $buscaTexto['message'] . '</td></tr>';
                        }
                        ?>

                    </div>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header bg-primary text-white px-2 py-1"><i class="bi bi-fast-forward-btn"></i> Tramitações</div>

                <div class="card-body p-2">
                    <div class="table-responsive mb-0">
                        <table class="table table-hover custom-table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Data</th>
                                    <th scope="col">Despacho</th>
                                    <th scope="col">Órgão</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $itensPorPagina = 10;
                                $paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

                                $buscaTramitacoes = $proposicaoController->buscarTramitacoesSenado($proposicaoIdGet);

                                if ($buscaTramitacoes['status'] == 'success' && !empty($buscaTramitacoes['dados'])) {
                                    $movimentacoes = [];
                                    foreach ($buscaTramitacoes['dados']['MovimentacaoMateria']['Materia']['Autuacoes']['Autuacao'] as $tramitacao) {
                                        foreach ($tramitacao['InformesLegislativos']['InformeLegislativo'] as $informe) {
                                            $movimentacoes[] = $informe; // Armazene os informes
                                        }
                                    }

                                    $totalPaginas = ceil(count($movimentacoes) / $itensPorPagina);

                                    $offset = ($paginaAtual - 1) * $itensPorPagina;
                                    $itensDaPagina = array_slice($movimentacoes, $offset, $itensPorPagina);

                                    foreach ($itensDaPagina as $informe) {
                                        echo '<tr>';
                                        echo '<td>' . date('d/m/y', strtotime($informe['Data'])) . '</td>';
                                        echo '<td>' . $informe['Descricao'] . '</td>';
                                        echo '<td>' . $informe['Local']['SiglaLocal'] . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($buscaTramitacoes['status'] == 'success' && empty($buscaTramitacoes['dados'])) {
                                    echo '<tr><td colspan=3>Nenhuma tramitação encontrada</td></tr>';
                                } else {
                                    echo '<tr><td colspan=3>' . $buscaTramitacoes['message'] . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    if (isset($totalPaginas) && $totalPaginas > 1) {
                        echo '<ul class="pagination custom-pagination mt-2 mb-0">';

                        // Primeira página
                        echo '<li class="page-item ' . ($paginaAtual == 1 ? 'active' : '') . '"><a class="page-link" href="?secao=proposicaoSF&id=' . $proposicaoIdGet . '&pagina=1">Primeira</a></li>';

                        // Páginas intermediárias
                        for ($i = 1; $i < $totalPaginas - 1; $i++) {
                            $pageNumber = $i + 1;
                            echo '<li class="page-item ' . ($paginaAtual == $pageNumber ? 'active' : '') . '"><a class="page-link" href="?secao=proposicaoSF&id=' . $proposicaoIdGet . '&pagina=' . $pageNumber . '">' . $pageNumber . '</a></li>';
                        }

                        // Última página
                        echo '<li class="page-item ' . ($paginaAtual == $totalPaginas ? 'active' : '') . '"><a class="page-link" href="?secao=proposicaoSF&id=' . $proposicaoIdGet . '&pagina=' . $totalPaginas . '">Última</a></li>';

                        echo '</ul>';
                    }
                    ?>
                </div>

            </div>
        </div>
    </div>
</div>