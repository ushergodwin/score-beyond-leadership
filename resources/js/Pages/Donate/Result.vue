<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import LandingLayout from '@/Layouts/LandingLayout.vue';

const props = defineProps<{
    donation: {
        donation_number: string;
        amount: number;
        currency: string;
        payment_status: string;
        status_label: string;
        status_intent: 'success' | 'danger' | 'warning';
        donor_name: string;
        donor_email: string;
        paid_at?: string;
        message?: string;
    };
}>();

const badgeClass = computed(() => {
    switch (props.donation.status_intent) {
        case 'success':
            return 'badge bg-success-subtle text-success';
        case 'danger':
            return 'badge bg-danger-subtle text-danger';
        default:
            return 'badge bg-warning-subtle text-warning';
    }
});

const formattedAmount = (amount: number, currency: string) => {
    const locale = currency === 'EUR' ? 'de-DE' : currency === 'USD' ? 'en-US' : 'en-UG';
    const options: Intl.NumberFormatOptions = {
        style: 'currency',
        currency: currency,
    };

    if (currency === 'UGX') {
        options.maximumFractionDigits = 0;
    } else {
        options.minimumFractionDigits = 2;
        options.maximumFractionDigits = 2;
    }

    return new Intl.NumberFormat(locale, options).format(amount);
};
</script>

<template>
    <Head :title="`Donation ${donation.donation_number}`" />
    <LandingLayout>
        <template #hero>
            <section class="py-5 bg-gradient-primary text-white">
                <div class="container">
                    <h1 class="display-4 fw-bold mb-3">Thank You for Your Donation!</h1>
                    <p class="lead mb-0">
                        Your generosity helps us continue our mission to empower women and girls.
                    </p>
                </div>
            </section>
        </template>

        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Success Appreciation Message -->
                    <div v-if="donation.status_intent === 'success'" class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 bg-success-subtle">
                                <div class="card-body p-4 p-lg-5">
                                    <div class="d-flex align-items-start gap-4">
                                        <div class="flex-shrink-0">
                                            <i class="bi bi-check-circle-fill text-success" style="font-size: 3.5rem;"></i>
                                        </div>
                                        <div class="flex-grow-1 text-start">
                                            <h2 class="h3 fw-bold text-success mb-3">Thank You for Your Generous Donation!</h2>
                                            <p class="lead mb-3">
                                                Your payment has been successfully processed. We are deeply grateful for your support of Score Beyond Leadership!
                                            </p>
                                            <p class="mb-0 text-muted">
                                                Your donation will directly support programs that empower women and girls through sports, education, health, and livelihood opportunities. You will receive a confirmation email shortly. Thank you for being part of our mission to transform lives and create lasting impact in our communities.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Failed Payment Message -->
                    <div v-else-if="donation.status_intent === 'danger'" class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 bg-danger-subtle">
                                <div class="card-body p-4 p-lg-5">
                                    <div class="d-flex align-items-start gap-4">
                                        <div class="flex-shrink-0">
                                            <i class="bi bi-x-circle-fill text-danger" style="font-size: 3.5rem;"></i>
                                        </div>
                                        <div class="flex-grow-1 text-start">
                                            <h2 class="h3 fw-bold text-danger mb-3">Payment Failed</h2>
                                            <p class="mb-0 text-muted">
                                                We were unable to process your donation. Please try again or contact us at +256 772 319503 or info@scorebeyondleadership.org for assistance. We appreciate your willingness to support our cause.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Payment Message -->
                    <div v-else class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 bg-warning-subtle">
                                <div class="card-body p-4 p-lg-5">
                                    <div class="d-flex align-items-start gap-4">
                                        <div class="flex-shrink-0">
                                            <i class="bi bi-clock-history text-warning" style="font-size: 3.5rem;"></i>
                                        </div>
                                        <div class="flex-grow-1 text-start">
                                            <h2 class="h3 fw-bold text-warning mb-3">Payment Pending</h2>
                                            <p class="mb-0 text-muted">
                                                Your donation is being processed. You will receive a confirmation email once the payment is completed. Thank you for your patience and your support!
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4 p-lg-5 text-center">
                            <h2 class="h4 fw-bold mb-4">{{ donation.status_label }}</h2>

                            <div class="mb-4">
                                <span :class="badgeClass" class="badge-lg px-4 py-2">
                                    {{ donation.payment_status.toUpperCase() }}
                                </span>
                            </div>

                            <div class="alert alert-light rounded-4 mb-4" role="alert">
                                <div class="row g-3 text-start">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Donation Number</small>
                                        <strong>{{ donation.donation_number }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Amount</small>
                                        <strong class="text-primary fs-5">
                                            {{ formattedAmount(donation.amount, donation.currency) }}
                                        </strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Donor</small>
                                        <strong>{{ donation.donor_name }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Email</small>
                                        <strong>{{ donation.donor_email }}</strong>
                                    </div>
                                    <div v-if="donation.paid_at" class="col-12">
                                        <small class="text-muted d-block">Date</small>
                                        <strong>{{ donation.paid_at }}</strong>
                                    </div>
                                </div>
                            </div>

                            <div v-if="donation.message" class="alert alert-info rounded-4 mb-4 text-start">
                                <strong>Your Message:</strong>
                                <p class="mb-0 mt-2">{{ donation.message }}</p>
                            </div>

                            <div v-if="donation.status_intent === 'success'" class="alert alert-success rounded-4 mb-4">
                                <h5 class="fw-bold mb-2">Your Impact</h5>
                                <p class="mb-0">
                                    Your donation will directly support programs that empower women and girls through sports, education, health, and livelihood opportunities. Thank you for being part of our mission!
                                </p>
                            </div>

                            <div v-else-if="donation.status_intent === 'danger'" class="alert alert-danger rounded-4 mb-4">
                                <h5 class="fw-bold mb-2">Payment Failed</h5>
                                <p class="mb-0">
                                    We were unable to process your donation. Please try again or contact us at +256 772 319503 or info@scorebeyondleadership.org for assistance.
                                </p>
                            </div>

                            <div v-else class="alert alert-warning rounded-4 mb-4">
                                <h5 class="fw-bold mb-2">Payment Pending</h5>
                                <p class="mb-0">
                                    Your donation is being processed. You will receive a confirmation email once the payment is completed.
                                </p>
                            </div>

                            <div class="d-flex flex-wrap gap-3 justify-content-center mt-4">
                                <Link
                                    class="btn btn-primary rounded-pill px-4 text-uppercase fw-semibold"
                                    :href="route('donate.index')"
                                >
                                    Make Another Donation
                                </Link>
                                <Link
                                    class="btn btn-outline-primary rounded-pill px-4 text-uppercase fw-semibold"
                                    :href="route('shop.index')"
                                >
                                    Visit Our Shop
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </LandingLayout>
</template>

<style scoped>
.bg-gradient-primary {
    background: linear-gradient(135deg, #a01d62 0%, #f03733 100%);
}

.badge-lg {
    font-size: 1rem;
}
</style>

