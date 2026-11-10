<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([['Rani Pratama', 'rani@connectspace.test'], ['Budi Santoso', 'budi@connectspace.test']] as [$name, $email]) {
            User::firstOrCreate(['email' => $email], [
                'name' => $name,
                'password' => Hash::make('password'),
            ]);
        }
    }
}
