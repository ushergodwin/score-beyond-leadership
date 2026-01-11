<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import LandingLayout from '@/Layouts/LandingLayout.vue';
import { showSuccess, showError, showWarning } from '@/composables/useNotifications';

interface ImpactTier {
    amount: number;
    label: string;
    description: string;
    currency: string;
}

const props = defineProps<{
    impactTiers: ImpactTier[];
    prefill?: {
        first_name?: string;
        email?: string;
    };
}>();

const selectedTier = ref<ImpactTier | null>(null);
const customAmount = ref<number | null>(null);

const form = useForm({
    first_name: props.prefill?.first_name ?? '',
    last_name: '',
    email: props.prefill?.email ?? '',
    phone: '',
    country: 'UG',
    organization: '',
    address: '',
    amount: 0,
    currency: 'UGX',
    is_recurring: false,
    frequency: null as string | null,
    impact_tier: null as string | null,
    payment_method: 'pesapal_visa_mastercard',
    consent_to_contact: false,
    communications_opt_in: false,
    message: '',
});

const currencyOptions = [
    { value: 'UGX', label: 'UGX (Ugandan Shilling)', symbol: 'UGX', min: 1000 },
    { value: 'USD', label: 'USD (US Dollar)', symbol: '$', min: 1 },
    { value: 'EUR', label: 'AURO (Euro)', symbol: '€', min: 1 },
];

const minAmount = computed(() => {
    const currency = currencyOptions.find((c) => c.value === form.currency);
    return currency?.min ?? 1000;
});

const currencySymbol = computed(() => {
    const currency = currencyOptions.find((c) => c.value === form.currency);
    return currency?.symbol ?? 'UGX';
});

const selectTier = (tier: ImpactTier) => {
    selectedTier.value = tier;
    customAmount.value = null;
    form.amount = tier.amount;
    form.currency = tier.currency;
    form.impact_tier = tier.label;
    // Reset frequency to one-time when selecting a tier
    if (!form.frequency || form.frequency === 'one-time') {
        form.frequency = 'one-time';
        form.is_recurring = false;
    }
};

const setCustomAmount = () => {
    if (customAmount.value && customAmount.value >= minAmount.value) {
        selectedTier.value = null;
        form.amount = customAmount.value;
        form.impact_tier = null;
        // Reset frequency to one-time when setting custom amount
        if (!form.frequency || form.frequency === 'one-time') {
            form.frequency = 'one-time';
            form.is_recurring = false;
        }
    }
};

const onCurrencyChange = () => {
    // Reset amount when currency changes
    selectedTier.value = null;
    customAmount.value = null;
    form.amount = 0;
    form.impact_tier = null;
};

const submit = () => {
    if (!isAmountValid.value) {
        showWarning(`Minimum donation is ${formattedAmount(minAmount.value, form.currency)}`);
        return;
    }

    // Set is_recurring based on frequency
    form.is_recurring = form.frequency !== null && form.frequency !== 'one-time';
    if (!form.frequency) {
        form.frequency = 'one-time';
    }

    form.post(route('donate.store'), {
        onSuccess: () => {
            showSuccess('Redirecting to payment...');
        },
        onError: () => {
            showError('Please check the form for errors and try again.');
        },
    });
};

const formattedAmount = (amount: number, currency?: string) => {
    const curr = currency ?? form.currency;
    const locale = curr === 'EUR' ? 'de-DE' : curr === 'USD' ? 'en-US' : 'en-UG';
    const options: Intl.NumberFormatOptions = {
        style: 'currency',
        currency: curr,
    };

    if (curr === 'UGX') {
        options.maximumFractionDigits = 0;
    } else {
        options.minimumFractionDigits = 2;
        options.maximumFractionDigits = 2;
    }

    return new Intl.NumberFormat(locale, options).format(amount);
};

const isAmountValid = computed(() => {
    return form.amount >= minAmount.value;
});

const paymentOptions = [
    { value: 'pesapal_visa_mastercard', label: 'Visa / Mastercard', icon: 'bi-credit-card' },
    { value: 'pesapal_mobile_money', label: 'Mobile Money', icon: 'bi-phone' },
];
</script>

