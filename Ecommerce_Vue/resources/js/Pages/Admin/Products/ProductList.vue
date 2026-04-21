<script setup>
import ComboBox from "@/Components/ComboBox.vue";
import InputField from "@/Components/InputField.vue";
import PaginationLinks from "@/Components/PaginationLinks.vue";
import PrimaryBtn from "@/Components/PrimaryBtn.vue";
import TextArea from "@/Components/TextArea.vue";

import { router, useForm, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import Swal from 'sweetalert2'

defineProps({
    products: Object,
    brands:Object,
    category:Object
});

// const products = usePage().props.products;
const categories = usePage().props.categories;
const brands = usePage().props.brands;

// const selectedCategory = ref("");
// const selectedBrand = ref("");

//---Options---<

const categoryOptions = computed(() =>
    categories.map((c) => ({
        value: c.id,
        label: c.name,
    })),

);

const brandOptions = computed(() =>
    brands.map((b) => ({
        value: b.id,
        label: b.name,
    })),
);

const isAddProduct = ref(false);
const editMode = ref(false);
const dialogVisible = ref(false);

//Open add Modal

const openAddModal = () => {
    isAddProduct.value = true;
    dialogVisible.value = true;
    editMode.value = false;
    addForm.reset()
};

const openEditModal = (product) => {
    isAddProduct.value = false;
    dialogVisible.value = true;
    editMode.value = true;
    addForm.id = product.id;
    addForm.title = product.title;
    addForm.price = product.price;
    addForm.quantity = product.quantity;
    addForm.brand_id = product.brand_id;
    addForm.category_id = product.category_id;
    addForm.description = product.description;

    addForm.product_images = (product.product_images || []).map(img => ({
        id: img.id,
        image: img.image
    }));

};

const updateProduct = () => {
    const productId = addForm.id;
    addForm.product_images = productImages.value.map(f => f.raw);

    addForm.post(route('admin.products.update', productId), {
        data: { ...addForm, _method: 'PUT' },
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
            dialogVisible.value = false
            addForm.reset()
            productImages.value = []

        },
        onError: (errors) => {
            addForm.errors = errors;
        }
    });
};

const handleClose = (done) => {
    dialogVisible.value = false
    addForm.clearErrors()
    done()
}

const addForm = useForm({
    title: '',
    price: '',
    quantity: '',
    category_id: null,
    brand_id: null,
    description: '',
    product_images: []
})

const addProduct = () => {

    addForm.product_images = productImages.value.map(f => f.raw)
    addForm.post(route('admin.products.store'), {
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

            dialogVisible.value = false
            addForm.reset()
            productImages.value = []

        }

    })

}
// Upload Multiple Images
const productImages = ref([]);
const dialogImageUrl = ref();

const handleFileChange = (file) => {
    productImages.value.push(file);
}

const handlePictureCardPreview = (file) => {
    dialogImageUrl.value = file.url
    dialogVisible.value = true
}

const handleRemove = (file) => {
    console.log(file)
}

