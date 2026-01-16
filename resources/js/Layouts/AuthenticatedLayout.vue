<script setup>
import { ref, computed } from "vue";
import { usePage, router, Link } from "@inertiajs/vue3";

import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";

import { useTenantRole } from "@/composables/useTenantRole";
import { Head } from "@inertiajs/vue3";

const showingNavigationDropdown = ref(false);

const page = usePage();

const auth = computed(() => page.props.auth ?? {});
const tenants = computed(() => auth.value.tenants ?? []);
const currentTenantId = computed(() => auth.value.currentTenantId);

const { isOwner } = useTenantRole();

function switchTenant(event) {
    router.post(
        route("tenant.switch"),
        { tenant_id: event.target.value },
        { preserveScroll: true }
    );
}
</script>

<template>
    <Head>
        <meta name="csrf-token" :content="$page.props.csrf_token" />
    </Head>
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

                        <NavLink :href="route('projects.index')">
                            Projects
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

                            <DropdownLink v-if="isOwner" href="/billing">
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
