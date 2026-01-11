<script setup lang="ts">
import { computed, ref, onUnmounted } from 'vue';
import { showSuccess, showError } from '@/composables/useNotifications';

interface Props {
    url: string;
    title: string;
    description?: string;
    image?: string;
}

const props = defineProps<Props>();

const showDropdown = ref(false);

const shareData = computed(() => ({
    url: props.url,
    title: props.title,
    description: props.description || props.title,
    image: props.image || '',
}));

const shareToFacebook = () => {
    const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareData.value.url)}`;
    window.open(shareUrl, '_blank', 'width=600,height=400');
    showDropdown.value = false;
};

const shareToTwitter = () => {
    const text = `${shareData.value.title} - ${shareData.value.description}`;
    const shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(shareData.value.url)}&text=${encodeURIComponent(text)}`;
    window.open(shareUrl, '_blank', 'width=600,height=400');
    showDropdown.value = false;
};

const shareToWhatsApp = () => {
    const text = `${shareData.value.title} - ${shareData.value.url}`;
    const shareUrl = `https://wa.me/?text=${encodeURIComponent(text)}`;
    window.open(shareUrl, '_blank');
    showDropdown.value = false;
};

const shareViaEmail = () => {
    const subject = encodeURIComponent(`Check out: ${shareData.value.title}`);
    const body = encodeURIComponent(`${shareData.value.description}\n\n${shareData.value.url}`);
    window.location.href = `mailto:?subject=${subject}&body=${body}`;
    showDropdown.value = false;
};

const copyLink = async () => {
    try {
        await navigator.clipboard.writeText(shareData.value.url);
        showSuccess('Link copied to clipboard!');
        showDropdown.value = false;
    } catch (err) {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = shareData.value.url;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showSuccess('Link copied to clipboard!');
        } catch (e) {
            showError('Failed to copy link');
        }
        document.body.removeChild(textArea);
        showDropdown.value = false;
    }
};

// Close dropdown when clicking outside
const handleClickOutside = (event: MouseEvent) => {
    const target = event.target as HTMLElement;
    if (!target.closest('.share-button-container')) {
        showDropdown.value = false;
    }
};

if (typeof window !== 'undefined') {
    document.addEventListener('click', handleClickOutside);
}

onUnmounted(() => {
    if (typeof window !== 'undefined') {
        document.removeEventListener('click', handleClickOutside);
    }
});
</script>

<template>
    <div class="share-button-container position-relative">
        <button
            class="btn btn-outline-secondary rounded-pill px-4"
            type="button"
            @click.stop="showDropdown = !showDropdown"
        >
            <i class="bi bi-share me-2"></i>
            Share
        </button>

        <div
            v-if="showDropdown"
            class="dropdown-menu show position-absolute end-0 mt-2 shadow-lg"
            style="min-width: 200px; z-index: 1050;"
            @click.stop
        >
            <button class="dropdown-item" type="button" @click="shareToFacebook">
                <i class="bi bi-facebook me-2 text-primary"></i>
                Facebook
            </button>
            <button class="dropdown-item" type="button" @click="shareToTwitter">
                <i class="bi bi-twitter-x me-2"></i>
                Twitter/X
            </button>
            <button class="dropdown-item" type="button" @click="shareToWhatsApp">
                <i class="bi bi-whatsapp me-2 text-success"></i>
                WhatsApp
            </button>
            <button class="dropdown-item" type="button" @click="shareViaEmail">
                <i class="bi bi-envelope me-2 text-info"></i>
                Email
            </button>
            <hr class="dropdown-divider" />
            <button class="dropdown-item" type="button" @click="copyLink">
                <i class="bi bi-link-45deg me-2"></i>
                Copy Link
            </button>
        </div>
    </div>
</template>

<style scoped>
.dropdown-menu {
    border: 1px solid rgba(0, 0, 0, 0.15);
    border-radius: 0.5rem;
}

.dropdown-item {
    padding: 0.5rem 1rem;
    display: flex;
    align-items: center;
    transition: background-color 0.2s;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.dropdown-item i {
    width: 20px;
    text-align: center;
}
</style>

