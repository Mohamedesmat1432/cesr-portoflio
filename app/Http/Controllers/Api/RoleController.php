<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * All resource in storage.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 2); // Default to 10 items per page

            $roles = Role::latest()->paginate($perPage);

            return response()->json(
                [
                    'roles' => $roles,
                    'message' => __('messages.all_roles'),
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
    public function store(StoreRoleRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['guard_name'] = 'web';

            $role = Role::create($validated);

            return response()->json(
                [
                    'role' => $role,
                    'message' => __('messages.role_created'),
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
    public function show(Role $role)
    {
        try {
            return response()->json(
                [
                    'role' => $role,
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
    public function update(UpdateRoleRequest $request, Role $role)
    {
        try {
            $validated = $request->validated();

            $role = $role->update($validated);

            return response()->json(
                [
                    'role' => $role,
                    'message' => __('messages.role_updated'),
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
    public function destroy(Role $role)
    {
        try {
            // $role = Role::find($id);
            if ($role) {
                $role->delete();

                return response()->json(
                    [
                        'role' => $role,
                        'message' => __('messages.role_deleted'),
                    ],
                    200,
                );
            }
            return response()->json(
                [
                    'message' => __('messages.validate_errors'),
                ],
                404,
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
