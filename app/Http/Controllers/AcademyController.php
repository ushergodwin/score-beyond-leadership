<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AcademyApplicationRequest;
use App\Models\AcademyApplication;
use App\Models\AcademyPage;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AcademyController extends Controller
{
    public function index(): Response
    {
        $page = AcademyPage::getAcademyPage();

        // Fallback to default content if no page exists
        if (!$page) {
            $pageData = [
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
            ];
        } else {
            $pageData = [
                'hero_title' => $page->hero_title,
                'hero_subtitle' => $page->hero_subtitle,
                'offers_heading' => $page->offers_heading,
                'offers_description' => $page->offers_description,
                'offerings' => $page->offerings ?? [],
                'location' => $page->location,
                'why_matters_heading' => $page->why_matters_heading,
                'why_matters_description' => $page->why_matters_description,
                'join_heading' => $page->join_heading,
                'join_description' => $page->join_description,
                'join_cta_text' => $page->join_cta_text,
            ];
        }

        return Inertia::render('Academy/Index', $pageData);
    }

    public function apply(): Response
    {
        return Inertia::render('Academy/Apply', [
            'prefill' => [
                'parent_email' => auth()->user()?->email,
            ],
        ]);
    }

    public function store(AcademyApplicationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Calculate age if not provided
        if (!isset($data['student_age']) && isset($data['student_date_of_birth'])) {
            $birthDate = new \DateTime($data['student_date_of_birth']);
            $today = new \DateTime();
            $age = $today->diff($birthDate)->y;
            $data['student_age'] = $age;
        }

        // Create academy application
        $application = AcademyApplication::create([
            'status' => 'submitted',
            'student_first_name' => $data['student_first_name'],
            'student_last_name' => $data['student_last_name'],
            'student_date_of_birth' => $data['student_date_of_birth'],
            'student_age' => $data['student_age'] ?? null,
            'student_gender' => $data['student_gender'],
            'student_school' => $data['student_school'] ?? null,
            'student_grade' => $data['student_grade'] ?? null,
            'parent_first_name' => $data['parent_first_name'],
            'parent_last_name' => $data['parent_last_name'],
            'parent_email' => $data['parent_email'],
            'parent_phone' => $data['parent_phone'],
            'parent_relationship' => $data['parent_relationship'],
            'parent_address' => $data['parent_address'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'],
            'emergency_contact_phone' => $data['emergency_contact_phone'],
            'emergency_contact_relationship' => $data['emergency_contact_relationship'] ?? null,
            'medical_conditions' => $data['medical_conditions'] ?? null,
            'dietary_requirements' => $data['dietary_requirements'] ?? null,
            'program_interest' => $data['program_interest'] ?? null,
            'previous_experience' => $data['previous_experience'] ?? null,
            'expectations' => $data['expectations'] ?? null,
            'terms_agreed' => true,
            'media_consent' => $data['media_consent'] ?? false,
        ]);

        // Send confirmation email to parent
        app(\App\Services\EmailService::class)->sendAcademyApplicationConfirmation($application);
        
        // Send notification emails to admins and managers
        app(\App\Services\EmailService::class)->sendAcademyApplicationNotification($application);

        return redirect()->route('academy.result', $application->id)
            ->with('success', 'Your academy application has been submitted successfully!');
    }

    public function result(int $id): Response
    {
        $application = AcademyApplication::findOrFail($id);

        return Inertia::render('Academy/Result', [
            'application' => [
                'id' => $application->id,
                'student_first_name' => $application->student_first_name,
                'student_last_name' => $application->student_last_name,
                'parent_email' => $application->parent_email,
                'status' => $application->status,
                'submitted_at' => $application->created_at->format('M d, Y H:i'),
            ],
        ]);
    }
}
