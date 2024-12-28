<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{

    public function __construct(){
        $this->middleware(
            'permission:show_permissions',
            [
                'only' => [
                    'showAssignPermissionsForm',
                    'getPermissionsForRole'
                ]
            ]
        );

        $this->middleware(
            'permission:manage_permissions',
            [
                'only' => [
                    'assignPermissions'
                ]
            ]
        );
    }
    
    public function showAssignPermissionsForm()
    {
        $roles = Role::all();
        $permissions = Permission::all();
    
        // Fetch permissions assigned to the selected role (if any)
        $rolePermissions = [];
        if (old('role_id')) {
            $role = Role::find(old('role_id'));
            if ($role) {
                // Get all the permission ids assigned to the role
                $rolePermissions = $role->permissions->pluck('id')->toArray();
            }
        }
    
        return view('roles.index', compact('roles', 'permissions', 'rolePermissions'));
    }
    
    public function assignPermissions(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        $role = Role::find($request->role_id);
        if ($role) {
            // Sync the permissions with the role (add new ones and remove unselected ones)
            $role->permissions()->sync($request->permissions);
        }
    
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        
        return redirect()->back()->with('success', 'Permissions assigned successfully.');
    }

    public function getPermissionsForRole($roleId)
    {
        // Fetch the role
        $role = Role::findOrFail($roleId);

        // Get the permissions assigned to this role
        $permissions = $role->permissions->pluck('id')->toArray();

        // Return the permissions as a JSON response
        return response()->json([
            'permissions' => $permissions
        ]);
    }
    

}
