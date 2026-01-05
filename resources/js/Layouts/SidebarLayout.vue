<template>
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        <div class="hidden md:flex md:w-64 md:flex-col">
            <div class="flex flex-col flex-grow pt-5 overflow-y-auto bg-white border-r border-gray-200">
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0 px-4">
                    <Link :href="route('dashboard')" class="flex items-center space-x-2">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 6v1m0-1v1m6-10h2m-10 0h2m-1 14h1"/>
                        </svg>
                        <span class="text-xl font-bold text-gray-900">LoanManager</span>
                    </Link>
                </div>

                <!-- Navigation -->
                <div class="mt-8 flex-grow flex flex-col">
                    <nav class="flex-1 px-4 space-y-2">
                        <!-- Dashboard -->
                        <NavLink 
                            :href="route('dashboard')" 
                            :active="route().current('dashboard')" 
                            class="group flex items-center px-4 py-3 text-sm font-medium rounded-md transition-colors duration-200"
                            :class="route().current('dashboard') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Dashboard
                        </NavLink>

                        <!-- Loans -->
                        <NavLink 
                            :href="route('loans.index')" 
                            :active="route().current('loans.*')" 
                            class="group flex items-center px-4 py-3 text-sm font-medium rounded-md transition-colors duration-200"
                            :class="route().current('loans.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 6v1m0-1v1m6-10h2m-10 0h2m-1 14h1"/>
                            </svg>
                            Loans
                        </NavLink>

                        <!-- Customers -->
                        <NavLink 
                            :href="route('customers.index')" 
                            :active="route().current('customers.*')" 
                            class="group flex items-center px-4 py-3 text-sm font-medium rounded-md transition-colors duration-200"
                            :class="route().current('customers.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Customers
                        </NavLink>

                        <!-- Payments -->
                        <NavLink 
                            :href="route('payment-schedules.index')" 
                            :active="route().current('payment-schedules.*')" 
                            class="group flex items-center px-4 py-3 text-sm font-medium rounded-md transition-colors duration-200"
                            :class="route().current('payment-schedules.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 6v1m0-1v1m6-10h2m-10 0h2m-1 14h1"/>
                            </svg>
                            Payment Schedules
                        </NavLink>
                        <NavLink 
                            :href="route('payments.index')" 
                            :active="route().current('payments.*')" 
                            class="group flex items-center px-4 py-3 text-sm font-medium rounded-md transition-colors duration-200"
                            :class="route().current('payments.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 6v1m0-1v1m6-10h2m-10 0h2m-1 14h1"/>
                            </svg>
                            Payments
                        </NavLink>

                        <!-- Reports -->
                        <NavLink 
                            :href="route('reports.index')" 
                            :active="route().current('reports.*')" 
                            class="group flex items-center px-4 py-3 text-sm font-medium rounded-md transition-colors duration-200"
                            :class="route().current('reports.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Reports
                        </NavLink>

                        <!-- Calculator -->
                        <NavLink 
                            :href="route('calculator.index')" 
                            :active="route().current('calculator.*')" 
                            class="group flex items-center px-4 py-3 text-sm font-medium rounded-md transition-colors duration-200"
                            :class="route().current('calculator.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                        >
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Calculator
                        </NavLink>

                        <!-- Admin Section -->
                        <div v-if="isAdmin" class="pt-4 mt-4 border-t border-gray-200">
                            <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Administration
                            </p>
                            
                            <NavLink 
                                :href="route('users.index')" 
                                :active="route().current('users.*')" 
                                class="group flex items-center px-4 py-3 text-sm font-medium rounded-md transition-colors duration-200"
                                :class="route().current('users.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                            >
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                                User Management
                            </NavLink>

                            <NavLink 
                                :href="route('roles.index')" 
                                :active="route().current('roles.*')" 
                                class="group flex items-center px-4 py-3 text-sm font-medium rounded-md transition-colors duration-200"
                                :class="route().current('roles.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                            >
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Role Management
                            </NavLink>
                        </div>
                    </nav>
                </div>

                <!-- User Menu -->
                <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                    <div class="flex items-center w-full">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-semibold">
                                    {{ userInitial }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-700">{{ page.props.auth.user?.name }}</p>
                            <p class="text-xs font-medium text-gray-500 capitalize">
                                {{ userRole }}
                            </p>
                        </div>
                        <Dropdown>
                            <template #trigger>
                                <button class="ml-2 flex-shrink-0 p-1 rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                                    </svg>
                                </button>
                            </template>
                            <template #content>
                                <DropdownLink :href="route('profile.edit')">Profile Settings</DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="md:hidden">
            <div class="fixed inset-0 flex z-40" v-show="mobileMenuOpen">
                <div class="fixed inset-0" @click="mobileMenuOpen = false">
                    <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
                </div>
                <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
                    <div class="absolute top-0 right-0 -mr-12 pt-2">
                        <button @click="mobileMenuOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                        <div class="flex-shrink-0 flex items-center px-4">
                            <span class="text-xl font-bold text-gray-900">LoanManager</span>
                        </div>
                        <nav class="mt-5 px-2 space-y-1">
                            <!-- Mobile navigation items would go here -->
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <!-- Top bar -->
            <div class="md:hidden pl-1 pt-1 sm:pl-3 sm:pt-3 bg-white border-b border-gray-200">
                <button @click="mobileMenuOpen = true" class="-ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

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
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const mobileMenuOpen = ref(false);
const page = usePage();

// Safe computed properties to handle undefined values
const userInitial = computed(() => {
    if (!page.props.auth.user || !page.props.auth.user.name) {
        return 'U';
    }
    return page.props.auth.user.name.charAt(0).toUpperCase();
});

const userRole = computed(() => {
    if (!page.props.auth.user || !page.props.auth.user.roles || !page.props.auth.user.roles.length) {
        return 'user';
    }
    return page.props.auth.user.roles[0];
});

const isAdmin = computed(() => {
    if (!page.props.auth.user || !page.props.auth.user.roles) {
        return false;
    }
    return page.props.auth.user.roles.includes('admin');
});
</script>