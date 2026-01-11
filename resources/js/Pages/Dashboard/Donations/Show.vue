<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps<{
    donation: {
        id: number;
        donation_number: string;
        amount: number;
        currency: string;
        payment_status: string;
        is_recurring: boolean;
        frequency: string | null;
        impact_tier: string | null;
        message: string | null;
        first_name: string;
        last_name: string;
        email: string;
        phone: string | null;
        country: string | null;
        organization: string | null;
        address: string | null;
        tax_receipt_requested: boolean;
        created_at: string;
        paid_at: string | null;
    };
}>();

const getPaymentStatusBadgeClass = (status: string) => {
    return {
        'badge bg-success-subtle text-success': status === 'completed',
        'badge bg-warning-subtle text-warning': status === 'pending',
        'badge bg-danger-subtle text-danger': status === 'failed',
    };
};

const formatDonationAmount = (amount: number, currency: string) => {
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

const isDownloading = ref(false);

const downloadReceipt = () => {
    isDownloading.value = true;
    // Use window.location to trigger download
    window.location.href = route('dashboard.donations.receipt', props.donation.donation_number);
    
    // Reset after a delay
    setTimeout(() => {
        isDownloading.value = false;
    }, 2000);
};
</script>

<template>
    <Head :title="`Donation ${donation.donation_number}`" />
    <DashboardLayout>
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <Link :href="route('dashboard.donations')" class="text-decoration-none text-muted mb-2 d-inline-flex align-items-center gap-2">
                        <i class="bi bi-arrow-left"></i>
                        <span>Back to Donations</span>
                    </Link>
                    <h1 class="h3 fw-bold text-primary mb-2 mt-2">Donation {{ donation.donation_number }}</h1>
                    <div class="d-flex flex-wrap gap-2">
                        <span :class="getPaymentStatusBadgeClass(donation.payment_status)">
                            {{ donation.payment_status }}
                        </span>
                        <span v-if="donation.is_recurring" class="badge bg-info-subtle text-info">
                            Recurring ({{ donation.frequency }})
                        </span>
                        <span v-else class="badge bg-secondary-subtle text-secondary">
                            One-time
                        </span>
                    </div>
                </div>
                <button
                    v-if="donation.payment_status === 'completed'"
                    class="btn btn-primary rounded-pill"
                    :disabled="isDownloading"
                    @click="downloadReceipt"
                >
                    <span v-if="isDownloading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    <i v-else class="bi bi-download me-2"></i>
                    {{ isDownloading ? 'Generating...' : 'Download Receipt' }}
                </button>
            </div>

            <div class="row g-4">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Donation Details -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 p-4">
                            <h2 class="h5 fw-bold mb-0">Donation Details</h2>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Donation Number</label>
                                        <div class="fw-semibold">{{ donation.donation_number }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Amount</label>
                                        <div class="h4 fw-bold text-danger mb-0">
                                            {{ formatDonationAmount(donation.amount, donation.currency) }}
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Payment Status</label>
                                        <div>
                                            <span :class="getPaymentStatusBadgeClass(donation.payment_status)">
                                                {{ donation.payment_status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Date Created</label>
                                        <div>{{ new Date(donation.created_at).toLocaleString() }}</div>
                                    </div>
                                    <div v-if="donation.paid_at" class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Date Paid</label>
                                        <div>{{ new Date(donation.paid_at).toLocaleString() }}</div>
                                    </div>
                                    <div v-if="donation.impact_tier" class="mb-3">
                                        <label class="form-label small fw-semibold text-muted">Impact Tier</label>
                                        <div class="badge bg-primary-subtle text-primary">{{ donation.impact_tier }}</div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="donation.message" class="mt-4 pt-4 border-top">
                                <label class="form-label small fw-semibold text-muted">Your Message</label>
                                <p class="mb-0">{{ donation.message }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Impact Information -->
                    <div v-if="donation.payment_status === 'completed'" class="card border-0 shadow-sm rounded-4 bg-success-subtle">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start gap-3">
                                <i class="bi bi-heart-fill text-success" style="font-size: 2rem;"></i>
                                <div>
                                    <h3 class="h5 fw-bold text-success mb-2">Thank You for Your Generosity!</h3>
                                    <p class="mb-0 text-muted">
                                        Your donation directly supports programs that empower women and girls through sports, education, health, and livelihood opportunities. Your contribution makes a real difference in transforming lives and creating lasting impact in our communities.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Donor Information -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 p-4">
                            <h2 class="h5 fw-bold mb-0">Donor Information</h2>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Name</label>
                                <div class="fw-semibold">{{ donation.first_name }} {{ donation.last_name }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Email</label>
                                <div>{{ donation.email }}</div>
                            </div>
                            <div v-if="donation.phone" class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Phone</label>
                                <div>{{ donation.phone }}</div>
                            </div>
                            <div v-if="donation.country" class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Country</label>
                                <div>{{ donation.country }}</div>
                            </div>
                            <div v-if="donation.organization" class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Organization</label>
                                <div>{{ donation.organization }}</div>
                            </div>
                            <div v-if="donation.address" class="mb-0">
                                <label class="form-label small fw-semibold text-muted">Address</label>
                                <div class="text-muted small">{{ donation.address }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Receipt Information -->
                    <div v-if="donation.tax_receipt_requested && donation.payment_status === 'completed'" class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 p-4">
                            <h2 class="h5 fw-bold mb-0">Tax Receipt</h2>
                        </div>
                        <div class="card-body p-4">
                            <p class="text-muted small mb-3">
                                You requested a tax receipt for this donation. You can download it using the button above.
                            </p>
                            <button class="btn btn-outline-primary rounded-pill w-100" @click="downloadReceipt">
                                <i class="bi bi-download me-2"></i>Download Receipt
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

