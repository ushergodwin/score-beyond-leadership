<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import ShareButton from '@/Components/ShareButton.vue';
import type { ProductCard, ProductDetail, ProductVariant } from '@/types/shop';
import { showSuccess, showError, showWarning } from '@/composables/useNotifications';

const props = defineProps<{
    product: ProductDetail;
    related: ProductCard[];
}>();

const selectedImage = ref(props.product.images[0]?.url ?? null);
const selectedVariantId = ref<number | null>(
    props.product.variants.length > 0
        ? (props.product.variants.find((variant) => variant.is_default)?.id ?? props.product.variants[0]?.id ?? null)
        : null,
);

const hasVariants = computed(() => props.product.variants.length > 0);

const stockLabel = computed(() => {
    const selectedVariant = hasVariants.value
        ? props.product.variants.find((v) => v.id === selectedVariantId.value)
        : null;
    
    const inventory = selectedVariant?.inventory ?? props.product.inventory;
    
    if (inventory === 0) {
        return 'Sold out';
    }

    if (inventory < 6) {
        return 'Low stock';
    }

    return 'In stock';
});

const isOutOfStock = computed(() => {
    const selectedVariant = hasVariants.value
        ? props.product.variants.find((v) => v.id === selectedVariantId.value)
        : null;
    
    const inventory = selectedVariant?.inventory ?? props.product.inventory;
    return inventory === 0;
});

const formattedPrice = (price: ProductDetail['price']) =>
    new Intl.NumberFormat('en-UG', {
        style: 'currency',
        currency: 'UGX',
        maximumFractionDigits: 0,
    }).format(price.ugx);

const formattedUsd = (price: ProductDetail['price']) =>
    new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(price.usd);

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);

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
        if (flash?.success) {
            showSuccess(flash.success as string);
        }
    },
    { deep: true, immediate: true },
);

const isAddingToCart = ref(false);

const handleAddToCart = () => {
    // If product has variants, variant must be selected
    if (hasVariants.value && selectedVariantId.value === null) {
        showWarning('Please select a variant');
        return;
    }

    // Check stock
    if (isOutOfStock.value) {
        showError(hasVariants.value ? 'This variant is out of stock' : 'This product is out of stock');
        return;
    }

    isAddingToCart.value = true;
    router.post(
        route('cart.store'),
        {
            product_id: props.product.id,
            variant_id: selectedVariantId.value ?? null,
            quantity: 1,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess(`${props.product.name} added to cart`);
                isAddingToCart.value = false;
            },
            onError: () => {
                showError('Failed to add item to cart. Please try again.');
                isAddingToCart.value = false;
            },
        },
    );
};

const selectVariant = (variant: ProductVariant) => {
    selectedVariantId.value = variant.id;
};

const handleAddToWishlist = () => {
    if (!isAuthenticated.value) {
        showWarning('Please sign in to save items to your wishlist');
        router.visit(route('login'));
        return;
    }

    router.post(
        route('dashboard.wishlist.store'),
        {
            product_id: props.product.id,
            product_variant_id: selectedVariantId.value ?? null,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess('Item added to wishlist');
            },
            onError: () => {
                showError('Failed to add item to wishlist. Please try again.');
            },
        },
    );
};

</script>

