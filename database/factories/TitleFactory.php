<?php

namespace Database\Factories;

use App\Enums\RecordType;
use App\Models\Title;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Title>
 */
class TitleFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucfirst($name),
            'name_normalized' => Title::normalizeName($name),
            'type' => fake()->randomElement([RecordType::Income, RecordType::Expense]),
            'created_by_user_id' => User::factory(),
        ];
    }
}
