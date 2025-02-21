<div class="card mb-2 ">
    <div class="card-body p-1">
        <a class="btn btn-primary btn-sm custom-nav barra_navegacao" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
    </div>
</div>

<div class="card mb-2">
    <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-person-gear"></i> Área do gestor</div>
    <div class="card-body card_descricao_body p-2">
        <p class="card-text">Esta área é destinada à gestão do gabinete, incluindo o gerenciamento de usuários, níveis de acesso e dados do gabinete.</p>
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
        <p class="card-text mb-2">Dados do gabinete:</p>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar'])) {

            $dados = [
                'gabinete_id' => $buscaGabinete['dados']['gabinete_id'],
                'gabinete_tipo' => $buscaGabinete['dados']['gabinete_tipo'],
                'gabinete_nome' => $buscaGabinete['dados']['gabinete_nome'],
                'gabinete_usuarios' => $buscaGabinete['dados']['gabinete_usuarios'],
                'gabinete_estado' => $buscaGabinete['dados']['gabinete_estado'],
                'gabinete_endereco' => htmlspecialchars($_POST['gabinete_endereco'], ENT_QUOTES, 'UTF-8'),
                'gabinete_municipio' => htmlspecialchars($_POST['gabinete_municipio'], ENT_QUOTES, 'UTF-8'),
                'gabinete_email' => htmlspecialchars($_POST['gabinete_email'], ENT_QUOTES, 'UTF-8'),
                'gabinete_telefone' => htmlspecialchars($_POST['gabinete_telefone'], ENT_QUOTES, 'UTF-8')
            ];

            $result = $gabineteController->atualizarGabinete($dados);

            if ($result['status'] == 'success') {
                echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                $buscaGabinete = $gabineteController->buscaGabinete($buscaUsuario['dados']['usuario_gabinete']);
            } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
            } else if ($result['status'] == 'error') {
                echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' | Código do erro: ' . $result['id_erro'] . '</div>';
            }
        }
        ?>

        <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
            <div class="col-md-2 col-12">
                <input type="text" class="form-control form-control-sm" name="gabinete_email" value="<?php echo $buscaGabinete['dados']['gabinete_email'] ?>" placeholder="E-mail" required>
            </div>
            <div class="col-md-2 col-12">
                <input type="text" class="form-control form-control-sm" name="gabinete_telefone" value="<?php echo $buscaGabinete['dados']['gabinete_telefone'] ?>" placeholder="Telefone" required>
            </div>
            <div class="col-md-2 col-12">
                <input type="text" class="form-control form-control-sm" name="gabinete_municipio" value="<?php echo $buscaGabinete['dados']['gabinete_municipio'] ?>" placeholder="Município" required>
            </div>
            <div class="col-md-2 col-12">
                <input type="text" class="form-control form-control-sm" name="gabinete_endereco" placeholder="Endereço" value="<?php echo $buscaGabinete['dados']['gabinete_endereco'] ?>" required>
            </div>
            <div class="col-md-2 col-12">
                <button type="submit" class="btn btn-success btn-sm" name="btn_atualizar"><i class="bi bi-floppy-fill"></i> Atualizar</button>
            </div>
        </form>
    </div>
</div>

