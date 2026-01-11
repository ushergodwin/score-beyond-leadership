<script setup lang="ts">
interface Props {
    size?: 'sm' | 'md' | 'lg';
    color?: 'primary' | 'white' | 'secondary';
}

const props = withDefaults(defineProps<Props>(), {
    size: 'md',
    color: 'primary',
});

const sizeClass = {
    sm: 'spinner-border-sm',
    md: '',
    lg: 'spinner-border-lg',
};

const colorClass = {
    primary: 'text-primary',
    white: 'text-white',
    secondary: 'text-secondary',
};
</script>

<template>
    <div class="d-flex justify-content-center align-items-center" :class="colorClass[color]">
        <div class="spinner-border" :class="sizeClass[size]" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</template>

