<?php

use GabineteMvc\Controllers\GabineteController;
use GabineteMvc\Controllers\UsuarioController;
use GabineteMvc\Middleware\Utils;

$usuarioController = new UsuarioController();
$gabineteController = new GabineteController();
$util = new Utils();

$configPath = dirname(__DIR__, 3) . '/src/Configs/config.php';
$config = require $configPath;

$buscaUsuario = $usuarioController->buscaUsuario('usuario_id', $_SESSION['usuario_id']);
$buscaGabinete = $gabineteController->buscaGabinete('gabinete_id', $_SESSION['usuario_gabinete']);
$buscaTipoGabinete = $gabineteController->buscaTipoGabinete($buscaGabinete['dados']['gabinete_tipo']);

if ($buscaUsuario['status'] != 'success' || $buscaGabinete['status'] != 'success' || $buscaTipoGabinete['status'] != 'success') {
    header('Location:?secao=home');
}

?>

<div class="card mb-2">
    <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-person-gear"></i> Área do gestor</div>
    <div class="card-body card_descricao_body p-2">
        <p class="card-text">Esta área é destinada à gestão do gabinete, incluindo o gerenciamento de usuários, níveis de acesso e dados do gabinete.</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mb-2">
            <div class="card-body card_descricao_body p-2">
                <h6 class="card-title mb-2"><?php echo $buscaGabinete['dados']['gabinete_nome'] ?> - <?php echo $buscaGabinete['dados']['gabinete_estado_autoridade'] ?></h6>
                <p class="card-text mb-1">Assinaturas do plano: <?php echo $buscaGabinete['dados']['gabinete_usuarios'] ?> - <a href="#">Solicitar novas assinaturas</a></p>
            </div>
        </div>
    </div>
</div>

