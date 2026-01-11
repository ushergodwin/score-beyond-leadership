<script setup lang="ts">
const props = defineProps<{
    value?: string;
    for?: string;
}>();
</script>

<template>
    <label :for="props.for" class="form-label fw-semibold">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
