<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import LandingLayout from '@/Layouts/LandingLayout.vue';

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <Head title="Page Not Found" />
    <LandingLayout>
        <template #hero>
            <section class="py-5 section-bg-gradient-dark border-bottom text-white">
                <div class="container text-center">
                    <h1 class="display-1 fw-bold mb-3">404</h1>
                    <p class="lead mb-0 text-white-50">
                        Oops! The page you're looking for doesn't exist.
                    </p>
                </div>
            </section>
        </template>

        <div class="container pt-4 pb-5">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="mb-5">
                        <i class="bi bi-exclamation-triangle display-1 text-muted mb-4"></i>
                        <h2 class="h3 fw-bold mb-3">Page Not Found</h2>
                        <p class="lead text-muted mb-4">
                            The page you're looking for might have been moved, deleted, or doesn't exist.
                        </p>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <Link :href="route('home')" class="btn btn-primary btn-lg rounded-pill px-4">
                            <i class="bi bi-house me-2"></i>
                            Go to Homepage
                        </Link>
                        <button @click="goBack" class="btn btn-outline-primary btn-lg rounded-pill px-4">
                            <i class="bi bi-arrow-left me-2"></i>
                            Go Back
                        </button>
                    </div>

                    <div class="mt-5">
                        <h3 class="h5 fw-semibold mb-3">Popular Pages</h3>
                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                            <Link :href="route('shop.index')" class="btn btn-sm btn-outline-secondary rounded-pill">
                                Shop
                            </Link>
                            <Link :href="route('volunteer.index')" class="btn btn-sm btn-outline-secondary rounded-pill">
                                Volunteer
                            </Link>
                            <Link :href="route('academy.index')" class="btn btn-sm btn-outline-secondary rounded-pill">
                                Academy
                            </Link>
                            <Link :href="route('donate.index')" class="btn btn-sm btn-outline-secondary rounded-pill">
                                Donate
                            </Link>
                            <Link :href="route('gallery.index')" class="btn btn-sm btn-outline-secondary rounded-pill">
                                Gallery
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </LandingLayout>
</template>

