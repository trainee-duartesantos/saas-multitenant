import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

export function useTenantRole() {
    const page = usePage();

    const role = computed(() => page.props.auth.currentTenantRole);

    const isOwner = computed(() => role.value === "owner");
    const isMember = computed(() => role.value === "member");

    return {
        role,
        isOwner,
        isMember,
    };
}
