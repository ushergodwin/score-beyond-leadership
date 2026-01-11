<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps<{
    application: {
        id: number;
        status: string;
        program_type: string;
        preferred_name: string | null;
        first_name: string;
        last_name: string;
        date_of_birth: string | null;
        nationality: string | null;
        email: string;
        phone: string;
        country_of_residence: string | null;
        city: string | null;
        preferred_volunteer_role: string | null;
        preferred_roles: string[] | null;
        availability_start: string | null;
        availability_end: string | null;
        length_of_stay_weeks: number | null;
        tshirt_size: string | null;
        skills_experience: string | null;
        payment_status: string;
        created_at: string;
        updated_at: string;
    };
}>();

const getStatusBadgeClass = (status: string) => {
    return {
        'badge bg-success-subtle text-success': status === 'approved',
        'badge bg-info-subtle text-info': status === 'reviewing',
        'badge bg-warning-subtle text-warning': status === 'submitted' || status === 'pending',
        'badge bg-danger-subtle text-danger': status === 'rejected',
    };
};

const getProgramTypeBadgeClass = (type: string) => {
    return {
        'badge bg-primary-subtle text-primary': type === 'paid',
        'badge bg-secondary-subtle text-secondary': type === 'unpaid',
    };
};

const formatDate = (dateString: string | null): string => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    }).format(date);
};
</script>

<template>
    <Head :title="`Volunteer Application #${application.id}`" />
    <DashboardLayout>
        <div class="container-fluid py-4 px-3 px-md-4">
            <!-- Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <Link :href="route('dashboard.volunteer-applications')" class="text-decoration-none text-muted mb-2 d-inline-flex align-items-center gap-2">
                        <i class="bi bi-arrow-left"></i>
                        <span>Back to Applications</span>
                    </Link>
                    <h1 class="h3 fw-bold text-primary mb-2 mt-2">Application #{{ application.id }}</h1>
                    <div class="d-flex flex-wrap gap-2">
                        <span :class="['badge rounded-pill fw-semibold', getStatusBadgeClass(application.status)]">
                            {{ application.status }}
                        </span>
                        <span :class="['badge rounded-pill fw-semibold', getProgramTypeBadgeClass(application.program_type)]">
                            {{ application.program_type === 'paid' ? 'Paid Program' : 'Unpaid Program' }}
                        </span>
                        <span v-if="application.payment_status !== 'not_required'" class="badge bg-info-subtle text-info rounded-pill fw-semibold">
                            Payment: {{ application.payment_status }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Personal Information -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 p-4">
                            <h2 class="h5 fw-bold mb-0">Personal Information</h2>
                        </div>
                        <div class="card-body p-4">
                            <dl class="row mb-0">
                                <dt class="col-sm-5 text-muted">Name</dt>
                                <dd class="col-sm-7 fw-semibold">{{ application.first_name }} {{ application.last_name }}</dd>

                                <dt v-if="application.preferred_name" class="col-sm-5 text-muted">Preferred Name</dt>
                                <dd v-if="application.preferred_name" class="col-sm-7">{{ application.preferred_name }}</dd>

                                <dt class="col-sm-5 text-muted">Email</dt>
                                <dd class="col-sm-7">{{ application.email }}</dd>

                                <dt class="col-sm-5 text-muted">Phone</dt>
                                <dd class="col-sm-7">{{ application.phone }}</dd>

                                <dt v-if="application.date_of_birth" class="col-sm-5 text-muted">Date of Birth</dt>
                                <dd v-if="application.date_of_birth" class="col-sm-7">{{ formatDate(application.date_of_birth) }}</dd>

                                <dt v-if="application.nationality" class="col-sm-5 text-muted">Nationality</dt>
                                <dd v-if="application.nationality" class="col-sm-7">{{ application.nationality }}</dd>

                                <dt v-if="application.country_of_residence" class="col-sm-5 text-muted">Country of Residence</dt>
                                <dd v-if="application.country_of_residence" class="col-sm-7">{{ application.country_of_residence }}</dd>

                                <dt v-if="application.city" class="col-sm-5 text-muted">City</dt>
                                <dd v-if="application.city" class="col-sm-7">{{ application.city }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Volunteer Details -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 p-4">
                            <h2 class="h5 fw-bold mb-0">Volunteer Details</h2>
                        </div>
                        <div class="card-body p-4">
                            <dl class="row mb-0">
                                <dt v-if="application.preferred_volunteer_role" class="col-sm-5 text-muted">Preferred Role</dt>
                                <dd v-if="application.preferred_volunteer_role" class="col-sm-7 fw-semibold">{{ application.preferred_volunteer_role }}</dd>

                                <dt v-if="application.preferred_roles && application.preferred_roles.length > 0" class="col-sm-5 text-muted">Other Roles</dt>
                                <dd v-if="application.preferred_roles && application.preferred_roles.length > 0" class="col-sm-7">
                                    <span v-for="(role, index) in application.preferred_roles" :key="role" class="badge bg-light text-dark me-1">
                                        {{ role }}{{ index < application.preferred_roles.length - 1 ? ',' : '' }}
                                    </span>
                                </dd>

                                <dt v-if="application.availability_start" class="col-sm-5 text-muted">Availability Start</dt>
                                <dd v-if="application.availability_start" class="col-sm-7">{{ formatDate(application.availability_start) }}</dd>

                                <dt v-if="application.availability_end" class="col-sm-5 text-muted">Availability End</dt>
                                <dd v-if="application.availability_end" class="col-sm-7">{{ formatDate(application.availability_end) }}</dd>

                                <dt v-if="application.length_of_stay_weeks" class="col-sm-5 text-muted">Length of Stay</dt>
                                <dd v-if="application.length_of_stay_weeks" class="col-sm-7">{{ application.length_of_stay_weeks }} week(s)</dd>

                                <dt v-if="application.tshirt_size" class="col-sm-5 text-muted">T-Shirt Size</dt>
                                <dd v-if="application.tshirt_size" class="col-sm-7">{{ application.tshirt_size }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Skills & Experience -->
                <div v-if="application.skills_experience" class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 p-4">
                            <h2 class="h5 fw-bold mb-0">Skills & Experience</h2>
                        </div>
                        <div class="card-body p-4">
                            <p class="mb-0 text-muted">{{ application.skills_experience }}</p>
                        </div>
                    </div>
                </div>

                <!-- Application Timeline -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 p-4">
                            <h2 class="h5 fw-bold mb-0">Application Timeline</h2>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Application Submitted</div>
                                        <div class="text-muted small">{{ new Date(application.created_at).toLocaleString() }}</div>
                                    </div>
                                </div>
                                <div v-if="application.status !== 'submitted'" class="d-flex gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Status Updated</div>
                                        <div class="text-muted small">{{ new Date(application.updated_at).toLocaleString() }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

