<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import type { MoneyDisplay } from '@/types/shop';

interface RecentOrder {
    id: number;
    order_number: string;
    status: string;
    payment_status: string;
    grand_total: MoneyDisplay;
    items_count: number;
    placed_at: string | null;
    created_at: string;
}

interface RecentDonation {
    id: number;
    donation_number: string;
    amount: number;
    currency: string;
    payment_status: string;
    is_recurring: boolean;
    created_at: string;
    paid_at: string | null;
}

const props = defineProps<{
    stats: {
        total_orders: number;
        total_donations: number;
        total_spent: MoneyDisplay;
        total_donated: {
            ugx: number;
            formatted: string;
        };
    };
    recent_orders: RecentOrder[];
    recent_donations: RecentDonation[];
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
        'badge bg-success-subtle text-success': status === 'completed' || status === 'processing',
        'badge bg-warning-subtle text-warning': status === 'pending',
        'badge bg-danger-subtle text-danger': status === 'failed' || status === 'payment_failed',
        'badge bg-info-subtle text-info': status === 'shipped' || status === 'delivered',
    };
};

const getPaymentStatusBadgeClass = (status: string) => {
    return {
        'badge bg-success-subtle text-success': status === 'completed',
        'badge bg-warning-subtle text-warning': status === 'pending',
        'badge bg-danger-subtle text-danger': status === 'failed',
    };
};

const formatDonationAmount = (amount: number, currency: string) => {
    const locale = currency === 'EUR' ? 'de-DE' : currency === 'USD' ? 'en-US' : 'en-UG';
    const options: Intl.NumberFormatOptions = {
        style: 'currency',
        currency: currency,
    };

    if (currency === 'UGX') {
        options.maximumFractionDigits = 0;
    } else {
        options.minimumFractionDigits = 2;
        options.maximumFractionDigits = 2;
    }

    return new Intl.NumberFormat(locale, options).format(amount);
};
</script>

<template>
    <Head title="Dashboard" />
    <DashboardLayout>
        <div class="container-fluid py-4">
            <!-- Welcome Section -->
            <div class="mb-4">
                <h1 class="h3 fw-bold text-primary mb-2">Welcome back!</h1>
                <p class="text-muted mb-0">Here's an overview of your activity</p>
            </div>

            <!-- Stats Cards -->
            <div class="row g-3 g-md-4 mb-4">
                <div class="col-12 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="bg-primary-subtle rounded-circle p-2">
                                    <i class="bi bi-bag text-primary" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                            <h3 class="h4 fw-bold mb-1">{{ stats.total_orders }}</h3>
                            <p class="text-muted small mb-0">Total Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="bg-danger-subtle rounded-circle p-2">
                                    <i class="bi bi-heart text-danger" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                            <h3 class="h4 fw-bold mb-1">{{ stats.total_donations }}</h3>
                            <p class="text-muted small mb-0">Total Donations</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="bg-success-subtle rounded-circle p-2">
                                    <i class="bi bi-currency-exchange text-success" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                            <h3 class="h6 fw-bold mb-1">
                                {{ formatterUGX.format(stats.total_spent.ugx) }}
                            </h3>
                            <p class="text-muted small mb-0">Total Spent</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="bg-warning-subtle rounded-circle p-2">
                                    <i class="bi bi-gift text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                            <h3 class="h6 fw-bold mb-1">{{ stats.total_donated.formatted }}</h3>
                            <p class="text-muted small mb-0">Total Donated</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Recent Orders -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 p-4 pb-0">
                            <div class="d-flex align-items-center justify-content-between">
                                <h2 class="h5 fw-bold mb-0">Recent Orders</h2>
                                <Link
                                    :href="route('dashboard.orders')"
                                    class="btn btn-link text-primary p-0 text-decoration-none"
                                >
                                    View All
                                    <i class="bi bi-arrow-right ms-1"></i>
                                </Link>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div v-if="recent_orders.length === 0" class="text-center py-5">
                                <i class="bi bi-bag text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-3 mb-0">No orders yet</p>
                                <Link :href="route('shop.index')" class="btn btn-primary rounded-pill mt-3">
                                    Start Shopping
                                </Link>
                            </div>
                            <div v-else class="d-flex flex-column gap-3">
                                <Link
                                    v-for="order in recent_orders"
                                    :key="order.id"
                                    :href="route('dashboard.orders.show', order.order_number)"
                                    class="text-decoration-none text-dark"
                                >
                                    <div class="d-flex align-items-start justify-content-between p-3 border rounded-4 hover-shadow">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <h3 class="h6 fw-bold mb-0">{{ order.order_number }}</h3>
                                                <span :class="getStatusBadgeClass(order.status)">
                                                    {{ order.status }}
                                                </span>
                                            </div>
                                            <p class="text-muted small mb-2">
                                                {{ order.items_count }} item{{ order.items_count !== 1 ? 's' : '' }} &middot; {{ order.placed_at || order.created_at }}
                                            </p>
                                            <div class="fw-bold text-primary">
                                                {{ formatterUGX.format(order.grand_total.ugx) }}
                                            </div>
                                        </div>
                                        <i class="bi bi-chevron-right text-muted ms-2"></i>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Donations -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 p-4 pb-0">
                            <div class="d-flex align-items-center justify-content-between">
                                <h2 class="h5 fw-bold mb-0">Recent Donations</h2>
                                <Link
                                    :href="route('dashboard.donations')"
                                    class="btn btn-link text-primary p-0 text-decoration-none"
                                >
                                    View All
                                    <i class="bi bi-arrow-right ms-1"></i>
                                </Link>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div v-if="recent_donations.length === 0" class="text-center py-5">
                                <i class="bi bi-heart text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-3 mb-0">No donations yet</p>
                                <Link :href="route('donate.index')" class="btn btn-primary rounded-pill mt-3">
                                    Make a Donation
                                </Link>
                            </div>
                            <div v-else class="d-flex flex-column gap-3">
                                <Link
                                    v-for="donation in recent_donations"
                                    :key="donation.id"
                                    :href="route('dashboard.donations.show', donation.donation_number)"
                                    class="text-decoration-none text-dark"
                                >
                                    <div class="d-flex align-items-start justify-content-between p-3 border rounded-4 hover-shadow">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <h3 class="h6 fw-bold mb-0">{{ donation.donation_number }}</h3>
                                                <span :class="getPaymentStatusBadgeClass(donation.payment_status)">
                                                    {{ donation.payment_status }}
                                                </span>
                                                <span
                                                    v-if="donation.is_recurring"
                                                    class="badge bg-info-subtle text-info"
                                                >
                                                    Recurring
                                                </span>
                                            </div>
                                            <p class="text-muted small mb-2">
                                                {{ donation.paid_at || donation.created_at }}
                                            </p>
                                            <div class="fw-bold text-danger">
                                                {{ formatDonationAmount(donation.amount, donation.currency) }}
                                            </div>
                                        </div>
                                        <i class="bi bi-chevron-right text-muted ms-2"></i>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<style scoped>
.hover-shadow {
    transition: all 0.2s ease;
}

.hover-shadow:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    transform: translateY(-2px);
}
</style>

