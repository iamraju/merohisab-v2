<?php

use App\Enums\RecordType;
use App\Models\Title;
use App\Models\User;

test('customer can create title and duplicate names are reused case-insensitively', function () {
    $customer = User::factory()->customer()->create();

    $this->actingAs($customer)->post(route('titles.store'), [
        'name' => 'Groceries',
        'type' => RecordType::Expense->value,
    ])->assertRedirect();

    $this->assertDatabaseHas('titles', [
        'name_normalized' => 'groceries',
        'type' => RecordType::Expense->value,
        'created_by_user_id' => $customer->id,
    ]);

    $this->actingAs($customer)->post(route('titles.store'), [
        'name' => '   groceries   ',
        'type' => RecordType::Expense->value,
    ])->assertRedirect();

    expect(Title::query()->where('name_normalized', 'groceries')->where('type', RecordType::Expense->value)->count())->toBe(1);
});

test('customer cannot update or delete titles', function () {
    $customer = User::factory()->customer()->create();
    $title = Title::factory()->create();

    $this->actingAs($customer)->patch(route('titles.update', $title), [
        'name' => 'Updated',
        'type' => RecordType::Expense->value,
    ])->assertForbidden();

    $this->actingAs($customer)->delete(route('titles.destroy', $title))->assertForbidden();
});
