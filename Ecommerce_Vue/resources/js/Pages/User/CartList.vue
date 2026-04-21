<script setup>
import { computed, reactive } from 'vue'
import Swal from 'sweetalert2';
import UserLayouts from './Layouts/UserLayouts.vue';
import InputField from '@/Components/InputField.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';


const page = usePage()

const cart = computed(() => page.props.cart?.data ?? {})

const carts = computed(() => cart.value.items ?? [])
const products = computed(() => cart.value.products ?? [])
const subtotal = computed(() => cart.value.total ?? 0)

const shipping = 5
const tax = 5

const grandTotal = computed(() => subtotal.value + shipping + tax)


// const carts = computed(() => usePage().props.cart.data.items);
// const products = computed(() => usePage().props.cart.data.products);
// const subtotal = usePage().props.cart?.data?.total ?? 0;

// const shipping = 5;
// const tax = 5;
// const grandTotal = subtotal + shipping + tax;

const itemId = (id) => carts.value.findIndex((item) => item.product_id === id);
const update = (product, quantity) =>

  router.patch(route('cart.update', product), {
    quantity,
  });

const remove = (product) =>
  router.delete(route('cart.delete', product));

const props = defineProps({
  userAddress: Object
})

const form = reactive({
  address1: props.userAddress?.address1 ?? null,
  address2: props.userAddress?.address2 ?? null,
  city: props.userAddress?.city ?? null,
  state: props.userAddress?.state ?? null,
  zipcode: props.userAddress?.zipcode ?? null,
  country_code: props.userAddress?.country_code ?? null,
  type: props.userAddress?.type ?? null,
})

const AddForm = useForm({
  address1: null,
  address2: null,
  city: null,
  state: null,
  zipcode: null,
  country_code: null,
  type: null,
})


//confirm order
function submit() {

  if (!form.address1) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'warning',
      title: 'Please add a shipping address first.',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true
    });
  }

  router.visit(route('checkout.store'), {
    method: 'post',
    data: {
      carts: usePage().props.cart.data.items,
      products: usePage().props.cart.data.products,
      total: grandTotal.value,
      address: form,
    },
  })


}


function AddAddress() {
  const page = usePage();
  AddForm.post(route('checkout.address'), {
    onSuccess: () => {
      AddForm.reset()
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
      if (page.props.flash?.error) {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'error',
          title: page.props.flash.error,
          showConfirmButton: false,
          timer: 3000
        });
      }
    },
    onError: (errors) => {
      console.log(errors)
    }
  })
}

const removeAddress = (id) => {
  if (!confirm('Are you sure you want to remove this address?')) return;

  router.patch(route('cart.address.updateIsMain', id), {
    onSuccess: (page) => {
      form.reset();
      if (userAddress && userAddress.id === id) userAddress = null;
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: page.props.flash.success,
        showConfirmButton: false,
        timer: 3000
      });
    }
  });
};
function onQtyInput(product) {
  const item = this.carts[this.itemId(product.id)]
  if (!item) return

  const stock = product.quantity ?? 0
  let qty = Number(item.quantity)
  if (!Number.isFinite(qty) || qty < 1) {
    qty = 1
  }

  qty = Math.min(qty, stock)
  qty = Math.floor(qty)

  item.quantity = qty
}




