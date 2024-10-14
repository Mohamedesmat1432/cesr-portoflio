<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 2); // Default to 10 items per page

            $users = User::with('roles')->latest()->paginate($perPage);

            return response()->json(
                [
                    'users' => $users,
                    'message' => __('messages.all_users'),
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'errors' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = User::create($validated);

            if($request->has('roles')) {
                $user->syncPermissions($validated['roles']);
            }

            return response()->json(
                [
                    'user' => $user,
                    'message' => __('messages.user_created'),
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'errors' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json(
                [
                    'user' => $user,
                    'roles' => $user->roles()->pluck('name')
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'errors' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            
            $user = User::findOrFail($id);

            $user->update($validated);

            if($request->has('roles')) {
                $user->syncPermissions($validated['roles']);
            }

            return response()->json(
                [
                    'user' => $user,
                    'message' => __('messages.user_updated'),
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'errors' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id)->delete();

            return response()->json(
                [
                    'user' => $user,
                    'message' => __('messages.user_deleted'),
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'errors' => $e->getMessage(),
                ],
                500,
            );
        }
    }
}
