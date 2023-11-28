<script setup>
import { ref } from 'vue';
import { FwbTab, FwbTabs } from 'flowbite-vue'

defineProps({
    name: String,
    title: String,
});

const activeTab = ref('first')

</script>

<template>
    <fwb-tabs v-model="activeTab" variant="pills" class="p-5">
        <fwb-tab :name="name" :title="title" >

        </fwb-tab>
    </fwb-tabs>
</template>
