<template>
    <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
        <div class="flex-shrink-0 flex items-center px-4">
            <Link :href="route('dashboard')" class="flex items-center space-x-2">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 6v1m0-1v1m6-10h2m-10 0h2m-1 14h1"/>
                </svg>
                <span class="text-xl font-bold text-gray-900">LoanManager</span>
            </Link>
        </div>
        <nav class="mt-5 px-2 space-y-1">
            <!-- Mobile navigation items - same as desktop sidebar but simplified -->
            <MobileNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                <template #icon>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </template>
                Dashboard
            </MobileNavLink>

            <MobileNavLink :href="route('loans.index')" :active="route().current('loans.*')">
                <template #icon>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 6v1m0-1v1m6-10h2m-10 0h2m-1 14h1"/>
                    </svg>
                </template>
                Loans
            </MobileNavLink>

            <!-- Add other mobile navigation links as needed -->
        </nav>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import MobileNavLink from '@/Components/MobileNavLink.vue';
</script>