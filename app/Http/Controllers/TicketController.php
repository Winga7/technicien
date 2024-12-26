<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index()
    {
        return Inertia::render('Tickets/Index', [
            'tickets' => Ticket::with(['client', 'technicien'])->latest()->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Tickets/Create', [
            'clients' => Client::all(),
            'techniciens' => User::where('role', 'technicien')->get(),
        ]);
    }

    public function show(Ticket $ticket)
    {

        return Inertia::render('Tickets/Show', [
            'ticket' => $ticket->load(['client', 'technicien', 'interventions.technicien']),
            'techniciens' => User::where('role', 'technicien')->get(),
        ]);
    }

    public function edit(Ticket $ticket)
    {
        return Inertia::render('Tickets/Edit', [
            'ticket' => $ticket->load(['client', 'technicien']),
            'clients' => Client::all(),
            'techniciens' => User::where('role', 'technicien')->get(),
        ]);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'statut' => 'required|string|in:en attente,en cours,terminé',
            'client_id' => 'required_without:client|nullable|exists:clients,id',
            'client' => 'required_without:client_id|array',
            'client.name' => 'required_with:client|string',
            'client.prenom' => 'required_with:client|string',
            'client.email' => 'required_with:client|email|unique:clients,email,' . $ticket->client_id,
            'client.telephone' => 'nullable|string',
            'client.addresse' => 'nullable|string',
            'technicien_id' => 'nullable|exists:users,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Créer un nouveau client si nécessaire
        if (!isset($validated['client_id']) && isset($validated['client'])) {
            $client = Client::create([
                'name' => $validated['client']['name'],
                'prenom' => $validated['client']['prenom'],
                'email' => $validated['client']['email'],
                'telephone' => $validated['client']['telephone'],
                'addresse' => $validated['client']['addresse'],
            ]);
            $validated['client_id'] = $client->id;
            unset($validated['client']);
        }

        // Gérer les images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('tickets', 'public');
                $imagePaths[] = $path;
            }
        }

        // Mettre à jour le ticket
        $ticket->update([
            'titre' => $validated['titre'],
            'description' => $validated['description'],
            'statut' => $validated['statut'],
            'client_id' => $validated['client_id'],
            'technicien_id' => $validated['technicien_id'] ?? null,
            'images' => !empty($imagePaths) ? json_encode($imagePaths) : null
        ]);

        return redirect(url()->current())
            ->with('message', 'Ticket mis à jour avec succès');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'titre' => 'required|string|max:255',
                'description' => 'required|string',
                'statut' => 'required|string|in:en attente,en cours,terminé',
                'client_id' => 'required_without:client',
                'client' => 'required_without:client_id',
                'technicien_id' => 'nullable|exists:users,id',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                // Validation conditionnelle pour les champs du client
                'client.name' => 'required_with:client',
                'client.prenom' => 'required_with:client',
                'client.email' => 'required_with:client|email|unique:clients,email',
                'client.telephone' => 'nullable|string',
                'client.addresse' => 'nullable|string',
            ]);

            // Créer un nouveau client si nécessaire
            if (!$request->client_id && $request->client) {
                $client = Client::create([
                    'name' => $request->client['name'],
                    'prenom' => $request->client['prenom'],
                    'email' => $request->client['email'],
                    'telephone' => $request->client['telephone'] ?? null,
                    'addresse' => $request->client['addresse'] ?? null,
                ]);
                $clientId = $client->id;
            } else {
                $clientId = $request->client_id;
            }

            // Gérer les images
            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('tickets', 'public');
                    $imagePaths[] = $path;
                }
            }

            // Créer le ticket
            $ticket = Ticket::create([
                'titre' => $validated['titre'],
                'description' => $validated['description'],
                'statut' => $validated['statut'],
                'client_id' => $clientId,
                'technicien_id' => $validated['technicien_id'] ?? null,
                'images' => !empty($imagePaths) ? json_encode($imagePaths) : null
            ]);

            return redirect()->route('tickets.index')
                ->with('message', 'Ticket créé avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du ticket: ' . $e->getMessage());
            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de la création du ticket: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('tickets.index')
            ->with('message', 'Ticket supprimé avec succès');
    }
}