<div class="card mb-2">
    <div class="card-body p-2">
        <p class="card-text mb-2">Dados do gabinete: </p>
        <?php

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar_gabinete'])) {
            $dados = [
                'gabinete_id' => $buscaGabinete['dados']['gabinete_id'],
                'gabinete_tipo' => $_POST['gabinete_tipo'],
                'gabinete_nome' => htmlspecialchars($_POST['gabinete_nome'], ENT_QUOTES, 'UTF-8'),
                'gabinete_nome_sistema' => $util->sanitizarString(htmlspecialchars($_POST['gabinete_nome'], ENT_QUOTES, 'UTF-8')),
                'gabinete_estado' => htmlspecialchars($_POST['gabinete_estado'], ENT_QUOTES, 'UTF-8'),
                'gabinete_endereco' => htmlspecialchars($_POST['gabinete_endereco'], ENT_QUOTES, 'UTF-8'),
                'gabinete_municipio' => htmlspecialchars($_POST['gabinete_municipio'], ENT_QUOTES, 'UTF-8'),
                'gabinete_email' => htmlspecialchars($_POST['gabinete_email'], ENT_QUOTES, 'UTF-8'),
                'gabinete_telefone' => htmlspecialchars($_POST['gabinete_telefone'], ENT_QUOTES, 'UTF-8')
            ];

            $result = $gabineteController->atualizarGabinete($dados);

            if ($result['status'] == 'success') {
                echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                echo '
                   <script>
                        setTimeout(function() {
                            window.location.href = "?secao=meu-gabinete";
                        }, 1000);
                    </script>';
            } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
            } else if ($result['status'] == 'error') {
                echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' | Código do erro: ' . $result['id_erro'] . '</div>';
            }
        }
        ?>

        <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
            <div class="col-md-4 col-12">
                <input type="text" class="form-control form-control-sm" name="gabinete_nome" value="<?php echo $buscaGabinete['dados']['gabinete_nome'] ?>" placeholder="E-mail" required>
            </div>
            <div class="col-md-2 col-12">
                <select class="form-select form-select-sm" name="gabinete_tipo" required>
                    <?php
                    foreach ($gabineteController->listarTipoGabinete()['dados'] as $tipo) {
                        if ($tipo['gabinete_tipo_id'] == $buscaGabinete['dados']['gabinete_tipo']) {
                            echo '<option value="' . $tipo['gabinete_tipo_id'] . '" selected>' . $tipo['gabinete_tipo_nome'] . '</option>';
                        } else {
                            echo '<option value="' . $tipo['gabinete_tipo_id'] . '">' . $tipo['gabinete_tipo_nome'] . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-4 col-12">
                <input type="text" class="form-control form-control-sm" name="gabinete_email" value="<?php echo $buscaGabinete['dados']['gabinete_email'] ?>" placeholder="E-mail">
            </div>

            <div class="col-md-2 col-12">
                <input type="text" class="form-control form-control-sm" name="gabinete_telefone" value="<?php echo $buscaGabinete['dados']['gabinete_telefone'] ?>" data-mask="(00) 0000-0000" placeholder="Telefone">
            </div>
            <div class="col-md-1 col-6">
                <select class="form-select form-select-sm" id="estado" name="gabinete_estado">
                    <option value=" " selected>UF</option>
                </select>
            </div>
            <div class="col-md-2 col-6">
                <select class="form-select form-select-sm" id="municipio" name="gabinete_municipio">
                    <option value=" " selected>Município</option>
                </select>
            </div>
            <div class="col-md-2 col-12">
                <input type="text" class="form-control form-control-sm" name="gabinete_endereco" placeholder="Endereço" value="<?php echo $buscaGabinete['dados']['gabinete_endereco'] ?>">
            </div>
            <div class="col-md-2 col-12">
                <button type="submit" class="btn btn-success btn-sm" name="btn_atualizar_gabinete"><i class="bi bi-floppy-fill"></i> Atualizar</button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mb-2">
            <div class="card-body p-2">
                <p class="card-text mb-2">Meus dados: </p>

                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar_usuario'])) {

                    $usuario = [
                        'usuario_id' => $buscaUsuario['dados']['usuario_id'],
                        'usuario_nome' => htmlspecialchars($_POST['usuario_nome'], ENT_QUOTES, 'UTF-8'),
                        'usuario_email' => htmlspecialchars($_POST['usuario_email'], ENT_QUOTES, 'UTF-8'),
                        'usuario_telefone' => htmlspecialchars($_POST['usuario_telefone'], ENT_QUOTES, 'UTF-8'),
                        'usuario_aniversario' => $util->formatarAniversario(htmlspecialchars($_POST['usuario_aniversario'], ENT_QUOTES, 'UTF-8')),// Data formatada
                        
                    ];

                    $result = $usuarioController->atualizarUsuario($usuario);

                    if ($result['status'] == 'success') {
                        echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                        echo '
                        <script>
                             setTimeout(function() {
                                 window.location.href = "?secao=meu-gabinete";
                             }, 1000);
                         </script>';
                    } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                        echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                    } else if ($result['status'] == 'forbidden') {
                        echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
                    } else if ($result['status'] == 'error') {
                        echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['error_id']) ? ' | Código do erro: ' . $result['error_id'] : '') . '</div>';
                    }
                }

                ?>

                <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                    <div class="col-md-3 col-12">
                        <input type="text" class="form-control form-control-sm" name="usuario_nome" placeholder="Nome" value="<?php echo $buscaUsuario['dados']['usuario_nome'] ?>" required>
                    </div>
                    <div class="col-md-2 col-12">
                        <input type="email" class="form-control form-control-sm" name="usuario_email" placeholder="Email" value="<?php echo $buscaUsuario['dados']['usuario_email'] ?>" required>
                    </div>
                    <div class="col-md-2 col-6">
                        <input type="text" class="form-control form-control-sm" name="usuario_telefone" placeholder="Celular (com DDD)" data-mask="(00) 00000-0000" value="<?php echo $buscaUsuario['dados']['usuario_telefone'] ?>" maxlength="15" >
                    </div>
                    <div class="col-md-2 col-6">
                        <input type="text" class="form-control form-control-sm" name="usuario_aniversario" data-mask="00/00" placeholder="Aniversário (dd/mm)" value="<?php echo $buscaUsuario['dados']['usuario_aniversario'] ?  date('d/m', strtotime($buscaUsuario['dados']['usuario_aniversario'])) : '' ?>" >
                    </div>
                    <div class="col-md-3 col-12">
                        <button type="submit" class="btn btn-primary btn-sm" name="btn_atualizar_usuario"><i class="bi bi-floppy-fill"></i> Atualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card mb-2">
    <div class="card-body card_descricao_body p-2">
        <p class="card-text mb-2">Para cadastrar novos usuários no sistema, envie o endereço abaixo e solicite que criem uma conta.</p>
        <p class="card-text">Link para o cadastro de novos usuários:
            <span id="link-cadastro" style="display: none;"><?php echo $config['app']['base_url'] ?>?secao=novo-usuario&token=<?php echo $buscaGabinete['dados']['gabinete_id'] ?></span>
            <a href="javascript:void(0);" onclick="copyToClipboard()"><b>Copiar</b></a>
        </p>
    </div>
