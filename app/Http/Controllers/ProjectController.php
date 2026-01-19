<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    /**
     * Lista projetos do tenant atual
     */
    public function index()
    {
        $this->authorize('viewAny', Project::class);

        return Inertia::render('Projects/Index', [
            'projects' => Project::all(),
        ]);
    }

    /**
     * Criar novo projeto
     */
    public function store(Request $request)
    {
        $tenant = $request->attributes->get('tenant');

        if (! $tenant->canCreateProject()) {
            return back()->with(
                'error',
                'O seu plano atingiu o limite de projetos. Faça upgrade para criar mais.'
            );
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tenant->projects()->create([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Projeto criado com sucesso.');
    }


    /**
     * Apagar projeto
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->back();
    }
}
