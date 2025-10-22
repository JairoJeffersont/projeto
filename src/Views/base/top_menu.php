<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container-fluid">
        <button class="btn btn-primary" style="font-size:0.9em" id="sidebarToggle">Menu</button>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mt-2 mt-lg-0">

                <li class="nav-item"><a class="nav-link loading-modal" href="?secao=meu-gabinete">Meu gabinete</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Configurações</a>
                    <div class="dropdown-menu dropdown-menu-end  custom_menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item loading-modal" href="?secao=tipos-orgaos">Tipos de órgões e entidades</a>
                        <a class="dropdown-item loading-modal" href="?secao=tipos-pessoas">Tipos de pessoas</a>
                        <a class="dropdown-item loading-modal" href="?secao=profissoes">Profissões</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item loading-modal" href="?secao=tipos-documentos">Tipos de documentos</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item loading-modal" href="?secao=tipos-emendas">Tipos de emendas</a>
                        <a class="dropdown-item loading-modal" href="?secao=situacoes-emendas">Situações de emendas</a>

                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $_SESSION['usuario']['nome']; ?></a>
                    <div class="dropdown-menu dropdown-menu-end  custom_menu" aria-labelledby="navbarDropdown">

                        <a class="dropdown-item loading-modal loading-modal confirm-action" data-message="Tem certeza que deseja encerrar sua sessão?" href="?secao=sair">Sair</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>