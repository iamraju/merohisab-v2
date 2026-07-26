<script setup>
import { computed, onMounted, ref } from "vue";

const deferredPrompt = ref(null);
const visible = ref(false);

const isStandalone = computed(() => {
    if (typeof window === "undefined") {
        return false;
    }

    return (
        window.matchMedia("(display-mode: standalone)").matches ||
        window.navigator.standalone === true
    );
});

const install = async () => {
    if (!deferredPrompt.value) {
        return;
    }

    deferredPrompt.value.prompt();
    await deferredPrompt.value.userChoice;
    deferredPrompt.value = null;
    visible.value = false;
};

const dismiss = () => {
    visible.value = false;
};

onMounted(() => {
    if (isStandalone.value) {
        return;
    }

    window.addEventListener("beforeinstallprompt", (event) => {
        event.preventDefault();
        deferredPrompt.value = event;
        visible.value = true;
    });

    window.addEventListener("appinstalled", () => {
        deferredPrompt.value = null;
        visible.value = false;
    });
});
</script>

<template>
    <transition
        enter-active-class="duration-300 ease-out"
        enter-from-class="translate-y-3 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="duration-200 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-3 opacity-0"
    >
        <aside
            v-if="visible"
            class="fixed inset-x-4 bottom-4 z-50 rounded-2xl border border-teal-200 bg-white/95 p-4 shadow-xl backdrop-blur sm:inset-x-auto sm:right-6 sm:w-[24rem]"
            aria-live="polite"
        >
            <p class="text-sm font-semibold text-slate-900">
                Install MeroHisab
            </p>
            <p class="mt-1 text-sm text-slate-600">
                Add MeroHisab to your home screen for faster access and offline
                support.
            </p>
            <div class="mt-3 flex items-center gap-2">
                <button
                    type="button"
                    class="rounded-lg bg-teal-700 px-3 py-2 text-sm font-semibold text-white hover:bg-teal-800"
                    @click="install"
                >
                    Install
                </button>
                <button
                    type="button"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                    @click="dismiss"
                >
                    Not now
                </button>
            </div>
        </aside>
    </transition>
</template>
