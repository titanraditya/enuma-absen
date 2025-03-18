<?php

namespace App\Validation;

class CustomRules
{
    public function no_space(string $str, string &$error = null): bool
    {
        if (strpos($str, ' ') !== false) {
            $error = 'Tidak boleh mengandung spasi.';
            return false;
        }
        return true;
    }
}