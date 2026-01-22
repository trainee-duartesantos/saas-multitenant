<script setup>
defineProps({
    project: Object,
});
</script>

<template>
    <div class="max-w-5xl mx-auto p-6">
        <h1 class="text-2xl font-bold">{{ project.name }}</h1>

        <p class="text-sm text-gray-500 mt-2">Projeto ID: {{ project.id }}</p>
    </div>
</template>
