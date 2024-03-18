<?php

namespace App\classes;

use App\Models\license;
use Illuminate\Support\Facades\DB;

class GenerateLicense
{
    public static function GenerateHash($size = 60)
    {

        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789';
        $quantidadeCaracteres = strlen($caracteres);
        $hash = '';

        for ($i = 0; $i < $size; $i++) {
            $index = random_int(0, $quantidadeCaracteres - 1);
            $hash .= $caracteres[$index];
        }
        return $hash;
    }
    public function hash()
    {
        do {
            $hash = GenerateLicense::GenerateHash();
        } while (DB::table('licenses')->where('hash', $hash)->exists());
        return  $hash;
    }
}
