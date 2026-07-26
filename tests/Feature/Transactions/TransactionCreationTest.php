<?php

use App\Enums\RecordType;
use App\Models\Title;
use App\Models\User;

test('customer can create transaction', function () {
    $customer = User::factory()->customer()->create();
    $title = Title::factory()->create([
        'type' => RecordType::Income,
    ]);

    $this->actingAs($customer)->post(route('transactions.store'), [
        'type' => RecordType::Income->value,
        'title_id' => $title->id,
        'amount' => '500.50',
        'occurred_at' => now()->toDateTimeString(),
        'remarks' => 'Salary payment',
    ])->assertRedirect();

    $this->assertDatabaseHas('transactions', [
        'user_id' => $customer->id,
        'title_id' => $title->id,
        'type' => RecordType::Income->value,
        'amount' => '500.50',
    ]);
});

test('transaction title must match selected type', function () {
    $customer = User::factory()->customer()->create();
    $expenseTitle = Title::factory()->create([
        'type' => RecordType::Expense,
    ]);

    $this->actingAs($customer)->post(route('transactions.store'), [
        'type' => RecordType::Income->value,
        'title_id' => $expenseTitle->id,
        'amount' => '10.00',
        'occurred_at' => now()->toDateTimeString(),
    ])->assertSessionHasErrors('title_id');
});
