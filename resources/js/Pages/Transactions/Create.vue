<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { computed, ref, watch } from "vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    titles: {
        type: Array,
        required: true,
    },
    defaults: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    type: props.defaults.type,
    title_id: null,
    amount: "",
    occurred_at: props.defaults.occurred_at,
    remarks: "",
});

const titleQuery = ref("");
const titleOpen = ref(false);

const filteredTitles = computed(() =>
    props.titles
        .filter((title) => title.type === form.type)
        .filter((title) =>
            title.name.toLowerCase().includes(titleQuery.value.toLowerCase()),
        ),
);

const selectedTitleName = computed(() => {
    const selected = props.titles.find((title) => title.id === form.title_id);
    return selected ? selected.name : "";
});

const selectTitle = (title) => {
    form.title_id = title.id;
    titleQuery.value = title.name;
    titleOpen.value = false;
};

watch(
    () => form.type,
    () => {
        form.title_id = null;
        titleQuery.value = "";
        titleOpen.value = false;
    },
);

const submit = () => {
    form.post(route("transactions.store"), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset("title_id", "amount", "remarks");
            form.occurred_at = props.defaults.occurred_at;
            titleQuery.value = "";
            titleOpen.value = false;
        },
    });
};
</script>

<template>
    <Head title="Add Transaction" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-2xl font-bold leading-tight text-ink">
                Add Transaction
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                One-screen quick entry for income and expense records.
            </p>
        </template>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <form
                class="space-y-5 rounded-xl border border-slate-200 bg-white p-5 sm:p-6 lg:col-span-2"
                @submit.prevent="submit"
            >
                <div>
                    <label
                        class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-500"
                        >Transaction Type</label
                    >
                    <div
                        class="grid grid-cols-2 rounded-xl border border-slate-200 bg-paper p-1"
                    >
                        <button
                            type="button"
                            class="flex items-center justify-center gap-2 rounded-lg py-2.5 text-sm font-semibold transition-all"
                            :class="
                                form.type === 'income'
                                    ? 'bg-white text-income shadow-sm'
                                    : 'text-slate-500'
                            "
                            @click="form.type = 'income'"
                        >
                            Income
                        </button>
                        <button
                            type="button"
                            class="flex items-center justify-center gap-2 rounded-lg py-2.5 text-sm font-semibold transition-all"
                            :class="
                                form.type === 'expense'
                                    ? 'bg-white text-expense shadow-sm'
                                    : 'text-slate-500'
                            "
                            @click="form.type = 'expense'"
                        >
                            Expense
                        </button>
                    </div>
                </div>

                <div class="relative">
                    <label
                        class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-500"
                        >Title</label
                    >
                    <input
                        v-model="titleQuery"
                        type="text"
                        :placeholder="
                            form.type === 'income'
                                ? 'Search salary, rent...'
                                : 'Search groceries, fees...'
                        "
                        class="w-full rounded-lg border-slate-200 px-3.5 py-2.5 text-sm focus:border-accent focus:ring-accent/30"
                        @focus="titleOpen = true"
                    />

                    <div
                        v-if="titleOpen"
                        class="absolute z-20 mt-1 max-h-56 w-full overflow-y-auto rounded-lg border border-slate-200 bg-white shadow-lg"
                    >
                        <button
                            v-for="title in filteredTitles"
                            :key="title.id"
                            type="button"
                            class="flex w-full items-center justify-between px-3.5 py-2 text-left text-sm hover:bg-paper"
                            @click="selectTitle(title)"
                        >
                            <span>{{ title.name }}</span>
                            <span
                                v-if="form.title_id === title.id"
                                class="text-xs text-accent"
                                >Selected</span
                            >
                        </button>
                        <p
                            v-if="filteredTitles.length === 0"
                            class="px-3.5 py-2 text-sm text-slate-500"
                        >
                            No matching title found.
                        </p>
                    </div>

                    <p class="mt-1.5 text-xs text-slate-500">
                        Selected:
                        <span class="font-medium text-ink">{{
                            selectedTitleName || "None"
                        }}</span>
                    </p>
                    <p
                        v-if="form.errors.title_id"
                        class="mt-1 text-xs text-expense"
                    >
                        {{ form.errors.title_id }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label
                            for="amount"
                            class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-500"
                            >Amount (Rs)</label
                        >
                        <div class="relative">
                            <span
                                class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"
                                >Rs</span
                            >
                            <input
                                id="amount"
                                v-model="form.amount"
                                inputmode="decimal"
                                type="number"
                                step="0.01"
                                min="0.01"
                                class="w-full rounded-lg border-slate-200 py-2.5 pl-9 pr-3.5 text-base font-medium tabular focus:border-accent focus:ring-accent/30"
                                required
                            />
                        </div>
                        <p
                            v-if="form.errors.amount"
                            class="mt-1 text-xs text-expense"
                        >
                            {{ form.errors.amount }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="occurred_at"
                            class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-500"
                            >Date and Time</label
                        >
                        <input
                            id="occurred_at"
                            v-model="form.occurred_at"
                            type="datetime-local"
                            class="w-full rounded-lg border-slate-200 px-3.5 py-2.5 text-sm focus:border-accent focus:ring-accent/30"
                            required
                        />
                        <p
                            v-if="form.errors.occurred_at"
                            class="mt-1 text-xs text-expense"
                        >
                            {{ form.errors.occurred_at }}
                        </p>
                    </div>
                </div>

                <details>
                    <summary
                        class="cursor-pointer text-sm font-medium text-accent"
                    >
                        Add a remark (optional)
                    </summary>
                    <textarea
                        v-model="form.remarks"
                        rows="3"
                        class="mt-2 block w-full rounded-lg border-slate-200 px-3.5 py-2.5 text-sm focus:border-accent focus:ring-accent/30"
                        placeholder="Example: Weekly grocery run"
                    />
                </details>

                <div class="flex items-center gap-3 pt-2">
                    <button
                        type="submit"
                        :disabled="form.processing || !form.title_id"
                        :class="
                            form.type === 'income'
                                ? 'bg-income hover:bg-emerald-700'
                                : 'bg-expense hover:bg-rose-700'
                        "
                        class="rounded-lg px-5 py-2.5 text-sm font-semibold text-white transition-colors disabled:opacity-50"
                    >
                        Save Transaction
                    </button>
                    <p class="text-xs text-slate-500">
                        Entries are saved instantly to your ledger.
                    </p>
                </div>
            </form>

            <aside
                class="h-fit rounded-xl border border-slate-200 bg-white p-5"
            >
                <h3 class="font-display text-sm font-bold text-ink">
                    Quick Tips
                </h3>
                <ul
                    class="mt-4 space-y-3 text-xs leading-relaxed text-slate-600"
                >
                    <li class="flex gap-2">
                        <span
                            class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-accent"
                        />Use existing titles whenever possible to keep reports
                        clean.
                    </li>
                    <li class="flex gap-2">
                        <span
                            class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-accent"
                        />Select type first; title choices update automatically.
                    </li>
                    <li class="flex gap-2">
                        <span
                            class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-accent"
                        />Short remarks help when reviewing month-end spending.
                    </li>
                </ul>
            </aside>
        </div>
    </AuthenticatedLayout>
</template>
