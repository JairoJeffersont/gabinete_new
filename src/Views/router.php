<?php

$pagina = isset($_GET['secao']) ? $_GET['secao'] :  'home';

$rotas = [
    'login' => './src/Views/login/login.php',
    'sair' => './src/Views/login/sair.php',
    'home' => './src/Views/home/home.php',
    'usuarios' => './src/Views/usuario/usuarios.php',
    'usuario' => './src/Views/usuario/editar-usuario.php',
    'cadastro' => './src/Views/cliente/cadastro-cliente.php',
    'fatal-error' => './src/Views/erro/fatal_error.php'
];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include './src/Views/erro/404.php';
}
