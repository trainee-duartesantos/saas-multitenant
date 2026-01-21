import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h, ref, provide } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

import UpgradeModal from "@/Components/UpgradeModal.vue";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,

    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),

    setup({ el, App, props, plugin }) {
        const showUpgradeModal = ref(false);
        const upgradeReason = ref(null);

        function requireUpgrade(reason) {
            upgradeReason.value = reason;
            showUpgradeModal.value = true;
        }

        const vueApp = createApp({
            setup() {
                // 🌍 PROVIDE GLOBAL
                provide("requireUpgrade", requireUpgrade);

                return () =>
                    h("div", [
                        h(App, props),

                        // 🔔 Modal global (fora dos layouts!)
                        h(UpgradeModal, {
                            open: showUpgradeModal.value,
                            reason: upgradeReason.value,
                            onClose: () => (showUpgradeModal.value = false),
                        }),
                    ]);
            },
        });

        vueApp.use(plugin);
        vueApp.use(ZiggyVue);
        vueApp.mount(el);
    },

    progress: {
        color: "#4B5563",
    },
});
