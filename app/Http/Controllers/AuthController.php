<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;

class AuthController extends Controller
{
    //
    public function loadRegister(){
        return view('register');
    }

    public function userRegister(Request $request){

        $request->validate([
            'name' => "required",
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ]);
        $user = new User;
        $user->name =  $request->name;
        $user->email =  $request->email;
        $user->password = Hash::make($request->password);
        $user->is_subscribed = 0;
        $user->save();
        return redirect()->back()->with('success', 'User register successsfully');

    }

    public function loadLogin(){
        return view('login');
    }

    public function userLogin(Request $request){
        $request->validate([
           
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
           return redirect()->route('dashboard');
        }
    }

    public function dashboard(){
        return view('dashboard');
    }

    public function logout(){
        Auth::logout();
        return redirect('/');
    }
}
