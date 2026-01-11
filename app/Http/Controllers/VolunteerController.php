<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\VolunteerApplicationRequest;
use App\Models\VolunteerApplication;
use App\Models\VolunteerProgram;
use App\Models\VolunteerRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class VolunteerController extends Controller
{
    public function index(): Response
    {
        $programs = VolunteerProgram::where('is_active', true)
            ->orderBy('display_order')
            ->get()
            ->map(function ($program) {
                return [
                    'slug' => $program->slug,
                    'title' => $program->title,
                    'badge' => $program->badge,
                    'summary' => $program->summary,
                    'description' => $program->description,
                    'benefits' => $program->benefits ?? [],
                    'logistics' => $program->logistics ?? [],
                ];
            })
            ->toArray();

        return Inertia::render('Volunteer/Index', [
            'programs' => $programs,
        ]);
    }

    public function apply(?string $program = null): Response
    {
        $programType = $this->sanitizeProgramType($program);

        return Inertia::render('Volunteer/Apply', $this->formProps($programType));
    }

    public function store(VolunteerApplicationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Handle file uploads
        $cvPath = null;
        $idDocumentPath = null;

        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('volunteer-applications/cv', 'public');
        }

        if ($request->hasFile('id_document')) {
            $idDocumentPath = $request->file('id_document')->store('volunteer-applications/id-documents', 'public');
        }

        // Create volunteer application
        $application = VolunteerApplication::create([
            'status' => 'submitted',
            'program_type' => $data['program_type'],
            'preferred_name' => $data['preferred_name'] ?? null,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'nationality' => $data['nationality'] ?? null,
            'passport_number' => $data['passport_number'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'],
            'country_of_residence' => $data['country_of_residence'],
            'city' => $data['city'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'],
            'emergency_contact_relationship' => $data['emergency_contact_relationship'] ?? null,
            'emergency_contact_phone' => $data['emergency_contact_phone'],
            'emergency_contact_email' => $data['emergency_contact_email'] ?? null,
            'preferred_volunteer_role' => $data['preferred_volunteer_role'] ?? null,
            'preferred_roles' => $data['preferred_roles'] ?? null,
            'availability_start' => $data['availability_start'] ?? null,
            'availability_end' => $data['availability_end'] ?? null,
            'length_of_stay_weeks' => $data['length_of_stay_weeks'] ?? null,
            'tshirt_size' => $data['tshirt_size'] ?? null,
            'skills_experience' => $data['skills_experience'] ?? null,
            'medical_conditions' => $data['medical_conditions'] ?? null,
            'dietary_requirements' => $data['dietary_requirements'] ?? null,
            'accommodation_required' => $data['accommodation_required'] ?? false,
            'bringing_equipment' => $data['bringing_equipment'] ?? false,
            'code_of_conduct_agreed' => true,
            'media_consent' => $data['media_consent'] ?? false,
            'payment_method' => $data['payment_method'] ?? null,
            'payment_status' => $data['program_type'] === 'paid' ? 'pending' : 'not_required',
            'cv_path' => $cvPath,
            'id_document_path' => $idDocumentPath,
        ]);

        // Send confirmation email
        app(\App\Services\EmailService::class)->sendVolunteerApplicationConfirmation($application);
        
        // TODO: If paid program, initiate payment process

        return redirect()->route('volunteer.result', $application->id)
            ->with('success', 'Your volunteer application has been submitted successfully!');
    }

    public function result(int $id): Response
    {
        $application = VolunteerApplication::findOrFail($id);

        return Inertia::render('Volunteer/Result', [
            'application' => [
                'id' => $application->id,
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'email' => $application->email,
                'program_type' => $application->program_type,
                'status' => $application->status,
                'submitted_at' => $application->created_at->format('M d, Y H:i'),
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formProps(string $selectedProgramType): array
    {
        $volunteerRoles = VolunteerRole::where('is_active', true)
            ->orderBy('display_order')
            ->pluck('name')
            ->toArray();

        // Fallback to default roles if none exist in database
        if (empty($volunteerRoles)) {
            $volunteerRoles = [
                'Coaching',
                'Event Support',
                'Community Outreach',
                'Arts & Crafts Teaching',
                'Fundraising',
                'Administration',
                'Health & Wellness',
                'Education Support',
            ];
        }

        return [
            'volunteerRoles' => $volunteerRoles,
            'tshirtSizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'], // Keep as config for now
            'prefill' => [
                'first_name' => auth()->user()?->name,
                'email' => auth()->user()?->email,
            ],
            'selectedProgramType' => $selectedProgramType,
        ];
    }

    private function sanitizeProgramType(?string $program): string
    {
        return in_array($program, ['paid', 'unpaid'], true) ? $program : 'unpaid';
    }
}

