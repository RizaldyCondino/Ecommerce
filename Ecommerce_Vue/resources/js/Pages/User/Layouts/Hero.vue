<script setup>
// import { onMounted } from 'vue'
// import { initFlowbite } from 'flowbite'

// onMounted(() => {
//     initFlowbite();
// })


</script>

<template>



<!--Hero Section -->
<section class="relative min-h-screen flex items-center">
  
  <!-- BACKGROUND IMAGE -->
  <div class="absolute inset-0">
    <img 
      src="https://cdn.pixabay.com/photo/2017/10/28/23/47/dock-2898497_1280.jpg"
      class="w-full h-full object-cover"
      alt="Background"
    />
    <!-- DARK OVERLAY -->
    <div class="absolute inset-0 bg-black bg-opacity-10"></div>
  </div>

  <!-- CONTENT -->
  <div class="relative container mx-auto px-6 flex justify-end">
    <div class="w-full lg:w-1/2 text-right text-white">
      
      <span class="block w-20 h-1 bg-white mb-6 ml-auto"></span>

      <h1 class="uppercase text-5xl sm:text-7xl font-extrabold leading-tight">
        Discover
        <span class="block text-4xl sm:text-6xl text-pink-400">
          the Future of Gadgets Today 
        </span>
      </h1>

      <p class="mt-6 text-gray-200 max-w-md ml-auto">
        “Shop the latest tech, from smart devices to must-have accessories — all in one place, delivered fast.”
      </p>

      <div class="mt-8">
        <a href="#" class="inline-block uppercase py-3 px-6 rounded-lg border-2 border-white hover:bg-white hover:text-black transition">
          Shope Now!
        </a>
      </div>

    </div>
  </div>

</section>

<!--End Hero Section-->



</template>