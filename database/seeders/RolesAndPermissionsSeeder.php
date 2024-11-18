<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [

            // Material
            'manage-material',
            'material-view',
            'material-create',
            'material-edit',
            'material-delete',

            // Customer
            'manage-customer',
            'customer-view',
            'customer-create',
            'customer-edit',
            'customer-delete',
            // Daily activity
            'daily-activity-create',
            

//permission
            'manage-permission',
            'permission-view',
            'permission-create',
            'permission-edit',
            'permission-delete',

         
            // Proforma
            'manage-proforma',
            'proforma-view',
            'proforma-create',
            'proforma-edit',
            'proforma-delete',
            'proforma-print',
            // proforma image
            'manage-proforma-image',
            'proforma-image-view',
            'proforma-image-create',
            'proforma-image-edit',
            'proforma-image-delete',
            'proforma-image-approve',
            'proforma-image-decline',
            // proforma work
            'manage-proforma-work',
            'proforma-work-view',
            'proforma-work-create',
            'proforma-work-edit',
            'proforma-work-delete',
            'proforma-work-approve',
            'proforma-work-decline',
            // project
            'manage-project',
            'project-view',
            'project-create',
            'project-edit',
            'project-delete',
            'project-material-print',
            'project-material-edit',
            'project-material-delete',
            'project-upload-file',
            // Purchase request
            'manage-purchase-request',
            'purchase-request-view',
            'purchase-request-create',
            'purchase-request-edit',
            'purchase-request-delete',
            'purchase-request-approve',
            'purchase-request-decline',
//role
'manage-role',
'role-view',
'role-create',
'role-update',
'role-delete',
//seller
'manage-seller',
'seller-view',
'seller-create',
'seller-update',
'seller-delete',
//service
'manage-service',
'service-view',
'service-create',
'service-update',
'service-delete',
//service detail
'service-detail-view',
'service-detail-create',
'service-detail-update',
'service-detail-delete',
//setting 
'manage-setting',
'setting-view',
'setting-create',
'setting-update',
'setting-delete',
//stock 
'manage-stock',
'stock-view',
'stock-create',
'stock-update',
'stock-delete',
'stock-remove-material',
'stock-add-material',

//team
'manage-team',
'team-view',
'team-create',
'team-update',
'team-delete',
//user
'manage-user',
'user-view',
'user-create',
'user-update',
'user-delete',
'user-assign-team',
           

        ];


        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }


        $roles = [
            'Admin' => $permissions
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::create(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);

        }
    }
}
