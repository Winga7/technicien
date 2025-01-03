<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Client;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $statistics = [
            'tickets' => [
                'enAttente' => Ticket::where('statut', 'en attente')->count(),
                'enCours' => Ticket::where('statut', 'en cours')->count(),
                'termines' => Ticket::where('statut', 'terminé')->count(),
                'actifs' => Ticket::whereIn('statut', ['en attente', 'en cours'])->count(),
                'nouveauxCeMois' => Ticket::whereMonth('created_at', now()->month)->count(),
                'total' => Ticket::count()
            ],
            'users' => [
                'techniciens' => User::where('role', 'technicien')->count(),
                'admins' => User::where('role', 'admin')->count(),
                'total' => User::count()
            ],
            'clients' => [
                'nouveauxCeMois' => Client::whereMonth('created_at', now()->month)->count(),
                'actifs' => Client::has('tickets')->count(),
                'total' => Client::count()
            ]
        ];

        $clients = Client::all();

        return Inertia::render('Dashboard', [
            'statistics' => $statistics,
            'clients' => $clients
        ]);
    }
}
