<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import LandingLayout from '@/Layouts/LandingLayout.vue';

interface GalleryImage {
    id: number;
    path: string;
    caption?: string;
    alt_text: string;
}

interface GallerySection {
    id: number;
    title: string;
    description?: string;
    images: GalleryImage[];
}

const props = defineProps<{
    sections: GallerySection[];
    title: string;
}>();

const selectedImage = ref<{ section: GallerySection; image: GalleryImage; index: number } | null>(null);
const currentImageIndex = ref(0);

const openLightbox = (section: GallerySection, image: GalleryImage, index: number) => {
    selectedImage.value = { section, image, index };
    currentImageIndex.value = index;
};

const closeLightbox = () => {
    selectedImage.value = null;
    currentImageIndex.value = 0;
};

const nextImage = () => {
    if (!selectedImage.value) return;
    const section = selectedImage.value.section;
    if (currentImageIndex.value < section.images.length - 1) {
        currentImageIndex.value++;
        selectedImage.value.image = section.images[currentImageIndex.value];
        selectedImage.value.index = currentImageIndex.value;
    }
};

const prevImage = () => {
    if (!selectedImage.value) return;
    const section = selectedImage.value.section;
    if (currentImageIndex.value > 0) {
        currentImageIndex.value--;
        selectedImage.value.image = section.images[currentImageIndex.value];
        selectedImage.value.index = currentImageIndex.value;
    }
};

// Keyboard navigation
const handleKeydown = (event: KeyboardEvent) => {
    if (!selectedImage.value) return;
    if (event.key === 'Escape') {
        closeLightbox();
    } else if (event.key === 'ArrowRight') {
        nextImage();
    } else if (event.key === 'ArrowLeft') {
        prevImage();
    }
};
</script>

<template>

    <Head title="Gallery - Score Beyond Leadership" />
    <LandingLayout>
        <template #hero>
            <section class="py-5 section-bg-gradient-dark border-bottom text-white">
                <div class="container">
                    <h1 class="display-4 fw-bold mb-3">Our {{ props.title }}</h1>
                    <p class="lead mb-0 text-white-50">
                        Explore our projects and initiatives through photos that capture the impact we're making in
                        communities.
                    </p>
                </div>
            </section>
        </template>

        <div class="container pt-4 pb-5">
            <div v-if="sections.length === 0" class="text-center py-5">
                <i class="bi bi-images display-1 text-muted mb-3"></i>
                <p class="lead text-muted">No gallery sections available at the moment.</p>
            </div>

            <div v-else class="row g-5">
                <div v-for="section in sections" :key="section.id" class="col-12">
                    <div class="page-section">
                        <div class="mb-4">
                            <h2 class="h3 fw-bold mb-2 text-primary">{{ section.title }}</h2>
                            <div v-if="section.description" class="text-muted mb-0" v-html="section.description"></div>
                        </div>

                        <div class="gallery-grid">
                            <div v-for="(image, index) in section.images" :key="image.id" class="gallery-item"
                                @click="openLightbox(section, image, index)">
                                <div class="gallery-item-inner">
                                    <img :src="image.path" :alt="image.alt_text" class="gallery-image" loading="lazy" />
                                    <div class="gallery-overlay">
                                        <i class="bi bi-zoom-in display-4 text-white"></i>
                                        <p v-if="image.caption" class="text-white mb-0 mt-2 small">{{ image.caption }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lightbox Modal -->
        <div v-if="selectedImage" class="lightbox-modal" @click="closeLightbox" @keydown="handleKeydown" tabindex="0">
            <div class="lightbox-content" @click.stop>
                <button class="lightbox-close" @click="closeLightbox" aria-label="Close lightbox">
                    <i class="bi bi-x-lg"></i>
                </button>
                <button v-if="currentImageIndex > 0" class="lightbox-nav lightbox-prev" @click.stop="prevImage"
                    aria-label="Previous image">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button
                    v-if="selectedImage.section.images.length > 1 && currentImageIndex < selectedImage.section.images.length - 1"
                    class="lightbox-nav lightbox-next" @click.stop="nextImage" aria-label="Next image">
                    <i class="bi bi-chevron-right"></i>
                </button>
                <img :src="selectedImage.image.path" :alt="selectedImage.image.alt_text" class="lightbox-image" />
                <div class="lightbox-info">
                    <h3 class="h5 fw-bold text-white mb-1">{{ selectedImage.section.title }}</h3>
                    <p v-if="selectedImage.image.caption" class="text-white-50 mb-0">{{ selectedImage.image.caption }}
                    </p>
                    <p class="text-white-50 small mb-0 mt-2">
                        {{ currentImageIndex + 1 }} / {{ selectedImage.section.images.length }}
                    </p>
                </div>
            </div>
        </div>
    </LandingLayout>
</template>

<style scoped>
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.25rem;
}

@media (min-width: 576px) {
    .gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1.5rem;
    }
}

@media (min-width: 768px) {
    .gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.75rem;
    }
}

@media (min-width: 992px) {
    .gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2rem;
    }
}

@media (min-width: 1200px) {
    .gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    }
}

.gallery-item {
    cursor: pointer;
    border-radius: var(--sb-radius-sm);
    overflow: hidden;
    position: relative;
    aspect-ratio: 4 / 3;
    background: #f8f9fa;
}

.gallery-item-inner {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.gallery-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(160, 29, 98, 0.8);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    padding: 1rem;
    text-align: center;
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

.gallery-item:hover .gallery-image {
    transform: scale(1.1);
}

/* Lightbox Styles */
.lightbox-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.95);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    cursor: pointer;
}

.lightbox-content {
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: default;
}

.lightbox-image {
    max-width: 100%;
    max-height: 80vh;
    object-fit: contain;
    border-radius: var(--sb-radius-sm);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
}

.lightbox-close {
    position: absolute;
    top: -3rem;
    right: 0;
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    cursor: pointer;
    transition: background 0.3s ease;
    z-index: 10000;
}

.lightbox-close:hover {
    background: rgba(255, 255, 255, 0.2);
}

.lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    cursor: pointer;
    transition: background 0.3s ease;
    z-index: 10000;
}

.lightbox-nav:hover {
    background: rgba(255, 255, 255, 0.2);
}

.lightbox-prev {
    left: -4rem;
}

.lightbox-next {
    right: -4rem;
}

.lightbox-info {
    margin-top: 1.5rem;
    text-align: center;
    max-width: 600px;
}

@media (max-width: 768px) {
    .gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
    }

    .lightbox-nav {
        width: 2.5rem;
        height: 2.5rem;
        font-size: 1.5rem;
    }

    .lightbox-prev {
        left: 0.5rem;
    }

    .lightbox-next {
        right: 0.5rem;
    }

    .lightbox-close {
        top: 0.5rem;
        right: 0.5rem;
    }
}
</style>
