<script setup>
defineProps({
    // depois vamos usar dados reais
});
</script>

<template>
    <div class="max-w-3xl mx-auto py-12 space-y-6">
        <h1 class="text-2xl font-bold">Bem-vindo 👋</h1>

        <p class="text-gray-600">
            Vamos configurar o seu tenant passo a passo.
        </p>

        <div class="bg-white rounded-xl shadow p-6">
            <p class="font-semibold">Passo atual:</p>
            <p class="text-sm text-gray-500">Configuração inicial do tenant</p>

            <button class="mt-4 px-4 py-2 bg-black text-white rounded" disabled>
                Continuar
            </button>
        </div>
    </div>
</template>
