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
        $interventions = Intervention::with(['client', 'techniciens'])->paginate(10);
        return response()->json($interventions);
    }

    // Méthode pour créer une nouvelle intervention
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'ticket_id' => 'required|exists:tickets,id',
            'client_id' => 'required|exists:clients,id',
            'techniciens' => 'required|array',
            'techniciens.*' => 'exists:users,id',
            'statut' => 'required|string|in:en attente,en cours,terminé',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Gérer les images d'abord
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('interventions', 'public');
                $imagePaths[] = $path;
            }
        }

        // Créer l'intervention avec les images
        $intervention = Intervention::create([
            'titre' => $validated['titre'],
            'description' => $validated['description'],
            'ticket_id' => $validated['ticket_id'],
            'client_id' => $validated['client_id'],
            'statut' => $validated['statut'],
            'images' => !empty($imagePaths) ? json_encode($imagePaths) : null
        ]);

        // Attacher les techniciens à l'intervention
        $intervention->techniciens()->attach($validated['techniciens']);

        return back()->with('message', 'Intervention créée avec succès');
    }

    // Méthode pour afficher une intervention spécifique
    public function show(Intervention $intervention)
    {
        $intervention->load(['client', 'techniciens']);
        return response()->json($intervention);
    }

    // Méthode pour mettre à jour une intervention
    public function update(Request $request, Intervention $intervention)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'techniciens' => 'required|array',
            'techniciens.*' => 'exists:users,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Gérer les nouvelles images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('interventions', 'public');
                $imagePaths[] = $path;
            }
        }

        $intervention->update([
            'titre' => $validated['titre'],
            'description' => $validated['description'],
            'images' => !empty($imagePaths) ? json_encode($imagePaths) : $intervention->images
        ]);

        // Mettre à jour les techniciens
        $intervention->techniciens()->sync($validated['techniciens']);

        return back()->with('message', 'Intervention mise à jour avec succès');
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
