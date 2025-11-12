<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Job;
use Carbon\Carbon;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = [
            [
                'job_title' => 'Construction Worker',
                'company_name' => 'BuildCorp Construction',
                'job_industry' => 'Construction',
                'location' => 'Quezon City, Metro Manila',
                'vacancies' => 15,
                'apply_until' => Carbon::now()->addDays(30)->format('Y-m-d'),
                'status' => 'active',
                'qualifications' => [
                    'At least 18 years old',
                    'High school graduate or equivalent',
                    'Physically fit and able to perform manual labor',
                    'Experience in construction preferred but not required',
                    'Willing to work overtime and weekends',
                ],
                'additional_info' => [
                    'Free transportation to work site',
                    'Complete safety equipment provided',
                    'SSS, PhilHealth, and Pag-IBIG benefits',
                    'Performance bonuses available',
                ],
            ],
            [
                'job_title' => 'Nursing Aide',
                'company_name' => 'MediCare Hospital',
                'job_industry' => 'Healthcare',
                'location' => 'Makati City, Metro Manila',
                'vacancies' => 8,
                'apply_until' => Carbon::now()->addDays(20)->format('Y-m-d'),
                'status' => 'active',
                'qualifications' => [
                    'Nursing Aide NC II Certificate',
                    'At least 1 year experience in hospital setting',
                    'Good communication skills',
                    'Compassionate and patient-oriented',
                    'Willing to work on shifting schedule',
                ],
                'additional_info' => [
                    'Competitive salary package',
                    'Health insurance coverage',
                    'Training and development opportunities',
                    'Career advancement programs',
                ],
            ],
            [
                'job_title' => 'Warehouse Supervisor',
                'company_name' => 'Global Logistics Inc.',
                'job_industry' => 'Logistics',
                'location' => 'Pasig City, Metro Manila',
                'vacancies' => 3,
                'apply_until' => Carbon::now()->addDays(15)->format('Y-m-d'),
                'status' => 'active',
                'qualifications' => [
                    'College degree in Business or related field',
                    'Minimum 3 years supervisory experience',
                    'Knowledge of warehouse management systems',
                    'Strong leadership and organizational skills',
                    'Proficient in MS Office applications',
                ],
                'additional_info' => [
                    'HMO coverage for employee and dependents',
                    'Annual performance incentives',
                    '13th month pay and bonuses',
                    'Company-provided transportation',
                ],
            ],
            [
                'job_title' => 'Restaurant Server',
                'company_name' => 'Fine Dining Restaurant',
                'job_industry' => 'Hospitality',
                'location' => 'Taguig City, Metro Manila',
                'vacancies' => 10,
                'apply_until' => Carbon::now()->addDays(25)->format('Y-m-d'),
                'status' => 'active',
                'qualifications' => [
                    'High school graduate',
                    'At least 6 months experience in food service',
                    'Excellent customer service skills',
                    'Neat and professional appearance',
                    'Willing to work on weekends and holidays',
                ],
                'additional_info' => [
                    'Service charge incentives',
                    'Free meals during shift',
                    'Tips and gratuities',
                    'Employee discount privileges',
                ],
            ],
            [
                'job_title' => 'Call Center Agent',
                'company_name' => 'TechSupport Solutions',
                'job_industry' => 'BPO',
                'location' => 'Quezon City, Metro Manila',
                'vacancies' => 20,
                'apply_until' => Carbon::now()->addDays(40)->format('Y-m-d'),
                'status' => 'active',
                'qualifications' => [
                    'College level or graduate',
                    'Excellent English communication skills',
                    'Basic computer navigation skills',
                    'Customer service oriented',
                    'Willing to work on shifting schedules',
                ],
                'additional_info' => [
                    'Night differential pay',
                    'HMO on Day 1',
                    'Performance-based incentives',
                    'Free shuttle service',
                    'Work-life balance programs',
                ],
            ],
            [
                'job_title' => 'Electrician',
                'company_name' => 'PowerTech Services',
                'job_industry' => 'Technical Services',
                'location' => 'Manila City, Metro Manila',
                'vacancies' => 5,
                'apply_until' => Carbon::now()->addDays(18)->format('Y-m-d'),
                'status' => 'active',
                'qualifications' => [
                    'NC II or NC III Electrical Installation Certification',
                    'Minimum 2 years hands-on experience',
                    'Knowledge of electrical codes and safety standards',
                    'Ability to read blueprints and technical diagrams',
                    'Own basic tools preferred',
                ],
                'additional_info' => [
                    'Tool allowance provided',
                    'Safety equipment supplied',
                    'Hazard pay and allowances',
                    'Skills upgrade training',
                ],
            ],
            [
                'job_title' => 'Security Guard',
                'company_name' => 'Elite Security Agency',
                'job_industry' => 'Security Services',
                'location' => 'Various locations in Metro Manila',
                'vacancies' => 25,
                'apply_until' => Carbon::now()->addDays(35)->format('Y-m-d'),
                'status' => 'active',
                'qualifications' => [
                    'High school graduate',
                    'Valid Security Guard License',
                    'At least 21 years old',
                    'Physically and mentally fit',
                    'No criminal record',
                    'Willing to work on 12-hour shifts',
                ],
                'additional_info' => [
                    'Uniform and equipment provided',
                    'Government-mandated benefits',
                    'Rice subsidy',
                    'Training and seminars',
                ],
            ],
            [
                'job_title' => 'Sales Associate',
                'company_name' => 'Retail Store Chain',
                'job_industry' => 'Retail',
                'location' => 'Ortigas, Pasig City',
                'vacancies' => 12,
                'apply_until' => Carbon::now()->subDays(5)->format('Y-m-d'), // Expired for testing
                'status' => 'expired',
                'qualifications' => [
                    'College level or graduate',
                    'Pleasant personality with good communication skills',
                    'Sales experience is an advantage',
                    'Customer-oriented and result-driven',
                    'Willing to work on mall hours',
                ],
                'additional_info' => [
                    'Sales commission',
                    'Employee purchase discount',
                    'HMO after regularization',
                    'Career growth opportunities',
                ],
            ],
            [
                'job_title' => 'Machine Operator',
                'company_name' => 'Manufacturing Corp',
                'job_industry' => 'Manufacturing',
                'location' => 'Caloocan City, Metro Manila',
                'vacancies' => 10,
                'apply_until' => Carbon::now()->addDays(28)->format('Y-m-d'),
                'status' => 'active',
                'qualifications' => [
                    'High school graduate or vocational course graduate',
                    'Experience in machine operation preferred',
                    'Mechanical aptitude',
                    'Willing to work on shifting schedule',
                    'Can work under pressure',
                ],
                'additional_info' => [
                    'Overtime pay',
                    'Production incentives',
                    'Complete benefits package',
                    'On-the-job training provided',
                ],
            ],
            [
                'job_title' => 'Administrative Assistant',
                'company_name' => 'Corporate Services Inc.',
                'job_industry' => 'Administrative',
                'location' => 'BGC, Taguig City',
                'vacancies' => 4,
                'apply_until' => Carbon::now()->subDays(10)->format('Y-m-d'), // Expired for testing
                'status' => 'expired',
                'qualifications' => [
                    'Bachelor\'s degree in any field',
                    'Proficient in MS Office (Word, Excel, PowerPoint)',
                    'Excellent organizational and multitasking skills',
                    'Good written and verbal communication',
                    'At least 1 year administrative experience',
                ],
                'additional_info' => [
                    'Modern office environment',
                    'Flexible work arrangements',
                    'HMO and life insurance',
                    'Professional development programs',
                ],
            ],
        ];

        foreach ($jobs as $jobData) {
            Job::create($jobData);
        }

        $this->command->info('Created 10 sample job postings');
    }
}
