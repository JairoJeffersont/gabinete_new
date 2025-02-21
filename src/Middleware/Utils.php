<?php

namespace GabineteMvc\Middleware;


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
}
