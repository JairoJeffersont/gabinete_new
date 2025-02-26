<?php

ob_start();

use GabineteMvc\Controllers\clippingController;

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

$clippingController = new ClippingController();

$id = $_GET['id'];

$busca = $clippingController->buscaClippingTipo($id);


if ($busca['status'] != 'success') {
    header('Location: ?secao=tipos-clipping');
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
                    <a class="btn btn-success btn-sm custom-nav barra_navegacao" href="?secao=tipos-clipping" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao"><i class="bi bi-building"></i> Editar tipo de Clipping</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível editar um tipo de clipping, garantindo a organização correta dessas informações no sistema.</p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar'])) {
                        $dados = [
                            'clipping_tipo_nome' => htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'),
                            'clipping_tipo_descricao' => htmlspecialchars($_POST['descricao'], ENT_QUOTES, 'UTF-8'),
                            'clipping_tipo_id' => $id,
                        ];

                        $result = $clippingController->atualizarClippingTipo($dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            $busca = $clippingController->buscaClippingTipo($id);
                        } else {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" role="alert">' . $result['message'] . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {
                        $result = $clippingController->apagarClippingTipo($id);
                        if ($result['status'] == 'success') {
                            header('Location: ?secao=tipos-clipping');
                        } else {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" role="alert">' . $result['message'] . '</div>';
                        }
                    }
                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST">
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="nome" placeholder="Nome do Tipo" value="<?php echo $busca['dados']['clipping_tipo_nome'] ?>" required>
                        </div>
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="descricao" placeholder="Descrição" value="<?php echo $busca['dados']['clipping_tipo_descricao'] ?>" required>
                        </div>
                        <div class="col-md-6 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_atualizar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                            <button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="bi bi-trash-fill"></i> Apagar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>