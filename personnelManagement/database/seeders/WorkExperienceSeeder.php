<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\WorkExperience;
use App\Models\Application;
use Carbon\Carbon;

class WorkExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define job titles mapped to industries (relevant work experience)
        $industryExperiences = [
            'Construction' => [
                'Construction Worker', 'Carpenter', 'Mason', 'Foreman', 'Heavy Equipment Operator',
                'Site Engineer', 'Construction Laborer', 'Welder', 'Painter', 'Scaffolder'
            ],
            'Healthcare' => [
                'Nursing Aide', 'Caregiver', 'Medical Assistant', 'Health Worker', 'Patient Care Technician',
                'Nurse', 'Hospital Attendant', 'Clinical Assistant', 'Ward Clerk', 'Emergency Room Aide'
            ],
            'Logistics' => [
                'Warehouse Supervisor', 'Inventory Clerk', 'Logistics Coordinator', 'Supply Chain Assistant',
                'Warehouse Staff', 'Dispatch Coordinator', 'Delivery Driver', 'Stock Controller', 'Forklift Operator'
            ],
            'Hospitality' => [
                'Restaurant Server', 'Waiter/Waitress', 'Food Service Crew', 'Bartender', 'Kitchen Staff',
                'Cashier', 'Host/Hostess', 'Busser', 'Barista', 'Restaurant Supervisor'
            ],
            'BPO' => [
                'Call Center Agent', 'Customer Service Representative', 'Technical Support Representative',
                'Team Leader', 'Quality Analyst', 'Sales Agent', 'Chat Support Specialist', 'Email Support Agent'
            ],
            'Technical Services' => [
                'Electrician', 'Technician', 'Maintenance Worker', 'HVAC Technician', 'Plumber',
                'Electronics Technician', 'Mechanic', 'Facilities Technician', 'Service Technician'
            ],
            'Security Services' => [
                'Security Guard', 'Security Officer', 'Security Supervisor', 'CCTV Operator', 'Bouncer',
                'Security Escort', 'Access Control Officer', 'Night Watch', 'Security Inspector'
            ],
            'Retail' => [
                'Sales Associate', 'Sales Clerk', 'Cashier', 'Store Supervisor', 'Visual Merchandiser',
                'Sales Representative', 'Customer Service Associate', 'Inventory Associate', 'Store Manager'
            ],
            'Manufacturing' => [
                'Machine Operator', 'Production Worker', 'Assembly Line Worker', 'Quality Inspector',
                'Production Supervisor', 'Factory Worker', 'Packaging Specialist', 'Maintenance Technician'
            ],
            'Administrative' => [
                'Administrative Assistant', 'Office Clerk', 'Receptionist', 'Data Entry Clerk', 'Executive Assistant',
                'Secretary', 'Office Coordinator', 'Administrative Officer', 'Document Controller'
            ],
            'Information Technology' => [
                'IT Support Specialist', 'System Administrator', 'Web Developer', 'Software Developer',
                'Network Engineer', 'Database Administrator', 'IT Technician', 'Help Desk Technician'
            ],
            'Human Resources' => [
                'HR Assistant', 'Recruiter', 'HR Coordinator', 'Compensation Analyst', 'Benefits Administrator',
                'HR Specialist', 'Training Coordinator', 'Talent Acquisition Specialist'
            ],
        ];

        // Define sample companies per industry
        $industryCompanies = [
            'Construction' => ['BuildCorp Construction', 'Premier Builders Inc.', 'Metro Construction Group', 'Elite Contractors', 'Phoenix Construction'],
            'Healthcare' => ['MediCare Hospital', 'St. Mary\'s Medical Center', 'HealthPlus Clinic', 'City General Hospital', 'Community Health Services'],
            'Logistics' => ['Global Logistics Inc.', 'FastTrack Shipping', 'Metro Warehousing', 'TransCo Logistics', 'Supply Chain Solutions'],
            'Hospitality' => ['Fine Dining Restaurant', 'Grand Hotel Manila', 'Cafe Express', 'The Food Hub', 'Luxury Resort & Spa'],
            'BPO' => ['TechSupport Solutions', 'GlobalConnect BPO', 'Customer Care Center', 'Digital Services Corp', 'Elite Outsourcing'],
            'Technical Services' => ['PowerTech Services', 'TechFix Solutions', 'Metro Electrical Services', 'Prime Maintenance Corp', 'Technical Experts Inc.'],
            'Security Services' => ['Elite Security Agency', 'Guardian Security Services', 'ProSec Solutions', 'SafeGuard Corp', 'Metro Security Group'],
            'Retail' => ['Retail Store Chain', 'MegaMall Department Store', 'Fashion Outlet', 'SuperMart', 'The Shopping Center'],
            'Manufacturing' => ['Manufacturing Corp', 'Industrial Products Inc.', 'Metro Manufacturing', 'Prime Industries', 'Precision Manufacturing'],
            'Administrative' => ['Corporate Services Inc.', 'Business Solutions Corp', 'Metro Admin Services', 'Professional Services Inc.', 'Corporate Partners'],
            'Information Technology' => ['Tech Innovators Inc.', 'Digital Solutions Corp', 'IT Services Company', 'Software Development Inc.', 'TechHub Solutions'],
            'Human Resources' => ['HR Solutions Inc.', 'Talent Management Corp', 'People First Inc.', 'HR Consulting Group', 'Workforce Solutions'],
        ];

        // Get all users (applicants, employees, and HR staff)
        $users = User::with('applications.job')->get();

        $createdCount = 0;

        foreach ($users as $user) {
            // Determine industry based on user's applications or job_industry preference
            $targetIndustry = null;

            // If user has applications, use the industry of their first application
            if ($user->applications->count() > 0) {
                $firstApplication = $user->applications->first();
                $targetIndustry = $firstApplication->job->job_industry ?? $user->job_industry;
            } else {
                // Otherwise, use their job_industry preference
                $targetIndustry = $user->job_industry;
            }

            // Default to 'Administrative' if no industry found
            if (!$targetIndustry || !isset($industryExperiences[$targetIndustry])) {
                $targetIndustry = 'Administrative';
            }

            // Create 1-3 work experiences per user
            $numberOfExperiences = rand(1, 3);

            for ($i = 0; $i < $numberOfExperiences; $i++) {
                // Pick a random job title from the target industry
                $jobTitle = $industryExperiences[$targetIndustry][array_rand($industryExperiences[$targetIndustry])];

                // Pick a random company from the target industry
                $companyName = $industryCompanies[$targetIndustry][array_rand($industryCompanies[$targetIndustry])];

                // Generate realistic date ranges
                // Most recent experience: ended 0-12 months ago or still working
                // Older experiences: ended 1-5 years ago
                $yearsBack = $i + 1; // First exp = 1 year, second = 2 years, etc.

                $endDate = null;
                $startDate = Carbon::now()->subYears($yearsBack)->subMonths(rand(0, 11));

                // 30% chance of still working at most recent job (i == 0)
                if ($i === 0 && rand(1, 100) <= 30) {
                    $endDate = null; // Still working
                } else {
                    // Calculate duration (6 months to 3 years)
                    $durationMonths = rand(6, 36);
                    $endDate = (clone $startDate)->addMonths($durationMonths);

                    // Ensure end date is not in the future
                    if ($endDate->isFuture()) {
                        $endDate = Carbon::now()->subMonths(rand(1, 6));
                    }
                }

                WorkExperience::create([
                    'user_id' => $user->id,
                    'job_title' => $jobTitle,
                    'company_name' => $companyName,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate ? $endDate->format('Y-m-d') : null,
                ]);

                $createdCount++;
            }
        }

        $this->command->info("Created {$createdCount} work experience entries for " . $users->count() . " users");
    }
}
