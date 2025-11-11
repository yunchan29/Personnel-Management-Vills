<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Support\Facades\Hash;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = Job::all();

        if ($jobs->isEmpty()) {
            $this->command->warn('No jobs found. Please run JobSeeder first.');
            return;
        }

        $applicantNames = [
            ['Carlo', 'Santos', 'Miguel'],
            ['Liza', 'Reyes', 'Cruz'],
            ['Roberto', 'Fernandez', 'Lopez'],
            ['Carla', 'Mendoza', 'Garcia'],
            ['Miguel', 'Rivera', 'Santos'],
            ['Sofia', 'Castro', 'Torres'],
            ['Ramon', 'Gonzales', 'Perez'],
            ['Isabel', 'Diaz', 'Martinez'],
            ['Ricardo', 'Morales', 'Hernandez'],
            ['Teresa', 'Ramos', 'Gutierrez'],
            ['Eduardo', 'Navarro', 'Jimenez'],
            ['Patricia', 'Flores', 'Ruiz'],
            ['Fernando', 'Vargas', 'Alvarez'],
            ['Carmen', 'Romero', 'Gomez'],
            ['Antonio', 'Silva', 'Ortiz'],
        ];

        $statuses = ['pending', 'to_review', 'approved', 'for_interview', 'interviewed', 'scheduled_for_training'];
        $cities = ['Calamba', 'Cabuyao', 'Santa Rosa', 'Los Baños', 'Cavinti', 'Pila'];
        $civilStatuses = ['Single', 'Married', 'Widowed'];
        $religions = ['Catholic', 'Christian', 'Islam', 'INC'];

        foreach ($applicantNames as $index => $name) {
            // Create applicant user
            $applicant = User::create([
                'first_name' => $name[0],
                'last_name' => $name[1],
                'middle_name' => $name[2],
                'email' => strtolower($name[0]) . '.' . strtolower($name[1]) . $index . '@applicant.com',
                'password' => Hash::make('password'),
                'role' => 'applicant',
                'gender' => in_array($name[0], ['Liza', 'Carla', 'Sofia', 'Isabel', 'Teresa', 'Patricia', 'Carmen']) ? 'Female' : 'Male',
                'birth_place' => 'Calamba City',
                'birth_date' => now()->subYears(rand(21, 45))->format('Y-m-d'),
                'age' => rand(21, 45),
                'civil_status' => $civilStatuses[array_rand($civilStatuses)],
                'nationality' => 'Filipino',
                'religion' => $religions[array_rand($religions)],
                'mobile_number' => '0917' . rand(1000000, 9999999),
                'province' => 'Laguna',
                'city' => $cities[array_rand($cities)],
                'barangay' => 'Barangay ' . rand(1, 6),
                'street_details' => rand(1, 999) . ' Street',
                'postal_code' => rand(1000, 1999),
                'full_address' => rand(1, 999) . ' Street, Barangay ' . rand(1, 6) . ', ' . $cities[array_rand($cities)] . ', Metro Manila',
                'active_status' => 'Active',
                'email_verified_at' => now(),
            ]);

            // Create 1-2 applications per applicant
            $numberOfApplications = rand(1, 2);

            for ($i = 0; $i < $numberOfApplications; $i++) {
                $job = $jobs->random();
                $status = $statuses[array_rand($statuses)];

                $application = Application::create([
                    'user_id' => $applicant->id,
                    'job_id' => $job->id,
                    'resume_snapshot' => 'resumes/sample_resume_' . $applicant->id . '.pdf',
                    'licenses' => json_encode([
                        'Driver\'s License' => 'licenses/drivers_' . $applicant->id . '.pdf',
                    ]),
                    'sss_number' => rand(10, 99) . '-' . rand(1000000, 9999999) . '-' . rand(1, 9),
                    'philhealth_number' => rand(10, 99) . '-' . rand(100000000, 999999999) . '-' . rand(1, 9),
                    'tin_id_number' => rand(100, 999) . '-' . rand(100, 999) . '-' . rand(100, 999) . '-' . rand(100, 999),
                    'pagibig_number' => rand(1000, 9999) . '-' . rand(1000, 9999) . '-' . rand(1000, 9999),
                    'status' => $status,
                    'reviewed_at' => in_array($status, ['approved', 'for_interview', 'interviewed', 'scheduled_for_training']) ? now()->subDays(rand(1, 10)) : null,
                    'is_archived' => false,
                ]);

                // If status is for_interview or interviewed, create an interview
                if (in_array($status, ['for_interview', 'interviewed'])) {
                    \App\Models\Interview::create([
                        'application_id' => $application->id,
                        'user_id' => $applicant->id,
                        'scheduled_by' => 1, // HR Admin
                        'scheduled_at' => now()->addDays(rand(1, 7)),
                        'status' => $status === 'interviewed' ? 'completed' : 'scheduled',
                        'remarks' => $status === 'interviewed' ? 'Applicant performed well during interview' : null,
                    ]);
                }

                // If status is scheduled_for_training, create training schedule
                if ($status === 'scheduled_for_training') {
                    \App\Models\TrainingSchedule::create([
                        'application_id' => $application->id,
                        'user_id' => $applicant->id,
                        'scheduled_by' => 2, // HR Staff
                        'scheduled_at' => now()->subDays(rand(1, 5)),
                        'start_date' => now()->addDays(rand(1, 14)),
                        'end_date' => now()->addDays(rand(15, 30)),
                        'start_time' => '08:00:00',
                        'end_time' => '17:00:00',
                        'location' => 'Training Center, Quezon City',
                        'status' => 'scheduled',
                        'remarks' => 'Initial training for new hires',
                    ]);
                }
            }
        }

        $this->command->info('Created 15 applicants with ' . Application::count() . ' applications');
    }
}
