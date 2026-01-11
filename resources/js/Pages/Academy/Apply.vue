<script setup lang="ts">
import { Head, Link, useForm } from "@inertiajs/vue3";
import LandingLayout from "@/Layouts/LandingLayout.vue";
import { showError, showSuccessDialog } from "@/composables/useNotifications";

const props = defineProps<{
    prefill?: {
        parent_email?: string;
    };
}>();

const form = useForm({
    // Student Information
    student_first_name: "",
    student_last_name: "",
    student_date_of_birth: "",
    student_age: null as number | null,
    student_gender: "",
    student_school: "",
    student_grade: "",
    
    // Parent/Guardian Information
    parent_first_name: "",
    parent_last_name: "",
    parent_email: props.prefill?.parent_email ?? "",
    parent_phone: "",
    parent_relationship: "",
    parent_address: "",
    
    // Additional Information
    medical_conditions: "",
    dietary_requirements: "",
    emergency_contact_name: "",
    emergency_contact_phone: "",
    emergency_contact_relationship: "",
    
    // Program Interest
    program_interest: "",
    previous_experience: "",
    expectations: "",
    
    // Agreements
    terms_agreed: false,
    media_consent: false,
});

const submit = () => {
    form.post(route("academy.store"), {
        onSuccess: () => {
            showSuccessDialog(
                "Application Submitted!",
                "Your academy application has been received. We will review it and contact you soon.",
            );
        },
        onError: () => {
            showError("Please check the form for errors and try again.");
        },
    });
};

const calculateAge = () => {
    if (form.student_date_of_birth) {
        const birthDate = new Date(form.student_date_of_birth);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        form.student_age = age;
    }
};
</script>

