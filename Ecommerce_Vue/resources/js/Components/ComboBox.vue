<template>
  
    <label
      :for="id"
      class="block mb-2.5 text-sm font-medium text-heading"
    >
      {{ label }}
    </label>

    <select
      :id="id"
      :name="name"
      :value="modelValue"
      @change="emit('update:modelValue', $event.target.value)"
      class="mb-2 block w-full px-3 py-2.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-md focus:ring-brand focus:border-brand shadow-xs appearance-none"
    >
      <option disabled value="">
        {{ placeholder }}
      </option>

      <option
        v-for="option in options"
        :key="option.value"
        :value="option.value"
      >
        {{ option.label }}
      </option>
    </select>
 
</template>

<script setup>
defineProps({
  modelValue: [String, Number],
  label: String,
  name: String,
  id: String,
  placeholder: {
    type: String,
    default: 'Choose an option'
  },
  options: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update:modelValue'])
</script>
