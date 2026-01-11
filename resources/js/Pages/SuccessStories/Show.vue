<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import LandingLayout from "@/Layouts/LandingLayout.vue";

type Story = {
    id: number;
    title: string;
    description: string;
    quote?: string;
    image_url: string;
    image_alt: string;
};

type RelatedStory = {
    id: number;
    title: string;
    description: string;
    quote?: string;
    image_url: string;
    image_alt: string;
};

const props = defineProps<{
    story: Story;
    relatedStories?: RelatedStory[];
}>();
</script>

<template>
    <Head :title="props.story.title + ' - Success Story'" />
    <LandingLayout>
        <template #hero>
            <section class="py-5 section-bg-gradient-dark border-bottom text-white">
                <div class="container">
                    <h1 class="display-4 fw-bold mb-3">{{ props.story.title }}</h1>
                    <p v-if="props.story.quote" class="lead mb-0 text-white-50">
                        <em>{{ props.story.quote }}</em>
                    </p>
                </div>
            </section>
        </template>

        <section class="container pt-4 pb-5">
            <div class="row align-items-start">
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <img
                        :src="props.story.image_url"
                        :alt="props.story.image_alt"
                        class="img-fluid rounded-3 shadow-sm w-100"
                        style="object-fit: cover; height: 100%; min-height: 400px;"
                    />
                </div>
                <div class="col-lg-7">
                    <article class="story-content">
                        <div class="story-text">
                            <p
                                v-for="(paragraph, index) in props.story.description.split('\n\n')"
                                :key="index"
                                class="text-muted mb-4"
                                style="line-height: 1.8; font-size: 1.1rem;"
                            >
                                {{ paragraph }}
                            </p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section v-if="props.relatedStories && props.relatedStories.length > 0" class="container pb-5">
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="h3 fw-bold mb-0">More Success Stories</h2>
                </div>
            </div>
            <div class="row g-4">
                <div
                    v-for="relatedStory in props.relatedStories"
                    :key="relatedStory.id"
                    class="col-md-6 col-lg-4"
                >
                    <div class="card h-100 shadow-sm border-0">
                        <img
                            :src="relatedStory.image_url"
                            :alt="relatedStory.image_alt"
                            class="card-img-top project-image"
                        />
                        <div class="card-body d-flex flex-column">
                            <h4 class="card-title h6 fw-bold mb-2">{{ relatedStory.title }}</h4>
                            <p v-if="relatedStory.quote" class="card-text text-muted small mb-2">
                                <em>{{ relatedStory.quote }}</em>
                            </p>
                            <p class="card-text text-muted mb-3 flex-grow-1">{{ relatedStory.description }}</p>
                            <Link
                                :href="route('success-stories.show', relatedStory.id)"
                                class="btn btn-sm btn-outline-primary rounded-pill mt-auto"
                            >
                                Read Full Story
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </LandingLayout>
</template>

<style scoped>
.story-content {
    height: 100%;
}

.story-text p {
    text-align: justify;
}

.project-image {
    aspect-ratio: 4 / 3;
    object-fit: cover;
    transition: transform 0.3s ease;
    width: 100%;
}

.card:hover .project-image {
    transform: scale(1.1);
}

@media (max-width: 991.98px) {
    .story-content img {
        min-height: 300px !important;
    }
}
</style>

