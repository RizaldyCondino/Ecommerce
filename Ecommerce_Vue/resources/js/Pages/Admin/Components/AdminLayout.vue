<script setup>
import { onMounted } from 'vue'
import { initFlowbite } from 'flowbite'
import Navbar from './Navbar.vue';
import Sidebar from './Sidebar.vue';



// initialize components based on data attribute selectors
onMounted(() => {
    initFlowbite();
})

</script>

<template>

    <div class="antialiased bg-gray-50 dark:bg-gray-900">
    
    <!--Navbar Start-->
        <Navbar></Navbar>
    <!--End-->

    <!-- Sidebar -->
        <Sidebar>
            
        </Sidebar>
    <!--End-->

    <main class="p-2 md:ml-64 h-auto pt-10">
         <slot/>
    </main>
   

    
  </div>
</template>