<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import axios from 'axios';

interface Country {
    name: string;
    code: string;
    flag: string;
}

const props = withDefaults(
    defineProps<{
        modelValue?: string;
        placeholder?: string;
        error?: string;
        disabled?: boolean;
    }>(),
    {
        placeholder: 'Select a country',
        disabled: false,
    }
);

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const isOpen = ref(false);
const searchTerm = ref('');
const countries = ref<Country[]>([]);
const isLoading = ref(false);
const selectRef = ref<HTMLDivElement | null>(null);
const searchInputRef = ref<HTMLInputElement | null>(null);

const selectedCountry = computed(() => {
    if (!props.modelValue) return null;
    return countries.value.find((c) => c.code === props.modelValue) ?? null;
});

const filteredCountries = computed(() => {
    if (!searchTerm.value.trim()) {
        return countries.value;
    }

    const term = searchTerm.value.toLowerCase().trim();
    return countries.value.filter(
        (country) =>
            country.name.toLowerCase().includes(term) ||
            country.code.toLowerCase().includes(term)
    );
});

const selectCountry = (country: Country) => {
    emit('update:modelValue', country.code);
    isOpen.value = false;
    searchTerm.value = '';
};

const toggleDropdown = () => {
    if (props.disabled) return;
    isOpen.value = !isOpen.value;
    if (isOpen.value && searchInputRef.value) {
        setTimeout(() => {
            searchInputRef.value?.focus();
        }, 100);
    }
};

const handleClickOutside = (event: MouseEvent) => {
    if (selectRef.value && !selectRef.value.contains(event.target as Node)) {
        isOpen.value = false;
        searchTerm.value = '';
    }
};

const handleEscape = (event: KeyboardEvent) => {
    if (event.key === 'Escape' && isOpen.value) {
        isOpen.value = false;
        searchTerm.value = '';
    }
};

watch(() => props.modelValue, () => {
    if (!props.modelValue) {
        searchTerm.value = '';
    }
});

onMounted(async () => {
    document.addEventListener('click', handleClickOutside);
    document.addEventListener('keydown', handleEscape);

    isLoading.value = true;
    try {
        const response = await axios.get<Country[]>('/api/countries');
        countries.value = response.data;
    } catch (error) {
        // Silently handle country loading errors
    } finally {
        isLoading.value = false;
    }
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    document.removeEventListener('keydown', handleEscape);
});
</script>

<template>
    <div ref="selectRef" class="country-select position-relative">
        <div
            class="form-control rounded-pill d-flex align-items-center justify-content-between cursor-pointer"
            :class="{
                'is-invalid': error,
                'opacity-50': disabled,
            }"
            @click="toggleDropdown"
        >
            <div class="d-flex align-items-center gap-2 flex-grow-1">
                <span v-if="selectedCountry" class="fs-5">{{ selectedCountry.flag }}</span>
                <span v-if="selectedCountry" class="text-truncate">{{ selectedCountry.name }}</span>
                <span v-else class="text-muted">{{ placeholder }}</span>
            </div>
            <i
                class="bi"
                :class="isOpen ? 'bi-chevron-up' : 'bi-chevron-down'"
            ></i>
        </div>

        <div
            v-if="isOpen"
            class="country-select-dropdown position-absolute w-100 bg-white border rounded-4 shadow-lg mt-1"
            style="z-index: 1050; max-height: 300px; overflow: hidden;"
        >
            <div class="p-2 border-bottom">
                <input
                    ref="searchInputRef"
                    v-model="searchTerm"
                    type="text"
                    class="form-control form-control-sm rounded-pill"
                    placeholder="Search countries..."
                    @click.stop
                />
            </div>
            <div class="overflow-auto" style="max-height: 250px;">
                <div v-if="isLoading" class="p-3 text-center text-muted">
                    <div class="spinner-border spinner-border-sm me-2"></div>
                    Loading countries...
                </div>
                <div
                    v-else-if="filteredCountries.length === 0"
                    class="p-3 text-center text-muted"
                >
                    No countries found
                </div>
                <button
                    v-else
                    v-for="country in filteredCountries"
                    :key="country.code"
                    type="button"
                    class="country-option w-100 text-start p-3 border-0 bg-transparent d-flex align-items-center gap-2"
                    :class="{
                        'bg-primary-subtle': selectedCountry?.code === country.code,
                    }"
                    @click.stop="selectCountry(country)"
                >
                    <span class="fs-5">{{ country.flag }}</span>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ country.name }}</div>
                        <small class="text-muted">{{ country.code }}</small>
                    </div>
                    <i
                        v-if="selectedCountry?.code === country.code"
                        class="bi bi-check-circle-fill text-primary"
                    ></i>
                </button>
            </div>
        </div>

        <div v-if="error" class="invalid-feedback d-block">{{ error }}</div>
    </div>
</template>

<style scoped>
.country-select {
    z-index: 1;
}

.cursor-pointer {
    cursor: pointer;
}

.country-option {
    transition: background-color 0.15s ease;
}

.country-option:hover {
    background-color: var(--bs-primary-bg-subtle) !important;
}

.country-select-dropdown {
    animation: fadeIn 0.15s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

