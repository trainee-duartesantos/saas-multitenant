<script setup>
defineProps({
    items: Array,
});
</script>

<template>
    <div class="rounded-xl border border-gray-200 bg-white p-5 space-y-3">
        <h3 class="font-semibold text-gray-900">Checklist de onboarding</h3>

        <ul class="space-y-2">
            <li
                v-for="item in items"
                :key="item.key"
                class="flex items-center gap-3"
            >
                <span
                    class="h-5 w-5 flex items-center justify-center rounded-full text-xs font-bold"
                    :class="
                        item.done
                            ? 'bg-green-500 text-white'
                            : 'bg-gray-200 text-gray-600'
                    "
                >
                    {{ item.done ? "✓" : "•" }}
                </span>

                <span :class="item.done ? 'text-gray-900' : 'text-gray-500'">
                    {{ item.label }}
                </span>
            </li>
        </ul>
    </div>
</template>
