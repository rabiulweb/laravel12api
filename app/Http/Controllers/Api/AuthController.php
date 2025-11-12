<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|min:2',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'fail',
                'message' => $validator->errors(),
            ], 400);
        }

        $data = $request->all();
        User::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'User created successfully',
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'fail',
                'message' => $validator->errors(),
            ], 400);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            $response['token'] = $user->createToken('BlogApp')->plainTextToken;
            $response['email'] = $user->email;
            $response['name']  = $user->name;

            return response()->json([
                'status'  => 'success',
                'message' => "Logged in successfully!",
                'data'    => $response,
            ], 200);
        } else {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Invalid creanditials',
            ], 400);
        }
    }

    public function profile()
    {
        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'data'   => $user,
        ], 200);
    }

    public function logout()
    {
        $user = Auth::user();
        $user->tokens()->delete();
        return response()->json([
            'status'  => 'success',
            'message' => 'Logout successfully!',
        ], 200);
    }
}
