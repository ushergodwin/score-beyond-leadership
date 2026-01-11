<?php

namespace Database\Seeders;

use App\Models\HomePageFocusArea;
use App\Models\HomePageProject;
use App\Models\HomePageSuccessStory;
use Illuminate\Database\Seeder;

class HomePageContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Note: Images need to be manually uploaded via admin panel
        // These seeders create records with placeholder paths
        
        // Focus Areas
        $focusAreas = [
            [
                'title' => 'Education',
                'description' => 'Empowering youth through accessible learning.',
                'image_path' => 'images/home/education.webp', // Placeholder - upload via admin
                'display_order' => 1,
            ],
            [
                'title' => 'Livelihood and Skilling',
                'description' => 'Building sustainable, skilled livelihoods.',
                'image_path' => 'images/home/livelihood-and-skilling.webp',
                'display_order' => 2,
            ],
            [
                'title' => 'Health and Wellbeing',
                'description' => 'Promoting community health and wellness.',
                'image_path' => 'images/home/health-and-wellbeing.webp',
                'display_order' => 3,
            ],
        ];

        foreach ($focusAreas as $area) {
            HomePageFocusArea::updateOrCreate(
                ['title' => $area['title']],
                array_merge($area, ['is_active' => true])
            );
        }

        // Projects
        $projects = [
            [
                'title' => 'Food Security in Adjumani',
                'description' => 'Addressing food security challenges in the region.',
                'location' => 'Adjumani',
                'image_path' => 'images/home/food-security.webp',
                'display_order' => 1,
            ],
            [
                'title' => 'Women and Sports Conference',
                'description' => 'Empowering women through sports conferences.',
                'location' => 'Uganda',
                'image_path' => 'images/home/women-and-sports-conference.webp',
                'display_order' => 2,
            ],
            [
                'title' => 'SAY Project',
                'description' => 'Youth empowerment across multiple regions.',
                'location' => 'Northern and Eastern Regions',
                'image_path' => 'images/home/say-project.webp',
                'display_order' => 3,
            ],
            [
                'title' => 'Score Beyond Girls\' League',
                'description' => 'Promoting girls\' participation in sports.',
                'location' => 'Uganda',
                'image_path' => 'images/home/girls-league.webp',
                'display_order' => 4,
            ],
        ];

        foreach ($projects as $project) {
            HomePageProject::updateOrCreate(
                ['title' => $project['title']],
                array_merge($project, ['is_active' => true])
            );
        }

        // Success Stories
        $stories = [
            [
                'title' => 'Immaculate\'s Journey',
                'description' => 'Immaculate Adongpiny\'s journey shows the power of opportunity and support. Scouted by our team during the Girls\' League in Gulu at Restore Academy, she was identified for outstanding performance. She was given a scholarship at St. Noa in Kampala where she excelled academically. She was also identified to play for the Uganda U16 and U18 Basketball National Teams. Through our partner b1gplay, she earned a life-changing sports scholarship in Italy, inspiring others to dream big.',
                'quote' => 'From the Classroom to the Field',
                'image_path' => 'images/home/immaculate-journey.webp',
                'display_order' => 1,
            ],
            [
                'title' => 'Purity\'s Story',
                'description' => 'In 2016, during a Nationals qualifier, Score Beyond discovered Purity and invited her to their first camp, The Champion in Me. The experience helped her gain confidence and pursue her passion for sports, despite challenges. Encouraged by a teacher, she earned scholarships to Seroma High School and later Victoria University. Now, she works as the administration and finance officer of Score Beyond Leadership Organisation while pursuing a master\'s degree at Victoria University—a testament to resilience and the transformative power of opportunity.',
                'quote' => 'The Transformative Power Of Opportunity',
                'image_path' => 'images/home/purity-story.webp',
                'display_order' => 2,
            ],
        ];

        foreach ($stories as $story) {
            HomePageSuccessStory::updateOrCreate(
                ['title' => $story['title']],
                array_merge($story, ['is_active' => true])
            );
        }
    }
}
