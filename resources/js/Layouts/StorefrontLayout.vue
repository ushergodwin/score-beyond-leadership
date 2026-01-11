<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import scoreBeyondLogo from '../../images/scorebeyond-white.webp';

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
import {
    clearCartFromStorage,
    hasStoredCart,
    loadCartFromStorage,
    saveCartToStorage,
    type CartItem,
} from '../composables/useCartPersistence';
import { showSuccess, showError, showInfo } from '../composables/useNotifications';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

interface SearchSuggestion {
    id: number;
    name: string;
    slug: string;
}

const isMenuOpen = ref(false);

const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};

const page = usePage();
const appName = computed(() => (page.props.appName as string | undefined) ?? 'Score Beyond');
const cartCount = computed(() => Number(page.props.cart?.count ?? 0));

// Check if we're on the home page
const isHomePage = computed(() => {
    const url = (page.url as string) || '';
    return url === '/' || url === '';
});

// Check if we're in the shop section
const isShopSection = computed(() => {
    const url = (page.url as string) || '';
    return url.startsWith('/shop') || url.startsWith('/checkout') || url.startsWith('/cart');
});

// Nav bar search functionality
const navSearchTerm = ref('');
const navSearchSuggestions = ref<SearchSuggestion[]>([]);
const showNavSuggestions = ref(false);
const navSearchInputRef = ref<HTMLInputElement | null>(null);
const navSuggestionsRef = ref<HTMLDivElement | null>(null);
const navSearchTimeout = ref<ReturnType<typeof setTimeout> | null>(null);

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

// Debounced search for nav bar suggestions
watch(navSearchTerm, (value) => {
    if (navSearchTimeout.value) {
        clearTimeout(navSearchTimeout.value);
    }

    if (value.length < 2) {
        navSearchSuggestions.value = [];
        showNavSuggestions.value = false;
        return;
    }

    navSearchTimeout.value = setTimeout(async () => {
        try {
            const response = await axios.get(route('shop.search-suggestions'), {
                params: { q: value },
            });
            navSearchSuggestions.value = response.data;
            showNavSuggestions.value = response.data.length > 0;
        } catch (error) {
            // Silently handle search suggestion errors
            navSearchSuggestions.value = [];
            showNavSuggestions.value = false;
        }
    }, 300);
});

const handleNavSearch = (event?: Event) => {
    const searchTerm = navSearchTerm.value.trim();
    if (searchTerm) {
        router.get(route('shop.index'), { search: searchTerm });
        showNavSuggestions.value = false;
    }
};

const selectNavSuggestion = (suggestion: SearchSuggestion) => {
    navSearchTerm.value = suggestion.name;
    router.get(route('shop.index'), { search: suggestion.name });
    showNavSuggestions.value = false;
};

// Close nav suggestions when clicking outside
const handleNavClickOutside = (event: MouseEvent) => {
    if (
        navSuggestionsRef.value &&
        !navSuggestionsRef.value.contains(event.target as Node) &&
        navSearchInputRef.value &&
        !navSearchInputRef.value.contains(event.target as Node)
    ) {
        showNavSuggestions.value = false;
    }
};

// Restore cart from localStorage on mount if session is empty
onMounted(() => {
    if (cartCount.value === 0 && hasStoredCart()) {
        const storedItems = loadCartFromStorage();
        if (storedItems.length > 0) {
            router.post(
                route('cart.restore'),
                {
                    items: storedItems.map((item) => ({
                        id: item.id,
                        product_id: item.product_id,
                        variant_id: item.variant_id,
                        quantity: item.quantity,
                    })),
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['cart'],
                },
            );
        }
    }
    
    // Add click outside listener for nav search
    document.addEventListener('click', handleNavClickOutside);
});

