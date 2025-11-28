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
    },
    xAxis: {
        type: Array,
        default: () => []
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
    if (!chartInstance) return;

    const option = {
        title: {
            text: props.title,
            left: 'center'
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            }
        },
        xAxis: {
            type: 'category',
            data: props.xAxis.length ? props.xAxis : props.data.map(item => item.name)
        },
        yAxis: {
            type: 'value'
        },
        series: [
            {
                data: props.data.map(item => item.value),
                type: 'bar',
                itemStyle: {
                    color: '#3b82f6'
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