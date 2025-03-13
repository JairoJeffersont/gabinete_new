<?php


ob_start();

$anoGet = isset($_GET['ano']) ? $_GET['ano'] : date('Y');

$autorGet = $utils->sanitizarString($gabinete['dados']['gabinete_nome']);

$string = $gabinete['dados']['gabinete_nome_sistema'];
$autorGet = str_replace("-", "+", $string);

$tipoget = isset($_GET['tipo']) ? $_GET['tipo'] : 'PL';

$itensGet = isset($_GET['itens']) ? (int)$_GET['itens'] : 10;
$paginaGet = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

$buscaProposicoes = $proposicaoController->buscarProposicoesSenado($autorGet, $anoGet, $tipoget);

?>

<div class="card mb-2">
    <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-newspaper"></i> Proposições | <?php echo $tipoGabinete['dados']['gabinete_tipo_nome'] ?> | <?php echo $gabinete['dados']['gabinete_nome'] ?></div>
    <div class="card-body card_descricao_body p-2">
        <p class="card-text mb-2">Nesta seção, você pode pesquisar pelas proposições de autoria e co-autoria do senador, facilitando o acesso às informações relevantes de forma rápida e organizada.</p>
        <p class="card-text mb-0">As informações são de responsabilidade da Senado Federal, podendo sofrer alterações a qualquer momento ou com algum atraso.</p>
    </div>
</div>

<div class="card mb-2">
    <div class="card-body p-2">
        <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
            <div class="col-md-1 col-2">
                <input type="hidden" name="secao" value="proposicoes" />
                <input type="text" class="form-control form-control-sm" name="ano" data-mask="0000" value="<?php echo $anoGet ?>">
            </div>
            <div class="col-md-2 col-10">
                <select class="form-select form-select-sm" name="tipo" required>
                    <?php
                    $buscaTipos = $proposicaoController->buscarTiposProposicaoSenado();

                    if ($buscaTipos['status'] == 'success' && !empty($buscaTipos['dados'])) {
                        foreach ($buscaTipos['dados']['ListaSiglas']['SiglasAtivas'] as $tipo) {
                            foreach ($tipo as $type) {
                                if ($type['Sigla'] == $tipoget) {
                                    echo '<option value="' . $type['Sigla'] . '" selected>' . $type['Descricao'] . '</option>';
                                } else {
                                    echo '<option value="' . $type['Sigla'] . '">' . $type['Descricao'] . '</option>';
                                }
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

                    $autorGet = str_replace(" ", "%20", $autorGet);
                    $buscaProposicoes = $proposicaoController->buscarProposicoesSenado($autorGet, $anoGet, $tipoget);

                    if ($buscaProposicoes['status'] == 'success' && isset($buscaProposicoes['dados']['PesquisaBasicaMateria']['Materias']['Materia'])) {

                        usort($buscaProposicoes['dados']['PesquisaBasicaMateria']['Materias']['Materia'], function ($a, $b) {
                            return $b['Numero'] - $a['Numero']; // Ordem decrescente
                        });

                        $totalRegistros = count($buscaProposicoes['dados']['PesquisaBasicaMateria']['Materias']['Materia']);

                        $totalPaginas = ceil($totalRegistros / $itensGet);

                        $offset = ($paginaGet - 1) * $itensGet;

                        foreach (array_slice($buscaProposicoes['dados']['PesquisaBasicaMateria']['Materias']['Materia'], $offset, $itensGet) as $materia) {

                            $buscaNota = $notaController->buscarNotaTecnica('nota_proposicao',  $materia['Codigo']);


                            if ($buscaNota['status'] == 'success') {
                                $ementa = '<b><em>' . $buscaNota['dados'][0]['nota_proposicao_apelido'] . '</b></em><br>' . $buscaNota['dados'][0]['nota_proposicao_resumo'];
                            } else {
                                $ementa = $materia['Ementa'];
                            }


                            echo '<tr>';
                            echo '<td style="white-space: nowrap;"><a href="?secao=proposicao-senado&id=' . $materia['Codigo'] . '">' . $materia['Sigla'] . ' ' . ltrim($materia['Numero'], '0') . '/' . $materia['Ano'] . '</a></td>';
                            echo '<td>' . $ementa . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="2">Nenhuma proposição encontrada.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php

        if (isset($totalPaginas)) {
            $totalPaginas = $totalPaginas;
        } else {
            $totalPaginas = 0;
        }

        if ($totalPaginas > 0 && $totalPaginas != 1) {
            echo '<ul class="pagination custom-pagination mt-2 mb-0">';
            echo '<li class="page-item ' . ($paginaGet == 1 ? 'active' : '') . '"><a class="page-link" href="?secao=proposicoes&itens=' . $itensGet . '&pagina=1&tipo=' . $tipoget . '&ano=' . $anoGet . '">Primeira</a></li>';

            for ($i = 1; $i < $totalPaginas - 1; $i++) {
                $pageNumber = $i + 1;
                echo '<li class="page-item ' . ($paginaGet == $pageNumber ? 'active' : '') . '"><a class="page-link" href="?secao=proposicoes&itens=' . $itensGet . '&pagina=' . $pageNumber . '&tipo=' . $tipoget . '&ano=' . $anoGet . '">' . $pageNumber . '</a></li>';
            }

            echo '<li class="page-item ' . ($paginaGet == $totalPaginas ? 'active' : '') . '"><a class="page-link" href="?secao=proposicoes&itens=' . $itensGet . '&pagina=' . $totalPaginas . '&tipo=' . $tipoget . '&ano=' . $anoGet . '">Última</a></li>';
            echo '</ul>';
        }
        ?>
    </div>
</div>