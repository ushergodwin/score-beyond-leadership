<script setup lang="ts">
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import LandingLayout from "@/Layouts/LandingLayout.vue";
import { showError, showSuccessDialog } from "@/composables/useNotifications";

type ProgramType = "paid" | "unpaid";

const props = defineProps<{
    volunteerRoles: string[];
    tshirtSizes: string[];
    prefill?: {
        first_name?: string;
        email?: string;
    };
    selectedProgramType: ProgramType;
}>();

const programCopy: Record<
    ProgramType,
    {
        heading: string;
        description: string;
        subtext: string;
    }
> = {
    unpaid: {
        heading: "Community Volunteer Application",
        description:
            "Serve alongside our team in Kampala and upcountry hubs. Mentor girls, assist during tournaments, and lend your skills to community outreach events.",
        subtext: "Unpaid service opportunity • Flexible schedules • Ideal for local and regional volunteers",
    },
    paid: {
        heading: "Professional Fellowship Application",
        description:
            "Apply for structured placements that include stipends, project deliverables, and dedicated mentorship. Ideal for professionals seeking immersive impact work.",
        subtext: "Paid placement • Fixed work plan • Ideal for international fellows and senior volunteers",
    },
};

const isPaidProgram = ref(props.selectedProgramType === "paid");
const isDiaspora = ref(false);
const selectedRoles = ref<string[]>([]);

const form = useForm({
    // Personal Information
    first_name: props.prefill?.first_name ?? "",
    last_name: "",
    preferred_name: "",
    date_of_birth: "",
    nationality: "",
    passport_number: "",
    email: props.prefill?.email ?? "",
    phone: "",
    country_of_residence: "UG",
    city: "",

    // Emergency Contact
    emergency_contact_name: "",
    emergency_contact_relationship: "",
    emergency_contact_phone: "",
    emergency_contact_email: "",

    // Program Details
    program_type: props.selectedProgramType,
    preferred_volunteer_role: "",
    preferred_roles: [] as string[],
    availability_start: "",
    availability_end: "",
    length_of_stay_weeks: null as number | null,

    // Additional Information
    tshirt_size: "",
    skills_experience: "",
    medical_conditions: "",
    dietary_requirements: "",
    accommodation_required: false,
    bringing_equipment: false,

    // Agreements
    code_of_conduct_agreed: false,
    media_consent: false,

    // Payment
    payment_method: "",

    // File Uploads
    cv: null as File | null,
    id_document: null as File | null,
});

isDiaspora.value = form.country_of_residence !== "UG";

watch(
    () => form.program_type,
    (value) => {
        isPaidProgram.value = value === "paid";
        if (!isPaidProgram.value) {
            form.payment_method = "";
        }
    },
    { immediate: true },
);

watch(
    () => form.country_of_residence,
    (value) => {
        isDiaspora.value = value !== "UG";
    },
    { immediate: true },
);

const toggleRole = (role: string) => {
    const index = selectedRoles.value.indexOf(role);
    if (index > -1) {
        selectedRoles.value.splice(index, 1);
    } else {
        selectedRoles.value.push(role);
    }
    form.preferred_roles = selectedRoles.value;
};

const submit = () => {
    form.post(route("volunteer.store"), {
        forceFormData: true,
        onSuccess: () => {
            showSuccessDialog(
                "Application Submitted!",
                "Your volunteer application has been received. We will review it and contact you soon.",
            );
        },
        onError: () => {
            showError("Please check the form for errors and try again.");
        },
    });
};

const handleFileChange = (field: "cv" | "id_document", event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form[field] = target.files[0];
    }
};

const selectedProgramContent = computed(() => programCopy[props.selectedProgramType]);
const volunteerRoles = computed(() => props.volunteerRoles);
const tshirtSizes = computed(() => props.tshirtSizes);
</script>

