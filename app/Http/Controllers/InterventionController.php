<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Intervention;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InterventionController extends Controller
{
    // Méthode pour lister toutes les interventions
    public function index()
    {
        $interventions = Intervention::with(['client', 'technicien'])->paginate(10);
        return response()->json($interventions);
    }

    // Méthode pour créer une nouvelle intervention
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'technicien_id' => 'nullable|exists:users,id',
            'ticket_id' => 'nullable|exists:tickets,id',
            'images' => 'nullable|array',
        ]);

        $validated['images'] = json_encode($validated['images'] ?? []);

        $intervention = Intervention::create($validated);

        return back()->with('message', 'Intervention créée avec succès');
    }

    // Méthode pour afficher une intervention spécifique
    public function show(Intervention $intervention)
    {
        $intervention->load(['client', 'technicien']);
        return response()->json($intervention);
    }

    // Méthode pour mettre à jour une intervention
    public function update(Request $request, Intervention $intervention)
    {
        $validated = $request->validate([
            'titre' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'technicien_id' => 'nullable|exists:users,id',
            'images' => 'sometimes|array',
        ]);

        if (isset($validated['images'])) {
            $validated['images'] = json_encode($validated['images']);
        }

        $intervention->update($validated);

        return response()->json($intervention);
    }

    // Méthode pour supprimer une intervention
    public function destroy(Intervention $intervention)
    {
        $intervention->delete();
        return response()->json(['message' => 'Intervention supprimée avec succès.']);
    }

    public function create()
    {
        return Inertia::render('Interventions/Create', [
            'clients' => Client::all(),
            'techniciens' => User::where('role', 'technicien')->get(),
        ]);
    }
}
