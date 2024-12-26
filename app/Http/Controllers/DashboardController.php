<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $statistics = [
            'enAttente' => Ticket::where('statut', 'en attente')->count(),
            'enCours' => Ticket::where('statut', 'en cours')->count(),
            'termines' => Ticket::where('statut', 'terminé')->count(),
        ];

        return Inertia::render('Dashboard', [
            'statistics' => $statistics
        ]);
    }
}
