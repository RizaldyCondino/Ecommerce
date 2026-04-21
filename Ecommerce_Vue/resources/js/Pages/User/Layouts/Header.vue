<script setup>
import { computed, onMounted } from 'vue'
import { initFlowbite } from 'flowbite'
import { Link, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
onMounted(() => {
    initFlowbite();
})
const page = usePage()
const canLogin = computed(() => page.props.canLogin)
const canRegister = computed(() => page.props.canRegister)
const user = computed(() => page.props.auth.user)
const cart = computed(()=> page.props.cart) 

onMounted(() => {
  if (page.props.flash?.info) {
    Swal.fire({
      icon: 'info',
      text: page.props.flash.info,
      confirmButtonColor: '#3085d6'
    });
  }
});
</script>

<template>

<!---Header -->
<nav class="fixed top-0 left-0 w-full z-50 bg-white shadow-md border-b border-gray-200">
  <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
  <Link :href="route('user.home')" class="flex items-center space-x-3 rtl:space-x-reverse">
      <i class="fa-solid fa-shop " style="color: rgb(30, 168, 199);"></i>
      <span class="self-center text-xl text-heading font-semibold whitespace-nowrap">Shops</span>
  </Link>
  <div v-if="canLogin"  class="flex gap-4 items-center md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
      
      <Link :href="route('cart.view')"  class=" relative text-white bg-blue-600 border border-transparent hover:bg-blue-700 focus:ring-4 focus:ring-blue-400 rounded-lg shadow font-medium leading-5 text-sm p-2 focus:outline-none">
        <i class=" fa-solid fa-bag-shopping w-6 h-4"></i>
        <span class="sr-only">Notifications</span>
        <div class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-600 border-2 border-white rounded-full -top-2 -right-2">{{ cart.data.count || 0 }}</div>
      </Link>
      
      <button v-if="user" type="button" class="flex text-sm bg-neutral-primary rounded-full md:me-0 focus:ring-4 focus:ring-neutral-tertiary" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown" data-dropdown-placement="bottom">
        <span class="sr-only">Open user menu</span>
        <div
          class="w-8 h-8 rounded-full bg-[#1ea8c7] text-white flex items-center justify-center uppercase font-semibold"
        >
          {{ $page.props.auth.user ? $page.props.auth.user.name.charAt(0) : '?' }}
        </div>
      </button>
      
      <div class="flex justify-between gap-2" v-else>
        <Link :href="route('login')"
          type="button"
          class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 shadow-xs font-medium leading-5 rounded-md text-sm px-4  py-2.5 focus:outline-none"
        >
          Login
        </Link>

        <Link :href="route('register')" v-if="canRegister"
          type="button"
          class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 shadow-xs font-medium leading-5 rounded-md text-sm px-4 py-2.5 focus:outline-none"
        >
          Register
        </Link>      
      </div>

      <!-- Dropdown menu -->
      <div class="z-50 bg-white hidden bg-neutral-primary-medium border border-default-medium rounded-base shadow-lg w-44" id="user-dropdown">
        <div class="px-4 py-3 text-sm border-b border-default">
          <span class="block text-heading font-medium">
            {{ page.props.auth.user ? page.props.auth.user.name : null }}
          </span>
          <!-- <span class="block text-body truncate">name@flowbite.com</span> -->
        </div>
        <ul class="p-2 text-sm text-body font-medium" aria-labelledby="user-menu-button">
          <li>
            <Link :href="route('orders.dashboard.index')" method="get" >
            <a class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Dashboard</a>
            </Link>
          </li>
          <li>
            <a href="#" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Settings</a>
          </li>
          <li>
            <a href="#" class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Earnings</a>
          </li>
          <Link :href="route('logout')" method="post" >
            <a class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Sign out</a>
          </Link>
        </ul>
      </div>
      <button data-collapse-toggle="navbar-user" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-body rounded-base md:hidden hover:bg-neutral-secondary-soft hover:text-heading focus:outline-none focus:ring-2 focus:ring-neutral-tertiary" aria-controls="navbar-user" aria-expanded="false">
        <span class="sr-only">Open main menu</span>
        <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14"/></svg>
      </button>
  </div>
  <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-user">
    <ul class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-default rounded-base bg-neutral-secondary-soft md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:bg-neutral-primary">
      <li>
        <a href="#" class="block py-2 px-3 text-white bg-brand rounded md:bg-transparent md:text-fg-brand md:p-0" aria-current="page">Home</a>
      </li>
      <li>
        <a href="#" class="block py-2 px-3 text-heading rounded hover:bg-neutral-tertiary md:hover:bg-transparent md:border-0 md:hover:text-fg-brand md:p-0 md:dark:hover:bg-transparent">Company</a>
      </li>
      <li>
        <a href="#" class="block py-2 px-3 text-heading rounded hover:bg-neutral-tertiary md:hover:bg-transparent md:border-0 md:hover:text-fg-brand md:p-0 md:dark:hover:bg-transparent">Marketplace</a>
      </li>
      <li>
        <a href="#" class="block py-2 px-3 text-heading rounded hover:bg-neutral-tertiary md:hover:bg-transparent md:border-0 md:hover:text-fg-brand md:p-0 md:dark:hover:bg-transparent">Features</a>
      </li>
      <li>
        <a href="#" class="block py-2 px-3 text-heading rounded hover:bg-neutral-tertiary md:hover:bg-transparent md:border-0 md:hover:text-fg-brand md:p-0 md:dark:hover:bg-transparent">Term</a>
      </li>
      <li>
        <a href="#" class="block py-2 px-3 text-heading rounded hover:bg-neutral-tertiary md:hover:bg-transparent md:border-0 md:hover:text-fg-brand md:p-0 md:dark:hover:bg-transparent">Contact</a>
      </li>
    </ul>
  </div>
  </div>
</nav>


<!---End Header-->





</template>