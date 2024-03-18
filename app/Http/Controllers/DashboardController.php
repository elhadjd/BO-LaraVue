<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\company;
use App\Models\license;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('dashboard/index', [
            "cards" => [
                [
                    'title' => 'Total de Licenças Gerada',
                    'count' => license::all()->count()
                ],

                [
                    'title' => 'Total de Administradores Gerada',
                    'count' => User::all()->count()
                ],
            ],
            "activity" => [
                [
                    'name' => 'Leonardo rv comercio e serviços',
                    "type" => 'Solicitação de licença',
                    'company' => 'Leonardo rv comercio e serviços',
                    "nif" => '5000659738',
                    "created_at" => date("d-m-Y")
                ],
                [
                    'name' => 'Leonardo rv comercio e serviços',
                    "type" => 'Solicitação de licença',
                    'company' => 'Leonardo rv comercio e serviços',
                    "nif" => '5000659738',
                    "created_at" => date("d-m-Y")
                ],
                [
                    'name' => 'Leonardo rv comercio e serviços',
                    "type" => 'Solicitação de licença',
                    'company' => 'Leonardo rv comercio e serviços',
                    "nif" => '5000659738',
                    "created_at" => date("d-m-Y")
                ]
            ]
        ]);
    }

    public function getNotifications() {
        return DB::table('notifications')->where('read_at',null)->get();
    }

    public function unreadNotification()
    {
        return DB::table('notifications')->where('read_at',null)->update([
            'read_at'=>now()
        ]);
    }
}
