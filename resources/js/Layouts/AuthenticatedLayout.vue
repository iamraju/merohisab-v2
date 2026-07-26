<script setup>
import { computed, onMounted, onUnmounted, ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import PwaInstallPrompt from "@/Components/PwaInstallPrompt.vue";
import { Link, usePage } from "@inertiajs/vue3";

const page = usePage();
const sidebarOpen = ref(false);
const sidebarCollapsed = ref(false);
const userMenu = ref(false);
const userMenuRoot = ref(null);

const menuItems = computed(() => {
    const overview = [
        {
            key: "dashboard",
            label: "Dashboard",
            href: route("dashboard"),
            active: route().current("dashboard"),
            icon: "grid",
        },
    ];

    const hisab = [
        {
            key: "titles",
            label: "Titles",
            href: route("titles.index"),
            active: route().current("titles.*"),
            icon: "list",
        },
    ];

    if (page.props.auth.user.role === "customer") {
        hisab.push(
            {
                key: "transactions",
                label: "Add Entry",
                href: route("transactions.create"),
                active: route().current("transactions.*"),
                icon: "plus",
            },
            {
                key: "reports",
                label: "Reports",
                href: route("reports.index"),
                active: route().current("reports.*"),
                icon: "chart",
            },
        );
    }

    if (page.props.auth.user.role === "super_admin") {
        hisab.push({
            key: "customers",
            label: "Customers",
            href: route("admin.customers.index"),
            active: route().current("admin.customers.*"),
            icon: "users",
        });
    }

    const account = [
        {
            key: "profile",
            label: "Settings",
            href: route("profile.edit"),
            active: route().current("profile.*"),
            icon: "settings",
        },
    ];

    return { overview, hisab, account };
});

const sectionLabel = computed(() =>
    route().current("dashboard") ? "Overview" : "Hisab",
);

const pageTitle = computed(() => {
    if (route().current("dashboard")) return "Dashboard";
    if (route().current("titles.*")) return "Titles";
    if (route().current("transactions.*")) return "Add Transaction";
    if (route().current("reports.*")) return "Reports";
    if (route().current("admin.customers.*")) return "Customers";

    return "Workspace";
});

const initials = computed(() =>
    (page.props.auth.user.name || "U")
        .split(" ")
        .slice(0, 2)
        .map((part) => part.charAt(0).toUpperCase())
        .join(""),
);

const linkClasses = (active) =>
    active
        ? "flex items-center gap-3 rounded-lg bg-white/10 px-3 py-2.5 text-sm font-medium text-white"
        : "flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-300 transition-colors hover:bg-white/5 hover:text-white";

const toggleSidebar = () => {
    if (window.innerWidth >= 1024) {
        sidebarCollapsed.value = !sidebarCollapsed.value;
        return;
    }

    sidebarOpen.value = !sidebarOpen.value;
};

const closeUserMenuOnOutsideClick = (event) => {
    if (userMenuRoot.value && !userMenuRoot.value.contains(event.target)) {
        userMenu.value = false;
    }
};

const closeUserMenuOnEscape = (event) => {
    if (event.key === "Escape") {
        userMenu.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", closeUserMenuOnOutsideClick);
    document.addEventListener("keydown", closeUserMenuOnEscape);
});

onUnmounted(() => {
    document.removeEventListener("click", closeUserMenuOnOutsideClick);
    document.removeEventListener("keydown", closeUserMenuOnEscape);
});
</script>

<template>
    <div
        class="flex h-screen overflow-hidden bg-paper font-sans text-slate-800 antialiased"
    >
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-30 flex shrink-0 transform flex-col bg-ink text-slate-300 transition-all duration-200 ease-in-out lg:static lg:translate-x-0"
            :style="{ width: sidebarCollapsed ? '5rem' : '16rem' }"
        >
            <div
                class="flex h-16 items-center gap-2 border-b border-white/10 px-5"
            >
                <div
                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-accent/40 bg-accent/20"
                >
                    <span class="font-display text-sm font-bold text-accent"
                        >मे</span
                    >
                </div>
                <div v-if="!sidebarCollapsed" class="leading-tight">
                    <p
                        class="font-display text-sm font-bold tracking-wide text-white"
                    >
                        MeroHisab
                    </p>
                    <p class="text-[11px] text-slate-400">Family Ledger</p>
                </div>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                <p
                    v-if="!sidebarCollapsed"
                    class="mb-2 px-3 text-[11px] uppercase tracking-wider text-slate-500"
                >
                    Overview
                </p>
                <Link
                    v-for="item in menuItems.overview"
                    :key="item.key"
                    :href="item.href"
                    :class="linkClasses(item.active)"
                    @click="sidebarOpen = false"
                    :title="sidebarCollapsed ? item.label : ''"
                >
                    <svg
                        v-if="item.icon === 'grid'"
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <rect x="3" y="3" width="7" height="9" rx="1.5" />
                        <rect x="14" y="3" width="7" height="5" rx="1.5" />
                        <rect x="14" y="12" width="7" height="9" rx="1.5" />
                        <rect x="3" y="16" width="7" height="5" rx="1.5" />
                    </svg>

                    <svg
                        v-else-if="item.icon === 'list'"
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M4 6h16M4 12h16M4 18h7" />
                    </svg>

                    <svg
                        v-else-if="item.icon === 'plus'"
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M12 5v14M5 12h14" />
                    </svg>

                    <svg
                        v-else-if="item.icon === 'chart'"
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M3 3v18h18" />
                        <path d="M7 15l4-5 3 3 4-6" />
                    </svg>

                    <svg
                        v-else
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 00-3-3.87" />
                        <path d="M16 3.13a4 4 0 010 7.75" />
                    </svg>

                    <span v-if="!sidebarCollapsed">{{ item.label }}</span>
                </Link>

                <p
                    v-if="!sidebarCollapsed"
                    class="mb-2 mt-5 px-3 text-[11px] uppercase tracking-wider text-slate-500"
                >
                    Hisab
                </p>
                <Link
                    v-for="item in menuItems.hisab"
                    :key="item.key"
                    :href="item.href"
                    :class="linkClasses(item.active)"
                    @click="sidebarOpen = false"
                    :title="sidebarCollapsed ? item.label : ''"
                >
                    <svg
                        v-if="item.icon === 'plus'"
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    <svg
                        v-else-if="item.icon === 'list'"
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                    <svg
                        v-else-if="item.icon === 'chart'"
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M3 3v18h18" />
                        <path d="M7 15l4-5 3 3 4-6" />
                    </svg>
                    <svg
                        v-else
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 00-3-3.87" />
                        <path d="M16 3.13a4 4 0 010 7.75" />
                    </svg>
                    <span v-if="!sidebarCollapsed">{{ item.label }}</span>
                </Link>

                <p
                    v-if="!sidebarCollapsed"
                    class="mb-2 mt-5 px-3 text-[11px] uppercase tracking-wider text-slate-500"
                >
                    Account
                </p>
                <Link
                    v-for="item in menuItems.account"
                    :key="item.key"
                    :href="item.href"
                    :class="linkClasses(item.active)"
                    :title="sidebarCollapsed ? item.label : ''"
                >
                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="12" cy="12" r="3" />
                        <path
                            d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09a1.65 1.65 0 00-1-1.51 1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09a1.65 1.65 0 001.51-1 1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z"
                        />
                    </svg>
                    <span v-if="!sidebarCollapsed">{{ item.label }}</span>
                </Link>
            </nav>

            <div class="border-t border-white/10 p-3">
                <div
                    class="flex items-center gap-3 rounded-lg px-2 py-2 hover:bg-white/5"
                >
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-accent/30 text-sm font-semibold text-white"
                    >
                        {{ initials }}
                    </div>
                    <div v-if="!sidebarCollapsed" class="min-w-0 flex-1">
                        <p class="truncate text-sm text-white">
                            {{ page.props.auth.user.name }}
                        </p>
                        <p class="truncate text-xs text-slate-400">
                            {{
                                page.props.auth.user.role === "super_admin"
                                    ? "Super Admin"
                                    : "Customer"
                            }}
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    class="mt-2 w-full rounded-lg px-2 py-2 text-left text-xs text-slate-400 hover:bg-white/5 hover:text-white"
                    @click="toggleSidebar"
                >
                    {{ sidebarCollapsed ? "Expand Sidebar" : "Toggle Sidebar" }}
                </button>
            </div>
        </aside>

        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-20 bg-black/40 lg:hidden"
            @click="sidebarOpen = false"
        />

        <div class="flex min-w-0 flex-1 flex-col">
            <header
                class="flex h-16 shrink-0 items-center gap-4 border-b border-slate-200 bg-white px-4 lg:px-8"
            >
                <button class="text-slate-500 lg:hidden" @click="toggleSidebar">
                    <svg
                        width="22"
                        height="22"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="hidden lg:block">
                    <p class="text-xs text-slate-400">{{ sectionLabel }}</p>
                    <h1 class="-mt-0.5 font-display text-lg font-bold text-ink">
                        {{ pageTitle }}
                    </h1>
                </div>

                <div class="ml-auto hidden max-w-md flex-1 md:block">
                    <div class="relative">
                        <svg
                            class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle cx="11" cy="11" r="7" />
                            <path d="M21 21l-4.3-4.3" />
                        </svg>
                        <input
                            type="text"
                            placeholder="Search transactions, titles..."
                            class="w-full rounded-lg border border-slate-200 bg-paper py-2 pl-9 pr-3 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/40"
                        />
                    </div>
                </div>

                <button class="relative text-slate-500 hover:text-ink">
                    <svg
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9" />
                        <path d="M13.7 21a2 2 0 01-3.4 0" />
                    </svg>
                    <span
                        class="absolute -right-1 -top-1 h-2 w-2 rounded-full bg-expense"
                    />
                </button>

                <div ref="userMenuRoot" class="relative">
                    <button
                        class="flex items-center gap-2"
                        @click="userMenu = !userMenu"
                    >
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-ink text-xs font-semibold text-white"
                        >
                            {{ initials }}
                        </div>
                        <svg
                            class="hidden h-4 w-4 text-slate-400 sm:block"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M6 9l6 6 6-6" />
                        </svg>
                    </button>

                    <div
                        v-if="userMenu"
                        class="absolute right-0 z-40 mt-2 w-44 rounded-lg border border-slate-200 bg-white py-1 text-sm shadow-lg"
                    >
                        <Link
                            :href="route('profile.edit')"
                            class="block px-3 py-2 text-slate-700 hover:bg-paper"
                        >
                            Profile
                        </Link>
                        <Link
                            :href="`${route('profile.edit')}#update-password`"
                            class="block px-3 py-2 text-slate-700 hover:bg-paper"
                        >
                            Change password
                        </Link>
                        <div class="my-1 border-t border-slate-100" />
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="block w-full px-3 py-2 text-left text-expense hover:bg-paper"
                        >
                            Log out
                        </Link>
                    </div>
                </div>
            </header>

            <main class="flex-1 space-y-6 overflow-y-auto p-4 lg:p-8">
                <div v-if="$slots.header" class="mb-6">
                    <slot name="header" />
                </div>
                <slot />
            </main>
        </div>

        <PwaInstallPrompt />
    </div>
</template>
