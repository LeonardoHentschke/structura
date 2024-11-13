<?php

namespace App\Http\Controllers;

use App\Models\FinancialTransaction;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FinancialTransactionController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    // Listar todas as movimentações financeiras
    public function index()
    {
        $transactions = FinancialTransaction::all();
        return response()->json($transactions);
    }

    // Exibir uma movimentação financeira específica
    public function show($id)
    {
        $transaction = FinancialTransaction::findOrFail($id);
        return response()->json($transaction);
    }

    // Criar uma nova movimentação financeira
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'type' => 'required|in:income,expense',
            'transaction_date' => 'required|date',
        ]);

        // Adicionar o ID do usuário autenticado à criação da transação
        $transaction = FinancialTransaction::create(array_merge(
            $request->all(),
            ['user_id' => $user->id]
        ));

        return response()->json($transaction, 201);
    }

    // Atualizar uma movimentação financeira existente
    public function update(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'description' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric',
            'type' => 'sometimes|in:income,expense',
            'transaction_date' => 'sometimes|date',
        ]);

        $transaction = FinancialTransaction::findOrFail($id);

        // Atualizar a transação, adicionando o ID do usuário autenticado
        $transaction->update(array_merge(
            $request->all(),
            ['user_id' => $user->id]
        ));

        return response()->json($transaction);
    }

    // Deletar uma movimentação financeira
    public function destroy($id)
    {
        $transaction = FinancialTransaction::findOrFail($id);
        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully']);
    }
}
