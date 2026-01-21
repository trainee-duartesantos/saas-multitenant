<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, Link, usePage } from "@inertiajs/vue3";
import { inject, computed } from "vue";
import { usePlanFeature } from "@/composables/usePlanFeature";
import FeatureLock from "@/Components/FeatureLock.vue";

const props = defineProps({
    projects: Array,
    canCreateProject: Boolean,
});

/**
 * Modal global de upgrade
 */
const requireUpgrade = inject("requireUpgrade");

/**
 * Tenant (para mostrar limites)
 */
const page = usePage();
const tenant = computed(() => page.props.auth.currentTenant);

const usage = computed(() => tenant.value?.usage ?? {});
const limits = computed(() => tenant.value?.plan?.limits ?? {});

/**
 * Criar projeto
 */
function createProject() {
    if (!props.canCreateProject) {
        requireUpgrade(
            "O seu plano atual atingiu o limite de projetos. Faça upgrade para criar mais projetos."
        );
        return;
    }

    router.post(route("projects.store"), {
        name: "Novo Projeto",
    });
}
</script>

<template>
    <Head title="Projects" />

    <AuthenticatedLayout>
        <div class="p-6 space-y-6 max-w-6xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold">Projetos</h1>
                    <p class="text-sm text-gray-500">
                        Gere os projetos do seu tenant
                    </p>
                </div>

                <button
                    v-if="canCreateProject"
                    @click="createProject"
                    class="inline-flex items-center gap-2 rounded-md bg-black px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 transition"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4"
                        />
                    </svg>

                    Novo projeto
                </button>

                <FeatureLock
                    v-else
                    feature-name="projects_access"
                    required-plan="Pro"
                />
            </div>

            <!-- Aviso de limite -->
            <div
                v-if="!canCreateProject"
                class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700"
            >
                🚫 Atingiu o limite de projetos do seu plano
                <span v-if="limits.max_projects">
                    ({{ usage.projects }} / {{ limits.max_projects }})
                </span>
                —
                <Link
                    :href="route('pricing.index')"
                    class="underline font-medium"
                >
                    Fazer upgrade
                </Link>
            </div>

            <!-- Empty state -->
            <div
                v-if="projects.length === 0"
                class="bg-white rounded-xl border p-10 text-center space-y-3"
            >
                <p class="text-gray-600">Ainda não tem projetos criados.</p>

                <button
                    @click="createProject"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-black text-white text-sm hover:bg-gray-800"
                >
                    ➕ Criar primeiro projeto
                </button>
            </div>

            <!-- Grid de projetos -->
            <div
                v-else
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
            >
                <div
                    v-for="project in projects"
                    :key="project.id"
                    class="bg-white rounded-xl border p-5 hover:shadow transition"
                >
                    <h2 class="font-semibold text-gray-900">
                        {{ project.name }}
                    </h2>

                    <p class="text-xs text-gray-500 mt-1">
                        Projeto ID #{{ project.id }}
                    </p>

                    <div class="mt-4">
                        <Link
                            :href="route('projects.show', project.id)"
                            class="text-sm text-blue-600 hover:underline"
                        >
                            Abrir projeto →
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
