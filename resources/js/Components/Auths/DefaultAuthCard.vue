<script setup lang="ts">
import { defineProps } from 'vue';
import ApplicationLogo from '../ApplicationLogo.vue';

const props = defineProps({
  title: String,
  customClasses: String,
})
</script>

<template>
  <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
    <div
      class="w-full max-w-md rounded-sm bg-white shadow-default p-4 sm:p-12.5"
      :class="customClasses"
    >
      <h2 class="text-center mb-9 text-2xl font-bold text-black dark:text-white sm:text-title-xl2">
        {{ props.title }}
      </h2>
      
      <slot></slot>
    </div>
  </div>
</template>
