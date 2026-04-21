<script setup>
import { onMounted } from "vue";
import { initFlowbite } from "flowbite";
import AdminLayout from "./Components/AdminLayout.vue";
import { router } from '@inertiajs/vue3'
import Chart from "chart.js/auto";

// initialize components based on data attribute selectors
import { ref } from "vue";
import PaginationLinks from "@/Components/PaginationLinks.vue";
import Swal from "sweetalert2";

const today = ref(new Date().toLocaleDateString("en-US", {
    month: "short", // Oct
    day: "numeric", // 24
    year: "numeric" // 2023
}));

const props = defineProps({
    totalRevenue: Number,
    growthPercentage: Number,
    activeUsers: Number,
    activeUserGrowth: Number, // add this if you want the growth %
    salesThisMonth: Number,
    totalProduct: Number,
    monthlyRevenue: Array,
    transactions: Array,
})

onMounted(() => {
    initFlowbite();

    const ctx = document.getElementById("revenueChart").getContext("2d");

    new Chart(ctx, {
        type: "line",
        data: {
            labels: [
                "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
            ],
            datasets: [{
                label: "Revenue ($)",
                data: props.monthlyRevenue,
                backgroundColor: "rgba(6,81,237,0.1)",
                borderColor: "rgba(6,81,237,1)",
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // ✅ makes chart fill parent div
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});


const updateStatus = (transaction) => {
    transaction.processing = true;

    router.put(route("transactions.update", transaction.id), {
        status: transaction.status
    }, {
        preserveScroll: true,

        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "Updated!",
                text: "Transaction status updated successfully.",
                timer: 1500,
                showConfirmButton: false
            });
        },

        onFinish: () => {
            transaction.processing = false;
        }
    });
};
</script>
<template>
    <AdminLayout>

        <div class="flex-1 overflow-y-auto lg:p-8 dark:bg-dark-bg bg-slate-50/50 pt-4 pr-4 pb-4 pl-4">

            <!-- Page Header -->
            <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div class="">
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">Overview</h1>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Welcome back, here's what's happening
                        today.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-9 items-center rounded-lg border border-slate-200 bg-white px-3 shadow-sm dark:border-dark-border dark:bg-dark-card">
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-300">
                            {{ today }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Card 1 -->
                <div
                    class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] transition-all hover:-translate-y-1 hover:shadow-lg dark:border-dark-border dark:bg-dark-card dark:shadow-none">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">TOTAL REVENUE</p>
                            <h3 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">
                                ${{ totalRevenue.toLocaleString() }}
                            </h3>
                        </div>
                        <div
                            class="dark:bg-brand-900/20 dark:text-brand-300 text-brand-900 bg-brand-50 rounded-lg pt-2 pr-2 pb-1 pl-2">
                            <iconify-icon icon="solar:dollar-linear" width="20" height="20"
                                style="color: rgb(1, 6, 148);"></iconify-icon>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-1 text-xs">
                        <span
                            :class="growthPercentage >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'"
                            class="flex items-center font-medium">
                            <iconify-icon
                                :icon="growthPercentage >= 0 ? 'solar:arrow-right-up-linear' : 'solar:arrow-right-down-linear'"
                                class="mr-0.5"></iconify-icon>
                            {{ growthPercentage }}%
                        </span>
                        <span class="text-slate-400">from last month</span>
                    </div>
                </div>

                <!-- Card 2 -->
                <div
                    class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] transition-all hover:-translate-y-1 hover:shadow-lg dark:border-dark-border dark:bg-dark-card dark:shadow-none">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">ACTIVE USERS</p>
                            <h3 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">
                                {{ activeUsers.toLocaleString() }}
                            </h3>
                        </div>
                        <div
                            class="dark:bg-purple-900/20 dark:text-purple-300 text-purple-600 bg-purple-50 rounded-lg pt-2 pr-2 pb-0 pl-2">
                            <iconify-icon icon="solar:users-group-two-rounded-linear" width="20"></iconify-icon>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-1 text-xs">
                        <span class="flex items-center font-medium text-emerald-600 dark:text-emerald-400">
                            <iconify-icon icon="solar:arrow-right-up-linear" class="mr-0.5"></iconify-icon>
                            +{{ activeUserGrowth }}%
                        </span>
                        <span class="text-slate-400">from last month</span>
                    </div>
                </div>

                <!-- Card 3 -->
                <div
                    class="group overflow-hidden transition-all hover:-translate-y-1 hover:shadow-lg dark:border-dark-border dark:bg-dark-card dark:shadow-none bg-white border-slate-200 border rounded-xl pt-5 pr-5 pb-5 pl-5 relative shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)]">
                    <div class="flex items-start justify-between">
                        <div class="">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Sales</p>
                            <h3 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">
                                {{ salesThisMonth }}
                            </h3>
                        </div>
                        <div
                            class="dark:bg-orange-900/20 dark:text-orange-300 text-orange-600 bg-orange-50 rounded-lg pt-2 pr-2 pb-0 pl-2">
                            <iconify-icon icon="solar:graph-down-linear" width="20" class=""></iconify-icon>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-1 text-xs">
                        <span class="flex items-center font-medium text-green-500">
                            <iconify-icon icon="solar:arrow-right-down-linear" class="mr-0.5"></iconify-icon>
                            this month
                        </span>

                    </div>
                </div>

                <!-- Card 4 -->
                <div
                    class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] transition-all hover:-translate-y-1 hover:shadow-lg dark:border-dark-border dark:bg-dark-card dark:shadow-none">
                    <div class="flex items-start justify-between">
                        <div class="">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Products</p>
                            <h3 class="dark:text-white text-2xl font-semibold text-slate-900 tracking-tight mt-2">{{
                                totalProduct }}
                            </h3>
                        </div>
                        <div
                            class="dark:bg-teal-900/20 dark:text-teal-300 text-teal-600 bg-teal-50 rounded-lg pt-2 pr-2 pb-0 pl-2">
                            <iconify-icon icon="solar:clock-circle-linear" width="20"></iconify-icon>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-1 text-xs">
                        <span class="flex items-center font-medium text-emerald-600 dark:text-emerald-400">
                            <iconify-icon icon="solar:arrow-right-up-linear" class="mr-0.5"></iconify-icon>

                        </span>

                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="mt-6 w-full">
                <!-- Main Chart -->
                <div
                    class="w-full rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-dark-border dark:bg-dark-card">
                    <!-- Chart Header -->
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Revenue Growth</h3>
                        <select
                            class="rounded-lg border border-slate-200 bg-transparent px-2 py-1 text-xs text-slate-600 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-dark-border dark:text-slate-400">
                            <option>This Year</option>
                            <option>Last Year</option>
                        </select>
                    </div>

                    <!-- Chart Canvas -->
                    <div class="relative w-full" style="height: 400px;">
                        <canvas id="revenueChart" class="w-full h-full"></canvas>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div
                class="mt-6 rounded-xl border border-slate-200 bg-white shadow-sm dark:border-dark-border dark:bg-dark-card">
                <div
                    class="flex flex-col gap-4 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between dark:border-dark-border">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Recent Transactions</h3>
                    <div class="flex items-center gap-2">
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="border-b border-slate-200 bg-slate-50/50 text-xs font-medium uppercase tracking-wide text-slate-500 dark:border-dark-border dark:bg-white/5 dark:text-slate-400">

                                <th class="p-4">Customer</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Date</th>
                                <th class="p-4">Amount</th>

                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-dark-border text-sm">
                            <tr v-for="transaction in transactions.data" :key="transaction.id"
                                class="group hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">

                                <td class="p-4">
                                    <div class="flex items-center gap-3">

                                        <div class="flex flex-col">
                                            <span class="font-medium text-slate-900 dark:text-white">{{
                                                transaction.user?.name ?? 'N/A' }}</span>
                                            <span class="text-xs text-slate-500">{{ transaction.user?.email ?? 'N/A'
                                            }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="p-4">
                                    <select v-model="transaction.status" @change="updateStatus(transaction)" :class="{
                                        'rounded-md px-2 py-1 text-xs font-medium focus:outline-none transition-colors duration-200': true,
                                        'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400': transaction.status?.toLowerCase() === 'paid',
                                        'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400': transaction.status?.toLowerCase() === 'pending',
                                        'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400': transaction.status?.toLowerCase() === 'failed'
                                    }">
                                        <option value="paid">Paid</option>
                                        <option value="pending">Pending</option>
                                        <option value="failed">Failed</option>
                                    </select>
                                </td>
                                <td class="p-4 text-slate-500 dark:text-slate-400">
                                    {{ new Date(transaction.created_at).toLocaleDateString('en-US', {
                                        month: 'short',
                                        day: 'numeric', year: 'numeric'
                                    }) }}
                                </td>

                                <td class="p-4 font-medium text-slate-900 dark:text-white">
                                    ${{ Number(transaction.amount).toLocaleString() }}
                                </td>

                                <td class="p-4 text-right">
                                    <button
                                        class="text-slate-400 hover:text-brand-900 dark:hover:text-white transition-colors">
                                        <iconify-icon icon="solar:menu-dots-linear" width="20"></iconify-icon>
                                    </button>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <PaginationLinks :paginator="transactions"></PaginationLinks>

            </div>



        </div>

    </AdminLayout>
</template>
