<div class="card mb-2">
    <div class="card-header bg-primary text-white px-2 py-1 card_descricao_bg"><i class="bi bi-gear"></i> Configuração inicial</div>
    <div class="card-body card_descricao_body">
        <h5 class="card-title mb-3 mt-1">Seja bem-vindo(a): <?php echo $buscaUsuario['dados']['usuario_nome']; ?>!</h5>
        <p class="card-text mb-0">Bem-vindo ao seu primeiro acesso! Para prosseguir, preencha as informações do gabinete ao qual você pertence e onde atua como gestor.</p>
        <p class="card-text mb-2"> Após concluir essa configuração inicial, você poderá cadastrar os membros do gabinete que utilizarão o sistema.</p>
        <hr>
        <p class="card-text mb-0"><i class="bi bi-dot"></i> Gabinete: <?php echo $buscaGabinete['dados']['gabinete_nome'] . '/' . $buscaGabinete['dados']['gabinete_estado']; ?></p>
        <p class="card-text mb-3"><i class="bi bi-dot"></i> Usuários permitidos: <?php echo $buscaGabinete['dados']['gabinete_usuarios']; ?></p>
        <p class="card-text mb-0"> Preencha os dados abaixo.</p>
    </div>

</div>

<div class="card shadow-sm mb-2">
    <div class="card-body p-2">

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {

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
                echo '<div class="alert alert-success px-2 py-1 mb-2 custom-alert" data-timeout="3" role="alert">' . $result['message'] . '. Aguarde...</div>';
                echo "<script>setTimeout(() => {
                        window.location.href = '?secao=home';
                    }, 1000);
                    </script>";
            }
        }

        ?>
        <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
            <div class="col-md-2 col-12">
                <input type="text" class="form-control form-control-sm" name="gabinete_endereco" placeholder="Endereço" required>
            </div>
            <div class="col-md-2 col-12">
                <input type="text" class="form-control form-control-sm" name="gabinete_municipio" placeholder="Município" required>
            </div>
            <div class="col-md-2 col-6">
                <input type="email" class="form-control form-control-sm" name="gabinete_email" placeholder="E-mail" required>
            </div>
            <div class="col-md-2 col-6">
                <input type="text" class="form-control form-control-sm" name="gabinete_telefone" data-mask="(61) 00000-0000" placeholder="Telefone com DDD" required>
            </div>
            <div class="col-md-2 col-12">
                <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
            </div>
        </form>
    </div>
</div>