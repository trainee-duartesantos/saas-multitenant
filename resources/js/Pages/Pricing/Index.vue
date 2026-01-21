<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";

defineProps({
    plans: Array,
    currentPlanId: Number,
    canUpgrade: Boolean,
});

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
                            ⭐ Priority support
                        </li>
                    </ul>

                    <!-- 🔥 LÓGICA CORRETA -->
                    <button
                        v-if="plan.id > currentPlanId && canUpgrade"
                        @click="upgrade(plan)"
                        class="btn-primary"
                    >
                        Upgrade
                    </button>

                    <button
                        v-else-if="plan.id < currentPlanId && canUpgrade"
                        @click="downgrade(plan)"
                        class="btn-secondary"
                    >
                        Downgrade (next cycle)
                    </button>

                    <button v-else disabled class="btn-disabled">
                        Current plan
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
            </div>
        </div>
    </AuthenticatedLayout>
</template>
