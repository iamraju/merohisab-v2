<?php

namespace App\Http\Controllers;

use App\Models\Title;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            return Inertia::render('Dashboard', [
                'role' => 'super_admin',
                'adminSummary' => [
                    'total_customers' => User::query()->where('role', 'customer')->count(),
                    'active_customers' => User::query()->where('role', 'customer')->where('status', 'active')->count(),
                    'total_titles' => Title::query()->count(),
                    'customer_created_titles' => Title::query()->whereNotNull('created_by_user_id')->count(),
                ],
                'customerSummary' => null,
            ]);
        }

        Gate::authorize('viewAny', Transaction::class);

        $baseQuery = Transaction::query()->where('user_id', $user->id);

        $totalIncome = (string) (clone $baseQuery)
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = (string) (clone $baseQuery)
            ->where('type', 'expense')
            ->sum('amount');

        return Inertia::render('Dashboard', [
            'role' => 'customer',
            'adminSummary' => null,
            'customerSummary' => [
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
            ],
        ]);
    }
}
