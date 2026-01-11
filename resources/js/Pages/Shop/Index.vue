<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import type { PaginationMeta, ProductCard } from '@/types/shop';
import axios from 'axios';

interface Category {
    id: number;
    name: string;
    slug: string;
}

interface CategoryWithProducts extends Category {
    products: ProductCard[];
}

interface ShopFilters {
    category?: string;
    sort?: string;
    search?: string;
}

interface SearchSuggestion {
    id: number;
    name: string;
    slug: string;
}

const props = defineProps<{
    filters?: ShopFilters;
    categories?: Category[];
    categoriesWithProducts?: CategoryWithProducts[];
    products?: {
        data: ProductCard[];
        meta: PaginationMeta;
    };
    featuredProducts?: ProductCard[];
}>();

const searchTerm = ref(props.filters?.search ?? '');
const searchSuggestions = ref<SearchSuggestion[]>([]);
const showSuggestions = ref(false);
const searchInputRef = ref<HTMLInputElement | null>(null);
const suggestionsRef = ref<HTMLDivElement | null>(null);
const searchTimeout = ref<ReturnType<typeof setTimeout> | null>(null);

// Check if we're showing filtered results or category sections
const isFilteredView = computed(() => props.products !== undefined);
const isCategoryView = computed(() => props.categoriesWithProducts !== undefined);

watch(
    () => props.filters?.search,
    (value) => {
        searchTerm.value = value ?? '';
    },
);

// Debounced search for suggestions
watch(searchTerm, (value) => {
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value);
    }

    if (value.length < 2) {
        searchSuggestions.value = [];
        showSuggestions.value = false;
        return;
    }

    searchTimeout.value = setTimeout(async () => {
        try {
            const response = await axios.get(route('shop.search-suggestions'), {
                params: { q: value },
            });
            searchSuggestions.value = response.data;
            showSuggestions.value = response.data.length > 0;
        } catch (error) {
            // Silently handle search suggestion errors
            searchSuggestions.value = [];
            showSuggestions.value = false;
        }
    }, 300);
});

const applyFilters = (newFilters: Partial<ShopFilters>) => {
    router.get(
        route('shop.index'),
        {
            ...props.filters,
            ...newFilters,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
    showSuggestions.value = false;
};

const selectSuggestion = (suggestion: SearchSuggestion) => {
    searchTerm.value = suggestion.name;
    applyFilters({ search: suggestion.name });
    showSuggestions.value = false;
};

const handleSearch = () => {
    if (searchTerm.value.trim()) {
        applyFilters({ search: searchTerm.value.trim() });
    } else {
        applyFilters({ search: undefined });
    }
};

const formattedPrice = (price: ProductCard['price']) =>
    new Intl.NumberFormat('en-UG', {
        style: 'currency',
        currency: 'UGX',
        maximumFractionDigits: 0,
    }).format(price.ugx);

const formattedUsd = (price: ProductCard['price']) =>
    new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(price.usd);

const paginationLinks = computed(() => props.products?.meta.links ?? []);

// Auto-scrolling banner - show 2 products per slide
const currentBannerIndex = ref(0);
const bannerInterval = ref<ReturnType<typeof setInterval> | null>(null);
const bannerPaused = ref(false);

// Group products into pairs (2 per slide)
const productSlides = computed(() => {
    if (!props.featuredProducts) return [];
    const slides: Array<Array<typeof props.featuredProducts[number]>> = [];
    for (let i = 0; i < props.featuredProducts.length; i += 2) {
        slides.push(props.featuredProducts.slice(i, i + 2));
    }
    return slides;
});

const totalSlides = computed(() => productSlides.value.length);

const startBannerAutoScroll = () => {
    if (!props.featuredProducts || totalSlides.value <= 1) return;
    
    bannerInterval.value = setInterval(() => {
        if (!bannerPaused.value) {
            currentBannerIndex.value = (currentBannerIndex.value + 1) % totalSlides.value;
        }
    }, 5000); // Change every 5 seconds
};

const pauseBanner = () => {
    bannerPaused.value = true;
};

const resumeBanner = () => {
    bannerPaused.value = false;
};

const goToBannerSlide = (index: number) => {
    currentBannerIndex.value = index;
    // Reset the interval when manually changing slides
    if (bannerInterval.value) {
        clearInterval(bannerInterval.value);
        startBannerAutoScroll();
    }
};

// Close suggestions when clicking outside
const handleClickOutside = (event: MouseEvent) => {
    if (
        suggestionsRef.value &&
        !suggestionsRef.value.contains(event.target as Node) &&
        searchInputRef.value &&
        !searchInputRef.value.contains(event.target as Node)
    ) {
        showSuggestions.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    if (props.featuredProducts && props.featuredProducts.length > 0 && totalSlides.value > 1) {
        startBannerAutoScroll();
    }
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value);
    }
    if (bannerInterval.value) {
        clearInterval(bannerInterval.value);
    }
});
</script>

