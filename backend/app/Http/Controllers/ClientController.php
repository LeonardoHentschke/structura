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
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $client = Client::create(array_merge(
            $request->only('name', 'cpf', 'phone', 'email', 'birth_date'),
            ['created_by' => $user->id, 'updated_by' => $user->id]
        ));

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
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        $client = Client::findOrFail($id);
    
        $client->update(array_merge(
            $request->only('name', 'cpf', 'phone', 'email', 'birth_date'),
            ['updated_by' => $user->id]
        ));
    
        $addresses = $request->get('addresses', []);
        $existingAddresses = $client->addresses->keyBy('id');

        foreach ($addresses as $addressData) {
            if (isset($addressData['id']) && $existingAddresses->has($addressData['id'])) {
                $existingAddresses[$addressData['id']]->update(array_merge(
                    $addressData,
                    ['updated_by' => $user->id]
                ));
            } else {
                $client->addresses()->create(array_merge($addressData, [
                    'client_id' => $client->id,
                    'created_by' => $user->id,
                    'updated_by' => $user->id
                ]));
            }
        }

        return response()->json($client->load('addresses'));
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        return response()->json(null, 204);
    }
}
