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
    'pessoas' => '../src/Views/pessoas/pessoas.php',
    'pessoa' => '../src/Views/pessoas/pessoa.php',
    'aniversariantes' => '../src/Views/pessoas/aniversariantes.php',
    'tipos-documentos' => '../src/Views/documentos/tipos-documentos.php',
    'tipo-documento' => '../src/Views/documentos/tipo-documento.php',
    'documentos' => '../src/Views/documentos/documentos.php',
    'documento' => '../src/Views/documentos/documento.php',
    'tipos-emendas' => '../src/Views/emendas/tipos-emendas.php',
    'tipo-emenda' => '../src/Views/emendas/tipo-emenda.php',
    'situacoes-emendas' => '../src/Views/emendas/situacoes-emendas.php',
    'situacao-emenda' => '../src/Views/emendas/situacao-emenda.php',
    'sair' => '../src/Views/includes/sair.php',
    'home' => '../src/Views/home/home.php',
];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include '../src/Views/errors/404.php';
}
