<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientController extends Controller {

    public function index() {
        $clients = Client::with(['addresses', 'creator', 'editor'])->get();
        return Inertia::render('Clients', ['clients' => $clients]);
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

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|min:3',
            'cpf' => 'required|unique:clients,cpf,' . $client->id,
            'birth_date' => 'required|date',
            'addresses' => 'required|array|min:1',
            'addresses.*.street' => 'required|string',
            'addresses.*.number' => 'required|string',
            'addresses.*.city' => 'required|string',
            'addresses.*.latitude' => 'nullable|numeric',
            'addresses.*.longitude' => 'nullable|numeric',
        ]);

        $client->update([
            'name' => $request->input('name'),
            'cpf' => $request->input('cpf'),
            'birth_date' => $request->input('birth_date'),
            'updated_by' => Auth::id(),
        ]);

        $client->addresses()->delete();
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

        return redirect()->route('clients');
    }

}
