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
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:clients',
            'birth_date' => 'nullable|date',
            'addresses' => 'array',
            'addresses.*.street' => 'required|string|max:255',
            'addresses.*.number' => 'required|string|max:10',
            'addresses.*.city' => 'required|string|max:100',
            'addresses.*.latitude' => 'nullable|numeric',
            'addresses.*.longitude' => 'nullable|numeric',
        ]);

        $client = Client::create([
            'name' => $request->name,
            'cpf' => $request->cpf,
            'birth_date' => $request->birth_date,
            'created_by' => Auth::id(), // assume que você tem um usuário autenticado
        ]);

        foreach ($request->addresses as $address) {
            $client->addresses()->create(array_merge($address, [
                'client_id' => $client->id,
                'created_by' => Auth::id(),
            ]));
        }

        return redirect()->route('clients')->with('success', 'Cliente criado com sucesso.');
    }

    public function update(Request $request, Client $client) {
        $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:clients,cpf,' . $client->id,
            'birth_date' => 'nullable|date',
            'addresses' => 'array',
            'addresses.*.street' => 'required|string|max:255',
            'addresses.*.number' => 'required|string|max:10',
            'addresses.*.city' => 'required|string|max:100',
            'addresses.*.latitude' => 'nullable|numeric',
            'addresses.*.longitude' => 'nullable|numeric',
        ]);

        $client->update($request->only('name', 'cpf', 'birth_date'));

        // Atualizar os endereços do cliente aqui conforme necessário
        // Isso pode incluir lógica para adicionar novos endereços ou excluir existentes

        return redirect()->route('clients')->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Client $client) {
        $client->delete();
        return redirect()->route('clients')->with('success', 'Cliente excluído com sucesso.');
    }
}
