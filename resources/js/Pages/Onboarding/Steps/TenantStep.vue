<script setup>
import { router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    tenant: Object,
});

const name = ref(props.tenant.name);

const submit = () => {
    router.post(route("onboarding.tenant.store"), {
        name: name.value,
    });
};
</script>

<template>
    <div class="space-y-6">
        <h1 class="text-2xl font-semibold">Vamos começar 🚀</h1>

        <p class="text-gray-600">
            Começa por confirmar o nome do teu workspace.
        </p>

        <input
            v-model="name"
            class="border rounded w-full px-3 py-2"
            placeholder="Nome do tenant"
        />

        <button @click="submit" class="bg-black text-white px-4 py-2 rounded">
            Continuar
        </button>
    </div>
</template>
