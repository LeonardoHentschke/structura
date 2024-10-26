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
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'address_id' => 'required|exists:addresses,id',
            'situation_id' => 'required|exists:project_situations,id',
            'type_id' => 'required|exists:project_types,id',
            'created_by' => 'required|exists:users,id',
            'mcmv' => 'required|boolean',
            'square_meters' => 'required|numeric',
            'value' => 'required|numeric',
        ]);

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
        $validated = $request->validate([
            'client_id' => 'exists:clients,id',
            'address_id' => 'exists:addresses,id',
            'situation_id' => 'exists:project_situations,id',
            'type_id' => 'exists:project_types,id',
            'updated_by' => 'exists:users,id',
            'mcmv' => 'boolean',
            'square_meters' => 'numeric',
            'value' => 'numeric',
        ]);

        $project = Project::findOrFail($id);
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

