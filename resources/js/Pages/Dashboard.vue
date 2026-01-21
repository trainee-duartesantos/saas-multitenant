<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage, Link } from "@inertiajs/vue3";
import { computed } from "vue";
import { useTenantRole } from "@/composables/useTenantRole";
import OnboardingChecklist from "@/Components/OnboardingChecklist.vue";

const page = usePage();
const tenant = computed(() => page.props.auth.currentTenant ?? {});

const usage = computed(() => tenant.value.usage ?? { members: 0, projects: 0 });
const limits = computed(() => tenant.value.plan?.limits ?? {});
const plan = computed(() => tenant.value.plan ?? {});
const pendingInvites = computed(() => page.props.pendingInvitationsCount ?? 0);

const percent = (used, max) => {
    if (!max) return 0;
    return Math.min(100, Math.round((used / max) * 100));
};

const memberPercent = computed(() =>
    percent(usage.value.members, limits.value.max_members)
);

const projectPercent = computed(() =>
    percent(usage.value.projects, limits.value.max_projects)
);

const nearingLimit = (value) => value >= 80 && value < 100;
const exceededLimit = (value) => value >= 100;

const checklist = computed(() => page.props.onboardingChecklist);
const { isOwner } = useTenantRole();
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="p-6 space-y-8">
            <!-- HEADER -->
            <div>
                <h1 class="text-2xl font-bold">Dashboard</h1>
                <p class="text-sm text-gray-500">
                    Visão geral do teu workspace
                </p>
            </div>

            <!-- KPI CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Members -->
                <div class="bg-white rounded-xl border p-4">
                    <p class="text-xs text-gray-500">Membros</p>
                    <p class="text-2xl font-semibold">
                        {{ usage.members }}
                    </p>
                    <p class="text-xs text-gray-400">
                        Limite: {{ limits.max_members ?? "∞" }}
                    </p>
                </div>

                <!-- Projects -->
                <div class="bg-white rounded-xl border p-4">
                    <p class="text-xs text-gray-500">Projetos</p>
                    <p class="text-2xl font-semibold">
                        {{ usage.projects }}
                    </p>
                    <p class="text-xs text-gray-400">
                        Limite: {{ limits.max_projects ?? "∞" }}
                    </p>
                </div>

                <!-- Plan -->
                <div class="bg-white rounded-xl border p-4">
                    <p class="text-xs text-gray-500">Plano</p>
                    <p class="text-2xl font-semibold">
                        {{ plan.name ?? "—" }}
                    </p>
                    <Link
                        v-if="isOwner"
                        :href="route('pricing.index')"
                        class="text-xs text-blue-600 hover:underline"
                    >
                        Gerir plano →
                    </Link>
                </div>

                <!-- Invites -->
                <div class="bg-white rounded-xl border p-4">
                    <p class="text-xs text-gray-500">Convites pendentes</p>
                    <p class="text-2xl font-semibold">
                        {{ pendingInvites }}
                    </p>
                    <Link
                        :href="route('tenant.members.index')"
                        class="text-xs text-blue-600 hover:underline"
                    >
                        Ver membros →
                    </Link>
                </div>
            </div>

            <!-- USAGE -->
            <div class="bg-white rounded-xl border p-6 space-y-6">
                <h2 class="text-lg font-semibold">Utilização do plano</h2>

                <!-- Members -->
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>👥 Membros</span>
                        <span>
                            {{ usage.members }} /
                            {{ limits.max_members ?? "∞" }}
                        </span>
                    </div>

                    <div class="h-2 rounded bg-gray-200 overflow-hidden">
                        <div
                            class="h-full transition-all"
                            :class="{
                                'bg-green-600': memberPercent < 80,
                                'bg-yellow-500': nearingLimit(memberPercent),
                                'bg-red-600': exceededLimit(memberPercent),
                            }"
                            :style="{ width: memberPercent + '%' }"
                        />
                    </div>

                    <p
                        v-if="nearingLimit(memberPercent)"
                        class="text-xs text-yellow-600 mt-1"
                    >
                        ⚠️ Estás perto do limite de membros
                    </p>

                    <p
                        v-if="exceededLimit(memberPercent)"
                        class="text-xs text-red-600 mt-1"
                    >
                        🚫 Limite de membros atingido
                    </p>
                </div>

                <!-- Projects -->
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>📁 Projetos</span>
                        <span>
                            {{ usage.projects }} /
                            {{ limits.max_projects ?? "∞" }}
                        </span>
                    </div>

                    <div class="h-2 rounded bg-gray-200 overflow-hidden">
                        <div
                            class="h-full transition-all"
                            :class="{
                                'bg-green-600': projectPercent < 80,
                                'bg-yellow-500': nearingLimit(projectPercent),
                                'bg-red-600': exceededLimit(projectPercent),
                            }"
                            :style="{ width: projectPercent + '%' }"
                        />
                    </div>

                    <p
                        v-if="nearingLimit(projectPercent)"
                        class="text-xs text-yellow-600 mt-1"
                    >
                        ⚠️ Estás perto do limite de projetos
                    </p>

                    <p
                        v-if="exceededLimit(projectPercent)"
                        class="text-xs text-red-600 mt-1"
                    >
                        🚫 Limite de projetos atingido
                    </p>
                </div>

                <!-- CTA UPGRADE -->
                <div
                    v-if="
                        isOwner &&
                        (nearingLimit(memberPercent) ||
                            nearingLimit(projectPercent))
                    "
                    class="rounded-lg border border-yellow-300 bg-yellow-50 p-4 text-sm"
                >
                    <p class="font-medium text-yellow-800">
                        🚀 Estás a aproximar-te do limite do plano
                    </p>
                    <p class="text-yellow-700">
                        Faz upgrade para desbloquear mais recursos.
                    </p>

                    <Link
                        :href="route('pricing.index')"
                        class="inline-block mt-2 rounded-md bg-black px-4 py-2 text-white text-xs hover:bg-gray-800"
                    >
                        Ver planos
                    </Link>
                </div>
            </div>

            <!-- ONBOARDING -->
            <OnboardingChecklist v-if="checklist" :items="checklist" />

            <!-- OWNER AREA -->
            <div
                v-if="isOwner"
                class="rounded-xl bg-green-100 border border-green-200 p-4"
            >
                🔐 Área exclusiva para Owners
            </div>
        </div>
    </AuthenticatedLayout>
</template>
