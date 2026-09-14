<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Guard;
use App\Models\JobPosting;
use App\Models\Policy;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@norixsecurity.co.uk'],
            [
                'name' => 'Alex Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'ops@norixsecurity.co.uk'],
            [
                'name' => 'Olivia Operations',
                'password' => Hash::make('ops123'),
                'role' => 'operations',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'careers@norixsecurity.co.uk'],
            [
                'name' => 'Riley Recruiter',
                'password' => Hash::make('recruit123'),
                'role' => 'recruiter',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'finance@norixsecurity.co.uk'],
            [
                'name' => 'Finley Finance',
                'password' => Hash::make('finance123'),
                'role' => 'finance',
            ]
        );

        $jobs = [
            [
                'title' => 'SIA Door Supervisor',
                'location' => 'Central London',
                'description' => 'Join Norix Security as a licensed door supervisor covering retail and leisure venues.',
                'requirements' => ['Valid SIA Door Supervisor licence', 'Right to work in the UK', 'Flexible nights and weekends'],
            ],
            [
                'title' => 'Mobile Patrol Officer',
                'location' => 'Greater London',
                'description' => 'Mobile response and scheduled patrols for commercial clients.',
                'requirements' => ['Valid SIA licence', 'UK driving licence preferred', 'Reliable communication'],
            ],
            [
                'title' => 'Retail Security Officer',
                'location' => 'South East England',
                'description' => 'Customer-facing retail guarding with loss-prevention focus.',
                'requirements' => ['SIA licence', 'Retail experience preferred', 'Right to work documentation'],
            ],
        ];

        foreach ($jobs as $job) {
            JobPosting::query()->updateOrCreate(
                ['slug' => Str::slug($job['title'])],
                [
                    'title' => $job['title'],
                    'location' => $job['location'],
                    'description' => $job['description'],
                    'requirements' => $job['requirements'],
                    'is_active' => true,
                ]
            );
        }

        $client = Client::query()->updateOrCreate(
            ['email' => 'sarah@thamesretail.example'],
            [
                'name' => 'Thames Retail Group',
                'contact_name' => 'Sarah Mitchell',
                'phone' => '+44 20 7946 1001',
                'address' => '12 Oxford Street, London W1D 1AN',
                'contract_notes' => 'Nightly coverage; monthly invoicing.',
            ]
        );

        $site = Site::query()->updateOrCreate(
            ['client_id' => $client->id, 'name' => 'Oxford Street Flagship'],
            [
                'address' => '12 Oxford Street, London W1D 1AN',
                'requirements' => 'SIA Door Supervisor; customer-facing.',
                'is_active' => true,
            ]
        );

        $guard = Guard::query()->updateOrCreate(
            ['email' => 'priya.shah@norixsecurity.co.uk'],
            [
                'full_name' => 'Priya Shah',
                'phone' => '+44 7700 900789',
                'sia_licence_number' => '10555666777888',
                'sia_expiry' => now()->addDays(40)->toDateString(),
                'availability_notes' => 'Central London days and evenings.',
                'is_active' => true,
            ]
        );
        $guard->sites()->syncWithoutDetaching([$site->id]);

        $policyTitles = [
            'Corporate Social Responsibility',
            'Environmental Policy Statement',
            'Diversity and Inclusion Policy',
            'Health and Safety Policy Statement',
            'Quality Policy Statement',
            'Media Handling Policy',
        ];

        foreach ($policyTitles as $i => $title) {
            Policy::query()->updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'sort_order' => $i + 1,
                    'is_published' => true,
                    'file_path' => null,
                ]
            );
        }
    }
}
