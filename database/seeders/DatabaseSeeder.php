<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Catatan: tanpa WithoutModelEvents karena DemoContentSeeder
     * mengandalkan event model (slug otomatis via HasSlug).
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);
        $this->call(DemoContentSeeder::class);
        $this->call(WidgetSeeder::class);
        $this->call(PpdbSeeder::class);
    }
}
