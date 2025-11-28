<template>
    <Link :class="className">
        <slot />
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    active: Boolean,
    className: String
})
</script>