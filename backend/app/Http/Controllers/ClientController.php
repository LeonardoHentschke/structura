<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

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

        $client = Client::findOrFail($id);

        // Atualizar o cliente com o ID do usuário como 'updated_by'
        $client->update(array_merge(
            $request->only('name', 'cpf', 'phone', 'email', 'birth_date'),
            ['updated_by' => $user->id]
        ));

        // Aqui você pode adicionar a lógica para atualizar os endereços, se necessário

        return response()->json($client);
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        return response()->json(null, 204);
    }
}
