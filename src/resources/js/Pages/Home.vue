<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import ApiRequest from '@/services/ApiRequest';

const api = new ApiRequest();

const statistics = ref({
    total: 0,
    main_categories: 0,
    subcategories: 0,
    active: 0
});
const loading = ref(true);

const getStatistics = async () => {
    try {
        loading.value = true;
        const response = await api.get('/categories/statistics');
        
        if (response && response.data && response.data.success) {
            statistics.value = response.data.data;
        }
    } catch (error) {
        console.error('Erro ao buscar estatísticas:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    getStatistics();
});
</script>

<template>
    <AppLayout>
        <div class="p-4 md:p-8">
            <div class="max-w-6xl mx-auto">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">
                        Dashboard
                    </h1>
                    <a
                        href="/categories"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors shadow-md flex items-center gap-2"
                    >
                        Ir p/ Categorias
                    </a>
                </div>

                <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="i in 4" :key="i" class="bg-white rounded-lg shadow-md p-6 animate-pulse">
                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
                        <div class="h-8 bg-gray-200 rounded w-1/2"></div>
                    </div>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                        <div class="text-gray-600 text-sm font-medium mb-2">Total</div>
                        <div class="text-3xl font-bold text-gray-900">{{ statistics.total }}</div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                        <div class="text-gray-600 text-sm font-medium mb-2">Categorias Principais</div>
                        <div class="text-3xl font-bold text-gray-900">{{ statistics.main_categories }}</div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                        <div class="text-gray-600 text-sm font-medium mb-2">Subcategorias</div>
                        <div class="text-3xl font-bold text-gray-900">{{ statistics.subcategories }}</div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
                        <div class="text-gray-600 text-sm font-medium mb-2">Ativos</div>
                        <div class="text-3xl font-bold text-gray-900">{{ statistics.active }}</div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>