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
            'ticket_id' => 'required|exists:tickets,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Gérer les images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('interventions', 'public');
                $imagePaths[] = $path;
            }
        }

        // Créer l'intervention avec les chemins d'images
        $intervention = Intervention::create([
            'titre' => $validated['titre'],
            'description' => $validated['description'],
            'client_id' => $validated['client_id'],
            'technicien_id' => $validated['technicien_id'],
            'ticket_id' => $validated['ticket_id'],
            'images' => !empty($imagePaths) ? json_encode($imagePaths) : null
        ]);

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
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'technicien_id' => 'nullable|exists:users,id',
        ]);

        $intervention->update($validated);

        return back();
    }

    // Méthode pour supprimer une intervention
    public function destroy(Intervention $intervention)
    {
        $intervention->delete();
        return back()->with('message', 'Intervention supprimée avec succès');
    }

    public function create()
    {
        return Inertia::render('Interventions/Create', [
            'clients' => Client::all(),
            'techniciens' => User::where('role', 'technicien')->get(),
        ]);
    }
}
