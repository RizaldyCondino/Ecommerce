<script setup>

import PaginationLinksLG from '@/Components/PaginationLinksLG.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({
  products: Object
})

const addToCart = (product) => {
 
  router.post(route('cart.store', product), {}, {
    preserveState: true,   
    preserveScroll: true,  
  });
}
</script>

<template>
  <div class="mx-auto max-w-7xl pb-20 px-3 py-6 sm:px-6 lg:px-8">
    
    <!-- Responsive Grid -->
    <div class="rounded-xl grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
      
      <div
        v-for="product in products.data"
        :key="product.id"
        class="w-full bg-white shadow-md rounded-xl transition duration-300 hover:scale-105 hover:shadow-xl"
      >
        <Link :href="route('productsList.view', product.id)" class="block">

    
          <div class="relative">
            <img
              v-if="product.product_images?.length"
              :src="`/${product.product_images[0].image}`"
              class="w-full h-48 sm:h-56 md:h-60 lg:h-64 object-cover rounded-t-xl"
            />

            <img
              v-else
              src="https://media.istockphoto.com/id/1396814518/vector/image-coming-soon-no-photo-no-thumbnail-image-available-vector-illustration.jpg?s=612x612&w=0&k=20&c=hnh2OZgQGhf0b46-J2z7aHbIWwq8HNlSDaNp2wn_iko="
              class="w-full h-48 sm:h-56 md:h-60 lg:h-64 object-cover rounded-t-xl"
            />

          </div>

          <div class="p-3 space-y-2">

            <div class="flex justify-between">
              <div class="flex justify-start">
                <template v-for="n in 5" :key="n">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="w-3 h-3 sm:w-4 sm:h-4 text-yellow-500"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </template>
                <span class="ml-2 text-xs text-gray-600">4.5 (120 reviews)</span>
              </div>

              <span class="text-gray-400 uppercase text-[10px] sm:text-xs">
                {{ product.brand?.name }}
              </span>
            </div>

            <p class="font-semibold hover:underline text-black text-sm sm:text-base line-clamp-2 leading-tight">
              {{ product.title }}
            </p>

            <div class="flex justify-between">
              <p class="font-bold text-gray-400 text-sm sm:text-base lg:text-xs">
               qty {{ product.quantity }} Avail.
              </p>
              <p class="font-bold text-black text-sm sm:text-base lg:text-lg">
                $ {{ product.price.toLocaleString() }}
              </p>
            </div>
          </div>
        </Link>

        <button
          type="button"
          @click="addToCart(product)"
          :disabled="product.quantity === 0"
          :class="[
            'rounded-b-xl w-full py-3 flex items-center justify-center gap-2 font-medium transition-colors',
            product.quantity === 0
              ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
              : 'bg-black text-white hover:bg-blue-800'
          ]"
        >
          <i class="fa-solid fa-cart-plus"></i>
          <span class="hidden sm:inline">
            {{ product.quantity === 0 ? 'Out of Stock' : 'Add to Cart' }}
          </span>
        </button>
      </div>
      
    </div>
    <PaginationLinksLG :paginator="products"></PaginationLinksLG>
  </div>
</template>


