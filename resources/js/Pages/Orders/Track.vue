<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import LandingLayout from '@/Layouts/LandingLayout.vue';
import axios from 'axios';

const props = defineProps<{
    orderNumber: string;
    token?: string;
}>();

interface TimelineItem {
    status: string;
    label: string;
    date: string | null;
    completed: boolean;
}

interface OrderItem {
    name: string;
    quantity: number;
    sku: string;
}

interface OrderData {
    order_number: string;
    status: string;
    payment_status: string;
    timeline: TimelineItem[];
    items: OrderItem[];
    estimated_delivery: string | null;
    currency: string;
    grand_total: number;
    shipping_method: {
        name: string;
        estimated_min_days: number;
        estimated_max_days: number;
    } | null;
}

const order = ref<OrderData | null>(null);
const loading = ref(true);
const error = ref<string | null>(null);
const pollInterval = ref<number | null>(null);

const fetchOrder = async () => {
    try {
        loading.value = true;
        error.value = null;

        const url = route('orders.track.api', props.orderNumber);
        const params = props.token ? { token: props.token } : {};

        const response = await axios.get(url, { params });
        order.value = response.data;
    } catch (err: unknown) {
        if (axios.isAxiosError(err)) {
            if (err.response?.status === 404) {
                error.value = 'Order not found. Please check your order number.';
            } else if (err.response?.status === 403) {
                error.value = 'Invalid tracking token. Please check your tracking link.';
            } else if (err.response?.status === 429) {
                error.value = 'Too many requests. Please wait a moment and try again.';
            } else {
                error.value = err.response?.data?.error || 'Failed to load order information. Please try again.';
            }
        } else {
            error.value = 'An unexpected error occurred. Please try again.';
        }
    } finally {
        loading.value = false;
    }
};

const formatDate = (dateString: string | null): string => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
};

const formatPrice = (amount: number, currency: string): string => {
    if (currency === 'UGX') {
        return new Intl.NumberFormat('en-UG', {
            style: 'currency',
            currency: 'UGX',
            maximumFractionDigits: 0,
        }).format(amount);
    } else if (currency === 'USD') {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
        }).format(amount);
    } else {
        return new Intl.NumberFormat('de-DE', {
            style: 'currency',
            currency: 'EUR',
        }).format(amount);
    }
};

const getStatusBadgeClass = (status: string): string => {
    const statusMap: Record<string, string> = {
        pending: 'bg-warning-subtle text-warning',
        processing: 'bg-info-subtle text-info',
        shipped: 'bg-primary-subtle text-primary',
        delivered: 'bg-success-subtle text-success',
        cancelled: 'bg-danger-subtle text-danger',
        payment_failed: 'bg-danger-subtle text-danger',
    };
    return statusMap[status] || 'bg-secondary-subtle text-secondary';
};

const getPaymentStatusBadgeClass = (status: string): string => {
    const statusMap: Record<string, string> = {
        completed: 'bg-success-subtle text-success',
        pending: 'bg-warning-subtle text-warning',
        failed: 'bg-danger-subtle text-danger',
    };
    return statusMap[status] || 'bg-secondary-subtle text-secondary';
};

// Poll for updates every 10 seconds
onMounted(() => {
    fetchOrder();
    pollInterval.value = window.setInterval(() => {
        fetchOrder();
    }, 10000); // 10 seconds
});

onUnmounted(() => {
    if (pollInterval.value !== null) {
        clearInterval(pollInterval.value);
    }
});
</script>

