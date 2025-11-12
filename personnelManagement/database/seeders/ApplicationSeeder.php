<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Job;
use App\Models\Application;
use App\Models\Resume;
use App\Models\File201;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        $civilStatuses = ['Single', 'Married', 'Widowed'];
        $religions = ['Catholic', 'Christian', 'Islam', 'INC'];

        // Define cities with their barangays and postal codes
        $laguna_locations = [
            ['city' => 'Los Baños', 'barangays' => ['Balian', 'Dambo', 'Galalan', 'Isla (Pob.)', 'Mabato-Azufre', 'San Jose (Pob.)'], 'postal' => '4030'],
            ['city' => 'City of Calamba', 'barangays' => ['Baclaran', 'Banaybanay', 'Banlic', 'Butong', 'Bigaa', 'Gulod', 'Mamatid'], 'postal' => '4027'],
            ['city' => 'City of Cabuyao', 'barangays' => ['Baclaran', 'Banaybanay', 'Banlic', 'Butong', 'Bigaa', 'Casile'], 'postal' => '4025'],
            ['city' => 'City of San Pablo', 'barangays' => ['San Antonio 1 (Pob.)', 'San Buenaventura (Pob.)', 'San Cristobal (Pob.)', 'San Diego (Pob.)'], 'postal' => '4000'],
            ['city' => 'City of Santa Rosa', 'barangays' => ['Aplaya', 'Balibago', 'Caingin', 'Dila', 'Labas', 'Macabling', 'Malitlit'], 'postal' => '4026'],
            ['city' => 'Cavinti', 'barangays' => ['Anglas', 'Bangco', 'Bukal', 'Cansuso', 'Duhat', 'Inao-Awan', 'Kanluran Talaongan'], 'postal' => '4033'],
            ['city' => 'Pila', 'barangays' => ['Aplaya', 'Bagong Pook', 'Bukal', 'Bulilan Norte (Pob.)', 'Bulilan Sur (Pob.)', 'Concepcion'], 'postal' => '4010'],
        ];

        foreach ($applicantNames as $index => $name) {
            // Pick a random location
            $location = $laguna_locations[array_rand($laguna_locations)];
            $barangay = $location['barangays'][array_rand($location['barangays'])];
            $street = rand(1, 999) . ' Street';

            // Get job industries
            $jobIndustries = SeederHelper::getJobIndustries();

            // Generate profile picture
            $profilePic = SeederHelper::generateProfilePicture($name[0], $name[1], strtolower($name[0]) . '_' . strtolower($name[1]) . '_' . $index . '.png');

            // Create applicant user
            $applicant = User::create([
                'first_name' => $name[0],
                'last_name' => $name[1],
                'middle_name' => $name[2],
                'email' => strtolower($name[0]) . '.' . strtolower($name[1]) . $index . '@applicant.com',
                'password' => Hash::make('Password123!'),
                'role' => 'applicant',
                'gender' => in_array($name[0], ['Liza', 'Carla', 'Sofia', 'Isabel', 'Teresa', 'Patricia', 'Carmen']) ? 'Female' : 'Male',
                'birth_place' => 'City of Calamba',
                'birth_date' => now()->subYears(rand(21, 45))->format('Y-m-d'),
                'age' => rand(21, 45),
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

            // Generate main resume PDF for the applicant (stored in resumes table)
            $mainResumeFilename = 'resume_' . $applicant->id . '_' . time() . '.pdf';
            $mainResumePath = SeederHelper::generateResumePDF([
                'first_name' => $applicant->first_name,
                'middle_name' => $applicant->middle_name,
                'last_name' => $applicant->last_name,
                'email' => $applicant->email,
                'mobile_number' => $applicant->mobile_number,
                'full_address' => $applicant->full_address,
                'birth_date' => $applicant->birth_date,
                'gender' => $applicant->gender,
                'civil_status' => $applicant->civil_status,
                'nationality' => $applicant->nationality,
            ], $mainResumeFilename);

            // Create resume entry in resumes table
            Resume::create([
                'user_id' => $applicant->id,
                'resume' => $mainResumePath,
            ]);

            // Generate sample license PDFs for File201 (1-2 licenses per applicant)
            $numberOfLicenses = rand(1, 2);
            $licenseTypes = ['CISCO', 'Driver\'s License', 'Professional License', 'Security License', 'Food Handler'];
            $file201Licenses = [];

            for ($j = 0; $j < $numberOfLicenses; $j++) {
                $licenseName = $licenseTypes[array_rand($licenseTypes)];
                $licenseNumber = str_pad(rand(0, 99999999999999), 14, '0', STR_PAD_LEFT); // 14 digit number
                $licenseDate = now()->subYears(rand(1, 5))->format('Y-m-d');

                $file201Licenses[] = [
                    'name' => $licenseName,
                    'number' => $licenseNumber,
                    'date' => $licenseDate,
                ];
            }

            // Add empty slot if only 1 license
            if ($numberOfLicenses === 1) {
                $file201Licenses[] = [
                    'name' => null,
                    'number' => null,
                    'date' => null,
                ];
            }

            // Create File201 entry with government IDs and licenses
            File201::create([
                'user_id' => $applicant->id,
                'sss_number' => str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT), // 9 digits
                'philhealth_number' => str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT), // 12 digits
                'tin_id_number' => str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT), // 12 digits
                'pagibig_number' => str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT), // 12 digits
                'licenses' => $file201Licenses,
            ]);

            // Create 1-2 applications per applicant
            $numberOfApplications = rand(1, 2);
            $appliedJobIds = []; // Track jobs this applicant has applied to

            for ($i = 0; $i < $numberOfApplications; $i++) {
                // Ensure unique job per applicant (respect unique constraint)
                $availableJobs = $jobs->whereNotIn('id', $appliedJobIds);

                if ($availableJobs->isEmpty()) {
                    // No more unique jobs available for this applicant
                    break;
                }

                $job = $availableJobs->random();
                $appliedJobIds[] = $job->id; // Mark this job as applied
                $status = $statuses[array_rand($statuses)];

                // Generate resume PDF
                $resumeFilename = 'resume_' . $applicant->id . '_' . time() . '_' . $i . '.pdf';
                $resumePath = SeederHelper::generateResumePDF([
                    'first_name' => $applicant->first_name,
                    'middle_name' => $applicant->middle_name,
                    'last_name' => $applicant->last_name,
                    'email' => $applicant->email,
                    'mobile_number' => $applicant->mobile_number,
                    'full_address' => $applicant->full_address,
                    'birth_date' => $applicant->birth_date,
                    'gender' => $applicant->gender,
                    'civil_status' => $applicant->civil_status,
                    'nationality' => $applicant->nationality,
                ], $resumeFilename);

                // Generate driver's license PDF (similar to resume but for license)
                $licenseFilename = 'license_drivers_' . $applicant->id . '_' . time() . '_' . $i . '.pdf';
                $licensePath = SeederHelper::generateResumePDF([
                    'first_name' => $applicant->first_name,
                    'middle_name' => $applicant->middle_name,
                    'last_name' => $applicant->last_name,
                    'email' => $applicant->email,
                    'mobile_number' => $applicant->mobile_number,
                    'full_address' => $applicant->full_address,
                    'birth_date' => $applicant->birth_date,
                    'gender' => $applicant->gender,
                    'civil_status' => $applicant->civil_status,
                    'nationality' => $applicant->nationality,
                ], $licenseFilename);

                $application = Application::create([
                    'user_id' => $applicant->id,
                    'job_id' => $job->id,
                    'resume_snapshot' => $resumePath,
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

        // Create 5 employees (applicants who completed the full workflow and were hired)
        $employeeNames = [
            ['Juan', 'Dela Cruz', 'Santos'],
            ['Maria', 'Garcia', 'Reyes'],
            ['Jose', 'Ramos', 'Mendoza'],
            ['Ana', 'Torres', 'Santos'],
            ['Pedro', 'Gonzales', 'Rivera'],
        ];

        $employeeCount = 0;
        foreach ($employeeNames as $index => $name) {
            // Pick a random location
            $location = $laguna_locations[array_rand($laguna_locations)];
            $barangay = $location['barangays'][array_rand($location['barangays'])];
            $street = rand(1, 999) . ' Street';

            // Get job industries
            $jobIndustries = SeederHelper::getJobIndustries();

            // Generate profile picture
            $profilePic = SeederHelper::generateProfilePicture($name[0], $name[1], 'employee_' . strtolower($name[0]) . '_' . strtolower($name[1]) . '.png');

            // Create employee user (promoted from applicant)
            $employee = User::create([
                'first_name' => $name[0],
                'last_name' => $name[1],
                'middle_name' => $name[2],
                'email' => strtolower($name[0]) . '.' . strtolower($name[1]) . '@villspms.com',
                'password' => Hash::make('Password123!'),
                'role' => 'employee', // Already promoted
                'gender' => in_array($name[0], ['Maria', 'Ana']) ? 'Female' : 'Male',
                'birth_place' => 'City of Calamba',
                'birth_date' => now()->subYears(rand(25, 45))->format('Y-m-d'),
                'age' => rand(25, 45),
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

            // Generate resume PDF
            $mainResumeFilename = 'resume_employee_' . $employee->id . '_' . time() . '.pdf';
            $mainResumePath = SeederHelper::generateResumePDF([
                'first_name' => $employee->first_name,
                'middle_name' => $employee->middle_name,
                'last_name' => $employee->last_name,
                'email' => $employee->email,
                'mobile_number' => $employee->mobile_number,
                'full_address' => $employee->full_address,
                'birth_date' => $employee->birth_date,
                'gender' => $employee->gender,
                'civil_status' => $employee->civil_status,
                'nationality' => $employee->nationality,
            ], $mainResumeFilename);

            // Create resume entry
            Resume::create([
                'user_id' => $employee->id,
                'resume' => $mainResumePath,
            ]);

            // Create File201 entry
            File201::create([
                'user_id' => $employee->id,
                'sss_number' => str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                'philhealth_number' => str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT),
                'tin_id_number' => str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT),
                'pagibig_number' => str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT),
                'licenses' => [
                    ['name' => 'Driver\'s License', 'number' => str_pad(rand(0, 99999999999999), 14, '0', STR_PAD_LEFT), 'date' => now()->subYears(rand(1, 3))->format('Y-m-d')],
                    ['name' => null, 'number' => null, 'date' => null],
                ],
            ]);

            // Pick a random job for this employee
            $job = $jobs->random();

            // Assign job_id to employee
            $employee->job_id = $job->id;
            $employee->save();

            // Generate resume snapshot for application
            $resumeFilename = 'resume_app_' . $employee->id . '_' . time() . '.pdf';
            $resumePath = SeederHelper::generateResumePDF([
                'first_name' => $employee->first_name,
                'middle_name' => $employee->middle_name,
                'last_name' => $employee->last_name,
                'email' => $employee->email,
                'mobile_number' => $employee->mobile_number,
                'full_address' => $employee->full_address,
                'birth_date' => $employee->birth_date,
                'gender' => $employee->gender,
                'civil_status' => $employee->civil_status,
                'nationality' => $employee->nationality,
            ], $resumeFilename);

            // Contract dates
            $contractStart = now()->subMonths(rand(1, 6));
            $contractEnd = $contractStart->copy()->addMonths(rand(6, 12));

            // Create application with hired status and contract details
            $application = Application::create([
                'user_id' => $employee->id,
                'job_id' => $job->id,
                'resume_snapshot' => $resumePath,
                'status' => 'hired',
                'reviewed_at' => now()->subDays(rand(30, 90)),
                'is_archived' => false,
                'contract_signing_schedule' => now()->subDays(rand(15, 60)),
                'contract_start' => $contractStart->format('Y-m-d'),
                'contract_end' => $contractEnd->format('Y-m-d'),
            ]);

            // Create interview record
            \App\Models\Interview::create([
                'application_id' => $application->id,
                'user_id' => $employee->id,
                'scheduled_by' => 1, // HR Admin
                'scheduled_at' => now()->subDays(rand(90, 120)),
                'status' => 'completed',
                'remarks' => 'Excellent performance during interview. Highly recommended for hiring.',
            ]);

            // Create training schedule (completed)
            \App\Models\TrainingSchedule::create([
                'application_id' => $application->id,
                'user_id' => $employee->id,
                'scheduled_by' => 2, // HR Staff
                'scheduled_at' => now()->subDays(rand(70, 100)),
                'start_date' => now()->subDays(rand(60, 80)),
                'end_date' => now()->subDays(rand(45, 60)),
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'location' => 'Training Center, Quezon City',
                'status' => 'completed',
                'remarks' => 'Successfully completed training program',
            ]);

            // Create passed evaluation
            $knowledge = rand(18, 25);
            $skill = rand(18, 25);
            $participation = rand(18, 25);
            $professionalism = rand(18, 25);
            $totalScore = $knowledge + $skill + $participation + $professionalism;

            \App\Models\TrainingEvaluation::create([
                'application_id' => $application->id,
                'evaluated_by' => 2, // HR Staff
                'knowledge' => $knowledge,
                'skill' => $skill,
                'participation' => $participation,
                'professionalism' => $professionalism,
                'total_score' => $totalScore,
                'result' => 'Passed',
                'evaluated_at' => now()->subDays(rand(30, 45)),
            ]);

            $employeeCount++;
        }

        $this->command->info('Created 15 applicants with various statuses and ' . $employeeCount . ' employees (promoted from applicants)');
        $this->command->info('Total resumes: ' . Resume::count() . ', File201s: ' . File201::count() . ', Applications: ' . Application::count());
    }
}
