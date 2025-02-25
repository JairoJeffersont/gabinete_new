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



$emendaGet = $_GET['id'];

$buscaEmenda = $emendaController->buscarEmenda('emenda_id', $emendaGet);

if ($buscaEmenda['status'] == 'not_found' || $buscaEmenda['status'] == 'error') {
    header('Location: ?secao=emendas');
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
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-cash-stack"></i> Adicionar Emenda</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-2">Nesta página, você pode cadastrar novas emendas, informando dados como número, valor, descrição, status, órgão responsável, município e objetivo.</p>
                    <p class="card-text mb-0">Além disso, é possível filtrar e visualizar emendas já cadastradas, organizadas por diferentes critérios como número, valor, status e município.
                </div>
            </div>
            <div class="card shadow-sm mb-2 no-print">
                <div class="card-body card_descricao_body p-0">
                    <nav class="navbar navbar-expand bg-body-tertiary p-0 ">
                        <div class="container-fluid p-0">
                            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                <ul class="navbar-nav me-auto mb-0 mb-lg-0">
                                    <li class="nav-item">
                                        <a class="nav-link active p-1" aria-current="page" href="#">
                                            <button class="btn btn-success btn-sm" style="font-size: 0.850em;" id="btn_novo_objetivo" type="button"><i class="bi bi-plus-circle-fill"></i> Novo objetivo</button>
                                            <button class="btn btn-secondary btn-sm" style="font-size: 0.850em;" id="btn_nova_status" type="button"><i class="bi bi-plus-circle-fill"></i> Novo status</button>
                                            <button class="btn btn-primary btn-sm" style="font-size: 0.850em;" id="btn_novo_orgao" type="button"><i class="bi bi-plus-circle-fill"></i> Novo órgão</button>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
            <div class="card shadow-sm mb-2 no-print">
                <div class="card-body p-2">
                    <?php
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

                        ];

                        $result = $emendaController->atualizarEmenda($emendaGet, $dadosEmenda);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                            $buscaEmenda = $emendaController->buscarEmenda('emenda_id', $emendaGet);
                        } else {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {
                        $result = $emendaController->apagarEmenda($emendaGet);
                        if ($result['status'] == 'success') {
                            header('Location: ?secao=emendas');
                            exit;
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                        }
                    }
                    ?>

                    <form class="row g-2 form_custom no-print" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="emenda_numero" placeholder="Número da Emenda" value="<?php echo $buscaEmenda['dados']['emenda_numero'] ?>" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm" name="emenda_valor" id="emenda_valor" placeholder="Valor da Emenda (R$)" value="<?php echo $buscaEmenda['dados']['emenda_valor'] ?>" required>
                        </div>
                        <div class="col-md-8 col-12">
                            <input type="text" class="form-control form-control-sm" name="emenda_descricao" placeholder="Descrição" value="<?php echo $buscaEmenda['dados']['emenda_descricao'] ?>" required>
                        </div>
                        <div class="col-md-4 col-12">
                            <select class="form-select form-select-sm" name="emenda_status" id="emenda_status" required>
                                <?php

                                $emendasStatus = $emendaController->listarEmendaStatus($_SESSION['usuario_gabinete']);

                                if ($emendasStatus['status'] == 'success') {
                                    foreach ($emendasStatus['dados'] as $status) {
                                        if ($status['emendas_status_id'] == 1 || $status['emendas_status_id'] == $buscaEmenda['dados']['emenda_status']) {
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
                                        if ($orgao['orgao_id'] == 1 || $orgao['orgao_id'] == $buscaEmenda['dados']['emenda_orgao']) {
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
                                        if ($objetivo['emendas_objetivos_id'] == 1 || $objetivo['emendas_objetivos_id'] ==  $buscaEmenda['dados']['emenda_objetivo']) {
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
                                <option value="1" <?= ($buscaEmenda['dados']['emenda_tipo'] == 1) ? 'selected' : ''; ?>>Emenda parlamentar</option>
                                <option value="2" <?= ($buscaEmenda['dados']['emenda_tipo'] == 2) ? 'selected' : ''; ?>>Emenda de bancada</option>
                            </select>
                        </div>

                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="emenda_informacoes" placeholder="Informações Adicionais. Ex. Ordem de pagamento, códigos gerais..." rows="5" required><?php echo $buscaEmenda['dados']['emenda_informacoes'] ?></textarea>
                        </div>

                        <div class="col-md-3 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                            <button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="bi bi-trash-fill"></i> Apagar</button>

                        </div>
                    </form>
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
                if (estado.sigla === "<?php echo $buscaEmenda['dados']['emenda_estado'] ?>") {
                    setTimeout(function() {
                        selectEstado.append(`<option value="${estado.sigla}" selected>${estado.sigla}</option>`).change();
                    }, 500);

                } else {
                    setTimeout(function() {
                        selectEstado.append(`<option value="${estado.sigla}">${estado.sigla}</option>`);
                    }, 500);
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
                if (municipio.nome === "<?php echo $buscaEmenda['dados']['emenda_municipio'] ?>") {
                    selectMunicipio.append(`<option value="${municipio.nome}" selected>${municipio.nome}</option>`);
                } else {
                    selectMunicipio.append(`<option value="${municipio.nome}">${municipio.nome}</option>`);
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
</script>