</script>
<template>
  <UserLayouts>

    <section class="text-gray-600 body-font relative">
      <div class="container px-5 py-24 mx-auto flex sm:flex-nowrap flex-wrap">
        <div class="lg:w-2/3 md:w-1/2 rounded-lg  sm:mr-10 p-10 ">
          <!---List of Cart-->

          <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm text-body bg-neutral-secondary-medium border-b border-default-medium">
              <tr>
                <th scope="col" class="px-16 py-3">
                  <span class="sr-only">Image</span>
                </th>
                <th scope="col" class="px-6 py-3 font-medium">
                  Product
                </th>
                <th scope="col" class="px-6 py-3 font-medium">
                  Qty
                </th>
                <th scope="col" class="px-6 py-3 font-medium">
                  Price
                </th>
                <th scope="col" class="px-6 py-3 font-medium">
                  Action
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="product in products" :key="product.id"
                class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                <td class="p-4">
                  <img v-if="product.product_images?.length > 0" :src="`/${product.product_images[0]?.image}`"
                    class="w-16 md:w-24 max-w-full max-h-full">
                  <img v-else src="https://i.pinimg.com/736x/53/3b/6e/533b6ec4729a3eb881c2bd3f22f0838f.jpg"
                    class="w-16 md:w-24 max-w-full max-h-full">
                </td>
                <td class="px-6 py-4 font-semibold text-heading">
                  {{ product.title }}
                </td>
                <td class="px-6 py-4">
                  <form class="max-w-xs mx-auto">
                    <label for="counter-input-1" class="sr-only">Choose quantity:</label>
                    <div class="relative flex items-center">
                      <button @click.prevent="update(product, carts[itemId(product.id)].quantity - 1)" type="button"
                        id="decrement-button-1" data-input-counter-decrement="counter-input-1"
                        :disabled="carts[itemId(product.id)].quantity <= 1" :class="[
                          carts[itemId(product.id)].quantity > 1
                            ? 'cursor-pointer text-purple-600'
                            : 'cursor-not-allowed text-gray-300 dark:text-gray-500',
                          'flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary rounded-full text-sm focus:outline-none h-6 w-6'
                        ]">

                        <svg class="w-3 h-3 text-heading" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                          width="24" height="24" fill="none" viewBox="0 0 24 24">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 12h14" />
                        </svg>
                      </button>
                      <input
                        v-model="carts[itemId(product.id)].quantity"
                        @input="onQtyInput(product)"
                        type="text"
                        id="counter-input-1"
                        data-input-counter
                        class="shrink-0 text-heading border-0 bg-transparent text-sm font-normal focus:outline-none focus:ring-0 max-w-[2.5rem] text-center"
                        required
                      />
                      <button
                          @click.prevent="update(product, carts[itemId(product.id)].quantity + 1)"
                          :disabled="carts[itemId(product.id)].quantity >= product.quantity"
                          :class="[
                            'flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary rounded-full text-sm focus:outline-none h-6 w-6',
                            carts[itemId(product.id)].quantity >= product.quantity ? 'opacity-50 cursor-not-allowed' : ''
                          ]"
                        >
                        <svg class="w-3 h-3 text-heading" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                          width="24" height="24" fill="none" viewBox="0 0 24 24">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 12h14m-7 7V5" />
                        </svg>
                      </button>
                    </div>
                  </form>
                </td>
                <td class="px-6 py-4 font-semibold text-heading">
                  $ {{ Math.max(0, product.price).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                  }) }}
                </td>
                <td class="px-6 py-4">
                  <a @click="remove(product)"
                    class="font-medium hover:text-900 cursor-pointer text-red-500 hover:underline">Remove</a>
                </td>
              </tr>

            </tbody>
          </table>

          <form @submit.prevent="AddAddress">
            <div class=" bg-white flex flex-col md:ml-auto w-full md:py-8 mt-8 md:mt-0">
              <div class="relative mb-4">
                <InputField v-model="AddForm.address1" label="Address" :error="AddForm.errors.address1"></InputField>
              </div>

              <div class="relative mb-4 flex justify-between gap-x-5">
                <InputField v-model="AddForm.city" label="City" :error="AddForm.errors.city"></InputField>
                <InputField v-model="AddForm.state" label="State" :error="AddForm.errors.state"></InputField>
              </div>

              <div class="relative mb-4 flex justify-between gap-x-5">
                <InputField v-model="AddForm.zipcode" label="Zip Code" :error="AddForm.errors.zipcode"></InputField>
                <InputField v-model="AddForm.country_code" label="Country Code" :error="AddForm.errors.country_code">
                </InputField>
                <InputField v-model="AddForm.type" label="Type (eg. Home or Office)" :error="AddForm.errors.type">
                </InputField>
              </div>

            </div>
            <button type="submit"
              class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg">Submit</button>
          </form>
          <!--End-->
        </div>
        <div class=" lg:w-1/3 md:w-1/2 bg-white flex flex-col md:ml-auto w-full md:py-8 mt-8 md:mt-0">

          <h2 class="text-gray-900 text-lg mb-1 font-medium title-font">Order Summary</h2>
          <form @submit.prevent="submit">
            <div class="px-4 py-4 sm:px-6">
              <div class="flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Total:</h3>
                <p class="mt-1 max-w-2xl text-xm text-gray-500">
                  $ {{ (subtotal > 0 ? subtotal : 0).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                  }) }}
                </p>
              </div>

              <div class=" mt-3 mb-3 flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Shipping:</h3>
                <p class="mt-1 max-w-2xl text-xm text-gray-500">${{ shipping }}</p>
              </div>

              <div class="flex mt-3  items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Tax:</h3>
                <p class="mt-1 max-w-2xl text-xm text-gray-500">$ {{ tax }}</p>
              </div>

              <div class="flex mt-3 items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Total Shipping:</h3>
                <p class="mt-1 max-w-2xl text-xm text-gray-500">$ {{ grandTotal.toLocaleString(undefined, {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                }) }}</p>
              </div>
            </div>

            <button type="submit"
              class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg">Checkout</button>
            <p class="text-xs text-gray-500 mt-3">Continue Shipping</p>
          </form>
          <div v-if="userAddress" v-for="address in [userAddress]" :key="address.id"
            class="relative p-4 my-8 bg-white border border-gray-200 rounded-lg shadow-md sm:p-6 lg:p-8 dark:bg-gray-800 dark:border-gray-700">

            <!-- Close Button -->
            <button @click="removeAddress(address.id)"
              class="absolute top-2 right-4 text-gray-400 hover:text-red-500 font-bold">
              ✕
            </button>

            <h5 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">
              <i class="fa-solid fa-location-dot" style="color: rgb(29, 192, 238);"></i> Shipping Address
            </h5>

            <p class="text-gray-500 pl-2 dark:text-gray-400">
              {{ address.type }}: {{ address.address1 }}{{ address.address2 ? ', ' + address.address2 : '' }},
              {{ address.city }}{{ address.state ? ', ' + address.state : '' }}
              {{ address.zipcode }}, {{ address.country_code }}
            </p>
          </div>

          <!-- Fallback if no address exists -->
          <div v-else
            class="p-4 my-8 bg-white border border-gray-200 rounded-lg shadow-md sm:p-6 lg:p-8 dark:bg-gray-800 dark:border-gray-700">
            <h5 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">
              <i class="fa-solid fa-location-dot" style="color: rgb(29, 192, 238);"></i> Shipping Address
            </h5>
            <p class="text-gray-500 pl-2 dark:text-gray-400">
              Add Shipping address
            </p>
          </div>




        </div>
      </div>
    </section>


  </UserLayouts>

</template>
