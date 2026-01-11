<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import scoreBeyondLogo from '../../images/scorebeyond-white.webp';

const isMenuOpen = ref(false);
const isSidebarOpen = ref(false);

const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const page = usePage();
const user = computed(() => page.props.auth?.user as { name: string; email: string } | null);
const unreadNotificationsCount = computed(() => Number(page.props.unreadNotificationsCount ?? 0));

const logout = () => {
    router.post(route('logout'));
};

// Close menu/sidebar when clicking outside
onMounted(() => {
    const handleClickOutside = (event: MouseEvent) => {
        const target = event.target as HTMLElement;
        if (!target.closest('.mobile-menu') && !target.closest('.menu-toggle')) {
            isMenuOpen.value = false;
        }
        if (!target.closest('.sidebar') && !target.closest('.sidebar-toggle')) {
            isSidebarOpen.value = false;
        }
    };
    document.addEventListener('click', handleClickOutside);
});

interface NavigationItem {
    name: string;
    route: string;
    icon: string;
    external?: boolean;
    action?: string;
    danger?: boolean;
}

const navigation: NavigationItem[] = [
    { name: 'Dashboard', route: 'dashboard', icon: 'bi-house-door' },
    { name: 'Orders', route: 'dashboard.orders', icon: 'bi-bag' },
    { name: 'Donations', route: 'dashboard.donations', icon: 'bi-heart' },
    { name: 'Volunteer', route: 'dashboard.volunteer-applications', icon: 'bi-person-check' },
    { name: 'Saved Items', route: 'dashboard.wishlist', icon: 'bi-bookmark' },
    { name: 'Notifications', route: 'dashboard.notifications', icon: 'bi-bell' },
    { name: 'Profile', route: 'profile.edit', icon: 'bi-person' },
    { name: 'Shop', route: 'shop.index', icon: 'bi-shop', external: true },
    { name: 'Logout', route: 'logout', icon: 'bi-box-arrow-right', action: 'logout', danger: true },
];
</script>

