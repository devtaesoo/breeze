<script setup>
import { FwbButton } from 'flowbite-vue'

defineProps({
    title: String,
    color: String
});
</script>
<template>
   <div class="flex m-4">
        <fwb-button class="flex-1" size="md" :color="color">{{ title }}</fwb-button>
   </div>
</template>
