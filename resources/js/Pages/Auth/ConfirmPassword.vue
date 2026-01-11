<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { showError } from '@/composables/useNotifications';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();
        },
        onError: () => {
            showError('Invalid password. Please try again.');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirm Password" />

        <div class="text-center mb-4">
            <h2 class="h3 fw-bold text-primary mb-2">Confirm Password</h2>
            <p class="text-muted">This is a secure area of the application. Please confirm your password before continuing.</p>
        </div>

        <form @submit.prevent="submit">
            <div class="mb-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    autofocus
                    :class="{ 'is-invalid': form.errors.password }"
                />
                <InputError :message="form.errors.password" />
            </div>

            <PrimaryButton
                class="w-100"
                :class="{ 'opacity-50': form.processing }"
                :disabled="form.processing"
            >
                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                Confirm
            </PrimaryButton>
        </form>
    </GuestLayout>
</template>