<template>
    <div class="dashboard-layout min-vh-100 d-flex flex-column">
        <!-- Top Navigation Bar (Mobile & Desktop) -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
            <div class="container-fluid">
                <!-- Mobile: Hamburger Menu + Logo -->
                <div class="d-flex align-items-center gap-3">
                    <button
                        class="btn btn-link text-white p-0 d-lg-none sidebar-toggle"
                        type="button"
                        @click="toggleSidebar"
                        aria-label="Toggle sidebar"
                    >
                        <i class="bi bi-list" style="font-size: 1.5rem;"></i>
                    </button>
                    <Link class="navbar-brand d-flex align-items-center gap-2" :href="route('home')">
                        <img :src="scoreBeyondLogo" alt="Score Beyond" height="32" />
                        <span class="fw-bold">Dashboard</span>
                    </Link>
                </div>

                <!-- Desktop: Navigation Links -->
                <div class="d-none d-lg-flex align-items-center gap-4">
                    <Link
                        v-for="item in navigation.slice(0, 4)"
                        :key="item.route"
                        :href="route(item.route)"
                        class="text-white text-decoration-none fw-semibold"
                        :class="{ 'text-warning': $page.url.startsWith('/dashboard/' + item.route.split('.').pop()) }"
                    >
                        {{ item.name }}
                    </Link>
                </div>

                <!-- User Menu -->
                <div class="d-flex align-items-center gap-3">
                    <!-- Notifications Badge (Mobile) -->
                    <Link
                        :href="route('dashboard.notifications')"
                        class="btn btn-link text-white p-0 d-lg-none position-relative"
                    >
                        <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                        <span
                            v-if="unreadNotificationsCount > 0"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size: 0.6rem;"
                        >
                            {{ unreadNotificationsCount > 9 ? '9+' : unreadNotificationsCount }}
                        </span>
                    </Link>

                    <!-- Notifications Dropdown (Desktop) -->
                    <div class="dropdown d-none d-lg-block">
                        <Link
                            :href="route('dashboard.notifications')"
                            class="btn btn-link text-white p-0 position-relative"
                        >
                            <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                            <span
                                v-if="unreadNotificationsCount > 0"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                style="font-size: 0.6rem;"
                            >
                                {{ unreadNotificationsCount > 9 ? '9+' : unreadNotificationsCount }}
                            </span>
                        </Link>
                    </div>

                    <!-- User Info (No Dropdown) -->
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-circle text-white" style="font-size: 1.5rem;"></i>
                        <span class="d-none d-md-inline text-white fw-semibold">{{ user?.name ?? 'User' }}</span>
                    </div>
                </div>
            </div>
        </nav>

        <div class="d-flex flex-grow-1 dashboard-content-wrapper">
            <!-- Sidebar (Desktop) -->
            <aside class="sidebar d-none d-lg-block bg-light border-end flex-shrink-0">
                <div class="p-3 d-flex flex-column h-100">
                    <nav class="nav flex-column gap-2 flex-grow-1">
                        <Link
                            v-for="item in navigation.filter(item => !item.action && !item.external)"
                            :key="item.route"
                            :href="route(item.route)"
                            class="nav-link rounded-pill d-flex align-items-center gap-3 px-3 py-2"
                            :class="{
                                'bg-primary text-white': $page.url.startsWith('/dashboard/' + item.route.split('.').pop()) || ($page.url === '/dashboard' && item.route === 'dashboard') || ($page.url.startsWith('/profile') && item.route === 'profile.edit'),
                                'text-dark': !($page.url.startsWith('/dashboard/' + item.route.split('.').pop()) || ($page.url === '/dashboard' && item.route === 'dashboard') || ($page.url.startsWith('/profile') && item.route === 'profile.edit')),
                            }"
                        >
                            <i :class="item.icon" style="font-size: 1.1rem;"></i>
                            <span>{{ item.name }}</span>
                        </Link>
                    </nav>
                    
                    <!-- Separator -->
                    <hr class="my-3" />
                    
                    <!-- External Links and Actions -->
                    <nav class="nav flex-column gap-2">
                        <Link
                            v-for="item in navigation.filter(item => item.external)"
                            :key="item.route"
                            :href="route(item.route)"
                            class="nav-link rounded-pill d-flex align-items-center gap-3 px-3 py-2 text-dark"
                        >
                            <i :class="item.icon" style="font-size: 1.1rem;"></i>
                            <span>{{ item.name }}</span>
                        </Link>
                        <button
                            v-for="item in navigation.filter(item => item.action)"
                            :key="item.route"
                            type="button"
                            class="nav-link rounded-pill d-flex align-items-center gap-3 px-3 py-2 border-0 w-100 text-start"
                            :class="{
                                'text-danger': item.danger,
                                'text-dark': !item.danger,
                            }"
                            @click="logout"
                        >
                            <i :class="item.icon" style="font-size: 1.1rem;"></i>
                            <span>{{ item.name }}</span>
                        </button>
                    </nav>
                </div>
            </aside>

            <!-- Mobile Sidebar Overlay -->
            <div
                v-if="isSidebarOpen"
                class="offcanvas offcanvas-start d-lg-none"
                :class="{ show: isSidebarOpen }"
                tabindex="-1"
                style="visibility: visible;"
            >
                <div class="offcanvas-header border-bottom">
                    <h5 class="offcanvas-title fw-bold">Menu</h5>
                    <button
                        type="button"
                        class="btn-close"
                        @click="toggleSidebar"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="offcanvas-body p-0 d-flex flex-column">
                    <nav class="nav flex-column flex-grow-1">
                        <Link
                            v-for="item in navigation.filter(item => !item.action && !item.external)"
                            :key="item.route"
                            :href="route(item.route)"
                            class="nav-link px-4 py-3 d-flex align-items-center gap-3 border-bottom"
                            :class="{
                                'bg-primary text-white': $page.url.startsWith('/dashboard/' + item.route.split('.').pop()) || ($page.url === '/dashboard' && item.route === 'dashboard') || ($page.url.startsWith('/profile') && item.route === 'profile.edit'),
                            }"
                            @click="toggleSidebar"
                        >
                            <i :class="item.icon" style="font-size: 1.1rem;"></i>
                            <span>{{ item.name }}</span>
                        </Link>
                    </nav>
                    
                    <!-- Separator -->
                    <hr class="my-0" />
                    
                    <!-- External Links and Actions -->
                    <nav class="nav flex-column">
                        <Link
                            v-for="item in navigation.filter(item => item.external)"
                            :key="item.route"
                            :href="route(item.route)"
                            class="nav-link px-4 py-3 d-flex align-items-center gap-3 border-bottom"
                            @click="toggleSidebar"
                        >
                            <i :class="item.icon" style="font-size: 1.1rem;"></i>
                            <span>{{ item.name }}</span>
                        </Link>
                        <button
                            v-for="item in navigation.filter(item => item.action)"
                            :key="item.route"
                            type="button"
                            class="nav-link px-4 py-3 d-flex align-items-center gap-3 border-bottom border-0 w-100 text-start"
                            :class="{
                                'text-danger': item.danger,
                            }"
                            @click="logout; toggleSidebar()"
                        >
                            <i :class="item.icon" style="font-size: 1.1rem;"></i>
                            <span>{{ item.name }}</span>
                        </button>
                    </nav>
                </div>
            </div>
            <div
                v-if="isSidebarOpen"
                class="offcanvas-backdrop fade show d-lg-none"
                @click="toggleSidebar"
            ></div>

            <!-- Main Content -->
            <main class="flex-grow-1 bg-white overflow-x-hidden dashboard-main-content">
                <slot />
            </main>
        </div>

        <!-- Bottom Navigation (Mobile Only) -->
        <nav class="d-lg-none navbar navbar-dark bg-primary border-top fixed-bottom">
            <div class="container-fluid">
                <div class="d-flex justify-content-around w-100">
                    <Link
                        v-for="item in navigation.slice(0, 4)"
                        :key="item.route"
                        :href="route(item.route)"
                        class="text-white text-center py-2 px-3 text-decoration-none d-flex flex-column align-items-center"
                        :class="{
                            'text-warning': $page.url.startsWith('/dashboard/' + item.route.split('.').pop()) || ($page.url === '/dashboard' && item.route === 'dashboard'),
                        }"
                    >
                        <i :class="item.icon" style="font-size: 1.25rem;"></i>
                        <small style="font-size: 0.7rem;">{{ item.name }}</small>
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Add padding bottom on mobile to account for bottom nav -->
        <div class="d-lg-none" style="height: 70px;"></div>
    </div>
