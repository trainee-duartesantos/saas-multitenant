<script setup>
import { ref, watch } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    tenant: Object,
    onboarding: Object,
});

// state local seguro
const form = ref({
    name: "",
});

// quando o tenant chega, sincroniza
watch(
    () => props.tenant,
    (tenant) => {
        if (tenant) {
            form.value.name = tenant.name;
        }
    },
    { immediate: true }
);

const submit = () => {
    router.post(route("onboarding.tenant.store"), {
        name: form.value.name,
    });
};
</script>

<template>
    <div class="max-w-2xl mx-auto py-16 space-y-8">
        <h1 class="text-2xl font-semibold">Bem-vindo 👋</h1>

        <p class="text-gray-600">
            Vamos configurar o seu tenant passo a passo.
        </p>

        <!-- PASSO 1 -->
        <div
            v-if="onboarding && onboarding.current_step === 'tenant'"
            class="bg-white rounded-xl shadow p-6 space-y-4"
        >
            <h2 class="font-medium text-lg">Configuração inicial do tenant</h2>

            <div>
                <label class="block text-sm font-medium mb-1">
                    Nome do tenant
                </label>

                <input
                    v-model="form.name"
                    type="text"
                    class="w-full border rounded px-3 py-2"
                />
            </div>

            <button
                @click="submit"
                class="bg-black text-white px-4 py-2 rounded"
            >
                Guardar e continuar
            </button>
        </div>

        <!-- PASSO SEGUINTE -->
        <div v-else class="text-gray-500">
            Próximo passo: convidar membros 👥
        </div>
    </div>
</template>
