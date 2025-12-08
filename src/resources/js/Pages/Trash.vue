<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import CardTrash from '@/components/CardTrash.vue';
import ApiRequest from '@/services/ApiRequest';

const api = new ApiRequest();
const mainCategories = ref([]);
const subcategories = ref([]);
const loading = ref(true);

const getTrashedCategories = async () => {
    try {
        loading.value = true;
        const response = await api.get('/categories/trashed/list');
        if (response.data.success) {
            mainCategories.value = response.data.data?.main_categories || [];
            subcategories.value = response.data.data?.subcategories || [];
        }
    } catch (error) {
        const message = error.response?.data?.message || 'Erro ao carregar categorias excluídas';
        alert(message);
    } finally {
        loading.value = false;
    }
};

const restoreCategory = async (id, isSubcategory = false) => {
    const message = isSubcategory 
        ? 'Deseja restaurar esta subcategoria?' 
        : 'Deseja restaurar esta categoria e suas subcategorias?';
    
    if (!confirm(message)) return;

    try {
        const response = await api.post(`/categories/trashed/${id}/restore`);
        if (response.data.success) {
            await getTrashedCategories();
        }
    } catch (error) {
        const message = error.response?.data?.message || 'Erro ao restaurar categoria';
        alert(message);
    }
};

const deleteCategory = async (id) => {
    if (!confirm('Excluir permanente?')) return;

    try {
        const response = await api.delete(`/categories/trashed/${id}/permanent`);
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
                <h1 class="text-2xl font-bold text-gray-900">Lixeira</h1>
                 
                <a
                    href="/categories"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors shadow-md flex items-center gap-2"
                >
                    Ir p/ Categorias
                </a>
            </header>

            <div v-if="loading" class="space-y-3">
                <div v-for="i in 3" :key="i" class="bg-white rounded-lg shadow-md p-6 animate-pulse">
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
                    <div class="h-8 bg-gray-200 rounded w-1/2"></div>
                </div>
            </div>

            <div v-else-if="mainCategories.length === 0 && subcategories.length === 0" class="bg-white rounded-lg shadow-md p-12 text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Lixeira vazia</h3>
            </div>

            <div v-else class="space-y-4">
                <div v-if="mainCategories.length > 0">
                    <h3>categoria</h3>
                    <div class="m-3 flex flex-col gap-2">
                        <CardTrash
                            v-for="category in mainCategories"
                            :key="category.id"
                            :category="category"
                            @restore="(id) => restoreCategory(id, false)"
                            @delete="deleteCategory"
                        />
                    </div>
                </div>

                <div v-if="subcategories.length > 0">
                    <h3>Subcategorias</h3>
                    <div class="m-3 flex flex-col gap-2">
                        <CardTrash
                            v-for="subcategory in subcategories"
                            :key="subcategory.id"
                            :category="subcategory"
                            :is-subcategory="true"
                            @restore="(id) => restoreCategory(id, true)"
                            @delete="deleteCategory"
                        />
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>