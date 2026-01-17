<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { useTenantRole } from "@/composables/useTenantRole";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import OnboardingChecklist from "@/Components/OnboardingChecklist.vue";

const page = usePage();

const plan = computed(() => page.props.plan);
const trial = computed(() => page.props.trial);

const checklist = usePage().props.onboardingChecklist;

const { isOwner } = useTenantRole();
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="text-xl font-bold">Dashboard</h1>

            <div class="mt-6 bg-white rounded-xl p-5 border">
                <h2 class="font-semibold text-lg mb-2">Plano</h2>

                <p class="text-sm text-gray-700">
                    Plano atual:
                    <strong>{{ plan.name }}</strong>
                </p>

                <p v-if="trial.active" class="text-sm text-green-600 mt-1">
                    Trial ativo até {{ trial.ends_at }}
                </p>

                <p v-else class="text-sm text-gray-500 mt-1">Trial terminado</p>
            </div>

            <OnboardingChecklist v-if="checklist" :items="checklist" />

            <!-- Só owners -->
            <div v-if="isOwner" class="mt-4 p-4 bg-green-100 rounded">
                🔐 Área exclusiva para Owners
            </div>
        </div>
    </AuthenticatedLayout>
</template>
