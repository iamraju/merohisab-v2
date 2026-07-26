<?php

namespace Database\Factories;

use App\Enums\RecordType;
use App\Models\Title;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement([RecordType::Income, RecordType::Expense]);
        $title = Title::factory()->state(['type' => $type]);

        return [
            'user_id' => User::factory(),
            'title_id' => $title,
            'type' => $type,
            'amount' => fake()->randomFloat(2, 1, 50000),
            'occurred_at' => fake()->dateTimeBetween('-1 year'),
            'remarks' => fake()->optional()->sentence(),
        ];
    }
}