<template>
    <Head :title="product.name" />
    <StorefrontLayout>
        <div class="container mb-5">
            <div class="row g-5 align-items-start mb-5">
                <div class="col-lg-6">
                    <div class="rounded-4 overflow-hidden shadow-sm bg-white mb-3" style="padding-top: 2rem;">
                        <img
                            :src="selectedImage ?? product.images[0]?.url ?? 'https://placehold.co/800x1000?text=Score+Beyond'"
                            :alt="product.name"
                            class="w-100 object-fit-contain"
                            style="aspect-ratio: 4 / 5; padding: 1rem;"
                        />
                    </div>
                    <div v-if="product.images.length > 2" class="product-images-scroll d-flex gap-3 overflow-x-auto pb-2" style="scrollbar-width: thin;">
                        <button
                            v-for="image in product.images"
                            :key="image.url"
                            class="btn btn-outline-light flex-shrink-0 p-0 rounded-4 border-0"
                            style="width: 100px;"
                            @click="selectedImage = image.url"
                        >
                            <img
                                :src="image.url"
                                :alt="image.alt_text ?? product.name"
                                class="w-100 object-fit-contain rounded-4 border"
                                :class="{ 'border-primary border-2': selectedImage === image.url }"
                                style="aspect-ratio: 1; padding: 0.5rem;"
                            />
                        </button>
                    </div>
                    <div v-else class="d-flex gap-3">
                        <button
                            v-for="image in product.images"
                            :key="image.url"
                            class="btn btn-outline-light flex-grow-1 p-0 rounded-4 border-0"
                            @click="selectedImage = image.url"
                        >
                            <img
                                :src="image.url"
                                :alt="image.alt_text ?? product.name"
                                class="w-100 object-fit-contain rounded-4 border"
                                :class="{ 'border-primary border-2': selectedImage === image.url }"
                                style="aspect-ratio: 4 / 5; padding: 0.5rem;"
                            />
                        </button>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge bg-primary-subtle text-primary text-uppercase fw-semibold">
                            {{ product.category }}
                        </span>
                        <span
                            v-if="product.is_limited_edition"
                            class="badge bg-warning-subtle text-warning text-uppercase fw-semibold"
                        >
                            Limited Edition
                        </span>
                        <span class="text-muted text-uppercase small fw-semibold">
                            SKU: {{ product.sku }}
                        </span>
                    </div>
                    <h1 class="display-5 fw-bold text-primary">{{ product.name }}</h1>
                    <p class="text-muted fs-5">{{ product.subtitle }}</p>
                    <div class="d-flex align-items-baseline gap-3 my-4">
                        <div class="display-6 fw-bold text-primary">{{ formattedPrice(product.price) }}</div>
                        <div class="text-muted fs-5">{{ formattedUsd(product.price) }}</div>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span class="badge rounded-pill bg-success-subtle text-success fw-semibold text-uppercase">
                            {{ stockLabel }}
                        </span>
                        <small class="text-muted">Ships worldwide in 2–5 business days</small>
                    </div>

                    <div v-if="hasVariants" class="mb-4">
                        <label class="text-uppercase small fw-semibold text-muted mb-2">Select size / variant</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button
                                v-for="variant in product.variants"
                                :key="variant.id"
                                class="btn rounded-pill px-4 fw-semibold"
                                :class="{
                                    'btn-primary text-white': selectedVariantId === variant.id,
                                    'btn-outline-secondary': selectedVariantId !== variant.id,
                                    'opacity-50': variant.inventory === 0,
                                }"
                                :disabled="variant.inventory === 0"
                                type="button"
                                @click="selectVariant(variant)"
                            >
                                {{ variant.name }}
                                <span v-if="variant.inventory === 0" class="ms-1">(Out of stock)</span>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-3">
                        <button
                            class="btn btn-primary btn-lg rounded-pill text-uppercase fw-semibold flex-grow-1"
                            :disabled="isOutOfStock || (hasVariants && selectedVariantId === null) || isAddingToCart"
                            type="button"
                            @click="handleAddToCart"
                        >
                            <span v-if="isAddingToCart" class="spinner-border spinner-border-sm me-2"></span>
                            <span v-else>Add to cart</span>
                        </button>
                        <ShareButton
                            :url="route('shop.show', product.slug)"
                            :title="product.name"
                            :description="product.subtitle ?? undefined"
                            :image="product.images[0]?.url ?? undefined"
                        />
                    </div>
                    <div v-if="isAuthenticated" class="d-flex gap-3">
                        <button
                            class="btn btn-outline-secondary rounded-pill flex-grow-1"
                            type="button"
                            @click="handleAddToWishlist"
                        >
                            <i class="bi bi-bookmark me-2"></i>Save to Wishlist
                        </button>
                    </div>

                    <hr class="my-5" />

                    <div class="row g-4">
                        <div class="col-md-6">
                            <h3 class="fw-semibold text-primary">Materials & Care</h3>
                            <p class="text-muted">{{ product.materials }}</p>
                            <p class="text-muted small">{{ product.care_instructions }}</p>
                        </div>
                        <div class="col-md-6">
                            <h3 class="fw-semibold text-primary">Artisan story</h3>
                            <p class="text-muted">{{ product.artisan_story }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <section class="mb-5" v-if="related.length > 0">
                <h2 class="fw-bold mb-3">Related products</h2>
                <div class="row g-4">
                    <div class="col-12 col-sm-6 col-lg-3" v-for="item in related" :key="item.id">
                        <div class="product-card bg-white h-100 d-flex flex-column">
                            <img
                                :src="item.image ?? 'https://placehold.co/600x800?text=Score+Beyond'"
                                :alt="item.image_alt ?? item.name"
                                class="product-card__image"
                            />
                            <div class="p-3 d-flex flex-column gap-2 flex-grow-1">
                                <p class="text-uppercase text-muted small fw-semibold mb-0">{{ item.category }}</p>
                                <h3 class="fs-6">{{ item.name }}</h3>
                                <div class="fw-bold text-primary">{{ formattedPrice(item.price) }}</div>
                                <div class="text-muted small">{{ formattedUsd(item.price) }}</div>
                                <Link class="btn btn-outline-primary rounded-pill mt-auto" :href="route('shop.show', item.slug)">
                                    View
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </StorefrontLayout>
</template>

