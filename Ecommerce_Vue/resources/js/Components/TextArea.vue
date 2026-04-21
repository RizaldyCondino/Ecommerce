<script setup>
const model = defineModel();
defineProps({
    label: String,
    icon: String,
    placeholder: {
        type: String,
        default: "",
    },
    
});
</script>

<template>
    <div>
        <label
            :for="label"
            class="block mb-2.5 text-sm font-medium text-heading"
        >
            {{ label }}
        </label>

        <div class="relative mt-1 rounded-md">
            <div
                class="pointer-events-none absolute top-3 left-0 flex items-center pl-3"
            >
               
            </div>
            <textarea
                rows="6"
                :name="label"
                :placeholder="placeholder"
                v-model="model"
                class=" rounded-md bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full p-3.5 shadow-xs placeholder:text-body"
            />
        </div>
    </div>
</template>