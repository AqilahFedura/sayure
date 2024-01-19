<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function login (request $request)
    {
        try {
            //validasi input
            $request->validate([
                'email'=>'email|required',
                'password' => 'required'
            ]);
        }
        //mengecek credentials (login)
        $credentials = Request(['email', 'password']);
        if(!Auth::attempt($credentials)){ //melakukan pengecekan
            //scanario pas fail atau error
            return ResponseFormatter::error([// ini itu sesuai di response formatter (ini bagian data)
                'massage'=> 'Unauthorized' // pesannya
            ],    'Authentication Failed', 500); // ini bagian kodenya
    

            }

            //jika hash tidak sesuai maka beri error
            $user = User::where('email', $request->email)->first(); // ini tu cuman satu diambil emailnya karena emailkan pasti satu yah, sama unik
            if(!hash::check($request->password, $user->password , [])){ // nah ini mengecek apakah inputan user passwordnya sama dengan yang ada di database
                throw new \Exception('invalid Credentials'); //ini kalau error

            }
            //jika berhasil loginkan
            $tokenResult = $user->createToken('authToken');
            $tokenResult = $tokenResult->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');

        //ini kita buat catch semisal error diluar login
        } catch(Exception $error){ // ini masih error gatau knp
            return ResponseFormatter:error([
                'massage' => 'something went wrong'
                'error' => $error
            ]) 'Authenticated');
        }
    }

    public function res
}