<template>
    <Head title="Shop" />
    <StorefrontLayout>
        <div class="container py-4">
            <!-- Featured Products Banner (only show on category view) -->
            <div v-if="isCategoryView && featuredProducts && featuredProducts.length > 0" class="mb-4">
                <div
                    class="featured-banner bg-white rounded-4 shadow-sm overflow-hidden position-relative"
                    @mouseenter="pauseBanner"
                    @mouseleave="resumeBanner"
                >
                    <div class="featured-banner-slides" :style="{ transform: `translateX(-${currentBannerIndex * 100}%)` }">
                        <div
                            v-for="(slide, slideIndex) in productSlides"
                            :key="slideIndex"
                            class="featured-banner-slide d-flex gap-3"
                        >
                            <Link
                                v-for="product in slide"
                                :key="product.id"
                                :href="route('shop.show', product.slug)"
                                class="featured-banner-item text-decoration-none d-flex align-items-center flex-grow-1"
                            >
                                <div class="featured-banner-image">
                                    <img
                                        :src="product.image ?? 'https://placehold.co/200x200?text=Score+Beyond'"
                                        :alt="product.image_alt ?? product.name"
                                        class="w-100 h-100 object-fit-cover"
                                    />
                                </div>
                                <div class="featured-banner-content flex-grow-1 p-3">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span
                                            v-if="product.is_limited_edition"
                                            class="badge bg-warning text-dark text-uppercase fw-semibold"
                                        >
                                            Limited Edition
                                        </span>
                                        <span class="badge bg-primary-subtle text-primary text-uppercase fw-semibold">
                                            Featured
                                        </span>
                                    </div>
                                    <h3 class="h6 fw-bold text-dark mb-1">{{ product.name }}</h3>
                                    <p v-if="product.subtitle" class="text-muted small mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-clamp: 2;">{{ product.subtitle }}</p>
                                    <div class="d-flex align-items-baseline gap-2 mb-2">
                                        <div class="h5 fw-bold text-primary mb-0">{{ formattedPrice(product.price) }}</div>
                                        <div class="text-muted small">{{ formattedUsd(product.price) }}</div>
                                    </div>
                                    <div>
                                        <span class="btn btn-primary btn-sm rounded-pill">
                                            View <i class="bi bi-arrow-right ms-1"></i>
                                        </span>
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>
                    
                    <!-- Banner Indicators -->
                    <div v-if="totalSlides > 1" class="featured-banner-indicators">
                        <button
                            v-for="(slide, index) in productSlides"
                            :key="index"
                            type="button"
                            class="featured-banner-indicator"
                            :class="{ active: currentBannerIndex === index }"
                            @click.stop="goToBannerSlide(index)"
                            :aria-label="`Go to slide ${index + 1}`"
                        ></button>
                    </div>
                </div>
            </div>

            <!-- Mobile Search (shown before categories on mobile) -->
            <div class="d-lg-none mb-4">
                <div class="position-relative">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted z-2"></i>
                    <input
                        ref="searchInputRef"
                        v-model="searchTerm"
                        type="search"
                        class="form-control ps-5 rounded-pill"
                        style="height: 38px; font-size: 0.875rem;"
                        placeholder="Search products..."
                        @keyup.enter="handleSearch"
                        @focus="showSuggestions = searchSuggestions.length > 0"
                    />
                    <div
                        v-if="showSuggestions && searchSuggestions.length > 0"
                        ref="suggestionsRef"
                        class="position-absolute top-100 start-0 w-100 bg-white border rounded-bottom shadow-lg mt-1 z-3"
                        style="max-height: 300px; overflow-y: auto;"
                    >
                        <button
                            v-for="suggestion in searchSuggestions"
                            :key="suggestion.id"
                            type="button"
                            class="w-100 text-start px-3 py-2 border-0 bg-transparent hover-bg-light"
                            @click="selectSuggestion(suggestion)"
                        >
                            {{ suggestion.name }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Category Sections View (default) -->
            <template v-if="isCategoryView && categoriesWithProducts">
                <div
                    v-for="category in categoriesWithProducts"
                    :key="category.id"
                    class="mb-5 card"
                >
                   <div class="card-header section-bg-gradient-dark text-white py-1">
                    <div class="d-flex align-items-center justify-content-between">
                        <h2 class="h4 fw-bold text-white fw-bold mb-0">{{ category.name }}</h2>
                        <Link
                            :href="route('shop.index', { category: category.slug })"
                            class="text-decoration-none text-white fw-semibold"
                        >
                            See All <i class="bi bi-arrow-right ms-1"></i>
                        </Link>
                    </div>
                   </div>
                    <div class="card-body">
                        <div class="products-scroll-container">
                            <div class="products-scroll d-flex gap-3 pb-3">
                                <Link
                                    v-for="product in category.products"
                                    :key="product.id"
                                    :href="route('shop.show', product.slug)"
                                    class="text-decoration-none flex-shrink-0"
                                    style="width: 180px; min-width: 180px;"
                                >
                                    <div class="product-card-jumia bg-white h-100 d-flex flex-column">
                                        <div class="position-relative bg-light" style="aspect-ratio: 1; overflow: hidden;">
                                            <img
                                                :src="product.image ?? 'https://placehold.co/400x400?text=Score+Beyond'"
                                                :alt="product.image_alt ?? product.name"
                                                class="w-100 h-100 object-fit-contain p-2"
                                            />
                                            <span
                                                v-if="product.is_limited_edition"
                                                class="position-absolute top-0 start-0 m-2 badge bg-warning text-dark"
                                            >
                                                Limited Edition
                                            </span>
                                        </div>
                                        <div class="p-3 d-flex flex-column flex-grow-1">
                                            <h3
                                                class="fs-6 mb-2 text-dark fw-normal"
                                                style="min-height: 2.5rem; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"
                                            >
                                                {{ product.name }}
                                            </h3>
                                            <div class="mt-auto">
                                                <div class="fw-bold text-primary mb-1">{{ formattedPrice(product.price) }}</div>
                                                <small class="text-muted">{{ formattedUsd(product.price) }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Filtered Results View (when search or category filter is applied) -->
            <template v-else-if="isFilteredView && products">
                <!-- Search and Filter Bar -->
                <div class="bg-white rounded-3 shadow-sm p-3 mb-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-5">
                           
                        </div>
                        <div class="col-lg-3 d-flex align-items-center justify-content-lg-end gap-2">
                            <label class="small text-muted mb-0">Sort:</label>
                            <select
                                class="form-select form-select-sm w-auto"
                                :value="filters?.sort ?? undefined"
                                @change="applyFilters({ sort: ($event.target as HTMLSelectElement).value })"
                            >
                                <option value="newest" selected>Newest arrivals</option>
                                <option value="price_asc">Price: Low to High</option>
                                <option value="price_desc">Price: High to Low</option>
                                <option value="popularity">Popularity</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="row g-3">
                    <div
                        v-for="product in products.data"
                        :key="product.id"
                        class="col-6 col-md-4 col-lg-3"
                    >
                        <Link :href="route('shop.show', product.slug)" class="text-decoration-none">
                            <div class="product-card-jumia bg-white h-100 d-flex flex-column">
                                <div class="position-relative bg-light" style="aspect-ratio: 1; overflow: hidden;">
                                    <img
                                        :src="product.image ?? 'https://placehold.co/400x400?text=Score+Beyond'"
                                        :alt="product.image_alt ?? product.name"
                                        class="w-100 h-100 object-fit-contain p-2"
                                        loading="lazy"
                                    />
                                    <span
                                        v-if="product.is_limited_edition"
                                        class="position-absolute top-0 start-0 m-2 badge bg-warning text-dark"
                                    >
                                        Limited Edition
                                    </span>
                                </div>
                                <div class="p-3 d-flex flex-column flex-grow-1">
                                    <h3
                                        class="fs-6 mb-2 text-dark fw-normal"
                                        style="min-height: 2.5rem; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"
                                    >
                                        {{ product.name }}
                                    </h3>
                                    <div class="mt-auto">
                                        <div class="fw-bold text-primary mb-1">{{ formattedPrice(product.price) }}</div>
                                        <small class="text-muted">{{ formattedUsd(product.price) }}</small>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Pagination -->
                <nav
                    v-if="paginationLinks.length > 3"
                    class="d-flex justify-content-center mt-4"
                >
                    <ul class="pagination mb-0">
                        <li
                            v-for="link in paginationLinks"
                            :key="link.label"
                            class="page-item"
                            :class="{ active: link.active, disabled: link.url === null }"
                        >
                            <Link
                                class="page-link"
                                :href="link.url ?? '#'"
                                v-html="link.label"
                            />
                        </li>
                    </ul>
                </nav>
            </template>
        </div>
    </StorefrontLayout>
</template>

<style scoped>
.products-scroll-container {
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

.products-scroll-container::-webkit-scrollbar {
    height: 8px;
}

.products-scroll-container::-webkit-scrollbar-track {
    background: #f7fafc;
    border-radius: 4px;
}

.products-scroll-container::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 4px;
}

.products-scroll-container::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

.products-scroll {
    min-width: min-content;
}

.hover-bg-light:hover {
    background-color: #f8f9fa !important;
}

@media (max-width: 575.98px) {
    .products-scroll > a {
        width: 150px !important;
        min-width: 150px !important;
    }
}

/* Featured Banner Styles */
.featured-banner {
    height: 200px;
    position: relative;
    overflow: hidden;
}

.featured-banner-slides {
    display: flex;
    width: 100%;
    height: 100%;
    transition: transform 0.5s ease-in-out;
}

.featured-banner-slide {
    min-width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0 1rem;
    color: inherit;
}

.featured-banner-item {
    flex: 1;
    min-width: 0;
    display: flex;
    align-items: center;
    height: 100%;
}

.featured-banner-image {
    width: 150px;
    height: 100%;
    flex-shrink: 0;
    background: #f8f9fa;
    overflow: hidden;
}

.featured-banner-image img {
    object-fit: cover;
    transition: transform 0.3s ease;
}

.featured-banner-item:hover .featured-banner-image img {
    transform: scale(1.05);
}

.featured-banner-content {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.featured-banner-indicators {
    position: absolute;
    bottom: 1rem;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 0.5rem;
    z-index: 10;
}

.featured-banner-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    border: none;
    background: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 0;
}

.featured-banner-indicator:hover {
    background: rgba(255, 255, 255, 0.8);
    transform: scale(1.2);
}

.featured-banner-indicator.active {
    background: var(--bs-primary);
    width: 24px;
    border-radius: 4px;
}

@media (max-width: 768px) {
    .featured-banner {
        height: auto;
        min-height: 200px;
    }

    .featured-banner-slide {
        flex-direction: column;
        padding: 1rem;
        gap: 1rem;
    }

    .featured-banner-item {
        width: 100%;
        height: auto;
    }

    .featured-banner-image {
        width: 120px;
        height: 120px;
    }

    .featured-banner-content {
        padding: 0.75rem !important;
    }

    .featured-banner-content h3 {
        font-size: 0.875rem;
    }

    .featured-banner-content .h5 {
        font-size: 1rem;
    }
}
</style>
