<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';

interface Notification {
    id: number;
    type: string;
    title: string;
    message: string;
    action_url: string | null;
    is_read: boolean;
    read_at: string | null;
    created_at: string;
    data: Record<string, unknown> | null;
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
    notifications: {
        data: Notification[];
        meta: PaginationMeta;
    };
}>();

const markAsRead = (notification: Notification) => {
    if (!notification.is_read) {
        router.post(route('dashboard.notifications.read', notification.id), {}, {
            preserveScroll: true,
            preserveState: true,
        });
    }
};

const markAllAsRead = () => {
    router.post(route('dashboard.notifications.read-all'), {}, {
        preserveScroll: true,
        preserveState: true,
    });
};

const getNotificationIcon = (type: string) => {
    if (type.startsWith('order_')) {
        return 'bi-bag';
    }
    if (type.startsWith('donation_')) {
        return 'bi-heart';
    }
    return 'bi-bell';
};

const getNotificationColor = (type: string) => {
    if (type.includes('confirmed') || type.includes('shipped') || type.includes('delivered')) {
        return 'text-success';
    }
    if (type.includes('failed')) {
        return 'text-danger';
    }
    return 'text-primary';
};

const paginationLinks = computed(() => props.notifications.meta.links || []);

const page = usePage();
const isLoading = computed(() => page.props.processing ?? false);
</script>

<template>
    <Head title="Notifications" />
    <DashboardLayout>
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h1 class="h3 fw-bold text-primary mb-2">Notifications</h1>
                    <p class="text-muted mb-0">Stay updated on your orders and donations</p>
                </div>
                <button
                    v-if="notifications.data.some(n => !n.is_read)"
                    class="btn btn-outline-primary rounded-pill"
                    @click="markAllAsRead"
                >
                    <i class="bi bi-check-all me-2"></i>Mark All as Read
                </button>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5 text-center">
                    <LoadingSpinner size="lg" />
                    <p class="text-muted mt-3 mb-0">Loading notifications...</p>
                </div>
            </div>

            <!-- Notifications List -->
            <div v-else-if="notifications.data.length === 0" class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5 text-center">
                    <i class="bi bi-bell text-muted" style="font-size: 4rem;"></i>
                    <h3 class="h5 fw-bold mt-3 mb-2">No notifications</h3>
                    <p class="text-muted mb-0">You're all caught up! We'll notify you when there's something new.</p>
                </div>
            </div>

            <div v-else class="d-flex flex-column gap-3">
                <div
                    v-for="notification in notifications.data"
                    :key="notification.id"
                    class="card border-0 shadow-sm rounded-4"
                    :class="{ 'bg-light': notification.is_read, 'border-primary border-2': !notification.is_read }"
                >
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start gap-3">
                            <div
                                class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center"
                                :class="getNotificationColor(notification.type)"
                                style="width: 48px; height: 48px; background-color: rgba(160, 29, 98, 0.1);"
                            >
                                <i :class="getNotificationIcon(notification.type)" style="font-size: 1.5rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
                                    <div>
                                        <h3 class="h6 fw-bold mb-1">{{ notification.title }}</h3>
                                        <p class="text-muted small mb-0">{{ notification.created_at }}</p>
                                    </div>
                                    <span
                                        v-if="!notification.is_read"
                                        class="badge bg-primary rounded-pill"
                                    >
                                        New
                                    </span>
                                </div>
                                <p class="mb-3">{{ notification.message }}</p>
                                <div class="d-flex gap-2">
                                    <Link
                                        v-if="notification.action_url"
                                        :href="notification.action_url"
                                        class="btn btn-sm btn-primary rounded-pill"
                                        @click="markAsRead(notification)"
                                    >
                                        View Details
                                    </Link>
                                    <button
                                        v-if="!notification.is_read"
                                        class="btn btn-sm btn-outline-secondary rounded-pill"
                                        @click="markAsRead(notification)"
                                    >
                                        Mark as Read
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="notifications.meta.last_page > 1" class="d-flex justify-content-center mt-4">
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
    transform: translateY(-2px);
    transition: all 0.2s ease;
}
</style>

