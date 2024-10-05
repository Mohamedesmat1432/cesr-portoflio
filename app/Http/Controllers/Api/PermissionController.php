<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        try {
            $permissions = Permission::paginate(10);

            return response()->json([
                'permissions' => $permissions,
                'message'=> __('messages.all_permissions')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request)
    {
        try{
            $validated = $request->validated();

            $permission = Permission::create($validated);

            return response()->json([
                'permission' => $permission,
                'message'=> __('messages.permission_created')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        return response()->json([
            'permission' => $permission,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        try{
            $validated = $request->validated();

            $permission = $permission->update($validated);

            return response()->json([
                'permission' => $permission,
                'message'=> __('messages.permission_updated')
            ], 200);
    
        }catch(\Exception $e){
            return response()->json([
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(permission $permission)
    {
        try{
            $permission = $permission->delete();

            return response()->json([
                'permission' => $permission,
                'message'=> __('messages.permission_deleted')
            ], 200);
    
        }catch(\Exception $e){
            return response()->json([
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
