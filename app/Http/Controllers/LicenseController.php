<?php

namespace App\Http\Controllers;

use App\classes\GenerateLicense;
use App\Mail\GenerateLicenseMail;
use App\Models\license;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class LicenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(license $license)
    {
        return Inertia::render('license/index',[
            'licenses' => $license->all()->load(['app_license','licenseType','client'])
        ]);
    }

    public function generate(license $license)
    {
        $generate = new GenerateLicense();
        $license->hash = $generate->hash();
        $license->approve = Auth::user()->id;
        $license->save();
        $client = $license->client()->first();

        try {
            $users = [
                "$client->email",
                "elhadjd73@gmail.com"
            ];

            Mail::to($users)->send(new GenerateLicenseMail($client));

            return $this->RespondSuccess('Licença enviada com sucesso',$license);

        } catch (\ErrorException $err) {
            return response()->json('Ola');
        }
    }
}
