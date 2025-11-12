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

        $this->command->info('Created 2 admin users (HR Admin and HR Staff)');
    }
}
