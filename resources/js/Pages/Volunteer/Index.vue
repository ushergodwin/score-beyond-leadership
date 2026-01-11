<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import LandingLayout from "@/Layouts/LandingLayout.vue";

type Program = {
    slug: "paid" | "unpaid";
    title: string;
    badge: string;
    summary: string;
    description: string;
    benefits: string[];
    logistics: string[];
};

const props = defineProps<{
    programs: Program[];
}>();
</script>

<template>
    <Head title="Volunteer Programs" />
    <LandingLayout>
        <template #hero>
            <section class="py-5 section-bg-gradient-dark border-bottom text-white">
                <div class="container">
                    <h1 class="display-4 fw-bold mb-3">Volunteer with Us</h1>
                    <p class="lead mb-0 text-white-50">
                        Joining Score Beyond Leadership as a volunteer means contributing to meaningful, community-centered impact.
                    </p>
                </div>
            </section>
        </template>

        <section class="container pt-4 pb-5">
            <div class="row align-items-center gy-4">
                <div class="col-lg-7">
                    <h2 class="h3 fw-bold mb-3">Why Volunteer?</h2>
                    <p class="text-muted mb-4">
                        Volunteers work directly with communities mentoring girls and women, coaching sports programs, supporting events, and helping deliver leadership and life-skills training. Your involvement strengthens our mission of empowering youth through structured development and sustainable opportunities.
                    </p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-chip">
                                <i class="bi bi-people-fill"></i>
                                Hands-on experience in program implementation
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-chip">
                                <i class="bi bi-lightning-charge"></i>
                                Leadership development through real community engagement
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-chip">
                                <i class="bi bi-graph-up-arrow"></i>
                                Contribution to measurable community impact
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-chip">
                                <i class="bi bi-journal-text"></i>
                                Certificate and recommendation upon successful completion
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="stat-card h-100">
                        <div>
                            <h3 class="h5 fw-bold mb-3 text-white">Who Can Volunteer?</h3>
                            <p class="text-white-50 mb-3">
                                We welcome students, professionals, and community leaders. Roles vary depending on skills and interest areas and may include:
                            </p>
                            <ul class="list-unstyled text-white-50 mb-0">
                                <li class="mb-2"><i class="bi bi-check-circle me-2"></i>Sports coaching</li>
                                <li class="mb-2"><i class="bi bi-check-circle me-2"></i>Event coordination</li>
                                <li class="mb-2"><i class="bi bi-check-circle me-2"></i>Community outreach</li>
                                <li class="mb-2"><i class="bi bi-check-circle me-2"></i>Mentorship</li>
                                <li class="mb-2"><i class="bi bi-check-circle me-2"></i>Fundraising support</li>
                                <li class="mb-0"><i class="bi bi-check-circle me-2"></i>Administrative support</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="container pb-5">
            <div class="row g-4">
                <div v-for="program in programs" :key="program.slug" class="col-lg-6">
                    <div class="program-card h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge badge-gradient">{{ program.badge }}</span>
                            <span class="text-muted text-uppercase small">
                                {{ program.slug === "paid" ? "Stipend + Structured placement" : "Service learning opportunity" }}
                            </span>
                        </div>
                        <h3 class="h4 fw-bold">{{ program.title }}</h3>
                        <p class="text-muted mb-2">{{ program.summary }}</p>
                        <p>{{ program.description }}</p>

                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <h4 class="h6 text-uppercase text-muted">Highlights</h4>
                                <ul class="list-unstyled mb-0">
                                    <li v-for="benefit in program.benefits" :key="benefit" class="d-flex gap-2 mb-2">
                                        <i class="bi bi-check2-circle text-success"></i>
                                        <span>{{ benefit }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h4 class="h6 text-uppercase text-muted">Logistics</h4>
                                <ul class="list-unstyled mb-0">
                                    <li v-for="item in program.logistics" :key="item" class="d-flex gap-2 mb-2">
                                        <i class="bi bi-calendar2-week text-primary"></i>
                                        <span>{{ item }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <Link
                                class="btn btn-primary rounded-pill px-4 w-100"
                                :href="route('volunteer.apply', { program: program.slug })"
                            >
                                {{ program.slug === "paid" ? "Apply for the Paid Fellowship" : "Apply for the Unpaid Program" }}
                            </Link>
                        </div>
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
    padding: 2rem;
    box-shadow: 0 1.5rem 3rem rgba(20, 30, 55, 0.08);
}

.badge-gradient {
    background: linear-gradient(135deg, #a01d62 0%, #f03733 50%, #f89f3d 100%);
    color: #fff;
    padding: 0.35rem 1rem;
    border-radius: 999px;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
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

.border-white-25 {
    border-color: rgba(255, 255, 255, 0.25) !important;
}
</style>