<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <div class="bg-white p-8 rounded-xl shadow text-center space-y-4">
            <h1 class="text-xl font-semibold">Convite já aceite</h1>
            <p class="text-gray-600">Este convite já foi utilizado.</p>

            <a
                :href="route('dashboard')"
                class="inline-flex justify-center rounded-lg bg-black px-4 py-2 text-white"
            >
                Ir para o dashboard
            </a>
        </div>
    </div>
</template>