const deleteImage = (id) => {
    if (!confirm('Are you sure you want to delete this image?')) return;

    router.delete(route('admin.products.image.delete', id), {
        onSuccess: (page) => {

            addForm.product_images = addForm.product_images.filter(img => img.id !== id);

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

const updateStatus = (product) => {
    product.processing = true;

    router.put(route('products.updatePublished', product.id), {}, {
        preserveScroll: true,
        onFinish: () => {
            product.published = !product.published;
            product.processing = false;

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `Product ${product.published ? 'published' : 'unpublished'} successfully`,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }
    });
};

</script>

<template>
    <section class="bg-gray-50 dark:bg-gray-900 p-2 sm:p-5">
        <!--Dialog-->
        <el-dialog v-model="dialogVisible" :title="editMode ? 'Edit Product' : 'Add Product'" width="60%"
            :before-close="handleClose">
            <div class="flex gap-4 p-4">
                <!-- LEFT: Form Fields -->
                <div class="flex-1">
                    <form @submit.prevent="editMode ? updateProduct() : addProduct()" class="space-y-4">
                        <input-field label="Title" v-model="addForm.title" :error="addForm.errors.title" />

                        <input-field label="Price" type="  number" v-model="addForm.price"
                            :error="addForm.errors.price" />

                        <input-field label="Quantity" type="number" v-model="addForm.quantity"
                            :error="addForm.errors.quantity" />

                        <combo-box id="category" name="category" label="Select Category" placeholder="Choose category"
                            :options="categoryOptions" v-model="addForm.category_id" />

                        <p v-if="addForm.errors.category_id" class="text-red-500 text-xs mb-1">
                            {{ addForm.errors.category_id = "The category field is required" }}
                        </p>

                        <combo-box id="brand" name="brand" label="Select Brand" placeholder="Choose brand"
                            :options="brandOptions" v-model="addForm.brand_id" />
                        <p v-if="addForm.errors.brand_id" class="text-red-500 text-xs mb-1">
                            {{ addForm.errors.brand_id = "The brand field is required" }}
                        </p>

                        <text-area label="Description" placeholder="Description..." :rows="4"
                            v-model="addForm.description" />

                        <primary-btn :dissable="addForm.processing"> Submit </primary-btn>
                    </form>
                </div>

                <!-- RIGHT: Photos / Uploads Multiple Images  -->
                <div class="flex-1 border-l border-gray-200 pl-4">
                    <h3 class="mb-2 font-semibold">Product Photos</h3>
                    <div class="space-y-2">
                        <div class="grid grid-cols-2 md:grid-cols-1 gap-4">
                            <div>

                                <el-upload v-model:file-list="productImages" list-type="picture-card" multiple
                                    :on-preview="handlePictureCardPreview" :on-remove="handleRemove"
                                    :on-change="handleFileChange">
                                    <i class="fa-regular fa-image"></i>
                                    <i class="fa-solid fa-plus"></i>
                                </el-upload>
                            </div>

                        </div>
                    </div>

                    <div v-if="editMode">
                        <h3 class="mb-2 font-semibold mt-2">List of photos</h3>
                        <div class="flex flex-wrap gap-2">
                            <div v-for="(pimage, index) in addForm.product_images" :key="pimage.id" class="relative">
                                <img class="w-32 h-32 rounded-base" :src="`/${pimage.image}`" alt="Product Image">

                                <!-- Delete Button -->
                                <button @click="deleteImage(pimage.id)"
                                    class="absolute top-1 right-1 w-6 h-6 bg-red-600 text-white rounded-full flex items-center justify-center text-sm hover:bg-red-700 focus:outline-none">
                                    ×
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </el-dialog>

        <!--End Dialog-->

        <div class="mx-auto max-w-screen-full px-4 ">
            <!-- Start coding here -->
            <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                <div
                    class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                    <div class="w-full md:w-1/2">

                    <!--SEARCH Label-->
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
                                <input type="text" id="simple-search"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Search" required="" />
                            </div>
                        </form>
                    </div>
                    <div
                        class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                        <button type="button" @click="openAddModal"
                            class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                            <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                            </svg>
                            Add product
                        </button>
                        <div class="flex items-center space-x-3 w-full md:w-auto">


                            <button id="filterDropdownButton" data-dropdown-toggle="filterDropdown"
                                class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true"
                                    class="h-4 w-4 mr-2 text-gray-400" viewbox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                        clip-rule="evenodd" />
                                </svg>
                                Filter
                                <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path clip-rule="evenodd" fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                </svg>
                            </button>
                            
                            <div id="filterDropdown"
                                class="z-10 hidden w-48 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
                                <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">
                                    Choose brand
                                </h6>
                                <ul class="space-y-2 text-sm" aria-labelledby="filterDropdownButton">
                                    <li class="flex items-center">
                                        <input id="apple" type="checkbox" value=""
                                            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                                        <label for="apple"
                                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Apple
                                            (56)</label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">
                                    Product name
                                </th>
                                <th scope="col" class="px-4 py-3">Category</th>
                                <th scope="col" class="px-4 py-3">Brand</th>
                                <th scope="col" class="px-4 py-3">Quantity</th>
                                <th scope="col" class="px-4 py-3">Price</th>
                                <th scope="col" class="px-4 py-3">Stock</th>
                                <th scope="col" class="px-4 py-3">Publish</th>
                                <th scope="col" class="px-4 py-3">Action</th>
                                <!-- <th scope="col" class="px-4 py-3">
                                    <span class="sr-only">Actions</span>
                                </th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in products.data" :key="product.id"
                                class="border-b dark:border-gray-700">
                                <th scope="row"
                                    class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ product.title }}
                                </th>
                                <td class="px-4 py-3">
                                    {{ product.category ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ product.brand ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ product.quantity }}
                                </td>
                                <td class="px-4 py-3"> $ {{ Math.max(0, product.price).toLocaleString(undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                    }) }}</td>
                                <td class="px-4 py-3">
                                    <span :class="product.quantity > 0 ? 'text-emerald-600' : 'text-red-600'">
                                        {{ product.quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <button @click="updateStatus(product)" :class="{
                                        'px-3 py-1 rounded-md text-xs font-medium transition-colors duration-200 focus:outline-none': true,
                                        'bg-emerald-600 text-white': product.published,
                                        'bg-red-600 text-white': !product.published
                                    }">
                                        {{ product.published ? 'Published' : 'Unpublished' }}
                                    </button>
                                </td>

                                <td class="px-4 py-3 flex items-center justify-">

                                    <button @click="
                                        openEditModal(product)
                                        "
                                        class="block py-2 px-4 text-blue-600 rounded-md  hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-blue">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <pagination-links :paginator="products"></pagination-links>

            </div>
        </div>
    </section>
</template>
