<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Maya Chen',
            'email' => 'test@example.com',
            'role' => UserRole::Client,
        ]);

        User::factory()->admin()->create([
            'name' => 'Apex Admin',
            'email' => 'admin@apexdrive.test',
        ]);

        $this->call(RentalCatalogSeeder::class);
    }
}
