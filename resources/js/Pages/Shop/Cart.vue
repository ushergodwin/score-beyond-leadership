<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import type { CartSummary, ProductCard } from '@/types/shop';
import { showSuccess, showError, showWarning, confirmAction } from '@/composables/useNotifications';

type CartItem = CartSummary['items'][number];

const props = defineProps<{
    cart: CartSummary;
    alsoBoughtProducts?: ProductCard[];
}>();

const page = usePage();

const priceFormatter = new Intl.NumberFormat('en-UG', {
    style: 'currency',
    currency: 'UGX',
    maximumFractionDigits: 0,
});

const usdFormatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
});

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

const hasItems = computed(() => props.cart.items.length > 0);

// Watch for flash messages
watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.cart_message) {
            showSuccess(flash.cart_message as string);
        }
        if (flash?.error) {
            showError(flash.error as string);
        }
    },
    { deep: true, immediate: true },
);

const increaseQuantity = (item: CartItem) => {
    const newQuantity = Math.min(item.quantity + 1, Math.min(10, item.stock));
    if (newQuantity > item.quantity) {
        updateQuantity(item.id, newQuantity);
    } else {
        showWarning('Maximum quantity reached for this item.');
    }
};

const decreaseQuantity = (item: CartItem) => {
    if (item.quantity > 1) {
        updateQuantity(item.id, item.quantity - 1);
    } else {
        removeItem(item.id);
    }
};

const updateQuantity = (itemId: string, quantity: number) => {
    router.patch(
        route('cart.update', itemId),
        { quantity },
        {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess('Cart updated');
            },
            onError: () => {
                showError('Failed to update cart. Please try again.');
            },
        },
    );
};

const removeItem = async (itemId: string) => {
    const confirmed = await confirmAction(
        'Remove Item',
        'Are you sure you want to remove this item from your cart?',
        'Yes, Remove',
        'Cancel',
    );

    if (confirmed) {
        router.delete(route('cart.destroy', itemId), {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess('Item removed from cart');
            },
            onError: () => {
                showError('Failed to remove item. Please try again.');
            },
        });
    }
};
</script>

<template>
    <Head title="Shopping Cart" />
    <StorefrontLayout>
        <template #hero>
            <section class="py-5 section-bg-gradient-dark border-bottom text-white">
                <div class="container">
                    <h1 class="display-5 fw-bold">Shopping cart</h1>
                    <p class="text-white-50">
                        Every purchase supports our mission to empower women and girls through sports, education, and livelihood opportunities.
                    </p>
                </div>
            </section>
        </template>

        <div class="container mb-5 mt-4">
            <div v-if="!hasItems" class="text-center py-5 page-section">
                <h2 class="fw-bold text-primary mb-3">Your cart is waiting.</h2>
                <p class="text-muted mb-4">
                    Explore apparel, accessories, and artisan crafts that directly support community programs.
                </p>
                <Link class="btn btn-primary rounded-pill px-4 text-uppercase fw-semibold" :href="route('shop.index')">
                    Continue shopping
                </Link>
            </div>
            <div v-else class="row g-5">
                <div class="col-lg-8">
                    <div class="page-section">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="h4 fw-bold mb-0">Cart items</h2>
                            <span class="text-muted">{{ cart.items.length }} items</span>
                        </div>
                        <div class="d-flex flex-column gap-4">
                            <article v-for="item in cart.items" :key="item.id" class="cart-item-card d-flex gap-3">
                                    <Link
                                        class="ratio ratio-1x1 flex-shrink-0 rounded-4 overflow-hidden shadow-sm"
                                        :href="route('shop.show', item.slug)"
                                        style="width: 120px;"
                                    >
                                        <img
                                            :src="item.image ?? 'https://placehold.co/600x800?text=Score+Beyond'"
                                            :alt="item.name"
                                            class="w-100 h-100 object-fit-cover"
                                        />
                                    </Link>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <Link class="text-decoration-none text-primary fw-semibold" :href="route('shop.show', item.slug)">
                                                    {{ item.name }}
                                                </Link>
                                                <p class="text-muted mb-1 small">Variant: {{ item.variant_name }}</p>
                                                <p class="text-muted mb-2 small">SKU: {{ item.sku }}</p>
                                                <div class="d-flex align-items-center gap-2">
                                                    <label class="small fw-semibold text-muted mb-0">Qty</label>
                                                    <div class="input-group" style="width: auto;">
                                                        <button
                                                            class="quantity-btn"
                                                            type="button"
                                                            :disabled="item.quantity <= 1"
                                                            @click="decreaseQuantity(item)"
                                                        >
                                                            <i class="bi bi-dash"></i>
                                                        </button>
                                                        <input
                                                            type="number"
                                                            class="form-control form-control-sm text-center"
                                                            :value="item.quantity"
                                                            min="1"
                                                            :max="Math.min(10, item.stock)"
                                                            style="width: 60px; border: 2px solid rgba(13, 59, 102, 0.1);"
                                                            readonly
                                                        />
                                                        <button
                                                            class="quantity-btn"
                                                            type="button"
                                                            :disabled="item.quantity >= Math.min(10, item.stock)"
                                                            @click="increaseQuantity(item)"
                                                        >
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    </div>
                                                    <button
                                                        class="btn btn-link text-danger small text-uppercase fw-semibold"
                                                        type="button"
                                                        @click="removeItem(item.id)"
                                                    >
                                                        Remove
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <strong class="d-block text-primary">
                                                    {{ priceFormatter.format(item.display_price.ugx) }}
                                                </strong>
                                                <small class="text-muted">{{ usdFormatter.format(item.display_price.usd) }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="page-section">
                        <h2 class="h4 fw-bold mb-4">Order summary</h2>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-semibold">{{ priceFormatter.format(cart.totals.subtotal.ugx) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Shipping</span>
                            <span class="fw-semibold text-success">Calculated at checkout</span>
                        </div>
                        <hr />
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold text-uppercase">Total</span>
                            <div class="text-end">
                                <div class="h3 fw-bold text-primary mb-0">
                                    {{ priceFormatter.format(cart.totals.grand_total.ugx) }}
                                </div>
                                <small class="text-muted">{{ usdFormatter.format(cart.totals.grand_total.usd) }}</small>
                            </div>
                        </div>
                        <Link
                            class="btn btn-primary btn-lg rounded-pill w-100 text-uppercase fw-semibold text-center"
                            :href="route('checkout.index')"
                        >
                            Checkout
                        </Link>
                    </div>
                </div>
            </div>

            <!-- People who bought this also bought section -->
            <div v-if="alsoBoughtProducts && alsoBoughtProducts.length > 0" class="row mt-5">
                <div class="col-12">
                    <h2 class="h4 fw-bold mb-4">People who bought this item also bought</h2>
                    <div class="products-scroll-container">
                        <div class="products-scroll d-flex gap-3 pb-3">
                            <Link
                                v-for="product in alsoBoughtProducts"
                                :key="product.id"
                                :href="route('shop.show', product.slug)"
                                class="text-decoration-none flex-shrink-0"
                                style="width: 180px;"
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
    height: 0.5rem;
}

.products-scroll-container::-webkit-scrollbar-track {
    background: #f7fafc;
    border-radius: 0.25rem;
}

.products-scroll-container::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 0.25rem;
}

.products-scroll-container::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

.products-scroll {
    min-width: min-content;
}
</style>

