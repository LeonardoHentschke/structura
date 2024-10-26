<?php

namespace App\Http\Controllers;

use App\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProjectTypeController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index()
    {
        return response()->json(ProjectType::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $type = ProjectType::create($request->only('name', 'description'));

        return response()->json(['type' => $type], 201);
    }

    public function show(string $id)
    {
        $type = ProjectType::findOrFail($id);
        return response()->json($type);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $type = ProjectType::findOrFail($id);
        $type->update($request->only('name', 'description'));

        return response()->json($type);
    }

    public function destroy(string $id)
    {
        $type = ProjectType::findOrFail($id);
        $type->delete();

        return response()->json(null, 204);
    }
}
