<?php
$pagina = isset($_GET['secao']) ? $_GET['secao'] :  header('Location: ?secao=home');

$rotas = [
    'login' => '../src/Views/login/login.php',
    'cadastro' => '../src/Views/cadastro/cadastro.php',
    'recuperar-senha' => '../src/Views/login/recuperar-senha.php',
    'nova-senha' => '../src/Views/login/nova-senha.php',
    'sair' => '../src/Views/includes/sair.php',
    'home' => '../src/Views/home/home.php',
];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include '../src/Views/errors/404.php';
}
