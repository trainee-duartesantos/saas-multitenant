<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, Link } from "@inertiajs/vue3";

defineProps({
    tenant: Object,
    subscription: Object,
    pendingPlan: Object,
});

/**
 * Cancelar downgrade agendado
 */
function cancelDowngrade() {
    router.post(route("billing.downgrade.cancel"));
}

/**
 * Cancelar subscrição (fim do ciclo)
 */
function cancelSubscription() {
    if (confirm("Tem a certeza que pretende cancelar a subscrição?")) {
        router.post(route("billing.cancel"));
    }
}

/**
 * Reativar durante grace period
 */
function resumeSubscription() {
    router.post(route("billing.resume"));
}
</script>

<template>
    <Head title="Billing" />

    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto p-6 space-y-6">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold">Billing</h1>
                <p class="text-sm text-gray-500">
                    Gerir subscrição e faturação do tenant
                </p>
            </div>

            <!-- 🔻 Downgrade agendado -->
            <div
                v-if="pendingPlan"
                class="rounded-lg border border-orange-200 bg-orange-50 p-4"
            >
                <p class="font-medium text-orange-900">🔻 Downgrade agendado</p>

                <p class="text-sm text-orange-700 mt-1">
                    O plano será alterado para
                    <strong>{{ pendingPlan.name }}</strong>
                    no próximo ciclo de faturação.
                </p>

                <button
                    class="mt-2 text-sm underline text-orange-900"
                    @click="cancelDowngrade"
                >
                    Cancelar downgrade
                </button>
            </div>

            <!-- ❌ Cancelada (grace period) -->
            <div
                v-if="subscription?.on_grace_period"
                class="rounded-lg border border-red-200 bg-red-50 p-4"
            >
                <p class="font-medium text-red-900">❌ Subscrição cancelada</p>

                <p class="text-sm text-red-700 mt-1">
                    O acesso permanece ativo até
                    <strong>{{ subscription.ends_at }}</strong>
                </p>

                <button
                    class="mt-2 text-sm underline text-red-900"
                    @click="resumeSubscription"
                >
                    Reativar subscrição
                </button>
            </div>

            <!-- Plano atual -->
            <div class="rounded-xl border bg-white p-5 space-y-2">
                <p class="text-sm text-gray-500">Plano atual</p>

                <p class="text-lg font-semibold">
                    {{ tenant.plan.name }}
                </p>

                <p class="text-sm text-gray-600">Tenant: {{ tenant.name }}</p>

                <div class="pt-3 flex gap-3">
                    <Link :href="route('pricing.index')" class="btn-primary">
                        Fazer upgrade
                    </Link>

                    <button
                        v-if="subscription && !subscription.on_grace_period"
                        @click="cancelSubscription"
                        class="text-sm text-red-600 underline"
                    >
                        Cancelar subscrição
                    </button>
                </div>
            </div>

            <!-- Histórico -->
            <div class="text-sm">
                <Link :href="route('billing.history')" class="underline">
                    Ver histórico de faturação →
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
