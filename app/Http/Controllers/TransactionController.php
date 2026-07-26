<?php

namespace App\Http\Controllers;

use App\Enums\RecordType;
use App\Models\Title;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function create(Request $request): Response
    {
        Gate::authorize('create', Transaction::class);

        return Inertia::render('Transactions/Create', [
            'titles' => Title::query()->orderBy('name')->get(['id', 'name', 'type']),
            'defaults' => [
                'type' => RecordType::Expense->value,
                'occurred_at' => now()->format('Y-m-d\\TH:i'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Transaction::class);

        $data = $request->validate([
            'type' => ['required', Rule::in([RecordType::Income->value, RecordType::Expense->value])],
            'title_id' => [
                'required',
                'integer',
                Rule::exists('titles', 'id')->where(fn ($query) => $query->where('type', $request->string('type')->toString())),
            ],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'occurred_at' => ['required', 'date'],
            'remarks' => ['nullable', 'string', 'max:5000'],
        ]);

        Transaction::create([
            'user_id' => $request->user()->id,
            'title_id' => $data['title_id'],
            'type' => $data['type'],
            'amount' => $data['amount'],
            'occurred_at' => $data['occurred_at'],
            'remarks' => $data['remarks'] ?? null,
        ]);

        return back()->with('status', 'Transaction saved successfully.');
    }
}
