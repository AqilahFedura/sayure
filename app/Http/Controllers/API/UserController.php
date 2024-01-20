<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\PasswordValidationRules;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth; //mengimport Auth (ini buat akses ke datanya)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // mengimport hash
use Illuminate\Support\Facades\Validator;
use Exception; // mengimport Exceptiom

class UserController extends Controller
{
    use PasswordValidationRules; // Trait untuk aturan validasi password

    public function login(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            // Mengecek credentials (login)
            $credentials = $request->only('email', 'password');
            if (!Auth::attempt($credentials)) {
                // Skenario saat gagal atau error
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], 'Authentication Failed', 500);
            }

            // Jika hash tidak sesuai, beri error
            $user = User::where('email', $request->email)->first();
            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credentials');
            }

            // Jika berhasil login
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');

        } catch (Exception $error) {
            // Menangani kesalahan dan memberikan respons error
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed');
        }
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'], // Validasi nama
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'], // Validasi email
                'address' => ['required', 'string'], // Validasi alamat
                'phoneNumber' => ['required', 'string'], // Validasi nomor telepon
                'houseNumber' => ['required', 'string'], // Validasi nomor rumah
                'city' => ['required', 'string'], // Validasi kota
                'password' => $this->passwordRules(), // Menggunakan aturan validasi password dari trait
            ]);

            // Membuat user baru
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'phoneNumber' => $request->phoneNumber,
                'houseNumber' => $request->houseNumber,
                'city' => $request->city,
                'password' => Hash::make($request->password),
            ]);

            // Membuat token untuk user baru
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            // Memberikan respons sukses
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ]);

        } catch (Exception $error) {
            // Menangani kesalahan dan memberikan respons error
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    //user logout dengan menghapus token yang "sekarang"
    public function logout(Request $request)
    {
        //kita buat token dimana yang diambil itu dari pada saat user regis dan login. setelah itu token dihapus

        $token = $request ->user()->currentAccessToken()->delete(); //jadi disini token "sekarang" yang di hapus 
        return ResponseFormatter::success($token, 'Token Revoked'); // isi variable tokennya $token itu boolean
        //jadi laravel itu udh nyediain banyak fungsi biar kita gaperlu repot hapus token
    }

    //mengambil data user untuk API, disini agar aplikasi dapat mengambil data profile user

    public function fetch(Request $request)
    {
        return ResponseFormatter::success($request->user(), 'Data profile user berhasil diambil');
    }

    //mengupdate informasi user dari database
    public function updateProfile(Request $request)
    {
        //kita ambil semua datanya  jika di update
        $data = $request -> all();

        $user = Auth::user();
        $user->update($data);

        return ResponseFormatter::success($user, 'Profile Updated');
    }

    //update phpto dengan cara memvalidasi
    public function updatePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'file' => 'required|image|max:2048' // ini maxnya kurang dari 2mb
        ]);

        //apabila update photo gagal maka akan memunculkan banyak error

        if($validator->fails())
        {
            return ResponseFormatter::error(
                ['error' => $validator->errors()],
                'update photo fails',
                481
            );
        }

        //jika tidak gagal maka akan tersimpan dalam assets yang di setting public
        if($validator->file('file'))
        {
            $file = $request->file->store('assets/user', 'public');

            // simpan foto ke database (urlnya)
            $user = Auth::user(); //panggil model usernya
            $user->profile_photo_path = $file; //ini bawaan dari laravelnya
            $user->update();

            return ResponseFormatter::success([$file], 'File successfully uploaded');


        }

    }
    
}
