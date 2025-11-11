<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create HR Admin
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Vills',
            'middle_name' => 'HR',
            'email' => 'hradmin@villspms.com',
            'password' => Hash::make('password'),
            'role' => 'hrAdmin',
            'gender' => 'Male',
            'birth_date' => '1985-06-15',
            'age' => 39,
            'civil_status' => 'Married',
            'nationality' => 'Filipino',
            'religion' => 'Catholic',
            'mobile_number' => '09171234567',
            'province' => 'Laguna',
            'city' => 'Calamba City',
            'barangay' => 'Barangay Commonwealth',
            'street_details' => '123 Admin Street',
            'postal_code' => '1121',
            'full_address' => '123 Admin Street, Barangay 4, Calamba City, Laguna',
            'active_status' => 'Active',
            'email_verified_at' => now(),
        ]);

        // Create HR Staff
        User::create([
            'first_name' => 'Staff',
            'last_name' => 'Vills',
            'middle_name' => 'HR',
            'email' => 'hrstaff@villspms.com',
            'password' => Hash::make('password'),
            'role' => 'hrStaff',
            'gender' => 'Female',
            'birth_date' => '1990-03-20',
            'age' => 34,
            'civil_status' => 'Single',
            'nationality' => 'Filipino',
            'religion' => 'Catholic',
            'mobile_number' => '09181234567',
            'province' => 'Laguna',
            'city' => 'Calamba City',
            'barangay' => 'Barandal',
            'street_details' => '456 Staff Avenue',
            'postal_code' => '1126',
            'full_address' => '456 Staff Avenue, Barandal, Calamba City, Laguna',
            'active_status' => 'Active',
            'email_verified_at' => now(),
        ]);

        // Create Sample Employees
        $employeeNames = [
            ['Juan', 'Dela Cruz', 'Santos'],
            ['Maria', 'Garcia', 'Reyes'],
            ['Jose', 'Ramos', 'Mendoza'],
            ['Ana', 'Torres', 'Santos'],
            ['Pedro', 'Gonzales', 'Rivera'],
        ];

        foreach ($employeeNames as $index => $name) {
            $cities = ['Los Baños', 'Calamba', 'Cabuyao', 'San Pablo', 'Sta. Rosa'];
            $civilStatuses = ['Single', 'Married', 'Widowed'];
            $religions = ['Catholic', 'Christian', 'Islam', 'Buddhist'];

            User::create([
                'first_name' => $name[0],
                'last_name' => $name[1],
                'middle_name' => $name[2],
                'email' => strtolower($name[0]) . '.' . strtolower($name[1]) . '@villspms.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'gender' => in_array($name[0], ['Maria', 'Ana']) ? 'Female' : 'Male',
                'birth_date' => now()->subYears(rand(25, 50))->format('Y-m-d'),
                'age' => rand(25, 50),
                'civil_status' => $civilStatuses[array_rand($civilStatuses)],
                'nationality' => 'Filipino',
                'religion' => $religions[array_rand($religions)],
                'mobile_number' => '0917' . rand(1000000, 9999999),
                'province' => 'Laguna',
                'city' => $cities[array_rand($cities)],
                'barangay' => 'Barangay ' . rand(1, 5),
                'street_details' => rand(1, 999) . ' Street',
                'postal_code' => rand(1000, 1999),
                'full_address' => rand(1, 999) . ' Street, Barangay ' . rand(1, 5) . ', ' . $cities[array_rand($cities)] . ', Metro Manila',
                'active_status' => 'Active',
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('Created 2 admin users and 5 employee users');
    }
}
