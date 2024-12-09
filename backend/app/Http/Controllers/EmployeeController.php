<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class EmployeeController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    // Lista todos os funcionários
    public function index()
    {
        $employees = Employee::all();
        return response()->json($employees);
    }

    // Cria um novo funcionário
    public function store(Request $request)
    {
        // Obtenha o usuário autenticado
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Valida os dados do funcionário
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:employees',
            'cpf' => 'required|string|max:14|unique:employees',
            'phone' => 'required|string|max:15',
        ]);

        // Cria o funcionário com o ID do usuário como 'created_by' e a data de admissão como 'admission_date'
        $employee = Employee::create(array_merge(
            $validated,
            ['created_by' => $user->id, 'admission_date' => now()]
        ));
        
        return response()->json($employee, 201);
    }

    // Exibe um funcionário específico
    public function show($id)
    {
        $employee = Employee::with('projects')->findOrFail($id);
        return response()->json($employee);
    }

    // Atualiza um funcionário específico
    public function update(Request $request, $id)
    {
        // Obtenha o usuário autenticado
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Buscar o funcionário
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:employees,email,' . $id,
            'cpf' => 'sometimes|required|string|max:14|unique:employees,cpf,' . $id,
            'phone' => 'sometimes|required|string|max:15',
            'demission_date' => 'nullable|date',
        ]);

        $validated['updated_by'] = $user->id;

        $employee->update($validated);
        return response()->json($employee);
    }

    // Remove um funcionário específico
    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employee->delete();

            return response()->json(['message' => 'Funcionário excluído com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao excluir funcionário', 'message' => $e->getMessage()], 500);
        }
    }

    // Adiciona um projeto a um funcionário
    public function addProject(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);

        $employee->projects()->attach($validated['project_id']);
        return response()->json(['message' => 'Project added to employee successfully']);
    }

    // Lista todos os projetos de um funcionário
    public function listProjects($id)
    {
        $employee = Employee::with('projects')->findOrFail($id);
        return response()->json($employee->projects);
    }

    // Atualiza um projeto de um funcionário
    public function updateProject(Request $request, $employeeId, $projectId)
    {
        $employee = Employee::findOrFail($employeeId);

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);

        $employee->projects()->updateExistingPivot($projectId, ['project_id' => $validated['project_id']]);
        return response()->json(['message' => 'Project updated for employee successfully']);
    }

    // Remove um projeto de um funcionário
    public function removeProject($employeeId, $projectId)
    {
        $employee = Employee::findOrFail($employeeId);
        $employee->projects()->detach($projectId);
        return response()->json(['message' => 'Project removed from employee successfully']);
    }
}