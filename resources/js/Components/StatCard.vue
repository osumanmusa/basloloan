<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div :class="iconContainerClass" class="p-3 rounded-lg">
                <component :is="iconComponent" class="w-6 h-6" />
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">{{ title }}</p>
                <p class="text-2xl font-semibold text-gray-900">{{ value }}</p>
                <div v-if="trend !== null" class="flex items-center mt-1">
                    <span :class="trendTextClass" class="text-sm">
                        {{ trend > 0 ? '+' : '' }}{{ trend }}%
                    </span>
                    <component 
                        :is="trendIcon" 
                        :class="trendIconClass" 
                        class="w-4 h-4 ml-1" 
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { 
    ChartBarIcon, 
    DocumentTextIcon, 
    CurrencyDollarIcon,
    ExclamationTriangleIcon,
    ArrowUpIcon,
    ArrowDownIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    title: String,
    value: [String, Number],
    icon: {
        type: String,
        default: 'chart'
    },
    trend: {
        type: Number,
        default: null
    }
});

const iconComponent = computed(() => {
    const icons = {
        portfolio: ChartBarIcon,
        loans: DocumentTextIcon,
        revenue: CurrencyDollarIcon,
        risk: ExclamationTriangleIcon,
        chart: ChartBarIcon
    };
    return icons[props.icon] || ChartBarIcon;
});

const iconContainerClass = computed(() => {
    const classes = {
        portfolio: 'bg-blue-100 text-blue-600',
        loans: 'bg-green-100 text-green-600',
        revenue: 'bg-yellow-100 text-yellow-600',
        risk: 'bg-red-100 text-red-600',
        chart: 'bg-purple-100 text-purple-600'
    };
    return classes[props.icon] || 'bg-gray-100 text-gray-600';
});

const trendIcon = computed(() => {
    return props.trend > 0 ? ArrowUpIcon : ArrowDownIcon;
});

const trendTextClass = computed(() => {
    return props.trend > 0 ? 'text-green-600' : 'text-red-600';
});

const trendIconClass = computed(() => {
    return props.trend > 0 ? 'text-green-500' : 'text-red-500';
});
</script>

