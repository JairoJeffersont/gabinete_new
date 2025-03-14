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

$buscaProposicao = $proposicaoController->buscarDetalheProposicaoCD($proposicaoIdGet);
$buscaAutores = $proposicaoController->buscarAutoresProposicaoCD($proposicaoIdGet);
$buscaNota = $notaController->buscarNotaTecnica('nota_proposicao', $proposicaoIdGet);
$buscaTema = $proposicaoController->listarProposicoesTemas($_SESSION['usuario_gabinete']);

if ($buscaProposicao['status'] == 'error' || empty($buscaProposicao['dados'])) {
    header('location: ?secao=proposicoes');
}

?>

<style>
    @media print {

        body {
            background-color: rgb(255, 255, 255) !important;
            background-image: none !important;
        }

        @page {
            margin: 15mm;
            size: A4 portrait;
        }

        body {
            background-color: rgb(255, 255, 255) !important;
            background-image: none !important;
        }


    }
</style>

<script>
    window.onload = function() {
        window.print();

        window.onafterprint = function() {
            window.close();
        };
    };
</script>


<div class="container-fluid p-2">
    <div class="col-12 text-center mb-1">
        <h5>Gabinete <?php echo $buscaGabinete['dados']['gabinete_nome'] ?></h5>
    </div>
    <div class="col-12 text-center mb-5">
        <h6>Ficha da proposição</h6>
    </div>
    <div class="row">
        <div class="col-10">
            <div class="card" style="background: none; border: none;">
                <div class="card-body card_descricao_bg" style="background: none;">
                    <h5 class="card-title mb-4"><?php echo $buscaProposicao['dados']['siglaTipo'] . ' ' . $buscaProposicao['dados']['numero'] . '/' . $buscaProposicao['dados']['ano']; ?></h5>
                    <?php

                    if ($buscaNota['status'] == 'success' && !empty($buscaNota['dados'])) {
                        echo '<p class="card-text mb-3">' . $buscaNota['dados']['nota_proposicao_apelido'] . '</p>';
                        echo '<p class="card-text mb-3"><b>Resumo</b></p>';
                        echo '<p class="card-text mb-3">' . $buscaNota['dados']['nota_proposicao_resumo'] . '</p>';
                        echo '<p class="card-text mb-3"><b>Ementa</b></p>';
                        echo '<p class="card-text mb-0"><em>' . $buscaProposicao['dados']['ementa'] . '</em></p>';
                    } else if ($buscaNota['status'] == 'not_found') {
                        echo '<p class="card-text mb-3"><b>Ementa</b></p>';
                        echo '<p class="card-text mb-0"><em>' . $buscaProposicao['dados']['ementa'] . '</em></p>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="col-10">
            <div class="card-body card_descricao_body p-3">


                <hr class="mb-2 mt-0">
                <p class="card-text mb-1"><i class="bi bi-calendar2-week"></i> Data de apresentação: <?php echo date('d/m/Y', strtotime($buscaProposicao['dados']['dataApresentacao'])) ?></p>
                <p class="card-text mb-2"><i class="bi bi-archive"></i> Situação: <?php echo ($buscaProposicao['dados']['statusProposicao']['descricaoSituacao'] == 'Arquivada') ? '<b>Arquivada</b>' : 'Em tramitação' ?></p>



            </div>
        </div>
        <div class="col-10">
            <div class="card" style="background: none; border: none;">
                <div class="card-body  card_descricao_bg" style="background: none;">
                    <h6 class="card-title">Nota técnica</h6>
                    <hr>
                    <?php

                    if ($buscaNota['status'] == 'success' && !empty($buscaNota['dados'])) {
                        echo $buscaNota['dados']['nota_texto'];
                    } else if ($buscaNota['status'] == 'not_found') {
                        echo '<p class="card-text mb-3">Não existe uma nota técnica para essa proposição</p>';
                    }

                    ?>

                </div>
            </div>
        </div>
        <div class="col-10">
            <div class="card" style="background: none; border: none;">
                <div class="card-body" style="background: none;">
                    <h6 class="card-title">Últimas Tramitações</h6>
                    <hr>
                    <div class="table-responsive mb-0">
                        <table class="table table-hover custom-table table-bordered table-striped mb-0 ">
                            <thead>
                                <tr>
                                    <th scope="col">Data</th>
                                    <th scope="col">Despacho</th>
                                    <th scope="col">Órgão</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $buscaTramitacoes = $proposicaoController->buscarTramitacoesProposicaoCD($proposicaoIdGet);

                                if ($buscaTramitacoes['status'] == 'success' && is_array($buscaTramitacoes['dados'])) {
                                    $itens = 10;
                                    $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

                                    usort($buscaTramitacoes['dados'], function ($a, $b) {
                                        return strtotime($b['dataHora']) - strtotime($a['dataHora']);
                                    });

                                    $totalRegistros = count($buscaTramitacoes['dados']);
                                    $totalPagina = ceil($totalRegistros / $itens);

                                    $offset = ($pagina - 1) * $itens;

                                    foreach (array_slice($buscaTramitacoes['dados'], $offset, $itens) as $tramitacao) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap;">' . date('d/m/Y H:i', strtotime($tramitacao['dataHora'])) . '</td>';
                                        echo '<td>' . htmlspecialchars($tramitacao['despacho']);
                                        echo '</td>';
                                        echo '<td>' . htmlspecialchars($tramitacao['siglaOrgao']) . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($buscaTramitacoes['status'] == 'error') {
                                    echo '<p class="card-text">' . $buscaTramitacoes['message'] . '</p>';
                                }

                                ?>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>