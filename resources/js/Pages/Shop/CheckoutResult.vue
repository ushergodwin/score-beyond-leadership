<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import type { MoneyDisplay } from '@/types/shop';
import { clearCartFromStorage } from '@/composables/useCartPersistence';

interface ResultItem {
    id: number;
    name: string;
    sku: string | null;
    quantity: number;
    subtotal: MoneyDisplay;
}

const props = defineProps<{
    order: {
        order_number: string;
        status: string;
        payment_status: string;
        status_label: string;
        status_intent: 'success' | 'danger' | 'warning';
        total: MoneyDisplay;
        items: ResultItem[];
    };
}>();

const badgeClass = computed(() => {
    switch (props.order.status_intent) {
        case 'success':
            return 'badge bg-success-subtle text-success';
        case 'danger':
            return 'badge bg-danger-subtle text-danger';
        default:
            return 'badge bg-warning-subtle text-warning';
    }
});

const formatterUGX = new Intl.NumberFormat('en-UG', {
    style: 'currency',
    currency: 'UGX',
    maximumFractionDigits: 0,
});

const formatterUSD = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
});

// Clear cart from localStorage after successful order
onMounted(() => {
    if (props.order.status_intent === 'success') {
        clearCartFromStorage();
    }
});
</script>

<template>
    <Head :title="`Order ${order.order_number}`" />
    <StorefrontLayout>
        <template #hero>
            <section class="py-5 bg-white border-bottom">
                <div class="container">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                        <div>
                            <p class="text-uppercase text-secondary small fw-semibold mb-2">Checkout complete</p>
                            <h1 class="display-5 fw-bold text-primary mb-2">Order {{ order.order_number }}</h1>
                            <p class="text-muted mb-0">
                                {{ order.status_label }} — we’ll keep you posted via email once fulfillment starts.
                            </p>
                        </div>
                        <span :class="badgeClass">{{ order.status_label }}</span>
                    </div>
                </div>
            </section>
        </template>

        <div class="container mb-5">
            <!-- Success Appreciation Message -->
            <div v-if="order.status_intent === 'success'" class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 bg-success-subtle">
                        <div class="card-body p-4 p-lg-5">
                            <div class="d-flex align-items-start gap-4">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h2 class="h4 fw-bold text-success mb-3">Thank You for Your Purchase!</h2>
                                    <p class="lead mb-3">
                                        Your payment has been successfully processed. We truly appreciate your support for Score Beyond Leadership!
                                    </p>
                                    <p class="mb-0 text-muted">
                                        Your order is being prepared and you'll receive a confirmation email shortly. We'll keep you updated on the shipping status. Your purchase helps us continue our mission to empower women and girls through sports, education, and leadership development.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Failed Payment Message -->
            <div v-else-if="order.status_intent === 'danger'" class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 bg-danger-subtle">
                        <div class="card-body p-4 p-lg-5">
                            <div class="d-flex align-items-start gap-4">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 3rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h2 class="h4 fw-bold text-danger mb-3">Payment Failed</h2>
                                    <p class="mb-0 text-muted">
                                        We were unable to process your payment. Please try again or contact us at +256 772 319503 or info@scorebeyondleadership.org for assistance.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Payment Message -->
            <div v-else class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 bg-warning-subtle">
                        <div class="card-body p-4 p-lg-5">
                            <div class="d-flex align-items-start gap-4">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-clock-history text-warning" style="font-size: 3rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h2 class="h4 fw-bold text-warning mb-3">Payment Pending</h2>
                                    <div class="alert alert-warning rounded-4 mb-3" role="alert">
                                        <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Important:</strong> 
                                        If you just completed payment on Pesapal, please wait a few moments for the payment to be confirmed. 
                                        Our system automatically processes payments in the background. You will receive a confirmation email once the payment is completed.
                                    </div>
                                    <p class="mb-0 text-muted">
                                        Your payment is being processed. Even if you closed the Pesapal page early, our system will automatically detect and process your payment. 
                                        You will receive a confirmation email once the payment is completed. Thank you for your patience!
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-4">Items</h2>
                            <div class="d-flex flex-column gap-3">
                                <article
                                    v-for="item in order.items"
                                    :key="item.id"
                                    class="d-flex justify-content-between border-bottom pb-3"
                                >
                                    <div>
                                        <div class="fw-semibold text-primary">{{ item.name }}</div>
                                        <div class="text-muted small">
                                            SKU: {{ item.sku ?? 'N/A' }} &middot; Qty {{ item.quantity }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold">
                                            {{ formatterUGX.format(item.subtotal.ugx) }}
                                        </div>
                                        <small class="text-muted">
                                            {{ formatterUSD.format(item.subtotal.usd) }}
                                        </small>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3">Order total</h2>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="display-6 fw-bold text-primary mb-0">
                                        {{ formatterUGX.format(order.total.ugx) }}
                                    </div>
                                    <small class="text-muted">
                                        {{ formatterUSD.format(order.total.usd) }}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <p class="text-muted small mb-0">Payment status</p>
                                    <span :class="badgeClass">{{ order.payment_status }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <Link class="btn btn-primary rounded-pill text-uppercase fw-semibold" :href="route('shop.index')">
                            Back to shop
                        </Link>
                        <Link class="btn btn-outline-secondary rounded-pill" :href="route('dashboard')" v-if="$page.props.auth?.user">
                            Go to dashboard
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </StorefrontLayout>
</template>

