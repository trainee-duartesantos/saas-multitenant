<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { useTenantRole } from "@/composables/useTenantRole";

defineProps({
    projects: Array,
});

const { isOwner } = useTenantRole();

function createProject() {
    router.post(route("projects.store"), {
        name: "Novo Projeto",
    });
}
</script>

<template>
    <Head title="Projects" />

    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="text-xl font-bold mb-4">Projects</h1>

            <!-- Botão só para owner -->
            <button
                v-if="isOwner"
                @click="createProject"
                class="mb-4 px-4 py-2 bg-blue-600 text-white rounded"
            >
                ➕ Criar Projeto
            </button>

            <ul>
                <li v-for="project in projects" :key="project.id">
                    {{ project.name }}
                </li>
            </ul>
        </div>
    </AuthenticatedLayout>
</template>
