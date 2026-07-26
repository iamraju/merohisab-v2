<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { computed, reactive } from "vue";

const props = defineProps({
    titles: { type: Array, required: true },
    transactions: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const filterForm = reactive({
    type: props.filters.type ?? "",
    title_id: props.filters.title_id ?? "",
    from: props.filters.from ?? "",
    to: props.filters.to ?? "",
});

const rows = computed(() => props.transactions.data ?? []);

const formatAmount = (amount, type) => {
    const numeric = Number(amount ?? 0);
    const sign = type === "income" ? "+" : "-";
    return `${sign} Rs ${numeric.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
};

const prettyDate = (value) =>
    value
        ? new Date(value).toLocaleDateString(undefined, {
              year: "numeric",
              month: "short",
              day: "numeric",
          })
        : "-";

const applyFilters = () => {
    router.get(route("reports.index"), filterForm, {
        preserveState: true,
        replace: true,
    });
};
</script>

<template>
    <Head title="Reports" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-2xl font-bold leading-tight text-ink">
                Reports
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Filter and review transactions in a structured ledger view.
            </p>
        </template>

        <div class="space-y-4">
            <section class="rounded-xl border border-slate-200 bg-white p-4">
                <form
                    class="flex flex-wrap items-center gap-3"
                    @submit.prevent="applyFilters"
                >
                    <input
                        v-model="filterForm.from"
                        type="date"
                        class="rounded-lg border-slate-200 px-3 py-1.5 text-sm"
                    />
                    <span class="text-sm text-slate-400">to</span>
                    <input
                        v-model="filterForm.to"
                        type="date"
                        class="rounded-lg border-slate-200 px-3 py-1.5 text-sm"
                    />

                    <select
                        v-model="filterForm.type"
                        class="rounded-lg border-slate-200 px-3 py-1.5 text-sm"
                    >
                        <option value="">All Types</option>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>

                    <select
                        v-model="filterForm.title_id"
                        class="rounded-lg border-slate-200 px-3 py-1.5 text-sm"
                    >
                        <option value="">All Titles</option>
                        <option
                            v-for="title in titles"
                            :key="title.id"
                            :value="title.id"
                        >
                            {{ title.name }}
                        </option>
                    </select>

                    <button
                        type="submit"
                        class="rounded-lg bg-ink px-4 py-2 text-sm font-semibold text-white hover:bg-inkline"
                    >
                        Apply Filters
                    </button>
                    <button
                        type="button"
                        class="text-sm font-medium text-accent hover:underline"
                        @click="
                            filterForm.type = '';
                            filterForm.title_id = '';
                            filterForm.from = '';
                            filterForm.to = '';
                            applyFilters();
                        "
                    >
                        Reset
                    </button>
                </form>
            </section>

            <section
                class="overflow-hidden rounded-xl border border-slate-200 bg-white"
            >
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr
                                class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400"
                            >
                                <th class="px-5 py-3 font-medium">Date</th>
                                <th class="px-5 py-3 font-medium">Title</th>
                                <th class="px-5 py-3 font-medium">Type</th>
                                <th
                                    class="px-5 py-3 font-medium hidden md:table-cell"
                                >
                                    Remarks
                                </th>
                                <th class="px-5 py-3 text-right font-medium">
                                    Amount
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="transaction in rows"
                                :key="transaction.id"
                                class="hover:bg-paper/60"
                            >
                                <td
                                    class="px-5 py-3.5 text-slate-500 whitespace-nowrap"
                                >
                                    {{ prettyDate(transaction.occurred_at) }}
                                </td>
                                <td class="px-5 py-3.5 font-medium text-ink">
                                    {{ transaction.title?.name ?? "Untitled" }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="inline-flex rounded-full px-2 py-1 text-xs font-medium"
                                        :class="
                                            transaction.type === 'income'
                                                ? 'bg-income/10 text-income'
                                                : 'bg-expense/10 text-expense'
                                        "
                                    >
                                        {{ transaction.type }}
                                    </span>
                                </td>
                                <td
                                    class="hidden px-5 py-3.5 text-slate-500 md:table-cell"
                                >
                                    {{ transaction.remarks ?? "-" }}
                                </td>
                                <td
                                    class="px-5 py-3.5 text-right font-medium tabular"
                                    :class="
                                        transaction.type === 'income'
                                            ? 'text-income'
                                            : 'text-expense'
                                    "
                                >
                                    {{
                                        formatAmount(
                                            transaction.amount,
                                            transaction.type,
                                        )
                                    }}
                                </td>
                            </tr>
                            <tr v-if="rows.length === 0">
                                <td
                                    colspan="5"
                                    class="px-5 py-8 text-center text-slate-500"
                                >
                                    No records match these filters.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    class="flex items-center justify-between border-t border-slate-100 px-5 py-3 text-sm text-slate-500"
                >
                    <p>
                        Page {{ transactions.current_page }} of
                        {{ transactions.last_page }}
                    </p>
                    <p>
                        Total:
                        <span class="font-medium text-ink">{{
                            transactions.total
                        }}</span>
                    </p>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
