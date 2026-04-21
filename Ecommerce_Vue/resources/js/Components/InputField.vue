<script setup>
const model = defineModel();

defineProps({
    label: String,      
    error: String,     
    placeholder: {
        type: String,
        default: "", 
    },
    type: {
        type: String,
        default: "text",
    },
});
</script>

<template>
  <div class="relative z-0 w-full mb-5 group">
    
    <!-- INPUT -->
    <input
      :type="type"
      :name="label"
      :placeholder="placeholder"
      v-model="model"
      :class="[
        'peer block py-2.5 px-0 w-full text-sm bg-transparent border-0 border-b-2 appearance-none focus:outline-none focus:ring-0',
        error
          ? 'border-red-500 focus:border-red-500'
          : 'border-default-medium text-heading focus:border-brand'
      ]"
    />

    <!-- LABEL -->
    <label
      :for="label"
      class="absolute text-sm font-medium text-body duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0]
             peer-focus:start-0 peer-focus:text-fg-brand
             peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0
             peer-focus:scale-75 peer-focus:-translate-y-6"
    >
      {{ label }}
    </label>

    <!-- ERROR MESSAGE -->
    <p v-if="error" class="text-red-500 text-xs mt-1">
      {{ error }}
    </p>
  </div>
</template>
