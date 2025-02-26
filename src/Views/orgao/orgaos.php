<?php

require './src/Middleware/VerificaLogado.php';
require 'vendor/autoload.php';

use GabineteMvc\Controllers\GabineteController;
use GabineteMvc\Controllers\OrgaoController;

$orgaoController = new OrgaoController();
$gabineteController = new GabineteController();

$buscaGab = $gabineteController->buscaGabinete('gabinete_id', $_SESSION['usuario_gabinete']);
$estadoDep = $buscaGab['dados']['gabinete_estado_autoridade'];

$itens = isset($_GET['itens']) ? (int) $_GET['itens'] : 10;
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$ordenarPor = isset($_GET['ordenarPor']) && in_array(htmlspecialchars($_GET['ordenarPor']), ['orgao_nome', 'orgao_estado', 'orgao_municipio', 'orgao_tipo_nome', 'orgao_criado_por', 'orgao_criado_em']) ? htmlspecialchars($_GET['ordenarPor']) : 'orgao_nome';
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
                <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-building"></i> Editar tipo de Órgão/Entidade</div>
                <div class="card-body card_descricao_body p-2">
                    <p class="card-text mb-2">Nesta seção, é possível editar um tipo de órgão ou entidades, garantindo a organização correta dessas informações no sistema.</p>
                    <p class="card-text mb-0">Todos os campos são obrigatórios</p>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-1">
                    <form class="row g-2 form_custom mb-0" method="post" enctype="application/x-www-form-urlencoded">
                        <div class="col-md-12 col-12">
                            <button type="submit" class="btn btn-success btn-sm" name="btn_xls"><i class="bi bi-file-earmark-excel-fill"></i> Excel</button>
                            <button type="submit" class="btn btn-primary btn-sm" name="btn_csv"><i class="bi bi-filetype-csv"></i> CSV</button>
                            <a href="?secao=imprimir-orgaos&estado=<?php echo $estado ?>" type="button" target="_blank" class="btn btn-secondary btn-sm" id="btn_imprimir"><i class="bi bi-printer-fill"></i> Imprimir</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm mb-2">
                <div class="card-body p-2">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_csv'])) {

                        $result = $orgaoController->gerarCsv($_SESSION['usuario_gabinete']);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="20" role="alert">' . $result['message'] . '. <a href="' . $result['file'] . '">Download</a></div>';
                        } else if ($result['status'] == 'not_found') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">Nenhum arquivo foi gerado</div>';
                        }
                    }

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_xls'])) {

                        $result = $orgaoController->gerarXls($_SESSION['usuario_gabinete']);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="20" role="alert">' . $result['message'] . '. <a href="' . $result['file'] . '">Download</a></div>';
                        } else if ($result['status'] == 'not_found') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">Nenhum arquivo foi gerado</div>';
                        }
                    }

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
                            'orgao_gabinete' => $_SESSION['usuario_gabinete']
                        ];

                        $result = $orgaoController->novoOrgao($dados);

                        if ($result['status'] == 'success') {
                            echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" role="alert" data-timeout="3">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                            echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" role="alert" data-timeout="3">' . $result['message'] . '</div>';
                        } else if ($result['status'] == 'error') {
                            echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['error_id']) ? ' | Código do erro: ' . $result['error_id'] : '') . '</div>';
                        }
                    }
                    ?>
                    <form class="row g-2 form_custom " id="form_novo" method="POST" enctype="application/x-www-form-urlencoded">
                        <div class="col-md-5 col-12">
                            <input type="text" class="form-control form-control-sm" name="nome" placeholder="Nome " required>
                        </div>
                        <div class="col-md-4 col-6">
                            <input type="email" class="form-control form-control-sm" name="email" placeholder="Email " required>
                        </div>
                        <div class="col-md-3 col-6">
                            <input type="text" class="form-control form-control-sm" name="telefone" placeholder="Telefone (somente números)" data-mask="(00) 00000-0000" maxlength="15">
                        </div>
                        <div class="col-md-4 col-12">
                            <input type="text" class="form-control form-control-sm" name="endereco" placeholder="Endereço ">
                        </div>
                        <div class="col-md-3 col-6">
                            <input type="text" class="form-control form-control-sm" name="bairro" placeholder="Bairro">
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="cep" placeholder="CEP (somente números)" data-mask="00000-000" maxlength="8">
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
                                        if ($orgaoTipo['orgao_tipo_nome'] == 'Tipo não informado') {
                                            echo '<option value="' . $orgaoTipo['orgao_tipo_id'] . '" selected>' . $orgaoTipo['orgao_tipo_nome'] . '</option>';
                                        } else {
                                            echo '<option value="' . $orgaoTipo['orgao_tipo_id'] . '">' . $orgaoTipo['orgao_tipo_nome'] . '</option>';
                                        }
                                    }
                                } else if ($busca['status'] == 'empty') {
                                    echo '<option>' . $busca['message'] . '</option>';
                                } else if ($busca['status'] == 'error') {
                                    echo '<option>' . $busca['message'] . '</option>';
                                }
                                ?>
                                <option value="+">Novo tipo + </option>
                            </select>
                        </div>
                        <div class="col-md-9 col-12">
                            <input type="text" class="form-control form-control-sm" name="site" placeholder="Site ou rede sociais">
                        </div>
                        <div class="col-md-12 col-12">
                            <textarea class="form-control form-control-sm" name="informacoes" rows="5" placeholder="Informações importantes desse órgão"></textarea>
                        </div>
                        <div class="col-md-4 col-6">
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
                                    <input type="hidden" name="secao" value="orgaos" />
                                    <select class="form-select form-select-sm" name="ordenarPor" required>
                                        <option value="orgao_nome" <?php echo $ordenarPor == 'orgao_nome' ? 'selected' : ''; ?>>Ordenar por | Nome</option>
                                        <option value="orgao_estado" <?php echo $ordenarPor == 'orgao_estado' ? 'selected' : ''; ?>>Ordenar por | Estado</option>
                                        <option value="orgao_municipio" <?php echo $ordenarPor == 'orgao_municipio' ? 'selected' : ''; ?>>Ordenar por | Muncípio</option>
                                        <option value="orgao_tipo_nome" <?php echo $ordenarPor == 'orgao_tipo_nome' ? 'selected' : ''; ?>>Ordenar por | Tipo</option>
                                        <option value="orgao_criado_em" <?php echo $ordenarPor == 'orgao_criado_em' ? 'selected' : ''; ?>>Ordenar por | Criação</option>
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
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Criado em | por</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $busca = $orgaoController->listarOrgaos($itens, $pagina, $ordem, $ordenarPor, $termo, $estado, $_SESSION['usuario_gabinete']);

                                if ($busca['status'] == 'success') {
                                    $total_de_registros = count($busca['dados']);
                                    foreach ($busca['dados'] as $orgao) {
                                        if ($orgao['orgao_id'] <> 1) {
                                            echo '<tr>';
                                            echo '<td style="white-space: nowrap;"><a href="?secao=orgao&id=' . $orgao['orgao_id'] . '">' . $orgao['orgao_nome'] . '</a></td>';
                                            echo '<td style="white-space: nowrap;">' . $orgao['orgao_email'] . '</td>';
                                            echo '<td style="white-space: nowrap;">' . $orgao['orgao_telefone'] . '</td>';
                                            echo '<td style="white-space: nowrap;">' . $orgao['orgao_endereco'] . '</td>';
                                            echo '<td style="white-space: nowrap;">' . $orgao['orgao_municipio'] . '/' . $orgao['orgao_estado'] . '</td>';
                                            echo '<td style="white-space: nowrap;">' . $orgao['orgao_tipo_nome'] . '</td>';
                                            echo '<td style="white-space: nowrap;">' . date('d/m/Y', strtotime($orgao['orgao_criado_em'])) . ' | ' . $orgao['usuario_nome'] . '</td>';
                                            echo '</tr>';
                                        }
                                    }
                                } else if ($busca['status'] == 'not_found') {
                                    echo '<tr><td colspan="7">' . $busca['message'] . '</td></tr>';
                                } else if ($busca['status'] == 'error') {
                                    echo '<tr><td colspan="7">Erro ao carregar os dados. ' . (isset($busca['error_id']) ? ' | Código do erro: ' . $busca['error_id'] : '') . '</td></tr>';
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
                        echo '<li class="page-item ' . ($pagina == 1 ? 'active' : '') . '"><a class="page-link" href="?secao=orgaos&itens=' . $itens . '&pagina=1&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . (isset($termo) ? '&termo=' . $termo : '') . '">Primeira</a></li>';

                        for ($i = 1; $i < $totalPagina - 1; $i++) {
                            $pageNumber = $i + 1;
                            echo '<li class="page-item ' . ($pagina == $pageNumber ? 'active' : '') . '"><a class="page-link" href="?secao=orgaos&itens=' . $itens . '&pagina=' . $pageNumber . '&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . (isset($termo) ? '&termo=' . $termo : '') . '">' . $pageNumber . '</a></li>';
                        }

                        echo '<li class="page-item ' . ($pagina == $totalPagina ? 'active' : '') . '"><a class="page-link" href="?secao=orgaos&itens=' . $itens . '&pagina=' . $totalPagina . '&ordenarPor=' . $ordenarPor . '&ordem=' . $ordem . (isset($termo) ? '&termo=' . $termo : '') . '">Última</a></li>';
                        echo '</ul>';
                    }
                    ?>

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

    $('button[name="btn_csv"]').on('click', function(event) {
        const confirmacao = confirm("Tem certeza que deseja criar esse arquivo? Essa operação pode levar varios minutos");
        if (!confirmacao) {
            event.preventDefault();
        }
    });

    $('button[name="btn_xls"]').on('click', function(event) {
        const confirmacao = confirm("Tem certeza que deseja criar esse arquivo? Essa operação pode levar varios minutos");
        if (!confirmacao) {
            event.preventDefault();
        }
    });

    window.addEventListener('keydown', function(event) {
        if ((event.ctrlKey || event.metaKey) && event.key === 'p') {
            event.preventDefault(); // Impede a janela de impressão padrão
            window.open('?secao=imprimir-orgaos&estado=<?php echo $estado ?>', '_blank'); // Abre a URL em uma nova aba
        }
    });
</script>