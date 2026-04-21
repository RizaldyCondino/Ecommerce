<script setup>
import AdminLayout from '../Components/AdminLayout.vue';
import ProductList from './ProductList.vue';

defineProps({
    products: Object,
});

</script>

<template>
    <AdminLayout>
        <ProductList :products="products">
            
        </ProductList>
    </AdminLayout>
</template>

<

