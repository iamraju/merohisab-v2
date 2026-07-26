<?php

namespace App\Models;

use App\Enums\RecordType;
use App\Models\Title;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(TransactionFactory::class)]
#[Fillable(['user_id', 'title_id', 'type', 'amount', 'occurred_at', 'remarks'])]
class Transaction extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => RecordType::class,
            'amount' => 'decimal:2',
            'occurred_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class);
    }
}
