<?php

namespace Database\Seeders;

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
        $this->call([
            RolesAndPermissionsSeeder::class, // also seeds IdentityAccessSeeder
            LocaleSeeder::class,
            SettingsSeeder::class,
            StructureActivitiesSeeder::class,
            StranglerSeeder::class,
            ContentSeeder::class,
            NewsSeeder::class,
            DocumentSeeder::class,
            ForumSeeder::class,
            HomeSeeder::class,
        ]);
    }
}