<template>
    <Head title="Apply to Academy" />
    <LandingLayout>
        <template #hero>
            <section class="py-5 section-bg-gradient-dark border-bottom text-white">
                <div class="container">
                    <Link class="text-white-50 text-decoration-none d-inline-flex align-items-center gap-2 mb-3" :href="route('academy.index')">
                        <i class="bi bi-arrow-left"></i>
                        Back to Academy
                    </Link>
                    <p class="badge rounded-pill bg-white text-primary text-uppercase fw-semibold mb-3">
                        Academy Application
                    </p>
                    <h1 class="display-5 fw-bold mb-3">
                        Join Score Beyond Academy
                    </h1>
                    <p class="lead mb-2">
                        Enroll your child in our structured development program designed for children and youth aged 6–18.
                    </p>
                    <p class="text-white-50 mb-0">
                        Using sports as a tool for leadership, education, and personal growth.
                    </p>
                </div>
            </section>
        </template>

        <div class="container pt-4 pb-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <form @submit.prevent="submit">
                        <!-- Student Information Section -->
                        <div class="form-card mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h4 fw-bold mb-0">Student Information</h2>
                                <span class="text-muted small">About the applicant</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        First Name <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        v-model="form.student_first_name"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.student_first_name }"
                                        required
                                    />
                                    <div v-if="form.errors.student_first_name" class="invalid-feedback">
                                        {{ form.errors.student_first_name }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Last Name <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        v-model="form.student_last_name"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.student_last_name }"
                                        required
                                    />
                                    <div v-if="form.errors.student_last_name" class="invalid-feedback">
                                        {{ form.errors.student_last_name }}
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        Date of Birth <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        v-model="form.student_date_of_birth"
                                        type="date"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.student_date_of_birth }"
                                        @change="calculateAge"
                                        required
                                    />
                                    <div v-if="form.errors.student_date_of_birth" class="invalid-feedback">
                                        {{ form.errors.student_date_of_birth }}
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Age</label>
                                    <input
                                        v-model.number="form.student_age"
                                        type="number"
                                        class="form-control"
                                        readonly
                                        :class="{ 'is-invalid': form.errors.student_age }"
                                    />
                                    <small class="text-muted">Calculated automatically</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        Gender <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        v-model="form.student_gender"
                                        class="form-select"
                                        :class="{ 'is-invalid': form.errors.student_gender }"
                                        required
                                    >
                                        <option value="">Select gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <div v-if="form.errors.student_gender" class="invalid-feedback">
                                        {{ form.errors.student_gender }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Current School</label>
                                    <input
                                        v-model="form.student_school"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.student_school }"
                                    />
                                    <div v-if="form.errors.student_school" class="invalid-feedback">
                                        {{ form.errors.student_school }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Grade/Class</label>
                                    <input
                                        v-model="form.student_grade"
                                        type="text"
                                        class="form-control"
                                        placeholder="e.g., P5, S2"
                                        :class="{ 'is-invalid': form.errors.student_grade }"
                                    />
                                    <div v-if="form.errors.student_grade" class="invalid-feedback">
                                        {{ form.errors.student_grade }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Parent/Guardian Information Section -->
                        <div class="form-card mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h4 fw-bold mb-0">Parent/Guardian Information</h2>
                                <span class="text-muted small">Contact details</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        First Name <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        v-model="form.parent_first_name"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.parent_first_name }"
                                        required
                                    />
                                    <div v-if="form.errors.parent_first_name" class="invalid-feedback">
                                        {{ form.errors.parent_first_name }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Last Name <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        v-model="form.parent_last_name"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.parent_last_name }"
                                        required
                                    />
                                    <div v-if="form.errors.parent_last_name" class="invalid-feedback">
                                        {{ form.errors.parent_last_name }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        v-model="form.parent_email"
                                        type="email"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.parent_email }"
                                        required
                                    />
                                    <div v-if="form.errors.parent_email" class="invalid-feedback">
                                        {{ form.errors.parent_email }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Phone Number <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        v-model="form.parent_phone"
                                        type="tel"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.parent_phone }"
                                        required
                                    />
                                    <div v-if="form.errors.parent_phone" class="invalid-feedback">
                                        {{ form.errors.parent_phone }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Relationship to Student <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        v-model="form.parent_relationship"
                                        class="form-select"
                                        :class="{ 'is-invalid': form.errors.parent_relationship }"
                                        required
                                    >
                                        <option value="">Select relationship</option>
                                        <option value="parent">Parent</option>
                                        <option value="guardian">Guardian</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <div v-if="form.errors.parent_relationship" class="invalid-feedback">
                                        {{ form.errors.parent_relationship }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Address</label>
                                    <input
                                        v-model="form.parent_address"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.parent_address }"
                                    />
                                    <div v-if="form.errors.parent_address" class="invalid-feedback">
                                        {{ form.errors.parent_address }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact Section -->
                        <div class="form-card mb-4">
                            <h2 class="h4 fw-bold mb-4">Emergency Contact</h2>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Emergency Contact Name <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        v-model="form.emergency_contact_name"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.emergency_contact_name }"
                                        required
                                    />
                                    <div v-if="form.errors.emergency_contact_name" class="invalid-feedback">
                                        {{ form.errors.emergency_contact_name }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Emergency Contact Phone <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        v-model="form.emergency_contact_phone"
                                        type="tel"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.emergency_contact_phone }"
                                        required
                                    />
                                    <div v-if="form.errors.emergency_contact_phone" class="invalid-feedback">
                                        {{ form.errors.emergency_contact_phone }}
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Relationship</label>
                                    <input
                                        v-model="form.emergency_contact_relationship"
                                        type="text"
                                        class="form-control"
                                        placeholder="e.g., Parent, Guardian, Relative"
                                        :class="{ 'is-invalid': form.errors.emergency_contact_relationship }"
                                    />
                                    <div v-if="form.errors.emergency_contact_relationship" class="invalid-feedback">
                                        {{ form.errors.emergency_contact_relationship }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information Section -->
                        <div class="form-card mb-4">
                            <h2 class="h4 fw-bold mb-4">Additional Information</h2>

                            <div class="mb-3">
                                <label class="form-label">Medical Conditions / Allergies</label>
                                <textarea
                                    v-model="form.medical_conditions"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Please disclose any medical conditions or allergies we should be aware of..."
                                    :class="{ 'is-invalid': form.errors.medical_conditions }"
                                ></textarea>
                                <div v-if="form.errors.medical_conditions" class="invalid-feedback">
                                    {{ form.errors.medical_conditions }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Dietary Requirements</label>
                                <textarea
                                    v-model="form.dietary_requirements"
                                    class="form-control"
                                    rows="2"
                                    placeholder="Any dietary restrictions or preferences..."
                                    :class="{ 'is-invalid': form.errors.dietary_requirements }"
                                ></textarea>
                                <div v-if="form.errors.dietary_requirements" class="invalid-feedback">
                                    {{ form.errors.dietary_requirements }}
                                </div>
                            </div>
                        </div>

                        <!-- Program Interest Section -->
                        <div class="form-card mb-4">
                            <h2 class="h4 fw-bold mb-4">Program Interest</h2>

                            <div class="mb-3">
                                <label class="form-label">Program Interest</label>
                                <select
                                    v-model="form.program_interest"
                                    class="form-select"
                                    :class="{ 'is-invalid': form.errors.program_interest }"
                                >
                                    <option value="">Select program</option>
                                    <option value="sports_camps">Sports Camps</option>
                                    <option value="personal_growth">Personal Growth</option>
                                    <option value="volunteer_trips">Volunteer Trips</option>
                                    <option value="all">All Programs</option>
                                </select>
                                <div v-if="form.errors.program_interest" class="invalid-feedback">
                                    {{ form.errors.program_interest }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Previous Experience</label>
                                <textarea
                                    v-model="form.previous_experience"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Tell us about any previous sports or leadership experience..."
                                    :class="{ 'is-invalid': form.errors.previous_experience }"
                                ></textarea>
                                <div v-if="form.errors.previous_experience" class="invalid-feedback">
                                    {{ form.errors.previous_experience }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Expectations</label>
                                <textarea
                                    v-model="form.expectations"
                                    class="form-control"
                                    rows="3"
                                    placeholder="What are your expectations from the academy program?"
                                    :class="{ 'is-invalid': form.errors.expectations }"
                                ></textarea>
                                <div v-if="form.errors.expectations" class="invalid-feedback">
                                    {{ form.errors.expectations }}
                                </div>
                            </div>
                        </div>

                        <!-- Agreements Section -->
                        <div class="form-card mb-4">
                            <h2 class="h4 fw-bold mb-4">Agreements</h2>

                            <div class="form-check mb-3">
                                <input
                                    v-model="form.terms_agreed"
                                    class="form-check-input"
                                    type="checkbox"
                                    id="terms_agreed"
                                    :class="{ 'is-invalid': form.errors.terms_agreed }"
                                    required
                                />
                                <label class="form-check-label" for="terms_agreed">
                                    <strong>
                                        I agree to the Terms and Conditions
                                        <span class="text-danger">*</span>
                                    </strong>
                                    <small class="d-block text-muted">
                                        I understand and agree to abide by Score Beyond Academy's terms and conditions
                                    </small>
                                </label>
                                <div v-if="form.errors.terms_agreed" class="invalid-feedback d-block">
                                    {{ form.errors.terms_agreed }}
                                </div>
                            </div>

                            <div class="form-check">
                                <input
                                    v-model="form.media_consent"
                                    class="form-check-input"
                                    type="checkbox"
                                    id="media_consent"
                                />
                                <label class="form-check-label" for="media_consent">
                                    <strong>Photography & Media Consent</strong>
                                    <small class="d-block text-muted">
                                        I consent to my child being photographed and featured in Score Beyond Leadership's media and promotional materials
                                    </small>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-between align-items-center mb-5">
                            <Link
                                class="btn btn-outline-secondary rounded-pill px-4"
                                :href="route('academy.index')"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                class="btn btn-primary btn-lg rounded-pill px-5 text-uppercase fw-bold"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                {{ form.processing ? "Submitting..." : "Submit Application" }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </LandingLayout>
</template>

<style scoped>
.form-card {
    background: white;
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
</style>

