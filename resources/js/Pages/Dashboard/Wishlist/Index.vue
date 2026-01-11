<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';

interface Product {
    id: number;
    name: string;
    slug: string;
    sku: string;
    base_price: number;
    base_currency: string;
    inventory: number;
    image: string | null;
    image_alt: string | null;
}

interface Variant {
    id: number;
    name: string;
    sku: string;
    price: number;
    currency: string;
    inventory: number;
}

interface WishlistItem {
    id: number;
    product: Product;
    variant: Variant | null;
    added_at: string;
}

interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}

const props = defineProps<{
    wishlistItems: {
        data: WishlistItem[];
        meta: PaginationMeta;
    };
}>();

const formatterUGX = new Intl.NumberFormat('en-UG', {
    style: 'currency',
    currency: 'UGX',
    maximumFractionDigits: 0,
});

const formatterUSD = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
});

const formatPrice = (item: WishlistItem) => {
    const price = item.variant ? item.variant.price : item.product.base_price;
    const currency = item.variant ? item.variant.currency : item.product.base_currency;
    
    if (currency === 'UGX') {
        return formatterUGX.format(price);
    } else if (currency === 'USD') {
        return formatterUSD.format(price);
    } else {
        return new Intl.NumberFormat('de-DE', {
            style: 'currency',
            currency: 'EUR',
        }).format(price);
    }
};

const removeFromWishlist = (item: WishlistItem) => {
    router.delete(route('dashboard.wishlist.destroy', item.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Show success message
        },
    });
};

const paginationLinks = computed(() => props.wishlistItems.meta.links || []);

const page = usePage();
const isLoading = computed(() => page.props.processing ?? false);
</script>

<template>
    <Head title="Saved Items" />
    <DashboardLayout>
        <div class="container-fluid py-4 px-3 px-md-4">
            <!-- Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h1 class="h3 fw-bold text-primary mb-2">Saved Items</h1>
                    <p class="text-muted mb-0">Your wishlist items</p>
                </div>
                <Link :href="route('shop.index')" class="btn btn-primary rounded-pill">
                    <i class="bi bi-shop me-2"></i>Continue Shopping
                </Link>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5 text-center">
                    <LoadingSpinner size="lg" />
                    <p class="text-muted mt-3 mb-0">Loading wishlist...</p>
                </div>
            </div>

            <!-- Wishlist Items -->
            <div v-else-if="wishlistItems.data.length === 0" class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5 text-center">
                    <i class="bi bi-bookmark text-muted" style="font-size: 4rem;"></i>
                    <h3 class="h5 fw-bold mt-3 mb-2">Your wishlist is empty</h3>
                    <p class="text-muted mb-4">Start saving items you love!</p>
                    <Link :href="route('shop.index')" class="btn btn-primary rounded-pill">
                        Browse Products
                    </Link>
                </div>
            </div>

            <div v-else class="row g-4">
                <div
                    v-for="item in wishlistItems.data"
                    :key="item.id"
                    class="col-12 col-sm-6 col-lg-4"
                >
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <Link :href="route('shop.show', item.product.slug)" class="text-decoration-none text-dark">
                            <img
                                :src="item.product.image ?? 'https://placehold.co/600x800?text=Score+Beyond'"
                                :alt="item.product.image_alt ?? item.product.name"
                                class="card-img-top"
                                style="aspect-ratio: 4 / 5; object-fit: cover;"
                            />
                        </Link>
                        <div class="card-body d-flex flex-column">
                            <div class="mb-2">
                                <span class="badge bg-secondary-subtle text-secondary small">
                                    {{ item.variant ? item.variant.name : 'Standard' }}
                                </span>
                            </div>
                            <h3 class="h6 fw-bold mb-2">
                                <Link :href="route('shop.show', item.product.slug)" class="text-decoration-none text-dark">
                                    {{ item.product.name }}
                                </Link>
                            </h3>
                            <div class="fw-bold text-primary mb-3">{{ formatPrice(item) }}</div>
                            <div class="d-flex gap-2 mt-auto">
                                <Link
                                    :href="route('shop.show', item.product.slug)"
                                    class="btn btn-primary rounded-pill flex-grow-1"
                                >
                                    View Product
                                </Link>
                                <button
                                    class="btn btn-outline-danger rounded-pill"
                                    @click="removeFromWishlist(item)"
                                    title="Remove from wishlist"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <small class="text-muted mt-2">Added {{ item.added_at }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="wishlistItems.meta.last_page > 1" class="d-flex justify-content-center mt-4">
                <nav>
                    <ul class="pagination">
                        <li v-for="link in paginationLinks" :key="link.label" class="page-item" :class="{ active: link.active, disabled: !link.url }">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="page-link rounded-pill mx-1"
                                v-html="link.label"
                            ></Link>
                            <span v-else class="page-link rounded-pill mx-1" v-html="link.label"></span>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </DashboardLayout>
</template>

<style scoped>
.card:hover {
    transform: translateY(-4px);
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}
</style>

