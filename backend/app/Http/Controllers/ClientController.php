<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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

        // Atualizar endereços recebidos no request
        $addresses = $request->get('addresses', []);

        // Obter IDs dos endereços recebidos
        $receivedAddressIds = array_filter(array_column($addresses, 'id'));

        // Remover endereços que não foram incluídos na atualização e não estão vinculados a projetos
        $client->addresses()->whereNotIn('id', $receivedAddressIds)->each(function ($address) {
            if ($address->projects()->count() === 0) {
                // Somente exclua o endereço se ele não estiver vinculado a nenhum projeto
                $address->delete();
            }
        });

        // Atualizar ou criar endereços
        foreach ($addresses as $addressData) {
            if (isset($addressData['id'])) {
                // Atualizar o endereço existente
                $address = $client->addresses()->find($addressData['id']);
                if ($address) {
                    $address->update(array_merge($addressData, [
                        'updated_by' => $user->id,
                    ]));
                }
            } else {
                // Criar um novo endereço
                $client->addresses()->create(array_merge($addressData, [
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]));
            }
        }

        // Retornar o cliente atualizado com os novos endereços
        return response()->json($client->load('addresses'));
    }

    public function getClientAddresses($clientId)
    {
        $client = Client::find($clientId);

        if (!$client) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        $addresses = $client->addresses; // Supondo que o relacionamento esteja bem definido
        return response()->json($addresses);
    }

    public function createAddress(Request $request, $clientId)
    {
        try {
            // Validação dos dados do endereço
            $validated = $request->validate([
                'street' => 'required|string',
                'number' => 'required|string',
                'city' => 'required|string',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            $validated['client_id'] = $clientId;
            $validated['created_by'] = Auth::id();

            // Encontrar o cliente pelo ID
            $client = Client::findOrFail($clientId);

            // Criar o endereço relacionado ao cliente
            $address = $client->addresses()->create($validated);

            return response()->json($address, 201);
        } catch (\Exception $e) {
            Log::error("Erro ao criar o endereço para o cliente $clientId: " . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao criar o endereço',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAddressProjects($addressId)
    {
        $address = Address::with('projects')->find($addressId);

        if (!$address) {
            return response()->json(['message' => 'Endereço não encontrado'], 404);
        }

        return response()->json($address->projects);
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        return response()->json(null, 204);
    }
}
