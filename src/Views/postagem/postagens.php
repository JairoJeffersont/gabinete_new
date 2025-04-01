<?php

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

use GabineteMvc\Controllers\PostagemController;

$postagemController = new PostagemController;


$itens = isset($_GET['itens']) ? (int) $_GET['itens'] : 10;
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$ordenarPor = isset($_GET['ordenarPor']) && in_array(htmlspecialchars($_GET['ordenarPor']), ['postagem_titulo', 'postagem_status', 'postagem_data', 'postagem_criada_em']) ? htmlspecialchars($_GET['ordenarPor']) : 'postagem_criada_em';
$ordem = isset($_GET['ordem']) ? strtolower(htmlspecialchars($_GET['ordem'])) : 'desc';
$ano = isset($_GET['ano']) ? (int) $_GET['ano'] : date('Y');
$situacao = isset($_GET['situacao']) ? strtolower(htmlspecialchars($_GET['situacao'])) : 'all';
?>
<div class="d-flex" id="wrapper">
    <?php include './src/Views/menus/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/menus/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav barra_navegacao" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                </div>
            </div>
            <div class="card mb-2 ">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-stickies"></i> Adicionar Postagem</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível adicionar e editar as postagens, garantindo a organização correta dessas informações no sistema.</p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>
            <div class="card shadow-sm mb-2 ">
                <div class="card-body  p-0">
                    <nav class="navbar navbar-expand bg-body-tertiary p-0 ">
                        <div class="container-fluid p-0">
                            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                <ul class="navbar-nav me-auto mb-0 mb-lg-0">
                                    <li class="nav-item">
                                        <a class="nav-link active p-1" aria-current="page" href="#">
                                            <button class="btn btn-primary btn-sm" style="font-size: 0.850em;" id="btn_nova_situacao" type="button"><i class="bi bi-plus-circle-fill"></i> Nova situação</button>
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

                        $dados = [
                            'postagem_titulo' => htmlspecialchars($_POST['postagem_titulo'], ENT_QUOTES, 'UTF-8'),
                            'postagem_informacoes' => htmlspecialchars($_POST['postagem_informacoes'], ENT_QUOTES, 'UTF-8'),
                            'postagem_data' => htmlspecialchars($_POST['postagem_data'], ENT_QUOTES, 'UTF-8'),
                            'postagem_midias' => htmlspecialchars($_POST['postagem_midias'], ENT_QUOTES, 'UTF-8'),
                            'postagem_status' => htmlspecialchars($_POST['postagem_status'], ENT_QUOTES, 'UTF-8'),
                            'postagem_criada_por' => $_SESSION['usuario_id'],
                            'postagem_gabinete' => $_SESSION['usuario_gabinete'],
                        ];

                        $result = $postagemController->criarPostagem($dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error' || $result['status'] == 'forbidden') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['error_id']) ? ' | Código do erro: ' . $result['error_id'] : '') . '</div>';
                        }
                    }
                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-3 col-12">
                            <input type="text" class="form-control form-control-sm" name="postagem_titulo" placeholder="Título" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <input type="text" class="form-control form-control-sm" name="postagem_midias" placeholder="Mídias (facebook, instagram, site...)" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <input type="date" class="form-control form-control-sm" name="postagem_data" value="<?php echo date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-3 col-12">
                            <select class="form-select form-select-sm" name="postagem_status" id="status_postagem" required>

                                <?php
                                $status_postagens = $postagemController->listarPostagemStatus($_SESSION['usuario_gabinete']);
                                if ($status_postagens['status'] == 'success') {
                                    foreach ($status_postagens['dados'] as $status) {
                                        if ($status['postagem_status_id'] == 1) {
                                            echo '<option value="' . $status['postagem_status_id'] . '" selected>' . $status['postagem_status_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $status['postagem_status_id'] . '">' . $status['postagem_status_nome'] . '</option>';
                                        }
                                    }
                                }

                                ?>
                                <option value="+">Novo tipo + </option>
                            </select>
                        </div>
                        <div class="col-md-12 col-12">
                            <script>
                                tinymce.init({
                                    selector: 'textarea',
                                    language: 'pt_BR',
                                    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                                    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                                    setup: function(editor) {
                                        editor.on('change', function() {
                                            tinymce.triggerSave(); // Atualiza a `<textarea>`
                                        });
                                    }
                                });
                            </script>

                            <textarea class="form-control form-control-sm" name="postagem_informacoes" placeholder="Informações, textos, instruções..." rows="4"></textarea>
                        </div>
                        <div class="col-md-3 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="fa-regular fa-floppy-disk"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row ">
                <div class="col-12">
                    <div class="card shadow-sm mb-2">
                        <div class="card-body card_description_body p-2">
                            <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                                <div class="col-md-2 col-6">
                                    <input type="hidden" name="secao" value="postagens" />
                                    <select class="form-select form-select-sm" name="ordenarPor" required>
                                        <option value="postagem_titulo" <?php echo $ordenarPor == 'postagem_titulo' ? 'selected' : ''; ?>>Ordenar por | Titulo</option>
                                        <option value="postagem_data" <?php echo $ordenarPor == 'postagem_data' ? 'selected' : ''; ?>>Ordenar por | Data</option>
                                        <option value="postagem_status " <?php echo $ordenarPor == 'postagem_status ' ? 'selected' : ''; ?>>Ordenar por | Situação</option>
                                        <option value="postagem_criada_por " <?php echo $ordenarPor == 'postagem_criada_por ' ? 'selected' : ''; ?>>Ordenar por | Criação</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-6">
                                    <select class="form-select form-select-sm" name="ordem" required>
                                        <option value="asc" <?php echo $ordem == 'asc' ? 'selected' : ''; ?>>Ordem Crescente</option>
                                        <option value="desc" <?php echo $ordem == 'desc' ? 'selected' : ''; ?>>Ordem Decrescente</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-12">
                                    <select class="form-select form-select-sm" name="situacao" required>
                                        <option value="all" <?php echo $situacao == 'all' ? 'selected' : ''; ?>>Mostrar tudo</option>
                                        <?php
                                        $buscaSituacao = $postagemController->listarPostagemStatus($_SESSION['usuario_gabinete']);
                                        foreach ($buscaSituacao['dados'] as $status) {
                                            if($situacao == $status['postagem_status_id']){
                                                
                                                echo '<option value="' . $status['postagem_status_id'] . '" selected>' . $status['postagem_status_nome'] . '</option>';

                                            }else{
                                                echo '<option value="' . $status['postagem_status_id'] . '">' . $status['postagem_status_nome'] . '</option>';

                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-2 col-4">
                                    <input type="number" class="form-control form-control-sm" name="ano" value="<?php echo $ano ?>">
                                </div>
                                <div class="col-md-2 col-6">
                                    <select class="form-select form-select-sm" name="itens" required>
                                        <option value="5" <?php echo $itens == 5 ? 'selected' : ''; ?>>5 itens</option>
                                        <option value="10" <?php echo $itens == 10 ? 'selected' : ''; ?>>10 itens</option>
                                        <option value="25" <?php echo $itens == 25 ? 'selected' : ''; ?>>25 itens</option>
                                        <option value="50" <?php echo $itens == 50 ? 'selected' : ''; ?>>50 itens</option>
                                    </select>
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
                                    <th scope="col">Título</th>
                                    <th scope="col">Mídias</th>
                                    <th scope="col">Data</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Criado por - em</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $busca = $postagemController->listarPostagens($itens, $pagina, $ordem, $ordenarPor, $situacao, $ano, $_SESSION['usuario_gabinete']);
                                if ($busca['status'] == 'success') {
                                    foreach ($busca['dados'] as $postagem) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap;"><a href="?secao=postagem&id=' . $postagem['postagem_id'] . '">' . $postagem['postagem_titulo'] . '</a></td>';
                                        echo '<td style="white-space: nowrap;">' . $postagem['postagem_midias'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . date('d/m/Y', strtotime($postagem['postagem_data'])) . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $postagem['postagem_status_nome'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $postagem['usuario_nome'] . ' - ' . date('d/m', strtotime($postagem['postagem_criada_em'])) . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($busca['status'] == 'not_found') {
                                    echo '<tr><td colspan="5">' . $busca['message'] . '</td></tr>';
                                } else if ($busca['status'] == 'error') {
                                    echo '<tr><td colspan="5">' . $busca['message'] . ' | Código do erro: ' . $busca['error_id'] . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        if (isset($busca['total_paginas'])) {
                            $totalPagina = $busca['total_paginas'];
                        } else {
                            $totalPagina = 0;
                        }

                        if ($totalPagina > 0 && $totalPagina != 1) {
                            echo '<ul class="pagination custom-pagination mt-2 mb-0">';
                            echo '<li class="page-item ' . ($pagina == 1 ? 'active' : '') . '"><a class="page-link" href="?secao=postagens&itens=' . $itens . '&pagina=1&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . '&ano=' . $ano . '">Primeira</a></li>';

                            for ($i = 1; $i < $totalPagina - 1; $i++) {
                                $pageNumber = $i + 1;
                                echo '<li class="page-item ' . ($pagina == $pageNumber ? 'active' : '') . '"><a class="page-link" href="?secao=postagens&itens=' . $itens . '&pagina=' . $pageNumber . '&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . '&ano=' . $ano . '">' . $pageNumber . '</a></li>';
                            }

                            echo '<li class="page-item ' . ($pagina == $totalPagina ? 'active' : '') . '"><a class="page-link" href="?secao=postagens&itens=' . $itens . '&pagina=' . $totalPagina . '&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . '&ano=' . $ano . '">Última</a></li>';
                            echo '</ul>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#status_postagem').change(function() {
        if ($('#status_postagem').val() == '+') {
            if (window.confirm("Você realmente deseja inserir um novo status?")) {
                window.location.href = "?secao=postagens-status";
            } else {
                $('#orgao').val(1000).change();
            }
        }
    });

    $('#btn_nova_situacao').click(function() {
        if (window.confirm("Você realmente deseja inserir uma nova situação de postagem?")) {
            window.location.href = "?secao=postagens-status";
        } else {
            return false;
        }
    });
</script>