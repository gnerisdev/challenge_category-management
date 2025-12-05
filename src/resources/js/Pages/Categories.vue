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
        return { ...field, value: form[field.name] || '' };
    });
});

const getCategories = async () => {
    try {
        const response = await api.get('/categories');
        if (response.data.success) {
            categories.value = response.data.data || [];
        }
    } catch (error) {
        const message = error.response?.data?.message || 'Erro ao carregar categorias';
        alert(message);
    }
};

const createCategory = async (body) => {
    try {
        const dataToSend = { ...body };
        if (parentId.value) dataToSend.parent_id = parentId.value;
        const response = await api.post('/categories', dataToSend);
        if (response.data.success) {
            closeModal();
            await getCategories();
        }
    } catch (error) {
        const message = error.response?.data?.message || 'Erro ao criar categoria';
        alert(message);
    }
};

const updateCategory = async (id, body) => {
    try {
        const response = await api.put(`/categories/${id}`, body);
        if (response.data.success) {
            closeModal();
            await getCategories();
        }
    } catch (error) {
        const message = error.response?.data?.message || 'Erro ao atualizar categoria';
        alert(message);
    }
};

const deleteCategory = async (id) => {
    if (!confirm('Quer excluir esta categoria?')) return;

    try {
        const response = await api.delete(`/categories/${id}`);
        if (response.data.success) {
            await getCategories();
        }
    } catch (error) {
        const message = error.response?.data?.message || 'Erro ao deletar categoria';
        alert(message);
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
        if (response.data.success) {
            isEditing.value = true;
            editingId.value = category.id;
            form.name = response.data.data.name || '';
            form.description = response.data.data.description || '';
            showModal.value = true;
        }
    } catch (error) {
        const message = error.response?.data?.message || 'Erro ao carregar categoria';
        alert(message);
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

const findCategoryById = (categories, categoryId) => {
    if (!categories || !Array.isArray(categories)) {
        return null;
    }
    
    const normalizedId = Number(categoryId);
    
    for (const cat of categories) {
        if (Number(cat.id) === normalizedId) {
            return cat;
        }
        if (cat.children && Array.isArray(cat.children) && cat.children.length > 0) {
            const found = findCategoryById(cat.children, categoryId);
            if (found) return found;
        }
    }
    return null;
};

const findCategoryGroup = (categories, categoryId) => {
    if (!categories || !Array.isArray(categories)) {
        return null;
    }
    
    const normalizedId = Number(categoryId);
    
    for (const cat of categories) {
        if (Number(cat.id) === normalizedId) {
            return categories;
        }
        
        if (cat.children && Array.isArray(cat.children) && cat.children.length > 0) {
            const childMatch = cat.children.find(child => Number(child.id) === normalizedId);
            if (childMatch) {
                return cat.children;
            }
            
            const found = findCategoryGroup(cat.children, categoryId);
            if (found) return found;
        }
    }
    
    return null;
};

const moveCategory = async (direction, category) => {
    console.log(category, '-----', direction)
    try {
        if (!category || (!category.id && category.id !== 0)) {
            alert('Erro: Categoria inválida');
            return;
        }

        const categoryId = category.id;
        const categoryGroup = findCategoryGroup(categories.value, categoryId);
        if (!categoryGroup) {
            alert(`Erro: Grupo de categorias não encontrado`);
            return;
        }

        const currentIndex = categoryGroup.findIndex(cat => Number(cat.id) === Number(categoryId));
        if (currentIndex === -1) {
            alert('Erro: Categoria não encontrada no grupo');
            return;
        }

        let newIndex = direction === 'up' ? currentIndex - 1 : currentIndex + 1;
        if (newIndex < 0 || newIndex >= categoryGroup.length) {
            return;
        }

        const parentId = categoryGroup[0]?.parent_id ?? null;
        const allSameParent = categoryGroup.every(cat => (cat.parent_id ?? null) === parentId);
        if (!allSameParent) {
            alert('Erro: Categorias no grupo têm parent_id diferentes');
            return;
        }

        const newGroup = [...categoryGroup];
        const temp = newGroup[currentIndex];
        newGroup[currentIndex] = newGroup[newIndex];
        newGroup[newIndex] = temp;

        const categoriesToUpdate = newGroup.map((cat, index) => ({
            id: cat.id,
            sort_order: index
        }));

        const response = await api.put('/categories/reorder', { categories: categoriesToUpdate });
        
        if (response.data.success) {
            await getCategories();
        } else {
            alert(response.data.message || 'Erro ao mover categoria');
        }
    } catch (error) {
        console.log(error)
        const message = error.response?.data?.message || 'Erro ao mover categoria';
        alert(message);
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
                    @move-up="category => moveCategory('up', category)"
                    @move-down="category => moveCategory('down', category)"
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
