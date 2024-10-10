<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function allPermissions()
    {
        return response()->json([
            'permissions' => Permission::all()
        ], 200);
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 2); // Default to 10 items per page

            $permissions = Permission::latest()->paginate($perPage);

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

            $validated['guard_name'] = 'web';

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
    public function show($id)
    {
        $permission = Permission::findOrFail($id);
        
        return response()->json([
            'permission' => $permission,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, $id)
    {
        try{
            $validated = $request->validated();

            $permission = Permission::findOrFail($id)->update($validated);

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
    public function destroy($id)
    {
        try{
            $permission = Permission::findOrFail($id)->delete();

            return response()->json(
                [
                    'permission' => $permission,
                    'message' => __('messages.permission_deleted'),
                ],
                200,
            );
        }catch(\Exception $e){
            return response()->json([
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
