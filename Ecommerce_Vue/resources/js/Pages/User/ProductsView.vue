<script setup>
import { ref } from "vue";
import Footer from './Layouts/Footer.vue';
import Header from './Layouts/Header.vue';
import {  router } from '@inertiajs/vue3';

const props = defineProps({
  product: Object
})

const mainImage = ref(props.product.product_images[0]?.image || "https://images.unsplash.com/photo-1505740420928-5e560c06d30e")

function changeImage(src) {
  mainImage.value = src
}

const activeIndex = ref(0)
function setActiveImage(index) {
  activeIndex.value = index
  mainImage.value = props.product.product_images[index].image
}

const addToCart = (product) => {
  router.patch(route('productsLisCart.update', product.id), {  // call your update route
    quantity: quantity.value,
    onSuccess: (page) => {
      if (page.props.flash?.success) {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: page.props.flash.success,
          showConfirmButton: false,
          timer: 3000
        });
      }
    },
  });
}
const quantity = ref(1);

function onQtyInput(product) {
  if (quantity.value > product.quantity) {
    quantity.value = product.quantity
  }

  if (quantity.value < 1) {
    quantity.value = 1
  }
}
</script>

<template>
  <Header />

  <div class="flex justify-center bg-gray-100">
    <div class="container mx-auto px-4 py-8">
      <div class="flex flex-wrap mt-8 -mx-4">

        <div class="w-full md:w-1/2 px-4 mt-10 mb-8">
      
          <img 
            :src="`/${mainImage}`" 
            alt="Product"
            class="w-full max-h-[500px] object-contain rounded-lg shadow-md mb-4"
          />

          <div class="flex gap-4 py-4 justify-start overflow-x-auto">
            <img 
              v-for="(image, index) in product.product_images" 
              :key="index"
              :src="`/${image.image}`"
              :alt="`Thumbnail ${index + 1}`"
              class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-md cursor-pointer opacity-60 hover:opacity-100 transition duration-300"
              :class="{'ring-2 ring-indigo-500 opacity-100': activeIndex === index}"
              @click="setActiveImage(index)"
            />
          </div>
        </div>

        <div class="mt-8 w-full md:w-1/2 px-4 mb-10">
          <h2 class="text-3xl font-bold mb-2">{{ props.product.title }}</h2>
          <div class="mb-4">
          <span class="text-2xl font-bold mr-2">
            $ {{ Math.max(0, props.product.price).toLocaleString(undefined) }}
          </span>
        </div>

          <div class="flex items-center mb-4">

            <template v-for="n in 5">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                   class="size-6 text-yellow-500">
                <path fill-rule="evenodd"
                      d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z"
                      clip-rule="evenodd" />
              </svg>
            </template>
            <span class="ml-2 text-gray-600">4.5 (120 reviews)</span>
          </div>

          <p class="text-gray-700 h-250px mb-6">{{ props.product.description }}</p>

          <div class="mb-6">
            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity:</label>
            <input 
              @input="onQtyInput(props.product)"
              type="number" 
              id="quantity" 
              name="quantity" 
              min="1"
              :max="props.product.quantity"
              v-model.number="quantity" 
              class="w-24 text-center rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
            <span class="block mt-2 text-xs font-medium text-gray-700 mb-1">Available: {{ props.product.quantity }} </span>
          </div>

          <div class="flex space-x-4 mb-6">
            <button
             type="button"
              @click="addToCart(product)"
              class="bg-black flex gap-2 items-center text-white px-6 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
              <i class="fa-solid fa-cart-plus"></i>Add to Cart
            </button>
            <!-- <button
              type="button"
              @click="isWishlisted = !isWishlisted"
              class="absolute top-2 right-2 bg-white/80 hover:bg-white rounded-full p-2 shadow transition"
            >
              <i
                :class="isWishlisted ? 'fa-solid fa-heart text-red-500' : 'fa-regular fa-heart text-red-400'"
              ></i>
            </button> -->
            <!-- <button
              @click="isWishlisted = !isWishlisted"
              class="flex gap-2 items-center px-6 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
              :class="isWishlisted ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'"
            >
              Wishlist
              <i :class="isWishlisted ? 'fa-solid fa-heart text-red-500' : 'fa-regular fa-heart text-gray-500'"></i>
            </button> -->
          </div>

        </div>
      </div>
    </div>
  </div>

  <Footer />
</template>
