<script setup>
import { ref, onMounted, reactive, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/components/Modal.vue';
import FormDefault from '@/components/FormDefault.vue';
import ButtonDefault from '@/components/ButtonDefault.vue';
import CardCategory from '@/components/CardCategory.vue';
import ApiRequest from '@/services/ApiRequest';

const api = new ApiRequest();

const categories = ref([]);
const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const parentId = ref(null);

const fields = ref([
    {
        name: 'name',
        label: 'Nome',
        type: 'text',
        width: '100%',
        required: true
    },
    {
        name: 'description',
        label: 'Descrição',
        type: 'textarea',
        width: '100%'
    }
]);

const form = reactive({ name: '', description: '' });

const formFields = computed(() => {
    return fields.value.map(field => {
        return {
            ...field,
            value: form[field.name] || ''
        };
    });
});

const getCategories = async () => {
    try {
        const response = await api.get('/categories');
        const data = await response.json();
        if (!response.ok) throw new Error(data.message);
        categories.value = data.data || [];
    } catch (error) {
        alert(error.message || 'Erro ao carregar categorias');
    }
};

const createCategory = async (body) => {
    try {
        const dataToSend = { ...body };
        if (parentId.value) dataToSend.parent_id = parentId.value;
        const response = await api.post('/categories', dataToSend);
        const data = await response.json();
        if (!response.ok) throw new Error(data.message);
        closeModal();
        await getCategories();
    } catch (error) {
        console.log(error);
        alert(error.message || 'Erro ao criar categoria');
    }
};

const updateCategory = async (id, body) => {
    try {
        const response = await api.put(`/categories/${id}`, body);
        const data = await response.json();
        if (!response.ok) throw new Error(data.message);
        closeModal();
        await getCategories();
    } catch (error) {
        alert(error.message || 'Erro ao atualizar categoria');
    }
};

const deleteCategory = async (id) => {
    if (!confirm('Quer excluir esta categoria?')) return;

    try {
        const response = await api.delete(`/categories/${id}`);
        const data = await response.json();
        if (!response.ok) throw new Error(data.message);
        await getCategories();
    } catch (error) {
        alert(error.message || 'Erro ao deletar categoria');
    }
};

const openModal = () => {
    isEditing.value = false;
    editingId.value = null;
    parentId.value = null;
    form.name = '';
    form.description = '';
    showModal.value = true;
};

const openEditModal = async (category) => {
    try {
        const response = await api.get(`/categories/${category.id}`);
        const data = await response.json();
        if (!response.ok) throw new Error(data.message);
    
        isEditing.value = true;
        editingId.value = category.id;
        form.name = data.data.name || '';
        form.description = data.data.description || '';
        showModal.value = true;
    } catch (error) {
        alert(error.message || 'Erro ao carregar categoria');
    }
};

const closeModal = () => {
    showModal.value = false;
    isEditing.value = false;
    editingId.value = null;
    parentId.value = null;
    form.name = '';
    form.description = '';
};

const sendForm = (data) => {
    if (isEditing.value && editingId.value) {
        updateCategory(editingId.value, data);
    } else {
        createCategory(data);
    }
};

const openSubcategoryModal = (parentCategory) => {
    isEditing.value = false;
    editingId.value = null;
    parentId.value = parentCategory.id;
    form.name = '';
    form.description = '';
    showModal.value = true;
};

const findCategoryGroup = (categories, categoryId) => {
    for (const cat of categories) {
        if (cat.id === categoryId) return categories;
        
        if (cat.children && cat.children.length > 0) {
            const found = findCategoryGroup(cat.children, categoryId);
            if (found) return found;
        }
    }
    return null;
};

const moveCategory = async (category, direction) => {
    try {
        const categoryGroup = findCategoryGroup(categories.value, category.id);
        if (!categoryGroup) return;

        const currentIndex = categoryGroup.findIndex(cat => cat.id === category.id);
        if (currentIndex === -1) return;

        let newIndex = direction === 'up' ? currentIndex - 1 : currentIndex + 1;
        if (newIndex < 0 || newIndex >= categoryGroup.length) return;

        const temp = categoryGroup[currentIndex];
        categoryGroup[currentIndex] = categoryGroup[newIndex];
        categoryGroup[newIndex] = temp;

        const categoriesToUpdate = categoryGroup.map((cat, index) => ({
            id: cat.id,
            sort_order: index
        }));

        const response = await api.post('/categories/reorder', {
            categories: categoriesToUpdate
        });
        
        const data = await response.json();
        if (!response.ok) throw new Error(data.message);
        
        await getCategories();
    } catch (error) {
        alert(error.message || 'Erro ao mover categoria');
        await getCategories();
    }
};

onMounted(() => {
    getCategories();
});
</script>

<template>
    <AppLayout>
        <section class="max-w-6xl mx-auto p-4 md:p-8">
            <header class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Categorias</h1>
                <ButtonDefault @click="openModal">Nova Categoria</ButtonDefault>
            </header>

            <div v-if="categories.length === 0" class="bg-white rounded-lg shadow-md p-12 text-center">
                <h3 class="mt-2 text-sm font-medium text-gray-900">
                    Nenhuma categoria encontrada
                </h3>
            </div>

            <div 
                v-else 
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
            >
                <CardCategory 
                    v-for="(category, index) in categories" 
                    :key="category.id"
                    :category="category"
                    :index="index"
                    :total="categories.length"
                    @edit="openEditModal"
                    @delete="deleteCategory"
                    @add-subcategory="openSubcategoryModal"
                    @move-up="moveCategory($event, 'up')"
                    @move-down="moveCategory($event, 'down')"
                />
            </div>
        </section>

        <Modal 
            :title="isEditing ? 'Editar Categoria' : (parentId ? 'Nova Subcategoria' : 'Nova Categoria')" 
            :show="showModal" 
            @close="closeModal"
        >
           <FormDefault :fields="formFields" @submit="sendForm" @close="closeModal" />
        </Modal>
    </AppLayout>
</template>
