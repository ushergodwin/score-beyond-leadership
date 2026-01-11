<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import LandingLayout from '@/Layouts/LandingLayout.vue';
import { showSuccess } from '@/composables/useNotifications';

const form = useForm({
    email: '',
    reason: '',
    confirmation: false,
});

const submitDeletionRequest = () => {
    form.post(route('policies.data-deletion'), {
        preserveScroll: true,
        onSuccess: () => {
            showSuccess('Your data deletion request has been received. We will process it within 30 days.');
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Privacy Policy - Score Beyond Leadership" />
    <LandingLayout>
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <h1 class="display-4 fw-bold mb-4">Privacy Policy</h1>
                    <p class="text-muted mb-4">Last updated: {{ new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) }}</p>

                    <div class="policy-content">
                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">1. Introduction</h2>
                            <p class="text-muted mb-3">
                                Score Beyond Leadership Organization ("we," "our," or "us") is committed to protecting your privacy in accordance with the Data Protection and Privacy Act, 2019 of Uganda. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website, make donations, purchase products, or use our services.
                            </p>
                            <p class="text-muted mb-3">
                                This policy is designed to comply with:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>The Data Protection and Privacy Act, 2019 (Uganda)</li>
                                <li>The Electronic Transactions Act, 2011 (Uganda)</li>
                                <li>The Computer Misuse Act, 2011 (Uganda)</li>
                                <li>International best practices for data protection</li>
                            </ul>
                            <p class="text-muted">
                                By using our website and services, you consent to the data practices described in this policy. If you do not agree with the practices described, please do not use our services.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">2. Information We Collect</h2>
                            
                            <h3 class="h5 fw-semibold mb-2 mt-4">2.1 Personal Information</h3>
                            <p class="text-muted mb-3">We may collect the following types of personal information:</p>
                            <ul class="text-muted mb-3">
                                <li><strong>Contact Information:</strong> Name, email address, phone number, postal address</li>
                                <li><strong>Account Information:</strong> Username, password, profile information</li>
                                <li><strong>Payment Information:</strong> Credit card details, billing address, payment history (processed securely through third-party payment processors)</li>
                                <li><strong>Transaction Information:</strong> Purchase history, donation records, order details</li>
                                <li><strong>Volunteer Information:</strong> Application details, background information, emergency contacts</li>
                                <li><strong>Communication Data:</strong> Correspondence with us, newsletter subscriptions, feedback</li>
                            </ul>

                            <h3 class="h5 fw-semibold mb-2 mt-4">2.2 Automatically Collected Information</h3>
                            <p class="text-muted mb-3">When you visit our website, we automatically collect:</p>
                            <ul class="text-muted mb-3">
                                <li>IP address and device information</li>
                                <li>Browser type and version</li>
                                <li>Pages visited and time spent on pages</li>
                                <li>Referring website addresses</li>
                                <li>Cookies and similar tracking technologies</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">3. How We Use Your Information</h2>
                            <p class="text-muted mb-3">We use the collected information for the following purposes:</p>
                            <ul class="text-muted mb-3">
                                <li>To process and fulfill your orders and donations</li>
                                <li>To manage your account and provide customer support</li>
                                <li>To send you transactional emails, receipts, and order confirmations</li>
                                <li>To send newsletters and updates (with your consent)</li>
                                <li>To process volunteer applications and manage volunteer programs</li>
                                <li>To improve our website, products, and services</li>
                                <li>To comply with legal obligations and prevent fraud</li>
                                <li>To analyze website usage and trends</li>
                                <li>To personalize your experience</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">4. Information Sharing and Disclosure</h2>
                            <p class="text-muted mb-3">We do not sell your personal information. We may share your information in the following circumstances:</p>
                            <ul class="text-muted mb-3">
                                <li><strong>Service Providers:</strong> With trusted third-party service providers who assist in operating our website, processing payments, shipping orders, or managing communications</li>
                                <li><strong>Legal Requirements:</strong> When required by law, court order, or government regulation</li>
                                <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of assets</li>
                                <li><strong>Protection of Rights:</strong> To protect our rights, property, or safety, or that of our users</li>
                                <li><strong>With Your Consent:</strong> When you explicitly authorize us to share your information</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">5. Data Security</h2>
                            <p class="text-muted mb-3">
                                In compliance with Section 19 of the Data Protection and Privacy Act, 2019 and the Computer Misuse Act, 2011, we implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. These measures include:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>SSL encryption for data transmission as required by the Electronic Transactions Act, 2011</li>
                                <li>Secure payment processing through certified payment gateways compliant with PCI DSS standards</li>
                                <li>Regular security assessments and updates</li>
                                <li>Access controls and authentication procedures</li>
                                <li>Secure data storage and backup systems</li>
                                <li>Employee training on data protection and privacy</li>
                                <li>Incident response procedures for data breaches</li>
                            </ul>
                            <p class="text-muted">
                                In the event of a data breach that may affect your personal information, we will notify you and the Personal Data Protection Office in accordance with Section 20 of the Data Protection and Privacy Act, 2019. However, no method of transmission over the Internet or electronic storage is 100% secure. While we strive to protect your information, we cannot guarantee absolute security.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">6. Cookies and Tracking Technologies</h2>
                            <p class="text-muted mb-3">
                                We use cookies and similar tracking technologies to enhance your browsing experience, analyze website traffic, and personalize content. You can control cookies through your browser settings, but disabling cookies may limit some website functionality.
                            </p>
                            <p class="text-muted">
                                Types of cookies we use:
                            </p>
                            <ul class="text-muted mb-3">
                                <li><strong>Essential Cookies:</strong> Required for website functionality</li>
                                <li><strong>Analytics Cookies:</strong> Help us understand how visitors use our website</li>
                                <li><strong>Functional Cookies:</strong> Remember your preferences and settings</li>
                                <li><strong>Marketing Cookies:</strong> Used to deliver relevant advertisements (with consent)</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">7. Your Rights and Choices</h2>
                            <p class="text-muted mb-3">
                                Under the Data Protection and Privacy Act, 2019 of Uganda, you have the following rights regarding your personal information:
                            </p>
                            <ul class="text-muted mb-3">
                                <li><strong>Right to Access:</strong> Request access to your personal data (Section 24 of the Data Protection and Privacy Act, 2019)</li>
                                <li><strong>Right to Correction:</strong> Request correction of inaccurate or incomplete data (Section 25 of the Data Protection and Privacy Act, 2019)</li>
                                <li><strong>Right to Deletion:</strong> Request deletion of your personal data, subject to legal requirements and our legitimate business interests (Section 26 of the Data Protection and Privacy Act, 2019)</li>
                                <li><strong>Right to Object:</strong> Object to processing of your personal data for direct marketing or other purposes (Section 27 of the Data Protection and Privacy Act, 2019)</li>
                                <li><strong>Right to Data Portability:</strong> Request transfer of your data to another service provider in a structured, commonly used format</li>
                                <li><strong>Right to Withdraw Consent:</strong> Withdraw consent for data processing where processing is based on consent</li>
                                <li><strong>Right to Opt-out:</strong> Unsubscribe from marketing communications at any time</li>
                                <li><strong>Right to Complain:</strong> Lodge a complaint with the Personal Data Protection Office if you believe your rights have been violated</li>
                            </ul>
                            <p class="text-muted">
                                To exercise these rights, please contact us using the information provided in the "Contact Us" section below. We will respond to your request within 30 days as required by the Data Protection and Privacy Act, 2019.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">8. Data Retention</h2>
                            <p class="text-muted mb-3">
                                In accordance with Section 18 of the Data Protection and Privacy Act, 2019, we retain your personal information only for as long as necessary to fulfill the purposes outlined in this Privacy Policy, unless a longer retention period is required or permitted by law. Retention periods vary based on:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>Legal and regulatory requirements under Ugandan law (e.g., tax records must be kept for 7 years as per the Tax Procedures Code Act)</li>
                                <li>The nature of the information</li>
                                <li>The purpose for which it was collected</li>
                                <li>Ongoing business needs and legitimate interests</li>
                                <li>Statutory limitation periods for legal claims</li>
                            </ul>
                            <p class="text-muted">
                                Once the retention period expires, we will securely delete or anonymize your personal information in accordance with Section 18(2) of the Data Protection and Privacy Act, 2019.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">9. Children's Privacy</h2>
                            <p class="text-muted">
                                Our services are not directed to individuals under the age of 18. We do not knowingly collect personal information from children. If we become aware that we have collected information from a child without parental consent, we will take steps to delete that information promptly.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">10. International Data Transfers</h2>
                            <p class="text-muted">
                                Your information may be transferred to and processed in countries other than your country of residence. These countries may have data protection laws that differ from those in your country. We take appropriate safeguards to ensure your information is protected in accordance with this Privacy Policy.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">11. Changes to This Privacy Policy</h2>
                            <p class="text-muted">
                                We may update this Privacy Policy from time to time to reflect changes in our practices or legal requirements. We will notify you of any material changes by posting the new Privacy Policy on this page and updating the "Last updated" date. We encourage you to review this policy periodically.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">12. Request Data Deletion</h2>
                            <p class="text-muted mb-4">
                                You have the right to request deletion of your personal data. Please note that we may need to retain certain information for legal, accounting, or operational purposes. To request data deletion, please complete the form below:
                            </p>
                            
                            <div class="card border shadow-sm">
                                <div class="card-body p-4">
                                    <form @submit.prevent="submitDeletionRequest">
                                        <div class="mb-3">
                                            <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                            <input
                                                type="email"
                                                id="email"
                                                v-model="form.email"
                                                class="form-control"
                                                :class="{ 'is-invalid': form.errors.email }"
                                                required
                                                placeholder="your.email@example.com"
                                            />
                                            <div v-if="form.errors.email" class="invalid-feedback">
                                                {{ form.errors.email }}
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="reason" class="form-label fw-semibold">Reason for Deletion (Optional)</label>
                                            <textarea
                                                id="reason"
                                                v-model="form.reason"
                                                class="form-control"
                                                :class="{ 'is-invalid': form.errors.reason }"
                                                rows="4"
                                                placeholder="Please provide any additional information about your deletion request..."
                                                maxlength="1000"
                                            ></textarea>
                                            <div v-if="form.errors.reason" class="invalid-feedback">
                                                {{ form.errors.reason }}
                                            </div>
                                            <small class="text-muted">{{ form.reason?.length || 0 }}/1000 characters</small>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input
                                                    type="checkbox"
                                                    id="confirmation"
                                                    v-model="form.confirmation"
                                                    class="form-check-input"
                                                    :class="{ 'is-invalid': form.errors.confirmation }"
                                                    required
                                                />
                                                <label class="form-check-label" for="confirmation">
                                                    I confirm that I want to delete my account and personal data. I understand that this action may be irreversible and that some information may be retained for legal or regulatory purposes. <span class="text-danger">*</span>
                                                </label>
                                                <div v-if="form.errors.confirmation" class="invalid-feedback d-block">
                                                    {{ form.errors.confirmation }}
                                                </div>
                                            </div>
                                        </div>

                                        <button
                                            type="submit"
                                            class="btn btn-danger"
                                            :disabled="form.processing"
                                        >
                                            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                            {{ form.processing ? 'Submitting...' : 'Submit Deletion Request' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">13. Contact Us</h2>
                            <p class="text-muted mb-3">
                                If you have questions, concerns, or requests regarding this Privacy Policy or our data practices, please contact us:
                            </p>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-2"><strong>Score Beyond Leadership Organization</strong></p>
                                    <p class="mb-2"><i class="bi bi-envelope me-2"></i> info@scorebeyondleadership.org</p>
                                    <p class="mb-2"><i class="bi bi-phone me-2"></i> +256 772 319503</p>
                                    <p class="mb-0"><i class="bi bi-geo-alt me-2"></i> Kampala, Uganda</p>
                                </div>
                            </div>
                            <p class="text-muted mt-3">
                                You also have the right to lodge a complaint with the Personal Data Protection Office if you believe your data protection rights have been violated. For more information, visit the Personal Data Protection Office website or contact them directly.
                            </p>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </LandingLayout>
</template>

<style scoped>
.policy-content {
    line-height: 1.8;
}

.policy-content h2 {
    color: var(--bs-primary);
    margin-top: 2rem;
}

.policy-content h3 {
    color: var(--bs-dark);
    margin-top: 1.5rem;
}

.policy-content ul {
    padding-left: 1.5rem;
}

.policy-content li {
    margin-bottom: 0.5rem;
}
</style>

