<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create HR Admin
        $adminProfilePic = SeederHelper::generateProfilePicture('Admin', 'Vills', 'admin_vills.png');
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Vills',
            'middle_name' => 'HR',
            'email' => 'hradmin@villspms.com',
            'password' => Hash::make('Password123!'),
            'role' => 'hrAdmin',
            'gender' => 'Male',
            'birth_date' => '1985-06-15',
            'age' => 39,
            'civil_status' => 'Married',
            'nationality' => 'Filipino',
            'religion' => 'Catholic',
            'mobile_number' => '09171234567',
            'province' => 'Laguna',
            'city' => 'City of Calamba',
            'barangay' => 'Baclaran',
            'street_details' => '123 Admin Street',
            'postal_code' => '4027',
            'full_address' => '123 Admin Street, Baclaran, City of Calamba, Laguna 4027',
            'profile_picture' => $adminProfilePic,
            'job_industry' => 'Human Resources',
            'active_status' => 'Active',
            'email_verified_at' => now(),
        ]);

        // Create HR Staff
        $staffProfilePic = SeederHelper::generateProfilePicture('Staff', 'Vills', 'staff_vills.png');
        User::create([
            'first_name' => 'Staff',
            'last_name' => 'Vills',
            'middle_name' => 'HR',
            'email' => 'hrstaff@villspms.com',
            'password' => Hash::make('Password123!'),
            'role' => 'hrStaff',
            'gender' => 'Female',
            'birth_date' => '1990-03-20',
            'age' => 34,
            'civil_status' => 'Single',
            'nationality' => 'Filipino',
            'religion' => 'Catholic',
            'mobile_number' => '09181234567',
            'province' => 'Laguna',
            'city' => 'City of Calamba',
            'barangay' => 'Banlic',
            'street_details' => '456 Staff Avenue',
            'postal_code' => '4027',
            'full_address' => '456 Staff Avenue, Banlic, City of Calamba, Laguna 4027',
            'profile_picture' => $staffProfilePic,
            'job_industry' => 'Human Resources',
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

        // Define cities with their barangays and postal codes
        $laguna_locations = [
            ['city' => 'Los Baños', 'barangays' => ['Balian', 'Dambo', 'Galalan', 'Isla (Pob.)', 'Mabato-Azufre', 'San Jose (Pob.)'], 'postal' => '4030'],
            ['city' => 'City of Calamba', 'barangays' => ['Baclaran', 'Banaybanay', 'Banlic', 'Butong', 'Bigaa', 'Casile', 'Gulod', 'Mamatid'], 'postal' => '4027'],
            ['city' => 'City of Cabuyao', 'barangays' => ['Baclaran', 'Banaybanay', 'Banlic', 'Butong', 'Bigaa', 'Casile'], 'postal' => '4025'],
            ['city' => 'City of San Pablo', 'barangays' => ['San Antonio 1 (Pob.)', 'San Buenaventura (Pob.)', 'San Cristobal (Pob.)', 'San Diego (Pob.)'], 'postal' => '4000'],
            ['city' => 'City of Santa Rosa', 'barangays' => ['Aplaya', 'Balibago', 'Caingin', 'Dila', 'Labas', 'Macabling', 'Malitlit'], 'postal' => '4026'],
        ];

        foreach ($employeeNames as $index => $name) {
            $civilStatuses = ['Single', 'Married', 'Widowed'];
            $religions = ['Catholic', 'Christian', 'Islam', 'Buddhist'];
            $jobIndustries = SeederHelper::getJobIndustries();

            // Pick a random location
            $location = $laguna_locations[array_rand($laguna_locations)];
            $barangay = $location['barangays'][array_rand($location['barangays'])];
            $street = rand(1, 999) . ' Street';

            $profilePic = SeederHelper::generateProfilePicture($name[0], $name[1], strtolower($name[0]) . '_' . strtolower($name[1]) . '.png');
            User::create([
                'first_name' => $name[0],
                'last_name' => $name[1],
                'middle_name' => $name[2],
                'email' => strtolower($name[0]) . '.' . strtolower($name[1]) . '@villspms.com',
                'password' => Hash::make('Password123!'),
                'role' => 'employee',
                'gender' => in_array($name[0], ['Maria', 'Ana']) ? 'Female' : 'Male',
                'birth_date' => now()->subYears(rand(25, 50))->format('Y-m-d'),
                'age' => rand(25, 50),
                'civil_status' => $civilStatuses[array_rand($civilStatuses)],
                'nationality' => 'Filipino',
                'religion' => $religions[array_rand($religions)],
                'mobile_number' => '0917' . rand(1000000, 9999999),
                'province' => 'Laguna',
                'city' => $location['city'],
                'barangay' => $barangay,
                'street_details' => $street,
                'postal_code' => $location['postal'],
                'full_address' => "{$street}, {$barangay}, {$location['city']}, Laguna {$location['postal']}",
                'profile_picture' => $profilePic,
                'job_industry' => $jobIndustries[array_rand($jobIndustries)],
                'active_status' => 'Active',
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('Created 2 admin users and 5 employee users');
    }
}
