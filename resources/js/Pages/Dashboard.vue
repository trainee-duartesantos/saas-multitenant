<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { useTenantRole } from "@/composables/useTenantRole";
import { usePage } from "@inertiajs/vue3";
import OnboardingChecklist from "@/Components/OnboardingChecklist.vue";

const checklist = usePage().props.onboardingChecklist;

const { isOwner } = useTenantRole();
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="text-xl font-bold">Dashboard</h1>

            <OnboardingChecklist v-if="checklist" :items="checklist" />

            <!-- Só owners -->
            <div v-if="isOwner" class="mt-4 p-4 bg-green-100 rounded">
                🔐 Área exclusiva para Owners
            </div>
        </div>
    </AuthenticatedLayout>
</template>
