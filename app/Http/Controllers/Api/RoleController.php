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

            $roles = Role::with('permissions')->latest()->paginate($perPage);

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

            if($request->has('permissions')) {
                $role->syncPermissions($validated['permissions']);
            }

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
    public function show($id)
    {
        try {
            $role = Role::findOrFail($id);

            return response()->json(
                [
                    'role' => $role,
                    'permissions' => $role->permissions()->pluck('name')
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
    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            
            $role = Role::findOrFail($id);

            $role->update($validated);

            if($request->has('permissions')) {
                $role->syncPermissions($validated['permissions']);
            }

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
    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id)->delete();

            return response()->json(
                [
                    'role' => $role,
                    'message' => __('messages.role_deleted'),
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
