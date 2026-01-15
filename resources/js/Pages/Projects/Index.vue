<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
    projects: Array,
});
</script>

<template>
    <Head title="Projects" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                Projects
            </h2>
        </template>

        <div class="p-6 space-y-4">
            <ul>
                <li v-for="project in projects" :key="project.id">
                    {{ project.name }}
                </li>
            </ul>
        </div>
    </AuthenticatedLayout>
</template>
