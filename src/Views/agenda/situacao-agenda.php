<?php

ob_start();

use GabineteMvc\Controllers\AgendaController;

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

$agendaController = new AgendaController();

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: ?secao=situacoes-agenda');
    exit;
}

$busca = $agendaController->buscaAgendaSituacao($id);

if ($busca['status'] !== 'success') {
    header('Location: ?secao=situacoes-agenda');
    exit;
}

?>

<div class="d-flex" id="wrapper">
    <?php include './src/Views/menus/side_bar.php'; ?>
    <div id="page-content-wrapper">
        <?php include './src/Views/menus/top_menu.php'; ?>
        <div class="container-fluid p-2">
            <div class="card mb-2">
                <div class="card-body p-1">
                    <a class="btn btn-primary btn-sm custom-nav barra_navegacao" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
                    <a class="btn btn-success btn-sm custom-nav barra_navegacao" href="?secao=situacoes-agenda" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-calendar-event"></i> Editar Tipo de Agenda</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível editar os tipos de agenda, garantindo uma organização eficiente.</p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        if (isset($_POST['btn_atualizar'])) {
                            $dados = [
                                'agenda_situacao_id' => $id,
                                'agenda_situacao_nome' => htmlspecialchars($_POST['agenda_tipo_nome'], ENT_QUOTES, 'UTF-8'),
                                'agenda_situacao_descricao' => htmlspecialchars($_POST['agenda_tipo_descricao'], ENT_QUOTES, 'UTF-8'),
                            ];

                            $result = $agendaController->atualizarAgendaSituacao($dados);

                            if ($result['status'] === 'success') {
                                echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                                $busca = $agendaController->buscaAgendaSituacao($id);
                            } else {
                                echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" role="alert">' . $result['message'] . '</div>';
                            }
                        }
                        if (isset($_POST['btn_apagar'])) {
                            $result = $agendaController->apagarAgendaSituacao($id);
                            if ($result['status'] === 'success') {
                                header('Location: ?secao=situacoes-agenda');
                                exit;
                            } else {
                                echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" role="alert">' . $result['message'] . '</div>';
                            }
                        }
                    }
                    ?>
                    <form class="row g-2 form_custom" method="POST">
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="agenda_tipo_nome" placeholder="Nome do Tipo" value="<?php echo $busca['dados']['agenda_situacao_nome']; ?>" required>
                        </div>
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="agenda_tipo_descricao" placeholder="Descrição" value="<?php echo $busca['dados']['agenda_situacao_descricao']; ?>" required>
                        </div>
                        <div class="col-md-6 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_atualizar"><i class="bi bi-floppy-fill"></i> Atualizar</button>
                            <button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="bi bi-trash-fill"></i> Apagar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>