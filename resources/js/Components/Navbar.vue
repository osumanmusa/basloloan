<template>
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left side - Search and mobile menu -->
                <div class="flex items-center">
                    <!-- Mobile menu button -->
                    <button 
                        @click="sidebarOpen = true"
                        class="md:hidden -ml-2 mr-2 h-8 w-8 rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <!-- Search bar -->
                    <div class="hidden md:block">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input
                                type="search"
                                placeholder="Search..."
                                class="block w-64 pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            >
                        </div>
                    </div>
                </div>

                <!-- Right side - Notifications and user menu -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div class="relative">
                        <button 
                            @click="notificationsOpen = !notificationsOpen"
                            class="p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 relative"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10 5a2 2 0 114 0v7M4 13a8 8 0 1116 0"/>
                            </svg>
                            <span 
                                v-if="unreadNotifications > 0"
                                class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center"
                            >
                                {{ unreadNotifications }}
                            </span>
                        </button>

                        <!-- Notifications dropdown -->
                        <div 
                            v-show="notificationsOpen"
                            class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                        >
                            <div class="py-2">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                                </div>
                                
                                <div class="max-h-96 overflow-y-auto">
                                    <div 
                                        v-for="notification in notifications"
                                        :key="notification.id"
                                        class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0"
                                    >
                                        <div class="flex items-start space-x-3">
                                            <div 
                                                :class="notificationIconClass(notification.type)"
                                                class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                                            >
                                                <component 
                                                    :is="notificationIcon(notification.type)" 
                                                    class="w-4 h-4 text-white" 
                                                />
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ notification.title }}
                                                </p>
                                                <p class="text-sm text-gray-500 truncate">
                                                    {{ notification.message }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ notification.time }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="notifications.length === 0" class="px-4 py-8 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10 5a2 2 0 114 0v7M4 13a8 8 0 1116 0"/>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">No new notifications</p>
                                    </div>
                                </div>

                                <div class="px-4 py-2 border-t border-gray-100">
                                    <a 
                                        href="#" 
                                        class="block text-center text-sm font-medium text-blue-600 hover:text-blue-900"
                                    >
                                        View all notifications
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick actions dropdown -->
                    <div class="relative">
                        <Dropdown>
                            <template #trigger>
                                <button class="p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                </button>
                            </template>
                            <template #content>
                                <DropdownLink :href="route('loans.create')">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        <span>New Loan</span>
                                    </div>
                                </DropdownLink>
                                <DropdownLink :href="route('customers.create')">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                        </svg>
                                        <span>New Customer</span>
                                    </div>
                                </DropdownLink>
                                <DropdownLink :href="route('payments.create')">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 6v1m0-1v1m6-10h2m-10 0h2m-1 14h1"/>
                                        </svg>
                                        <span>Record Payment</span>
                                    </div>
                                </DropdownLink>
                                <div class="border-t border-gray-100"></div>
                                <DropdownLink :href="route('calculator.index')">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        <span>Loan Calculator</span>
                                    </div>
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>

                    <!-- User menu -->
                    <div class="relative">
                        <Dropdown>
                            <template #trigger>
                                <button class="flex items-center space-x-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-semibold">
                                                {{ userInitial }}
                                            </span>
                                        </div>
                                        <div class="hidden md:block text-left">
                                            <p class="text-sm font-medium text-gray-700">{{ $page.props.auth.user?.name }}</p>
                                            <p class="text-xs text-gray-500 capitalize">
                                                {{ userRole }}
                                            </p>
                                        </div>
                                    </div>
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </template>
                            <template #content>
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ $page.props.auth.user?.name }}</p>
                                    <p class="text-sm text-gray-500">{{ $page.props.auth.user?.email }}</p>
                                </div>
                                
                                <DropdownLink :href="route('profile.edit')">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>Profile Settings</span>
                                    </div>
                                </DropdownLink>
                                
                                <DropdownLink :href="route('dashboard')">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                        <span>Dashboard</span>
                                    </div>
                                </DropdownLink>

                                <div class="border-t border-gray-100"></div>
                                
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    <div class="flex items-center space-x-2 text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <span>Log Out</span>
                                    </div>
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile sidebar overlay -->
    <div 
        v-show="sidebarOpen"
        class="fixed inset-0 flex z-40 md:hidden"
    >
        <div 
            v-show="sidebarOpen"
            class="fixed inset-0"
            @click="sidebarOpen = false"
        >
            <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
        </div>
        
        <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
            <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button 
                    @click="sidebarOpen = false"
                    class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                >
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Mobile sidebar content -->
            <SidebarMobile />
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import SidebarMobile from '@/Components/SidebarMobile.vue';
import { 
    BellIcon,
    ExclamationTriangleIcon,
    CheckCircleIcon,
    InformationCircleIcon,
    CurrencyDollarIcon
} from '@heroicons/vue/24/outline';

const sidebarOpen = ref(false);
const notificationsOpen = ref(false);
const page = usePage();

// Sample notifications data
const notifications = ref([
    {
        id: 1,
        type: 'payment',
        title: 'Payment Received',
        message: 'John Doe made a payment of $500 for loan #1234',
        time: '5 minutes ago',
        read: false
    },
    {
        id: 2,
        type: 'warning',
        title: 'Overdue Payment',
        message: 'Loan #5678 has an overdue payment of $300',
        time: '2 hours ago',
        read: false
    },
    {
        id: 3,
        type: 'info',
        title: 'New Loan Application',
        message: 'Jane Smith applied for a $10,000 loan',
        time: '1 day ago',
        read: true
    },
    {
        id: 4,
        type: 'success',
        title: 'Loan Approved',
        message: 'Loan #9012 has been approved and ready for disbursement',
        time: '2 days ago',
        read: true
    }
]);

const unreadNotifications = computed(() => {
    return notifications.value.filter(n => !n.read).length;
});

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

const notificationIcon = (type) => {
    const icons = {
        payment: CurrencyDollarIcon,
        warning: ExclamationTriangleIcon,
        success: CheckCircleIcon,
        info: InformationCircleIcon
    };
    return icons[type] || BellIcon;
};

const notificationIconClass = (type) => {
    const classes = {
        payment: 'bg-green-500',
        warning: 'bg-yellow-500',
        success: 'bg-blue-500',
        info: 'bg-gray-500'
    };
    return classes[type] || 'bg-gray-500';
};
</script>