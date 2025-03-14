<?php

use GabineteMvc\Controllers\ClippingController;
use GabineteMvc\Controllers\OrgaoController;

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

$clippingController = new ClippingController();

$orgaoController = new OrgaoController();

$ano = isset($_GET['ano']) ? (int) $_GET['ano'] : date('Y');
$termo = isset($_GET['termo']) ? strtolower(htmlspecialchars($_GET['termo'])) : '';

$busca = $clippingController->listarClippings($termo, $ano, $_SESSION['usuario_gabinete']);

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
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-newspaper"></i> Adicionar Tipo de Clipping</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-2">Seção para arquivamento de todos o tipo de conteúdo sobre o deputado</p>
                    <p class="card-text mb-0">Arquivos permitidos: PDF, JPG, PNG. Todos os campos são obrigatórios</p>
                </div>
            </div>
            <div class="card shadow-sm mb-2 ">
                <div class="card-body p-0 ">
                    <nav class="navbar navbar-expand bg-body-tertiary p-0 ">
                        <div class="container-fluid p-0">
                            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                <ul class="navbar-nav me-auto mb-0 mb-lg-0">
                                    <li class="nav-item">
                                        <a class="nav-link active p-1" aria-current="page" href="#">
                                            <button class="btn btn-primary btn-sm" style="font-size: 0.850em;" id="btn_novo_status" type="button"><i class="bi bi-plus-circle-fill"></i> Novo tipo</button>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {

                        $clipping = [
                            'clipping_resumo' => $_POST['clipping_resumo'],
                            'clipping_data' => $_POST['clipping_data'],
                            'clipping_titulo' => $_POST['clipping_titulo'],
                            'clipping_link' => $_POST['clipping_link'],
                            'clipping_orgao' => $_POST['clipping_orgao'],
                            'arquivo' => $_FILES['arquivo'],
                            'clipping_tipo' => $_POST['clipping_tipo'],
                            'clipping_criado_por' => $_SESSION['usuario_id'],
                            'clipping_gabinete' => $_SESSION['usuario_gabinete']
                        ];

                        $result = $clippingController->criarClipping($clipping);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'format_not_allowed' || $result['status'] == 'max_file_size_exceeded' || $result['status'] == 'file_already_exists') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error' || $result['status'] == 'upload_error' || $result['status'] == 'folder_error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                        }
                    }
                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-2 col-12">
                            <input type="url" class="form-control form-control-sm" name="clipping_link" placeholder="Link (http://...)" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="clipping_titulo" placeholder="Titulo" required>
                        </div>
                        <div class="col-md-1 col-12">
                            <input type="date" class="form-control form-control-sm" name="clipping_data" value="<?php echo date('Y-m-d') ?>" placeholder="Data" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" name="clipping_orgao" id="orgao" required>
                                <option value="1">Veículo não informado</option>
                                <?php
                                $buscaOrgaos = $orgaoController->listarOrgaos(1000, 1, 'asc', 'orgao_nome', null, null, $_SESSION['usuario_gabinete']);
                                if ($buscaOrgaos['status'] == 'success') {
                                    foreach ($buscaOrgaos['dados'] as $orgaos) {
                                        if ($orgaos['orgao_id'] == 1000) {
                                            echo '<option value="' . $orgaos['orgao_id'] . '" selected>' . $orgaos['orgao_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $orgaos['orgao_id'] . '">' . $orgaos['orgao_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                                <option value="+">Novo veículo + </option>
                            </select>
                        </div>

                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" name="clipping_tipo" id="clipping_tipo" required>
                                <?php
                                $buscaTipos = $clippingController->listarClippingTipos($_SESSION['usuario_gabinete']);
                                if ($buscaTipos['status'] == 'success') {
                                    foreach ($buscaTipos['dados'] as $tipos) {
                                        if ($tipos['clipping_tipo_id'] == 1) {
                                            echo '<option value="' . $tipos['clipping_tipo_id'] . '" selected>' . $tipos['clipping_tipo_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $tipos['clipping_tipo_id'] . '">' . $tipos['clipping_tipo_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                                <option value="+">Novo tipo + </option>
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <input type="file" class="form-control form-control-sm" name="arquivo" placeholder="Arquivo" required>
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="clipping_resumo" rows="10" placeholder="Texto do clipping" required></textarea>
                        </div>
                        <div class="col-md-2 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="fa-regular fa-floppy-disk"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row ">
                <div class="col-12">
                    <div class="card shadow-sm mb-2">
                        <div class="card-body p-2">
                            <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                                <div class="col-md-1 col-3">
                                    <input type="hidden" name="secao" value="clippings" />
                                    <input type="number" class="form-control form-control-sm" name="ano" value="<?php echo $ano ?>">
                                </div>
                                <div class="col-md-3 col-7">
                                    <input type="text" class="form-control form-control-sm" name="termo" value="<?php echo $termo ?>" placeholder="Buscar...">
                                </div>
                                <div class="col-md-1 col-2">
                                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Titulo</th>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Veículo</th>
                                    <th scope="col">Criado por - em</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $busca = $clippingController->listarClippings($termo, $ano, $_SESSION['usuario_gabinete']);
                                if ($busca['status'] == 'success') {
                                    foreach ($busca['dados'] as $clippingTipo) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap;"><a href="?secao=clipping&id=' . $clippingTipo['clipping_id'] . '">' . $clippingTipo['clipping_titulo'] . '</a></td>';
                                        echo '<td style="white-space: nowrap;">' . $clippingTipo['clipping_tipo_nome'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . ($clippingTipo['clipping_orgao'] == 1 ? 'Veículo não informado' : $clippingTipo['orgao_nome']) . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $clippingTipo['usuario_nome'] . ' - ' . date('d/m', strtotime($clippingTipo['clipping_criado_em'])) . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($busca['status'] == 'empty') {
                                    echo '<tr><td colspan="4">' . $busca['message'] . '</td></tr>';
                                } else if ($busca['status'] == 'error') {
                                    echo '<tr><td colspan="4">Erro ao carregar os dados.</td></tr>';
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
<script>
    $('#clipping_tipo').change(function() {
        if ($('#clipping_tipo').val() == '+') {
            if (window.confirm("Você realmente deseja inserir um novo tipo de clipping?")) {
                window.location.href = "?secao=tipos-clipping";
            } else {
                $('#clipping_tipo').val(1).change();
            }
        }
    });

    $('#btn_novo_status').click(function() {
        if (window.confirm("Você realmente deseja inserir um novo tipo de clipping?")) {
            window.location.href = "?secao=tipos-clipping";
        } else {
            return false;
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
</script>