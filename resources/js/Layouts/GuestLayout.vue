<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import scoreBeyondLogo from '../../images/scorebeyond-white.webp';
</script>

<template>
    <div class="min-vh-100 d-flex flex-column align-items-center justify-content-center section-bg-gradient py-5">
        <div class="mb-4">
            <Link href="/" class="text-decoration-none">
                <img
                    :src="scoreBeyondLogo"
                    alt="Score Beyond Leadership"
                    class="storefront-logo"
                    style="max-height: 80px; width: auto;"
                />
            </Link>
        </div>

        <div class="form-card w-100" style="max-width: 28rem;">
            <slot />
        </div>
    </div>
</template>
