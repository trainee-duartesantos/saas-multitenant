import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

export function usePlanFeature(feature) {
    const page = usePage();

    const tenant = computed(() => page.props.auth.currentTenant);
    const plan = computed(() => tenant.value?.plan);

    const enabled = computed(() => {
        if (!plan.value) return false;
        return Boolean(plan.value.features?.[feature]);
    });

    return {
        enabled,
        plan,
    };
}
