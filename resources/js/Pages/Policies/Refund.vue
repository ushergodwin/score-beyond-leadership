<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import LandingLayout from '@/Layouts/LandingLayout.vue';
</script>

<template>
    <Head title="Refund Policy - Score Beyond Leadership" />
    <LandingLayout>
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <h1 class="display-4 fw-bold mb-4">Refund Policy</h1>
                    <p class="text-muted mb-4">Last updated: {{ new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) }}</p>

                    <div class="policy-content">
                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">1. Overview</h2>
                            <p class="text-muted mb-3">
                                At Score Beyond Leadership Organization, we strive to ensure your complete satisfaction with your purchases in accordance with consumer protection laws in Uganda. This Refund Policy outlines the terms and conditions under which refunds, returns, and exchanges are processed.
                            </p>
                            <p class="text-muted mb-3">
                                This policy is designed to comply with:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>Consumer protection laws and regulations in Uganda</li>
                                <li>The Electronic Transactions Act, 2011</li>
                                <li>The Contracts Act, 2010</li>
                                <li>Fair trading practices as established by Ugandan law</li>
                            </ul>
                            <p class="text-muted">
                                Please read this policy carefully before making a purchase. By making a purchase, you agree to the terms outlined in this policy.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">2. Product Returns and Refunds</h2>
                            
                            <h3 class="h5 fw-semibold mb-2 mt-4">2.1 Return Eligibility</h3>
                            <p class="text-muted mb-3">Products may be returned for a refund or exchange within 14 days of delivery, provided that:</p>
                            <ul class="text-muted mb-3">
                                <li>The product is unused, unwashed, and in its original condition</li>
                                <li>The product is in its original packaging with all tags attached</li>
                                <li>You have proof of purchase (order confirmation or receipt)</li>
                                <li>The product is not a personalized or custom-made item</li>
                                <li>The product is not a digital product or downloadable content</li>
                            </ul>

                            <h3 class="h5 fw-semibold mb-2 mt-4">2.2 Return Process</h3>
                            <p class="text-muted mb-3">To initiate a return:</p>
                            <ol class="text-muted mb-3">
                                <li>Contact us at info@scorebeyondleadership.org or +256 772 319503 within 14 days of delivery</li>
                                <li>Provide your order number and reason for return</li>
                                <li>We will provide you with return instructions and a return authorization number</li>
                                <li>Package the item securely in its original packaging</li>
                                <li>Ship the item back to us using a trackable shipping method</li>
                            </ol>

                            <h3 class="h5 fw-semibold mb-2 mt-4">2.3 Refund Processing</h3>
                            <p class="text-muted mb-3">
                                Once we receive and inspect your returned item, we will process your refund within 5-10 business days. Refunds will be issued to the original payment method used for the purchase.
                            </p>
                            <p class="text-muted mb-3">
                                Please note:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>Shipping costs are non-refundable unless the item was defective or we made an error</li>
                                <li>Return shipping costs are the responsibility of the customer unless the item was defective</li>
                                <li>Refunds may take 5-10 business days to appear in your account, depending on your payment provider</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">3. Defective or Damaged Products</h2>
                            <p class="text-muted mb-3">
                                If you receive a defective or damaged product:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>Contact us immediately within 48 hours of delivery</li>
                                <li>Provide photos of the defect or damage</li>
                                <li>We will arrange for a replacement or full refund, including return shipping costs</li>
                                <li>We may request that you return the defective item for inspection</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">4. Wrong Item Received</h2>
                            <p class="text-muted mb-3">
                                If you receive the wrong item:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>Contact us immediately</li>
                                <li>We will arrange for the correct item to be sent at no additional cost</li>
                                <li>We will cover the return shipping costs for the incorrect item</li>
                                <li>You may keep the incorrect item or return it as instructed</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">5. Non-Refundable Items</h2>
                            <p class="text-muted mb-3">The following items are not eligible for return or refund:</p>
                            <ul class="text-muted mb-3">
                                <li>Personalized or custom-made products</li>
                                <li>Digital products or downloadable content</li>
                                <li>Items that have been used, washed, or damaged by the customer</li>
                                <li>Items returned after the 14-day return period</li>
                                <li>Items without proof of purchase</li>
                                <li>Gift cards (unless required by law)</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">6. Donations</h2>
                            <p class="text-muted mb-3">
                                Donations made to Score Beyond Leadership Organization are generally non-refundable. However, we understand that mistakes can happen. If you made a donation in error:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>Contact us within 7 days of the donation</li>
                                <li>Provide your donation receipt or confirmation number</li>
                                <li>We will review your request and may issue a refund at our discretion</li>
                                <li>Refunds for donations are processed on a case-by-case basis</li>
                            </ul>
                            <p class="text-muted">
                                Please note that refunded donations may affect your tax deduction eligibility. Consult with a tax professional for advice.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">7. Exchanges</h2>
                            <p class="text-muted mb-3">
                                We offer exchanges for products of equal or greater value. If you wish to exchange an item:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>Follow the return process outlined above</li>
                                <li>Indicate that you wish to exchange the item</li>
                                <li>Specify the item you would like in exchange</li>
                                <li>If the exchange item is of greater value, you will be charged the difference</li>
                                <li>If the exchange item is of lesser value, we will refund the difference</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">8. Cancellations</h2>
                            
                            <h3 class="h5 fw-semibold mb-2 mt-4">8.1 Order Cancellations</h3>
                            <p class="text-muted mb-3">
                                You may cancel an order before it has been shipped. To cancel:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>Contact us immediately with your order number</li>
                                <li>If the order has not been processed, we will cancel it and issue a full refund</li>
                                <li>If the order has already been shipped, it will be treated as a return</li>
                            </ul>

                            <h3 class="h5 fw-semibold mb-2 mt-4">8.2 Recurring Donation Cancellations</h3>
                            <p class="text-muted mb-3">
                                You can cancel a recurring donation at any time:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>Log into your account and navigate to your donations</li>
                                <li>Select the recurring donation you wish to cancel</li>
                                <li>Click "Cancel Recurring Donation"</li>
                                <li>Or contact us directly to cancel</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">9. Processing Times</h2>
                            <p class="text-muted mb-3">
                                Refund processing times:
                            </p>
                            <ul class="text-muted mb-3">
                                <li><strong>Inspection:</strong> 2-3 business days after we receive your return</li>
                                <li><strong>Refund Processing:</strong> 5-10 business days after approval</li>
                                <li><strong>Payment Provider:</strong> Additional 3-5 business days for the refund to appear in your account</li>
                            </ul>
                            <p class="text-muted">
                                Total time from return to refund: approximately 10-18 business days.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">10. Shipping Costs</h2>
                            <p class="text-muted mb-3">
                                Shipping cost refunds:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>Original shipping costs are non-refundable unless the item was defective or we made an error</li>
                                <li>Return shipping costs are the customer's responsibility unless the item was defective or incorrect</li>
                                <li>We will provide a prepaid return label for defective or incorrect items</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">11. International Returns</h2>
                            <p class="text-muted mb-3">
                                For international orders:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>Return shipping costs are the customer's responsibility</li>
                                <li>Customs duties and taxes are non-refundable</li>
                                <li>Please mark the return package as "Returned Goods" to avoid additional customs charges</li>
                                <li>We recommend using a trackable shipping method</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">12. Store Credit</h2>
                            <p class="text-muted mb-3">
                                In some cases, we may offer store credit instead of a refund:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>Store credit will be issued to your account</li>
                                <li>Store credit does not expire</li>
                                <li>Store credit can be used for future purchases</li>
                                <li>Store credit is non-transferable and non-refundable</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">13. Disputes and Chargebacks</h2>
                            <p class="text-muted mb-3">
                                If you are not satisfied with our refund resolution, please contact us before initiating a chargeback with your payment provider. We are committed to resolving all issues fairly and promptly in accordance with consumer protection laws in Uganda.
                            </p>
                            <p class="text-muted mb-3">
                                In the event of a dispute that cannot be resolved directly with us, you may:
                            </p>
                            <ul class="text-muted mb-3">
                                <li>Seek resolution through the appropriate consumer protection authorities in Uganda</li>
                                <li>Pursue legal remedies available under Ugandan law</li>
                                <li>Contact the Uganda Consumer Protection Association for assistance</li>
                            </ul>
                            <p class="text-muted">
                                Initiating a chargeback without first contacting us may result in your account being suspended or closed. We encourage open communication to resolve any issues amicably.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">14. Contact Us for Refunds</h2>
                            <p class="text-muted mb-3">
                                To request a refund, return, or exchange, please contact us:
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
                                Please include your order number, reason for return, and any relevant photos when contacting us.
                            </p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h3 fw-bold mb-3">15. Changes to This Policy</h2>
                            <p class="text-muted">
                                We reserve the right to modify this Refund Policy at any time. Changes will be effective immediately upon posting on this page. We encourage you to review this policy periodically to stay informed about our refund practices.
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

.policy-content ul,
.policy-content ol {
    padding-left: 1.5rem;
}

.policy-content li {
    margin-bottom: 0.5rem;
}
</style>

