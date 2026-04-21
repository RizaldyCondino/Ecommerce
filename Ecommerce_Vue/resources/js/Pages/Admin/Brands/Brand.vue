<script setup>
import PaginationLinks from '@/Components/PaginationLinks.vue';
import AdminLayout from '../Components/AdminLayout.vue';
import { ref, watch } from 'vue'
import InputField from '@/Components/InputField.vue';
import { router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { debounce } from 'lodash';

const props = defineProps({
    brands: Object,
})


const addForm = useForm({
    name: null,
})

const search = ref('')

watch(search, debounce((value) => {
    router.get(route('brand.index'), {
        search: value
    }, {
        preserveState: true,
        replace: true
    })
}, 300))


/* --- Add Dialog --- */
const showAddDialog = ref(false)
// const newBrand = ref('')

const addBrand = () => {

    addForm.post(route('brand.store'), {
        onSuccess: (page) => {

            if (page.props.flash.success) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: page.props.flash.success,
                    showConfirmButton: false,
                    timer: 3000
                })
            }
            showAddDialog.value = false


            addForm.reset()
        }
    })

}

const UpdateForm = useForm({
    name: props.brands.name,
})

/* --- Edit Dialog --- */
const showEditDialog = ref(false)

const openEditDialog = (brand) => {
    showEditDialog.value = true;
    UpdateForm.name = brand.name;
    UpdateForm.id = brand.id;
}

const updateBrand = () => {
    if (!UpdateForm.id) return;

    UpdateForm.put(route('brand.update', UpdateForm.id), {
        preserveScroll: true,
        onSuccess: (page) => {
            if (page.props.flash.success) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: page.props.flash.success,
                    showConfirmButton: false,
                    timer: 3000
                });
            }

            showEditDialog.value = false;
            UpdateForm.reset();
        },
        onError: (errors) => {
            console.log(errors); 
        }
    });
};
watch(search, (value) => {
    router.get(route('brand.index'), {
        search: value
    }, {
        preserveState: true,
        replace: true
    })
})
/* --- Delete --- */
// const deleteBrand = (id) => {
//     if (confirm('Are you sure you want to delete this brand?')) {

//     }
// }
</script>

<template>
    <AdminLayout>
        <div class="flex-1 overflow-y-auto lg:p-8 dark:bg-dark-bg bg-slate-50/50 pt-4 pr-4 pb-4 pl-4">

            <!-- Brands Table Card -->
            <div
                class="w-full rounded-xl border border-slate-200 bg-white shadow-sm dark:border-dark-border dark:bg-dark-card">

                <!-- Header -->
                <div
                    class="flex flex-col gap-4 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between dark:border-dark-border">

                    <div class="flex justify-between">
                        
                        <!-- <div class="p-4">
                            <input v-model="search" type="text" placeholder="Search brand..."
                                class="w-full rounded border p-2 dark:bg-dark-card dark:border-dark-border" />
                        </div> -->
                        <form class="flex items-center">
                            <label for="simple-search" class="sr-only">Search</label>
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                        fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" v-model="search"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Search" required="" />
                            </div>
                        </form>
                    </div>

                    <div class="flex items-center gap-2">
                        <button @click="showAddDialog = true"
                            class="flex items-center gap-1 rounded bg-blue-800 px-3 py-1 text-white hover:bg-blue-600">
                            <i class="fas fa-plus"></i>
                            Add Brands
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="border-b border-slate-200 bg-slate-50/50 text-xs font-medium uppercase tracking-wide text-slate-500 dark:border-dark-border dark:bg-white/5 dark:text-slate-400">
                                <th class="p-4">Brand</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-dark-border text-sm">
                            <tr v-for="brand in brands.data" :key="brand.id"
                                class="group hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">

                                <td class="p-4 font-medium text-slate-900 dark:text-white">
                                    {{ brand.name }}
                                </td>

                                <td class="p-4 text-center flex justify-end gap-2 pr-10">
                                    <button @click="openEditDialog(brand)" class="text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <button @click="deleteBrand(brand.id)" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <PaginationLinks :paginator="brands" />
            </div>

            <!-- Add Brand Dialog -->
            <div v-if="showAddDialog" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                <div
                    class="w-full max-w-md sm:w-3/4 md:w-1/2 lg:w-1/4 rounded-xl bg-white p-6 shadow-lg dark:bg-dark-card">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Add Brand</h3>
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-4">
                            <InputField label="Brand" v-model="addForm.name"></InputField>
                        </div>

                        <div class="flex justify-end gap-2 mt-2">
                            <button @click="showAddDialog = false"
                                class="rounded border border-slate-300 px-4 py-2 hover:bg-slate-100 dark:border-dark-border dark:hover:bg-white/10">
                                Cancel
                            </button>
                            <button @click="addBrand"
                                class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Brand Dialog -->
            <div v-if="showEditDialog" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                <div
                    class="w-full max-w-md sm:w-3/4 md:w-1/2 lg:w-1/4 rounded-xl bg-white p-6 shadow-lg dark:bg-dark-card">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Edit Brand</h3>
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-4">
                            <InputField label="Brand" v-model="UpdateForm.name"></InputField>
                        </div>

                        <div class="flex justify-end gap-2 mt-2">
                            <button @click="showEditDialog = false"
                                class="rounded border border-slate-300 px-4 py-2 hover:bg-slate-100 dark:border-dark-border dark:hover:bg-white/10">
                                Cancel
                            </button>
                            <button @click="updateBrand"
                                class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </AdminLayout>
</template>
