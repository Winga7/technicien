<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $users = User::all();
        return Inertia::render('Users/Index', [
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|string|in:admin,technicien',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required'
        ], [
            'password.required' => 'Le mot de passe est obligatoire',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
            'password.confirmed' => 'Les mots de passe ne correspondent pas',
            'password_confirmation.required' => 'La confirmation du mot de passe est obligatoire'
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['must_reset_password'] = true;

        $user = User::create($validated);

        return redirect()->route('users.index')
            ->with('message', 'Utilisateur créé avec succès.');
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $rules = [
            'name' => ['required', 'string', 'max:255', 'regex:/^(?!\s*$).+/'],
            'firstname' => ['required', 'string', 'max:255', 'regex:/^(?!\s*$).+/'],
            'email' => ['required', 'email', 'unique:users,email,' . ($user->id ?? '')],
            'telephone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\s()-]*$/'],
            'role' => ['required', 'string', 'in:admin,technicien'],
            'show_phone' => ['boolean'],
        ];

        // Ajoute les règles de validation du mot de passe uniquement si fourni
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        // Hash le nouveau mot de passe s'il est fourni
        if ($request->filled('password')) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('message', 'Utilisateur modifié avec succès.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()->route('users.index')
            ->with('message', 'Utilisateur supprimé avec succès.');
    }
}
