<template>
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        <Sidebar />

        <!-- Main content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <!-- Navbar -->
            <Navbar />

            <!-- Page content -->
            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        <!-- Page header -->
                        <div class="mb-6" v-if="$slots.header">
                            <slot name="header" />
                        </div>

                        <!-- Page content -->
                        <slot />
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import Sidebar from '@/Components/Sidebar.vue';
import Navbar from '@/Components/Navbar.vue';
</script>