</div>


<div class="card mb-2">
    <div class="card-body p-2">
        <p class="card-text mb-2">Usuários do gabinete:</a></p>

        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                <thead>
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col">Email</th>
                        <th scope="col">Aniversário</th>
                        <th scope="col">Telefone</th>
                        <th scope="col">Nível</th>
                        <th scope="col">Ativo</th>
                        <th scope="col">Criado</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $buscaUsuarios = $usuarioController->listarUsuarios(1000, 1, 'asc', 'usuario_nome', $buscaGabinete['dados']['gabinete_id']);

                    if ($buscaUsuarios['status'] == 'success') {

                        foreach ($buscaUsuarios['dados'] as $usuario) {
                            
                            foreach ($usuarioController->listarTipoUsuario()['dados'] as $tipoUsuario) {
                                if ($tipoUsuario['usuario_tipo_id'] == $usuario['usuario_tipo']) {
                                    $tipoUsuarioNome = $tipoUsuario['usuario_tipo_nome'];
                                    break;
                                }
                            }


                            if($usuario['usuario_id'] == $_SESSION['usuario_id']){
                                $link = '<td style="white-space: nowrap; justify-content: center; align-items: center;">' . $usuario['usuario_nome'] . '</td>';
                            }else{
                                $link = '<td style="white-space: nowrap; justify-content: center; align-items: center;"><a href="?secao=usuario&id=' . $usuario['usuario_id'] . '">' . $usuario['usuario_nome'] . '</a></td>';
                            }

                            echo '<tr>';
                            echo $link;
                            echo '<td style="white-space: nowrap;">' . $usuario['usuario_email'] . '</td>';
                            echo '<td style="white-space: nowrap;">' . (isset($usuario['usuario_aniversario']) && !empty($usuario['usuario_aniversario']) ? date('d/m', strtotime($usuario['usuario_aniversario'])) : '') . '</td>';
                            echo '<td style="white-space: nowrap;">' . $usuario['usuario_telefone'] . '</td>';
                            echo '<td style="white-space: nowrap;">' . $tipoUsuarioNome . '</td>';
                            echo '<td style="white-space: nowrap;">' . ($usuario['usuario_ativo'] ? 'Ativo' : 'Desativado') . '</td>';
                            echo '<td style="white-space: nowrap;">' . date('d/m/Y', strtotime($usuario['usuario_criado_em'])) . '</td>';
                            echo '</tr>';
                        }
                    } else if ($buscaUsuarios['status'] == 'not_found') {
                        echo '<tr><td colspan="6">' . $buscaUsuarios['message'] . '</td></tr>';
                    } else if ($buscaUsuarios['status'] == 'error') {
                        echo '<tr><td colspan="6">' . $buscaUsuarios['message'] . ' | Código do erro: ' . $buscaUsuarios['error_id'] . '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
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
                if (estado.sigla === "<?php echo $buscaGabinete['dados']['gabinete_estado'] ?>") {
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
                if (municipio.nome === "<?php echo $buscaGabinete['dados']['gabinete_municipio'] ?>") {
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


   
</script>