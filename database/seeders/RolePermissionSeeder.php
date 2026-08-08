<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Modul konten yang direncanakan untuk CMS.
     *
     * @var array<int, string>
     */
    protected array $modules = [
        'posts',
        'pages',
        'categories',
        'tags',
        'comments',
        'media',
        'albums',
        'banners',
        'menus',
        'widgets',
        'settings',
        'users',
    ];

    /**
     * Aksi standar per modul.
     *
     * @var array<int, string>
     */
    protected array $actions = [
        'view_any',
        'view',
        'create',
        'update',
        'delete',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $allPermissions = [];

        foreach ($this->modules as $module) {
            foreach ($this->actions as $action) {
                $name = "{$module}.{$action}";
                Permission::findOrCreate($name, 'web');
                $allPermissions[] = $name;
            }
        }

        $contentModules = array_diff($this->modules, ['users', 'settings']);
        $contentPermissions = array_values(array_filter(
            $allPermissions,
            fn (string $name): bool => in_array(explode('.', $name)[0], $contentModules, true),
        ));

        $penulisModules = ['posts', 'pages', 'categories', 'tags', 'media'];
        $penulisPermissions = array_values(array_filter(
            $allPermissions,
            fn (string $name): bool => in_array(explode('.', $name)[0], $penulisModules, true),
        ));

        // Super Admin: akses penuh lewat Gate::before, tanpa permission eksplisit.
        Role::findOrCreate('Super Admin', 'web');

        Role::findOrCreate('Admin', 'web')->syncPermissions($allPermissions);
        Role::findOrCreate('Operator', 'web')->syncPermissions($contentPermissions);
        Role::findOrCreate('Penulis', 'web')->syncPermissions($penulisPermissions);

        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@sekolahku-v2.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ],
        );

        $superAdmin->syncRoles(['Super Admin']);
    }
}
