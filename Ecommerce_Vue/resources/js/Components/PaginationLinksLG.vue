<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  paginator: Object
})

const makeLabel = label =>
  label.includes("Previous") ? "<" :
  label.includes("Next") ? ">" :
  label
</script>

<template>
  <div class="flex justify-between items-center p-6">
    <p class="text-gray-900 dark:text-slate-400 text-sm mt-1">
      Showing {{ paginator.from }} to {{ paginator.to }} of {{ paginator.total }} results
    </p>

    <div class="flex items-center rounded-lg overflow-hidden shadow-md">
      <div v-for="(link, i) in paginator.links" :key="i">
        <component
          :is="link.url ? Link : 'span'"
          :href="link.url"
          v-html="makeLabel(link.label)"
          class="border-x border-slate-200 dark:border-slate-700
                 w-10 h-10
                 md:w-11 md:h-11
                 grid place-items-center
                 text-sm md:text-base
                 bg-white dark:bg-slate-900"
          :class="{
            'hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer': link.url,
            'text-slate-300': !link.url,
            'font-bold text-indigo-600 dark:text-indigo-400': link.active
          }"
        />
      </div>
    </div>
  </div>
</template>
