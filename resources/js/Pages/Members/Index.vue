<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, Link, usePage } from "@inertiajs/vue3";
import { ref, computed, inject } from "vue";
import FeatureLock from "@/Components/FeatureLock.vue";
import { usePlanFeature } from "@/composables/usePlanFeature";

/**
 * Page + tenant
 */
const page = usePage();
const tenant = computed(() => page.props.auth.currentTenant);

/**
 * Plano / limites
 */
const usage = computed(() => tenant.value?.usage ?? {});
const limits = computed(() => tenant.value?.plan?.limits ?? {});
const maxMembers = computed(() => limits.value.max_members);
const usedMembers = computed(() => usage.value.members ?? 0);

const canInvite = computed(() => {
    if (maxMembers.value === null) return true;
    return usedMembers.value < maxMembers.value;
});

/**
 * Features do plano
 */
const { enabled: advancedPermissions } = usePlanFeature("advanced_permissions");

/**
 * Upgrade modal global
 */
const requireUpgrade = inject("requireUpgrade");

/**
 * Props
 */
const props = defineProps({
    members: Array,
    invitations: Array,
    currentUserId: Number,
    currentUserRole: String,
    onboarding: Object,
});

/**
 * Permissões
 */
const canManage = ["owner", "admin"].includes(props.currentUserRole);

/**
 * Onboarding
 */
const isOnboardingMembers =
    props.onboarding &&
    props.onboarding.current_step === "members" &&
    !props.onboarding.completed;

/**
 * Ações
 */
const updateRole = (userId, role) => {
    router.patch(route("tenant.members.update", userId), { role });
};

const removeMember = (userId) => {
    if (confirm("Remover este membro?")) {
        router.delete(route("tenant.members.destroy", userId));
    }
};

const resendInvite = (id) => {
    router.post(route("tenant.invitations.resend", id));
};

const cancelInvite = (id) => {
    if (confirm("Cancelar este convite?")) {
        router.delete(route("tenant.invitations.destroy", id));
    }
};

const transferOwnership = (userId) => {
    if (confirm("Tem a certeza? Irá deixar de ser owner e passar a admin.")) {
        router.post(route("tenant.members.transferOwnership", userId));
    }
};

/**
 * Convite
 */
const inviteEmail = ref("");
const inviteRole = ref("member");

const sendInvite = () => {
    if (!canInvite.value) {
        requireUpgrade(
            "O seu plano atual atingiu o limite de membros. Faça upgrade para convidar mais pessoas."
        );
        return;
    }

    router.post(
        route("tenant.invitations.store"),
        {
            email: inviteEmail.value,
            role: inviteRole.value,
        },
        {
            onSuccess: () => {
                inviteEmail.value = "";
                inviteRole.value = "member";
            },
        }
    );
};
</script>

<template>
    <Head title="Membros" />

    <AuthenticatedLayout>
        <div class="max-w-6xl mx-auto p-6 space-y-8">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">Membros</h1>
                    <p class="text-sm text-gray-500">
                        Gerir a equipa do seu tenant
                    </p>
                </div>
            </div>

            <!-- Onboarding -->
            <div
                v-if="isOnboardingMembers"
                class="rounded-lg border border-blue-200 bg-blue-50 p-4"
            >
                <p class="font-medium text-blue-900">
                    👥 Convida pelo menos um membro para continuar
                </p>
                <p class="text-sm text-blue-700">
                    O seu tenant precisa de mais alguém para ficar operacional.
                </p>
            </div>

            <!-- Convidar membro -->
            <div
                v-if="canManage"
                class="bg-white rounded-xl border p-4 space-y-2"
            >
                <div class="flex items-center gap-3">
                    <input
                        v-model="inviteEmail"
                        type="email"
                        placeholder="email@exemplo.com"
                        class="flex-1 border rounded px-3 py-2 text-sm"
                    />

                    <select
                        v-if="advancedPermissions"
                        v-model="inviteRole"
                        class="border rounded px-2 py-2 text-sm"
                    >
                        <option value="member">Member</option>
                        <option value="admin">Admin</option>
                    </select>

                    <FeatureLock
                        v-else
                        feature-name="advanced_permissions"
                        required-plan="Enterprise"
                    />

                    <button
                        @click="sendInvite"
                        class="px-4 py-2 rounded-md text-sm text-white"
                        :class="
                            canInvite
                                ? 'bg-blue-600 hover:bg-blue-700'
                                : 'bg-gray-400 cursor-not-allowed'
                        "
                    >
                        Convidar
                    </button>
                </div>

                <p
                    v-if="!canInvite"
                    class="text-xs text-red-600 flex items-center gap-1"
                >
                    🚫 Limite de membros atingido
                    <span v-if="maxMembers !== null">
                        ({{ usedMembers }}/{{ maxMembers }})
                    </span>
                    —
                    <Link
                        :href="route('pricing.index')"
                        class="underline font-medium"
                    >
                        Fazer upgrade
                    </Link>
                </p>
            </div>

            <!-- Membros -->
            <div class="bg-white rounded-xl border overflow-hidden">
                <h2 class="px-4 py-3 font-medium border-b">Membros ativos</h2>

                <table class="w-full text-sm table-fixed">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-3 text-left w-1/4">Nome</th>
                            <th class="w-1/3">Email</th>
                            <th class="w-1/6 text-center">Role</th>
                            <th>Ação</th>
                            <th v-if="canManage"></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="member in members"
                            :key="member.id"
                            class="border-b last:border-0"
                        >
                            <td class="p-3 truncate">
                                {{ member.name }}
                            </td>
                            <td class="truncate">{{ member.email }}</td>

                            <td class="text-center">
                                <span
                                    class="inline-flex items-center px-2 py-1 text-xs rounded-full"
                                    :class="{
                                        'bg-gray-100 text-gray-700':
                                            member.role === 'member',
                                        'bg-blue-100 text-blue-700':
                                            member.role === 'admin',
                                        'bg-purple-100 text-purple-700':
                                            member.role === 'owner',
                                    }"
                                >
                                    {{ member.role }}
                                </span>
                            </td>

                            <td class="text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-3">
                                    <button
                                        v-if="
                                            currentUserRole === 'owner' &&
                                            member.role !== 'owner'
                                        "
                                        @click="transferOwnership(member.id)"
                                        class="text-orange-600 hover:underline text-sm"
                                    >
                                        Tornar owner
                                    </button>

                                    <button
                                        v-if="
                                            member.id !== currentUserId &&
                                            member.role !== 'owner'
                                        "
                                        @click="removeMember(member.id)"
                                        class="text-red-600 hover:underline text-sm"
                                    >
                                        Remover
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Convites pendentes -->
            <div class="bg-white rounded-xl border p-4">
                <h2 class="font-medium mb-3">Convites pendentes</h2>

                <p v-if="!invitations.length" class="text-sm text-gray-500">
                    Sem convites pendentes.
                </p>

                <ul v-else class="space-y-2">
                    <li
                        v-for="invite in invitations"
                        :key="invite.id"
                        class="flex items-center justify-between rounded-md bg-gray-50 px-4 py-2 text-sm"
                    >
                        <div>
                            <strong>{{ invite.email }}</strong>
                            <span class="text-gray-500">
                                — {{ invite.role }}
                            </span>
                        </div>

                        <div v-if="canManage" class="flex gap-3">
                            <button
                                @click="resendInvite(invite.id)"
                                class="text-blue-600 hover:underline"
                            >
                                Reenviar
                            </button>

                            <button
                                @click="cancelInvite(invite.id)"
                                class="text-red-600 hover:underline"
                            >
                                Cancelar
                            </button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
