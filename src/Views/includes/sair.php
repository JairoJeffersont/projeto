<?php

$sessionHelper = new \App\Helpers\SessionHelper();

$sessionHelper::destruirSessao();
header('location: ?secao=login');