<template>
    <Head title="Apply to Volunteer" />
    <LandingLayout>
        <template #hero>
            <section class="py-5 section-bg-gradient-dark border-bottom text-white">
                <div class="container">
                    <h1 class="display-5 fw-bold mb-3">
                        {{ selectedProgramContent.heading }}
                    </h1>
                    <p class="lead mb-2">
                        {{ selectedProgramContent.description }}
                    </p>
                    <p class="text-white-50 mb-0">
                        {{ selectedProgramContent.subtext }}
                    </p>
                </div>
            </section>
        </template>

        <div class="container pt-4 pb-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <form @submit.prevent="submit">
                        <!-- Personal Information Section -->
                        <div class="form-card mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h4 fw-bold mb-0">Personal Information</h2>
                                <span class="text-muted small">Tell us about yourself</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        First Name <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        v-model="form.first_name"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.first_name }"
                                        required
                                    />
                                    <div v-if="form.errors.first_name" class="invalid-feedback">
                                        {{ form.errors.first_name }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Last Name <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        v-model="form.last_name"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.last_name }"
                                        required
                                    />
                                    <div v-if="form.errors.last_name" class="invalid-feedback">
                                        {{ form.errors.last_name }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Preferred Name</label>
                                    <input
                                        v-model="form.preferred_name"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.preferred_name }"
                                    />
                                    <div v-if="form.errors.preferred_name" class="invalid-feedback">
                                        {{ form.errors.preferred_name }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth</label>
                                    <input
                                        v-model="form.date_of_birth"
                                        type="date"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.date_of_birth }"
                                    />
                                    <div v-if="form.errors.date_of_birth" class="invalid-feedback">
                                        {{ form.errors.date_of_birth }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Nationality</label>
                                    <input
                                        v-model="form.nationality"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.nationality }"
                                    />
                                    <div v-if="form.errors.nationality" class="invalid-feedback">
                                        {{ form.errors.nationality }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Passport Number
                                        <span v-if="isDiaspora" class="text-danger">*</span>
                                    </label>
                                    <input
                                        v-model="form.passport_number"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.passport_number }"
                                        :required="isDiaspora"
                                    />
                                    <small v-if="isDiaspora" class="text-muted">
                                        Required for diaspora volunteers
                                    </small>
                                    <div v-if="form.errors.passport_number" class="invalid-feedback">
                                        {{ form.errors.passport_number }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.email }"
                                        required
                                    />
                                    <div v-if="form.errors.email" class="invalid-feedback">
                                        {{ form.errors.email }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Phone Number <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.phone }"
                                        required
                                    />
                                    <div v-if="form.errors.phone" class="invalid-feedback">
                                        {{ form.errors.phone }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Country of Residence <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        v-model="form.country_of_residence"
                                        class="form-select"
                                        :class="{ 'is-invalid': form.errors.country_of_residence }"
                                        required
                                    >
                                        <option value="UG">Uganda</option>
                                        <option value="US">United States</option>
                                        <option value="GB">United Kingdom</option>
                                        <option value="CA">Canada</option>
                                        <option value="AU">Australia</option>
                                        <option value="OTHER">Other</option>
                                    </select>
                                    <div v-if="form.errors.country_of_residence" class="invalid-feedback">
                                        {{ form.errors.country_of_residence }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Current City</label>
                                    <input
                                        v-model="form.city"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.city }"
                                    />
                                    <div v-if="form.errors.city" class="invalid-feedback">
                                        {{ form.errors.city }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact Section -->
                        <div class="form-card mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h4 fw-bold mb-0">Emergency Contact</h2>
                                <span class="text-muted small">In case we need urgent assistance</span>
                            </div>

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
                                        <label class="form-label">Relationship</label>
                                        <input
                                            v-model="form.emergency_contact_relationship"
                                            type="text"
                                            class="form-control"
                                            :class="{ 'is-invalid': form.errors.emergency_contact_relationship }"
                                            placeholder="e.g., Parent, Spouse, Friend"
                                        />
                                        <div v-if="form.errors.emergency_contact_relationship" class="invalid-feedback">
                                            {{ form.errors.emergency_contact_relationship }}
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

                                <div class="col-md-6">
                                    <label class="form-label">Emergency Contact Email</label>
                                    <input
                                        v-model="form.emergency_contact_email"
                                        type="email"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.emergency_contact_email }"
                                    />
                                    <div v-if="form.errors.emergency_contact_email" class="invalid-feedback">
                                        {{ form.errors.emergency_contact_email }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Program Details Section -->
                        <div class="form-card mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h4 fw-bold mb-0">Program Details</h2>
                                <span class="text-muted small">Select your preferred placement</span>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Program Type <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex flex-column flex-lg-row gap-3">
                                    <label
                                        class="form-check-card"
                                        :class="{ 'form-check-card--active': form.program_type === 'unpaid' }"
                                    >
                                        <input v-model="form.program_type" class="form-check-input" type="radio" value="unpaid" />
                                        <div>
                                            <strong>Unpaid Volunteer</strong>
                                            <small class="d-block text-muted">Community service opportunity</small>
                                        </div>
                                    </label>
                                    <label
                                        class="form-check-card"
                                        :class="{ 'form-check-card--active': form.program_type === 'paid' }"
                                    >
                                        <input v-model="form.program_type" class="form-check-input" type="radio" value="paid" />
                                        <div>
                                            <strong>Paid Program</strong>
                                            <small class="d-block text-muted">Structured placement with stipend</small>
                                        </div>
                                    </label>
                                </div>
                                <div v-if="form.errors.program_type" class="text-danger small mt-1">
                                    {{ form.errors.program_type }}
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Preferred Volunteer Role(s)</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <button
                                        v-for="role in volunteerRoles"
                                        :key="role"
                                        type="button"
                                        class="btn"
                                        :class="{
                                            'btn-primary': selectedRoles.includes(role),
                                            'btn-outline-primary': !selectedRoles.includes(role),
                                        }"
                                        @click="toggleRole(role)"
                                    >
                                        {{ role }}
                                    </button>
                                </div>
                                <input
                                    v-model="form.preferred_volunteer_role"
                                    type="text"
                                    class="form-control mt-3"
                                    placeholder="Or specify your preferred role"
                                    :class="{ 'is-invalid': form.errors.preferred_volunteer_role }"
                                />
                                <div v-if="form.errors.preferred_volunteer_role" class="invalid-feedback">
                                    {{ form.errors.preferred_volunteer_role }}
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Available Start Date</label>
                                    <input
                                        v-model="form.availability_start"
                                        type="date"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.availability_start }"
                                    />
                                    <div v-if="form.errors.availability_start" class="invalid-feedback">
                                        {{ form.errors.availability_start }}
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Available End Date</label>
                                    <input
                                        v-model="form.availability_end"
                                        type="date"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.availability_end }"
                                    />
                                    <div v-if="form.errors.availability_end" class="invalid-feedback">
                                        {{ form.errors.availability_end }}
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Length of Stay (Weeks)</label>
                                    <input
                                        v-model.number="form.length_of_stay_weeks"
                                        type="number"
                                        class="form-control"
                                        min="1"
                                        max="104"
                                        :class="{ 'is-invalid': form.errors.length_of_stay_weeks }"
                                    />
                                    <div v-if="form.errors.length_of_stay_weeks" class="invalid-feedback">
                                        {{ form.errors.length_of_stay_weeks }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information Section -->
                        <div class="form-card mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h4 fw-bold mb-0">Additional Information</h2>
                                <span class="text-muted small">Help us prepare for you</span>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">T-Shirt Size</label>
                                    <select
                                        v-model="form.tshirt_size"
                                        class="form-select"
                                        :class="{ 'is-invalid': form.errors.tshirt_size }"
                                    >
                                        <option value="">Select size</option>
                                        <option v-for="size in tshirtSizes" :key="size" :value="size">
                                            {{ size }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.tshirt_size" class="invalid-feedback">
                                        {{ form.errors.tshirt_size }}
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Relevant Skills & Experience</label>
                                <textarea
                                    v-model="form.skills_experience"
                                    class="form-control"
                                    rows="4"
                                    placeholder="Tell us about your skills, experience, and what you can contribute..."
                                    :class="{ 'is-invalid': form.errors.skills_experience }"
                                ></textarea>
                                <div v-if="form.errors.skills_experience" class="invalid-feedback">
                                    {{ form.errors.skills_experience }}
                                </div>
                            </div>

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

                            <div class="form-check mb-3">
                                <input
                                    v-model="form.accommodation_required"
                                    class="form-check-input"
                                    type="checkbox"
                                    id="accommodation"
                                />
                                <label class="form-check-label" for="accommodation">
                                    I require accommodation assistance
                                </label>
                            </div>

                            <div class="form-check">
                                <input
                                    v-model="form.bringing_equipment"
                                    class="form-check-input"
                                    type="checkbox"
                                    id="equipment"
                                />
                                <label class="form-check-label" for="equipment">
                                    I will be bringing equipment/kit
                                </label>
                            </div>
                        </div>

                        <!-- File Uploads Section -->
                        <div class="form-card mb-4">
                            <h2 class="h4 fw-bold mb-4">Documents</h2>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">CV / Resume (PDF, DOC, DOCX)</label>
                                    <input
                                        type="file"
                                        class="form-control"
                                        accept=".pdf,.doc,.docx"
                                        :class="{ 'is-invalid': form.errors.cv }"
                                        @change="handleFileChange('cv', $event)"
                                    />
                                    <small class="text-muted">Maximum file size: 5MB</small>
                                    <div v-if="form.errors.cv" class="invalid-feedback d-block">
                                        {{ form.errors.cv }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">ID Document / Passport Copy (PDF, JPG, PNG)</label>
                                    <input
                                        type="file"
                                        class="form-control"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        :class="{ 'is-invalid': form.errors.id_document }"
                                        @change="handleFileChange('id_document', $event)"
                                    />
                                    <small class="text-muted">Maximum file size: 5MB</small>
                                    <div v-if="form.errors.id_document" class="invalid-feedback d-block">
                                        {{ form.errors.id_document }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method (Conditional) -->
                        <div v-if="isPaidProgram" class="form-card mb-4">
                            <h2 class="h4 fw-bold mb-4">Payment Method</h2>
                            <div class="d-flex flex-column gap-3">
                                <label
                                    class="payment-option"
                                    :class="{
                                        'payment-option--active': form.payment_method === 'pesapal_visa_mastercard',
                                    }"
                                >
                                    <input
                                        v-model="form.payment_method"
                                        class="form-check-input"
                                        type="radio"
                                        value="pesapal_visa_mastercard"
                                    />
                                    <i class="bi bi-credit-card fs-4"></i>
                                    <span class="fw-semibold">Visa / Mastercard</span>
                                </label>
                                <label
                                    class="payment-option"
                                    :class="{
                                        'payment-option--active': form.payment_method === 'pesapal_mobile_money',
                                    }"
                                >
                                    <input
                                        v-model="form.payment_method"
                                        class="form-check-input"
                                        type="radio"
                                        value="pesapal_mobile_money"
                                    />
                                    <i class="bi bi-phone fs-4"></i>
                                    <span class="fw-semibold">Mobile Money</span>
                                </label>
                                <label
                                    class="payment-option"
                                    :class="{
                                        'payment-option--active': form.payment_method === 'bank_transfer',
                                    }"
                                >
                                    <input v-model="form.payment_method" class="form-check-input" type="radio" value="bank_transfer" />
                                    <i class="bi bi-bank fs-4"></i>
                                    <span class="fw-semibold">Bank Transfer</span>
                                </label>
                            </div>
                            <div v-if="form.errors.payment_method" class="text-danger small mt-2">
                                {{ form.errors.payment_method }}
                            </div>
                        </div>

                        <!-- Agreements Section -->
                        <div class="form-card mb-4">
                            <h2 class="h4 fw-bold mb-4">Agreements</h2>

                            <div class="form-check mb-3">
                                <input
                                    v-model="form.code_of_conduct_agreed"
                                    class="form-check-input"
                                    type="checkbox"
                                    id="code_of_conduct"
                                    :class="{ 'is-invalid': form.errors.code_of_conduct_agreed }"
                                    required
                                />
                                <label class="form-check-label" for="code_of_conduct">
                                    <strong>
                                        I agree to the Code of Conduct
                                        <span class="text-danger">*</span>
                                    </strong>
                                    <small class="d-block text-muted">
                                        I understand and agree to abide by Score Beyond Leadership's code of conduct
                                    </small>
                                </label>
                                <div v-if="form.errors.code_of_conduct_agreed" class="invalid-feedback d-block">
                                    {{ form.errors.code_of_conduct_agreed }}
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
                                        I consent to being photographed and featured in Score Beyond Leadership's media and promotional materials
                                    </small>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3 mb-5">
                            <Link class="btn btn-outline-secondary rounded-pill px-4" :href="route('volunteer.index')">
                                Back to Programs
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
.cursor-pointer {
    cursor: pointer;
}

.form-card {
    background: white;
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.form-check-card {
    border: 1px solid #e5e7eb;
    border-radius: 1rem;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    width: 100%;
    cursor: pointer;
}

.form-check-card--active {
    border-color: #f03733;
    box-shadow: 0 0.5rem 1.5rem rgba(240, 55, 51, 0.15);
}

.form-check-card input {
    margin-top: 0.35rem;
}

.payment-option {
    border: 1px solid #e5e7eb;
    border-radius: 1rem;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
}

.payment-option--active {
    border-color: #f03733;
    box-shadow: 0 0.5rem 1.5rem rgba(240, 55, 51, 0.15);
}

.payment-option input {
    margin-top: 0.1rem;
}
</style>

