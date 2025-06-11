<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
public function run(): void
{
    $this->call([
        \Modules\Auth\Database\Seeders\DocumentSeeder::class,
        \Modules\Auth\Database\Seeders\SignatureSeeder::class,
    ]);
}

}
