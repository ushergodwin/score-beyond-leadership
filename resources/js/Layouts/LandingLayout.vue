<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import scoreBeyondLogo from '../../images/scorebeyond-white.webp';
import { showSuccess, showError, showInfo } from '../composables/useNotifications';
import { useForm } from '@inertiajs/vue3';

// Generate random numbers for bot check
const generateBotCheck = () => {
    const num1 = Math.floor(Math.random() * 9) + 1; // 1-9
    const num2 = Math.floor(Math.random() * 9) + 1; // 1-9
    return {
        num1,
        num2,
        answer: num1 + num2,
    };
};

const page = usePage();
const appName = computed(() => (page.props.appName as string | undefined) ?? 'Score Beyond');

// Newsletter subscription form
const newsletterForm = useForm({
    email: '',
    first_name: '',
    last_name: '',
    source: 'footer',
    bot_check: '',
});

const botCheck = ref(generateBotCheck());
const botCheckAnswer = ref('');
const botCheckError = ref('');

const subscribeNewsletter = () => {
    // Validate bot check
    const userAnswer = parseInt(botCheckAnswer.value, 10);
    if (isNaN(userAnswer) || userAnswer !== botCheck.value.answer) {
        botCheckError.value = 'Please answer the question correctly';
        return;
    }
    botCheckError.value = '';

    newsletterForm.bot_check = botCheckAnswer.value;
    newsletterForm.post(route('newsletter.subscribe'), {
        preserveScroll: true,
        onSuccess: () => {
            newsletterForm.reset();
            botCheckAnswer.value = '';
            botCheckError.value = '';
            // Generate new challenge for next submission
            botCheck.value = generateBotCheck();
            if (page.props.flash?.success) {
                showSuccess(page.props.flash.success as string);
            }
            if (page.props.flash?.info) {
                showInfo(page.props.flash.info as string);
            }
        },
        onError: () => {
            if (page.props.flash?.error) {
                showError(page.props.flash.error as string);
            }
        },
    });
};

// Smooth scroll to section
const scrollToSection = (sectionId: string) => {
    const currentUrl = window.location.pathname;
    const homeRoute = route('home');
    
    // If not on home page, navigate to home with anchor
    if (currentUrl !== homeRoute && !currentUrl.endsWith('/')) {
        window.location.href = `${homeRoute}#${sectionId}`;
        return;
    }
    
    // If on home page, scroll to section
    const element = document.getElementById(sectionId);
    if (element) {
        const headerOffset = 80;
        const elementPosition = element.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth',
        });
    } else {
        // If element not found, navigate to home with anchor
        window.location.href = `${homeRoute}#${sectionId}`;
    }
};

// Back to top button
const showBackToTop = ref(false);
const isMobileMenuOpen = ref(false);

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const handleScroll = () => {
    showBackToTop.value = window.scrollY > 300;
};

const scrollToTop = () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth',
    });
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    
    // Handle hash anchors on page load
    const hash = window.location.hash;
    if (hash) {
        const sectionId = hash.substring(1); // Remove the #
        setTimeout(() => {
            const element = document.getElementById(sectionId);
            if (element) {
                const headerOffset = 80;
                const elementPosition = element.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth',
                });
            }
        }, 100); // Small delay to ensure page is rendered
    }
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

// Watch for global flash messages
watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            showSuccess(flash.success as string);
        }
        if (flash?.error) {
            showError(flash.error as string);
        }
        if (flash?.message) {
            showInfo(flash.message as string);
        }
    },
    { deep: true, immediate: true },
);
</script>

