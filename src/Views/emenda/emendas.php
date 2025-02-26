<?php

ob_start();

use GabineteMvc\Controllers\GabineteController;
use GabineteMvc\Controllers\EmendaController;
use GabineteMvc\Controllers\OrgaoController;

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

$emendaController = new EmendaController();
$orgaosController = new OrgaoController();
$gabineteController = new GabineteController();


$ordenarPor = isset($_GET['ordenarPor']) && in_array(htmlspecialchars($_GET['ordenarPor']), ['emenda_numero', 'emenda_valor', 'emenda_municipio', 'emendas_status_nome']) ? htmlspecialchars($_GET['ordenarPor']) : 'emenda_numero';
$ordem = isset($_GET['ordem']) ? strtolower(htmlspecialchars($_GET['ordem'])) : 'asc';
$itens = isset($_GET['itens']) ? (int) $_GET['itens'] : 10;
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$anoGet = isset($_GET['ano']) ? (int) $_GET['ano'] : date('Y');
$statusGet = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : 0;
$objetivoGet = isset($_GET['objetivo']) ? htmlspecialchars($_GET['objetivo']) : 0;
$tipoGet = isset($_GET['tipo']) ? (int) $_GET['tipo'] : 1;

$buscaGab = $gabineteController->buscaGabinete('gabinete_id', $_SESSION['usuario_gabinete']);
$estado = $buscaGab['dados']['gabinete_estado_autoridade'];

$estadoGet = isset($_GET['estado']) ? htmlspecialchars($_GET['estado']) : $estado;
$municipioGet = isset($_GET['municipio']) ? htmlspecialchars($_GET['municipio']) : null;

?>

