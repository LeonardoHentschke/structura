<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProjectController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index()
    {
        $projects = Project::with(['client', 'address', 'situation', 'type', 'createdBy', 'updatedBy'])->get();
        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'address_id' => 'required|exists:addresses,id',
            'situation_id' => 'required|exists:project_situations,id',
            'type_id' => 'required|exists:project_types,id',
            'mcmv' => 'required|boolean',
            'square_meters' => 'required|numeric',
            'price' => 'required|numeric',
        ]);

        $validated['created_by'] = $user->id;
        $validated['updated_by'] = $user->id;

        $project = Project::create($validated);
        return response()->json($project, 201);
    }

    public function show($id)
    {
        $project = Project::with(['client', 'address', 'situation', 'type', 'createdBy', 'updatedBy'])->findOrFail($id);
        return response()->json($project);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'client_id' => 'exists:clients,id',
            'address_id' => 'exists:addresses,id',
            'situation_id' => 'exists:project_situations,id',
            'type_id' => 'exists:project_types,id',
            'mcmv' => 'boolean',
            'square_meters' => 'numeric',
            'price' => 'numeric',
        ]);

        $project = Project::findOrFail($id);

        $validated['updated_by'] = $user->id;

        $project->update($validated);

        return response()->json($project);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        return response()->json(['message' => 'Project deleted successfully']);
    }
}
