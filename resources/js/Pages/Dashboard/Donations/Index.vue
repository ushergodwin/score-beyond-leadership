<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';

interface Donation {
    id: number;
    donation_number: string;
    amount: number;
    currency: string;
    payment_status: string;
    is_recurring: boolean;
    frequency: string | null;
    created_at: string;
    paid_at: string | null;
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
    donations: {
        data: Donation[];
        meta: PaginationMeta;
    };
    filters: {
        payment_status?: string;
        is_recurring?: boolean;
        search?: string;
        date_from?: string;
        date_to?: string;
    };
}>();

const paymentStatusFilter = ref(props.filters.payment_status || '');
const isRecurringFilter = ref(props.filters.is_recurring === undefined ? '' : String(props.filters.is_recurring));
const searchTerm = ref(props.filters.search || '');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');
const showFilters = ref(false);

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

const applyFilters = () => {
    router.get(
        route('dashboard.donations'),
        {
            payment_status: paymentStatusFilter.value || undefined,
            is_recurring: isRecurringFilter.value === '' ? undefined : isRecurringFilter.value === 'true',
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
    paymentStatusFilter.value = '';
    isRecurringFilter.value = '';
    searchTerm.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    applyFilters();
};

const paginationLinks = computed(() => props.donations.meta.links || []);

const page = usePage();
const isLoading = computed(() => page.props.processing ?? false);
</script>

<template>
    <Head title="My Donations" />
    <DashboardLayout>
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h1 class="h3 fw-bold text-primary mb-2">My Donations</h1>
                    <p class="text-muted mb-0">View all your donations and download receipts</p>
                </div>
                <div class="d-flex gap-2 w-100 w-md-auto">
                    <Link :href="route('donate.index')" class="btn btn-primary rounded-pill flex-grow-1 flex-md-grow-0">
                        <i class="bi bi-heart me-2"></i>Make a Donation
                    </Link>
                    <!-- Mobile Filter Toggle Button -->
                    <button
                        class="btn btn-outline-primary rounded-pill d-lg-none position-relative"
                        type="button"
                        @click="showFilters = !showFilters"
                    >
                        <i class="bi" :class="showFilters ? 'bi-x-lg' : 'bi-funnel'"></i>
                        <span v-if="paymentStatusFilter || isRecurringFilter || dateFrom || dateTo" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem; padding: 0.15rem 0.3rem;">
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
                            <label class="form-label small fw-semibold text-muted">Search</label>
                            <input
                                v-model="searchTerm"
                                type="text"
                                class="form-control rounded-pill"
                                placeholder="Donation number..."
                                @keyup.enter="applyFilters"
                            />
                        </div>
                        <div class="col-12 col-md-6 col-lg-2">
                            <label class="form-label small fw-semibold text-muted">Payment Status</label>
                            <select v-model="paymentStatusFilter" class="form-select rounded-pill" @change="applyFilters">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-2">
                            <label class="form-label small fw-semibold text-muted">Type</label>
                            <select v-model="isRecurringFilter" class="form-select rounded-pill" @change="applyFilters">
                                <option value="">All Types</option>
                                <option value="true">Recurring</option>
                                <option value="false">One-time</option>
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
                        <div class="col-12 col-md-6 col-lg-1 d-flex align-items-end">
                            <button class="btn btn-outline-secondary rounded-pill w-100" @click="clearFilters">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5 text-center">
                    <LoadingSpinner size="lg" />
                    <p class="text-muted mt-3 mb-0">Loading donations...</p>
                </div>
            </div>

            <!-- Donations List -->
            <div v-else-if="donations.data.length === 0" class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5 text-center">
                    <i class="bi bi-heart text-muted" style="font-size: 4rem;"></i>
                    <h3 class="h5 fw-bold mt-3 mb-2">No donations found</h3>
                    <p class="text-muted mb-4">
                        {{ searchTerm || paymentStatusFilter || isRecurringFilter ? 'Try adjusting your filters' : "You haven't made any donations yet" }}
                    </p>
                    <Link v-if="!searchTerm && !paymentStatusFilter && !isRecurringFilter" :href="route('donate.index')" class="btn btn-primary rounded-pill">
                        Make a Donation
                    </Link>
                </div>
            </div>

            <!-- Desktop Table View -->
            <div v-else class="d-none d-lg-block card border-0 shadow-sm rounded-4">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Donation Number</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Type</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="donation in donations.data" :key="donation.id">
                                <td>
                                    <div class="fw-semibold">{{ donation.donation_number }}</div>
                                </td>
                                <td>
                                    <div class="text-muted small">{{ donation.paid_at || donation.created_at }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-danger">
                                        {{ formatDonationAmount(donation.amount, donation.currency) }}
                                    </div>
                                </td>
                                <td>
                                    <span :class="getPaymentStatusBadgeClass(donation.payment_status)">
                                        {{ donation.payment_status }}
                                    </span>
                                </td>
                                <td>
                                    <span v-if="donation.is_recurring" class="badge bg-info-subtle text-info">
                                        Recurring ({{ donation.frequency }})
                                    </span>
                                    <span v-else class="badge bg-secondary-subtle text-secondary">
                                        One-time
                                    </span>
                                </td>
                                <td>
                                    <Link
                                        :href="route('dashboard.donations.show', donation.donation_number)"
                                        class="btn btn-sm btn-outline-primary rounded-pill"
                                    >
                                        View
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div v-if="!isLoading && donations.data.length > 0" class="d-lg-none d-flex flex-column gap-3">
                <Link
                    v-for="donation in donations.data"
                    :key="donation.id"
                    :href="route('dashboard.donations.show', donation.donation_number)"
                    class="card border-0 shadow-sm rounded-4 text-decoration-none text-dark"
                >
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div>
                                <h3 class="h6 fw-bold mb-1">{{ donation.donation_number }}</h3>
                                <p class="text-muted small mb-0">{{ donation.paid_at || donation.created_at }}</p>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span :class="getPaymentStatusBadgeClass(donation.payment_status)">
                                {{ donation.payment_status }}
                            </span>
                            <span v-if="donation.is_recurring" class="badge bg-info-subtle text-info">
                                Recurring
                            </span>
                            <span v-else class="badge bg-secondary-subtle text-secondary">
                                One-time
                            </span>
                        </div>
                        <div class="fw-bold text-danger fs-5">
                            {{ formatDonationAmount(donation.amount, donation.currency) }}
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="donations.meta.last_page > 1" class="d-flex justify-content-center mt-4">
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

