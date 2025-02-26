<?php

namespace GabineteMvc\Middleware;

use DateTime;

class Utils {

    public function sanitizarString($string) {
        $string = mb_strtolower($string, 'UTF-8');
        $string = preg_replace(
            array("/(á|à|ã|â|ä)/", "/(é|è|ê|ë)/", "/(í|ì|î|ï)/", "/(ó|ò|õ|ô|ö)/", "/(ú|ù|û|ü)/", "/(ñ)/", "/(ç)/"),
            array("a", "e", "i", "o", "u", "n", "c"),
            $string
        );
        $string = str_replace(' ', '-', $string);
        $string = preg_replace('/[^\w-]/', '', $string);

        return $string;
    }

    function calculaAniversario($aniversario) {
        $dataAtual = new DateTime();
        $anoAtual = $dataAtual->format('Y');

        $dataAniversario = DateTime::createFromFormat('Y-m-d', $aniversario);
        $mesDiaAniversario = $dataAniversario->format('m-d');

        $dataAniversario = DateTime::createFromFormat('Y-m-d', $anoAtual . '-' . $mesDiaAniversario);

        if ($dataAniversario < $dataAtual) {
            $dataAniversario->modify('+1 year');
        }

        $diferencaDias = $dataAtual->diff($dataAniversario)->days;

        if ($mesDiaAniversario === $dataAtual->format('m-d')) {
            return '🎉 Parabéns! Hoje é o seu aniversário! 🎂<hr>';
        } elseif ($diferencaDias <= 90) {
            return '<i class="bi bi-cake"></i> Seu aniversário está chegando! Faltam ' . $diferencaDias . ' dias<hr>';
        }
        return '';
    }

    function formatarAniversario($aniversario) {

        $data = DateTime::createFromFormat('d/m', $aniversario);
        return $usuario_aniversario_formatado = $data ? $data->format('2000-m-d') : null;
    }
}
