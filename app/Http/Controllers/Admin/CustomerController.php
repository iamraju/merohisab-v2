<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', User::class);

        $customers = User::query()
            ->where('role', 'customer')
            ->orderBy('name')
            ->paginate(20, ['id', 'name', 'email', 'phone', 'status', 'created_at']);

        return Inertia::render('Admin/Customers/Index', [
            'customers' => $customers,
        ]);
    }

    public function update(Request $request, User $customer): RedirectResponse
    {
        Gate::authorize('update', $customer);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$customer->id],
            'phone' => ['nullable', 'string', 'max:40'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $customer->update($data);

        return back()->with('status', 'Customer updated successfully.');
    }

    public function resetPassword(Request $request, User $customer): RedirectResponse
    {
        Gate::authorize('resetPassword', $customer);

        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $customer->update([
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('status', 'Customer password reset successfully.');
    }
}
