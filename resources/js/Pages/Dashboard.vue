<script setup>
import { nextTick, onMounted, onUnmounted, ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import Chart from "chart.js/auto";

defineProps({
    role: {
        type: String,
        required: true,
    },
    customerSummary: {
        type: Object,
        default: null,
    },
    adminSummary: {
        type: Object,
        default: null,
    },
});

const trendCanvas = ref(null);
const breakdownCanvas = ref(null);
let trendChart = null;
let breakdownChart = null;

const renderCharts = () => {
    if (!trendCanvas.value || !breakdownCanvas.value) {
        return;
    }

    trendChart = new Chart(trendCanvas.value, {
        type: "line",
        data: {
            labels: ["Feb", "Mar", "Apr", "May", "Jun", "Jul"],
            datasets: [
                {
                    label: "Income",
                    data: [98000, 110000, 102000, 125000, 138000, 145200],
                    borderColor: "#059669",
                    backgroundColor: "rgba(5,150,105,0.08)",
                    tension: 0.35,
                    fill: true,
                    pointRadius: 0,
                    borderWidth: 2,
                },
                {
                    label: "Expense",
                    data: [72000, 80000, 75000, 88000, 90000, 92450],
                    borderColor: "#E11D48",
                    backgroundColor: "rgba(225,29,72,0.06)",
                    tension: 0.35,
                    fill: true,
                    pointRadius: 0,
                    borderWidth: 2,
                },
            ],
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        boxWidth: 8,
                        usePointStyle: true,
                        font: { family: "Inter", size: 11 },
                    },
                },
            },
            scales: {
                y: {
                    ticks: { font: { family: "Inter", size: 10 } },
                    grid: { color: "#F1F5F9" },
                },
                x: {
                    ticks: { font: { family: "Inter", size: 10 } },
                    grid: { display: false },
                },
            },
        },
    });

    breakdownChart = new Chart(breakdownCanvas.value, {
        type: "doughnut",
        data: {
            labels: ["Groceries", "Fees", "Travel", "Fruits/Milk", "Other"],
            datasets: [
                {
                    data: [32, 24, 14, 12, 18],
                    backgroundColor: [
                        "#E11D48",
                        "#F97316",
                        "#F59E0B",
                        "#FB7185",
                        "#CBD5E1",
                    ],
                    borderWidth: 0,
                },
            ],
        },
        options: {
            maintainAspectRatio: false,
            cutout: "68%",
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        boxWidth: 8,
                        usePointStyle: true,
                        font: { family: "Inter", size: 10 },
                    },
                },
            },
        },
    });
};

onMounted(async () => {
    await nextTick();
    renderCharts();
});

