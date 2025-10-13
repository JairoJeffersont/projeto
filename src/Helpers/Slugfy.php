<?php

namespace App\Helpers;

class Slugfy {
    public static function slug(string $nome): string {
        $nome = strtolower($nome);

        // Remove títulos
        $titulos = ['dr.', 'dra.', 'sr.', 'sra.', 'srta.', 'senador', 'senadora', 'prof.', 'professor', 'professora'];
        $nome = str_replace($titulos, '', $nome);

        // Remove acentos
        $nome = iconv('UTF-8', 'ASCII//TRANSLIT', $nome);

        // Remove caracteres especiais
        $nome = preg_replace('/[^\p{L}\s-]/u', '', $nome);

        // Remove espaços extras
        $nome = trim($nome);

        // Substitui espaços por hífen
        $nome = preg_replace('/\s+/', '-', $nome);

        return $nome;
    }
}
