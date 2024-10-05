<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="My API", version="1.0.0")
 */

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *    path="/api/register",
     *    tags={"Authentication"},
     *    summary="Register User",
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", enum={"application/json"})
     *     ),
     *     @OA\Parameter(
     *         name="Content-Type",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", enum={"application/json"})
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@gmail.com"),
     *             @OA\Property(property="password", type="string", example="123456789"),
     *             @OA\Property(property="password_confirmation", type="string", example="123456789")
     *         )
     *     ),
     *    @OA\Response(response="201",description="You are register successfully"),
     *    @OA\Response(response="422", description="Validation errors"),
     *    @OA\Response(response="500", description="Server error"),
     * )
     */

    public function register(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();

            $validated['password'] = Hash::make($request->password);

            $user = User::create($validated);

            $token = $user->createToken($request->name)->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => __('messages.register_success'),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Login User",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="john@gmail.com"),
     *             @OA\Property(property="password", type="string", example="123456789"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="You are login successfully"),
     *     @OA\Response(response="422", description="Validation errors"),
     *     @OA\Response(response="401", description="Invalid credentials errors"),
     *     @OA\Response(response="500", description="Server error"),
     * )
     */

    public function login(LoginRequest $request)
    {
        try {

            $request->validated();

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['errors' => [
                    'email' => [__('messages.invalid_credentials')],
                ]], 401);
            }

            $token = $user->createToken($user->name)->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => __('messages.lgoin_success'),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout the user",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="You are logout successfully"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="500", description="Server error"),
     * )
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'message' =>  __('messages.logout_success'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Get user details",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="User details"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=500, description="server error")
     * )
     */

    public function getUser(Request $request) {
        try {
            return response()->json([
                'user' => $request->user(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
