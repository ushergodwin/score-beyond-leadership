<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { showError } from '@/composables/useNotifications';

const props = defineProps<{
    email: string;
    token: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
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
        <Head title="Reset Password" />

        <div class="text-center mb-4">
            <h2 class="h3 fw-bold text-primary mb-2">Reset Password</h2>
            <p class="text-muted">Enter your new password below</p>
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
                Reset Password
            </PrimaryButton>
        </form>
    </GuestLayout>
</template>
