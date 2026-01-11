<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import type { MoneyDisplay } from '@/types/shop';

interface OrderItem {
    id: number;
    product_name: string;
    sku: string | null;
    quantity: number;
    unit_price: MoneyDisplay;
    subtotal: MoneyDisplay;
}

interface Address {
    name: string;
    line_1: string;
    line_2: string | null;
    city: string | null;
    state: string | null;
    postal_code: string | null;
    country: string | null;
    phone: string | null;
}

interface TimelineItem {
    status: string;
    label: string;
    date: string | null;
    completed: boolean;
}

const props = defineProps<{
    order: {
        id: number;
        order_number: string;
        status: string;
        payment_status: string;
        currency: string;
        subtotal: MoneyDisplay;
        shipping_total: MoneyDisplay;
        tax_total: MoneyDisplay;
        grand_total: MoneyDisplay;
        placed_at: string | null;
        created_at: string;
        items: OrderItem[];
        shipping_address: Address | null;
        billing_address: Address | null;
        shipping_method: { name: string; description: string | null } | null;
        timeline: TimelineItem[];
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

const getStatusBadgeClass = (status: string) => {
    return {
        'badge bg-success-subtle text-success': status === 'completed' || status === 'processing' || status === 'delivered',
        'badge bg-warning-subtle text-warning': status === 'pending',
        'badge bg-danger-subtle text-danger': status === 'failed' || status === 'payment_failed' || status === 'cancelled',
        'badge bg-info-subtle text-info': status === 'shipped',
    };
};

const getPaymentStatusBadgeClass = (status: string) => {
    return {
        'badge bg-success-subtle text-success': status === 'completed',
        'badge bg-warning-subtle text-warning': status === 'pending',
        'badge bg-danger-subtle text-danger': status === 'failed',
    };
};

const formatTimelineDate = (date: string | null) => {
    if (!date) return 'Pending';
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head :title="`Order ${order.order_number}`" />
    <DashboardLayout>
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <Link :href="route('dashboard.orders')" class="text-decoration-none text-muted mb-2 d-inline-flex align-items-center gap-2">
                        <i class="bi bi-arrow-left"></i>
                        <span>Back to Orders</span>
                    </Link>
                    <h1 class="h3 fw-bold text-primary mb-2 mt-2">Order {{ order.order_number }}</h1>
                    <div class="d-flex flex-wrap gap-2">
                        <span :class="getStatusBadgeClass(order.status)">
                            {{ order.status }}
                        </span>
                        <span :class="getPaymentStatusBadgeClass(order.payment_status)">
                            {{ order.payment_status }}
                        </span>
                    </div>
                </div>
                <Link
                    :href="route('dashboard.orders.track', order.order_number)"
                    class="btn btn-primary rounded-pill"
                >
                    <i class="bi bi-geo-alt me-2"></i>Track Order
                </Link>
            </div>

            <div class="row g-4">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Order Timeline -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 p-4">
                            <h2 class="h5 fw-bold mb-0">Order Timeline</h2>
                        </div>
                        <div class="card-body p-4">
                            <div class="timeline">
                                <div
                                    v-for="(step, index) in order.timeline"
                                    :key="step.status"
                                    class="timeline-item d-flex gap-3"
                                >
                                    <div class="flex-shrink-0">
                                        <div
                                            class="timeline-icon rounded-circle d-flex align-items-center justify-content-center"
                                            :class="{
                                                'bg-success text-white': step.completed,
                                                'bg-light text-muted border': !step.completed,
                                            }"
                                        >
                                            <i
                                                :class="{
                                                    'bi-check-circle-fill': step.completed,
                                                    'bi-circle': !step.completed,
                                                }"
                                            ></i>
                                        </div>
                                        <div
                                            v-if="index < order.timeline.length - 1"
                                            class="timeline-line"
                                            :class="{ 'bg-success': step.completed, 'bg-light': !step.completed }"
                                        ></div>
                                    </div>
                                    <div class="flex-grow-1 pb-4">
                                        <h3 class="h6 fw-bold mb-1">{{ step.label }}</h3>
                                        <p class="text-muted small mb-0">{{ formatTimelineDate(step.date) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 p-4">
                            <h2 class="h5 fw-bold mb-0">Order Items</h2>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Product</th>
                                            <th>SKU</th>
                                            <th>Quantity</th>
                                            <th class="text-end pe-4">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="item in order.items" :key="item.id">
                                            <td class="ps-4">
                                                <div class="fw-semibold">{{ item.product_name }}</div>
                                            </td>
                                            <td>
                                                <span class="text-muted small">{{ item.sku ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ item.quantity }}</span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="fw-bold">
                                                    {{ formatterUGX.format(item.subtotal.ugx) }}
                                                </div>
                                                <small class="text-muted">
                                                    {{ formatterUSD.format(item.subtotal.usd) }}
                                                </small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Order Summary -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 p-4">
                            <h2 class="h5 fw-bold mb-0">Order Summary</h2>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Subtotal</span>
                                <span class="fw-semibold">
                                    {{ formatterUGX.format(order.subtotal.ugx) }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Shipping</span>
                                <span class="fw-semibold">
                                    {{ formatterUGX.format(order.shipping_total.ugx) }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Tax</span>
                                <span class="fw-semibold">
                                    {{ formatterUGX.format(order.tax_total.ugx) }}
                                </span>
                            </div>
                            <hr />
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Total</span>
                                <div class="text-end">
                                    <div class="h5 fw-bold text-primary mb-0">
                                        {{ formatterUGX.format(order.grand_total.ugx) }}
                                    </div>
                                    <small class="text-muted">
                                        {{ formatterUSD.format(order.grand_total.usd) }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div v-if="order.shipping_address" class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 p-4">
                            <h2 class="h5 fw-bold mb-0">Shipping Address</h2>
                        </div>
                        <div class="card-body p-4">
                            <p class="mb-1 fw-semibold">{{ order.shipping_address.name }}</p>
                            <p class="mb-1 text-muted small">
                                {{ order.shipping_address.line_1 }}<br />
                                <span v-if="order.shipping_address.line_2">{{ order.shipping_address.line_2 }}<br /></span>
                                <span v-if="order.shipping_address.city">{{ order.shipping_address.city }}, </span>
                                <span v-if="order.shipping_address.state">{{ order.shipping_address.state }} </span>
                                <span v-if="order.shipping_address.postal_code">{{ order.shipping_address.postal_code }}<br /></span>
                                <span v-if="order.shipping_address.country">{{ order.shipping_address.country }}</span>
                            </p>
                            <p v-if="order.shipping_address.phone" class="mb-0 text-muted small">
                                <i class="bi bi-telephone me-1"></i>{{ order.shipping_address.phone }}
                            </p>
                        </div>
                    </div>

                    <!-- Shipping Method -->
                    <div v-if="order.shipping_method" class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 p-4">
                            <h2 class="h5 fw-bold mb-0">Shipping Method</h2>
                        </div>
                        <div class="card-body p-4">
                            <p class="fw-semibold mb-1">{{ order.shipping_method.name }}</p>
                            <p v-if="order.shipping_method.description" class="text-muted small mb-0">
                                {{ order.shipping_method.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<style scoped>
.timeline {
    position: relative;
}

.timeline-item {
    position: relative;
}

.timeline-icon {
    width: 40px;
    height: 40px;
    font-size: 1.25rem;
    z-index: 1;
}

.timeline-line {
    width: 2px;
    height: 60px;
    margin: 0 auto;
    margin-top: 0.5rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    color: #6c757d;
    padding: 1rem;
}

.table td {
    padding: 1rem;
    vertical-align: middle;
}
</style>

