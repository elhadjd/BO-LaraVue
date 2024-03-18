<?php
namespace App\classes;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
class uploadPdf
{

    public function Upload($patch, $client)
    {
        
        $pdf = explode(",", $client->pdf);
        $ext = "";
        $extension = collect(['pdf','PDF']);

        $part1 = substr($pdf[0], strpos($pdf[0], '/') + 1);
        $ext = str_replace(";base64", "", $part1);

        if ($extension->contains($ext)) {
            $decode = base64_decode($pdf[1]);
            $filename = Str::random() . "." . $ext;
            
            $patch = public_path() . $patch . $client->id ;
            if (!File::exists($patch)) File::makeDirectory($patch, 0777, true, true);
            if (file_put_contents($patch.'/' . $filename, $decode)) {
                return $filename;
            }
        }
    }
}
