<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { showSuccess, showError } from '@/composables/useNotifications';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'), {
        onSuccess: () => {
            showSuccess('Password reset link has been sent to your email address.');
        },
        onError: () => {
            showError('Please check your email address and try again.');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div class="text-center mb-4">
            <h2 class="h3 fw-bold text-primary mb-2">Forgot Password</h2>
            <p class="text-muted">No problem. Just let us know your email address and we will email you a password reset link.</p>
        </div>

        <div
            v-if="status"
            class="alert alert-success mb-4"
            role="alert"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div class="mb-4">
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

            <PrimaryButton
                class="w-100 mb-3"
                :class="{ 'opacity-50': form.processing }"
                :disabled="form.processing"
            >
                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                Email Password Reset Link
            </PrimaryButton>

            <div class="text-center">
                <Link
                    :href="route('login')"
                    class="text-decoration-none text-primary small"
                >
                    ← Back to login
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
