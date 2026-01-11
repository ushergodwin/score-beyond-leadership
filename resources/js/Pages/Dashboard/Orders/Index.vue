<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';
import type { MoneyDisplay } from '@/types/shop';

interface Order {
    id: number;
    order_number: string;
    status: string;
    payment_status: string;
    grand_total: MoneyDisplay;
    items_count: number;
    placed_at: string | null;
    created_at: string;
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
    orders: {
        data: Order[];
        meta: PaginationMeta;
    };
    filters: {
        status?: string;
        payment_status?: string;
        search?: string;
        date_from?: string;
        date_to?: string;
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

const statusFilter = ref(props.filters.status || '');
const paymentStatusFilter = ref(props.filters.payment_status || '');
const searchTerm = ref(props.filters.search || '');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');
const showFilters = ref(false);

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

const applyFilters = () => {
    router.get(
        route('dashboard.orders'),
        {
            status: statusFilter.value || undefined,
            payment_status: paymentStatusFilter.value || undefined,
            search: searchTerm.value || undefined,
            date_from: dateFrom.value || undefined,
            date_to: dateTo.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const clearFilters = () => {
    statusFilter.value = '';
    paymentStatusFilter.value = '';
    searchTerm.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    applyFilters();
};

const paginationLinks = computed(() => props.orders.meta.links || []);

const page = usePage();
const isLoading = computed(() => page.props.processing ?? false);
</script>

<template>
    <Head title="My Orders" />
    <DashboardLayout>
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h1 class="h3 fw-bold text-primary mb-2">My Orders</h1>
                    <p class="text-muted mb-0">View and track all your orders</p>
                </div>
                <div class="d-flex gap-2 w-100 w-md-auto">
                    <div class="flex-grow-1" style="max-width: 400px;">
                        <input
                            v-model="searchTerm"
                            type="text"
                            class="form-control rounded-pill"
                            placeholder="Search by order number..."
                            @keyup.enter="applyFilters"
                        />
                    </div>
                    <!-- Mobile Filter Toggle Button -->
                    <button
                        class="btn btn-outline-primary rounded-pill d-lg-none position-relative"
                        type="button"
                        @click="showFilters = !showFilters"
                    >
                        <i class="bi" :class="showFilters ? 'bi-x-lg' : 'bi-funnel'"></i>
                        <span v-if="statusFilter || paymentStatusFilter || dateFrom || dateTo" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem; padding: 0.15rem 0.3rem;">
                            <span class="visually-hidden">Active filters</span>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="card border-0 shadow-sm rounded-4 mb-4" :class="{ 'd-none d-lg-block': !showFilters }">
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label small fw-semibold text-muted">Status</label>
                            <select v-model="statusFilter" class="form-select rounded-pill" @change="applyFilters">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label small fw-semibold text-muted">Payment Status</label>
                            <select v-model="paymentStatusFilter" class="form-select rounded-pill" @change="applyFilters">
                                <option value="">All Payments</option>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-2">
                            <label class="form-label small fw-semibold text-muted">From Date</label>
                            <input v-model="dateFrom" type="date" class="form-control rounded-pill" @change="applyFilters" />
                        </div>
                        <div class="col-12 col-md-6 col-lg-2">
                            <label class="form-label small fw-semibold text-muted">To Date</label>
                            <input v-model="dateTo" type="date" class="form-control rounded-pill" @change="applyFilters" />
                        </div>
                        <div class="col-12 col-md-6 col-lg-2 d-flex align-items-end">
                            <button class="btn btn-outline-secondary rounded-pill w-100" @click="clearFilters">
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5 text-center">
                    <LoadingSpinner size="lg" />
                    <p class="text-muted mt-3 mb-0">Loading orders...</p>
                </div>
            </div>

            <!-- Orders List -->
            <div v-else-if="orders.data.length === 0" class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5 text-center">
                    <i class="bi bi-bag text-muted" style="font-size: 4rem;"></i>
                    <h3 class="h5 fw-bold mt-3 mb-2">No orders found</h3>
                    <p class="text-muted mb-4">
                        {{ searchTerm || statusFilter || paymentStatusFilter ? 'Try adjusting your filters' : "You haven't placed any orders yet" }}
                    </p>
                    <Link v-if="!searchTerm && !statusFilter && !paymentStatusFilter" :href="route('shop.index')" class="btn btn-primary rounded-pill">
                        Start Shopping
                    </Link>
                </div>
            </div>

            <!-- Desktop Table View -->
            <div v-else class="d-none d-lg-block card border-0 shadow-sm rounded-4">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order Number</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="order in orders.data" :key="order.id">
                                <td>
                                    <div class="fw-semibold">{{ order.order_number }}</div>
                                </td>
                                <td>
                                    <div class="text-muted small">{{ order.placed_at || order.created_at }}</div>
                                </td>
                                <td>
                                    <div class="text-muted">{{ order.items_count }} item{{ order.items_count !== 1 ? 's' : '' }}</div>
                                </td>
                                <td>
                                    <span :class="getStatusBadgeClass(order.status)">
                                        {{ order.status }}
                                    </span>
                                </td>
                                <td>
                                    <span :class="getPaymentStatusBadgeClass(order.payment_status)">
                                        {{ order.payment_status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">
                                        {{ formatterUGX.format(order.grand_total.ugx) }}
                                    </div>
                                    <small class="text-muted">
                                        {{ formatterUSD.format(order.grand_total.usd) }}
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <Link
                                            :href="route('dashboard.orders.show', order.order_number)"
                                            class="btn btn-sm btn-outline-primary rounded-pill"
                                        >
                                            View
                                        </Link>
                                        <Link
                                            :href="route('dashboard.orders.track', order.order_number)"
                                            class="btn btn-sm btn-outline-secondary rounded-pill"
                                            title="Track Order"
                                        >
                                            <i class="bi bi-geo-alt"></i>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div v-if="!isLoading && orders.data.length > 0" class="d-lg-none d-flex flex-column gap-3">
                <Link
                    v-for="order in orders.data"
                    :key="order.id"
                    :href="route('dashboard.orders.show', order.order_number)"
                    class="card border-0 shadow-sm rounded-4 text-decoration-none text-dark"
                >
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div>
                                <h3 class="h6 fw-bold mb-1">{{ order.order_number }}</h3>
                                <p class="text-muted small mb-0">{{ order.placed_at || order.created_at }}</p>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span :class="getStatusBadgeClass(order.status)">
                                {{ order.status }}
                            </span>
                            <span :class="getPaymentStatusBadgeClass(order.payment_status)">
                                {{ order.payment_status }}
                            </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <Link
                                :href="route('dashboard.orders.track', order.order_number)"
                                class="btn btn-sm btn-outline-secondary rounded-pill"
                                @click.stop
                            >
                                <i class="bi bi-geo-alt me-1"></i>Track
                            </Link>
                            <div class="text-end">
                                <div class="fw-bold text-primary">
                                    {{ formatterUGX.format(order.grand_total.ugx) }}
                                </div>
                                <small class="text-muted">
                                    {{ formatterUSD.format(order.grand_total.usd) }}
                                </small>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="orders.meta.last_page > 1" class="d-flex justify-content-center mt-4">
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

.card:hover {
    transform: translateY(-2px);
    transition: all 0.2s ease;
}
</style>

