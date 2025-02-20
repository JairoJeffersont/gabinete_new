<?php

$pagina = isset($_GET['secao']) ? $_GET['secao'] :  header('Location: ?secao=home');

$rotas = [
    'login' => './src/Views/login/login.php',
    'cadastro' => './src/Views/cadastro/novo-cliente.php',
    'sair' => './src/Views/login/sair.php',
    'fatal-error' => './src/Views/erros/fatal_error.php',
    'home' => './src/Views/home/home.php',
    'meu-gabinete' => './src/Views/gabinete/meu-gabinete.php',
    'usuario' => './src/Views/gabinete/editar-usuario.php'

];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include './src/Views/erros/404.php';
}
