<script setup>
import { ref } from "vue";
import { router, Link } from "@inertiajs/vue3";

import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";

const showingNavigationDropdown = ref(false);

function switchTenant(event) {
    router.post(
        route("tenant.switch"),
        { tenant_id: event.target.value },
        { preserveScroll: true }
    );
}
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <nav
            class="border-b border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <!-- Left -->
                    <div class="flex">
                        <div class="flex shrink-0 items-center">
                            <Link :href="route('dashboard')">
                                <ApplicationLogo
                                    class="h-9 w-auto text-gray-800 dark:text-gray-200"
                                />
                            </Link>
                        </div>

                        <div
                            class="hidden sm:ms-10 sm:flex sm:items-center sm:space-x-6"
                        >
                            <NavLink
                                :href="route('dashboard')"
                                :active="route().current('dashboard')"
                            >
                                Dashboard
                            </NavLink>

                            <!-- Tenant Selector -->
                            <select
                                v-if="$page.props.auth.tenants?.length > 1"
                                :value="$page.props.auth.currentTenantId"
                                @change="switchTenant"
                                class="rounded-md border-gray-300 text-sm dark:bg-gray-700 dark:text-gray-200"
                            >
                                <option
                                    v-for="tenant in $page.props.auth.tenants"
                                    :key="tenant.id"
                                    :value="tenant.id"
                                >
                                    {{ tenant.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Right -->
                    <div class="hidden sm:flex sm:items-center">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button
                                    class="flex items-center text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ $page.props.auth.user.name }}
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')">
                                    Profile
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

                    <!-- Mobile -->
                    <div class="-me-2 flex items-center sm:hidden">
                        <button
                            @click="
                                showingNavigationDropdown =
                                    !showingNavigationDropdown
                            "
                        >
                            ☰
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <slot />
        </main>
    </div>
</template>
