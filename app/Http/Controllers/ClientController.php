<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    // List all clients
    public function index()
    {
        return Client::orderBy('name')->get();
    }

    // Create client (offline-safe)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $client = Client::create([
            'id' => $request->id ?? Str::uuid(), // important for offline sync
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'notes' => $request->notes,
              'created_by' => $request->user()->id,
        ]);

        return response()->json($client, 201);
    }

    // Show single client
    public function show($id)
    {
        return Client::findOrFail($id);
    }
}
