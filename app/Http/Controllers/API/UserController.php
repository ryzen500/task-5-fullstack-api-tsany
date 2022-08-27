<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $response['status'] = true;
            $response['message'] = 'Berhasil login';
            $response['data']['token'] = 'Bearer ' . $user->createToken('MyToken')->accessToken;

            return response()->json($response, 200);
        }else {
            $response['status'] = false;
            $response['message'] = 'email atau password salah';

            return response()->json($response, 401);
        }
    }

    public function register(Request $request) 
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'password_confirmation' => 'required|same:password'
        ]);

        if ($validate->fails())
        {
            $response['status'] = false;
            $response['message'] = 'Gagal registrasi';
            $response['error'] = $validate->errors();
            
            return response()->json($response, 422);
        }
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request->password),
        ]);
        $response['status'] = true;
        $response['message'] = 'Berhasil registrasi';
        $response['data']['token'] = 'Bearer ' . $user->createToken('MyToken')->accessToken;
        return response()->json($response, 200);
    }
}
