<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    titles: {
        type: Array,
        required: true,
    },
    canManage: {
        type: Boolean,
        required: true,
    },
});

const form = useForm({
    name: "",
    type: "expense",
});

const sortedTitles = computed(() =>
    [...props.titles].sort((a, b) =>
        a.name.localeCompare(b.name, undefined, { sensitivity: "base" }),
    ),
);

const submit = () => {
    form.post(route("titles.store"), {
        preserveScroll: true,
        onSuccess: () => form.reset("name"),
    });
};
</script>

<template>
    <Head title="Titles" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-2xl font-bold leading-tight text-ink">
                Titles
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Manage shared income and expense title catalog.
            </p>
        </template>

        <div class="space-y-4">
            <section
                class="rounded-xl border border-slate-200 bg-white p-4 sm:p-5"
            >
                <form
                    class="grid grid-cols-1 gap-3 md:grid-cols-5"
                    @submit.prevent="submit"
                >
                    <div class="md:col-span-2">
                        <label
                            for="name"
                            class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-500"
                            >Title name</label
                        >
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            placeholder="eg. Groceries or Salary"
                            class="w-full rounded-lg border-slate-200 px-3.5 py-2.5 text-sm focus:border-accent focus:ring-accent/30"
                            required
                        />
                        <p
                            v-if="form.errors.name"
                            class="mt-1 text-xs text-expense"
                        >
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="type"
                            class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-500"
                            >Type</label
                        >
                        <select
                            id="type"
                            v-model="form.type"
                            class="w-full rounded-lg border-slate-200 px-3.5 py-2.5 text-sm focus:border-accent focus:ring-accent/30"
                            required
                        >
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 flex items-end gap-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center rounded-lg bg-ink px-4 py-2.5 text-sm font-semibold text-white hover:bg-inkline disabled:opacity-50"
                        >
                            Save Title
                        </button>
                        <p class="text-xs text-slate-500">
                            Case-insensitive duplicates are reused
                            automatically.
                        </p>
                    </div>
                </form>
            </section>

            <section
                class="rounded-xl border border-slate-200 bg-white overflow-hidden"
            >
                <div
                    class="flex items-center justify-between border-b border-slate-100 px-5 py-4"
                >
                    <h3 class="font-display text-sm font-bold text-ink">
                        Shared Title List
                    </h3>
                    <span class="text-xs font-medium text-slate-500"
                        >{{ sortedTitles.length }} total</span
                    >
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr
                                class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400"
                            >
                                <th class="px-5 py-3 font-medium">Title</th>
                                <th class="px-5 py-3 font-medium">Type</th>
                                <th class="px-5 py-3 font-medium">Source</th>
                                <th class="px-5 py-3 font-medium">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="title in sortedTitles"
                                :key="title.id"
                                class="hover:bg-paper/60"
                            >
                                <td class="px-5 py-3.5 font-medium text-ink">
                                    {{ title.name }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="inline-flex rounded-full px-2 py-1 text-xs font-medium"
                                        :class="
                                            title.type === 'income'
                                                ? 'bg-income/10 text-income'
                                                : 'bg-expense/10 text-expense'
                                        "
                                    >
                                        {{ title.type }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">
                                    {{
                                        title.created_by_user_id
                                            ? "Customer"
                                            : "Admin"
                                    }}
                                </td>
                                <td class="px-5 py-3.5 text-slate-500">
                                    {{
                                        new Date(
                                            title.created_at,
                                        ).toLocaleDateString()
                                    }}
                                </td>
                            </tr>
                            <tr v-if="sortedTitles.length === 0">
                                <td
                                    class="px-5 py-6 text-center text-slate-500"
                                    colspan="4"
                                >
                                    No titles found yet.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p
                    v-if="canManage"
                    class="border-t border-slate-100 px-5 py-3 text-xs text-slate-500"
                >
                    Super admin can update or delete titles via management
                    endpoints.
                </p>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