<div class="card mb-2">
    <div class="card-body p-2">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar_usuario'])) {

            $usuario_aniversario = htmlspecialchars($_POST['usuario_aniversario'], ENT_QUOTES, 'UTF-8');

            $data = DateTime::createFromFormat('d/m', $usuario_aniversario);
            $usuario_aniversario_formatado = $data ? $data->format('2000-m-d') : null;

            $usuario = [
                'usuario_id' => $buscaUsuario['dados']['usuario_id'],
                'usuario_gabinete' => $buscaGabinete['dados']['gabinete_id'],
                'usuario_nome' => htmlspecialchars($_POST['usuario_nome'], ENT_QUOTES, 'UTF-8'),
                'usuario_email' => htmlspecialchars($_POST['usuario_email'], ENT_QUOTES, 'UTF-8'),
                'usuario_telefone' => htmlspecialchars($_POST['usuario_telefone'], ENT_QUOTES, 'UTF-8'),
                'usuario_aniversario' => $usuario_aniversario_formatado, // Data formatada
                'usuario_ativo' => $buscaUsuario['dados']['usuario_ativo'],
                'usuario_tipo' => $buscaUsuario['dados']['usuario_tipo'],
                'usuario_gestor' => $buscaUsuario['dados']['usuario_gestor']
            ];

            $result = $usuarioController->atualizarUsuario($usuario);

            if ($result['status'] == 'success') {
                echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
                $buscaUsuario = $usuarioController->buscaUsuario('usuario_id', $_SESSION['usuario_id']);
            } else if ($result['status'] == 'duplicated' || $result['status'] == 'bad_request' || $result['status'] == 'invalid_email') {
                echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '</div>';
            } else if ($result['status'] == 'forbidden') {
                echo '<div class="alert alert-info px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . '</div>';
            } else if ($result['status'] == 'error') {
                echo '<div class="alert alert-danger px-2 py-1 mb-2 custom-alert" data-timeout="0" role="alert">' . $result['message'] . ' ' . (isset($result['error_id']) ? ' | Código do erro: ' . $result['error_id'] : '') . '</div>';
            }
        }

        ?>
        <p class="card-text mb-2">Meus dados:</p>
        <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
            <div class="col-md-3 col-12">
                <input type="text" class="form-control form-control-sm" name="usuario_nome" placeholder="Nome" value="<?php echo $buscaUsuario['dados']['usuario_nome'] ?>" required>
            </div>
            <div class="col-md-2 col-12">
                <input type="email" class="form-control form-control-sm" name="usuario_email" placeholder="Email" value="<?php echo $buscaUsuario['dados']['usuario_email'] ?>" required>
            </div>
            <div class="col-md-2 col-6">
                <input type="text" class="form-control form-control-sm" name="usuario_telefone" placeholder="Celular (com DDD)" data-mask="(00) 00000-0000" value="<?php echo $buscaUsuario['dados']['usuario_telefone'] ?>" maxlength="15" required>
            </div>
            <div class="col-md-2 col-6">
                <input type="text" class="form-control form-control-sm" name="usuario_aniversario" data-mask="00/00" placeholder="Aniversário (dd/mm)" value="<?php echo $buscaUsuario['dados']['usuario_aniversario'] != '2000-01-01' ?  date('d/m', strtotime($buscaUsuario['dados']['usuario_aniversario'])) : '' ?>" required>
            </div>
            <div class="col-md-3 col-12">
                <button type="submit" class="btn btn-primary btn-sm" name="btn_atualizar_usuario"><i class="bi bi-floppy-fill"></i> Atualizar</button>
            </div>
        </form>

    </div>
</div>


<div class="card mb-2">
    <div class="card-body p-2">
        <p class="card-text mb-2">Usuários disponíveis: <?php echo $buscaGabinete['dados']['gabinete_usuarios'] ?> | <a href="#"> solicite mais usuários</a></p>

        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped mb-0 custom-table">
                <thead>
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col">Email</th>
                        <th scope="col">Telefone</th>
                        <th scope="col">Nível</th>
                        <th scope="col">Ativo</th>
                        <th scope="col">Criado</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $buscaUsuarios = $usuarioController->listarUsuarios(1000, 1, 'asc', 'usuario_nome', $_SESSION['usuario_gabinete']);


                    if ($buscaUsuarios['status'] == 'success') {
                        foreach ($buscaUsuarios['dados'] as $usuario) {
                            echo '<tr>';
                            echo '<td style="white-space: nowrap; justify-content: center; align-items: center;"><a href="?secao=usuario&id=' . $usuario['usuario_id'] . '">' . $usuario['usuario_nome'] . '</a></td>';
                            echo '<td style="white-space: nowrap;">' . $usuario['usuario_email'] . '</td>';
                            echo '<td style="white-space: nowrap;">' . $usuario['usuario_telefone'] . '</td>';
                            echo '<td style="white-space: nowrap;">' . $usuario['usuario_tipo'] . '</td>';
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
    function copyToClipboard() {
        // Pega o link do elemento com o id 'link-cadastro'
        var link = document.getElementById('link-cadastro').innerText;

        // Cria um elemento de input para copiar o texto para a área de transferência
        var tempInput = document.createElement('input');
        tempInput.value = link;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);

        // Opcional: pode adicionar um feedback visual aqui, como um alert ou tooltip
        alert('Link copiado para a área de transferência!');
    }
</script>