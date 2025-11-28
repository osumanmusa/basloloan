<template>
    <Link
        :href="href"
        class="group flex items-center px-4 py-3 text-sm font-medium rounded-md transition-colors duration-200"
        :class="active ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
    >
        <slot name="icon" />
        <span class="ml-3">
            <slot />
        </span>
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: String,
    active: Boolean
});
</script>