<template>
    <Head title="Donate" />
    <LandingLayout>
        <template #hero>
            <section class="py-5 section-bg-gradient-dark border-bottom text-white">
                <div class="container">
                    <h1 class="display-4 fw-bold mb-3">Make a Donation</h1>
                    <p class="lead mb-0 text-white-50">
                        Your contribution directly supports our mission to empower women and girls through sports, education, health, and livelihood opportunities.
                    </p>
                </div>
            </section>
        </template>

        <div class="container pt-4 pb-5">
            <div class="row g-5">
                <!-- Donation Form -->
                <div class="col-lg-8">
                    <div class="form-card">
                            <!-- Currency Selection -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Select Currency</label>
                                <select
                                    v-model="form.currency"
                                    class="form-select"
                                    @change="onCurrencyChange"
                                >
                                    <option
                                        v-for="option in currencyOptions"
                                        :key="option.value"
                                        :value="option.value"
                                    >
                                        {{ option.label }}
                                    </option>
                                </select>
                            </div>

                            <h2 class="h4 fw-bold mb-4">Choose Your Impact</h2>

                            <!-- Impact Tiers -->
                            <div class="row g-3 mb-4">
                                <div
                                    v-for="tier in impactTiers"
                                    :key="tier.label"
                                    class="col-md-12"
                                >
                                    <div class="d-flex gap-3 align-items-start">
                                        <button
                                            v-if="tier.currency === form.currency"
                                            type="button"
                                            class="impact-tier-card w-100 text-start h-100"
                                            :class="{ active: selectedTier?.label === tier.label }"
                                            @click="selectTier(tier)"
                                        >
                                            <div class="fw-bold text-primary mb-1">{{ tier.label }}</div>
                                            <div class="fs-5 fw-bold mb-1">{{ formattedAmount(tier.amount, tier.currency) }}</div>
                                            <div class="text-muted small">{{ tier.description }}</div>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Amount -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Or enter a custom amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ currencySymbol }}</span>
                                    <input
                                        v-model.number="customAmount"
                                        type="number"
                                        class="form-control"
                                        :placeholder="`Minimum ${formattedAmount(minAmount, form.currency)}`"
                                        :min="minAmount"
                                        :step="form.currency === 'UGX' ? 1000 : 1"
                                        @input="setCustomAmount"
                                    />
                                </div>
                                <small class="text-muted">
                                    Minimum donation: {{ formattedAmount(minAmount, form.currency) }}
                                </small>
                            </div>

                            <div class="alert alert-info rounded-4 mb-4" role="alert">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Your Impact:</strong> Every donation directly funds programs that empower women and girls through sports, education, health, and livelihood opportunities.
                            </div>

                            <!-- Donor Information -->
                            <h3 class="h5 fw-bold mb-3 mt-5">Your Information</h3>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input
                                        v-model="form.first_name"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.first_name }"
                                        required
                                    />
                                    <div v-if="form.errors.first_name" class="invalid-feedback">
                                        {{ form.errors.first_name }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input
                                        v-model="form.last_name"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.last_name }"
                                        required
                                    />
                                    <div v-if="form.errors.last_name" class="invalid-feedback">
                                        {{ form.errors.last_name }}
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.email }"
                                        required
                                    />
                                    <div v-if="form.errors.email" class="invalid-feedback">
                                        {{ form.errors.email }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.phone }"
                                    />
                                    <div v-if="form.errors.phone" class="invalid-feedback">
                                        {{ form.errors.phone }}
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Message (Optional)</label>
                                <textarea
                                    v-model="form.message"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Share why you're supporting Score Beyond Leadership..."
                                    :class="{ 'is-invalid': form.errors.message }"
                                ></textarea>
                                <div v-if="form.errors.message" class="invalid-feedback">
                                    {{ form.errors.message }}
                                </div>
                            </div>


                            <!-- Payment Method -->
                            <h3 class="h5 fw-bold mb-3 mt-5">Payment Method</h3>
                            <div class="d-flex flex-column gap-3 mb-4">
                                <label
                                    v-for="option in paymentOptions"
                                    :key="option.value"
                                    class="border rounded-4 p-3 d-flex align-items-center gap-3 cursor-pointer"
                                    :class="{
                                        'border-primary shadow-sm': form.payment_method === option.value,
                                        'border-light': form.payment_method !== option.value,
                                    }"
                                >
                                    <input
                                        v-model="form.payment_method"
                                        class="form-check-input"
                                        type="radio"
                                        :value="option.value"
                                    />
                                    <i :class="`bi ${option.icon} fs-4`"></i>
                                    <span class="fw-semibold">{{ option.label }}</span>
                                </label>
                            </div>

                            <!-- Options -->
                            <div class="form-check mb-3">
                                <input
                                    v-model="form.communications_opt_in"
                                    class="form-check-input"
                                    type="checkbox"
                                    id="communications"
                                />
                                <label class="form-check-label" for="communications">
                                    I'd like to receive updates about Score Beyond's impact
                                </label>
                            </div>

                            <!-- Recurring Donation -->
                            <div class="mb-4" v-if="isAmountValid">
                                <label class="form-label fw-semibold">Donation Frequency</label>
                                <select
                                    v-model="form.frequency"
                                    class="form-select"
                                    @change="form.is_recurring = form.frequency !== null && form.frequency !== 'one-time'"
                                >
                                    <option value="one-time">One-Time</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>

                            <div v-if="(form.errors as Record<string, string>).payment" class="alert alert-danger rounded-4 mb-3">
                                {{ (form.errors as Record<string, string>).payment }}
                            </div>

                            <button
                                type="button"
                                class="btn btn-warning btn-lg rounded-pill w-100 text-uppercase fw-bold mt-4"
                                :disabled="form.processing || !isAmountValid"
                                style="background: linear-gradient(135deg, #f89f3d 0%, #f03733 100%); border: none;"
                                @click="submit"
                            >
                                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                <i v-else class="bi bi-heart-fill me-2"></i>
                                {{ form.processing ? 'Processing...' : `Donate ${formattedAmount(form.amount)}` }}
                            </button>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="page-section mb-4">
                        <h3 class="h5 fw-bold mb-3">Your Donation Summary</h3>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Currency</span>
                            <span class="fw-semibold">{{ form.currency }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Amount</span>
                            <span class="fw-bold fs-5 text-primary">
                                {{ formattedAmount(form.amount || 0) }}
                            </span>
                        </div>
                        <div v-if="form.frequency && form.frequency !== 'one-time'" class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Frequency</span>
                            <span class="fw-semibold">{{ form.frequency === 'monthly' ? 'Monthly' : form.frequency === 'quarterly' ? 'Quarterly' : form.frequency === 'yearly' ? 'Yearly' : 'One-Time' }}</span>
                        </div>
                        <hr />
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold fs-5 text-primary">
                                {{ formattedAmount(form.amount || 0) }}
                            </span>
                        </div>
                    </div>

                    <div class="page-section">
                        <h4 class="h6 fw-bold mb-3">How Your Donation Helps</h4>
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-primary me-2"></i>
                                Support sports programs for girls
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-primary me-2"></i>
                                Provide educational opportunities
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-primary me-2"></i>
                                Improve health and well-being
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-primary me-2"></i>
                                Create livelihood opportunities
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </LandingLayout>
</template>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #a01d62 0%, #f03733 100%);
}
</style>

