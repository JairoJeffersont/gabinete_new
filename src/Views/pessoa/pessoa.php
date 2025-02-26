<?php

ob_start();

use GabineteMvc\Controllers\GabineteController;
use GabineteMvc\Controllers\OrgaoController;
use GabineteMvc\Controllers\PessoaController;
use GabineteMvc\Middleware\Utils;

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

$pessoaController = new PessoaController();
$orgaoController = new OrgaoController();
$gabineteController = new GabineteController();

$busca = $pessoaController->listarProfissoesPessoa($_SESSION['usuario_gabinete']);
$utils = new Utils();

$pessoaGet = $_GET['id'];

$buscaPessoa = $pessoaController->buscaPessoa('pessoa_id', $pessoaGet);

if ($buscaPessoa['status'] == 'not_found' || is_integer($pessoaGet) || $buscaPessoa['status'] == 'error') {
    header('Location: ?secao=pessoas');
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
                    <a class="btn btn-success btn-sm custom-nav barra_navegacao" href="?secao=pessoas" role="button"><i class="bi bi-arrow-left"></i> Voltar</a>

                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-people-fill"></i> Pessoas</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível adicionar e editar pessoas de interesso do mandato., garantindo a organização correta dessas informações no sistema.</p>
                    <p class="card-text mb-0">Os campos <b>Nome</b>, <b>email</b>, <b>aniversário</b>, <b>estado</b> e <b>município</b> são <b>obrigatórios</b>. A foto deve ser em <b>JPG</b> ou <b>PNG</b> e ter no máximo <b>2MB</b></p>
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
                                            <button class="btn btn-secondary btn-sm" style="font-size: 0.850em;" id="btn_nova_profissao" type="button"><i class="bi bi-plus-circle-fill"></i> Nova profissao</button>
                                            <button class="btn btn-primary btn-sm" style="font-size: 0.850em;" id="btn_novo_orgao" type="button"><i class="bi bi-plus-circle-fill"></i> Novo órgão</button>
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
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar'])) {
                        $dados = [
                            'pessoa_id' => $pessoaGet,
                            'pessoa_nome' => htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'),
                            'pessoa_email' => htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'),
                            'pessoa_aniversario' => $utils->formatarAniversario(htmlspecialchars($_POST['aniversario'], ENT_QUOTES, 'UTF-8')),
                            'pessoa_telefone' => htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8'),
                            'pessoa_endereco' => htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8'),
                            'pessoa_cep' => htmlspecialchars($_POST['cep'], ENT_QUOTES, 'UTF-8'),
                            'pessoa_bairro' => htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8'),
                            'pessoa_estado' => htmlspecialchars($_POST['estado'], ENT_QUOTES, 'UTF-8'),
                            'pessoa_municipio' => htmlspecialchars($_POST['municipio'], ENT_QUOTES, 'UTF-8'),
                            'pessoa_sexo' => htmlspecialchars($_POST['sexo'], ENT_QUOTES, 'UTF-8'),
                            'pessoa_facebook' => htmlspecialchars($_POST['facebook'], ENT_QUOTES, 'UTF-8'),
                            'pessoa_instagram' => htmlspecialchars($_POST['instagram'], ENT_QUOTES, 'UTF-8'),
                            'pessoa_x' => htmlspecialchars($_POST['x'], ENT_QUOTES, 'UTF-8'),
                            'pessoa_tipo' => htmlspecialchars($_POST['tipo'], ENT_QUOTES, 'UTF-8'),
                            'pessoa_profissao' => htmlspecialchars($_POST['profissao'], ENT_QUOTES, 'UTF-8'),
                            'pessoa_cargo' => htmlspecialchars($_POST['cargo'], ENT_QUOTES, 'UTF-8'),
                            'pessoa_orgao' => htmlspecialchars($_POST['orgao'], ENT_QUOTES, 'UTF-8'),
                            'pessoa_informacoes' => htmlspecialchars($_POST['informacoes'], ENT_QUOTES, 'UTF-8'),
                            'foto' => $_FILES['foto'],
                            'pessoa_gabinete' => $_SESSION['usuario_gabinete']
                        ];

                        $result = $pessoaController->atualizarPessoa($dados);



                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                            $buscaPessoa = $pessoaController->buscaPessoa('pessoa_id', $pessoaGet);
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'file_not_permited' || $result['status'] == 'max_file_size_exceeded' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error' || $result['status'] == 'forbidden' || $result['status'] == 'file_not_permitted' || $result['status'] == 'file_too_large') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_apagar'])) {
                        $result = $pessoaController->apagarPessoa($pessoaGet);
                        if ($result['status'] == 'success') {
                            header('Location: ?secao=pessoas');
                            exit;
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['id_erro']) ? ' | Código do erro: ' . $result['id_erro'] : '') . '</div>';
                        }
                    }

                    if (date('d/m', strtotime($buscaPessoa['dados']['pessoa_aniversario'])) == date('d/m')) {
                        echo '<div class="alert alert-warning px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">Hoje é aniversário de <b>' . $buscaPessoa['dados']['pessoa_nome'] . '</b>! Parabéns!</div>';
                    }
                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="nome" placeholder="Nome" value="<?php echo $buscaPessoa['dados']['pessoa_nome'] ?>" required>
                        </div>
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="email" placeholder="Email" value="<?php echo $buscaPessoa['dados']['pessoa_email'] ?>" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="aniversario" placeholder="Aniversário (dd/mm)" data-mask="00/00" value="<?php echo date('d/m', strtotime($buscaPessoa['dados']['pessoa_aniversario'])) ?>" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="telefone" placeholder="Telefone (Somente números)" maxlength="11" value="<?php echo $buscaPessoa['dados']['pessoa_telefone'] ?>" data-mask="(00) 00000-0000">
                        </div>
                        <div class="col-md-5 col-12">
                            <input type="text" class="form-control form-control-sm" name="endereco" placeholder="Endereço" value="<?php echo $buscaPessoa['dados']['pessoa_endereco'] ?>">
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="bairro" placeholder="Bairro" value="<?php echo $buscaPessoa['dados']['pessoa_bairro'] ?>">
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="cep" placeholder="CEP (Somente números)" maxlength="8" data-mask="00000-000" value="<?php echo $buscaPessoa['dados']['pessoa_cep'] ?>">
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
                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" id="sexo" name="sexo" required>
                                <option value="Sexo não informado" <?= $buscaPessoa['dados']['pessoa_sexo'] == 1 ? 'selected' : '' ?>>Sexo não informado</option>
                                <option value="Masculino" <?= $buscaPessoa['dados']['pessoa_sexo'] == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                                <option value="Feminino" <?= $buscaPessoa['dados']['pessoa_sexo'] == 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                                <option value="Outro" <?= $buscaPessoa['dados']['pessoa_sexo'] == 'Outro' ? 'selected' : '' ?>>Outro</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-4">
                            <input type="text" class="form-control form-control-sm" name="facebook" placeholder="@facebook " value="<?php echo $buscaPessoa['dados']['pessoa_facebook'] ?>">
                        </div>
                        <div class="col-md-2 col-4">
                            <input type="text" class="form-control form-control-sm" name="instagram" placeholder="@instagram " value="<?php echo $buscaPessoa['dados']['pessoa_instagram'] ?>">
                        </div>
                        <div class="col-md-2 col-4">
                            <input type="text" class="form-control form-control-sm" name="x" placeholder="@X (Twitter) " value="<?php echo $buscaPessoa['dados']['pessoa_x'] ?>">
                        </div>
                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" id="orgao" name="orgao">
                                <option value="1" selected>Órgão não informado</option>
                                <?php

                                $buscaOrgao = $orgaoController->listarOrgaos(1000, 1, 'ASC', 'orgao_nome', null, null, $_SESSION['usuario_gabinete']);

                                if ($buscaOrgao['status'] === 'success') {
                                    foreach ($buscaOrgao['dados'] as $orgao) {
                                        if ($orgao['orgao_id'] == $buscaPessoa['dados']['pessoa_orgao']) {
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
                            <select class="form-select form-select-sm" id="tipo" name="tipo" required>
                                <!--<option value="1" selected>Sem tipo definido</option>-->
                                <?php
                                $buscaTipo = $pessoaController->listarTiposPessoa($_SESSION['usuario_gabinete']);
                                if ($buscaTipo['status'] === 'success') {
                                    foreach ($buscaTipo['dados'] as $tipo) {
                                        if ($tipo['pessoa_tipo_id'] == $buscaPessoa['dados']['pessoa_tipo']) {
                                            echo '<option value="' . $tipo['pessoa_tipo_id'] . '" selected>' . $tipo['pessoa_tipo_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $tipo['pessoa_tipo_id'] . '">' . $tipo['pessoa_tipo_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                                <option value="+">Novo tipo + </option>
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <select class="form-select form-select-sm" id="profissao" name="profissao" required>
                                <!--<option value="1" selected>Profissão não informada</option>-->
                                <?php
                                $buscaProfissao = $pessoaController->listarProfissoesPessoa($_SESSION['usuario_gabinete']);
                                if ($buscaProfissao['status'] === 'success') {
                                    foreach ($buscaProfissao['dados'] as $profissao) {
                                        if ($profissao['pessoas_profissoes_id'] == $buscaPessoa['dados']['pessoa_profissao']) {
                                            echo '<option value="' . $profissao['pessoas_profissoes_id'] . '" selected>' . $profissao['pessoas_profissoes_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $profissao['pessoas_profissoes_id'] . '">' . $profissao['pessoas_profissoes_nome'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                                <option value="+">Nova profissao + </option>
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <input type="text" class="form-control form-control-sm" name="cargo" placeholder="Cargo (Diretor, assessor, coordenador....)" value="<?php echo $buscaPessoa['dados']['pessoa_cargo'] ?>">
                        </div>
                        <div class="col-md-3 col-12">
                            <div class="file-upload">
                                <input type="file" id="file-input" name="foto" style="display: none;" />
                                <button id="file-button" type="button" class="btn btn-primary btn-sm"><i class="fa-regular fa-image"></i> Escolher Foto</button>
                            </div>
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="informacoes" rows="5" placeholder="Informações importantes dessa pessoa"><?php echo $buscaPessoa['dados']['pessoa_informacoes'] ?></textarea>
                        </div>
                        <div class="col-md-12 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_atualizar"><i class="bi bi-floppy-fill"></i> Atualizar</button>
                            <button type="submit" class="btn btn-danger btn-sm" name="btn_apagar"><i class="bi bi-trash-fill"></i> Apagar</button>
                            <a href="?secao=imprimir-pessoa&id=<?php echo $pessoaGet ?>" target="_blank" class="btn btn-primary btn-sm"><i class="bi bi-printer-fill"></i> Imprimir</a>
                            <button type="button" class="btn btn-secondary btn-sm" id="btn_foto" data-bs-toggle="modal" data-bs-target="#fotoModal"><i class="bi bi-camera"></i> Ver foto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <img src="<?php echo !empty($buscaPessoa['dados']['pessoa_foto']) ? $buscaPessoa['dados']['pessoa_foto'] : './public/img/not_found.jpg' ?>" class="img-fluid" alt="Imagem">
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
                if (estado.sigla === "<?php echo $buscaPessoa['dados']['pessoa_estado'] ?>") {
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
                if (municipio.nome === "<?php echo $buscaPessoa['dados']['pessoa_municipio'] ?>") {
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
                window.location.href = "?secao=orgaos-tipos";
            } else {
                $('#tipo').val(1000).change();
            }
        }
    });

    $('#profissao').change(function() {
        if ($('#profissao').val() == '+') {
            if (window.confirm("Você realmente deseja inserir uma nova profissão?")) {
                window.location.href = "?secao=profissoes";
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

    $('#btn_novo_tipo').click(function() {
        if (window.confirm("Você realmente deseja inserir um novo tipo de pessoa?")) {
            window.location.href = "?secao=pessoas-tipos";
        } else {
            return false;
        }
    });

    $('#btn_novo_orgao').click(function() {
        if (window.confirm("Você realmente deseja inserir um novo órgão?")) {
            window.location.href = "?secao=orgaos";
        } else {
            return false;
        }
    });


    $('#btn_nova_profissao').click(function() {
        if (window.confirm("Você realmente deseja inserir uma nova profissão?")) {
            window.location.href = "?secao=profissoes";
        } else {
            return false;
        }
    });
</script>