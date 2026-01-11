<script setup lang="ts">
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { showSuccess, showError } from '@/composables/useNotifications';

const props = defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();

const page = usePage();
const user = computed(() => page.props.auth?.user as { name: string; email: string; email_verified_at: string | null } | null);

if (!user.value) {
    throw new Error('Profile page requires an authenticated user.');
}

const profileForm = useForm({
    name: user.value.name,
    email: user.value.email,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const updateProfile = () => {
    profileForm.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            showSuccess('Profile updated successfully');
        },
        onError: () => {
            showError('Failed to update profile. Please check the errors and try again.');
        },
    });
};

const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
            showSuccess('Password updated successfully');
        },
        onError: () => {
            if (passwordForm.errors.password) {
                passwordForm.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (passwordForm.errors.current_password) {
                passwordForm.reset('current_password');
                currentPasswordInput.value?.focus();
            }
            showError('Failed to update password. Please check the errors and try again.');
        },
    });
};
</script>

<template>
    <Head title="Profile Settings" />
    <DashboardLayout>
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="mb-4">
                <h1 class="h3 fw-bold text-primary mb-2">Profile Settings</h1>
                <p class="text-muted mb-0">Manage your account information and preferences</p>
            </div>

            <div class="row g-4">
                <!-- Profile Information -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 p-4">
                            <h2 class="h5 fw-bold mb-0">Profile Information</h2>
                            <p class="text-muted small mb-0 mt-1">Update your account's profile information and email address.</p>
                        </div>
                        <div class="card-body p-4">
                            <form @submit.prevent="updateProfile">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">Name</label>
                                    <input
                                        id="name"
                                        v-model="profileForm.name"
                                        type="text"
                                        class="form-control rounded-pill"
                                        :class="{ 'is-invalid': profileForm.errors.name }"
                                        required
                                        autofocus
                                        autocomplete="name"
                                    />
                                    <div v-if="profileForm.errors.name" class="invalid-feedback">
                                        {{ profileForm.errors.name }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Email</label>
                                    <input
                                        id="email"
                                        v-model="profileForm.email"
                                        type="email"
                                        class="form-control rounded-pill"
                                        :class="{ 'is-invalid': profileForm.errors.email }"
                                        required
                                        autocomplete="username"
                                    />
                                    <div v-if="profileForm.errors.email" class="invalid-feedback">
                                        {{ profileForm.errors.email }}
                                    </div>
                                </div>

                                <div v-if="mustVerifyEmail && !user?.email_verified_at" class="alert alert-warning rounded-4 mb-3">
                                    <p class="mb-2">
                                        Your email address is unverified.
                                    </p>
                                    <Link
                                        :href="route('verification.send')"
                                        method="post"
                                        as="button"
                                        class="btn btn-sm btn-outline-primary rounded-pill"
                                    >
                                        Click here to re-send the verification email.
                                    </Link>
                                    <div
                                        v-if="status === 'verification-link-sent'"
                                        class="mt-2 text-success small"
                                    >
                                        A new verification link has been sent to your email address.
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-3">
                                    <button
                                        type="submit"
                                        class="btn btn-primary rounded-pill"
                                        :disabled="profileForm.processing"
                                    >
                                        <span v-if="profileForm.processing" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                        Save Changes
                                    </button>
                                    <Transition
                                        enter-active-class="transition ease-in-out"
                                        enter-from-class="opacity-0"
                                        leave-active-class="transition ease-in-out"
                                        leave-to-class="opacity-0"
                                    >
                                        <span v-if="profileForm.recentlySuccessful" class="text-success small">
                                            Saved.
                                        </span>
                                    </Transition>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Update Password -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 p-4">
                            <h2 class="h5 fw-bold mb-0">Update Password</h2>
                            <p class="text-muted small mb-0 mt-1">Ensure your account is using a long, random password to stay secure.</p>
                        </div>
                        <div class="card-body p-4">
                            <form @submit.prevent="updatePassword">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label fw-semibold">Current Password</label>
                                    <input
                                        id="current_password"
                                        ref="currentPasswordInput"
                                        v-model="passwordForm.current_password"
                                        type="password"
                                        class="form-control rounded-pill"
                                        :class="{ 'is-invalid': passwordForm.errors.current_password }"
                                        autocomplete="current-password"
                                    />
                                    <div v-if="passwordForm.errors.current_password" class="invalid-feedback">
                                        {{ passwordForm.errors.current_password }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">New Password</label>
                                    <input
                                        id="password"
                                        ref="passwordInput"
                                        v-model="passwordForm.password"
                                        type="password"
                                        class="form-control rounded-pill"
                                        :class="{ 'is-invalid': passwordForm.errors.password }"
                                        autocomplete="new-password"
                                    />
                                    <div v-if="passwordForm.errors.password" class="invalid-feedback">
                                        {{ passwordForm.errors.password }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                                    <input
                                        id="password_confirmation"
                                        v-model="passwordForm.password_confirmation"
                                        type="password"
                                        class="form-control rounded-pill"
                                        :class="{ 'is-invalid': passwordForm.errors.password_confirmation }"
                                        autocomplete="new-password"
                                    />
                                    <div v-if="passwordForm.errors.password_confirmation" class="invalid-feedback">
                                        {{ passwordForm.errors.password_confirmation }}
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-3">
                                    <button
                                        type="submit"
                                        class="btn btn-primary rounded-pill"
                                        :disabled="passwordForm.processing"
                                    >
                                        <span v-if="passwordForm.processing" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                        Update Password
                                    </button>
                                    <Transition
                                        enter-active-class="transition ease-in-out"
                                        enter-from-class="opacity-0"
                                        leave-active-class="transition ease-in-out"
                                        leave-to-class="opacity-0"
                                    >
                                        <span v-if="passwordForm.recentlySuccessful" class="text-success small">
                                            Saved.
                                        </span>
                                    </Transition>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 p-4">
                            <h2 class="h5 fw-bold mb-0">Account Information</h2>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Name</label>
                                <div class="fw-semibold">{{ user?.name }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Email</label>
                                <div>{{ user?.email }}</div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-semibold text-muted">Email Status</label>
                                <div>
                                    <span v-if="user?.email_verified_at" class="badge bg-success-subtle text-success">
                                        Verified
                                    </span>
                                    <span v-else class="badge bg-warning-subtle text-warning">
                                        Unverified
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