onUnmounted(() => {
    if (navSearchTimeout.value) {
        clearTimeout(navSearchTimeout.value);
    }
    document.removeEventListener('click', handleNavClickOutside);
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

// Sync cart to localStorage whenever cart count changes
watch(
    () => page.props.cart,
    (cart) => {
        if (cart && 'items' in cart && Array.isArray(cart.items) && cart.items.length > 0) {
            const items: CartItem[] = cart.items.map((item) => ({
                id: item.id,
                product_id: item.product_id,
                variant_id: item.variant_id,
                name: item.name,
                variant_name: item.variant_name,
                quantity: item.quantity,
                unit_price: item.unit_price,
                currency: item.currency,
                display_price: item.display_price,
                image: item.image,
                slug: item.slug,
                sku: item.sku,
                stock: item.stock,
            }));
            saveCartToStorage(items);
        } else if (cartCount.value === 0) {
            clearCartFromStorage();
        }
    },
    { deep: true, immediate: true },
);
</script>

<template>
    <div class="storefront-layout min-vh-100 d-flex flex-column bg-light-subtle">
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
                    @click="toggleMenu"
                    aria-label="Toggle menu"
                >
                    <i class="bi" :class="isMenuOpen ? 'bi-x-lg' : 'bi-list'" style="font-size: 1.5rem;"></i>
                </button>
                <nav class="d-none d-lg-flex align-items-center gap-2 flex-grow-1 ms-4 flex-wrap">
                    <Link
                        class="nav-link-shop text-uppercase fw-semibold text-dark"
                        :href="route('home')"
                    >
                        Home
                    </Link>
                    <div v-if="!isHomePage" class="position-relative flex-grow-1" style="max-width: 300px; min-width: 200px;">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted z-2"></i>
                        <input
                            ref="navSearchInputRef"
                            id="nav-search-input"
                            v-model="navSearchTerm"
                            type="search"
                            class="form-control ps-5 rounded-pill"
                            style="height: 2rem; font-size: 0.75rem;"
                            placeholder="Search products..."
                            @keyup.enter="handleNavSearch"
                            @focus="showNavSuggestions = navSearchSuggestions.length > 0"
                        />
                        <div
                            v-if="showNavSuggestions && navSearchSuggestions.length > 0"
                            ref="navSuggestionsRef"
                            class="position-absolute top-100 start-0 w-100 bg-white border rounded-bottom shadow-lg mt-1 z-3"
                            style="max-height: 300px; overflow-y: auto;"
                        >
                            <button
                                v-for="suggestion in navSearchSuggestions"
                                :key="suggestion.id"
                                type="button"
                                class="w-100 text-start px-3 py-2 border-0 bg-transparent hover-bg-light"
                                @click="selectNavSuggestion(suggestion)"
                            >
                                {{ suggestion.name }}
                            </button>
                        </div>
                    </div>
                    <Link
                        class="nav-link-shop text-uppercase fw-semibold text-dark"
                        :href="route('shop.index')"
                    >
                        Shop
                    </Link>
                    <Link
                        v-if="!isShopSection"
                        class="nav-link-shop text-uppercase fw-semibold text-dark"
                        :href="route('gallery.index')"
                    >
                        Gallery
                    </Link>
                    <Link
                        class="nav-link-shop text-uppercase fw-semibold text-dark"
                        :href="route('volunteer.index')"
                    >
                        Volunteer
                    </Link>
                    <Link
                        class="nav-link-shop text-uppercase fw-semibold text-dark"
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
                </nav>
                <div v-if="!isHomePage" class="d-flex align-items-center gap-2">
                    <Link
                        class="btn btn-outline-primary rounded-pill position-relative"
                        :href="route('cart.index')"
                        aria-label="Open shopping cart"
                    >
                        <i class="bi bi-bag"></i>
                        <span
                            v-if="cartCount > 0"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        >
                            {{ cartCount }}
                        </span>
                    </Link>
                    <Link
                        class="btn btn-outline-secondary rounded-pill text-uppercase fw-semibold me-2"
                        :href="route('login')"
                        v-if="$page.props.auth?.user === null"
                    >
                    <i class="bi bi-box-arrow-in-right"></i>
                    </Link>
                    <Link
                        v-else
                        class="btn btn-outline-secondary rounded-pill text-uppercase fw-semibold me-2"
                        :href="route('dashboard')"
                    >
                    <i class="bi bi-speedometer2"></i>
                    </Link>
                </div>
            </div>
            <!-- Mobile Menu -->
            <div v-if="isMenuOpen" class="d-lg-none border-top bg-white">
                <div class="container py-3">
                    <div class="d-flex flex-column gap-2">
                        <Link
                            class="nav-link-mobile text-uppercase fw-semibold text-dark"
                            :href="route('home')"
                            @click="isMenuOpen = false"
                        >
                            Home
                        </Link>
                        <div v-if="!isHomePage" class="mb-2">
                            <input
                                v-model="navSearchTerm"
                                type="search"
                                class="form-control rounded-pill"
                                style="font-size: 0.875rem;"
                                placeholder="Search products..."
                                @keyup.enter="handleNavSearch"
                            />
                        </div>
                        <Link
                            class="nav-link-mobile text-uppercase fw-semibold text-dark"
                            :href="route('shop.index')"
                            @click="isMenuOpen = false"
                        >
                            Shop
                        </Link>
                        <Link
                            v-if="!isShopSection"
                            class="nav-link-mobile text-uppercase fw-semibold text-dark"
                            :href="route('gallery.index')"
                            @click="isMenuOpen = false"
                        >
                            Gallery
                        </Link>
                        <Link
                            class="nav-link-mobile text-uppercase fw-semibold text-dark"
                            :href="route('volunteer.index')"
                            @click="isMenuOpen = false"
                        >
                            Volunteer
                        </Link>
                        <Link
                            class="nav-link-mobile text-uppercase fw-semibold text-dark"
                            :href="route('academy.index')"
                            @click="isMenuOpen = false"
                        >
                            Academy
                        </Link>
                        <Link
                            class="btn btn-warning rounded-pill px-3 py-2 text-uppercase fw-bold shadow-sm mt-2"
                            :href="route('donate.index')"
                            style="background: linear-gradient(135deg, #f89f3d 0%, #f03733 100%); border: none;"
                            @click="isMenuOpen = false"
                        >
                            <i class="bi bi-heart-fill me-2"></i>
                            Donate Now
                        </Link>
                        <div class="d-flex gap-2 mt-2">
                            <Link
                                class="btn btn-outline-primary rounded-pill position-relative flex-grow-1"
                                :href="route('cart.index')"
                                @click="isMenuOpen = false"
                            >
                                <i class="bi bi-bag"></i>
                                <span
                                    v-if="cartCount > 0"
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                >
                                    {{ cartCount }}
                                </span>
                            </Link>
                            <Link
                                v-if="$page.props.auth?.user === null"
                                class="btn btn-outline-secondary rounded-pill flex-grow-1"
                                :href="route('login')"
                                @click="isMenuOpen = false"
                            >
                                <i class="bi bi-box-arrow-in-right"></i>
                            </Link>
                            <Link
                                v-else
                                class="btn btn-outline-secondary rounded-pill flex-grow-1"
                                :href="route('dashboard')"
                                @click="isMenuOpen = false"
                            >
                                <i class="bi bi-speedometer2"></i>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <main class="flex-grow-1 me-2" style="padding-top: 4rem;">
            <slot name="hero" />
            <section>
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
    </div>
</template>

<style scoped>
    .text-primary-ink {
        color: #a01d62;
    }

.letter-spacing-1 {
    letter-spacing: 0.08em;
}

.nav-link-shop {
    font-size: 0.75rem;
    letter-spacing: 0.05em;
    padding: 0.5rem 0.75rem;
    white-space: nowrap;
    text-decoration: none;
    transition: color 0.2s ease;
}

.nav-link-shop:hover {
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

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
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
}
</style>

