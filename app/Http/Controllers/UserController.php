<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class UserController extends Controller
{
    public function create() {
        return view('users.register');
        
    }

    // create new user
    public function store(Request $request){
        $formFields = $request->validate([
            'name' => ['required', 'min:3'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password'=> 'required|confirmed|min:6'
        ]);

        // hash password
        $formFields['password'] = bcrypt($formFields['password']);
        // Create User
        $user = User::create($formFields);

        //Login
        auth()->login($user);
        return redirect('/')->with('message', 'User created and Logged in');
    }


    // logout
    public function logout(Request $request) {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('message','You have been logges out !');
        
    }

    //Show Login Form
    public function login(){
        return view('users.login');
        
    }

    public function authenticate(Request $request){
        $formFields = $request->validate([
            'email' => ['required', 'email'],
            'password'=> 'required'
        ]);

        if(auth()->attempt($formFields)) {
            $request->session()->regenerate();

            return redirect('/')->with('message', 'You are now logged !');
        }
        return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');
    }
   

}
