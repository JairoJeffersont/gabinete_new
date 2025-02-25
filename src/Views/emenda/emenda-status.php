<?php

ob_start();

use GabineteMvc\Controllers\EmendaController;

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

$emendaStatusController = new EmendaController();

$id = $_GET['id'];

$busca = $emendaStatusController->buscaEmendaStatus($id);

if ($busca['status'] != 'success') {
    header('Location: ?secao=tipos-emendas');
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
                    <a class="btn btn-success btn-sm custom-nav barra_navegacao" href="?secao=emendas-status" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao"><i class="bi bi-building"></i> Editar Status de Emenda</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível editar o status de emenda, garantindo a organização correta dessas informações no sistema.</p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar'])) {
                        $dados = [
                            'emendas_status_nome' => htmlspecialchars($_POST['emendas_status_nome'], ENT_QUOTES, 'UTF-8'),
                            'emendas_status_descricao' => htmlspecialchars($_POST['emendas_status_descricao'], ENT_QUOTES, 'UTF-8'),
                            'emendas_status_criador_por' => $_SESSION['usuario_id'],
                            'emendas_status_gabinete' => $_SESSION['usuario_gabinete'],
                            'emendas_status_criado_por' => $_SESSION['usuario_id'],
                            'emendas_status_id' => $id
                        ];

                        $result = $emendaStatusController->atualizarEmendaStatus($dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            $busca = $emendaStatusController->buscaEmendaStatus($id);
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error' || $result['status'] == 'forbidden') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {

                        $result = $emendaStatusController->apagarEmendaStatus($id);
                        if ($result['status'] == 'success') {
                            header('Location: ?secao=emendas-status');
                        } else if ($result['status'] == 'forbidden' || $result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        }
                    }
                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST">
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="emendas_status_nome" placeholder="Nome do Status" value="<?php echo $busca['dados']['emendas_status_nome'] ?>" required>
                        </div>
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="emendas_status_descricao" placeholder="Descrição" value="<?php echo $busca['dados']['emendas_status_descricao'] ?>" required>
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