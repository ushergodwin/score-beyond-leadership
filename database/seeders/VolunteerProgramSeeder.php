<?php

namespace Database\Seeders;

use App\Models\VolunteerProgram;
use App\Models\VolunteerRole;
use Illuminate\Database\Seeder;

class VolunteerProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Volunteer Programs
        VolunteerProgram::updateOrCreate(
            ['slug' => 'unpaid'],
            [
                'title' => 'Unpaid Volunteers',
                'badge' => 'Unpaid',
                'summary' => 'Unpaid volunteers self-fund their stay and transport but receive full coordination, orientation, and placement support from Score Beyond Leadership.',
                'description' => 'This option is ideal for volunteers looking to gain hands-on experience.',
                'benefits' => [
                    'Full coordination and orientation support',
                    'Placement support from Score Beyond Leadership',
                    'Hands-on experience in community programs',
                    'Certificate and recommendation upon completion',
                ],
                'logistics' => [
                    'Self-funded stay and transport',
                    'Full program coordination provided',
                    'Ideal for gaining practical experience',
                ],
                'is_active' => true,
                'display_order' => 1,
            ]
        );

        VolunteerProgram::updateOrCreate(
            ['slug' => 'paid'],
            [
                'title' => 'Paid Volunteers',
                'badge' => 'Paid',
                'summary' => 'Paid volunteers contribute a program fee that may cover accommodation, meals, local transport, training, and a contribution toward community programs.',
                'description' => 'This option is ideal for volunteers seeking a structured experience with program support.',
                'benefits' => [
                    'Program fee covers accommodation and meals',
                    'Local transport included',
                    'Training and program support',
                    'Contribution toward community programs',
                ],
                'logistics' => [
                    'Structured program with full support',
                    'Accommodation and meals provided',
                    'Ideal for structured volunteer experience',
                ],
                'is_active' => true,
                'display_order' => 2,
            ]
        );

        // Seed Volunteer Roles
        $roles = [
            ['name' => 'Coaching', 'display_order' => 1],
            ['name' => 'Event Support', 'display_order' => 2],
            ['name' => 'Community Outreach', 'display_order' => 3],
            ['name' => 'Arts & Crafts Teaching', 'display_order' => 4],
            ['name' => 'Fundraising', 'display_order' => 5],
            ['name' => 'Administration', 'display_order' => 6],
            ['name' => 'Health & Wellness', 'display_order' => 7],
            ['name' => 'Education Support', 'display_order' => 8],
        ];

        foreach ($roles as $role) {
            VolunteerRole::updateOrCreate(
                ['name' => $role['name']],
                array_merge($role, ['is_active' => true])
            );
        }
    }
}
