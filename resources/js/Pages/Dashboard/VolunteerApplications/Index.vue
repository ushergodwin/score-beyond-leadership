<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';

interface VolunteerApplication {
    id: number;
    status: string;
    program_type: string;
    first_name: string;
    last_name: string;
    preferred_volunteer_role: string | null;
    availability_start: string | null;
    availability_end: string | null;
    payment_status: string;
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
    applications: {
        data: VolunteerApplication[];
        meta: PaginationMeta;
    };
    filters: {
        status?: string;
        program_type?: string;
        search?: string;
    };
}>();

const statusFilter = ref(props.filters.status || '');
const programTypeFilter = ref(props.filters.program_type || '');
const searchTerm = ref(props.filters.search || '');
const showFilters = ref(false);

const getStatusBadgeClass = (status: string) => {
    return {
        'badge bg-success-subtle text-success': status === 'approved',
        'badge bg-info-subtle text-info': status === 'reviewing',
        'badge bg-warning-subtle text-warning': status === 'submitted' || status === 'pending',
        'badge bg-danger-subtle text-danger': status === 'rejected',
    };
};

const getProgramTypeBadgeClass = (type: string) => {
    return {
        'badge bg-primary-subtle text-primary': type === 'paid',
        'badge bg-secondary-subtle text-secondary': type === 'unpaid',
    };
};

const applyFilters = () => {
    router.get(
        route('dashboard.volunteer-applications'),
        {
            status: statusFilter.value || undefined,
            program_type: programTypeFilter.value || undefined,
            search: searchTerm.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const clearFilters = () => {
    statusFilter.value = '';
    programTypeFilter.value = '';
    searchTerm.value = '';
    applyFilters();
};

const paginationLinks = computed(() => props.applications.meta.links || []);

const page = usePage();
const isLoading = computed(() => page.props.processing ?? false);

const formatDate = (dateString: string | null): string => {
    if (!dateString) return 'N/A';
    try {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        }).format(date);
    } catch {
        return dateString;
    }
};
</script>

<template>
    <Head title="Volunteer Applications" />
    <DashboardLayout>
        <div class="container-fluid py-4 px-3 px-md-4">
            <!-- Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h1 class="h3 fw-bold text-primary mb-2">Volunteer Applications</h1>
                    <p class="text-muted mb-0">View and track your volunteer applications</p>
                </div>
                <div class="d-flex gap-2 w-100 w-md-auto">
                    <div class="flex-grow-1" style="max-width: 400px;">
                        <input
                            v-model="searchTerm"
                            type="text"
                            class="form-control rounded-pill"
                            placeholder="Search applications..."
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
                        <span v-if="statusFilter || programTypeFilter" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem; padding: 0.15rem 0.3rem;">
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
                                <option value="submitted">Submitted</option>
                                <option value="pending">Pending</option>
                                <option value="reviewing">Reviewing</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label small fw-semibold text-muted">Program Type</label>
                            <select v-model="programTypeFilter" class="form-select rounded-pill" @change="applyFilters">
                                <option value="">All Types</option>
                                <option value="paid">Paid</option>
                                <option value="unpaid">Unpaid</option>
                            </select>
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
                    <p class="text-muted mt-3 mb-0">Loading applications...</p>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else-if="applications.data.length === 0" class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5 text-center">
                    <i class="bi bi-person-check text-muted" style="font-size: 4rem;"></i>
                    <h3 class="h5 fw-bold mt-3 mb-2">No applications found</h3>
                    <p class="text-muted mb-4">
                        {{ searchTerm || statusFilter || programTypeFilter ? 'Try adjusting your filters' : "You haven't submitted any volunteer applications yet" }}
                    </p>
                    <Link v-if="!searchTerm && !statusFilter && !programTypeFilter" :href="route('volunteer.index')" class="btn btn-primary rounded-pill">
                        <i class="bi bi-person-plus me-2"></i>Become a Volunteer
                    </Link>
                </div>
            </div>

            <!-- Desktop Table View -->
            <div v-else class="d-none d-lg-block card border-0 shadow-sm rounded-4">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Program Type</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Applied Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="application in applications.data" :key="application.id">
                                <td>
                                    <div class="fw-semibold">{{ application.first_name }} {{ application.last_name }}</div>
                                </td>
                                <td>
                                    <span :class="['badge rounded-pill fw-semibold', getProgramTypeBadgeClass(application.program_type)]">
                                        {{ application.program_type === 'paid' ? 'Paid' : 'Unpaid' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-muted small">{{ application.preferred_volunteer_role || 'N/A' }}</div>
                                </td>
                                <td>
                                    <span :class="['badge rounded-pill fw-semibold', getStatusBadgeClass(application.status)]">
                                        {{ application.status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-muted small">{{ formatDate(application.created_at) }}</div>
                                </td>
                                <td>
                                    <Link
                                        :href="route('dashboard.volunteer-applications.show', application.id)"
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
            <div v-if="!isLoading && applications.data.length > 0" class="d-lg-none d-flex flex-column gap-3">
                <Link
                    v-for="application in applications.data"
                    :key="application.id"
                    :href="route('dashboard.volunteer-applications.show', application.id)"
                    class="card border-0 shadow-sm rounded-4 text-decoration-none text-dark"
                >
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div>
                                <h3 class="h6 fw-bold mb-1">{{ application.first_name }} {{ application.last_name }}</h3>
                                <p class="text-muted small mb-0">{{ formatDate(application.created_at) }}</p>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span :class="['badge rounded-pill fw-semibold', getStatusBadgeClass(application.status)]">
                                {{ application.status }}
                            </span>
                            <span :class="['badge rounded-pill fw-semibold', getProgramTypeBadgeClass(application.program_type)]">
                                {{ application.program_type === 'paid' ? 'Paid' : 'Unpaid' }}
                            </span>
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-briefcase me-1"></i>{{ application.preferred_volunteer_role || 'N/A' }}
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="applications.meta.last_page > 1" class="d-flex justify-content-center mt-4">
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
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}
</style>

