<div class="card mb-2 ">
    <div class="card-body p-1">
        <a class="btn btn-primary btn-sm custom-nav barra_navegacao" href="?secao=home" role="button"><i class="bi bi-house-door-fill"></i> Início</a>
    </div>
</div>

<div class="card mb-2">
    <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-person-gear"></i> Área do Usuário</div>
    <div class="card-body card_descricao_body p-2">
        <p class="card-text">Nesta área, você pode gerenciar seus dados pessoais e realizar a atualização de informações, incluindo a troca de senha.</p>
    </div>
</div>

<div class="card mb-2">

    <div class="card-body card_descricao_body p-2">
        <h6 class="card-title mb-1 mt-1"><?php echo $buscaGabinete['dados']['gabinete_nome'] . '/' . $buscaGabinete['dados']['gabinete_estado']; ?></h6>
        <p class="card-text mb-0">Gestor:
            <?php
            $buscaGestor = $usuarioController->listarUsuarios(1, 1, 'desc', 'usuario_gestor', $_SESSION['usuario_gabinete']);
            echo $buscaGestor['dados'][0]['usuario_nome'];
            ?>
        </p>
    </div>

</div>

<div class="card mb-2">
    <div class="card-body p-2">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_atualizar'])) {

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
                'usuario_tipo' => $buscaUsuario['dados']['usuario_tipo']
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
                <button type="submit" class="btn btn-success btn-sm" name="btn_atualizar"><i class="bi bi-floppy-fill"></i> Salvar</button>
            </div>
        </form>

    </div>
</div>