<template>
    <div class="storefront-layout min-vh-100 d-flex flex-column bg-light-subtle" style="overflow-x: hidden; width: 100%; max-width: 100vw;">
        <header class="storefront-header border-bottom position-fixed top-0 start-0 end-0 z-3">
            <div class="container py-2 py-lg-3 d-flex align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2">
                    <Link class="text-decoration-none d-flex align-items-center" :href="route('home')">
                        <img
                            :src="scoreBeyondLogo"
                            alt="Score Beyond Leadership"
                            class="storefront-logo"
                        />
                    </Link>
                </div>
                <button
                    class="btn btn-link d-lg-none p-2 text-dark"
                    type="button"
                    @click="toggleMobileMenu"
                    aria-label="Toggle menu"
                >
                    <i class="bi" :class="isMobileMenuOpen ? 'bi-x-lg' : 'bi-list'" style="font-size: 1.5rem;"></i>
                </button>
                <nav class="d-none d-lg-flex align-items-center gap-2 flex-wrap">
                    <Link
                        class="nav-link-landing text-uppercase fw-semibold text-dark"
                        :href="route('home')"
                    >
                        Home
                    </Link>
                    <a
                        href="#about"
                        class="nav-link-landing text-uppercase fw-semibold text-dark"
                        @click.prevent="scrollToSection('about')"
                    >
                        About Us
                    </a>
                    <a
                        href="#focus-areas"
                        class="nav-link-landing text-uppercase fw-semibold text-dark"
                        @click.prevent="scrollToSection('focus-areas')"
                    >
                        Our Program
                    </a>
                    <a
                        href="#success-stories"
                        class="nav-link-landing text-uppercase fw-semibold text-dark"
                        @click.prevent="scrollToSection('success-stories')"
                    >
                        Success Stories
                    </a>
                    <Link
                        class="nav-link-landing text-uppercase fw-semibold text-dark"
                        :href="route('shop.index')"
                    >
                        Shop
                    </Link>
                    <Link
                        class="nav-link-landing text-uppercase fw-semibold text-dark"
                        :href="route('gallery.index')"
                    >
                        Gallery
                    </Link>
                    <Link
                        class="nav-link-landing text-uppercase fw-semibold text-dark"
                        :href="route('volunteer.index')"
                    >
                        Volunteer
                    </Link>
                    <Link
                        class="nav-link-landing text-uppercase fw-semibold text-dark"
                        :href="route('academy.index')"
                    >
                        Academy
                    </Link>
                    <Link
                        class="btn btn-warning rounded-pill px-3 py-1 text-uppercase fw-bold shadow-sm"
                        :href="route('donate.index')"
                        style="background: linear-gradient(135deg, #f89f3d 0%, #f03733 100%); border: none; font-size: 0.75rem; white-space: nowrap;"
                    >
                        <i class="bi bi-heart-fill me-1"></i>
                        Donate
                    </Link>
                    <Link
                        v-if="$page.props.auth?.user === null"
                        class="btn btn-outline-secondary rounded-pill px-3 py-1 text-uppercase fw-semibold"
                        :href="route('login')"
                        style="font-size: 0.75rem; white-space: nowrap;"
                    >
                        Login
                    </Link>
                    <Link
                        v-else
                        class="btn btn-outline-secondary rounded-pill px-3 py-1 text-uppercase fw-semibold"
                        :href="route('dashboard')"
                        style="font-size: 0.75rem; white-space: nowrap;"
                    >
                        Dashboard
                    </Link>
                </nav>
            </div>
            <!-- Mobile Menu -->
            <div v-if="isMobileMenuOpen" class="d-lg-none border-top bg-white">
                <div class="container py-3">
                    <div class="d-flex flex-column gap-2">
                        <Link
                            class="nav-link-mobile text-uppercase fw-semibold text-dark"
                            :href="route('home')"
                            @click="isMobileMenuOpen = false"
                        >
                            Home
                        </Link>
                        <a
                            href="#about"
                            class="nav-link-mobile text-uppercase fw-semibold text-dark"
                            @click.prevent="scrollToSection('about'); isMobileMenuOpen = false"
                        >
                            About Us
                        </a>
                        <a
                            href="#focus-areas"
                            class="nav-link-mobile text-uppercase fw-semibold text-dark"
                            @click.prevent="scrollToSection('focus-areas'); isMobileMenuOpen = false"
                        >
                            Our Program
                        </a>
                        <a
                            href="#success-stories"
                            class="nav-link-mobile text-uppercase fw-semibold text-dark"
                            @click.prevent="scrollToSection('success-stories'); isMobileMenuOpen = false"
                        >
                            Success Stories
                        </a>
                        <Link
                            class="nav-link-mobile text-uppercase fw-semibold text-dark"
                            :href="route('shop.index')"
                            @click="isMobileMenuOpen = false"
                        >
                            Shop
                        </Link>
                        <Link
                            class="nav-link-mobile text-uppercase fw-semibold text-dark"
                            :href="route('gallery.index')"
                            @click="isMobileMenuOpen = false"
                        >
                            Gallery
                        </Link>
                        <Link
                            class="nav-link-mobile text-uppercase fw-semibold text-dark"
                            :href="route('volunteer.index')"
                            @click="isMobileMenuOpen = false"
                        >
                            Volunteer
                        </Link>
                        <Link
                            class="nav-link-mobile text-uppercase fw-semibold text-dark"
                            :href="route('academy.index')"
                            @click="isMobileMenuOpen = false"
                        >
                            Academy
                        </Link>
                        <Link
                            class="btn btn-warning rounded-pill px-3 py-2 text-uppercase fw-bold shadow-sm mt-2"
                            :href="route('donate.index')"
                            style="background: linear-gradient(135deg, #f89f3d 0%, #f03733 100%); border: none;"
                            @click="isMobileMenuOpen = false"
                        >
                            <i class="bi bi-heart-fill me-2"></i>
                            Donate Now
                        </Link>
                        <Link
                            v-if="$page.props.auth?.user === null"
                            class="btn btn-outline-secondary rounded-pill px-3 py-2 text-uppercase fw-semibold mt-2"
                            :href="route('login')"
                            @click="isMobileMenuOpen = false"
                        >
                            Login
                        </Link>
                        <Link
                            v-else
                            class="btn btn-outline-secondary rounded-pill px-3 py-2 text-uppercase fw-semibold mt-2"
                            :href="route('dashboard')"
                            @click="isMobileMenuOpen = false"
                        >
                            Dashboard
                        </Link>
                    </div>
                </div>
            </div>
        </header>
        <main class="flex-grow-1 me-2 storefront-main">
            <slot name="hero" />
            <section style="padding-top: 6rem; overflow-x: hidden;">
                <slot />
            </section>
        </main>
        <footer class="storefront-footer text-white py-5 mt-auto">
            <div class="container">
                <!-- Newsletter Subscription Section -->
                <div class="row mb-5 pb-4 border-bottom border-white border-opacity-25">
                    <div class="col-lg-8 mx-auto text-center">
                        <h3 class="h4 fw-bold mb-2">Stay Connected</h3>
                        <p class="text-white-50 mb-4">
                            Subscribe to our newsletter to receive updates on programs, volunteer opportunities, and store drops.
                        </p>
                        <form @submit.prevent="subscribeNewsletter" class="row g-2 justify-content-center align-items-start">
                            <div class="col-md-4">
                                <input
                                    v-model="newsletterForm.first_name"
                                    type="text"
                                    class="form-control"
                                    placeholder="First Name"
                                    :class="{ 'is-invalid': newsletterForm.errors.first_name }"
                                />
                                <div v-if="newsletterForm.errors.first_name" class="invalid-feedback d-block text-start">
                                    {{ newsletterForm.errors.first_name }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <input
                                    v-model="newsletterForm.email"
                                    type="email"
                                    class="form-control"
                                    placeholder="Your Email Address"
                                    required
                                    :class="{ 'is-invalid': newsletterForm.errors.email }"
                                />
                                <div v-if="newsletterForm.errors.email" class="invalid-feedback d-block text-start">
                                    {{ newsletterForm.errors.email }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text" style="white-space: nowrap; font-size: 0.875rem;">{{ botCheck.num1 }} + {{ botCheck.num2 }} =</span>
                                    <input
                                        v-model.number="botCheckAnswer"
                                        type="number"
                                        class="form-control"
                                        placeholder="Answer"
                                        :class="{ 'is-invalid': botCheckError }"
                                    />
                                </div>
                                <div v-if="botCheckError" class="invalid-feedback d-block text-start">
                                    {{ botCheckError }}
                                </div>
                            </div>
                            <div class="col-md-auto d-flex align-items-start">
                                <button
                                    type="submit"
                                    class="btn btn-warning btn-lg fw-bold text-uppercase px-4"
                                    :disabled="newsletterForm.processing"
                                    style="background: linear-gradient(135deg, #f89f3d 0%, #f03733 100%); border: none; height: 38px; line-height: 1;"
                                >
                                    <span v-if="newsletterForm.processing" class="spinner-border spinner-border-sm me-2"></span>
                                    Subscribe
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Footer Content -->
                <div class="row gy-4">
                    <div class="col-lg-4 col-md-6">
                        <img
                            :src="scoreBeyondLogo"
                            alt="Score Beyond Leadership"
                            class="storefront-logo-footer mb-3"
                        />
                        <p class="text-white-50 mb-3">
                            Score Beyond Leadership Organization is a sports and development organization founded in December 2015, dedicated to creating lasting change in communities.
                        </p>
                        <div class="d-flex gap-3">
                            <a href="https://www.facebook.com" target="_blank" class="text-white-50" style="font-size: 1.5rem;">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://www.twitter.com" target="_blank" class="text-white-50" style="font-size: 1.5rem;">
                                <i class="bi bi-twitter"></i>
                            </a>
                            <a href="https://www.instagram.com" target="_blank" class="text-white-50" style="font-size: 1.5rem;">
                                <i class="bi bi-instagram"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3">
                        <div class="fw-semibold text-uppercase mb-3">Quick Links</div>
                        <ul class="list-unstyled text-white-50 d-flex flex-column gap-2 mb-0">
                            <li><Link class="text-white-50 text-decoration-none" :href="route('shop.index')">Shop</Link></li>
                            <li><Link class="text-white-50 text-decoration-none" :href="route('gallery.index')">Gallery</Link></li>
                            <li><Link class="text-white-50 text-decoration-none" :href="route('donate.index')">Donate</Link></li>
                            <li><Link class="text-white-50 text-decoration-none" :href="route('volunteer.index')">Volunteer</Link></li>
                            <li><Link class="text-white-50 text-decoration-none" :href="route('academy.index')">Academy</Link></li>
                            <li><a class="text-white-50 text-decoration-none" href="https://www.scorebeyondleadership.org/" target="_blank">About Us</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <div class="fw-semibold text-uppercase mb-3">Contact</div>
                        <ul class="list-unstyled text-white-50 d-flex flex-column gap-2 mb-0">
                            <li>
                                <i class="bi bi-phone me-2"></i>
                                <a href="tel:+256772319503" class="text-white-50 text-decoration-none">+256 772 319503</a>
                            </li>
                            <li>
                                <i class="bi bi-whatsapp me-2"></i>
                                <a href="https://wa.me/256772319503" target="_blank" rel="noopener noreferrer" class="text-white-50 text-decoration-none">WhatsApp Us</a>
                            </li>
                            <li>
                                <i class="bi bi-envelope me-2"></i>
                                <a href="mailto:info@scorebeyondleadership.org" class="text-white-50 text-decoration-none">info@scorebeyondleadership.org</a>
                            </li>
                            <li>
                                <i class="bi bi-geo-alt me-2"></i>
                                <span>Kampala, Uganda</span>
                            </li>
                            <li>
                                <i class="bi bi-geo-alt me-2"></i>
                                <span>Adjumani, Uganda</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-12">
                        <div class="fw-semibold text-uppercase mb-3">Legal</div>
                        <ul class="list-unstyled text-white-50 d-flex flex-column gap-2 mb-0">
                            <li><Link class="text-white-50 text-decoration-none" :href="route('policies.privacy')">Privacy Policy</Link></li>
                            <li><Link class="text-white-50 text-decoration-none" :href="route('policies.terms')">Terms of Service</Link></li>
                            <li><Link class="text-white-50 text-decoration-none" :href="route('policies.refund')">Refund Policy</Link></li>
                        </ul>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="row mt-4 pt-4 border-top border-white border-opacity-25">
                    <div class="col-12 text-center">
                        <small class="text-white-50">&copy; {{ new Date().getFullYear() }} Score Beyond Leadership Organization. All rights reserved.</small>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Floating WhatsApp Button -->
        <a
            href="https://wa.me/256772319503"
            target="_blank"
            rel="noopener noreferrer"
            class="whatsapp-float"
            aria-label="Contact us on WhatsApp"
        >
            <i class="bi bi-whatsapp"></i>
        </a>

        <!-- Back to Top Button -->
        <button
            v-if="showBackToTop"
            @click="scrollToTop"
            class="back-to-top-btn"
            aria-label="Back to top"
        >
            <i class="bi bi-arrow-up"></i>
        </button>
    </div>
</template>

<style scoped>
.letter-spacing-1 {
    letter-spacing: 0.08em;
}

.storefront-header {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
    backdrop-filter: blur(10px);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1030;
    width: 100%;
}

.storefront-logo {
    max-height: 50px;
    width: auto;
    object-fit: contain;
    transition: opacity 0.2s ease;
}

.storefront-logo:hover {
    opacity: 0.8;
}

.storefront-logo-footer {
    max-height: 60px;
    width: auto;
    object-fit: contain;
}

.storefront-main {
    padding-top: 4rem;
    overflow-x: hidden;
}

.nav-link-landing {
    font-size: 0.75rem;
    letter-spacing: 0.05em;
    padding: 0.5rem 0.75rem;
    white-space: nowrap;
    text-decoration: none;
    transition: color 0.2s ease;
}

.nav-link-landing:hover {
    color: var(--bs-primary) !important;
}

.nav-link-mobile {
    font-size: 0.875rem;
    letter-spacing: 0.05em;
    padding: 0.75rem 0;
    text-decoration: none;
    transition: color 0.2s ease;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.nav-link-mobile:hover {
    color: var(--bs-primary) !important;
}

.back-to-top-btn {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    background: linear-gradient(135deg, #f89f3d 0%, #f03733 100%);
    border: none;
    color: white;
    font-size: 1.25rem;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(160, 29, 98, 0.3);
    transition: all 0.3s ease;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.back-to-top-btn:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 20px rgba(160, 29, 98, 0.4);
}

.whatsapp-float {
    position: fixed;
    bottom: 2rem;
    left: 2rem;
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 50%;
    background: #25D366;
    color: white;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
    transition: all 0.3s ease;
    z-index: 1000;
    text-decoration: none;
}

.whatsapp-float:hover {
    transform: translateY(-4px) scale(1.1);
    box-shadow: 0 6px 20px rgba(37, 211, 102, 0.5);
    color: white;
}

@media (max-width: 767.98px) {
    .whatsapp-float {
        width: 3rem;
        height: 3rem;
        font-size: 1.25rem;
        bottom: 1.5rem;
        left: 1.5rem;
    }

    .back-to-top-btn {
        bottom: 1.5rem;
        right: 1.5rem;
    }
}
</style>

