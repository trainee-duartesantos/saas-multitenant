<script setup>
import { router } from "@inertiajs/vue3";

defineProps({
    invitation: Object,
    authenticated: Boolean,
});

const accept = () => {
    router.post(
        route("tenant.invitations.accept", route().params.token),
        {},
        {
            onSuccess: () => {
                router.visit(route("dashboard"), {
                    replace: true,
                });
            },
        }
    );
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <div class="w-full max-w-md bg-white rounded-xl shadow p-8 space-y-6">
            <h1 class="text-2xl font-semibold text-gray-900">
                Convite para {{ invitation.tenant.name }}
            </h1>

            <p class="text-gray-600">
                <strong>{{ invitation.email }}</strong> foi convidado para se
                juntar como <strong>{{ invitation.role }}</strong
                >.
            </p>

            <div v-if="!authenticated" class="space-y-4">
                <p class="text-sm text-gray-500">
                    Para aceitar este convite, faz login na tua conta.
                </p>

                <a
                    :href="route('login')"
                    class="inline-flex w-full justify-center rounded-lg bg-black px-4 py-2 text-white hover:bg-gray-800"
                >
                    Fazer login
                </a>
            </div>

            <button
                v-else
                @click="accept"
                class="w-full rounded-lg bg-black px-4 py-2 text-white hover:bg-gray-800"
            >
                Aceitar convite
            </button>
        </div>
    </div>
</template>
