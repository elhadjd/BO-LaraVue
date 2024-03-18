<?php

namespace App\Http\Controllers;

use App\classes\uploadImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UsersController extends Controller
{
    public function index(User $user)
    {
        return Inertia::render('users/index',[
            'users' => $user->with('userProfile')->get()
        ]);
    }

    public function profile()
    {
        return Inertia::render('users/profile',[
            'user' => Auth::user()
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'user_profile.phone' => 'required',
            'user_profile.level'=> 'required',
            'password' => 'required|min:6|max:200',

        ]);

        $data = (object) $request->all();

        $image = new uploadImage();

        if($request->imagem) {
            $data->user_profile['image']  = $image->Upload('/publicUser/img/',$request->imagem);
        }else {
            $data->user_profile['image'] = $data->user_profile['image'] != "" ? $data->user_profile['image'] : 'user.png';
        }

        DB::transaction(function()use($request,&$data){
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->userProfile()->create($data->user_profile);
        });


        return $this->RespondSuccess('Usuario Cadastrado com Sucesso',[]);
    }

    public function update(Request $request,User $user)
    {
        if (Auth::user()->userProfile->level != 'Administrador') return $this->RespondError('Asesso negado');
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'user_profile.phone' => 'required',
            'user_profile.level'=> 'required',
        ]);

        $data = (object) $request->all();

        $image = new uploadImage();

        if($request->imagem) {
            $data->user_profile['image']  = $image->Upload('/publicUser/img/',$request->imagem);
        }else {
            $data->user_profile['image'] = $data->user_profile['image'] != "" ? $data->user_profile['image'] : 'user.png';
        }

        DB::transaction(function()use($request,&$data,&$user){
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            unset($data->user_profile['created_at'],$data->user_profile['updated_at']);

            $user->userProfile()->update($data->user_profile);
        });

        return $this->RespondSuccess('Dados atualizados com sucesso');
    }

    public function updatePassword(User $user,$password,$newPassword)
    {
        if (Hash::check($password,$user->password)) {
            if ($password === $newPassword) {
                return $this->RespondError('As senhas não podem ser iguais');
            }
            $user->password = Hash::make($password);
            $user->save();
            return $this->RespondSuccess('Senha atualizada com sucesso');
        } else {
            return $this->RespondError('Senha Atual está errada');
        }
    }

    public function destroy(string $id)
    {

    }
}
