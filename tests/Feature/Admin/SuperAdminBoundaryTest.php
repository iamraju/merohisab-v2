<?php

use App\Enums\RecordType;
use App\Models\Title;
use App\Models\Transaction;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('super admin has non-financial dashboard and cannot access customer financial endpoints', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('role', 'super_admin')
            ->has('adminSummary')
            ->missing('customerSummary.total_income')
            ->missing('customerSummary.total_expense')
        );

    $this->actingAs($superAdmin)->get(route('transactions.create'))->assertForbidden();
    $this->actingAs($superAdmin)->get(route('reports.index'))->assertForbidden();

    $title = Title::factory()->create([
        'type' => RecordType::Expense,
    ]);

    $this->actingAs($superAdmin)->post(route('transactions.store'), [
        'type' => RecordType::Expense->value,
        'title_id' => $title->id,
        'amount' => '100.00',
        'occurred_at' => now()->toDateTimeString(),
    ])->assertForbidden();
});

test('super admin customers page excludes amount and transaction fields', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $customer = User::factory()->customer()->create();

    $expenseTitle = Title::factory()->create(['type' => RecordType::Expense]);
    Transaction::factory()->create([
        'user_id' => $customer->id,
        'title_id' => $expenseTitle->id,
        'type' => RecordType::Expense,
        'amount' => '235.45',
    ]);

    $this->actingAs($superAdmin)
        ->get(route('admin.customers.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Customers/Index')
            ->has('customers.data')
            ->missing('customers.data.0.amount')
            ->missing('customers.data.0.transactions')
        );

    $this->assertDatabaseHas('transactions', [
        'user_id' => $customer->id,
        'amount' => '235.45',
    ]);
});
