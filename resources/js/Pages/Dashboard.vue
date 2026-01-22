<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed, inject } from "vue";
import OnboardingChecklist from "@/Components/OnboardingChecklist.vue";

const page = usePage();
const requireUpgrade = inject("requireUpgrade");

/**
 * Tenant
 */
const tenant = computed(() => page.props.auth?.currentTenant ?? null);
const plan = computed(() => tenant.value?.plan ?? null);
const usage = computed(() => tenant.value?.usage ?? {});
const limits = computed(() => plan.value?.limits ?? {});

/**
 * Helpers
 */
const percent = (used, max) => {
    if (!max) return 0;
    return Math.min(100, Math.round((used / max) * 100));
};

/**
 * Members
 */
const membersUsed = computed(() => usage.value.members ?? 0);
const membersMax = computed(() => limits.value.max_members ?? null);
const membersPercent = computed(() =>
    percent(membersUsed.value, membersMax.value)
);

/**
 * Projects
 */
const projectsUsed = computed(() => usage.value.projects ?? 0);
const projectsMax = computed(() => limits.value.max_projects ?? null);
const projectsPercent = computed(() =>
    percent(projectsUsed.value, projectsMax.value)
);

/**
 * States
 */
const isMembersNear = computed(
    () => membersMax.value && membersPercent.value >= 80
);
const isProjectsNear = computed(
    () => projectsMax.value && projectsPercent.value >= 80
);

const isMembersExceeded = computed(
    () => membersMax.value && membersUsed.value >= membersMax.value
);
const isProjectsExceeded = computed(
    () => projectsMax.value && projectsUsed.value >= projectsMax.value
);

/**
 * Onboarding
 */
const checklist = computed(() => page.props.onboardingChecklist ?? []);
const latestProjects = computed(() => page.props.latestProjects ?? []);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto px-6 py-8 space-y-8">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="h-10 w-10 rounded-full bg-blue-200 flex items-center justify-center font-bold text-gray-700"
                    >
                        {{ tenant?.name?.[0] }}
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold">Dashboard</h1>
                        <p class="text-sm text-gray-500">
                            {{ tenant?.name }}
                        </p>
                    </div>
                </div>

                <button
                    v-if="isMembersNear || isProjectsNear"
                    @click="
                        requireUpgrade(
                            'O seu plano está quase cheio. Faça upgrade para evitar bloqueios.'
                        )
                    "
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-black text-white text-sm hover:bg-gray-800"
                >
                    🚀 Fazer upgrade do plano
                </button>
            </div>

            <!-- PLAN CARD -->
            <div class="bg-white rounded-xl border p-6">
                <h2 class="text-lg font-semibold mb-2">Plano atual</h2>

                <p class="text-sm text-gray-700">
                    Plano:
                    <strong>{{ plan?.name ?? "—" }}</strong>
                </p>

                <p class="text-xs text-gray-500 mt-1">
                    Tenant: {{ tenant?.name }}
                </p>
            </div>

            <!-- USAGE CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- MEMBERS -->
                <div class="bg-white rounded-xl border p-6 space-y-3">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold flex items-center gap-2">
                            👥 Membros
                        </h3>

                        <span
                            v-if="isMembersExceeded"
                            class="text-xs font-medium text-red-700 bg-red-100 px-2 py-0.5 rounded-full"
                        >
                            🚫 Limite atingido
                        </span>

                        <span
                            v-else-if="isMembersNear"
                            class="text-xs font-medium text-yellow-800 bg-yellow-100 px-2 py-0.5 rounded-full"
                        >
                            ⚠️ Quase cheio
                        </span>
                    </div>

                    <div class="flex justify-between text-sm">
                        <span>Utilização</span>
                        <span>
                            {{ membersUsed }} /
                            {{ membersMax ?? "∞" }}
                        </span>
                    </div>

                    <div class="h-2 rounded bg-gray-200 overflow-hidden">
                        <div
                            class="h-full transition-all"
                            :class="
                                isMembersExceeded
                                    ? 'bg-red-600'
                                    : isMembersNear
                                    ? 'bg-yellow-500'
                                    : 'bg-blue-600'
                            "
                            :style="{ width: membersPercent + '%' }"
                        />
                    </div>

                    <Link
                        href="/members"
                        class="inline-block text-sm text-blue-600 hover:underline"
                    >
                        👥 Gerir membros
                    </Link>
                </div>

                <!-- PROJECTS -->
                <div class="bg-white rounded-xl border p-6 space-y-3">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold flex items-center gap-2">
                            📁 Projetos
                        </h3>

                        <span
                            v-if="isProjectsExceeded"
                            class="text-xs font-medium text-red-700 bg-red-100 px-2 py-0.5 rounded-full"
                        >
                            🚫 Limite atingido
                        </span>

                        <span
                            v-else-if="isProjectsNear"
                            class="text-xs font-medium text-yellow-800 bg-yellow-100 px-2 py-0.5 rounded-full"
                        >
                            ⚠️ Quase cheio
                        </span>
                    </div>

                    <div class="flex justify-between text-sm">
                        <span>Utilização</span>
                        <span>
                            {{ projectsUsed }} /
                            {{ projectsMax ?? "∞" }}
                        </span>
                    </div>

                    <div class="h-2 rounded bg-gray-200 overflow-hidden">
                        <div
                            class="h-full transition-all"
                            :class="
                                isProjectsExceeded
                                    ? 'bg-red-600'
                                    : isProjectsNear
                                    ? 'bg-yellow-500'
                                    : 'bg-green-600'
                            "
                            :style="{ width: projectsPercent + '%' }"
                        />
                    </div>

                    <Link
                        href="/projects"
                        class="inline-block text-sm text-blue-600 hover:underline"
                    >
                        📁 Ver projetos
                    </Link>
                </div>
            </div>

            <!-- RECENT PROJECTS -->
            <div class="bg-white rounded-xl border p-6 space-y-4">
                <h3 class="font-semibold flex items-center gap-2">
                    📊 Projetos recentes
                </h3>

                <p v-if="!latestProjects.length" class="text-sm text-gray-500">
                    Ainda não existem projetos.
                </p>

                <ul v-else class="space-y-2">
                    <li
                        v-for="project in latestProjects"
                        :key="project.id"
                        class="flex items-center justify-between text-sm"
                    >
                        <div class="flex flex-col">
                            <span class="font-medium truncate">
                                {{ project.name }}
                            </span>
                            <span class="text-xs text-gray-400">
                                Criado em {{ project.created_at }}
                            </span>
                        </div>

                        <Link
                            :href="route('projects.show', project.id)"
                            class="text-blue-600 hover:underline text-xs"
                        >
                            Abrir projeto
                        </Link>
                    </li>
                </ul>

                <Link
                    href="/projects"
                    class="inline-block text-sm text-gray-600 hover:underline"
                >
                    📁 Ver todos os projetos
                </Link>
            </div>

            <!-- ONBOARDING -->
            <OnboardingChecklist v-if="checklist.length" :items="checklist" />
        </div>
    </AuthenticatedLayout>
</template>
