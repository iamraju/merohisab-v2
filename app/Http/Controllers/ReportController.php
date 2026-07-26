<?php

namespace App\Http\Controllers;

use App\Models\Title;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Transaction::class);

        $query = Transaction::query()
            ->where('user_id', $request->user()->id)
            ->with('title:id,name')
            ->orderByDesc('occurred_at');

        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->toString());
        }

        if ($request->filled('title_id')) {
            $query->where('title_id', $request->integer('title_id'));
        }

        if ($request->filled('from')) {
            $query->whereDate('occurred_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('occurred_at', '<=', $request->date('to'));
        }

        return Inertia::render('Reports/Index', [
            'filters' => $request->only(['type', 'title_id', 'from', 'to']),
            'titles' => Title::query()->orderBy('name')->get(['id', 'name', 'type']),
            'transactions' => $query->paginate(20),
        ]);
    }
}
