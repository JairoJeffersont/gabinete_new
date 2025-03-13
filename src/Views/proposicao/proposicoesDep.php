<?php

ob_start();

$anoGet = isset($_GET['ano']) ? $_GET['ano'] : date('Y');
$autorGet = $gabinete['dados']['gabinete_nome_sistema'];

$tipoget = isset($_GET['tipo']) ? $_GET['tipo'] : 'pl';

$itensGet = isset($_GET['itens']) ? (int)$_GET['itens'] : 10;
$paginaGet = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

?>

<div class="card mb-2">
    <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-newspaper"></i> Proposições | <?php echo $tipoGabinete['dados']['gabinete_tipo_nome'] ?> | <?php echo $gabinete['dados']['gabinete_nome'] ?></div>
    <div class="card-body card_descricao_body p-2">
        <p class="card-text mb-2">Nesta seção, você pode pesquisar pelas proposições de autoria e co-autoria do deputado, facilitando o acesso às informações relevantes de forma rápida e organizada.</p>
        <p class="card-text mb-0">As informações são de responsabilidade da Câmara dos Deputados, podendo sofrer alterações a qualquer momento ou com algum atraso.</p>
    </div>
</div>
<div class="card mb-2">
    <div class="card-body p-2">
        <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
            <div class="col-md-1 col-2">
                <input type="hidden" name="secao" value="proposicoes" />
                <input type="text" class="form-control form-control-sm" name="ano" data-mask="0000" value="<?php echo $anoGet ?>">
            </div>
            <div class="col-md-1 col-10">
                <select class="form-select form-select-sm" name="tipo" required>

                    <?php
                    $tiposProposicoes = $proposicaoController->buscarTiposProposicaoCD();
                    if ($tiposProposicoes['status'] == 'success') {
                        usort($tiposProposicoes['dados'], function ($a, $b) {
                            return strcmp($a['nome'], $b['nome']);
                        });

                        foreach ($tiposProposicoes['dados'] as $tipoProposicao) {
                            if ($tipoProposicao['sigla'] == $tipoget) {
                                echo '<option value="' . $tipoProposicao['sigla'] . '" selected>' . $tipoProposicao['nome'] . '</option>';
                            } else {
                                echo '<option value="' . $tipoProposicao['sigla'] . '">' . $tipoProposicao['nome'] . '</option>';
                            }
                        }
                    }
                    ?>

                </select>
            </div>

            <div class="col-md-1 col-4">
                <select class="form-select form-select-sm" name="itens" required>
                    <option value="5" <?php echo $itensGet == 5 ? 'selected' : ''; ?>>5 itens</option>
                    <option value="10" <?php echo $itensGet == 10 ? 'selected' : ''; ?>>10 itens</option>
                    <option value="25" <?php echo $itensGet == 25 ? 'selected' : ''; ?>>25 itens</option>
                    <option value="50" <?php echo $itensGet == 50 ? 'selected' : ''; ?>>50 itens</option>
                </select>
            </div>

            <div class="col-md-1 col-2">
                <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>
<div class="card mb-2">
    <div class="card-body p-2">
        <div class="table-responsive mb-0">
            <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                <thead>
                    <tr>
                        <th scope="col">Título</th>
                        <th scope="col">Ementa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $buscaProposicao = $proposicaoController->buscarProposicoesDeputado($autorGet, $anoGet, $itensGet, $paginaGet, $tipoget);

                    if ($buscaProposicao['status'] == 'success') {
                        foreach ($buscaProposicao['dados'] as $proposicao) {
                            echo '<tr>';
                            echo '<td style="white-space: nowrap;"><a href="?secao=proposicao&id=' . $proposicao['id'] . '">' . (count($proposicao['proposicao_autores']) > 1 ? '<i class="bi bi-people-fill"></i>' : '<i class="bi bi-person-fill"></i>') . ' | ' . $proposicao['siglaTipo'] . ' ' . $proposicao['numero'] . '/' . $proposicao['ano'] . '</a></td>';
                            echo '<td>' . $proposicao['ementa'] . '</td>';
                            echo '</tr>';
                        }
                    } else if ($buscaProposicao['status'] == 'empty') {
                        echo '<tr><td colspan="2">' . $buscaProposicao['message'] . '</td></tr>';
                    }
                    ?>

                </tbody>
            </table>
        </div>
        <?php

        if (isset($buscaProposicao['total_paginas'])) {
            $totalPagina = $buscaProposicao['total_paginas'];
        } else {
            $totalPagina = 0;
        }

        if ($totalPagina > 0 && $totalPagina != 1) {
            echo '<ul class="pagination custom-pagination mt-2 mb-0">';
            echo '<li class="page-item ' . ($paginaGet == 1 ? 'active' : '') . '"><a class="page-link" href="?secao=proposicoes&itens=' . $itensGet . '&pagina=1&tipo=' . $tipoget . '&ano=' . $anoGet . '">Primeira</a></li>';

            for ($i = 1; $i < $totalPagina - 1; $i++) {
                $pageNumber = $i + 1;
                echo '<li class="page-item ' . ($paginaGet == $pageNumber ? 'active' : '') . '"><a class="page-link" href="?secao=proposicoes&itens=' . $itensGet . '&pagina=' . $pageNumber . '&tipo=' . $tipoget . '&ano=' . $anoGet . '">' . $pageNumber . '</a></li>';
            }

            echo '<li class="page-item ' . ($paginaGet == $totalPagina ? 'active' : '') . '"><a class="page-link" href="?secao=proposicoes&itens=' . $itensGet . '&pagina=' . $totalPagina . '&tipo=' . $tipoget . '&ano=' . $anoGet . '">Última</a></li>';
            echo '</ul>';
        }
        ?>
    </div>
</div>