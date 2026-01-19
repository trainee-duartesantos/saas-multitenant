<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, Link } from "@inertiajs/vue3";

defineProps({
    projects: Array,
    canCreateProject: Boolean,
});

function createProject() {
    router.post(route("projects.store"), {
        name: "Novo Projeto",
    });
}
</script>

<template>
    <Head title="Projects" />

    <AuthenticatedLayout>
        <div class="p-6 space-y-4">
            <h1 class="text-xl font-bold">Projects</h1>

            <!-- Criar projeto -->
            <button
                v-if="canCreateProject"
                @click="createProject"
                class="btn-primary"
            >
                Criar projeto
            </button>

            <p v-else class="text-sm text-red-600 mt-2">
                Atingiu o limite de projetos do seu plano.
                <Link href="/billing" class="underline"> Fazer upgrade </Link>
            </p>

            <!-- Lista -->
            <ul class="list-disc ml-6">
                <li v-for="project in projects" :key="project.id">
                    {{ project.name }}
                </li>
            </ul>
        </div>
    </AuthenticatedLayout>
</template>
