<?php

namespace App\Models;

use App\Enums\RecordType;
use App\Models\Transaction;
use Database\Factories\TitleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[UseFactory(TitleFactory::class)]
#[Fillable(['name', 'name_normalized', 'type', 'created_by_user_id'])]
class Title extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => RecordType::class,
        ];
    }

    public static function normalizeName(string $name): string
    {
        return Str::lower(trim(preg_replace('/\s+/', ' ', $name) ?? $name));
    }

    protected static function booted(): void
    {
        static::saving(function (Title $title): void {
            $title->name_normalized = self::normalizeName($title->name);
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
