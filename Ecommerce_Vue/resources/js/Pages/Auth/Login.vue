<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Header from '../User/Layouts/Header.vue';
import Footer from '../User/Layouts/Footer.vue';
defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>



<template>
    <section class="relative min-h-screen flex items-center">

        
        <!-- <div class="absolute inset-0">
            <img src="https://cdn.pixabay.com/photo/2017/10/28/23/47/dock-2898497_1280.jpg"
                class="w-full h-full object-cover" alt="Background" />
           
            <div class="absolute inset-0 bg-black bg-opacity-10"></div>
        </div> -->

        <!-- CONTENT -->
        <div class="relative container mx-auto px-6 flex justify-center">
            <Header></Header>

            <Head title="Log in" />

            <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                {{ status }}
            </div>

            <div class=" bg-white shadow-lg rounded-xl overflow-hidden mt-10">
                <div class="p-6">
                    <form @submit.prevent="submit">
                        <div class="flex items center p-6">
                            <Link :href="route('user.home')"
                                class="flex items-center space-x-3 rtl:space-x-reverse p-2 rounded-lg hover:bg-gray-100 transition">
                                <i class="fa-solid fa-shop text-7xl" style="color: rgb(30, 168, 199);"></i>
                                <span class="font-semibold text-7xl text-heading">Shops</span>
                            </Link>
                        </div>
                        <div>
                            <InputLabel for="email" value="Email" />

                            <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                                autofocus autocomplete="username" />

                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="password" value="Password" />

                            <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password"
                                required autocomplete="current-password" />

                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="mt-4 block">
                            <label class="flex items-center">
                                <Checkbox name="remember" v-model:checked="form.remember" />
                                <span class="ms-2 text-sm text-gray-600">Remember me</span>
                            </label>
                        </div>

                        <div class="mt-4 flex items-center justify-end">
                            <Link v-if="canResetPassword" :href="route('password.request')"
                                class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Forgot your password?
                            </Link>

                            <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing">
                                Log in
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

</template>