<template>
    <Head :title="`Track Order ${orderNumber}`" />
    <LandingLayout>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <!-- Header -->
                    <div class="text-center mb-5">
                        <h1 class="display-5 fw-bold text-primary mb-3">Track Your Order</h1>
                        <p class="text-muted">Order Number: <strong>{{ orderNumber }}</strong></p>
                    </div>

                    <!-- Loading State -->
                    <div v-if="loading && !order" class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-5 text-center">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mb-0">Loading order information...</p>
                        </div>
                    </div>

                    <!-- Error State -->
                    <div v-else-if="error" class="card border-0 shadow-sm rounded-4 border-danger">
                        <div class="card-body p-5 text-center">
                            <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                            <h3 class="h5 fw-bold mt-3 mb-2">Error</h3>
                            <p class="text-muted mb-4">{{ error }}</p>
                            <button class="btn btn-primary rounded-pill" @click="fetchOrder">
                                <i class="bi bi-arrow-clockwise me-2"></i>Try Again
                            </button>
                        </div>
                    </div>

                    <!-- Order Information -->
                    <div v-else-if="order" class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <!-- Order Status -->
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                                <div>
                                    <h2 class="h4 fw-bold mb-2">{{ order.order_number }}</h2>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span :class="['badge rounded-pill fw-semibold', getStatusBadgeClass(order.status)]">
                                            {{ order.status.replace('_', ' ').toUpperCase() }}
                                        </span>
                                        <span :class="['badge rounded-pill fw-semibold', getPaymentStatusBadgeClass(order.payment_status)]">
                                            Payment {{ order.payment_status }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary fs-4">{{ formatPrice(order.grand_total, order.currency) }}</div>
                                </div>
                            </div>

                            <!-- Timeline -->
                            <div class="mb-4">
                                <h3 class="h6 fw-bold mb-3">Order Timeline</h3>
                                <div class="timeline">
                                    <div
                                        v-for="(step, index) in order.timeline"
                                        :key="step.status"
                                        class="timeline-item d-flex gap-3 mb-4"
                                    >
                                        <div class="flex-shrink-0">
                                            <div
                                                class="rounded-circle d-flex align-items-center justify-content-center"
                                                :class="step.completed ? 'bg-primary text-white' : 'bg-light text-muted'"
                                                style="width: 40px; height: 40px;"
                                            >
                                                <i
                                                    :class="[
                                                        'bi',
                                                        step.completed ? 'bi-check-circle-fill' : 'bi-circle',
                                                    ]"
                                                ></i>
                                            </div>
                                            <div
                                                v-if="index < order.timeline.length - 1"
                                                class="mx-auto"
                                                :class="step.completed ? 'bg-primary' : 'bg-light'"
                                                style="width: 2px; height: 40px;"
                                            ></div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold mb-1">{{ step.label }}</div>
                                            <div v-if="step.date" class="text-muted small">
                                                {{ formatDate(step.date) }}
                                            </div>
                                            <div v-else class="text-muted small">Pending</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <div class="mb-4">
                                <h3 class="h6 fw-bold mb-3">Order Items</h3>
                                <div class="table-responsive">
                                    <table class="table table-sm mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Item</th>
                                                <th class="text-end">Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="item in order.items" :key="item.sku">
                                                <td>
                                                    <div class="fw-semibold">{{ item.name }}</div>
                                                    <small class="text-muted">SKU: {{ item.sku }}</small>
                                                </td>
                                                <td class="text-end">{{ item.quantity }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Shipping Information -->
                            <div v-if="order.shipping_method" class="mb-3">
                                <h3 class="h6 fw-bold mb-2">Shipping</h3>
                                <p class="mb-1">
                                    <strong>Method:</strong> {{ order.shipping_method.name }}
                                </p>
                                <p v-if="order.estimated_delivery" class="mb-0 text-muted small">
                                    <strong>Estimated Delivery:</strong> {{ formatDate(order.estimated_delivery) }}
                                </p>
                            </div>

                            <!-- Auto-refresh indicator -->
                            <div class="text-center mt-4">
                                <small class="text-muted">
                                    <i class="bi bi-arrow-clockwise me-1"></i>
                                    Auto-refreshing every 10 seconds
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </LandingLayout>
</template>

<style scoped>
.timeline-item:last-child .flex-shrink-0 > div:last-child {
    display: none;
}
</style>

