<template>
    <button
        type="submit"
        class="btn btn-primary"
        :class="$attrs.class"
        v-bind="$attrs"
    >
        <slot />
    </button>
</template>
