<script setup>
import { router } from "@inertiajs/vue3";

const props = defineProps({
    members: Array,
    invitations: Array,
    currentUserId: Number,
    currentUserRole: String,
});

const canManage = ["owner", "admin"].includes(props.currentUserRole);

const updateRole = (userId, role) => {
    router.patch(route("tenant.members.update", userId), { role });
};

const removeMember = (userId) => {
    if (confirm("Remover este membro?")) {
        router.delete(route("tenant.members.destroy", userId));
    }
};
</script>

<template>
    <div class="max-w-4xl mx-auto py-10 space-y-10">
        <h1 class="text-2xl font-semibold">Membros</h1>

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

                        <td v-if="canManage">
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

            <ul v-else class="text-sm text-gray-600 space-y-1">
                <li v-for="invite in invitations" :key="invite.id">
                    {{ invite.email }} — {{ invite.role }}
                </li>
            </ul>
        </div>
    </div>
</template>