onUnmounted(() => {
    if (trendChart) {
        trendChart.destroy();
        trendChart = null;
    }

    if (breakdownChart) {
        breakdownChart.destroy();
        breakdownChart = null;
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-2xl font-bold leading-tight text-ink">
                Dashboard
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Overview of your family ledger activity.
            </p>
        </template>

        <div class="space-y-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div
                    v-if="role === 'customer'"
                    class="rounded-xl border border-slate-200 border-l-4 border-l-income bg-white p-5"
                >
                    <p class="text-xs text-slate-500">Total Income</p>
                    <p
                        class="mt-1 font-display text-2xl font-bold text-ink tabular"
                    >
                        {{ customerSummary?.total_income ?? "0.00" }}
                    </p>
                </div>
                <div
                    v-if="role === 'customer'"
                    class="rounded-xl border border-slate-200 border-l-4 border-l-expense bg-white p-5"
                >
                    <p class="text-xs text-slate-500">Total Expense</p>
                    <p
                        class="mt-1 font-display text-2xl font-bold text-ink tabular"
                    >
                        {{ customerSummary?.total_expense ?? "0.00" }}
                    </p>
                </div>
                <div
                    v-if="role === 'customer'"
                    class="rounded-xl border border-slate-200 border-l-4 border-l-accent bg-white p-5"
                >
                    <p class="text-xs text-slate-500">Net Balance</p>
                    <p
                        class="mt-1 font-display text-2xl font-bold text-ink tabular"
                    >
                        {{
                            (
                                Number(customerSummary?.total_income ?? 0) -
                                Number(customerSummary?.total_expense ?? 0)
                            ).toFixed(2)
                        }}
                    </p>
                </div>
                <div
                    v-if="role === 'customer'"
                    class="rounded-xl border border-slate-200 border-l-4 border-l-slate-300 bg-white p-5"
                >
                    <p class="text-xs text-slate-500">Savings Rate</p>
                    <p
                        class="mt-1 font-display text-2xl font-bold text-ink tabular"
                    >
                        {{
                            Number(customerSummary?.total_income ?? 0) > 0
                                ? Math.round(
                                      ((Number(
                                          customerSummary?.total_income ?? 0,
                                      ) -
                                          Number(
                                              customerSummary?.total_expense ??
                                                  0,
                                          )) /
                                          Number(
                                              customerSummary?.total_income ??
                                                  0,
                                          )) *
                                          100,
                                  )
                                : 0
                        }}%
                    </p>
                </div>

                <div
                    v-if="role === 'super_admin'"
                    class="rounded-xl border border-slate-200 border-l-4 border-l-accent bg-white p-5"
                >
                    <p class="text-xs text-slate-500">Total Customers</p>
                    <p
                        class="mt-1 font-display text-2xl font-bold text-ink tabular"
                    >
                        {{ adminSummary?.total_customers ?? 0 }}
                    </p>
                </div>
                <div
                    v-if="role === 'super_admin'"
                    class="rounded-xl border border-slate-200 border-l-4 border-l-income bg-white p-5"
                >
                    <p class="text-xs text-slate-500">Active Customers</p>
                    <p
                        class="mt-1 font-display text-2xl font-bold text-ink tabular"
                    >
                        {{ adminSummary?.active_customers ?? 0 }}
                    </p>
                </div>
                <div
                    v-if="role === 'super_admin'"
                    class="rounded-xl border border-slate-200 border-l-4 border-l-slate-300 bg-white p-5"
                >
                    <p class="text-xs text-slate-500">Total Titles</p>
                    <p
                        class="mt-1 font-display text-2xl font-bold text-ink tabular"
                    >
                        {{ adminSummary?.total_titles ?? 0 }}
                    </p>
                </div>
                <div
                    v-if="role === 'super_admin'"
                    class="rounded-xl border border-slate-200 border-l-4 border-l-indigo-400 bg-white p-5"
                >
                    <p class="text-xs text-slate-500">
                        Customer-Created Titles
                    </p>
                    <p
                        class="mt-1 font-display text-2xl font-bold text-ink tabular"
                    >
                        {{ adminSummary?.customer_created_titles ?? 0 }}
                    </p>
                </div>
            </div>

            <div
                v-if="role === 'customer'"
                class="grid grid-cols-1 gap-4 xl:grid-cols-3"
            >
                <section
                    class="rounded-xl border border-slate-200 bg-white p-5 xl:col-span-2"
                >
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="font-display text-sm font-bold text-ink">
                            Income vs Expense - last 6 months
                        </h3>
                    </div>
                    <div class="h-[220px]">
                        <canvas ref="trendCanvas" />
                    </div>
                </section>

                <section
                    class="rounded-xl border border-slate-200 bg-white p-5"
                >
                    <h3 class="mb-4 font-display text-sm font-bold text-ink">
                        Expense by Title
                    </h3>
                    <div class="h-[260px]">
                        <canvas ref="breakdownCanvas" />
                    </div>
                </section>
            </div>

            <div v-else class="grid grid-cols-1 gap-4 xl:grid-cols-3">
                <section
                    class="rounded-xl border border-slate-200 bg-white p-5 xl:col-span-2"
                >
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="font-display text-sm font-bold text-ink">
                            Performance Snapshot
                        </h3>
                        <span
                            class="rounded-full bg-accent/10 px-2 py-1 text-xs font-medium text-accent"
                            >Live</span
                        >
                    </div>
                    <div class="space-y-3">
                        <div class="h-2 rounded-full bg-slate-100">
                            <div
                                class="h-2 rounded-full bg-income"
                                :style="{
                                    width: role === 'customer' ? '78%' : '64%',
                                }"
                            />
                        </div>
                        <div class="h-2 rounded-full bg-slate-100">
                            <div
                                class="h-2 rounded-full bg-expense"
                                :style="{
                                    width: role === 'customer' ? '52%' : '33%',
                                }"
                            />
                        </div>
                        <div class="h-2 rounded-full bg-slate-100">
                            <div
                                class="h-2 rounded-full bg-amber-400"
                                :style="{
                                    width: role === 'customer' ? '40%' : '46%',
                                }"
                            />
                        </div>
                    </div>
                    <p class="mt-4 text-xs text-slate-500">
                        <span v-if="role === 'customer'"
                            >Income, expense, and savings trend indicators for
                            the current period.</span
                        >
                        <span v-else
                            >Operational indicators for customer growth and
                            title curation health.</span
                        >
                    </p>
                </section>

                <section
                    class="rounded-xl border border-slate-200 bg-white p-5"
                >
                    <h3 class="font-display text-sm font-bold text-ink">
                        Notes
                    </h3>
                    <ul class="mt-4 space-y-2 text-sm text-slate-600">
                        <li class="rounded-lg bg-slate-50 px-3 py-2">
                            Use the left menu to move between table and form
                            pages quickly.
                        </li>
                        <li
                            class="rounded-lg bg-slate-50 px-3 py-2"
                            v-if="role === 'customer'"
                        >
                            Your financial data is scoped to your own account.
                        </li>
                        <li class="rounded-lg bg-slate-50 px-3 py-2" v-else>
                            Super admin views intentionally exclude amounts and
                            transaction rows.
                        </li>
                    </ul>
                </section>
            </div>

            <section class="rounded-xl border border-slate-200 bg-white">
                <div
                    class="flex items-center justify-between border-b border-slate-100 px-5 py-4"
                >
                    <h3 class="font-display text-sm font-bold text-ink">
                        Recent Activity
                    </h3>
                    <span class="text-xs font-medium text-accent"
                        >Overview</span
                    >
                </div>
                <div class="divide-y divide-slate-100 text-sm">
                    <div class="flex items-center justify-between px-5 py-3">
                        <div>
                            <p class="font-medium text-ink">
                                Titles catalog updated
                            </p>
                            <p class="text-xs text-slate-500">
                                Shared list refreshed
                            </p>
                        </div>
                        <p class="text-xs text-slate-500">Today</p>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3">
                        <div>
                            <p
                                class="font-medium text-ink"
                                v-if="role === 'customer'"
                            >
                                New transaction added
                            </p>
                            <p class="font-medium text-ink" v-else>
                                New customer registered
                            </p>
                            <p class="text-xs text-slate-500">
                                System event recorded
                            </p>
                        </div>
                        <p class="text-xs text-slate-500">This week</p>
                    </div>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
