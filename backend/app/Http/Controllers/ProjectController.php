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

        // Validação dos campos
        $validated = $request->validate([
            'name' => 'required|string|unique:projects,name',
            'client_id' => 'required|exists:clients,id',
            'address_id' => 'required|exists:addresses,id',
            'situation_id' => 'required|exists:project_situations,id',
            'type_id' => 'required|exists:project_types,id',
            'mcmv' => 'required|boolean',
            'price' => 'required|numeric',
            'square_meters' => 'required|numeric',
        ]);

        // Garantir que `mcmv` é booleano
        $validated['mcmv'] = filter_var($validated['mcmv'], FILTER_VALIDATE_BOOLEAN);

        // Definir campos de usuário que criou e atualizou
        $validated['created_by'] = $user->id;
        $validated['updated_by'] = $user->id;

        // Criar projeto
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
            'name' => 'sometimes|required|string|max:255|unique:projects,name' . $id,
            'client_id' => 'exists:clients,id',
            'address_id' => 'exists:addresses,id',
            'situation_id' => 'exists:project_situations,id',
            'type_id' => 'exists:project_types,id',
            'mcmv' => 'boolean',
            'square_meters' => 'numeric',
            'price' => 'numeric',
        ]);

        // Forçar a conversão de mcmv para booleano
        if (isset($validated['mcmv'])) {
            $validated['mcmv'] = filter_var($validated['mcmv'], FILTER_VALIDATE_BOOLEAN);
        }

        $project = Project::findOrFail($id);

        $validated['updated_by'] = $user->id;

        $project->update($validated);

        return response()->json($project);
    }

    public function hasProjects(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Verifica se existem projetos criados pelo usuário
        $hasProjects = Project::where('created_by', $user->id)->exists();

        return response()->json(['has_projects' => $hasProjects]);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        return response()->json(['message' => 'Project deleted successfully']);
    }

    //Add um funcionário responsável por um projeto
    public function addResponsible(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'responsible_id' => 'required|exists:employees,id',
        ]);

        $project = Project::findOrFail($id);
        $project->responsible_id = $validated['responsible_id'];
        $project->save();

        return response()->json(['message' => 'Responsible added successfully']);
    }

    //Edita um funcionário responsável por um projeto
    public function updateResponsible(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'responsible_id' => 'required|exists:employees,id',
        ]);

        $project = Project::findOrFail($id);
        $project->responsible_id = $validated['responsible_id'];
        $project->save();

        return response()->json(['message' => 'Responsible updated successfully']);
    }

    //Remove um funcionário responsável por um projeto
    public function deleteResponsible(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $project = Project::findOrFail($id);
        $project->responsible_id = null;
        $project->save();

        return response()->json(['message' => 'Responsible removed successfully']);
    }

    //Busca o funcionário responsável pelo projeto
    public function getResponsible(Request $request, $id)
    {
        $project = Project::with('responsible')->findOrFail($id);
        return response()->json($project->responsible);
    }
}
