<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['teacher', 'school_manager', 'school_recruiter', 'platform_admin'] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }
}
