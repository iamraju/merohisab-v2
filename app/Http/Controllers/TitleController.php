<?php

namespace App\Http\Controllers;

use App\Enums\RecordType;
use App\Models\Title;
use App\Services\TitleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TitleController extends Controller
{
    public function __construct(private readonly TitleService $titleService)
    {
    }

    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Title::class);

        return Inertia::render('Titles/Index', [
            'canManage' => $request->user()->isSuperAdmin(),
            'titles' => Title::query()
                ->orderBy('type')
                ->orderBy('name')
                ->get(['id', 'name', 'type', 'created_by_user_id', 'created_at']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Title::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in([RecordType::Income->value, RecordType::Expense->value])],
        ]);

        $this->titleService->createOrReuse($data['name'], RecordType::from($data['type']), $request->user());

        return back()->with('status', 'Title saved successfully.');
    }

    public function update(Request $request, Title $title): RedirectResponse
    {
        Gate::authorize('update', $title);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in([RecordType::Income->value, RecordType::Expense->value])],
        ]);

        $title->update([
            'name' => trim($data['name']),
            'type' => $data['type'],
        ]);

        return back()->with('status', 'Title updated successfully.');
    }

    public function destroy(Title $title): RedirectResponse
    {
        Gate::authorize('delete', $title);

        $title->delete();

        return back()->with('status', 'Title deleted successfully.');
    }
}
