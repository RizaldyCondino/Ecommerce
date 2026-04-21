<script setup>
import { onMounted } from 'vue'
import { initFlowbite } from 'flowbite'

import { Link, router } from '@inertiajs/vue3';

import UserLayouts from './Layouts/UserLayouts.vue';
import Hero from './Layouts/Hero.vue';
import Products from './Components/Products.vue';
import PaginationLinks from '@/Components/PaginationLinks.vue';
onMounted(() => {
  initFlowbite();
})

defineProps({
  products: Object,
})




// const { products } = defineProps({ products: Object })

</script>

<template>
  <UserLayouts>
    <Hero></Hero>

    <div class="bg-white">
      <div class="flex justify-end mx-auto max-w-2xl px-4 pt-16  sm:px-6 sm:pt-4  lg:max-w-7xl lg:px-8">
        <Link :href="route('products.index')"
          class="flex items-center text-indigo-700 border hover:bg-indigo-50 border-indigo-600 py-2 px-6 gap-2 rounded ">
          View More<i class="fa-solid fa-arrow-right-long text-indigo-700"></i></Link>

      </div>
      
      <div class="lg:col-span-3">
        <Products :products="products"></Products>
        
      </div>

    </div>
  </UserLayouts>

</template>
