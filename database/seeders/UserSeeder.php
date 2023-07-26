<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'QA One',
            'email' => 'one@qa.com',
            'password' => Hash::make('passQAone'),
        ]);

        DB::table('users')->insert([
            'name' => 'QA Two',
            'email' => 'two@qa.com',
            'password' => Hash::make('passQAtwo'),
        ]);
    }
}
