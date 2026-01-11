<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import CountrySelect from '@/Components/CountrySelect.vue';
import type { CartSummary, ShippingMethodOption } from '@/types/shop';
import type { PageProps } from '@/types';
import { showSuccess, showError, showWarning } from '@/composables/useNotifications';

const props = defineProps<{
    cart: CartSummary;
    shippingMethods: ShippingMethodOption[];
    prefill: {
        first_name?: string | null;
        last_name?: string | null;
        email?: string | null;
        phone?: string | null;
        country?: string | null;
        city?: string | null;
        state?: string | null;
        postal_code?: string | null;
        line_one?: string | null;
        line_two?: string | null;
    };
}>();

const cartSummary = props.cart;
const shippingMethodOptions = props.shippingMethods;

const form = useForm({
    first_name: props.prefill.first_name ?? '',
    last_name: props.prefill.last_name ?? '',
    email: props.prefill.email ?? '',
    phone: props.prefill.phone ?? '',
    country: props.prefill.country ?? 'UG',
    city: props.prefill.city ?? '',
    state: props.prefill.state ?? '',
    postal_code: props.prefill.postal_code ?? '',
    line_one: props.prefill.line_one ?? '',
    line_two: props.prefill.line_two ?? '',
    shipping_method_id: shippingMethodOptions[0]?.id ?? null,
    payment_method: 'pesapal_card',
    customer_note: '',
    subscribe: true,
    create_account: false,
    password: '',
    password_confirmation: '',
});

const showAccountFields = computed(() => form.create_account);

const submit = () => {
    form.post(route('checkout.store'), {
        onSuccess: () => {
            showSuccess('Redirecting to payment...');
        },
        onError: () => {
            showError('Please check the form for errors and try again.');
        },
    });
};

const selectedShipping = computed(() =>
    shippingMethodOptions.find((method) => method.id === form.shipping_method_id) ?? null,
);

const subtotalFormatter = new Intl.NumberFormat('en-UG', {
    style: 'currency',
    currency: 'UGX',
    maximumFractionDigits: 0,
});

const usdFormatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
});

const computedTotals = computed(() => {
    const shippingRate = selectedShipping.value?.rate ?? { ugx: 0, usd: 0 };

    return {
        shipping: shippingRate,
        subtotal: cartSummary.totals.subtotal,
        grand_total: {
            ugx: cartSummary.totals.subtotal.ugx + shippingRate.ugx,
            usd: parseFloat((cartSummary.totals.subtotal.usd + shippingRate.usd).toFixed(2)),
        },
    };
});

const page = usePage<PageProps<{ errors: Record<string, string | undefined> }>>();
const paymentError = computed(() => page.props.errors?.payment);
const paymentMethodError = computed(() => page.props.errors?.payment_method);
// Mobile Money is now available for all shipping methods including international
// No restriction needed

const paymentOptions = [
    {
        value: 'pesapal_card',
        title: 'Visa / Mastercard',
        description: 'Supports international and local debit or credit cards.',
        icon: 'bi-credit-card',
    },
    {
        value: 'pesapal_mobile',
        title: 'Mobile Money (MTN / Airtel)',
        description: 'Pay securely with Mobile Money.',
        icon: 'bi-phone',
    },
];

// Mobile Money is now available for all shipping methods
const isPaymentOptionDisabled = (optionValue: string): boolean => false;
</script>

