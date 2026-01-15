<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { useTenantRole } from "@/composables/useTenantRole";

const { isOwner } = useTenantRole();
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="text-xl font-bold">Dashboard</h1>

            <!-- Só owners -->
            <div v-if="isOwner" class="mt-4 p-4 bg-green-100 rounded">
                🔐 Área exclusiva para Owners
            </div>
        </div>
    </AuthenticatedLayout>
</template>
