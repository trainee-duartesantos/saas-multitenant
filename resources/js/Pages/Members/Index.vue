<script setup>
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();

const tenant = computed(() => page.props.auth.currentTenant);

const maxMembers = computed(() => tenant.value?.plan?.limits?.max_members);
const usedMembers = computed(() => tenant.value?.usage?.members ?? 0);

const canInvite = computed(() => {
    if (maxMembers.value === null) return true;
    return usedMembers.value < maxMembers.value;
});

const props = defineProps({
    members: Array,
    invitations: Array,
    currentUserId: Number,
    currentUserRole: String,
    onboarding: Object, // 👈 importante
});

/**
 * Onboarding — passo "members"
 */
const isOnboardingMembers =
    props.onboarding &&
    props.onboarding.current_step === "members" &&
    !props.onboarding.completed;

/**
 * Permissões
 */
const canManage = ["owner", "admin"].includes(props.currentUserRole);

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

const inviteEmail = ref("");
const inviteRole = ref("member");

const sendInvite = () => {
    if (!canInvite.value) return;

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
    <div class="max-w-4xl mx-auto py-10 space-y-10">
        <h1 class="text-2xl font-semibold">Membros</h1>

        <!-- ➕ Convidar membro -->
        <div v-if="canManage" class="bg-white rounded-xl shadow p-4 space-y-4">
            <h2 class="font-semibold">Convidar membro</h2>

            <div class="flex gap-3">
                <input
                    v-model="inviteEmail"
                    type="email"
                    placeholder="email@exemplo.com"
                    class="border rounded px-3 py-2 text-sm w-full"
                />

                <select
                    v-model="inviteRole"
                    class="border rounded px-2 py-2 text-sm"
                >
                    <option value="member">Member</option>
                    <option value="admin">Admin</option>
                </select>

                <button
                    @click="sendInvite"
                    :disabled="!canInvite"
                    class="px-4 py-2 rounded text-sm text-white"
                    :class="
                        canInvite
                            ? 'bg-blue-600 hover:bg-blue-700'
                            : 'bg-gray-400 cursor-not-allowed'
                    "
                >
                    Convidar
                </button>
            </div>
            <p v-if="!canInvite" class="text-sm text-red-600 mt-2">
                🚫 Limite de membros do plano atingido
                <span v-if="maxMembers !== null">
                    ({{ usedMembers }}/{{ maxMembers }})
                </span>
            </p>
        </div>

        <!-- 🟢 Onboarding Callout -->
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

        <!-- Lista de membros -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-3 text-left">Nome</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th v-if="canManage"></th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="member in members"
                        :key="member.id"
                        class="border-b last:border-0"
                    >
                        <td class="p-3">{{ member.name }}</td>
                        <td>{{ member.email }}</td>

                        <td>
                            <span
                                v-if="!canManage || member.id === currentUserId"
                            >
                                {{ member.role }}
                            </span>

                            <select
                                v-else
                                :value="member.role"
                                @change="
                                    updateRole(member.id, $event.target.value)
                                "
                                class="border rounded px-2 py-1 text-sm"
                            >
                                <option value="member">member</option>
                                <option value="admin">admin</option>
                                <option value="owner">owner</option>
                            </select>
                        </td>

                        <td v-if="canManage" class="space-x-3">
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
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Convites pendentes -->
        <div>
            <h2 class="font-semibold mb-2">Convites pendentes</h2>

            <p v-if="!invitations.length" class="text-sm text-gray-500">
                Sem convites pendentes.
            </p>

            <ul v-else class="space-y-2">
                <li
                    v-for="invite in invitations"
                    :key="invite.id"
                    class="flex items-center justify-between bg-gray-50 rounded px-4 py-2 text-sm"
                >
                    <div>
                        <strong>{{ invite.email }}</strong>
                        <span class="text-gray-500"> — {{ invite.role }} </span>
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
</template>
