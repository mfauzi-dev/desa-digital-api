<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        DB::table('roles')->updateOrInsert(
            ['name' => 'admin'],
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('roles')->updateOrInsert(
            ['name' => 'head_of_family'],
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
