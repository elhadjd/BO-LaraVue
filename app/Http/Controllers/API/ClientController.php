<?php

namespace App\Http\Controllers\Api;

use App\classes\uploadImage;
use App\classes\uploadPdf;
use App\Http\Controllers\Controller;
use App\Mail\paymentConfirmation;
use App\Models\account;
use App\Models\App;
use App\Models\Client;
use App\Models\license;
use App\Models\licenseType;
use App\Models\User;
use App\Notifications\LicensePaymentNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        return App::all();
    }

    public function SaveCompany(Request $request,Client $client)
    {
        $request->validate([
            'user.email'=>'email|required|unique:clients,email',
            'company.nif'=> 'required|unique:clients,nif',
            'company.activity' => 'required',
            'company.country' => 'required',
            'company.name' => 'required'
        ]);

        $company = (object) $request->company;
        $user = (object) $request->user;
        $apps = (object) $request->license;
        $finance = (object) $request->totals;
        $countApp = count(json_decode(json_encode($apps), true));
        $licenseType = licenseType::where('name',
        $countApp == 2 ? 'Basic' :
        ($countApp <=4 ? 'Premium' : 'Gold'))->first();
        $data = Carbon::now();
        $dataStart = Carbon::now();
        $dateDue = $data->addMonth($finance->month);

        DB::transaction(function()use($company,&$client,&$user,&$apps,&$dataStart,&$dateDue,&$finance,&$licenseType){
            if ($company->imagem) {
                $image = new uploadImage();
                $nameImage = $image->Upload('/clients/image/',$company->imagem,$client);
            }else{
                $nameImage = "img_empresa.gif";
            }

            $create = $client->create([
                'activity' => $company->activity['name'],
                'country' => $company->country['name'],
                'email' => $user->email,
                'image' => $nameImage,
                'name' => $company->name,
                'nif' => $company->nif,
                'hash'=>Hash::make($company->nif.'__'.$user->email),
                'phone' => $company->phone,
                'city' => $company->city,
            ]);

            $createLicense = $create->license()->create([
                'license_type_id'=> $licenseType->id,
                'hash' => $create->hash,
                'from' => $dataStart,
                'to' => $dateDue
            ]);

            foreach ($apps as $item) {
                $createLicense->app_license()->create([
                    'client_id' => $create->id,
                    'app_id' => $item['id'],
                    'license_id' => $createLicense
                ]);
            }

            $create->accountClient()->create([
                'license_id'=>$createLicense->id,
                'total'=>$finance->total,
                'discount'=>$finance->discount,
                'payed'=>$finance->total - $finance->discount,
                'restPayable' => $finance->total - $finance->discount,
            ]);
        });
        return $this->RespondSuccess('Cliente cadastrado com sucesso',account::all());
    }

    public function RequestAmount(Request $request,Client $client) {
        return ['client'=>$client->where('nif',$request->nif)
        ->where('email',$request->email)->first(),'accounts'=>account::all()];
    }

    public function uploadFile(Request $request)
    {
        $uploadPdf = new uploadPdf();
        $uploadImg = new uploadImage();
        if (isset($request->pdf)) {
            $fileName = $uploadPdf->Upload('/clients/files/',$request);
        }elseif(isset($request->imagem)){
            $fileName = $uploadImg->Upload('/clients/files/'.$request->id.'/',$request->imagem);
        }
        try {
            $user_admin = User::where('email','elhadjd73@gmail.com')->first();

            $user_admin->notify(new LicensePaymentNotification($fileName));

            Mail::to($request->email)->send(new paymentConfirmation($request,$fileName));

            return $this->RespondSuccess('Ficheiro enviado com sucesso');

        } catch (\ErrorException $e) {
            return "Erro ao enviar o email: " . $e->getMessage();
        }
    }

    function activeLicense(Request $request)
    {
        $request->validate([
            'nif' => 'required',
            'hash' => 'required'
        ]);

        $client = Client::where('nif',$request->nif)->first();

        return $this->RespondSuccess('',$client);

        if (!$client) return $this->RespondError('Cliente não encontrado, verifica o nif e tenta novamente');

        if ($client->license()->first()->hash !== $request->hash) return $this->RespondError('Licença invalida');

        if ($client->license()->first()->state == 'active') return $this->RespondError('Esta licença ja foi ativada');

        if ($client->license()->first()->state == 'expired') return $this->RespondError('Esta licença ja esta expirada');

        $update = $client->first()->license()->update([
            'state' => 'active'
        ]);

        if (!$update) return $this->RespondError('Erro ao ativar a licença por favor tente novamente');
    }
}
