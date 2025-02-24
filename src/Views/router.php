<?php

$pagina = isset($_GET['secao']) ? $_GET['secao'] :  header('Location: ?secao=home');

$rotas = [
    'login' => './src/Views/login/login.php',
    'cadastro' => './src/Views/cadastro/novo-gabinete.php',
    'novo-usuario' => './src/Views/cadastro/novo-usuario.php',
    'sair' => './src/Views/login/sair.php',
    'fatal-error' => './src/Views/erros/fatal_error.php',
    'home' => './src/Views/home/home.php',
    'meu-gabinete' => './src/Views/meu-gabinete/meu-gabinete.php',
    'usuario' => './src/Views/meu-gabinete/editar-usuario.php',
    'minhas-mensagens' => './src/Views/minhas-mensagens/mensagens.php',
    'mensagem' => './src/Views/minhas-mensagens/mensagem.php',
    'tipos-orgaos' => './src/Views/orgao/tipos-orgaos.php',
    'tipo-orgao' => './src/Views/orgao/tipo-orgao.php',
    'orgaos' => './src/Views/orgao/orgaos.php',
    'orgao' => './src/Views/orgao/orgao.php',
    'tipos-pessoas' => './src/Views/pessoa/tipos-pessoas.php',
    'tipo-pessoa' => './src/Views/pessoa/tipo-pessoa.php',
    'profissoes' => './src/Views/pessoa/profissoes.php',
    'profissao' => './src/Views/pessoa/profissao.php',



];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include './src/Views/erros/404.php';
}
