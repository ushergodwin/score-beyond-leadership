<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import LandingLayout from '@/Layouts/LandingLayout.vue';

const props = defineProps<{
    application: {
        id: number;
        first_name: string;
        last_name: string;
        email: string;
        program_type: string;
        status: string;
        submitted_at: string;
    };
}>();

const programTypeLabel = props.application.program_type === 'paid' ? 'Paid Program' : 'Unpaid Volunteer';
</script>

<template>
    <Head title="Volunteer Application Submitted" />
    <LandingLayout>
        <template #hero>
            <section class="py-5 bg-gradient-primary text-white">
                <div class="container">
                    <h1 class="display-4 fw-bold mb-3">Application Submitted!</h1>
                    <p class="lead mb-0">
                        Thank you for your interest in volunteering with Score Beyond Leadership.
                    </p>
                </div>
            </section>
        </template>

        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4 p-lg-5 text-center">
                            <div class="mb-4">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            </div>

                            <h2 class="h3 fw-bold mb-3">Your Application Has Been Received</h2>
                            <p class="text-muted mb-4">
                                We've received your volunteer application and will review it shortly. You will receive a confirmation email at
                                <strong>{{ application.email }}</strong> with further details.
                            </p>

                            <div class="alert alert-light rounded-4 mb-4 text-start" role="alert">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Application ID</small>
                                        <strong>#{{ application.id }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Program Type</small>
                                        <strong>{{ programTypeLabel }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Applicant</small>
                                        <strong>{{ application.first_name }} {{ application.last_name }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Submitted</small>
                                        <strong>{{ application.submitted_at }}</strong>
                                    </div>
                                    <div class="col-12">
                                        <small class="text-muted d-block">Status</small>
                                        <span class="badge bg-info">{{ application.status }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info rounded-4 mb-4 text-start">
                                <h5 class="fw-bold mb-2">What's Next?</h5>
                                <ul class="mb-0">
                                    <li>Our team will review your application within 5-7 business days</li>
                                    <li>You'll receive an email notification once your application has been reviewed</li>
                                    <li>If approved, we'll contact you to discuss next steps and scheduling</li>
                                    <li>For questions, contact us at +256 772 319503 or info@scorebeyondleadership.org</li>
                                </ul>
                            </div>

                            <div class="d-flex flex-wrap gap-3 justify-content-center mt-4">
                                <Link
                                    class="btn btn-primary rounded-pill px-4 text-uppercase fw-semibold"
                                    :href="route('volunteer.index')"
                                >
                                    Submit Another Application
                                </Link>
                                <Link
                                    class="btn btn-outline-primary rounded-pill px-4 text-uppercase fw-semibold"
                                    :href="route('shop.index')"
                                >
                                    Visit Our Shop
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </LandingLayout>
</template>

<style scoped>
.bg-gradient-primary {
    background: linear-gradient(135deg, #a01d62 0%, #f03733 100%);
}
</style>

