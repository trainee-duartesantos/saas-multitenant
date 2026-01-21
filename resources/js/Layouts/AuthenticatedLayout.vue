<script setup>
import { ref, computed } from "vue";
import { usePage, router, Link, Head } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import { useTenantRole } from "@/composables/useTenantRole";

const showingNavigationDropdown = ref(false);

const page = usePage();
const pendingCount = page.props.pendingInvitationsCount ?? 0;
const auth = computed(() => page.props.auth ?? {});
const tenants = computed(() => auth.value.tenants ?? []);
const currentTenantId = computed(() => auth.value.currentTenantId);
const success = computed(() => page.props.flash?.success);
const error = computed(() => page.props.flash?.error);

const { isOwner } = useTenantRole();
const tenant = computed(() => auth.value.currentTenant);
const canBilling = computed(() => tenant.value?.plan?.features?.billing_access);

function switchTenant(event) {
    router.post(
        route("tenant.switch"),
        { tenant_id: event.target.value },
        { preserveScroll: true, preserveState: false }
    );
}
</script>

<template>
    <Head>
        <meta name="csrf-token" :content="$page.props.csrf_token" />
    </Head>
    <div class="max-w-7xl mx-auto px-6 pt-4 space-y-2">
        <div
            v-if="success"
            class="rounded-md bg-green-50 border border-green-200 px-4 py-3 text-green-800 text-sm"
        >
            ✅ {{ success }}
        </div>

        <div
            v-if="error"
            class="rounded-md bg-red-50 border border-red-200 px-4 py-3 text-red-800 text-sm"
        >
            ❌ {{ error }}
        </div>
    </div>

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <nav class="border-b bg-white dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-4">
                <div class="flex h-16 justify-between items-center">
                    <!-- LEFT -->
                    <div class="flex items-center space-x-6">
                        <Link :href="route('dashboard')">
                            <ApplicationLogo class="h-8 w-auto" />
                        </Link>

                        <NavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </NavLink>

                        <Link
                            :href="route('invitations.index')"
                            class="relative inline-flex items-center gap-2 px-3 py-2 rounded-md hover:bg-gray-100"
                        >
                            <span>🔔</span>
                            <span class="text-sm">Convites</span>

                            <span
                                v-if="pendingCount > 0"
                                class="absolute -top-1 -right-1 inline-flex items-center justify-center text-xs font-bold rounded-full px-2 py-0.5 bg-red-600 text-white"
                            >
                                {{ pendingCount }}
                            </span>
                        </Link>

                        <NavLink :href="route('projects.index')">
                            Projects
                        </NavLink>

                        <NavLink :href="route('pricing.index')">
                            Pricing
                        </NavLink>

                        <NavLink :href="route('billing.history')">
                            Billing history
                        </NavLink>

                        <div
                            class="flex items-center gap-2 text-sm text-gray-500"
                        >
                            <span>Tenant:</span>
                            <strong class="text-gray-900">
                                {{
                                    tenants.find(
                                        (t) => t.id === currentTenantId
                                    )?.name
                                }}
                            </strong>
                        </div>

                        <!-- TENANT SELECTOR -->
                        <div v-if="tenants.length > 1" class="ms-4">
                            <select
                                class="rounded-md border-gray-300 text-sm px-2 py-1"
                                @change="switchTenant"
                                :value="currentTenantId"
                            >
                                <option
                                    v-for="tenant in tenants"
                                    :key="tenant.id"
                                    :value="tenant.id"
                                >
                                    {{ tenant.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- RIGHT -->
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="text-sm text-gray-600">
                                {{ auth.user?.name }}
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink :href="route('profile.edit')">
                                Profile
                            </DropdownLink>

                            <DropdownLink
                                v-if="isOwner && canBilling"
                                href="/billing"
                            >
                                Billing
                            </DropdownLink>

                            <DropdownLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </div>
        </nav>

        <main>
            <slot />
        </main>
    </div>
</template>
