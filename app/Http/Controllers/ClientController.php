<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Intervention;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:clients',
            'telephone' => 'nullable|string|max:20',
            'addresse' => 'nullable|string',
        ]);

        $client = Client::create($validated);

        return response()->json($client, 201);
    }
}
