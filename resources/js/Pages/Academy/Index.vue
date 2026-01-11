<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import LandingLayout from "@/Layouts/LandingLayout.vue";

type Offering = {
    icon?: string;
    label: string;
};

const props = defineProps<{
    hero_title: string;
    hero_subtitle: string;
    offers_heading?: string;
    offers_description: string;
    offerings?: Offering[];
    location?: string;
    why_matters_heading?: string;
    why_matters_description: string;
    join_heading?: string;
    join_description: string;
    join_cta_text?: string;
}>();
</script>

<template>
    <Head :title="props.hero_title" />
    <LandingLayout>
        <template #hero>
            <section class="py-5 section-bg-gradient-dark border-bottom text-white">
                <div class="container">
                    <h1 class="display-4 fw-bold mb-3">{{ props.hero_title }}</h1>
                    <p class="lead mb-0 text-white-50">
                        {{ props.hero_subtitle }}
                    </p>
                </div>
            </section>
        </template>

        <section class="container pt-4 pb-5">
            <div class="row align-items-center gy-4">
                <div class="col-lg-7">
                    <h2 v-if="props.offers_heading" class="h3 fw-bold mb-3">{{ props.offers_heading }}</h2>
                    <p class="text-muted mb-4">
                        {{ props.offers_description }}
                    </p>
                    <div v-if="props.offerings && props.offerings.length > 0" class="row g-3 mb-4">
                        <div v-for="(offering, index) in props.offerings" :key="index" class="col-md-6">
                            <div class="info-chip">
                                <i v-if="offering.icon" :class="`bi ${offering.icon}`"></i>
                                {{ offering.label }}
                            </div>
                        </div>
                        <div v-if="props.location" class="col-md-6">
                            <div class="info-chip">
                                <i class="bi bi-geo-alt-fill"></i>
                                Location: {{ props.location }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="stat-card h-100">
                        <div>
                            <h3 v-if="props.why_matters_heading" class="h5 fw-bold mb-3 text-white">{{ props.why_matters_heading }}</h3>
                            <p class="text-white-50 mb-0">
                                {{ props.why_matters_description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="container pb-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="program-card text-center">
                        <h3 v-if="props.join_heading" class="h4 fw-bold mb-3">{{ props.join_heading }}</h3>
                        <p class="text-muted mb-4">
                            {{ props.join_description }}
                        </p>
                        <Link
                            class="btn btn-primary btn-lg rounded-pill px-5"
                            :href="route('academy.apply')"
                        >
                            {{ props.join_cta_text || 'Apply Now' }}
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </LandingLayout>
</template>

<style scoped>
.program-card {
    background: white;
    border-radius: 1.25rem;
    padding: 3rem;
    box-shadow: 0 1.5rem 3rem rgba(20, 30, 55, 0.08);
}

.info-chip {
    border: 1px solid #e5e7eb;
    border-radius: 999px;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
}

.info-chip i {
    color: #f03733;
}

.stat-card {
    background: linear-gradient(135deg, #1b2028 0%, #313a49 100%);
    border-radius: 1.5rem;
    padding: 2.5rem;
    box-shadow: 0 1.5rem 3rem rgba(17, 17, 17, 0.35);
}
</style>

