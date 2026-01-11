<?php

namespace Database\Seeders;

use App\Models\AcademyPage;
use Illuminate\Database\Seeder;

class AcademyPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AcademyPage::updateOrCreate(
            ['slug' => 'academy'],
            [
                'hero_title' => 'Score Beyond Academy',
                'hero_subtitle' => 'We don\'t just build athletes we build future leaders.',
                'offers_heading' => 'What the Academy Offers',
                'offers_description' => 'The Score Beyond Academy is a structured development program designed for children and youth aged 6–18, using sports as a tool for leadership, education, and personal growth.',
                'offerings' => [
                    ['icon' => 'bi-trophy-fill', 'label' => 'Sports camps'],
                    ['icon' => 'bi-person-badge', 'label' => 'Personal Growth'],
                    ['icon' => 'bi-heart-fill', 'label' => 'Volunteer trips'],
                ],
                'location' => 'Hill Preparatory School Naguru',
                'why_matters_heading' => 'Why It Matters',
                'why_matters_description' => 'The Academy empowers both boys and girls to dream boldly, equipping them with confidence, discipline, and leadership skills that positively influence their education, health, and future opportunities.',
                'join_heading' => 'Join the Academy',
                'join_description' => 'Enroll your child in our structured development program and watch them grow into confident leaders through sports, mentorship, and personal development.',
                'join_cta_text' => 'Apply Now',
                'is_active' => true,
            ]
        );
    }
}
