<template>
    <div ref="chartRef" :style="{ width: width, height: height }"></div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import * as echarts from 'echarts';

const props = defineProps({
    data: {
        type: Array,
        default: () => []
    },
    width: {
        type: String,
        default: '100%'
    },
    height: {
        type: String,
        default: '400px'
    },
    title: {
        type: String,
        default: ''
    }
});

const chartRef = ref(null);
let chartInstance = null;

const initChart = () => {
    if (!chartRef.value) return;
    
    chartInstance = echarts.init(chartRef.value);
    updateChart();
};

const updateChart = () => {
    if (!chartInstance || !props.data.length) return;

    const option = {
        title: {
            text: props.title,
            left: 'center'
        },
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b}: {c} ({d}%)'
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            data: props.data.map(item => item.name)
        },
        series: [
            {
                name: 'Distribution',
                type: 'pie',
                radius: '50%',
                data: props.data,
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ]
    };

    chartInstance.setOption(option);
};

const resizeChart = () => {
    if (chartInstance) {
        chartInstance.resize();
    }
};

onMounted(() => {
    initChart();
    window.addEventListener('resize', resizeChart);
});

onUnmounted(() => {
    if (chartInstance) {
        chartInstance.dispose();
    }
    window.removeEventListener('resize', resizeChart);
});

watch(() => props.data, updateChart, { deep: true });
watch(() => props.title, updateChart);
</script>