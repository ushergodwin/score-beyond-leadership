<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import LandingLayout from '@/Layouts/LandingLayout.vue';
</script>

<template>
    <Head title="Terms of Service - Score Beyond Leadership" />
    <LandingLayout>
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <h1 class="display-4 fw-bold mb-4">Terms of Service</h1>
                    <p class="text-muted mb-4">Last updated: {{ new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) }}</p>

                    <div class="policy-content">
                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">1. Acceptance of Terms</h2>
                            <p class="text-muted mb-3">
                                By accessing and using the Score Beyond Leadership Organization website ("Website"), you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.
                            </p>
                            <p class="text-muted mb-3">
                                These Terms of Service ("Terms") govern your access to and use of our website, services, products, and content provided by Score Beyond Leadership Organization ("we," "us," or "our"). These Terms are subject to and governed by the laws of Uganda, including but not limited to:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>The Electronic Transactions Act, 2011</li>
                                <li>The Computer Misuse Act, 2011</li>
                                <li>The Data Protection and Privacy Act, 2019</li>
                                <li>The Consumer Protection Act (where applicable)</li>
                                <li>The Contracts Act, 2010</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">2. Use License</h2>
                            <p class="text-muted mb-3">
                                Permission is granted to temporarily access the materials on Score Beyond Leadership Organization's website for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>Modify or copy the materials</li>
                                <li>Use the materials for any commercial purpose or for any public display (commercial or non-commercial)</li>
                                <li>Attempt to decompile or reverse engineer any software contained on the website</li>
                                <li>Remove any copyright or other proprietary notations from the materials</li>
                                <li>Transfer the materials to another person or "mirror" the materials on any other server</li>
                            </ul>
                            <p class="text-muted">
                                This license shall automatically terminate if you violate any of these restrictions and may be terminated by Score Beyond Leadership Organization at any time.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">3. Account Registration</h2>
                            <p class="text-muted mb-3">
                                To access certain features of our website, you may be required to register for an account. When you register, you agree to:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>Provide accurate, current, and complete information</li>
                                <li>Maintain and promptly update your account information</li>
                                <li>Maintain the security of your password and identification</li>
                                <li>Accept all responsibility for activities that occur under your account</li>
                                <li>Notify us immediately of any unauthorized use of your account</li>
                            </ul>
                            <p class="text-muted">
                                We reserve the right to suspend or terminate your account if you provide false, inaccurate, or incomplete information, or if we determine that you have violated these Terms.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">4. Products and Services</h2>
                            
                            <h3 class="h5 fw-semibold mb-2 mt-4">4.1 Product Information</h3>
                            <p class="text-muted mb-3">
                                In compliance with consumer protection laws in Uganda, we strive to provide accurate product descriptions, images, and pricing. However, we do not warrant that product descriptions or other content on this site is accurate, complete, reliable, current, or error-free. If a product offered by us is not as described, your rights and remedies are as set forth in our Refund Policy and in accordance with applicable Ugandan consumer protection laws.
                            </p>

                            <h3 class="h5 fw-semibold mb-2 mt-4">4.2 Pricing</h3>
                            <p class="text-muted mb-3">
                                All prices are displayed in the currency indicated and are subject to change without notice. We reserve the right to modify prices at any time. Prices do not include shipping, handling, or applicable taxes unless otherwise stated.
                            </p>

                            <h3 class="h5 fw-semibold mb-2 mt-4">4.3 Availability</h3>
                            <p class="text-muted mb-3">
                                We reserve the right to limit the quantity of items purchased per person, per household, or per order. We also reserve the right to refuse or cancel any order at our sole discretion.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">5. Donations</h2>
                            <p class="text-muted mb-3">
                                When you make a donation through our website:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>Donations are voluntary and non-refundable unless otherwise stated</li>
                                <li>You will receive a receipt for tax purposes</li>
                                <li>Donations may be one-time or recurring as selected</li>
                                <li>You can cancel recurring donations at any time through your account</li>
                                <li>We reserve the right to refuse any donation at our discretion</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">6. Payment Terms</h2>
                            <p class="text-muted mb-3">
                                Payment for products and services must be made through our authorized payment processors in compliance with the Electronic Transactions Act, 2011 and the Financial Institutions Act. By providing payment information, you represent and warrant that:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>You are authorized to use the payment method provided</li>
                                <li>The payment information is accurate and complete</li>
                                <li>You will be charged the amount indicated at checkout</li>
                                <li>All transactions are subject to verification and may be declined at our discretion</li>
                            </ul>
                            <p class="text-muted">
                                We use secure third-party payment processors compliant with PCI DSS standards and do not store your complete payment card information on our servers, in accordance with the Data Protection and Privacy Act, 2019.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">7. Shipping and Delivery</h2>
                            <p class="text-muted mb-3">
                                We will make reasonable efforts to deliver products within the estimated timeframe. However, delivery times are estimates and not guaranteed. We are not responsible for delays caused by:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>Shipping carriers</li>
                                <li>Customs clearance</li>
                                <li>Weather conditions</li>
                                <li>Acts of God or other circumstances beyond our control</li>
                            </ul>
                            <p class="text-muted">
                                Risk of loss and title for products pass to you upon delivery to the carrier.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">8. Returns and Refunds</h2>
                            <p class="text-muted mb-3">
                                Please refer to our <Link :href="route('policies.refund')" class="text-primary">Refund Policy</Link> for detailed information about returns, refunds, and exchanges.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">9. Volunteer Applications</h2>
                            <p class="text-muted mb-3">
                                When you submit a volunteer application:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>You provide accurate and complete information</li>
                                <li>We reserve the right to accept or reject applications at our discretion</li>
                                <li>Background checks may be required</li>
                                <li>Volunteer positions are subject to availability and program needs</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">10. Intellectual Property</h2>
                            <p class="text-muted mb-3">
                                All content on this website, including but not limited to text, graphics, logos, images, audio clips, digital downloads, and software, is the property of Score Beyond Leadership Organization or its content suppliers and is protected by international copyright laws.
                            </p>
                            <p class="text-muted">
                                You may not reproduce, distribute, modify, create derivative works of, publicly display, publicly perform, republish, download, store, or transmit any of the material on our website without prior written consent.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">11. User Conduct</h2>
                            <p class="text-muted mb-3">You agree not to:</p>
                            <ul class="text-muted mb-3">
                                <li>Use the website for any unlawful purpose</li>
                                <li>Violate any applicable laws or regulations</li>
                                <li>Infringe upon the rights of others</li>
                                <li>Transmit any harmful, offensive, or inappropriate content</li>
                                <li>Interfere with or disrupt the website or servers</li>
                                <li>Attempt to gain unauthorized access to any portion of the website</li>
                                <li>Use automated systems to access the website without permission</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">12. Disclaimer of Warranties</h2>
                            <p class="text-muted mb-3">
                                The materials on Score Beyond Leadership Organization's website are provided on an 'as is' basis. Score Beyond Leadership Organization makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">13. Limitation of Liability</h2>
                            <p class="text-muted mb-3">
                                In no event shall Score Beyond Leadership Organization or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on Score Beyond Leadership Organization's website, even if Score Beyond Leadership Organization or a Score Beyond Leadership Organization authorized representative has been notified orally or in writing of the possibility of such damage.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">14. Indemnification</h2>
                            <p class="text-muted">
                                You agree to indemnify, defend, and hold harmless Score Beyond Leadership Organization, its officers, directors, employees, agents, and affiliates from and against any claims, liabilities, damages, losses, and expenses, including reasonable attorneys' fees, arising out of or in any way connected with your access to or use of the website, your violation of these Terms, or your violation of any rights of another.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">15. Links to Third-Party Websites</h2>
                            <p class="text-muted">
                                Our website may contain links to third-party websites that are not owned or controlled by Score Beyond Leadership Organization. We have no control over, and assume no responsibility for, the content, privacy policies, or practices of any third-party websites. You acknowledge and agree that Score Beyond Leadership Organization shall not be responsible or liable for any damage or loss caused by or in connection with the use of any such content, goods, or services available on or through any such websites.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">16. Modifications to Terms</h2>
                            <p class="text-muted">
                                Score Beyond Leadership Organization may revise these Terms of Service at any time without notice. By using this website, you are agreeing to be bound by the then current version of these Terms of Service. We encourage you to review these Terms periodically.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">17. Governing Law and Dispute Resolution</h2>
                            <p class="text-muted mb-3">
                                These Terms shall be governed by and construed in accordance with the laws of the Republic of Uganda, without regard to its conflict of law provisions. Any disputes arising under or in connection with these Terms shall be subject to the exclusive jurisdiction of the courts of Uganda.
                            </p>
                            <p class="text-muted mb-3">
                                Applicable Ugandan laws include but are not limited to:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>The Constitution of the Republic of Uganda, 1995</li>
                                <li>The Electronic Transactions Act, 2011</li>
                                <li>The Computer Misuse Act, 2011</li>
                                <li>The Data Protection and Privacy Act, 2019</li>
                                <li>The Contracts Act, 2010</li>
                                <li>The Consumer Protection Act (where applicable)</li>
                                <li>The Companies Act, 2012</li>
                            </ul>
                            <p class="text-muted">
                                In the event of any dispute, the parties agree to first attempt to resolve the matter through good faith negotiation. If negotiation fails, disputes shall be resolved through the appropriate courts of Uganda in accordance with the Civil Procedure Act.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">18. Severability</h2>
                            <p class="text-muted">
                                If any provision of these Terms is found to be unenforceable or invalid, that provision shall be limited or eliminated to the minimum extent necessary so that these Terms shall otherwise remain in full force and effect.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">19. Contact Information</h2>
                            <p class="text-muted mb-3">
                                If you have any questions about these Terms of Service, please contact us:
                            </p>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-2"><strong>Score Beyond Leadership Organization</strong></p>
                                    <p class="mb-2"><i class="bi bi-envelope me-2"></i> info@scorebeyondleadership.org</p>
                                    <p class="mb-2"><i class="bi bi-phone me-2"></i> +256 772 319503</p>
                                    <p class="mb-0"><i class="bi bi-geo-alt me-2"></i> Kampala, Uganda</p>
                                </div>
                            </div>
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