<div class="d-flex" id="wrapper">
    <?php include './src/Views/menus/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/menus/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2 ">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav barra_navegacao" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-cash-stack"></i> Adicionar Emenda</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-2">Nesta página, você pode cadastrar novas emendas, informando dados como número, valor, descrição, status, órgão responsável, município e objetivo.</p>
                    <p class="card-text mb-0">Além disso, é possível filtrar e visualizar emendas já cadastradas, organizadas por diferentes critérios como número, valor, status e município.
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-1">
                    <form class="row g-2 form_custom mb-0" method="post" enctype="application/x-www-form-urlencoded">
                        <div class="col-md-12 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_xls"><i class="bi bi-file-earmark-excel-fill"></i> Excel</button>
                            <button type="submit" class="btn btn-primary btn-sm" name="btn_csv"><i class="bi bi-filetype-csv"></i> CSV</button>
                            <?php echo '<a href="?secao=imprimir-emendas&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . '&status=' . $statusGet . '&objetivo=' . $objetivoGet . '&tipo=' . $tipoGet . '&estado=' . $estadoGet . '&municipio=' . $municipioGet . '"" type="button" target="_blank" class="btn btn-secondary btn-sm" id="btn_imprimir"><i class="bi bi-printer-fill"></i> Imprimir</a>'; ?>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm mb-2 no-print">
                <div class="card-body p-2">
                    <?php

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_csv'])) {

                        $result = $emendaController->gerarCsv($itens, $pagina, $ordem, $ordenarPor, $statusGet, $tipoGet, $objetivoGet, $anoGet, $estadoGet, $municipioGet, $_SESSION['usuario_gabinete']);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="20" role="alert">' . $result['message'] . '. <a href="' . $result['file'] . '">Download</a></div>';
                        } else if ($result['status'] == 'not_found') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">Nenhum arquivo foi gerado</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_xls'])) {

                        $result = $emendaController->gerarXls($itens, $pagina, $ordem, $ordenarPor, $statusGet, $tipoGet, $objetivoGet, $anoGet, $estadoGet, $municipioGet, $_SESSION['usuario_gabinete']);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="20" role="alert">' . $result['message'] . '. <a href="' . $result['file'] . '">Download</a></div>';
                        } else if ($result['status'] == 'not_found') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">Nenhum arquivo foi gerado</div>';
                        }
                    }


                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {
                        $dadosEmenda = [
                            'emenda_numero' => htmlspecialchars($_POST['emenda_numero'], ENT_QUOTES, 'UTF-8'),
                            'emenda_ano' => date('Y'),
                            'emenda_valor' => (float) str_replace(',', '.', str_replace('.', '', htmlspecialchars($_POST['emenda_valor'], ENT_QUOTES, 'UTF-8'))),
                            'emenda_descricao' => htmlspecialchars($_POST['emenda_descricao'], ENT_QUOTES, 'UTF-8'),
                            'emenda_status' => htmlspecialchars($_POST['emenda_status'], ENT_QUOTES, 'UTF-8'),
                            'emenda_orgao' => htmlspecialchars($_POST['emenda_orgao'], ENT_QUOTES, 'UTF-8'),
                            'emenda_municipio' => htmlspecialchars($_POST['emenda_municipio'], ENT_QUOTES, 'UTF-8'),
                            'emenda_estado' => htmlspecialchars($_POST['emenda_estado'], ENT_QUOTES, 'UTF-8'),

                            'emenda_objetivo' => htmlspecialchars($_POST['emenda_objetivo'], ENT_QUOTES, 'UTF-8'),
                            'emenda_informacoes' => htmlspecialchars($_POST['emenda_informacoes'], ENT_QUOTES, 'UTF-8'),
                            'emenda_tipo' => htmlspecialchars($_POST['emenda_tipo'], ENT_QUOTES, 'UTF-8'),
                            'emenda_gabinete' => $_SESSION['usuario_gabinete'],
                            'emenda_criado_por' => $_SESSION['usuario_id']
                        ];

                        $result = $emendaController->criarEmenda($dadosEmenda);

                        if ($result['status'] == 'success') {
                            $emendas = $emendaController->listarEmendas(10, 1, 'asc', 'emenda_numero', 1, 1, 1, 1, null, null, 2025, $_SESSION['usuario_gabinete']);
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error' || $result['status'] == 'forbidden') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        }
                    }
                    ?>

                    <form class="row g-2 form_custom no-print" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="emenda_numero" placeholder="Número da Emenda" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="emenda_valor" id="emenda_valor" placeholder="Valor da Emenda (R$)" required>
                        </div>
                        <div class="col-md-8 col-12">
                            <input type="text" class="form-control form-control-sm" name="emenda_descricao" placeholder="Descrição" required>
                        </div>
                        <div class="col-md-4 col-12">
                            <select class="form-select form-select-sm" name="emenda_status" id="emenda_status" required>
                                <?php

                                $emendasStatus = $emendaController->listarEmendaStatus($_SESSION['usuario_gabinete']);
                                if ($emendasStatus['status'] == 'success') {
                                    foreach ($emendasStatus['dados'] as $status) {
                                        if ($status['emendas_status_id'] == 1) {
                                            echo '<option value="' . $status['emendas_status_id'] . '" selected>' . $status['emendas_status_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $status['emendas_status_id'] . '">' . $status['emendas_status_nome'] . '</option>';
                                        }
                                    }
                                }

                                ?>
                                <option value="+">Novo status + </option>
                            </select>
                        </div>
                        <div class="col-md-4 col-12">
                            <select class="form-select form-select-sm" name="emenda_orgao" id="orgao" required>
                                <option value="1" selected>Órgão não informado</option>
                                <?php

                                $orgaos = $orgaosController->listarOrgaos(1000, 1, 'ASC', 'orgao_nome', null, null, $_SESSION['usuario_gabinete']);
                                if ($orgaos['status'] == 'success') {
                                    foreach ($orgaos['dados'] as $orgao) {
                                        if ($orgaos['orgao_id'] == 1) {
                                            echo '<option value="' . $orgao['orgao_id'] . '" selected>' . $orgao['orgao_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $orgao['orgao_id'] . '">' . $orgao['orgao_nome'] . '</option>';
                                        }
                                    }
                                }

                                ?>
                                <option value="+">Novo órgão + </option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" id="estado" name="emenda_estado" required>
                                <option value="" selected>UF</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" id="municipio" name="emenda_municipio" required>
                                <option value="" selected>Município</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-12">
                            <select class="form-select form-select-sm" name="emenda_objetivo" id="emenda_objetivo" required>
                                <?php

                                $emendasObjetivos = $emendaController->listarEmendaObjetivo($_SESSION['usuario_gabinete']);

                                if ($emendasObjetivos['status'] == 'success') {
                                    foreach ($emendasObjetivos['dados'] as $objetivo) {
                                        if ($objetivo['emendas_objetivos_id'] == 1) {
                                            echo '<option value="' . $objetivo['emendas_objetivos_id'] . '" selected>' . $objetivo['emendas_objetivos_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $objetivo['emendas_objetivos_id'] . '">' . $objetivo['emendas_objetivos_nome'] . '</option>';
                                        }
                                    }
                                }

                                ?>
                                <option value="+">Novo objetivo + </option>
                            </select>
                        </div>
                        <div class="col-md-6 col-12">
                            <select class="form-select form-select-sm" name="emenda_tipo" required>
                                <option value="1">Emenda parlamentar</option>
                                <option value="2">Emenda de bancada</option>
                            </select>
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="emenda_informacoes" placeholder="Informações Adicionais. Ex. Ordem de pagamento, códigos gerais..." rows="5" required></textarea>
                        </div>

                        <div class="col-md-3 col-12">
                            <div class="file-upload">

                                <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm mb-2 no-print">
                <div class="card-body p-2">
                    <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                        <div class="col-md-1 col-6">
                            <input type="hidden" name="secao" value="emendas" />
                            <input type="number" class="form-control form-control-sm" name="ano" value="<?php echo $anoGet ?>">
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" name="ordenarPor" required>
                                <option value="emenda_numero" <?php echo $ordenarPor == 'emenda_numero' ? 'selected' : ''; ?>>Ordenar por | Número</option>
                                <option value="emenda_valor" <?php echo $ordenarPor == 'emenda_valor' ? 'selected' : ''; ?>>Ordenar por | Valor</option>
                                <option value="emenda_municipio" <?php echo $ordenarPor == 'emenda_municipio' ? 'selected' : ''; ?>>Ordenar por | Município</option>
                                <option value="emendas_status_nome" <?php echo $ordenarPor == 'emendas_status_nome' ? 'selected' : ''; ?>>Ordenar por | Status</option>
                            </select>
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" name="ordem" required>
                                <option value="asc" <?php echo $ordem == 'asc' ? 'selected' : ''; ?>>Ordem Crescente</option>
                                <option value="desc" <?php echo $ordem == 'desc' ? 'selected' : ''; ?>>Ordem Decrescente</option>
                            </select>
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" name="itens" required>
                                <option value="5" <?php echo $itens == 5 ? 'selected' : ''; ?>>5 itens</option>
                                <option value="10" <?php echo $itens == 10 ? 'selected' : ''; ?>>10 itens</option>
                                <option value="25" <?php echo $itens == 25 ? 'selected' : ''; ?>>25 itens</option>
                                <option value="50" <?php echo $itens == 50 ? 'selected' : ''; ?>>50 itens</option>
                                <option value="100" <?php echo $itens == 100 ? 'selected' : ''; ?>>100 itens</option>
                            </select>
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" name="status" required>
                                <option value="0">Tudo</option>

                                <?php

                                $emendasStatus = $emendaController->listarEmendaStatus($_SESSION['usuario_gabinete']);
                                if ($emendasStatus['status'] == 'success') {
                                    foreach ($emendasStatus['dados'] as $status) {
                                        if ($status['emendas_status_id'] == $statusGet) {
                                            echo '<option value="' . $status['emendas_status_id'] . '" selected>' . $status['emendas_status_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $status['emendas_status_id'] . '">' . $status['emendas_status_nome'] . '</option>';
                                        }
                                    }
                                }

                                ?>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" name="objetivo" required>
                                <option value="0">Tudo</option>
                                <?php

                                $emendasObjetivos = $emendaController->listarEmendaObjetivo($_SESSION['usuario_gabinete']);

                                if ($emendasObjetivos['status'] == 'success') {
                                    foreach ($emendasObjetivos['dados'] as $objetivo) {
                                        if ($objetivo['emendas_objetivos_id'] == $objetivoGet) {
                                            echo '<option value="' . $objetivo['emendas_objetivos_id'] . '" selected>' . $objetivo['emendas_objetivos_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $objetivo['emendas_objetivos_id'] . '">' . $objetivo['emendas_objetivos_nome'] . '</option>';
                                        }
                                    }
                                }

                                ?>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" id="estado2" name="estado" required>
                                <option value="" selected>UF</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" id="municipio2" name="municipio">
                                <option value="" selected>Município</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" name="tipo" required>
                                <option value="1" <?php echo $tipoGet == 1 ? 'selected' : ''; ?>>Emenda individual</option>
                                <option value="2" <?php echo $tipoGet == 2 ? 'selected' : ''; ?>>Emenda de bancada</option>
                            </select>
                        </div>
                        <div class="col-md-1 col-2">
                            <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Número</th>
                                    <th scope="col">Valor</th>
                                    <th scope="col">Descrição</th>
                                    <th scope="col">Objetivo</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Órgão</th>
                                    <th scope="col">Município</th>
                                    <th scope="col" class="no-print">Criado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $emendas = $emendaController->listarEmendas($itens, $pagina, $ordem, $ordenarPor, $statusGet, $tipoGet, $objetivoGet, $anoGet, $estadoGet, $municipioGet, $_SESSION['usuario_gabinete']);
                                if ($emendas['status'] == 'success') {
                                    foreach ($emendas['dados'] as $emenda) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap;"><a href="?secao=emenda&id=' . $emenda['emenda_id'] . '">' . $emenda['emenda_numero'] . '</a></td>';
                                        echo '<td style="white-space: nowrap;">R$ ' . number_format($emenda['emenda_valor'], 2, ',', '.') . '</td>';
                                        echo '<td>' . $emenda['emenda_descricao'] . '</td>';
                                        echo '<td >' . $emenda['emendas_objetivos_nome'] . '</td>';
                                        echo '<td >' . $emenda['emendas_status_nome'] . '</td>';
                                        echo '<td >' . $emenda['orgao_nome'] . '</td>';
                                        echo '<td >' . $emenda['emenda_municipio'] . ' | ' . $emenda['emenda_estado'] . '</td>';
                                        echo '<td  class="no-print">' . date('d/m/Y', strtotime($emenda['emenda_criada_em'])) . ' | ' . $emenda['usuario_nome'] . '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="7">' . $emendas['message'] . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    if (isset($emendas['total_paginas'])) {
                        $totalPagina = $emendas['total_paginas'];
                    } else {
                        $totalPagina = 0;
                    }

                    if ($totalPagina > 0 && $totalPagina != 1) {
                        echo '<ul class="pagination custom-pagination mt-2 mb-0 no-print">';
                        echo '<li class="page-item ' . ($pagina == 1 ? 'active' : '') . '"><a class="page-link" href="?secao=emendas&itens=' . $itens . '&pagina=1&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . '&status=' . $statusGet . '&objetivo=' . $objetivoGet . '&tipo=' . $tipoGet . '&estado=' . $estadoGet . '&municipio=' . $municipioGet . '">Primeira</a></li>';

                        for ($i = 1; $i < $totalPagina - 1; $i++) {
                            $pageNumber = $i + 1;
                            echo '<li class="page-item ' . ($pagina == $pageNumber ? 'active' : '') . '"><a class="page-link" href="?secao=emendas&itens=' . $itens . '&pagina=' . $pageNumber . '&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . '&status=' . $statusGet . '&objetivo=' . $objetivoGet . '&tipo=' . $tipoGet . '&estado=' . $estadoGet . '&municipio=' . $municipioGet . '">' . $pageNumber . '</a></li>';
                        }

                        echo '<li class="page-item ' . ($pagina == $totalPagina ? 'active' : '') . '"><a class="page-link" href="?secao=emendas&itens=' . $itens . '&pagina=' . $totalPagina . '&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . '&status=' . $statusGet . '&objetivo=' . $objetivoGet . '&tipo=' . $tipoGet . '&estado=' . $estadoGet . '&municipio=' . $municipioGet . '">Última</a></li>';
                        echo '</ul>';
                    }
                    ?>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <h6 class="card-title mb-0"><i class="bi bi-cash-stack no-print"></i> | R$
                        <?php

                        if (isset($emendas['dados'][0]['total_valor'])) {
                            echo number_format($emendas['dados'][0]['total_valor'], 2, ',', '.');
                        } else {
                            echo '0,00';
                        }


                        ?>


                    </h6>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        carregarEstados();
        $('#emenda_valor').mask('###.###.###,##', {
            reverse: true
        });
    });

    function carregarEstados() {
        $.getJSON('https://servicodados.ibge.gov.br/api/v1/localidades/estados?orderBy=nome', function(data) {
            const selectEstado = $('#estado');
            selectEstado.empty();
            selectEstado.append('<option value="" selected>UF</option>');
            data.forEach(estado => {
                selectEstado.append(`<option value="${estado.sigla}">${estado.sigla}</option>`);
            });

            const selectEstado2 = $('#estado2');
            selectEstado2.empty();
            selectEstado2.append('<option value="" selected>UF</option>');
            data.forEach(estado => {
                if (estado.sigla == '<?php echo $estadoGet ?>') {
                    selectEstado2.append(`<option value="${estado.sigla}" selected>${estado.sigla}</option>`);
                    ''
                    setTimeout(function() {
                        carregarMunicipios('<?php echo $estadoGet ?>');
                    }, 500)
                } else {
                    selectEstado2.append(`<option value="${estado.sigla}">${estado.sigla}</option>`);
                }
            });
        });
    }

    function carregarMunicipios(estadoId) {
        $.getJSON(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${estadoId}/municipios?orderBy=nome`, function(data) {
            const selectMunicipio = $('#municipio');
            selectMunicipio.empty();
            selectMunicipio.append('<option value="" selected>Município</option>');
            data.forEach(municipio => {
                selectMunicipio.append(`<option value="${municipio.nome}">${municipio.nome}</option>`);
            });

            const selectMunicipio2 = $('#municipio2');
            selectMunicipio2.empty();
            selectMunicipio2.append('<option value="" selected>Município</option>');
            data.forEach(municipio => {
                if (municipio.nome == '<?php echo $municipioGet ?>') {
                    selectMunicipio2.append(`<option value="${municipio.nome}" selected>${municipio.nome}</option>`);
                } else {
                    selectMunicipio2.append(`<option value="${municipio.nome}">${municipio.nome}</option>`);
                }
            });
        });
    }


    $('#estado').change(function() {
        const estadoId = $(this).val();
        if (estadoId) {
            $('#municipio').empty().append('<option value="">Aguarde...</option>');
            carregarMunicipios(estadoId);
        } else {
            $('#municipio').empty().append('<option value="" selected>Município</option>');
        }
    });

    $('#estado2').change(function() {
        const estadoId = $(this).val();
        if (estadoId) {
            $('#municipio2').empty().append('<option value="">Aguarde...</option>');
            carregarMunicipios(estadoId);
        } else {
            $('#municipio2').empty().append('<option value="" selected>Município</option>');
        }
    });


    $('#btn_nova_status').click(function() {
        if (window.confirm("Você realmente deseja inserir um novo status?")) {
            window.location.href = "?secao=emendas-status";
        } else {
            return false;
        }
    });

    $('#btn_novo_objetivo').click(function() {
        if (window.confirm("Você realmente deseja inserir uma novo objetivo?")) {
            window.location.href = "?secao=emendas-objetivos";
        } else {
            return false;
        }
    });

    $('#emenda_objetivo').change(function() {
        if ($('#emenda_objetivo').val() == '+') {
            if (window.confirm("Você realmente deseja inserir uma novo objetivo?")) {
                window.location.href = "?secao=emendas-objetivos";
            } else {
                $('#profissao').val(1000).change();
            }
        }
    });

    $('#emenda_status').change(function() {
        if ($('#emenda_status').val() == '+') {
            if (window.confirm("Você realmente deseja inserir uma novo objetivo?")) {
                window.location.href = "?secao=emendas-status";
            } else {
                $('#profissao').val(1000).change();
            }
        }
    });

    $('#orgao').change(function() {
        if ($('#orgao').val() == '+') {
            if (window.confirm("Você realmente deseja inserir um novo órgão?")) {
                window.location.href = "?secao=orgaos";
            } else {
                $('#orgao').val(1000).change();
            }
        }
    });


    $('#btn_novo_orgao').click(function() {
        if (window.confirm("Você realmente deseja inserir um novo órgão?")) {
            window.location.href = "?secao=orgaos";
        } else {
            return false;
        }
    });

    $('button[name="btn_csv"]').on('click', function(event) {
        const confirmacao = confirm("Tem certeza que deseja criar esse arquivo? Essa operação pode levar varios minutos");
        if (!confirmacao) {
            event.preventDefault();
        }
    });

    $('button[name="btn_xls"]').on('click', function(event) {
        const confirmacao = confirm("Tem certeza que deseja criar esse arquivo? Essa operação pode levar varios minutos");
        if (!confirmacao) {
            event.preventDefault();
        }
    });

    window.addEventListener('keydown', function(event) {
        if ((event.ctrlKey || event.metaKey) && event.key === 'p') {
            event.preventDefault(); // Impede a janela de impressão padrão
            alert('Clique no botão imprimir');
        }
    });
</script>