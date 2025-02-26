<?php

ob_start();

use GabineteMvc\Controllers\DocumentoController;
use GabineteMvc\Controllers\OrgaoController;

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

$documentoController = new DocumentoController();
$orgaoController = new OrgaoController();

$documentoGet = $_GET['id'];

$buscaDocumento = $documentoController->buscaDocumento('documento_id', $documentoGet);

if ($buscaDocumento['status'] != 'success') {
    header('Location: ?secao=documentos');
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
                    <a class="btn btn-success btn-sm custom-nav barra_navegacao" href="?secao=documentos" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-file-earmark-text"></i> Editar documento</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível editar um documentos do sistema. O arquivo deve ser em <b>PDF, Word ou Excel</b> e ter até <b>15mb</b></p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios. São permitidos arquivos <b>PDF</b>, <b>WORD</b> e <b>Excel</b>. Tamanho máximo de <b>20MB</b></p>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar'])) {

                        $dados = [
                            'documento_id' => $documentoGet,
                            'documento_titulo' => htmlspecialchars($_POST['documento_titulo'], ENT_QUOTES, 'UTF-8'),
                            'documento_resumo' => htmlspecialchars($_POST['documento_resumo'], ENT_QUOTES, 'UTF-8'),
                            'arquivo' =>  $_FILES['arquivo'],
                            'documento_ano' => htmlspecialchars($_POST['documento_ano'], ENT_QUOTES, 'UTF-8'),
                            'documento_tipo' => htmlspecialchars($_POST['documento_tipo'], ENT_QUOTES, 'UTF-8'),
                            'documento_orgao' => htmlspecialchars($_POST['documento_orgao'], ENT_QUOTES, 'UTF-8'),
                            'documento_resumo' => htmlspecialchars($_POST['documento_resumo'], ENT_QUOTES, 'UTF-8'),
                            'documento_gabinete' => $_SESSION['usuario_gabinete'],
                        ];

                        $result = $documentoController->atualizarDocumento($dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                            echo '<script>
                            setTimeout(function(){
                                window.location.href = "?secao=documento&id=' . $documentoGet . '";
                            }, 1000);</script>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error' || $result['status'] == 'format_not_allowed' || $result['status'] == 'max_file_size_exceeded' || $result['status'] == 'file_already_exists') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        }
                    }


                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {
                        $result = $documentoController->apagarDocumento($documentoGet);
                        if ($result['status'] == 'success') {
                            header('Location: ?secao=documentos');
                            exit;
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error' || $result['status'] == 'forbidden') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                        }
                    }
                    ?>

                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="documento_titulo" placeholder="Número" value="<?php echo $buscaDocumento['dados']['documento_titulo'] ?>" required>
                        </div>
                        <div class="col-md-1 col-12">
                            <input type="text" class="form-control form-control-sm" name="documento_ano" placeholder="Ano" data-mask="0000" value="<?php echo $buscaDocumento['dados']['documento_ano'] ?>" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="documento_resumo" value="<?php echo $buscaDocumento['dados']['documento_resumo'] ?>" placeholder="Resumo" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" id="orgao" name="documento_orgao">
                                <option value="1" selected>Órgão não informado</option>
                                <?php

                                $orgaos = $orgaoController->listarOrgaos(1000, 1, 'asc', 'orgao_nome', null, null, $_SESSION['usuario_cliente']);

                                if ($buscaOrgao['status'] === 'success') {
                                    foreach ($buscaOrgao['dados'] as $orgao) {
                                        if ($orgao['orgao_id'] == $buscaDocumento['dados']['documento_orgao']) {
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
                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" name="documento_tipo" id="tipo" required>
                                <?php
                                $tipos = $documentoController->listarDocumentoTipo($_SESSION['usuario_cliente']);
                                if ($tipos['status'] == 'success') {
                                    foreach ($tipos['dados'] as $tipo) {
                                        if ($buscaDocumento['dados']['documento_tipo'] == $tipo['documento_tipo_id']) {
                                            echo '<option value="' . $tipo['documento_tipo_id'] . '" selected>' . $tipo['documento_tipo_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $tipo['documento_tipo_id'] . '">' . $tipo['documento_tipo_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                                <option value="+">Novo tipo + </option>
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <input type="file" class="form-control form-control-sm" name="arquivo" />
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="documento_resumo" rows="10" placeholder="Resumo do documento"><?php echo $buscaDocumento['dados']['documento_resumo'] ?></textarea>
                        </div>
                        <div class="col-md-5 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_atualizar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                            <button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="bi bi-trash-fill"></i> Apagar</button>
                            <a type="button" href="<?php echo $buscaDocumento['dados']['documento_arquivo'] ?>" download target="_blank" class="btn btn-primary btn-sm"><i class="bi bi-cloud-arrow-down-fill"></i> Download</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body card_descrica_body p-1">
                    <?php
                    $arquivo = $buscaDocumento['dados']['documento_arquivo'];

                    if (file_exists($arquivo)) {
                        // Verifica a extensão do arquivo
                        $extensao = pathinfo($arquivo, PATHINFO_EXTENSION);

                        if ($extensao == 'pdf') {
                            // Exibe o PDF
                            echo "<embed src='$arquivo' type='application/pdf' width='100%' height='1000px'>";
                        } elseif (in_array($extensao, ['doc', 'docx', 'xls', 'xlsx'])) {
                            // Exibe o botão de download para arquivos .doc, .docx, .xls, .xlsx
                            echo '<a href="' . $arquivo . '"><p class="card-text mt-1 ms-2 mb-1"> Arquivo '.$extensao.'. Clique para fazer download.</a></p>';
                        } else {
                            echo '<center><img src="public/img/loading.gif"/></center>';
                        }
                    } else {
                        // Se o arquivo não existir, exibe o GIF de loading
                        echo '<center><img src="public/img/loading.gif"/></center>';
                    }
                    ?>
                </div>
            </div>


        </div>
    </div>
</div>
<script>
    $('#orgao').change(function() {
        if ($('#orgao').val() == '+') {
            if (window.confirm("Você realmente deseja inserir um novo órgão?")) {
                window.location.href = "?secao=orgaos";
            } else {
                $('#orgao').val(1000).change();
            }
        }
    });

    $('#tipo').change(function() {
        if ($('#tipo').val() == '+') {
            if (window.confirm("Você realmente deseja inserir um novo tipo?")) {
                window.location.href = "?secao=tipos-documentos";
            } else {
                $('#orgao').val(1000).change();
            }
        }
    });
</script>