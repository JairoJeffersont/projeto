<?php
$pagina = isset($_GET['secao']) ? $_GET['secao'] :  header('Location: ?secao=home');

$rotas = [
    'login' => '../src/Views/login/login.php',
    'cadastro' => '../src/Views/cadastro/cadastro.php',
    'recuperar-senha' => '../src/Views/login/recuperar-senha.php',
    'nova-senha' => '../src/Views/login/nova-senha.php',
    'tipos-orgaos' => '../src/Views/orgaos/tipos-orgaos.php',
    'tipo-orgao' => '../src/Views/orgaos/tipo-orgao.php',
    'orgaos' => '../src/Views/orgaos/orgaos.php',
    'orgao' => '../src/Views/orgaos/orgao.php',
    'tipos-pessoas' => '../src/Views/pessoas/tipos-pessoas.php',
    'tipo-pessoa' => '../src/Views/pessoas/tipo-pessoa.php',
    'profissoes' => '../src/Views/pessoas/profissoes.php',
    'profissao' => '../src/Views/pessoas/profissao.php',
    'sair' => '../src/Views/includes/sair.php',
    'home' => '../src/Views/home/home.php',
];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include '../src/Views/errors/404.php';
}
