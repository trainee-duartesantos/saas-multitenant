<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
    invitations: Array,
});
</script>

<template>
    <Head title="Convites pendentes" />

    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto p-6 space-y-6">
            <h1 class="text-2xl font-bold">Convites pendentes</h1>

            <p v-if="!invitations.length" class="text-gray-500">
                Não tens convites pendentes.
            </p>

            <div v-else class="space-y-3">
                <div
                    v-for="inv in invitations"
                    :key="inv.id"
                    class="flex items-center justify-between border rounded-xl bg-white p-4"
                >
                    <div>
                        <div class="font-semibold">
                            {{ inv.tenant.name }}
                        </div>
                        <div class="text-sm text-gray-600">
                            Role: {{ inv.role }}
                        </div>
                        <div class="text-xs text-gray-400">
                            {{ inv.created_at }}
                        </div>
                    </div>

                    <a
                        :href="`/invitations/${inv.token}`"
                        class="rounded-lg bg-black px-4 py-2 text-white hover:bg-gray-800"
                    >
                        Ver / Aceitar
                    </a>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
