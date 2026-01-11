<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
// @ts-ignore - AOS types
import AOS from 'aos';
import LandingLayout from '@/Layouts/LandingLayout.vue';
import transformingLivesImage from '../../../images/transforming-lives-through-sport.webp';
import transformingLivesBgImage from '../../../images/transforming-lives-through-sport-development-and-leadership.jpg';
import communityImpactImage from '../../../images/community-impact.webp';
import projectsCompletedImage from '../../../images/projects-completed.webp';
import youthEmpowermentImage from '../../../images/youth-empowerment.webp';

type Project = {
    id: number;
    title: string;
    description?: string;
    location?: string;
    image_url: string;
    image_alt: string;
};

type FocusArea = {
    id: number;
    title: string;
    description?: string;
    image_url: string;
    image_alt: string;
};

type SuccessStory = {
    id: number;
    title: string;
    description: string;
    quote?: string;
    image_url: string;
    image_alt: string;
    has_more?: boolean;
};

const props = defineProps<{
    projects?: Project[];
    focusAreas?: FocusArea[];
    successStories?: SuccessStory[];
}>();

onMounted(() => {
    AOS.refresh();
});
</script>

<template>
    <Head title="Home - Score Beyond Leadership" />
    <LandingLayout>
        <!-- Hero Section -->
        <template #hero>
            <section 
                class="hero-section"
                :style="{ backgroundImage: `url(${transformingLivesBgImage})` }"
            >
                <div class="container position-relative" style="z-index: 2; max-width: 100%; min-height: 100vh; display: flex; align-items: center;">
                    <div class="row align-items-center g-0 w-100">
                        <div class="col-lg-8 mx-auto text-center text-white px-3" data-aos="fade-up" data-aos-duration="800">
                            <h1 class="display-3 fw-bold mb-4 hero-title">
                                Transforming Lives Through Sport Development and Leadership
                            </h1>
                            <p class="lead mb-5 hero-subtitle">
                                Help us create lasting change by empowering women and girls through promoting education, health, well being, livelihood and the skilling needed to thrive.
                            </p>
                            <div class="row g-3 justify-content-center">
                                <div class="col-md-6 col-lg-5">
                                    <Link
                                        :href="route('donate.index')"
                                        class="btn btn-warning btn-lg rounded-pill px-5 fw-bold shadow-lg w-100"
                                        style="background: linear-gradient(135deg, #f89f3d 0%, #f03733 100%); border: none;"
                                    >
                                        <i class="bi bi-heart-fill me-2"></i>
                                        Donate Now
                                    </Link>
                                </div>
                                <div class="col-md-6 col-lg-5">
                                    <Link
                                        :href="route('volunteer.index')"
                                        class="btn btn-light btn-lg rounded-pill px-5 fw-bold shadow-lg w-100"
                                    >
                                        <i class="bi bi-people-fill me-2"></i>
                                        Get Involved
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </template>

        <!-- About Us Section -->
        <section id="about" class="py-5" style="overflow-x: hidden;">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 mx-auto text-center mb-5" data-aos="fade-up" data-aos-duration="800">
                        <h2 class="h3 text-uppercase text-muted mb-3">About Us</h2>
                        <h3 class="display-4 fw-bold mb-4">The Core Mission Behind All Our Work</h3>
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <img
                                    :src="transformingLivesImage"   
                                    alt="Transforming Lives Through Sport Development and Leadership"
                                    class="img-fluid rounded-3"
                                    style="aspect-ratio: 3/2; object-fit: cover; width: 100%;"
                                    loading="lazy"
                                />
                            </div>
                            <div class="col-md-6">
                                <p class="lead text-muted fs-5 text-start">
                                    Score Beyond Leadership Organization is a sports and development organization founded in December 2015, dedicated to creating lasting change in communities. We use sports as a platform to inspire and mobilize women and girls, particularly those who are underprivileged. SB is committed to fostering human capital development by empowering leadership, promoting education retention, enhancing health and well-being, and providing livelihood opportunities through skill development.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-4 mb-5">
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="card h-100 shadow-sm border-0 text-center">
                            <img
                                :src="communityImpactImage"
                                alt="Community Impact"
                                class="card-img-top stat-image"
                                loading="lazy"
                            />
                            <div class="card-body">
                                <div class="display-3 fw-bold text-primary mb-3">5000+</div>
                                <h4 class="card-title h5 fw-semibold mb-2">Beneficiaries Impacted</h4>
                                <p class="card-text text-muted mb-0 small">Lives transformed through our programs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="card h-100 shadow-sm border-0 text-center">
                            <img
                                :src="projectsCompletedImage"
                                alt="Projects Completed"
                                class="card-img-top stat-image"
                                loading="lazy"
                            />
                            <div class="card-body">
                                <div class="display-3 fw-bold text-primary mb-3">15+</div>
                                <h4 class="card-title h5 fw-semibold mb-2">Projects Completed</h4>
                                <p class="card-text text-muted mb-0 small">Successful initiatives across Uganda</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="card h-100 shadow-sm border-0 text-center">
                            <img
                                :src="youthEmpowermentImage"
                                alt="Youth Empowerment"
                                class="card-img-top stat-image"
                                loading="lazy"
                            />
                            <div class="card-body">
                                <div class="display-3 fw-bold text-primary mb-3">100+</div>
                                <h4 class="card-title h5 fw-semibold mb-2">Communities Reached</h4>
                                <p class="card-text text-muted mb-0 small">Active programs across regions</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Themes Section -->
        <section id="focus-areas" class="py-5 bg-light mb-5" style="overflow-x: hidden;">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-lg-8 mx-auto text-center" data-aos="fade-up" data-aos-duration="800">
                        <h2 class="h3 text-uppercase text-muted mb-3">Our Focus Areas</h2>
                        <h3 class="display-5 fw-bold mb-4">Themes Of The Score Beyond Leadership Organization</h3>
                        <p class="lead text-muted">
                            Explore the core themes that drive Score Beyond's mission. Each theme represents a focus area where we dedicate our efforts to empower communities through education, sustainable livelihoods, and health initiatives.
                        </p>
                    </div>
                </div>
                <div class="row g-4">
                    <div
                        v-for="(area, index) in props.focusAreas"
                        :key="area.id"
                        class="col-md-6 col-lg-4"
                        :data-aos="'fade-up'"
                        :data-aos-delay="(index % 3 + 1) * 100"
                    >
                        <div class="card h-100 shadow-sm border-0 text-center">
                            <img
                                :src="area.image_url"
                                :alt="area.image_alt"
                                class="card-img-top focus-area-image"
                                loading="lazy"
                            />
                            <div class="card-body">
                                <h4 class="card-title h5 fw-bold mb-3">{{ area.title }}</h4>
                                <p v-if="area.description" class="card-text text-muted mb-0">{{ area.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Projects Section -->
        <section class="py-5" style="overflow-x: hidden;">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-lg-8 mx-auto text-center" data-aos="fade-up" data-aos-duration="800">
                        <h2 class="h3 text-uppercase text-muted mb-3">Our Work</h2>
                        <h3 class="display-5 fw-bold mb-4">Showcasing Some Of Our Projects</h3>
                    </div>
                </div>
                <div class="row g-4">
                    <div
                        v-for="(project, index) in props.projects"
                        :key="project.id"
                        class="col-md-6 col-lg-4 col-xl-3"
                        :data-aos="'fade-up'"
                        :data-aos-delay="(index % 4 + 1) * 100"
                    >
                        <div class="card h-100 shadow-sm border-0">
                            <img
                                :src="project.image_url"
                                :alt="project.image_alt"
                                class="card-img-top project-image"
                                loading="lazy"
                            />
                            <div class="card-body">
                                <h4 class="card-title h6 fw-bold mb-2">{{ project.title }}</h4>
                                <p v-if="project.location" class="card-text text-muted small mb-2">
                                    <i class="bi bi-geo-alt me-1"></i> {{ project.location }}
                                </p>
                                <p v-if="project.description" class="card-text text-muted small mb-0">{{ project.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Success Stories Section -->
        <section id="success-stories" class="py-5 bg-light" style="overflow-x: hidden;">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-lg-8 mx-auto text-center" data-aos="fade-up" data-aos-duration="800">
                        <h2 class="h3 text-uppercase text-muted mb-3">Impact Stories</h2>
                        <h3 class="display-5 fw-bold mb-4">Success Stories At The SBLO</h3>
                        <p class="lead text-muted">
                            At Score Beyond Leadership Organization, every individual story is a testament to the power of sports to change lives. Here are a few of the many inspiring success stories:
                        </p>
                    </div>
                </div>
                <div class="success-stories-grid">
                    <div
                        v-for="(story, index) in props.successStories"
                        :key="story.id"
                        :data-aos="'fade-up'"
                        :data-aos-delay="(index % 4 + 1) * 100"
                    >
                        <div class="card h-100 shadow-sm border-0">
                            <img
                                :src="story.image_url"
                                :alt="story.image_alt"
                                class="card-img-top project-image"
                                loading="lazy"
                            />
                            <div class="card-body d-flex flex-column">
                                <h4 class="card-title h6 fw-bold mb-2">{{ story.title }}</h4>
                                <p v-if="story.quote" class="card-text text-muted mb-2" style="font-size: 0.875rem;">
                                    <em>{{ story.quote }}</em>
                                </p>
                                <p class="card-text text-muted mb-3 flex-grow-1" style="font-size: 0.9rem; line-height: 1.6;">
                                    {{ story.description }}
                                </p>
                                <Link
                                    v-if="story.has_more"
                                    :href="route('success-stories.show', story.id)"
                                    class="btn btn-sm btn-outline-primary rounded-pill mt-auto"
                                >
                                    Read Full Story
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Get Involved Section -->
        <section class="py-5" style="overflow-x: hidden;">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-lg-8 mx-auto text-center" data-aos="fade-up" data-aos-duration="800">
                        <h2 class="h3 text-uppercase text-muted mb-3">Join Us</h2>
                        <h3 class="display-5 fw-bold mb-4">Get Involved</h3>
                        <p class="lead text-muted">
                            Join us in making a difference! Whether you are a volunteer, a donor, or a partner, your support is essential to helping us continue our mission of empowering women and girls through sports.
                        </p>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
                        <div class="page-section h-100 text-center">
                            <div class="mb-4">
                                <i class="bi bi-people-fill display-1 text-primary"></i>
                            </div>
                            <h4 class="h5 fw-bold mb-3">Volunteer Your Time</h4>
                            <p class="text-muted mb-4">Make a direct impact by volunteering with our programs and initiatives.</p>
                            <Link
                                :href="route('volunteer.index')"
                                class="btn btn-outline-primary rounded-pill px-4"
                            >
                                Become a Volunteer
                            </Link>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
                        <div class="page-section h-100 text-center">
                            <div class="mb-4">
                                <i class="bi bi-heart-fill display-1 text-primary"></i>
                            </div>
                            <h4 class="h5 fw-bold mb-3">Donate</h4>
                            <p class="text-muted mb-4">Support our mission with a financial contribution that creates lasting change.</p>
                            <Link
                                :href="route('donate.index')"
                                class="btn btn-warning rounded-pill px-4"
                                style="background: linear-gradient(135deg, #f89f3d 0%, #f03733 100%); border: none;"
                            >
                                Donate Now
                            </Link>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
                        <div class="page-section h-100 text-center">
                            <div class="mb-4">
                                <i class="bi bi-hand-thumbs-up-fill display-1 text-primary"></i>
                            </div>
                            <h4 class="h5 fw-bold mb-3">Partner with Us</h4>
                            <p class="text-muted mb-4">Collaborate with us to expand our reach and impact in communities.</p>
                            <a
                                href="mailto:info@scorebeyondleadership.org"
                                class="btn btn-outline-primary rounded-pill px-4"
                            >
                                Contact Us
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </LandingLayout>
</template>

<style scoped>
.min-vh-75 {
    min-height: 75vh;
}

/* Prevent overflow issues with animations and transforms */
section:not(.hero-section) {
    overflow: visible !important;
    overflow-y: visible !important;
    overflow-x: hidden !important;
    height: auto !important;
    max-height: none !important;
    min-height: auto !important;
    position: relative;
}

/* Ensure card images are properly contained */
.card-img-top {
    display: block;
}

/* Ensure containers properly contain animated content */
.container {
    overflow-x: hidden;
    position: relative;
    overflow-y: visible;
}

/* Ensure rows don't cause overflow */
.row {
    overflow-x: hidden;
    overflow-y: visible;
}

/* Prevent AOS animations from causing vertical overflow */
[data-aos] {
    will-change: transform;
    backface-visibility: hidden;
    transform: translateZ(0);
    -webkit-transform: translateZ(0);
    overflow: visible !important;
    position: relative;
}

/* Contain AOS animated elements within their parent */
[data-aos].aos-animate {
    overflow: visible !important;
}

/* Prevent zoom-in animations from causing overflow */
[data-aos="zoom-in"] {
    transform-origin: center center;
}

/* Ensure fade animations don't cause layout shifts */
[data-aos^="fade"] {
    opacity: 0;
    transition-property: opacity, transform;
}

[data-aos^="fade"].aos-animate {
    opacity: 1;
}

.hero-section {
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: scroll;
    padding: 0;
    position: relative;
    width: 100vw;
    max-width: 100vw;
    margin-left: calc(-50vw + 50%);
    margin-right: calc(-50vw + 50%);
    margin-top: 0;
    margin-bottom: 0;
    min-height: 100vh;
    filter: none;
    backdrop-filter: none;
    z-index: 0;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        135deg,
        rgba(160, 29, 98, 0.35) 0%,
        rgba(239, 65, 51, 0.3) 50%,
        rgba(248, 159, 61, 0.3) 100%
    );
    z-index: 1;
}

.hero-title {
    text-shadow: 0.125rem 0.125rem 0.75rem rgba(0, 0, 0, 0.7), 0 0 1rem rgba(0, 0, 0, 0.5);
    color: #ffffff;
    line-height: 1.2;
    font-weight: 800;
    font-size: clamp(2rem, 5vw, 3.5rem);
    letter-spacing: -0.02em;
}

.hero-subtitle {
    color: #ffffff;
    text-shadow: 0.0625rem 0.0625rem 0.5rem rgba(0, 0, 0, 0.7), 0 0 0.75rem rgba(0, 0, 0, 0.4);
    font-size: clamp(1.125rem, 2vw, 1.375rem);
    font-weight: 500;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .hero-section {
        background-attachment: scroll;
        padding-top: 4rem;
        min-height: 100vh;
    }
}

/* Ensure cards contain scaled images */
.card {
    overflow: hidden;
}

.project-image {
    aspect-ratio: 4 / 3;
    object-fit: cover;
    transition: transform 0.3s ease;
    width: 100%;
}

.card:hover .project-image {
    transform: scale(1.1);
}

.focus-area-image {
    aspect-ratio: 3 / 2;
    object-fit: cover;
    transition: transform 0.3s ease;
    width: 100%;
}

.card:hover .focus-area-image {
    transform: scale(1.1);
}

.stat-image {
    aspect-ratio: 4 / 3;
    object-fit: cover;
    transition: transform 0.3s ease;
    width: 100%;
}

.card:hover .stat-image {
    transform: scale(1.1);
}

/* Success Stories Grid - Adapts to number of items */
.success-stories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

/* When there are only 1-2 items, they take more space */
@media (min-width: 768px) {
    .success-stories-grid {
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    }
}

@media (min-width: 992px) {
    .success-stories-grid {
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 2rem;
    }
}

/* Ensure cards maintain consistent height */
.success-stories-grid .card {
    display: flex;
    flex-direction: column;
}
</style>

