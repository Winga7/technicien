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
                'actifs' => Ticket::where('statut', '!=', 'terminé')->count(),
                'nouveauxCeMois' => Ticket::whereMonth('created_at', now()->month)->count(),
                'total' => Ticket::count()
            ],
            'users' => [
                'total' => User::count(),
                'techniciens' => User::where('role', 'technicien')->count(),
                'admins' => User::where('role', 'admin')->count()
            ],
            'clients' => [
                'total' => Client::count(),
                'nouveauxCeMois' => Client::whereMonth('created_at', now()->month)->count(),
                'actifs' => Client::whereHas('tickets', function ($query) {
                    $query->where('statut', '!=', 'terminé');
                })->count()
            ]
        ];

        return Inertia::render('Dashboard', [
            'statistics' => $statistics
        ]);
    }
}
