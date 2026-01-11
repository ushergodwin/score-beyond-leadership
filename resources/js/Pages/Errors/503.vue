<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import LandingLayout from '@/Layouts/LandingLayout.vue';

const reload = () => {
    window.location.reload();
};
</script>

<template>
    <Head title="Service Unavailable" />
    <LandingLayout>
        <template #hero>
            <section class="py-5 section-bg-gradient-dark border-bottom text-white">
                <div class="container text-center">
                    <h1 class="display-1 fw-bold mb-3">503</h1>
                    <p class="lead mb-0 text-white-50">
                        We'll be back shortly.
                    </p>
                </div>
            </section>
        </template>

        <div class="container pt-4 pb-5">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="mb-5">
                        <i class="bi bi-tools display-1 text-warning mb-4"></i>
                        <h2 class="h3 fw-bold mb-3">Service Temporarily Unavailable</h2>
                        <p class="lead text-muted mb-4">
                            We're currently performing scheduled maintenance to improve your experience.
                        </p>
                        <p class="text-muted">
                            We should be back online shortly. Thank you for your patience!
                        </p>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <Link :href="route('home')" class="btn btn-primary btn-lg rounded-pill px-4">
                            <i class="bi bi-house me-2"></i>
                            Go to Homepage
                        </Link>
                        <button @click="reload" class="btn btn-outline-primary btn-lg rounded-pill px-4">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            Refresh Page
                        </button>
                    </div>

                    <div class="mt-5">
                        <p class="text-muted small">
                            Error Code: 503 | Maintenance Mode
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </LandingLayout>
</template>

