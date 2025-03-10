<?php

ob_start();

use GabineteMvc\Controllers\AgendaController;
use GabineteMvc\Controllers\GabineteController;

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

$agendaController = new AgendaController();

$gabineteController = new GabineteController();
$buscaGabinete = $gabineteController->buscaGabinete('gabinete_id', $_SESSION['usuario_gabinete']);

$dataGet = isset($_GET['data']) ? $_GET['data'] : date('Y-m-d');
$tipoGet = (isset($_GET['tipo']) && $_GET['tipo'] !== 'null') ? $_GET['tipo'] : null;
$situacaoGet = (isset($_GET['situacao']) && $_GET['situacao'] !== 'null') ? $_GET['situacao'] : null;

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
            <div class="card mb-2">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-calendar-event"></i> Adicionar agenda</div>
                <div class="card-body card_descricao_body p-2">
                <p class="card-text mb-2">Nesta seção, é possível adicionar e editar os compromissos, garantindo a organização correta dessas informações no sistema.</p>
                <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-1">
                    <form class="row g-2 form_custom mb-0" method="post" enctype="application/x-www-form-urlencoded">
                        <div class="col-md-12 col-12">
                            <a href="?secao=tipos-agenda" type="button" class="btn btn-success btn-sm" id="btn_novo_tipo"><i class="bi bi-plus-circle-fill"></i> Novo tipo</a>
                            <a href="?secao=situacoes-agenda" type="button" class="btn btn-primary btn-sm" id="btn_novo_situacao"><i class="bi bi-plus-circle-fill"></i> Nova situação</a>
                            <a href="?secao=imprimir-pessoas&estado=<?php echo $estado ?>" type="button" target="_blank" class="btn btn-secondary btn-sm" id="btn_imprimir"><i class="bi bi-printer"></i> Imprimir agenda</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">


                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {

                        $dados = [
                            'agenda_titulo' => htmlspecialchars($_POST['agenda_titulo'], ENT_QUOTES, 'UTF-8'),
                            'agenda_situacao' => htmlspecialchars($_POST['agenda_situacao'], ENT_QUOTES, 'UTF-8'),
                            'agenda_tipo' => htmlspecialchars($_POST['agenda_tipo'], ENT_QUOTES, 'UTF-8'),
                            'agenda_data' => htmlspecialchars($_POST['agenda_data'], ENT_QUOTES, 'UTF-8'),
                            'agenda_local' => htmlspecialchars($_POST['agenda_local'], ENT_QUOTES, 'UTF-8'),
                            'agenda_estado' => htmlspecialchars($_POST['agenda_estado'], ENT_QUOTES, 'UTF-8'),
                            'agenda_informacoes' => htmlspecialchars($_POST['agenda_informacoes'], ENT_QUOTES, 'UTF-8'),
                            'agenda_criada_por' => $_SESSION['usuario_id'],
                            'agenda_gabinete' => $_SESSION['usuario_gabinete'],
                        ];

                        $result = $agendaController->novaAgenda($dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            $buscaAgendas = $agendaController->listarAgendas($dataGet, $tipoGet, $situacaoGet, $_SESSION['usuario_gabinete']);
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['error_id']) ? ' | Código do erro: ' . $result['error_id'] : '') . '</div>';
                        }
                    }
                    ?>

                    <form class="row g-2 form_custom" id="form_novo" method="POST">
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="agenda_titulo" placeholder="Titulo do compromisso" required>
                        </div>

                        <div class="col-md-4 col-12">
                            <select class="form-select form-select-sm" name="agenda_situacao" id="situacao" required>
                                <?php
                                $buscaSituacoes = $agendaController->listarAgendaSituacoes($_SESSION['usuario_gabinete']);
                                if ($buscaSituacoes['status'] == 'success') {
                                    foreach ($buscaSituacoes['dados'] as $situacao) {
                                        if ($situacao['agenda_situacao_id'] == 1) {
                                            echo '<option value="' . $situacao['agenda_situacao_id'] . '" selected>' . $situacao['agenda_situacao_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $situacao['agenda_situacao_id'] . '">' . $situacao['agenda_situacao_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                                <option value="+">Nova situação + </option>
                            </select>
                        </div>

                        <div class="col-md-4 col-12">
                            <select class="form-select form-select-sm" name="agenda_tipo" id="tipo" required>
                                <?php
                                $buscaTipos = $agendaController->listarAgendaTipos($_SESSION['usuario_gabinete']);
                                if ($buscaTipos['status'] == 'success') {
                                    foreach ($buscaTipos['dados'] as $tipo) {
                                        if ($tipo['agenda_tipo_id'] == 1) {
                                            echo '<option value="' . $tipo['agenda_tipo_id'] . '" selected>' . $tipo['agenda_tipo_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $tipo['agenda_tipo_id'] . '">' . $tipo['agenda_tipo_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                                <option value="+">Nova tipo + </option>
                            </select>
                        </div>
                        <div class="col-md-1 col-12">
                            <input type="datetime-local" class="form-control form-control-sm" name="agenda_data" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                        </div>
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="agenda_local" placeholder="Local da agenda" required>
                        </div>

                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" name="agenda_estado" required>
                                <option value="DF">Brasília</option>
                                <option value="<?php echo $buscaGabinete['dados']['gabinete_estado_autoridade'] ?>">Estado - <?php echo $buscaGabinete['dados']['gabinete_estado_autoridade'] ?></option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>

                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="agenda_informacoes" rows="5" placeholder="Informações da agenda" required></textarea>
                        </div>

                        <div class="col-md-1 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm mb-2 no-print">
                <div class="card-body p-2">
                    <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                        <div class="col-md-1 col-12">
                            <input type="hidden" name="secao" value="agendas" />
                            <input type="date" class="form-control form-control-sm" name="data" value="<?php echo $dataGet ?>">
                        </div>
                        <div class="col-md-2 col-5">
                            <select class="form-select form-select-sm" name="tipo" required>
                                <option value="null">Tudo</option>
                                <?php
                                $buscaTipos = $agendaController->listarAgendaTipos($_SESSION['usuario_gabinete']);
                                if ($buscaTipos['status'] == 'success') {
                                    foreach ($buscaTipos['dados'] as $tipo) {
                                        if ($tipo['agenda_tipo_id'] == $tipoGet) {
                                            echo '<option value="' . $tipo['agenda_tipo_id'] . '" selected>' . $tipo['agenda_tipo_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $tipo['agenda_tipo_id'] . '">' . $tipo['agenda_tipo_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>

                            </select>
                        </div>

                        <div class="col-md-2 col-5">
                            <select class="form-select form-select-sm" name="situacao" required>
                                <option value="null">Tudo</option>

                                <?php
                                $buscaSituacoes = $agendaController->listarAgendaSituacoes($_SESSION['usuario_gabinete']);
                                if ($buscaSituacoes['status'] == 'success') {
                                    foreach ($buscaSituacoes['dados'] as $situacao) {
                                        if ($situacao['agenda_situacao_id'] == $situacaoGet) {
                                            echo '<option value="' . $situacao['agenda_situacao_id'] . '" selected>' . $situacao['agenda_situacao_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $situacao['agenda_situacao_id'] . '">' . $situacao['agenda_situacao_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>

                            </select>
                        </div>

                        <div class="col-md-1 col-2">
                            <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body card_descricao_body p-2">
                    <?php
                    $buscaAgendas = $agendaController->listarAgendas($dataGet, $tipoGet, $situacaoGet, $_SESSION['usuario_gabinete']);

                    if ($buscaAgendas['status'] == 'success') {
                        echo ' <div class="accordion" id="accordionPanelsStayOpenExample">';
                        foreach ($buscaAgendas['dados'] as $agenda) {
                            echo '
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" style="font-size: 0.5em" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse' . $agenda['agenda_id'] . '" aria-expanded="true" aria-controls="panelsStayOpen-collapse' . $agenda['agenda_id'] . '">
                                                ' . date('H:i', strtotime($agenda['agenda_data'])) . ' | ' . $agenda['agenda_titulo'] . '
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapse' . $agenda['agenda_id'] . '" class="accordion-collapse collapse">
                                            <div class="accordion-body" style="font-size: 0.9em">
                                                <p class="card-text mb-1"><i class="bi bi-arrow-right-short"></i> ' . $agenda['agenda_tipo_nome'] . '</p>
                                                <p class="card-text mb-3"><i class="bi bi-arrow-right-short"></i> <b>' . $agenda['agenda_situacao_nome'] . '</b></p>
                                                <p class="card-text mb-3"><i class="bi bi-arrow-right-short"></i> ' . $agenda['agenda_local'] . ' - ' . $agenda['agenda_estado'] . '</p>
                                                <p class="card-text mb-0"><i class="bi bi-arrow-right-short"></i> ' . $agenda['agenda_informacoes'] . '</p><hr>
                                                
                                                <div class="d-flex gap-1 mt-2">
                                                    <a href="?secao=agenda&id=' . $agenda['agenda_id'] . '" class="btn btn-sm btn-primary px-2 py-1" style="font-size: 0.9em"><i class="bi bi-pencil"></i> Editar</a>
                                                    <!--<button class="btn btn-sm btn-success px-2 py-1" style="font-size: 0.9em"><i class="bi bi-whatsapp"></i> Enviar</button>-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ';
                        }
                        echo '</div>';
                    } else {
                        echo '<p class="card-text">Nenhuma agenda para o dia <b>' . date('d/m', strtotime($dataGet)) . '</b></p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#btn_novo_tipo').click(function() {
        if (window.confirm("Você realmente deseja inserir um novo tipo?")) {
            window.location.href = "?secao=tipos-agenda";
        } else {
            return false;
        }
    });
    $('#btn_novo_situacao').click(function() {
        if (window.confirm("Você realmente deseja inserir uma nova situação?")) {
            window.location.href = "?secao=situacoes-agenda";
        } else {
            return false;
        }
    });


    $('#situacao').change(function() {
        if ($('#situacao').val() == '+') {
            if (window.confirm("Você realmente deseja inserir uma nova situação?")) {
                window.location.href = "?secao=situacoes-agenda";
            } else {
                $('#situacao').val(1000).change();
            }
        }
    });

    $('#tipo').change(function() {
        if ($('#tipo').val() == '+') {
            if (window.confirm("Você realmente deseja inserir um novo tipo?")) {
                window.location.href = "?secao=tipos-agenda";
            } else {
                $('#tipo').val(1000).change();
            }
        }
    });
</script>