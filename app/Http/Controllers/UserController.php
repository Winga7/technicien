<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        return Inertia::render('Users/Index', [
            'users' => User::select('id', 'nom', 'email', 'telephone', 'role')->get()
        ]);
    }

    public function create()
    {
        $this->authorize('create', User::class);
        return Inertia::render('Users/Create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => 'required|string|in:admin,technicien',
        ]);

        $validated['password'] = bcrypt('password');
        $validated['must_reset_password'] = true;

        $user = User::create($validated);

        return redirect()->route('users.index')
            ->with('message', 'Utilisateur créé avec succès. Le mot de passe par défaut est "password".');
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'telephone' => 'sometimes|string|max:20',
            'role' => 'sometimes|string|in:admin,technicien',
        ]);

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('message', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();

        return redirect()->back()->with('message', 'Utilisateur supprimé avec succès');
    }

    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => $user
        ]);
    }
}
