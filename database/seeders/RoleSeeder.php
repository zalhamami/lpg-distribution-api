<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::truncate();
        $values = [\App\Role::USER, \App\Role::ADMIN, \App\Role::SUPPLIER, \App\Role::AGENT];
        for ($i = 0; $i < count($values); $i++) {
            Role::create([
                'name' => $values[$i],
                'guard_name' => 'web',
            ]);
        }
    }
}
