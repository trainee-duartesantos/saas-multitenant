<script setup>
import { inject } from "vue";

const props = defineProps({
    featureName: {
        type: String,
        required: true,
    },
    requiredPlan: {
        type: String,
        default: "Pro",
    },
});

const requireUpgrade = inject("requireUpgrade");

const upgrade = () => {
    requireUpgrade(
        `Esta funcionalidade está disponível no plano ${props.requiredPlan}.`
    );
};
</script>

<template>
    <span
        class="relative inline-flex items-center gap-1 text-gray-400 cursor-pointer group"
        @click="upgrade"
    >
        🔒
        <span class="text-xs underline">Bloqueado</span>

        <!-- Tooltip -->
        <span
            class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 whitespace-nowrap rounded bg-black px-2 py-1 text-xs text-white opacity-0 group-hover:opacity-100 transition pointer-events-none"
        >
            Disponível no plano {{ requiredPlan }}
        </span>
    </span>
</template>
