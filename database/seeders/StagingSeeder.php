<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class StagingSeeder extends Seeder
{
    public function run(): void
    {
        if (! User::count()) {
            return;
        }

        User::create([
            'name' => 'Admin',
            'email' => config('penny.admin.default_email'),
            'password' => config('penny.admin.default_password'),
        ]);
    }
}
