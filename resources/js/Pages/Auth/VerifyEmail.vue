<script setup lang="ts">
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { showSuccess, showError } from '@/composables/useNotifications';

const props = defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'), {
        onSuccess: () => {
            showSuccess('A new verification link has been sent to your email address.');
        },
        onError: () => {
            showError('Unable to send verification email. Please try again.');
        },
    });
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <div class="text-center mb-4">
            <h2 class="h3 fw-bold text-primary mb-2">Verify Your Email</h2>
            <p class="text-muted">Thanks for signing up! Before getting started, could you verify your email address?</p>
        </div>

        <div
            v-if="verificationLinkSent"
            class="alert alert-success mb-4"
            role="alert"
        >
            A new verification link has been sent to the email address you provided during registration.
        </div>

        <div class="alert alert-info mb-4" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            If you didn't receive the email, we will gladly send you another.
        </div>

        <form @submit.prevent="submit" class="mb-4">
            <PrimaryButton
                class="w-100"
                :class="{ 'opacity-50': form.processing }"
                :disabled="form.processing"
            >
                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                Resend Verification Email
            </PrimaryButton>
        </form>

        <div class="text-center">
            <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="btn btn-link text-decoration-none text-muted"
            >
                Log Out
            </Link>
        </div>
    </GuestLayout>
</template>
