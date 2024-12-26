<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Intervention;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        return Inertia::render('Clients/Index', [
            'clients' => Client::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:clients',
            'telephone' => 'nullable|string|max:20',
            'addresse' => 'nullable|string',
        ]);

        Client::create($validated);

        return redirect()->back()->with('message', 'Client créé avec succès');
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'telephone' => 'nullable|string|max:20',
            'addresse' => 'nullable|string',
        ]);

        $client->update($validated);

        return redirect()->back()->with('message', 'Client modifié avec succès');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->back()->with('message', 'Client supprimé avec succès');
    }

    public function show(Client $client)
    {
        return Inertia::render('Clients/Show', [
            'client' => $client,
            'tickets' => $client->tickets()
                ->with(['interventions' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }])
                ->latest()
                ->get()
        ]);
    }
}
