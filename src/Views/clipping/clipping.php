<?php

ob_start();

use GabineteMvc\Controllers\ClippingController;
use GabineteMvc\Controllers\OrgaoController;

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

$clippingController = new ClippingController();

$orgaoController = new OrgaoController();

$clippingGet = $_GET['id'];

$buscaClipping = $clippingController->buscarClipping('clipping_id', $clippingGet);

if ($buscaClipping['status'] != 'success') {
    header('Location: ?secao=clippings');
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
                    <a class="btn btn-success btn-sm custom-nav barra_navegacao" href="?secao=clippings" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-newspaper"></i> Adicionar Tipo de Clipping</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-2">Seção para arquivamento de todos o tipo de conteúdo sobre o deputado</p>
                    <p class="card-text mb-0">Arquivos permitidos: PDF, JPG, PNG. Todos os campos são obrigatórios</p>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar'])) {

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

                        $result = $clippingController->atualizarClipping($clippingGet, $clipping);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            $buscaClipping = $clippingController->buscarClipping('clipping_id', $clippingGet);

                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'format_not_allowed' || $result['status'] == 'max_file_size_exceeded' || $result['status'] == 'file_already_exists') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error' || $result['status'] == 'upload_error' || $result['status'] == 'folder_error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {
                        $result = $clippingController->apagarClipping($clippingGet);
                        if ($result['status'] == 'success') {
                            header('Location: ?secao=clippings');
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                        }
                    }
                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-2 col-12">
                            <input type="url" class="form-control form-control-sm" name="clipping_link" placeholder="Link (http://...)" value="<?php echo $buscaClipping['dados']['clipping_link'] ?>" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="clipping_titulo" placeholder="Titulo" value="<?php echo $buscaClipping['dados']['clipping_titulo'] ?>" required>
                        </div>
                        <div class="col-md-1 col-12">
                            <input type="date" class="form-control form-control-sm" name="clipping_data" value="<?php echo $buscaClipping['dados']['clipping_data'] ?>" placeholder="Data" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" name="clipping_orgao" id="orgao" required>
                                <option value="1">Veículo não informado</option>
                                <?php
                                $buscaOrgaos = $orgaoController->listarOrgaos(1000, 1, 'asc', 'orgao_nome', null, null, $_SESSION['usuario_gabinete']);
                                if ($buscaOrgaos['status'] == 'success') {
                                    foreach ($buscaOrgaos['dados'] as $orgaos) {
                                        if ($orgaos['orgao_id'] == $buscaClipping['dados']['clipping_orgao']) {
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
                                $buscaTipos = $clippingController->ListarClippingTipos($_SESSION['usuario_gabinete']);
                                if ($buscaTipos['status'] == 'success') {
                                    foreach ($buscaTipos['dados'] as $tipos) {
                                        if ($tipos['clipping_tipo_id'] == $buscaClipping['dados']['clipping_tipo']) {
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
                            <input type="file" class="form-control form-control-sm" name="arquivo" placeholder="Arquivo">
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="clipping_resumo" rows="10" placeholder="Texto do clipping" required><?php echo $buscaClipping['dados']['clipping_resumo'] ?></textarea>
                        </div>
                        <div class="col-md-2 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_atualizar"><i class="bi bi-floppy-fill"></i> Atualizar</button>
                            <button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="bi bi-trash-fill"></i> Apagar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    
                    $arquivo = $buscaClipping['dados']['clipping_arquivo'];
                    $extensao = pathinfo($arquivo, PATHINFO_EXTENSION);

                    if (in_array($extensao, ['png', 'jpg', 'jpeg'])) {
                        echo '<img src="' . $arquivo . '" class="img-fluid" alt="Imagem do clipping">';
                    } else {
                        echo '<embed src="' . $arquivo . '" type="application/pdf" width="100%" height="600px" />';
                    }
                    ?>
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