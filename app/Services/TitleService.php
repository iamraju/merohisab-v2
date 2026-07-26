<?php

namespace App\Services;

use App\Enums\RecordType;
use App\Models\Title;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TitleService
{
    public function createOrReuse(string $name, RecordType $type, User $actor): Title
    {
        return DB::transaction(function () use ($name, $type, $actor): Title {
            $normalized = Title::normalizeName($name);

            $existing = Title::query()
                ->where('name_normalized', $normalized)
                ->where('type', $type->value)
                ->first();

            if ($existing !== null) {
                return $existing;
            }

            return Title::create([
                'name' => trim($name),
                'name_normalized' => $normalized,
                'type' => $type,
                'created_by_user_id' => $actor->isSuperAdmin() ? null : $actor->id,
            ]);
        });
    }
}
