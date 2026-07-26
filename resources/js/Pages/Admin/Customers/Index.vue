<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
    customers: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Customers" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-2xl font-bold leading-tight text-ink">
                Customers
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Registered customer profiles without financial amounts.
            </p>
        </template>

        <div class="space-y-4">
            <div
                class="overflow-x-auto rounded-xl border border-slate-200 bg-white"
            >
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-paper/70">
                        <tr>
                            <th
                                class="px-3 py-2 text-left font-medium text-slate-600"
                            >
                                Name
                            </th>
                            <th
                                class="px-3 py-2 text-left font-medium text-slate-600"
                            >
                                Email
                            </th>
                            <th
                                class="px-3 py-2 text-left font-medium text-slate-600"
                            >
                                Phone
                            </th>
                            <th
                                class="px-3 py-2 text-left font-medium text-slate-600"
                            >
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="customer in props.customers.data"
                            :key="customer.id"
                            class="hover:bg-paper/60"
                        >
                            <td class="px-3 py-2">{{ customer.name }}</td>
                            <td class="px-3 py-2">{{ customer.email }}</td>
                            <td class="px-3 py-2">
                                {{ customer.phone || "-" }}
                            </td>
                            <td class="px-3 py-2 capitalize">
                                {{ customer.status }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-slate-500">
                Financial amounts are intentionally excluded by query design.
            </p>
        </div>
    </AuthenticatedLayout>
</template>
