<script setup>
import { router } from "@inertiajs/vue3";

defineProps({
    open: Boolean,
    reason: String,
    plan: Object,
});

const emit = defineEmits(["close"]);

const goToPricing = () => {
    emit("close");
    router.visit(route("pricing.index"));
};
</script>

<template>
    <div
        v-if="open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
    >
        <div
            class="w-full max-w-md bg-white rounded-xl shadow-lg p-6 space-y-5"
        >
            <h2 class="text-xl font-semibold">🚀 Upgrade necessário</h2>

            <p class="text-gray-600">
                {{ reason }}
            </p>

            <div class="rounded-lg bg-gray-50 p-4 text-sm">
                <p>
                    Plano atual:
                    <strong>{{ plan?.name }}</strong>
                </p>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button
                    @click="emit('close')"
                    class="px-4 py-2 text-sm rounded-md border"
                >
                    Agora não
                </button>

                <button
                    @click="goToPricing"
                    class="px-4 py-2 text-sm rounded-md bg-black text-white hover:bg-gray-800"
                >
                    Ver planos
                </button>
            </div>
        </div>
    </div>
</template>
