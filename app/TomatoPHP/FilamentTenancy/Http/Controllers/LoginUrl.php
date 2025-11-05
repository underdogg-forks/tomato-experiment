<?php

namespace TomatoPHP\FilamentTenancy\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Features\UserImpersonation;

class LoginUrl extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|string|email|max:255',
        ]);

        $tenant = tenancy()->tenant;

        $user                    = User::query()->firstOrNew(['email' => $tenant->email]);
        $user->name              = $tenant->name;
        $user->email             = $tenant->email;
        $user->packages          = $tenant->packages;
        $user->password          = $tenant->password;
        $user->email_verified_at = $user->email_verified_at ?: Carbon::now();
        $user->save();

        $this->syncPermissions($user);

        return UserImpersonation::makeResponse($request->get('token'));
    }

    protected function syncPermissions(User $user): void
    {
        $packageKeys = json_decode($user->packages, true) ?: [];
        if ( ! is_array($packageKeys) || empty($packageKeys)) {
            return;
        }

        $permissions = [];
        foreach (config('app.packages', []) as $key => $package) {
            if ( ! in_array($key, $packageKeys, true)) {
                continue;
            }

            foreach ($package['permissions'] ?? [] as $permission) {
                $permissions = array_merge($permissions, $this->generatePermissions($permission));
            }
        }

        if (empty($permissions)) {
            return;
        }

        $role = Role::query()->firstOrCreate([
            'name'       => 'super_admin',
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($permissions);
        $user->roles()->sync($role->id);

        if ($tenantName = optional(tenancy()->tenant)->name) {
            $site            = new \TomatoPHP\FilamentSettingsHub\Settings\SitesSettings();
            $site->site_name = $tenantName;
            $site->save();
        }
    }

    protected function generatePermissions(string $table): array
    {
        if (str($table)->contains('page')) {
            $definitions = [$table];
        } else {
            $definitions = [
                'view_' . $table,
                'view_any_' . $table,
                'create_' . $table,
                'update_' . $table,
                'restore_' . $table,
                'restore_any_' . $table,
                'replicate_' . $table,
                'reorder_' . $table,
                'delete_' . $table,
                'delete_any_' . $table,
                'force_delete_' . $table,
                'force_delete_any_' . $table,
            ];
        }

        $permissionIds = [];
        foreach ($definitions as $value) {
            $permission = Permission::query()->firstOrCreate([
                'name'       => $value,
                'guard_name' => 'web',
            ]);

            $permissionIds[] = $permission->id;
        }

        return $permissionIds;
    }
}
