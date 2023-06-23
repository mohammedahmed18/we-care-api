<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email =  "mostafa123@email.com";
        $apiToken = Str::random(60);
        $isFound = User::where("email" , $email)->first();
        if($isFound) return;

        User::create([
            'name' => "Mostafa",
            'email' => $email,
            'password' => Hash::make("123456"),
            'api_token' => $apiToken,
        ]);
    }
}
