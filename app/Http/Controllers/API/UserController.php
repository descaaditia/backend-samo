<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Laravel\Fortify\Rules\Password;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request){
        try {
            $request->validate([
                'name' => ['required','string','max:255'],
                'username' => ['required','string','max:255','unique:users'],
                'phone' => ['required','string','max:255'],
                'email' => ['nullable','string','email','max:255','unique:users'],
                'password' => ['required','string',new password],
            ]);
            User::create([
                'name' => $request->input('name'),
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'password' =>Hash::make($request->input('password')),
            ]);

            $user = User::where('email', $request->input('email'))->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ],'User Registered');

        } catch(Exception $error) {

            return ResponseFormatter::error([
                'message' => 'Error Register',
                'error' => $error,
            ],'Authentication Failed..!!',500);
        }
    }

    public function login(Request $request){
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);
            
            $credential = request(['email','password']);

            if(!Auth::attempt($credential)){
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'

                ],'Authentication Failed', 500);
            }

            $user = User::where('email', $request->input('email'))->first();

            if(! Hash::check($request->input('password'), $user->password)){
                 throw new \Exception('Invalid Credential');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user

            ],'Authenticated');

        } catch (Exception $error ) {
            return ResponseFormatter::error([
                'message' => 'Something Wrong',
                'error' => $error

            ],'Authentication Failed', 500);
        }
    }
    public function fetch(Request $request){
        // karena pake sanctum tidak perlu cek maunal, karena user sudah tervalidasi yang sudah login
        return ResponseFormatter::success(
           $request->user(),'Data User Berhasil Diambil'

        );
    }
    public function updateProfile(Request $request){
        $data = '';
        $user = '';
        
        try {
            $request->validate([
                'email' => 'email|required',
                'name' => 'required',
                'username' => 'required',
                'phone' => 'required'
            ]);

            $data = $request->all();
            $user = Auth::user();
            $user->update($data);

            return ResponseFormatter::success(
                $user,
                'User Updated'
     
             );

        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Shometing Wrong',
                'error' => $error

            ],'Update Failed', 202);
        }
    }

    public function logout(Request $request){
        $token = $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success(
            $token,
            'Token Revoked'
 
         );
    }
}
