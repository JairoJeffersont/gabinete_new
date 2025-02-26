<?php

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

$buscaGab = $gabineteController->buscaGabinete('gabinete_id', $_SESSION['usuario_gabinete']);
$estadoDep = $buscaGab['dados']['gabinete_estado_autoridade'];

$itens = isset($_GET['itens']) ? (int) $_GET['itens'] : 10;
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$ordenarPor = isset($_GET['ordenarPor']) && in_array(htmlspecialchars($_GET['ordenarPor']), ['pessoa_nome', 'pessoa_estado', 'pessoa_municipio', 'pessoa_tipo_nome', 'pessoa_criada_por']) ? htmlspecialchars($_GET['ordenarPor']) : 'pessoa_nome';
$ordem = isset($_GET['ordem']) ? strtolower(htmlspecialchars($_GET['ordem'])) : 'asc';
$termo = isset($_GET['termo']) ? htmlspecialchars($_GET['termo']) : null;
$estado = (isset($_GET['estado']) && $_GET['estado'] !== 'null') ? $_GET['estado'] : null;

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
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-people-fill"></i> Pessoas</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível adicionar e editar pessoas de interesso do mandato., garantindo a organização correta dessas informações no sistema.</p>
                    <p class="card-text mb-0">Os campos <b>Nome</b>, <b>email</b>, <b>aniversário</b>, <b>estado</b> e <b>município</b> são <b>obrigatórios</b>. A foto deve ser em <b>JPG</b> ou <b>PNG</b> e ter no máximo <b>20MB</b></p>
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
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {
                        $dados = [
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
                            'pessoa_criada_por' => $_SESSION['usuario_id'],
                            'pessoa_gabinete' => $_SESSION['usuario_gabinete']
                        ];

                        $result = $pessoaController->novaPessoa($dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'file_not_permited' || $result['status'] == 'too_big' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error' || $result['status'] == 'forbidden' || $result['status'] == 'file_not_permitted' || $result['status'] == 'file_too_large') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                        }
                    }
                    ?>
                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="nome" placeholder="Nome " required>
                        </div>
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="email" placeholder="Email " required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="aniversario" placeholder="Aniversário (dd/mm)" data-mask="00/00" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="telefone" placeholder="Telefone (Somente números)" maxlength="15" data-mask="(00) 00000-0000">
                        </div>
                        <div class="col-md-5 col-12">
                            <input type="text" class="form-control form-control-sm" name="endereco" placeholder="Endereço ">
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="bairro" placeholder="Bairro ">
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="cep" placeholder="CEP (Somente números)" maxlength="9" data-mask="00000-000">
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
                                <option value="Sexo não informado" selected>Sexo não informado</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Feminino">Feminino</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-4">
                            <input type="text" class="form-control form-control-sm" name="facebook" placeholder="@facebook ">
                        </div>
                        <div class="col-md-2 col-4">
                            <input type="text" class="form-control form-control-sm" name="instagram" placeholder="@instagram ">
                        </div>
                        <div class="col-md-2 col-4">
                            <input type="text" class="form-control form-control-sm" name="x" placeholder="@X (Twitter) ">
                        </div>
                        <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" id="orgao" name="orgao">
                                <option value="1" selected>Órgão não informado</option>
                                <?php

                                $buscaOrgao = $orgaoController->listarOrgaos(1000, 1, 'ASC', 'orgao_nome', null, null, $_SESSION['usuario_gabinete']);

                                if ($buscaOrgao['status'] === 'success') {
                                    foreach ($buscaOrgao['dados'] as $orgao) {
                                        echo '<option value="' . $orgao['orgao_id'] . '">' . $orgao['orgao_nome'] . '</option>';
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
                                        if ($tipo['pessoa_tipo_id'] == 1) {
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
                                        if ($profissao['pessoas_profissoes_id'] == 1) {
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
                            <input type="text" class="form-control form-control-sm" name="cargo" placeholder="Cargo (Diretor, assessor, coordenador....)">
                        </div>
                        <div class="col-md-3 col-12">
                            <div class="file-upload">
                                <input type="file" id="file-input" name="foto" style="display: none;" />
                                <button id="file-button" type="button" class="btn btn-primary btn-sm"><i class="fa-regular fa-image"></i> Escolher Foto</button>
                            </div>
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="informacoes" rows="5" placeholder="Informações importantes dessa pessoa"></textarea>
                        </div>
                        <div class="col-md-2 col-6">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row ">
                <div class="col-12">
                    <div class="card shadow-sm mb-2">
                        <div class="card-body p-2">
                            <form class="row g-2 form_custom mb-0" method="GET" enctype="application/x-www-form-urlencoded">
                                <div class="col-md-2 col-6">
                                    <input type="hidden" name="secao" value="pessoas" />
                                    <select class="form-select form-select-sm" name="ordenarPor" required>
                                        <option value="pessoa_nome" <?php echo $ordenarPor == 'pessoa_nome' ? 'selected' : ''; ?>>Ordenar por | Nome</option>
                                        <option value="pessoa_estado" <?php echo $ordenarPor == 'pessoa_estado' ? 'selected' : ''; ?>>Ordenar por | Estado</option>
                                        <option value="pessoa_municipio" <?php echo $ordenarPor == 'pessoa_municipio' ? 'selected' : ''; ?>>Ordenar por | Muncípio</option>
                                        <option value="pessoa_tipo_nome" <?php echo $ordenarPor == 'pessoa_tipo_nome' ? 'selected' : ''; ?>>Ordenar por | Tipo</option>
                                        <option value="pessoa_criada_em" <?php echo $ordenarPor == 'pessoa_criada_em' ? 'selected' : ''; ?>>Ordenar por | Criação</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-6">
                                    <select class="form-select form-select-sm" name="ordem" required>
                                        <option value="asc" <?php echo $ordem == 'asc' ? 'selected' : ''; ?>>Ordem Crescente</option>
                                        <option value="desc" <?php echo $ordem == 'desc' ? 'selected' : ''; ?>>Ordem Decrescente</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-6">
                                    <select class="form-select form-select-sm" name="itens" required>
                                        <option value="5" <?php echo $itens == 5 ? 'selected' : ''; ?>>5 itens</option>
                                        <option value="10" <?php echo $itens == 10 ? 'selected' : ''; ?>>10 itens</option>
                                        <option value="25" <?php echo $itens == 25 ? 'selected' : ''; ?>>25 itens</option>
                                        <option value="50" <?php echo $itens == 50 ? 'selected' : ''; ?>>50 itens</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-6">
                                    <select class="form-select form-select-sm" name="estado" required>
                                        <option value="null" <?php echo $estado === null ? 'selected' : ''; ?>>Todos os estados</option>
                                        <option value="<?php echo $estadoDep ?>" <?php echo $estado === $estadoDep ? 'selected' : ''; ?>>Somente <?php echo $estadoDep ?></option>
                                    </select>
                                </div>
                                <div class="col-md-3 col-10">
                                    <input type="text" class="form-control form-control-sm" name="termo" placeholder="Buscar..." value="<?php echo $termo ?>">
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
                    <div class="table-responsive mb-0">
                        <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Telefone</th>
                                    <th scope="col">Endereço</th>
                                    <th scope="col">UF/Município</th>
                                    <th scope="col">Órgão</th>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Profissão</th>
                                    <th scope="col">Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $busca = $pessoaController->listarPessoas($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $_SESSION['usuario_gabinete']);

                                if ($busca['status'] == 'success') {
                                    $total_de_registros = count($busca['dados']);
                                    foreach ($busca['dados'] as $pessoa) {
                                        echo '<tr>';
                                        echo '<td style="white-space: nowrap;"><a href="?secao=pessoa&id=' . $pessoa['pessoa_id'] . '">' . $pessoa['pessoa_nome'] . '</a></td>';
                                        echo '<td style="white-space: nowrap;">' . $pessoa['pessoa_email'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $pessoa['pessoa_telefone'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $pessoa['pessoa_endereco'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $pessoa['pessoa_municipio'] . '/' . $pessoa['pessoa_estado'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $pessoa['orgao_nome'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $pessoa['pessoa_tipo_nome'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . $pessoa['pessoas_profissoes_nome'] . '</td>';
                                        echo '<td style="white-space: nowrap;">' . date('d/m/Y', strtotime($pessoa['pessoa_criada_em'])) . ' | ' . $pessoa['usuario_nome'] . '</td>';
                                        echo '</tr>';
                                    }
                                } else if ($busca['status'] == 'not_found') {
                                    echo '<tr><td colspan="11">' . $busca['message'] . '</td></tr>';
                                } else if ($busca['status'] == 'error') {
                                    echo '<tr><td colspan="11">' . $busca['message'] . ' | Código do erro: ' . $busca['id_erro'] . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <?php
                    if (isset($busca['total_paginas'])) {
                        $totalPagina = $busca['total_paginas'];
                    } else {
                        $totalPagina = 0;
                    }

                    if ($totalPagina > 0 && $totalPagina != 1) {
                        echo '<ul class="pagination custom-pagination mt-2 mb-0">';
                        echo '<li class="page-item ' . ($pagina == 1 ? 'active' : '') . '"><a class="page-link" href="?secao=pessoas&itens=' . $itens . '&pagina=1&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . (isset($termo) ? '&termo=' . $termo : '') . '&estado='.$estado.'">Primeira</a></li>';

                        for ($i = 1; $i < $totalPagina - 1; $i++) {
                            $pageNumber = $i + 1;
                            echo '<li class="page-item ' . ($pagina == $pageNumber ? 'active' : '') . '"><a class="page-link" href="?secao=pessoas&itens=' . $itens . '&pagina=' . $pageNumber . '&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . (isset($termo) ? '&termo=' . $termo : '') . '&estado='.$estado.'">' . $pageNumber . '</a></li>';
                        }

                        echo '<li class="page-item ' . ($pagina == $totalPagina ? 'active' : '') . '"><a class="page-link" href="?secao=pessoas&itens=' . $itens . '&pagina=' . $totalPagina . '&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . (isset($termo) ? '&termo=' . $termo : '') . '&estado='.$estado.'">Última</a></li>';
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
    $(document).ready(function() {
        carregarEstados();
    });

    function carregarEstados() {
        $.getJSON('https://servicodados.ibge.gov.br/api/v1/localidades/estados?orderBy=nome', function(data) {
            const selectEstado = $('#estado');
            selectEstado.empty();
            selectEstado.append('<option value="" selected>UF</option>');
            data.forEach(estado => {
                selectEstado.append(`<option value="${estado.sigla}">${estado.sigla}</option>`);
            });
        });
    }

    function carregarMunicipios(estadoId) {
        $.getJSON(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${estadoId}/municipios?orderBy=nome`, function(data) {
            const selectMunicipio = $('#municipio');
            selectMunicipio.empty();
            selectMunicipio.append('<option value="" selected>Município</option>');
            data.forEach(municipio => {
                selectMunicipio.append(`<option value="${municipio.nome}">${municipio.nome}</option>`);
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
                window.location.href = "?secao=tipos-pessoas";
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
            window.location.href = "?secao=tipos-pessoas";
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