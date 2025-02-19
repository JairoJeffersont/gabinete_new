<?php

use GabineteMvc\Controllers\ClienteController;
use GabineteMvc\Controllers\GabineteController;
use GabineteMvc\Controllers\UsuarioController;

ob_start();

session_start();
$_SESSION['usuario_tipo'] = 1;

require_once './vendor/autoload.php';

$gabineteController = new GabineteController();
$clienteController = new ClienteController();
$usuarioController = new UsuarioController();

?>

<link href="public/css/cadastro.css" rel="stylesheet">
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="centralizada text-center">
        <img src="public/img/logo_white.png" alt="" width="100" class="img_logo" />

        <h2 class="login_title mb-2">Conecta Política</h2>
        <p class="text-white">Cadastre seu gabinete</p>


        <?php

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {

            if ($_POST['senha'] !== $_POST['senha2']) {
                echo '<div class="alert alert-info px-2 py-1 mb-2 rounded-5 custom-alert" data-timeout="3" role="alert">Senhas não conferem.</div>';
                exit;
            }

            $dadosCliente = [
                'cliente_nome' => $_POST['nome'],
                'cliente_email' => $_POST['email'],
                'cliente_telefone' => $_POST['telefone'],
                'cliente_ativo' => 1,
                'cliente_endereco' => '',
                'cliente_cep' => '',
                'cliente_cpf' => $_POST['cpf']
            ];

            $resultCliente = $clienteController->novoCliente($dadosCliente);
            if ($resultCliente['status'] !== 'success') exit;

            $idCliente = $clienteController->listarClientes(1, 1, 'desc', 'cliente_criado_em')['dados'][0]['cliente_id'];

            $dadosGabinete = [
                'gabinete_cliente' => $idCliente,
                'gabinete_tipo' => $_POST['tipo'],
                'gabinete_politico' => $_POST['dep_nome'],
                'gabinete_estado' => $_POST['estado'],
                'gabinete_endereco' => '',
                'gabinete_municipio' => '',
                'gabinete_telefone' => $_POST['telefone'],
                'gabinete_funcionarios' => $_POST['assinaturas']
            ];

            $resultGabinete = $gabineteController->novoGabinete($dadosGabinete);
            if ($resultGabinete['status'] !== 'success') exit;

            $idGabinete = $gabineteController->listarGabinetes(1, 1, 'desc', 'gabinete_criado_em')['dados'][0]['gabinete_id'];

            $dadosUsuario = [
                'usuario_gabinete' => $idGabinete,
                'usuario_nome' => $_POST['nome'],
                'usuario_email' => $_POST['email'],
                'usuario_aniversario' => null,
                'usuario_telefone' => $_POST['telefone'],
                'usuario_senha' => $_POST['senha'],
                'usuario_tipo' => 2,
                'usuario_ativo' => 1
            ];

            $resultUsuario = $usuarioController->novoUsuario($dadosUsuario);

            if ($resultUsuario['status'] === 'success') {
                echo '<div class="alert alert-success px-2 py-1 mb-2 rounded-5 custom-alert" data-timeout="3" role="alert">' . $resultUsuario['message'] . '</div>';
            }
        }


        ?>

        <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
            <div class="col-md-6 col-12">
                <input type="text" class="form-control form-control-sm" name="nome" placeholder="Nome" required>
            </div>
            <div class="col-md-6 col-12">
                <input type="text" class="form-control form-control-sm" name="cpf" placeholder="CPF" data-mask="000.000.000-00" required>
            </div>
            <div class="col-md-12 col-12">
                <input type="email" class="form-control form-control-sm" name="email" placeholder="Email" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="text" class="form-control form-control-sm" name="telefone" placeholder="Telefone (com DDD)" data-mask="(00) 00000-0000" maxlength="15" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="text" class="form-control form-control-sm" name="assinaturas" placeholder="Licenças" data-mask="00">
            </div>
            <div class="col-md-6 col-6">
                <select class="form-select form-select-sm form_dep" name="tipo" required>
                    <option selected>Tipo do Gabinete</option>
                    <?php
                    $buscaTipo = $gabineteController->listarTiposGabinete();
                    if ($buscaTipo['status'] == 'success') {
                        foreach ($buscaTipo['dados'] as $tipo) {
                            if ($tipo['tipo_gabinete_id'] != 1) {
                                echo '<option value="' . $tipo['tipo_gabinete_id'] . '">' . $tipo['tipo_gabinete_nome'] . '</option>';
                            }
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-6 col-6">
                <select class="form-select form-select-sm form_dep" name="estado" required>
                    <option selected>Escolha o estado</option>
                    <option value="AC">Acre</option>
                    <option value="AL">Alagoas</option>
                    <option value="AM">Amazonas</option>
                    <option value="AP">Amapá</option>
                    <option value="BA">Bahia</option>
                    <option value="CE">Ceará</option>
                    <option value="DF">Distrito Federal</option>
                    <option value="ES">Espírito Santo</option>
                    <option value="GO">Goiás</option>
                    <option value="MA">Maranhão</option>
                    <option value="MT">Mato Grosso</option>
                    <option value="MS">Mato Grosso do Sul</option>
                    <option value="MG">Minas Gerais</option>
                    <option value="PA">Pará</option>
                    <option value="PB">Paraíba</option>
                    <option value="PE">Pernambuco</option>
                    <option value="PI">Piauí</option>
                    <option value="PR">Paraná</option>
                    <option value="RJ">Rio de Janeiro</option>
                    <option value="RN">Rio Grande do Norte</option>
                    <option value="RO">Rondônia</option>
                    <option value="RR">Roraima</option>
                    <option value="RS">Rio Grande do Sul</option>
                    <option value="SC">Santa Catarina</option>
                    <option value="SE">Sergipe</option>
                    <option value="SP">São Paulo</option>
                    <option value="TO">Tocantins</option>
                </select>
            </div>
            <div class="col-md-12 col-12">
                <input type="text" class="form-control form-control-sm" name="dep_nome" placeholder="Nome da urna" required>
            </div>
            <div class="col-md-6 col-6">
                <input type="password" class="form-control form-control-sm" name="senha" placeholder="Senha" " required>
            </div>
            <div class=" col-md-6 col-6">
                <input type="password" class="form-control form-control-sm" name="senha2" placeholder="Confirme a senha" required>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <button type="submit" name="btn_salvar" class="btn btn-primary">Salvar</button>
                <a type="button" href="?secao=login" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
        <p class="mt-3 copyright"><?php echo date('Y') ?> | JS Digital System</p>
    </div>
</div>