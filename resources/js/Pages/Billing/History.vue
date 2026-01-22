<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
    logs: Array,
});

function formatAction(action) {
    const map = {
        subscription_created: "Subscription created",
        plan_swapped: "Plan changed",
        plan_upgraded: "Plan upgraded",
        plan_downgraded: "Plan downgraded",
        subscription_canceled: "Subscription canceled",
    };
    return map[action] ?? action;
}
</script>

<template>
    <Head title="Billing History" />

    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto p-6 space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Histórico de Faturação</h1>
            </div>

            <div v-if="!logs.length" class="text-gray-500">
                Ainda não há eventos de faturamento.
            </div>

            <div v-else class="bg-white border rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="text-left p-3">Data</th>
                            <th class="text-left p-3">Ação</th>
                            <th class="text-left p-3">Plano</th>
                            <th class="text-left p-3">Operador</th>
                            <th class="text-left p-3">Subscrição Stripe</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="log in logs" :key="log.id" class="border-t">
                            <td class="p-3 whitespace-nowrap">
                                {{ log.created_at }}
                            </td>
                            <td class="p-3 font-medium">
                                {{ formatAction(log.action) }}
                            </td>
                            <td class="p-3">
                                <span v-if="log.plan">{{ log.plan.name }}</span>
                                <span v-else class="text-gray-400">—</span>
                            </td>
                            <td class="p-3">
                                <span v-if="log.actor">{{
                                    log.actor.email
                                }}</span>
                                <span v-else class="text-gray-400"
                                    >Stripe webhook</span
                                >
                            </td>
                            <td class="p-3">
                                <code class="text-xs">{{
                                    log.stripe_subscription_id ?? "—"
                                }}</code>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="text-xs text-gray-400">
                Apenas os eventos do Tenant atualmente ativo são exibidos.
            </p>
        </div>
    </AuthenticatedLayout>
</template>
