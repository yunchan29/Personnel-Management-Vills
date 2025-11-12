<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');
        $this->command->newLine();

        // Order matters! Jobs must be created before applications
        // Work experiences are created after applications to reflect job-related experience
        $this->call([
            UserSeeder::class,
            JobSeeder::class,
            ApplicationSeeder::class,
            WorkExperienceSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->info('📧 Login credentials:');
        $this->command->info('   HR Admin: hradmin@villspms.com / Password123!');
        $this->command->info('   HR Staff: hrstaff@villspms.com / Password123!');
        $this->command->info('   Employees: juan.delacruz@villspms.com / Password123! (and 4 more)');
        $this->command->info('   Applicants: carlo.santos0@applicant.com / Password123! (and 14 more)');
    }
}
