<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { showError } from '@/composables/useNotifications';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
        onError: () => {
            showError('Please check the form for errors and try again.');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="text-center mb-4">
            <h2 class="h3 fw-bold text-primary mb-2">Create Account</h2>
            <p class="text-muted">Join Score Beyond Leadership today</p>
        </div>

        <form @submit.prevent="submit">
            <div class="mb-3">
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    :class="{ 'is-invalid': form.errors.name }"
                />

                <InputError :message="form.errors.name" />
            </div>

            <div class="mb-3">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
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
                    autocomplete="new-password"
                    :class="{ 'is-invalid': form.errors.password }"
                />

                <InputError :message="form.errors.password" />
            </div>

            <div class="mb-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    :class="{ 'is-invalid': form.errors.password_confirmation }"
                />

                <InputError :message="form.errors.password_confirmation" />
            </div>

            <PrimaryButton
                class="w-100"
                :class="{ 'opacity-50': form.processing }"
                :disabled="form.processing"
            >
                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                Register
            </PrimaryButton>

            <div class="text-center mt-4">
                <p class="text-muted mb-0">
                    Already have an account?
                    <Link :href="route('login')" class="text-primary text-decoration-none fw-semibold">
                        Sign in
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
