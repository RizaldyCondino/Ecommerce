<script setup>
import Footer from './Layouts/Footer.vue';
import Header from './Layouts/Header.vue';
import Swal from 'sweetalert2';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  orders: Object,
});



const payOrder = (order) => {
  router.post(route('orders.pay', { order: order.id }), {
  });

};

const formatCurrency = (amount) => {
  return amount.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
};
</script>

<template>
  <Header />

  <div class="flex justify-center py-24">
    <div
      class="overflow-x-auto max-w-screen-lg bg-neutral-primary-soft shadow-xs rounded-base border border-default w-full">

      <!-- Loop over orders -->
      <div v-for="order in orders?.data || []" :key="order.id" class="mb-4">

        <!-- Table for orders with items -->
        <table v-if="order.order_items?.length" class="w-full text-sm text-left text-body border rounded">
          <thead class="text-sm text-body bg-neutral-secondary-soft border-b">
            <tr>
              <th class="px-6 py-1" colspan="4">ID #{{ order.id }}</th>
            </tr>

            <tr>
              <th class="px-6 py-1 text-xs" colspan="1" :class="order.status === 'unpaid'
                ? 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400'
                : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'">
                {{ order.status }}: $ {{ formatCurrency(order.total_price) }}
              </th>
            </tr>

            <tr>
              <th class="px-6 py-2">Product</th>
              <th class="px-6 py-2">Brand</th>
              <th class="px-6 py-2">Category</th>
              <th class="px-6 py-2">Price</th>
              <th class="px-6 py-2">Quantity</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in order.order_items" :key="item.id" class="bg-neutral-primary border-b">
            <td class="px-6 py-2">{{ item.product?.title || 'N/A' }}</td>
            <td class="px-6 py-2">{{ item.product?.brand?.name || 'N/A' }}</td>
            <td class="px-6 py-2">{{ item.product?.category?.name || 'N/A' }}</td>
            <td class="px-6 py-2">
              $ {{ formatCurrency(item.product?.price || 0) }}
            </td>
            <td class="px-6 py-2">
              {{ item.quantity || 1 }} 
            </td>
          </tr>
             <tr v-if="order.status === 'unpaid'">
              <td colspan="5" class="px-2 py-2 text-right">
                <button class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-1 rounded transition"
                  @click="payOrder(order)">
                  Pay Now
                </button>
              </td>
            </tr>
          </tbody>

        </table>

        <!-- Show order info even if no items -->
        <div v-else class="bg-neutral-primary p-4 mb-4 border rounded">
          <div class="font-semibold">ID #{{ order.id }}</div>
          <div :class="order.status === 'unpaid'
            ? 'text-amber-700 dark:text-amber-400'
            : 'text-emerald-700 dark:text-emerald-400'">
            {{ order.status }}: {{ formatCurrency(order.total_price) }}
          </div>
          <div class="text-sm text-body mt-2">No items in this order.</div>
        </div>
      </div>
      <!-- No orders at all -->
      <div v-if="!(orders?.data?.length)" class="p-6 text-center text-body">
        No orders found.
      </div>

    </div>
  </div>

  <Footer />
</template>
