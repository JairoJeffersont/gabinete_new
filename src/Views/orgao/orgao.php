<?php

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

use GabineteMvc\Controllers\OrgaoController;

$orgaoController = new OrgaoController();

$id = $_GET['id'];

$buscaOrgao = $orgaoController->buscaOrgao('orgao_id', $id);

if ($buscaOrgao['status'] != 'success') {
    header('Location: ?secao=orgaos');
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
                    <a class="btn btn-success btn-sm custom-nav barra_navegacao" href="?secao=orgaos" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-building"></i> Editar tipo de Órgão/Entidade</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível editar um tipo de órgão ou entidades, garantindo a organização correta dessas informações no sistema.</p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>
            <div class="card shadow-sm mb-2 ">
                <div class="card-body card_descricao_body p-0">
                    <nav class="navbar navbar-expand bg-body-tertiary p-0 ">
                        <div class="container-fluid p-0">
                            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                <ul class="navbar-nav me-auto mb-0 mb-lg-0">
                                    <li class="nav-item">
                                        <a class="nav-link active p-1" aria-current="page" href="#">
                                            <button class="btn btn-success btn-sm" style="font-size: 0.850em;" id="btn_novo_tipo" type="button"><i class="bi bi-plus-circle-fill"></i> Novo tipo</button>
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
                            'orgao_nome' => htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'),
                            'orgao_email' => htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'),
                            'orgao_telefone' => htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8'),
                            'orgao_endereco' => htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8'),
                            'orgao_cep' => htmlspecialchars($_POST['cep'], ENT_QUOTES, 'UTF-8'),
                            'orgao_bairro' => htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8'),
                            'orgao_estado' => htmlspecialchars($_POST['estado'], ENT_QUOTES, 'UTF-8'),
                            'orgao_municipio' => htmlspecialchars($_POST['municipio'], ENT_QUOTES, 'UTF-8'),
                            'orgao_tipo' => htmlspecialchars($_POST['tipo'], ENT_QUOTES, 'UTF-8'),
                            'orgao_site' => htmlspecialchars($_POST['site'], ENT_QUOTES, 'UTF-8'),
                            'orgao_informacoes' => htmlspecialchars($_POST['informacoes'], ENT_QUOTES, 'UTF-8'),
                            'orgao_criado_por' => $_SESSION['usuario_id'],
                            'orgao_gabinete' => $_SESSION['usuario_gabinete'],
                            'orgao_id' => $id
                        ];

                        $result = $orgaoController->atualizarOrgao($dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" role="alert" data-timeout="3">' . $result['message'] . '</div>';
                            $buscaOrgao = $orgaoController->buscaOrgao('orgao_id', $id);
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" role="alert" data-timeout="3">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['error_id']) ? ' | Código do erro: ' . $result['error_id'] : '') . '</div>';
                        }
                    }
                    ?>
                    <form class="row g-2 form_custom " id="form_novo" method="POST" enctype="application/x-www-form-urlencoded">
                        <div class="col-md-5 col-12">
                            <input type="text" class="form-control form-control-sm" name="nome" placeholder="Nome" value="<?php echo $buscaOrgao['dados']['orgao_nome'] ?>" required>
                        </div>
                        <div class="col-md-4 col-6">
                            <input type="email" class="form-control form-control-sm" name="email" placeholder="Email" value="<?php echo $buscaOrgao['dados']['orgao_email'] ?>" required>
                        </div>
                        <div class="col-md-3 col-6">
                            <input type="text" class="form-control form-control-sm" name="telefone" placeholder="Telefone (somente números)" value="<?php echo $buscaOrgao['dados']['orgao_telefone'] ?>" data-mask="(00) 00000-0000" maxlength="15">
                        </div>
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="endereco" placeholder="Endereço" value="<?php echo $buscaOrgao['dados']['orgao_endereco'] ?>">
                        </div>
                        <div class="col-md-3 col-6">
                            <input type="text" class="form-control form-control-sm" name="bairro" placeholder="Bairro" value="<?php echo $buscaOrgao['dados']['orgao_bairro'] ?>">
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="cep" placeholder="CEP (somente números)" value="<?php echo $buscaOrgao['dados']['orgao_cep'] ?>" data-mask="00000-000" maxlength="8">
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" id="estado" name="estado" required>
                                <option value="" selected>UF</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <select class="form-select form-select-sm" id="municipio" name="municipio" required>
                                <option value="" selected>Município</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <select class="form-select form-select-sm" id="tipo" name="tipo" required>
                                <?php
                                $busca = $orgaoController->listarOrgaosTipos($_SESSION['usuario_gabinete']);

                                if ($busca['status'] == 'success') {
                                    foreach ($busca['dados'] as $orgaoTipo) {
                                        if ($orgaoTipo['orgao_tipo_id'] == $buscaOrgao['dados']['orgao_tipo']) {
                                            echo '<option value="' . $orgaoTipo['orgao_tipo_id'] . '" selected>' . $orgaoTipo['orgao_tipo_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $orgaoTipo['orgao_tipo_id'] . '">' . $orgaoTipo['orgao_tipo_nome'] . '</option>';
                                        }
                                    }
                                } else if ($busca['status'] == 'not_found') {
                                    echo '<option>' . $busca['message'] . '</option>';
                                } else if ($busca['status'] == 'error') {
                                    echo '<option>' . $busca['message'] . '</option>';
                                }
                                ?>
                                <option value="+">Novo tipo + </option>
                            </select>
                        </div>
                        <div class="col-md-9 col-12">
                            <input type="text" class="form-control form-control-sm" name="site" placeholder="Site ou rede sociais" value="<?php echo $buscaOrgao['dados']['orgao_site'] ?>">
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="informacoes" rows="5" placeholder="Informações importantes desse órgão"><?php echo $buscaOrgao['dados']['orgao_informacoes'] ?></textarea>
                        </div>
                        <div class="col-md-4 col-6">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="fa-regular fa-floppy-disk"></i> Salvar</button>
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
    });

    function carregarEstados() {
        $.getJSON('https://servicodados.ibge.gov.br/api/v1/localidades/estados?orderBy=nome', function(data) {
            const selectEstado = $('#estado');
            selectEstado.empty();
            selectEstado.append('<option value="" selected>UF</option>');
            data.forEach(estado => {
                if (estado.sigla === "<?php echo $buscaOrgao['dados']['orgao_estado'] ?>") {
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
                if (municipio.nome === "<?php echo $buscaOrgao['dados']['orgao_municipio'] ?>") {
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


    $('#tipo').change(function() {
        if ($('#tipo').val() == '+') {
            if (window.confirm("Você realmente deseja inserir um novo tipo?")) {
                window.location.href = "?secao=tipos-orgaos";
            } else {
                $('#tipo').val(1000).change();
            }
        }
    });

    $('#btn_novo_tipo').click(function() {
        if (window.confirm("Você realmente deseja inserir um novo tipo?")) {
            window.location.href = "?secao=tipos-orgaos";
        } else {
            return false;
        }
    });
</script>