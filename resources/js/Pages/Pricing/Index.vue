<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref } from "vue";

defineProps({
    plans: Array,
    currentPlanId: Number,
    canUpgrade: Boolean,
});
const showRules = ref(false);

const upgrade = (plan) => {
    router.post(route("billing.checkout", plan.id));
};

const downgrade = (plan) => {
    if (!confirm("Downgrade will apply next billing cycle. Continue?")) return;

    router.post(route("billing.downgrade", plan.id));
};
</script>

<template>
    <Head title="Pricing" />

    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto p-6 space-y-6">
            <h1 class="text-2xl font-bold">Planos</h1>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    v-for="plan in plans"
                    :key="plan.id"
                    class="border rounded-xl p-5 bg-white space-y-4"
                >
                    <h2 class="text-xl font-semibold">{{ plan.name }}</h2>

                    <p class="text-2xl font-bold">
                        {{
                            plan.price === 0
                                ? "Grátis"
                                : `€${plan.price / 100}/mês`
                        }}
                    </p>

                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>
                            👥 {{ plan.max_members ?? "Ilimitado" }} membros
                        </li>
                        <li>
                            📁 {{ plan.max_projects ?? "Ilimitado" }} projetos
                        </li>
                        <li v-if="plan.has_priority_support">
                            ⭐ Suporte prioritário
                        </li>
                    </ul>

                    <!-- 🔥 LÓGICA CORRETA -->
                    <button
                        v-if="plan.id > currentPlanId && canUpgrade"
                        @click="upgrade(plan)"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-black text-white text-sm hover:bg-gray-800"
                    >
                        Upgrade
                    </button>

                    <button
                        v-else-if="plan.id < currentPlanId && canUpgrade"
                        @click="downgrade(plan)"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gray-700 text-white text-sm hover:bg-red-800"
                    >
                        Downgrade (próximo ciclo)
                    </button>

                    <button v-else disabled class="btn-disabled">
                        Plano atual
                    </button>

                    <span
                        v-if="plan.id === currentPlanId"
                        class="block text-center text-sm text-green-600 font-medium"
                    >
                        Plano atual
                    </span>

                    <span
                        v-if="!canUpgrade"
                        class="block text-center text-sm text-gray-400"
                    >
                        Apenas o owner pode alterar o plano
                    </span>
                </div>
                <button
                    class="text-xs text-gray-500 underline mt-2"
                    @click="showRules = true"
                >
                    Regras de cancelamento
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
    <div
        v-if="showRules"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-xl p-6 max-w-md w-full space-y-4">
            <h2 class="text-lg font-semibold">
                Regras de faturação e cancelamento
            </h2>

            <ul class="text-sm text-gray-600 space-y-2">
                <li>• Cancelamentos aplicam-se no fim do ciclo atual</li>
                <li>• Não existe reembolso proporcional</li>
                <li>• Downgrades entram em vigor no próximo ciclo</li>
                <li>• O acesso mantém-se até ao final do período pago</li>
            </ul>

            <div class="text-right">
                <button
                    @click="showRules = false"
                    class="px-4 py-2 rounded-md bg-black text-white text-sm"
                >
                    Fechar
                </button>
            </div>
        </div>
    </div>
</template>
