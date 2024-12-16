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

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'titre' => 'required|string|max:255',
                'description' => 'required|string',
                'client_id' => 'required_without:client|nullable|exists:clients,id',
                'client' => 'required_without:client_id|array',
                'client.nom' => 'required_with:client|string',
                'client.prenom' => 'required_with:client|string',
                'client.email' => 'required_with:client|email|unique:clients,email',
                'client.telephone' => 'nullable|string',
                'client.addresse' => 'nullable|string',
                'technicien_id' => 'nullable|exists:users,id',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            Log::info('Données validées :', $validated);

            // Créer un nouveau client si nécessaire
            if (!isset($validated['client_id']) && isset($validated['client'])) {
                Log::info('Création d\'un nouveau client');
                $client = Client::create([
                    'nom' => $validated['client']['nom'],
                    'prenom' => $validated['client']['prenom'], // Assurez-vous que le prénom est inclus
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
                Log::info('Traitement des images');
                foreach ($request->file('images') as $image) {
                    $path = $image->store('tickets', 'public');
                    $imagePaths[] = $path;
                }
            }

            // Créer le ticket
            $ticket = Ticket::create([
                'titre' => $validated['titre'],
                'description' => $validated['description'],
                'statut' => 'en attente',
                'client_id' => $validated['client_id'],
                'technicien_id' => $validated['technicien_id'] ?? null,
                'images' => !empty($imagePaths) ? json_encode($imagePaths) : null
            ]);

            Log::info('Ticket créé avec succès', ['ticket_id' => $ticket->id]);

            return redirect()->route('tickets.index')
                ->with('message', 'Ticket créé avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du ticket: ' . $e->getMessage());

            // Nettoyer les images en cas d'erreur
            if (isset($imagePaths)) {
                foreach ($imagePaths as $path) {
                    Storage::disk('public')->delete($path);
                }
            }

            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de la création du ticket: ' . $e->getMessage()
            ])->withInput();
        }
    }
}
