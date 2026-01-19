<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";

defineProps({
    plans: Array,
    currentPlanId: Number,
});

function upgrade(plan) {
    if (!confirm(`Upgrade para o plano ${plan.name}?`)) return;

    router.post(route("pricing.upgrade", plan.slug));
}
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

                    <button
                        v-if="plan.id !== currentPlanId"
                        @click="upgrade(plan)"
                        class="w-full bg-black text-white py-2 rounded hover:bg-gray-800"
                    >
                        Upgrade
                    </button>

                    <span
                        v-else
                        class="block text-center text-sm text-green-600 font-medium"
                    >
                        Plano atual
                    </span>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
