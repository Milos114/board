<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @group Auth
 *
 * API endpoints for registering and logging in users.
 */
class RegisterController extends Controller
{
    /**
     * @throws ValidationException
     * @unauthenticated
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->plainTextToken;

        return response()->json($success);
    }

    /**
     * @unauthenticated
     */
    public function login(Request $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $success['token'] = Auth::user()->createToken('MyApp')->plainTextToken;

            return response()->json($success);
        }

        return response()->json(['error' => 'Unauthorised'], 401);
    }
}
