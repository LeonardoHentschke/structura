<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientController extends Controller {

    public function index() {
        return Inertia::render('Clients');
    }

    public function store(Request $request) {
        
        $request->validate([
            'name' => 'required|min:3',
            'cpf' => 'required|unique:clients,cpf',
            'birth_date' => 'required|date',
            'addresses' => 'required|array|min:1',
            'addresses.*.street' => 'required|string',
            'addresses.*.number' => 'required|string',
            'addresses.*.city' => 'required|string',
            'addresses.*.latitude' => 'nullable|numeric',
            'addresses.*.longitude' => 'nullable|numeric',
        ]);

        $client = Client::create([
            'name' => $request->input('name'),
            'cpf' => $request->input('cpf'),
            'birth_date' => $request->input('birth_date'),
            'created_by' => Auth::id(),
        ]);
        
        // Remova o dd($client);
        
        foreach ($request->input('addresses') as $addressData) {
            $client->addresses()->create([
                'created_by' => Auth::id(),
                'street' => $addressData['street'],
                'number' => $addressData['number'],
                'city' => $addressData['city'],
                'latitude' => $addressData['latitude'] ?? null,
                'longitude' => $addressData['longitude'] ?? null,
            ]);
        }

        return redirect()->route('dashboard');
    }
}