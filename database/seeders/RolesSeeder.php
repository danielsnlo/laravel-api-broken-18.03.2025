<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Moderator']);
        Role::create(['name' => 'Guest']);
        Role::create(['name' => 'Editor']);
    }
}
