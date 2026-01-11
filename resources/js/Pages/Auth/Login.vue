<script setup lang="ts">
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { showSuccess, showError } from '@/composables/useNotifications';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
        onError: () => {
            showError('Invalid credentials. Please try again.');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="text-center mb-4">
            <h2 class="h3 fw-bold text-primary mb-2">Welcome Back</h2>
            <p class="text-muted">Sign in to your account to continue</p>
        </div>

        <div v-if="status" class="alert alert-success mb-4" role="alert">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div class="mb-3">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    :class="{ 'is-invalid': form.errors.email }"
                />

                <InputError :message="form.errors.email" />
            </div>

            <div class="mb-3">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    :class="{ 'is-invalid': form.errors.password }"
                />

                <InputError :message="form.errors.password" />
            </div>

            <div class="mb-3 form-check">
                <Checkbox name="remember" v-model:checked="form.remember" />
                <label class="form-check-label ms-2" for="remember">
                    Remember me
                </label>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-decoration-none text-primary small"
                >
                    Forgot your password?
                </Link>
                <span v-else></span>
            </div>

            <PrimaryButton
                class="w-100"
                :class="{ 'opacity-50': form.processing }"
                :disabled="form.processing"
            >
                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                Log in
            </PrimaryButton>

            <div class="text-center mt-4">
                <p class="text-muted mb-0">
                    Don't have an account?
                    <Link :href="route('register')" class="text-primary text-decoration-none fw-semibold">
                        Sign up
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