</template>

<style scoped>
.dashboard-layout {
    background-color: #f8f9fa;
}

.navbar {
    background: linear-gradient(135deg, #a01d62 0%, #f03733 100%) !important;
    z-index: 1030;
    height: 56px;
}

/* Content wrapper to account for fixed navbar */
.dashboard-content-wrapper {
    margin-top: 56px;
    min-height: calc(100vh - 56px);
}

/* Sidebar - Fixed on desktop */
.sidebar {
    position: fixed;
    top: 56px;
    left: 0;
    width: 250px;
    height: calc(100vh - 56px);
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 1020;
}

/* Main content area - accounts for fixed navbar and sidebar */
.dashboard-main-content {
    min-width: 0;
    width: 100%;
    margin-left: 0;
    padding: 1.5rem;
    min-height: calc(100vh - 56px);
}

/* Desktop: Add left margin for sidebar */
@media (min-width: 992px) {
    .dashboard-main-content {
        margin-left: 250px;
        padding: 1.5rem;
    }
}

/* Mobile: Add bottom padding for fixed bottom nav */
@media (max-width: 991.98px) {
    .dashboard-main-content {
        padding-bottom: 80px;
    }
}

.nav-link {
    transition: all 0.2s ease;
}

.nav-link:hover {
    background-color: rgba(160, 29, 98, 0.1) !important;
}

.nav-link.bg-primary {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.offcanvas {
    z-index: 1045;
}

.offcanvas-backdrop {
    z-index: 1040;
}

/* Bottom navigation styling */
.navbar.fixed-bottom {
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    padding: 0;
    z-index: 1030;
    height: 70px;
}

.navbar.fixed-bottom .container-fluid {
    padding: 0;
}

/* Smooth scrolling for sidebar */
.sidebar {
    scrollbar-width: thin;
    scrollbar-color: rgba(160, 29, 98, 0.3) transparent;
}

.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar::-webkit-scrollbar-thumb {
    background-color: rgba(160, 29, 98, 0.3);
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background-color: rgba(160, 29, 98, 0.5);
}

/* Sidebar button styling for logout */
.sidebar button.nav-link {
    background: transparent;
    cursor: pointer;
}

.sidebar button.nav-link:hover {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.sidebar button.nav-link.text-danger:hover {
    background-color: rgba(220, 53, 69, 0.15) !important;
    color: #dc3545 !important;
}
</style>

