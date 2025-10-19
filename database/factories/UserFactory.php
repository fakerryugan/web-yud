<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Core\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'username' => $this->faker->unique()->userName(),
            'nip' => $this->faker->unique()->numerify('##################'),
            'password' => bcrypt('password'),
            'fcm_token' => null,
            'status' => 1, // <-- TAMBAHKAN BARIS INI
            'remember_token' => Str::random(10),
        ];
    }
}