<script setup lang="ts">

const emit = defineEmits(['update:modelValue'])

const props = defineProps({
  name: String,
  label: String,
  error: String,
  type: {
    type: String,
    default: "text",
  },
  modelValue: [String,Number],
  validator: Function,
});

const handleInput = (event) => {
  emit("update:modelValue", event.target.value);
};
</script>

<template>
  <div class="mb-4">
    <label class="mb-2.5 block font-medium text-black dark:text-white">{{ props.label }}</label>
    <div class="relative">
      <input
        v-bind="$attrs"
        :type="type"
        :id="name"
        :name="name"
        :value="modelValue"
        @input="handleInput"
        :class="{ 'is-invalid': error }"
        class="w-full rounded-lg border border-stroke bg-transparent py-4 pl-6 pr-10 outline-none focus:border-primary focus-visible:shadow-none dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary text-black dark:text-white"
      />

      <div v-if="error" class="text-red-400">{{ error }}</div>

      <span class="absolute right-4 top-4">
        <slot></slot>
      </span>
    </div>
  </div>
</template>
