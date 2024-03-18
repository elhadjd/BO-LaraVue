<?php

namespace App\Http\Controllers;

use App\classes\uploadImage;
use App\Models\activity_type;
use App\Models\App;
use App\Models\AppLicense;
use App\Models\Client;
use App\Models\license;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ClientsController extends Controller
{

    public function index()
    {
        return Inertia::render('clients/index',[
            'clients' => Client::all()
        ]);
    }

    public function getActivity()
    {
        return activity_type::all();
    }


    public function newClient()
    {
        return Inertia::render('clients/Company');
    }

    public function license(Request $request)
    {
        return Inertia::render('clients/License',$request) ;
    }

    public function saveCompany(Request $request,Client $client)
    {
        if ($request->id) return $this->updateClient($request,$client);
        $request->validate([
            'email'=>'email|required|unique:clients',
            'nif'=> 'required|unique:clients,nif',
            'activity' => 'required',
            'country' => 'required',
            'name' => 'required'
        ]);

        DB::transaction(function()use($request,&$client){
            if ($request->imagem) {
                $image = new uploadImage();
                $nameImage = $image->Upload('/clients/image/',$request->imagem,$client);
            }else{
                $nameImage = "img_empresa.gif";
            }

            $create = $client->create([
                'activity' => $request->activity['name'],
                'country' => $request->country['name'],
                'email' => $request->email,
                'image' => $nameImage,
                'name' => $request->name,
                'nif' => $request->nif,
                'hash'=>Hash::make($request->nif.'__'.$request->email),
                'phone' => $request->phone,
                'city' => $request->city,
                'house_number' => $request->house_number,
                'thirst' => $request->thirst,
            ]);
            $create->license()->create([
                'license_type_id'=>1
            ]);

        });
        return $this->RespondSuccess('Cliente cadastrado com sucesso');
    }

    public function updateClient($request, $client)
    {
        if ($request->imagem) {
            $image = new uploadImage();
            $nameImage = $image->Upload('/clients/image/',$request->imagem,$client);
        }else{
            $nameImage = $request->image;
        }
        $update = [
            'activity' => $request->activity['name'],
            'country' => $request->country['name'],
            'email' => $request->email,
            'image' => $nameImage,
            'name' => $request->name,
            'nif' => $request->nif,
            'hash'=>$request->hash,
            'phone' => $request->phone,
            'city' => $request->city,
            'house_number' => $request->house_number,
            'thirst' => $request->thirst,
        ];
        if (!$client->update($update)) return $this->RespondError('Aconteceu um erro ao atualizar o cliente.');
        return $this->RespondSuccess('Cliente atualizado com sucesso');
    }

    public function getApps()
    {
       return App::all();
    }
    public function AddApps(Request $request, License $license, $client)
    {
        foreach ($request->all() as $item) {
            $license->app_license()->create([
                'client_id' => $client,
                'app_id' => $item['id'],
                'license_id' => $license
            ]);
        }
        return $license->app_license()->get();
    }
}
