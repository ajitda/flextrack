<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $inputdata = $request->all();
        $validation = $this->registerValidator($inputdata);
        if ($validation->fails()) {
            return $this->sendError('Validation Error', $validation->errors());
        }
        $user = User::create([
            'name' => $inputdata['name'],
            'email' => $inputdata['email'],
            'password' => Hash::make($inputdata['password']),
        ]);
        $access_token = $user->createToken('authToken')->accessToken;
        return $this->sendResponse(['user'=>$user, 'access_token'=>$access_token], 'User Created Successsfully!');
    }

    public function login(Request $request)
    {
        $inputdata = $request->all();
        $validation = $this->loginValidator($inputdata);
        if ($validation->fails()) {
            return $this->sendError('Validation Error', $validation->errors());
        }
        if (! auth()->attempt($inputdata)){
            return $this->sendError('Invalid Credentials');
        }
        $access_token = auth()->user()->createToken('authToken')->accessToken;
        return $this->sendResponse(['user'=>auth()->user(), 'access_token'=>$access_token], 'User Created Successsfully!');
    }

    protected function registerValidator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function loginValidator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string'],
            'password' => ['required', 'string']
        ]);
    }

    
}
