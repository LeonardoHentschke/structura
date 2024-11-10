<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ClientController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index()
    {
        $clients = Client::with('addresses')->get();
        return response()->json($clients);
    }

    public function store(Request $request)
    {
        // Obtenha o usuário autenticado
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Criar o cliente com o ID do usuário como 'created_by' e 'updated_by'
        $client = Client::create(array_merge(
            $request->only('name', 'cpf', 'phone', 'email', 'birth_date'),
            ['created_by' => $user->id, 'updated_by' => $user->id]
        ));

        // Criar os endereços associados ao cliente
        $addresses = $request->get('addresses', []);
        foreach ($addresses as $address) {
            $client->addresses()->create(array_merge($address, [
                'created_by' => $user->id,
                'updated_by' => $user->id
            ]));
        }

        return response()->json($client);
    }

    public function show($id)
    {
        $client = Client::with('addresses')->findOrFail($id);
        return response()->json($client);
    }

    public function update(Request $request, $id)
    {
        // Obtenha o usuário autenticado
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Buscar o cliente
        $client = Client::findOrFail($id);

        // Validar e atualizar os dados principais do cliente
        $validatedClientData = $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|max:14', // Ajuste conforme o formato do CPF que você usa
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'birth_date' => 'nullable|date',
        ]);

        // Atualizar os campos do cliente e marcar quem fez a atualização
        $client->update(array_merge($validatedClientData, [
            'updated_by' => $user->id,
        ]));

        // Atualizar endereços - vamos forçar a atualização para garantir a consistência
        $addresses = $request->get('addresses', []);

        // Primeiro, remover todos os endereços existentes
        $client->addresses()->delete();

        // Agora, recriar todos os endereços recebidos no request
        foreach ($addresses as $addressData) {
            $client->addresses()->create(array_merge($addressData, [
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]));
        }

        // Retornar o cliente atualizado com os novos endereços
        return response()->json($client->load('addresses'));
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        return response()->json(null, 204);
    }
}