<template>
    <Head title="Checkout" />
    <StorefrontLayout>
        <template #hero>
            <section class="py-4 bg-white border-bottom">
                <div class="container">
                    <h1 class="h3 fw-bold text-primary mb-0">Checkout</h1>
                </div>
            </section>
        </template>

        <div class="container mb-5 mt-4">
            <div class="row g-5">
                <div class="col-lg-7">
                    <div class="form-card mb-4">
                        <h2 class="h4 fw-bold mb-3">Contact details</h2>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label text-uppercase small fw-semibold text-muted">First name</label>
                                    <input
                                        v-model="form.first_name"
                                        type="text"
                                        class="form-control rounded-pill"
                                        :class="{ 'is-invalid': form.errors.first_name }"
                                    />
                                    <div class="invalid-feedback">{{ form.errors.first_name }}</div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-uppercase small fw-semibold text-muted">Last name</label>
                                    <input
                                        v-model="form.last_name"
                                        type="text"
                                        class="form-control rounded-pill"
                                        :class="{ 'is-invalid': form.errors.last_name }"
                                    />
                                    <div class="invalid-feedback">{{ form.errors.last_name }}</div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-uppercase small fw-semibold text-muted">Email</label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="form-control rounded-pill"
                                        :class="{ 'is-invalid': form.errors.email }"
                                    />
                                    <div class="invalid-feedback">{{ form.errors.email }}</div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-uppercase small fw-semibold text-muted">Phone</label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        class="form-control rounded-pill"
                                        :class="{ 'is-invalid': form.errors.phone }"
                                    />
                                    <div class="invalid-feedback">{{ form.errors.phone }}</div>
                                </div>
                            </div>
                    </div>

                    <div class="form-card mb-4">
                        <h2 class="h4 fw-bold mb-3">Shipping details</h2>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label text-uppercase small fw-semibold text-muted">Country</label>
                                    <CountrySelect
                                        v-model="form.country"
                                        placeholder="Select country"
                                        :error="form.errors.country"
                                    />
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-uppercase small fw-semibold text-muted">City</label>
                                    <input
                                        v-model="form.city"
                                        type="text"
                                        class="form-control rounded-pill"
                                        :class="{ 'is-invalid': form.errors.city }"
                                    />
                                    <div class="invalid-feedback">{{ form.errors.city }}</div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-uppercase small fw-semibold text-muted">State/Region</label>
                                    <input
                                        v-model="form.state"
                                        type="text"
                                        class="form-control rounded-pill"
                                        :class="{ 'is-invalid': form.errors.state }"
                                    />
                                    <div class="invalid-feedback">{{ form.errors.state }}</div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-uppercase small fw-semibold text-muted">Postal code</label>
                                    <input
                                        v-model="form.postal_code"
                                        type="text"
                                        class="form-control rounded-pill"
                                        :class="{ 'is-invalid': form.errors.postal_code }"
                                    />
                                    <div class="invalid-feedback">{{ form.errors.postal_code }}</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-uppercase small fw-semibold text-muted">Address line 1</label>
                                    <input
                                        v-model="form.line_one"
                                        type="text"
                                        class="form-control rounded-pill"
                                        :class="{ 'is-invalid': form.errors.line_one }"
                                    />
                                    <div class="invalid-feedback">{{ form.errors.line_one }}</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-uppercase small fw-semibold text-muted">Address line 2</label>
                                    <input
                                        v-model="form.line_two"
                                        type="text"
                                        class="form-control rounded-pill"
                                        :class="{ 'is-invalid': form.errors.line_two }"
                                    />
                                    <div class="invalid-feedback">{{ form.errors.line_two }}</div>
                                </div>
                            </div>
                    </div>

                    <div class="form-card mb-4">
                        <h2 class="h4 fw-bold mb-3">Shipping method</h2>
                            <div v-if="shippingMethodOptions.length > 0" class="d-flex flex-column gap-3">
                                <label
                                    v-for="method in shippingMethodOptions"
                                    :key="method.id"
                                    class="border rounded-4 p-3 d-flex align-items-center gap-3 cursor-pointer"
                                    :class="{
                                        'border-primary shadow-sm': form.shipping_method_id === method.id,
                                        'border-light': form.shipping_method_id !== method.id,
                                    }"
                                >
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        :value="method.id"
                                        v-model.number="form.shipping_method_id"
                                    />
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold text-primary">{{ method.name }}</div>
                                        <div class="text-muted small">
                                            {{ method.carrier }} &middot;
                                            <span v-if="method.eta">{{ method.eta }}</span>
                                            <span v-else>Flexible delivery</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-primary">
                                            {{ subtotalFormatter.format(method.rate.ugx) }}
                                        </div>
                                        <small class="text-muted">{{ usdFormatter.format(method.rate.usd) }}</small>
                                    </div>
                                </label>
                                <div class="text-danger small" v-if="form.errors.shipping_method_id">
                                    {{ form.errors.shipping_method_id }}
                                </div>
                            </div>
                            <div v-else class="alert alert-warning rounded-4 mb-0" role="alert">
                                Shipping options are temporarily unavailable. Please contact Score Beyond Leadership Organization at +256 772 319503 or info@scorebeyondleadership.org to complete your order.
                            </div>
                    </div>

                    <div class="form-card mb-4">
                        <h2 class="h4 fw-bold mb-3">Payment method</h2>
                            <div class="d-flex flex-column gap-3">
                                <label
                                    v-for="option in paymentOptions"
                                    :key="option.value"
                                    class="border rounded-4 p-3 d-flex align-items-center gap-3 cursor-pointer"
                                    :class="{
                                        'border-primary shadow-sm': form.payment_method === option.value,
                                        'border-light': form.payment_method !== option.value,
                                        'opacity-50': isPaymentOptionDisabled(option.value),
                                    }"
                                >
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        :value="option.value"
                                        v-model="form.payment_method"
                                        :disabled="isPaymentOptionDisabled(option.value)"
                                    />
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold text-primary d-flex align-items-center gap-2">
                                            <i class="bi" :class="option.icon"></i>
                                            {{ option.title }}
                                        </div>
                                        <div class="text-muted small">{{ option.description }}</div>
                                    </div>
                                </label>
                                <div class="text-danger small" v-if="paymentMethodError">
                                    {{ paymentMethodError }}
                                </div>
                            </div>
                    </div>

                    <div class="form-card">
                        <h2 class="h4 fw-bold mb-3">Notes & updates</h2>
                        <div class="mb-3">
                                <label class="form-label text-uppercase small fw-semibold text-muted">Order note</label>
                                <textarea
                                    v-model="form.customer_note"
                                    rows="3"
                                    class="form-control rounded-4"
                                    :class="{ 'is-invalid': form.errors.customer_note }"
                                    placeholder="Share fit, pick-up, or dedication notes"
                                ></textarea>
                                <div class="invalid-feedback">{{ form.errors.customer_note }}</div>
                            </div>
                            <div class="form-check mb-3">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="subscribeNews"
                                    v-model="form.subscribe"
                                />
                                <label class="form-check-label" for="subscribeNews">
                                    Keep me updated on programs, volunteer calls, and store drops.
                                </label>
                            </div>

                            <!-- Account Creation Section -->
                            <div v-if="!$page.props.auth?.user" class="border-top pt-4">
                                <div class="form-check mb-3">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="createAccount"
                                        v-model="form.create_account"
                                    />
                                    <label class="form-check-label fw-semibold" for="createAccount">
                                        Create an account with this information?
                                    </label>
                                </div>

                                <div v-if="showAccountFields" class="ms-4">
                                    <div class="mb-3">
                                        <label class="form-label text-uppercase small fw-semibold text-muted">
                                            Password
                                        </label>
                                        <input
                                            v-model="form.password"
                                            type="password"
                                            class="form-control rounded-pill"
                                            :class="{ 'is-invalid': form.errors.password }"
                                            placeholder="Enter password (min. 8 characters)"
                                        />
                                        <div class="invalid-feedback">{{ form.errors.password }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-uppercase small fw-semibold text-muted">
                                            Confirm Password
                                        </label>
                                        <input
                                            v-model="form.password_confirmation"
                                            type="password"
                                            class="form-control rounded-pill"
                                            :class="{ 'is-invalid': form.errors.password_confirmation }"
                                            placeholder="Confirm your password"
                                        />
                                        <div class="invalid-feedback">{{ form.errors.password_confirmation }}</div>
                                    </div>
                                    <div class="alert alert-info rounded-4 mb-0" role="alert">
                                        <small>
                                            <i class="bi bi-info-circle me-1"></i>
                                            Your account will be created after successful payment. You'll be automatically signed in.
                                        </small>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="page-section">
                        <h2 class="h4 fw-bold mb-4">Order summary</h2>
                        <div class="d-flex flex-column gap-3 mb-4">
                            <article v-for="item in cartSummary.items" :key="item.id" class="d-flex gap-3">
                                    <div class="ratio ratio-1x1 rounded-4 overflow-hidden" style="width: 80px;">
                                        <img
                                            :src="item.image ?? 'https://placehold.co/400x400?text=Score+Beyond'"
                                            :alt="item.name"
                                            class="w-100 h-100 object-fit-cover"
                                        />
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">{{ item.name }}</div>
                                        <div class="text-muted small">{{ item.variant_name }}</div>
                                        <div class="text-muted small">Qty {{ item.quantity }}</div>
                                    </div>
                            </article>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-semibold">{{ subtotalFormatter.format(cartSummary.totals.subtotal.ugx) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Shipping</span>
                            <span class="fw-semibold">
                                {{ subtotalFormatter.format(computedTotals.shipping.ugx ?? 0) }}
                            </span>
                        </div>
                        <hr />
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <span class="text-uppercase fw-semibold">Total</span>
                                <p class="text-muted small mb-0">Paid in UGX with USD display reference.</p>
                            </div>
                            <div class="text-end">
                                <div class="display-6 fw-bold text-primary mb-0">
                                    {{ subtotalFormatter.format(computedTotals.grand_total.ugx) }}
                                </div>
                                <small class="text-muted">
                                    {{ usdFormatter.format(computedTotals.grand_total.usd) }}
                                </small>
                            </div>
                        </div>

                        <div class="alert alert-info rounded-4" role="alert">
                            <strong>Impact reminder:</strong> Every purchase supports Score Beyond Leadership Organization's mission to empower women and girls through sports, education, health and well-being, and livelihood opportunities.
                        </div>

                        <div class="d-flex justify-content-center align-items-center mb-4">
                            <button
                                class="btn btn-primary btn-lg rounded-pill text-uppercase fw-semibold mt-3"
                                type="button"
                                :disabled="form.processing"
                                @click="submit"
                            >
                                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                <i class="bi bi-shield-fill-check me-2"></i> Pay Now
                            </button>
                        </div>
                        <div class="text-danger small mt-2" v-if="paymentError">
                            {{ paymentError }}
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <Link class="text-decoration-none text-muted" :href="route('cart.index')">
                            &larr; Return to cart
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </StorefrontLayout>
</template>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}
</style>

