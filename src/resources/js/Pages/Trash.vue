<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import ApiRequest from '@/services/ApiRequest';

const api = new ApiRequest();
const categories = ref([]);
const loading = ref(true);

const getTrashedCategories = async () => {
    try {
        loading.value = true;
        const response = await api.get('/categories/trashed/list');
        if (response.data.success) categories.value = response.data.data || [];
    } catch (error) {
        const message = error.response?.data?.message || 'Erro ao carregar categorias excluídas';
        alert(message);
    } finally {
        loading.value = false;
    }
};

const restoreCategory = async (id) => {
    if (!confirm('Deseja restaurar esta categoria e suas subcategorias?')) return;

    try {
        const response = await api.post(`/categories/trashed/${id}/restore`);
        if (response.data.success)  await getTrashedCategories();
    } catch (error) {
        const message = error.response?.data?.message || 'Erro ao restaurar categoria';
        alert(message);
    }
};

const deleteCategory = async (id) => {
    if (!confirm('Excluir permanente?')) return;

    try {
        const response = await api.delete(`/categories/trashed/${id}/force`);
        if (response.data.success) await getTrashedCategories();
    } catch (error) {
        const message = error.response?.data?.message || 'Erro ao excluir categoria';
        alert(message);
    }
};

onMounted(() => {
    getTrashedCategories();
});
</script>

<template>
    <AppLayout>
        <section class="max-w-6xl mx-auto p-4 md:p-8">
            <header class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Lixeira</h1>
                 
                <a
                    href="/categories"
                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors"
                >
                    Voltar para Categorias
                </a>
            </header>

            <div v-if="loading" class="space-y-3">
                <div v-for="i in 3" :key="i" class="bg-white rounded-lg shadow-md p-6 animate-pulse">
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
                    <div class="h-8 bg-gray-200 rounded w-1/2"></div>
                </div>
            </div>

            <div v-else-if="categories.length === 0" class="bg-white rounded-lg shadow-md p-12 text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Lixeira vazia</h3>
            </div>

            <div v-else class="space-y-3">
                <div
                    v-for="category in categories"
                    :key="category.id"
                    class="bg-white rounded-lg shadow-md p-4 border-l-4 border-red-500"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-medium text-lg text-gray-800">{{ category.name }}</h3>
                                <span class="px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded">Excluída</span>
                            </div>
                            <p v-if="category.description" class="text-gray-600 text-sm mb-2">{{ category.description }}</p>
                        </div>
                        <div class="flex items-center gap-2 ml-4">
                            <button
                                @click="restoreCategory(category.id)"
                                class="px-3 py-1.5 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center gap-1"
                                title="Restaurar"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Restaurar
                            </button>
                            <button
                                @click="deleteCategory(category.id)"
                                class="px-3 py-1.5 text-sm bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors flex items-center gap-1"
                                title="Excluir Permanentemente"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Excluir
                            </button>
                        </div>
                    </div>

                    <div v-if="category.children && category.children.length > 0" class="mt-4 pl-4 border-l-2 border-gray-200 space-y-2">
                        <div
                            v-for="child in category.children"
                            :key="child.id"
                            class="bg-gray-50 rounded-lg p-3 border-l-2 border-orange-400"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <div class="w-1.5 h-1.5 rounded-full bg-orange-500"></div>
                                        <h4 class="font-medium text-gray-800 text-sm">{{ child.name }}</h4>
                                    </div>
                                    <p v-if="child.description" class="text-gray-600 text-xs mb-1">{{ child.description }}</p>
                                </div>
                                <div class="flex items-center gap-1 ml-2">
                                    <button
                                        @click="restoreCategory(child.id)"
                                        class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 transition-colors"
                                    >
                                        Restaurar
                                    </button>
                                    <button
                                        @click="deleteCategory(child.id)"
                                        class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 transition-colors"                                   
                                    >
                                        Excluir
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>