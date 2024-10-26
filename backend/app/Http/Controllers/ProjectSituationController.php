<?php

namespace App\Http\Controllers;

use App\Models\ProjectSituation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProjectSituationController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index()
    {
        return response()->json(ProjectSituation::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $situation = ProjectSituation::create($request->only('name', 'description'));

        return response()->json($situation, 201);
    }

    public function show(string $id)
    {
        $situation = ProjectSituation::findOrFail($id);
        return response()->json($situation);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $situation = ProjectSituation::findOrFail($id);
        $situation->update($request->only('name', 'description'));

        return response()->json($situation);
    }

    public function destroy(string $id)
    {
        $situation = ProjectSituation::findOrFail($id);
        $situation->delete();

        return response()->json(null, 204);
    }
}
