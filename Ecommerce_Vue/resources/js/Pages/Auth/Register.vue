<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Header from '../User/Layouts/Header.vue';
import Footer from '../User/Layouts/Footer.vue';
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
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
        <Head title="Register" />

        <!-- TRANSPARENT CARD -->
        <div class="max-w-md w-full bg-white rounded-xl p-6 shadow-lg">
            <form @submit.prevent="submit">
                <div class="flex items-center justify-center mb-5">
                    <Link :href="route('user.home')"
                        class="flex items-center space-x-3 rtl:space-x-reverse p-2 rounded-lg transition">
                        <i class="fa-solid fa-shop text-7xl" style="color: rgb(30, 168, 199);"></i>
                        <span class="font-semibold text-7xl text-heading">Shops</span>
                    </Link>
                </div>

                <div>
                    <InputLabel for="name" value="Name" />
                    <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus
                        autocomplete="name" />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mt-4">
                    <InputLabel for="email" value="Email" />
                    <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                        autocomplete="username" />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" value="Password" />
                    <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required
                        autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password_confirmation" value="Confirm Password" />
                    <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                        v-model="form.password_confirmation" required autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <div class="mt-4 flex items-center justify-end">
                    <Link :href="route('login')"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Already registered?
                    </Link>
                    <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing">
                        Register
                    </PrimaryButton>
                </div>
            </form>
        </div>

        
    </div>
</section>


</template>
