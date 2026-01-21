<script setup>
import { computed, watch, ref } from "vue";
import { router, usePage, Link } from "@inertiajs/vue3";

const props = defineProps({
    open: { type: Boolean, default: false },
    reason: { type: String, default: "" },
    plan: { type: Object, default: null }, // current tenant plan
});

const emit = defineEmits(["close"]);

const page = usePage();
const plans = computed(() => page.props.plans ?? []);

const tenant = computed(() => page.props.auth?.currentTenant ?? null);
const currentSlug = computed(
    () => tenant.value?.plan?.slug ?? props.plan?.slug ?? null
);

const selectedSlug = ref(null);

watch(
    () => props.open,
    (isOpen) => {
        if (!isOpen) return;

        // Pré-seleciona “o próximo plano” (primeiro com preço maior)
        const current = plans.value.find((p) => p.slug === currentSlug.value);
        if (!current) {
            selectedSlug.value = plans.value[0]?.slug ?? null;
            return;
        }

        const next = plans.value.find(
            (p) => Number(p.price) > Number(current.price)
        );
        selectedSlug.value =
            (next ?? current)?.slug ?? plans.value[0]?.slug ?? null;
    },
    { immediate: true }
);

const selectedPlan = computed(
    () => plans.value.find((p) => p.slug === selectedSlug.value) ?? null
);

const isSamePlan = computed(
    () => selectedSlug.value && selectedSlug.value === currentSlug.value
);

function close() {
    emit("close");
}

function upgradeNow() {
    if (!selectedSlug.value) return;

    // ✅ Stripe-ready: Checkout via backend (Cashier)
    router.post(
        route("billing.checkout", selectedSlug.value),
        {},
        { preserveScroll: true }
    );
}

function formatPrice(value) {
    if (value === null || value === undefined) return "";
    // se estiveres a guardar em cents, troca por: value/100
    const n = Number(value);
    if (Number.isNaN(n)) return String(value);
    return new Intl.NumberFormat("pt-PT", {
        style: "currency",
        currency: "EUR",
        maximumFractionDigits: 0,
    }).format(n);
}

function limitLabel(v) {
    return v === null || v === undefined ? "∞" : v;
}
</script>

<template>
    <div
        v-if="open"
        class="fixed inset-0 z-50 flex items-center justify-center"
        aria-modal="true"
        role="dialog"
    >
        <!-- backdrop -->
        <div class="absolute inset-0 bg-black/40" @click="close"></div>

        <!-- modal -->
        <div
            class="relative w-full max-w-2xl rounded-2xl bg-white shadow-xl border"
        >
            <div class="p-6 border-b flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold">Upgrade necessário</h2>
                    <p v-if="reason" class="text-sm text-gray-600 mt-1">
                        {{ reason }}
                    </p>
                </div>

                <button
                    @click="close"
                    class="text-gray-400 hover:text-gray-600 text-xl leading-none"
                    aria-label="Fechar"
                >
                    ×
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="rounded-xl border bg-gray-50 p-4">
                    <div class="text-sm text-gray-700">
                        <span class="font-medium">Plano atual:</span>
                        <span class="ms-1">
                            {{ tenant?.plan?.name ?? "—" }}
                        </span>
                    </div>
                </div>

                <!-- chooser -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label
                        v-for="p in plans"
                        :key="p.slug"
                        class="cursor-pointer rounded-xl border p-4 hover:shadow-sm transition"
                        :class="
                            selectedSlug === p.slug
                                ? 'border-black ring-1 ring-black'
                                : 'border-gray-200'
                        "
                    >
                        <input
                            class="hidden"
                            type="radio"
                            name="plan"
                            :value="p.slug"
                            v-model="selectedSlug"
                        />

                        <div class="flex items-start justify-between">
                            <div>
                                <div class="font-semibold text-gray-900">
                                    {{ p.name }}
                                </div>
                                <div class="text-sm text-gray-600 mt-1">
                                    {{ formatPrice(p.price) }} / mês
                                </div>
                            </div>

                            <span
                                v-if="p.slug === currentSlug"
                                class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-700"
                            >
                                Atual
                            </span>
                        </div>

                        <div class="mt-3 text-xs text-gray-600 space-y-1">
                            <div>
                                👥 Membros:
                                {{ limitLabel(p.limits?.max_members) }}
                            </div>
                            <div>
                                📁 Projetos:
                                {{ limitLabel(p.limits?.max_projects) }}
                            </div>
                        </div>
                    </label>
                </div>

                <div v-if="!plans.length" class="text-sm text-gray-600">
                    Não foi possível carregar planos. Pode fazer upgrade na
                    página de preços.
                </div>
            </div>

            <!-- footer -->
            <div class="p-6 border-t flex items-center justify-between gap-3">
                <Link
                    :href="route('pricing.index')"
                    class="text-sm text-gray-600 hover:underline"
                >
                    Ver detalhes na página de preços
                </Link>

                <div class="flex items-center gap-2">
                    <button
                        @click="close"
                        class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50"
                    >
                        Agora não
                    </button>

                    <button
                        @click="upgradeNow"
                        :disabled="!selectedPlan || isSamePlan"
                        class="px-4 py-2 rounded-lg bg-black text-white text-sm hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Upgrade agora
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
